<?php

//ini_set('display_errors', 1);
ini_set('memory_limit', '512M');
ini_set('max_execution_time', '0'); 

include('core.class.php');
require_once dirname(__FILE__) . '/../admin/auth.php';
require_once dirname(__FILE__) . '/../include/func/func.image.php';

$finder = new BviraFinder();
$finder->init();

// CREATE DIRECTORY FOR BACKUP IMAGE
$image_dirname = $xcart_dir."/las/images/";
if(!is_dir($image_dirname))
{
    mkdir($image_dirname, 0777);
}

$arrimage_ext = array('jpeg','jpg','png','gif');// Allowed Image types

$sql    =   "SELECT
                a.id,
                c.vendor_name,
                b.bvira_cat_id,
                a.url,
                b.type,
                b.category
            FROM
                las_products_url a
            INNER JOIN
                las_vendor_categories b
            ON
                b.id = a.vendor_cat_id 
            INNER JOIN
                las_vendor_details c 
            ON
                c.id = a.vendor_id 
            WHERE
                c.status = 1
            AND
                a.is_done = 0";
$result =   db_query($sql);

$count  =   0;

while($product_url = db_fetch_array($result))
{
    $providerid = 604;

    $murl_id    = $product_url['id'];
    $wname      = addslashes($product_url['vendor_name']);
    $url        = $product_url['url'];
    $categoryid = $product_url['bvira_cat_id'];
    $GENDER     = addslashes($product_url['type']);
    $TYPE       = addslashes($product_url['category']);

    $products   = $finder->get_products($url);

    $vendorsku        = $products['details']['style_no'];
    $PRODUCT          = $products['details']['title'];
    $FULLDESCR        = $products['details']['description'];
    $PACKS            = $products['details']['size_details'];
    $SIZE             = $products['details']['size'];
    $COLOR            = $products['details']['colors'];
    $MATERIAL         = $products['details']['fabric'];
    $MADE             = $products['details']['made_in'];
    $IMAGE            = $products['images'];
    $vendor_cost      = $products['details']['unit_price'];
    $avail            = '99999';
    $shipping_freight = '5.99';
    $free_shipping    = 'N';
    $forsale          = 'Y';
    $UPC              = '';
    $MPN              = '';
    $WEIGHT           = '';
    $MANUFACTURER     = '';
    $saleid           = 1;
    $quantity_type    = 2;

    if(empty($PRODUCT) || empty($vendor_cost) || empty($IMAGE) || empty($COLOR) || empty($SIZE) || empty($PACKS) || empty($vendorsku))
    {
        $error_fields   = array();

        $error_fields[] = (empty($PRODUCT))     ? "product"     : "";
        $error_fields[] = (empty($vendor_cost)) ? "price"       : "";
        $error_fields[] = (empty($IMAGE))       ? "image"       : "";
        $error_fields[] = (empty($COLOR))       ? "color"       : "";
        $error_fields[] = (empty($SIZE))        ? "size"        : "";
        $error_fields[] = (empty($PACKS))       ? "packs"       : "";
        $error_fields[] = (empty($vendorsku))   ? "vendorsku"   : "";

        mysql_query("UPDATE las_products_url SET is_error = '1', error_fields = '".implode(',', $error_fields)."' WHERE id = ".$murl_id);
    }
    else
    {
        // WHOLESALER
        $select_query    = mysql_query("SELECT wholesaler_id FROM bvira_wholesalers WHERE wholesaler_name = '$wname'");
        $fetch_id        = mysql_fetch_array($select_query);
        $wholesaler_id   = $fetch_id['wholesaler_id'];
        if(empty($wholesaler_id))
        {
            mysql_query("INSERT INTO bvira_wholesalers (seller_id,wholesaler_name) VALUES ('$providerid','$wname')");
            $wholesaler_id = mysql_insert_id();
        }

        $vendor_cost      = str_replace("$", '', $vendor_cost);

        $whole_sale_price_per_unit = $vendor_cost;
        $quantity_per_pack         = $products['details']['unit_per_pack'];
        $whole_sale_price          = $whole_sale_price_per_unit;
    
        $PRODUCT     = str_replace("'", "", $PRODUCT);
        $PRODUCT     = str_replace('"', '', $PRODUCT);
        $PRODUCT     = str_replace('“', '', $PRODUCT);
        $PRODUCT     = str_replace('”', '', $PRODUCT);

        $FULLDESCR   = str_replace("'", "", $FULLDESCR);
        $FULLDESCR   = str_replace('"', '', $FULLDESCR);
        $FULLDESCR   = str_replace('“', '', $FULLDESCR);
        $FULLDESCR   = str_replace('”', '', $FULLDESCR);
        $FULLDESCR   = strip_tags($FULLDESCR, '<br><br/>');

        $Date        = time();

        $list_price     = $vendor_cost + ((1450/100) * $vendor_cost);
        $lowest_price   = $vendor_cost + ((250/100)  * $vendor_cost);
        $sale_price     = $vendor_cost + ((800/100)  * $vendor_cost);
            
        //CHECK IF THE PRODUCE ALREADY EXISTS OR NOT
        $is_exists      = 0;
        $prdchk         = mysql_query("SELECT count(*) as prd, productcode
                                    FROM xcart_products
                                    WHERE vendorsku = '".$vendorsku."'
                                    AND provider = '$providerid'
                                    AND wholesaler_id = '$wholesaler_id'");
        $fetch_products = mysql_fetch_array($prdchk);
        $is_exists      = $fetch_products['prd'];
        $productcode    = $fetch_products['productcode'];

        //MANUFACTURER (Check and Insert) Details
        $manufacturerid = 0;

        if(empty($MANUFACTURER) === false)
        {
            $sel_manquery   = mysql_query('SELECT manufacturerid FROM xcart_manufacturers WHERE manufacturer = "'.$MANUFACTURER.'"');
            if(mysql_num_rows($sel_manquery) == 0)
            {
                $fetch_manufact  = mysql_fetch_array($sel_manquery);
                $manufacturerid  = $fetch_manufact['manufacturerid'];
            } 
            else
            {
                mysql_query("INSERT INTO xcart_manufacturers (manufacturer,provider) VALUES ('$MANUFACTURER','$providerid')");
                $manufacturerid  = mysql_insert_id();
            }
        }
            
        //PRODUCT CODE DETAILS
        if(empty($is_exists))
        { 
            $sel_bvsku   = mysql_query("SELECT max(productid) as sku FROM xcart_products");
            $fecth_bvsku = mysql_fetch_array($sel_bvsku);
            $productid   = $fecth_bvsku['sku'];
            $newbvsk     = $productid + 1;
            $bvsk        = "BVA".$newbvsk;
        }
        else
        {
            $bvsk =  $productcode;
        }

        // Insert OR Update the data's into tables
        if($is_exists == 0)
        {
            mysql_query("INSERT INTO xcart_products
                            (productcode, product, provider, wholesaler_id, weight, list_price, lowest_possible_price, vendor_cost, whole_sale_price, whole_sale_price_per_unit, fulldescr, quantity_type, avail, quantity_per_pack, forsale, add_date, shipping_freight, free_shipping, manufacturerid, keywords, meta_description, meta_keywords, vendorsku, upc_ean_gtin_product_id, saleid)
                        VALUES
                            ('$bvsk', '$PRODUCT', '$providerid', '$wholesaler_id', '$WEIGHT', '$list_price', '$lowest_price', '$vendor_cost', '$whole_sale_price', '$whole_sale_price_per_unit', '$FULLDESCR', '$quantity_type', '$avail', '$quantity_per_pack', '$forsale', '$Date', '$shipping_freight', '$free_shipping', '$manufacturerid', '$PRODUCT', '$PRODUCT', '$PRODUCT', '$vendorsku', '$UPC', '$saleid')");
        } 
        else
        {
            mysql_query("UPDATE xcart_products
                        SET
                            product = '$PRODUCT',
                            fulldescr = '$FULLDESCR',
                            list_price = '$list_price',
                            vendor_cost = '$vendor_cost',
                            whole_sale_price = '$whole_sale_price',
                            whole_sale_price_per_unit = '$whole_sale_price_per_unit',
                            lowest_possible_price = '$lowest_price',
                            quantity_type = '$quantity_type',
                            avail = '$avail',
                            quantity_per_pack = '$quantity_per_pack',
                            forsale = '$forsale',
                            update_date = '$Date',
                            weight = '$WEIGHT',
                            free_shipping = '$free_shipping',
                            shipping_freight = '$shipping_freight',
                            saleid = '$saleid'
                        WHERE vendorsku = '$vendorsku'
                        AND provider = $providerid
                        AND wholesaler_id = '$wholesaler_id'");
        }

        $sel_proid   = mysql_query("SELECT productid FROM xcart_products WHERE vendorsku='".$vendorsku."' AND provider='$providerid' AND wholesaler_id='$wholesaler_id'");
        $fetch_proid = mysql_fetch_array($sel_proid);
        $product_id  = $fetch_proid['productid'];

        if($is_exists == 0)
        {
            mysql_query("INSERT INTO xcart_pricing (productid,quantity,price) VALUES ('$product_id','1','$sale_price')");
            $priceid = mysql_insert_id();

            mysql_query("INSERT INTO xcart_quick_prices (productid,priceid,membershipid,variantid) VALUES ('$product_id', '$priceid', '0', '0')");

            mysql_query("INSERT IGNORE INTO xcart_products_lng (code,productid,product,fulldescr) VALUES ('en','$product_id','$PRODUCT','$FULLDESCR')");
            mysql_query("INSERT IGNORE INTO xcart_products_categories (categoryid,productid,main) VALUES ('$categoryid','$product_id','Y')"); 
        }
        else
        {
            mysql_query("UPDATE xcart_pricing SET price = '$sale_price' WHERE productid = '$product_id'");   
            mysql_query("UPDATE xcart_products_lng SET product = '$PRODUCT',fulldescr = '$FULLDESCR' WHERE productid = '$product_id'");   
        }
        
        // Image Processing - Image fetch from URL
        if($is_exists == 0)
        {
            if(count($IMAGE) > 0)
            {
                for($k=0;$k<count($IMAGE);$k++)
                {
                    $pos=$k;
                    $image_exten1 = @getimagesize($IMAGE[$k]['src']);
                    $image_type1  = $image_exten1['mime'];
                    $image_ext1   = explode("/",$image_type1);
                    if(in_array(strtolower($image_ext1[1]), $arrimage_ext))
                    {
                        $image_name  = $bvsk."_".$k.".".$image_ext1[1];
                        $fullpath    = $image_dirname."/".$image_name;

                        $ch          = curl_init($IMAGE[$k]['src']);
                        curl_setopt($ch, CURLOPT_HEADER, 0);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
                        $rawdata     = curl_exec($ch);   
                        curl_close ($ch);

                        if(file_exists($fullpath))
                            @unlink($fullpath);

                        $fp          = fopen($fullpath,'x');
                        fwrite($fp, $rawdata);
                        fclose($fp);

                        if(file_exists($fullpath))
                        {
                            $Original_Image = $image_name;
                            $Check_image    = $fullpath;
                            $Check_image1   = $fullpath;
                            $Check_image2   = $fullpath;
                            $Check_image3   = $fullpath;

                            $T_Imagepath    = $xcart_dir."/images/T/T_".$Original_Image;
                            $PL_Imagepath   = $xcart_dir."/images/PL/PL_".$Original_Image;
                            $P_Imagepath    = $xcart_dir."/images/P/P_".$Original_Image;
                            $MID_Imagepath  = $xcart_dir."/images/MID/MID_".$Original_Image;

                            $md5_image  = md5($Check_image);
                            $newdate    = time();
                            $imagesize  = @getimagesize($Check_image);
                            $image_type = $imagesize['mime'];
                            $image_ext  = explode("/",$image_type);

                            // Tumbnail Image Processing
                            $thumb      = new Imagick($Check_image);
                            $t_width    = $config['images_dimensions']['T']['width'];
                            $t_height   = $config['images_dimensions']['T']['height']; 
                            list($newX, $newY) = scaleImage($thumb->getImageWidth(), $thumb->getImageHeight(), $t_width, $t_height);

                            //Scale the image
                            $thumb->thumbnailImage($newX,$newY);

                            //Write the new image to a file
                            $thumb->writeImage($T_Imagepath);
                            $T_Imagesize    = func_filesize($T_Imagepath);
                            $t_imagewidth   = $newX;
                            $t_imageheight  = $newY;
                            $T_image_path   = "./images/T/T_".$Original_Image;
                            $T_image        = "T_$Original_Image";
                            mysql_query("INSERT IGNORE INTO xcart_images_T (id,image_path,image_type,image_x,image_y,image_size,filename,date,md5,pos)
                                VALUES('$product_id','$T_image_path','$image_type','$t_imagewidth','$t_imageheight','$T_Imagesize','$T_image','$newdate','$md5_image','$pos')");

                            mysql_query("INSERT INTO xcart_quick_flags (productid,image_path_T) VALUES ('$product_id','$T_image_path')");

                            // PL Image Processing
                            $plimage        = new Imagick($Check_image1);
                            $pl_width       = $config['images_dimensions']['PL']['width'];
                            $pl_height      = $config['images_dimensions']['PL']['height']; 
                            list($plX,$plY) = scaleImage($plimage->getImageWidth(), $plimage->getImageHeight(), $pl_width, $pl_height);

                            //Scale the image
                            $plimage->thumbnailImage($plX, $plY);

                            //Write the new image to a file
                            $plimage->writeImage($PL_Imagepath);
                            $PL_Imagesize   = func_filesize($PL_Imagepath);
                            $pl_imagewidth  = $plX;
                            $pl_imageheight = $plY;
                            $PL_image_path  = "./images/PL/PL_".$Original_Image;
                            $PL_image       = "PL_$Original_Image";
                            mysql_query("INSERT IGNORE INTO xcart_images_PL (id,image_path,image_type,image_x,image_y,image_size,filename,date,md5,pos)
                                VALUES('$product_id','$PL_image_path','$image_type','$pl_imagewidth','$pl_imageheight','$PL_Imagesize','$PL_image','$newdate','$md5_image','$pos')");

                            //MID Image Processing
                            $midimage = new Imagick($Check_image2);
                            list($midX,$midY) = scaleImage($midimage->getImageWidth(), $midimage->getImageHeight(), '500', '500');

                            //Scale the image
                            $midimage->thumbnailImage($midX,$midY);

                            //Write the new image to a file
                            $midimage->writeImage($MID_Imagepath);
                            $MID_Imagesize  = func_filesize($MID_Imagepath);
                            $m_imagewidth   = $midX;
                            $m_imageheight  = $midY;
                            $MID_image_path = "./images/MID/MID_".$Original_Image;
                            $MID_image      = "MID_$Original_Image";
                            mysql_query("INSERT IGNORE INTO xcart_images_MID (id,image_path,image_type,image_x,image_y,image_size,filename,date,md5,pos)
                                VALUES('$product_id','$MID_image_path','$image_type','$m_imagewidth','$m_imageheight','$MID_Imagesize','$MID_image', '$newdate','$md5_image','$pos')");

                            // P Image Processing
                            copy($Check_image3, $P_Imagepath);
                            $P_Imagesize  = filesize($Check_image);
                            $P_image_path = "./images/P/P_".$Original_Image;
                            $P_image      = "P_$Original_Image";
                            mysql_query("INSERT IGNORE INTO xcart_images_P (id,image_path,image_type,image_x,image_y,image_size,filename,date,md5,pos)
                                VALUES('$product_id','$P_image_path','$image_type','$imagesize[0]','$imagesize[1]','$P_Imagesize', '$P_image', '$newdate','$md5_image','$pos')");
                        }
                    }
                } 
            }
        }

        // Made in
        if(empty($MADE) === false)
        {
            mysql_query("DELETE FROM xcart_extra_field_values WHERE productid = '$product_id' AND fieldid = 64");
            mysql_query("INSERT INTO xcart_extra_field_values (productid,fieldid,value) VALUES ('$product_id','64','$MADE')");
        }

        if(empty($MATERIAL) === false)
        {
            mysql_query("DELETE FROM xcart_extra_field_values WHERE productid = '$product_id' AND fieldid = 63");

            for($m=0;$m<count($MATERIAL);$m++)
            {
                mysql_query("INSERT INTO xcart_extra_field_values (productid,fieldid,value) VALUES ('$product_id','63','$MATERIAL[$m]')");
            }
        }

        if(empty($PACKS) === false)
        {
            mysql_query("DELETE FROM bvira_product_packs WHERE productid = ".$product_id);

            foreach($PACKS as $key => $value)
            {
                mysql_query("INSERT INTO bvira_product_packs (productid, size, size_count) VALUES ('$product_id', '$key', '$value')");
            }
        }
        
        if(empty($TYPE) === false)
        {
            mysql_query("DELETE FROM xcart_extra_field_values WHERE productid='$product_id' AND fieldid=58");
            mysql_query("INSERT INTO xcart_extra_field_values (productid,fieldid,value) VALUES ('$product_id','58','$TYPE')");
        }

        if(empty($GENDER) === false)
        {
            mysql_query("DELETE FROM xcart_extra_field_values WHERE productid='$product_id' AND fieldid=57");
            mysql_query("INSERT INTO xcart_extra_field_values (productid,fieldid,value) VALUES ('$product_id','57','$GENDER')");
        }

        if(empty($COLOR) === false)
        {
            $sel_clasid  = mysql_query("SELECT classid FROM xcart_classes WHERE productid='".$product_id."' AND class='Color'");
            $fetch_proid = mysql_fetch_array($sel_clasid);
            $classid     = $fetch_proid['classid'];
            if(empty($classid) === false)
            {
                mysql_query("DELETE FROM xcart_classes WHERE classid = ".$classid);
                mysql_query("DELETE FROM xcart_class_lng WHERE classid = ".$classid);
                mysql_query("DELETE FROM xcart_class_options WHERE classid = ".$classid);
            }

            mysql_query("INSERT INTO xcart_classes (productid,class,classtext,avail) VALUES ('$product_id','Color','Color','Y')");
            $last_classid = mysql_insert_id();

            mysql_query("INSERT INTO xcart_class_lng (code,classid,class,classtext) VALUES ('en','$last_classid','Color','Color')");

            for($c=0;$c<count($COLOR);$c++)
            {
                mysql_query("INSERT INTO xcart_class_options (classid,option_name,orderby,avail) VALUES ('$last_classid','$COLOR[$c]','1','Y')");
            }
        } 

        if(empty($SIZE) === false)
        {
            $sel_clasid  = mysql_query("SELECT classid FROM xcart_classes WHERE productid = '".$product_id."' AND class = 'Size'");
            $fetch_proid = mysql_fetch_array($sel_clasid);
            $classid     = $fetch_proid['classid'];
            if(empty($classid) === false)
            {
                mysql_query("DELETE FROM xcart_classes WHERE classid = ".$classid);
                mysql_query("DELETE FROM xcart_class_lng WHERE classid = ".$classid);
                mysql_query("DELETE FROM xcart_class_options WHERE classid = ".$classid);
            }

            mysql_query("INSERT INTO xcart_classes (productid,class,classtext,avail) VALUES ('$product_id','Size','Size','Y')");
            $last_classid = mysql_insert_id();

            mysql_query("INSERT INTO xcart_class_lng (code,classid,class,classtext) VALUES ('en','$last_classid','Size','Size')");

            $size_code = explode("-",$SIZE);
            for($c=0;$c<count($size_code);$c++)
            {
                mysql_query("INSERT INTO xcart_class_options (classid,option_name,orderby,avail) VALUES ('$last_classid','$size_code[$c]','1','Y')");
            }
        }

        mysql_query("UPDATE las_products_url SET is_done = '1' WHERE id = ".$murl_id);
    }

    $count++;

    if($count == 10)
    {
        $count = 0;
        sleep(5);
        //die;
    }
}