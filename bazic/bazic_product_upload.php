<?php

ini_set('display_errors', 1);
ini_set('memory_limit', '512M');
ini_set( 'max_execution_time','0');

include('core.class.php');
require_once dirname(__FILE__) . '/../admin/auth.php';
require_once dirname(__FILE__) . '/../include/func/func.image.php';

$image_dirname  = $xcart_dir."/bazic/images/";

if(!is_dir($image_dirname))
{
    mkdir($image_dirname, 0777);
}

$image_ext  =   array('jpeg','jpg','png','gif');//Allowed Image types

$sql        =   "SELECT
                    a.id,
                    a.Item_No,
                    a.image,
                    a.Item_Description,
                    a.categoryid,
                    a.Product_UPC,
                    a.Pack,
                    a.Dollar
                 FROM
                    basic_price_list AS a
                 WHERE
                    a.UOM = 'box'";
$sel_purl   =   db_query($sql);

$count = 0;

while($product_url = db_fetch_array($sel_purl))
{
    $providerid       = 604;
    $wholesaler_id    = 123;
    $catid            = (int) $product_url['categoryid'];
    $PRODUCTCODE      = (int) $product_url['Item_No'];
    $PRODUCT          = (string) $product_url['Item_Description'];
    $PACKS            = (int) $product_url['Pack'];
    $IMAGE            = $product_url['image'];
    $VENDOR_COST      = $product_url['Dollar'];
    $AVAIL            = '99999';
    $forsale          = 'Y';
    $SHIPPING_FREIGHT = '5.99';
    $free_shipping    = 'N';
    $UPC              = (string) $product_url['Product_UPC'];
    $MPN              = '';
    $WEIGHT           = '';
    $manufacturerid   = 0;
    $Date             = time();
    $saleid           = 1;
    $quantity_type    = 2;

    $whole_sale_price_per_unit  = $product_url['Dollar'];
    $quantity_per_pack          = $product_url['Pack'];
    $whole_sale_price           = $whole_sale_price_per_unit * $quantity_per_pack;

    $PRODUCT    = str_replace("'", "", $PRODUCT);
    $PRODUCT    = str_replace('"', '', $PRODUCT);
    $PRODUCT    = str_replace('“', '', $PRODUCT);
    $PRODUCT    = str_replace('”', '', $PRODUCT);

    $list_price     = $VENDOR_COST + ((1450/100) * $VENDOR_COST);
    $lowest_price   = $VENDOR_COST + ((250/100)  * $VENDOR_COST);
    $sale_price     = $VENDOR_COST + ((800/100)  * $VENDOR_COST);
        
    //CHECK IF THE PRODUCE ALREADY EXISTS OR NOT
    $is_exists      = 0;
    $prdchk         = mysql_query("SELECT count(*) as prd, productcode
                                   FROM xcart_products
                                   WHERE vendorsku = '".$PRODUCTCODE."'
                                   AND provider = '$providerid'
                                   AND wholesaler_id = '$wholesaler_id'");
    $fetch_products = mysql_fetch_array($prdchk);
    $is_exists      = $fetch_products['prd'];
    $productcode    = $fetch_products['productcode'];

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
        $bvsk = $productcode;
    }

    // Insert OR Update the data's into tables
    if($is_exists == 0)
    {
        mysql_query("INSERT INTO xcart_products
                        (productcode, product, provider, wholesaler_id, weight, list_price, lowest_possible_price, vendor_cost, whole_sale_price, whole_sale_price_per_unit, fulldescr, quantity_type, avail, quantity_per_pack, forsale, add_date, shipping_freight, free_shipping, manufacturerid, keywords, meta_description, meta_keywords, vendorsku, upc_ean_gtin_product_id, saleid)
                     VALUES
                        ('$bvsk', '$PRODUCT', '$providerid', '$wholesaler_id', '$WEIGHT', '$list_price', '$lowest_price', '$VENDOR_COST', '$whole_sale_price', '$whole_sale_price_per_unit', '$PRODUCT', '$quantity_type', '$AVAIL', '$quantity_per_pack', '$forsale', '$Date', '$SHIPPING_FREIGHT', '$free_shipping', '$manufacturerid', '$PRODUCT', '$PRODUCT', '$PRODUCT', '$PRODUCTCODE', '$UPC', '$saleid')");
    }
    else
    {
        mysql_query("UPDATE xcart_products
                     SET
                        product = '$PRODUCT',
                        fulldescr = '$PRODUCT',
                        list_price = '$list_price',
                        vendor_cost = '$VENDOR_COST',
                        whole_sale_price = '$whole_sale_price',
                        whole_sale_price_per_unit = '$whole_sale_price_per_unit',
                        lowest_possible_price = '$lowest_price',
                        quantity_type = '$quantity_type',
                        avail = '$AVAIL',
                        quantity_per_pack = '$quantity_per_pack',
                        forsale = '$forsale',
                        weight = '$WEIGHT',
                        free_shipping = '$free_shipping',
                        shipping_freight = '$SHIPPING_FREIGHT',
                        saleid = '$saleid'
                     WHERE vendorsku = '$PRODUCTCODE'
                     AND provider = $providerid
                     AND wholesaler_id = '$wholesaler_id'");
    }

    $sel_proid   = mysql_query("SELECT productid
                                FROM xcart_products
                                WHERE vendorsku = '".$PRODUCTCODE."'
                                AND provider = '$providerid'
                                AND wholesaler_id = '$wholesaler_id'");
    $fetch_proid = mysql_fetch_array($sel_proid);
    $product_id  = $fetch_proid['productid'];

    if($is_exists == 0)
    {
        mysql_query("INSERT into xcart_pricing (productid, quantity, price) VALUES ('$product_id', '1', '$sale_price')");

        mysql_query("INSERT IGNORE INTO xcart_products_lng (code, productid, product, fulldescr) VALUES ('en', '$product_id', '$PRODUCT', '$PRODUCT')");

        mysql_query("INSERT IGNORE INTO xcart_products_categories (categoryid, productid, main) VALUES ('$catid', '$product_id', 'Y')");
    }
    else
    {
        mysql_query("UPDATE xcart_pricing SET price = '$sale_price' WHERE productid = '$product_id'");

        mysql_query("UPDATE xcart_products_lng SET product = '$PRODUCT', fulldescr = '$PRODUCT' WHERE productid = '$product_id'");
    }
        
    // Image Processing - Image fetch from URL
    if($is_exists == 0)
    {
        if(!empty($IMAGE))
        {
            $image_src    = "http://bazicproducts.com/images/".$IMAGE.".gif";

            $image_exten1 = @getimagesize($image_src);
            $image_type1  = $image_exten1['mime'];
            $image_ext1   = explode("/", $image_type1);

            if(in_array(strtolower($image_ext1[1]), $image_ext))
            {
                $image_name  = $bvsk."_0.".$image_ext1[1];
                $fullpath    = $image_dirname.$image_name;

                $ch          = curl_init($image_src);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
                $rawdata     = curl_exec($ch);
                curl_close ($ch);

                if(file_exists($fullpath))
                {
                    chmod($fullpath, 0777);
                    @unlink($fullpath);
                }

                $fp = fopen($fullpath, 'x');
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
                    $image_ext  = explode("/", $image_type);

                    // Thumbnail Image Processing
                    $thumb            = new Imagick($Check_image);
                    $t_width          = $config['images_dimensions']['T']['width'];
                    $t_height         = $config['images_dimensions']['T']['height']; 
                    list($newX,$newY) = scaleImage($thumb->getImageWidth(), $thumb->getImageHeight(), $t_width, $t_height);

                    // Scale the image
                    $thumb->thumbnailImage($newX, $newY);

                    //Write the new image to a file
                    $thumb->writeImage($T_Imagepath);
                    $T_Imagesize        = func_filesize($T_Imagepath);
                    $t_imagewidth       = $newX;
                    $t_imageheight      = $newY;
                    $T_image_path       = "./images/T/T_".$Original_Image;
                    $T_image            = "T_$Original_Image";
                    mysql_query("INSERT IGNORE INTO xcart_images_T (id,image_path,image_type,image_x,image_y,image_size,filename,date,md5,pos)
                                 VALUES('$product_id','$T_image_path','$image_type','$t_imagewidth','$t_imageheight','$T_Imagesize','$T_image','$newdate','$md5_image','0')");

                    // PL Image Processing
                    $plimage        = new Imagick($Check_image1);
                    $pl_width       = $config['images_dimensions']['PL']['width'];
                    $pl_height      = $config['images_dimensions']['PL']['height']; 
                    list($plX,$plY) = scaleImage($plimage->getImageWidth(), $plimage->getImageHeight(), $pl_width, $pl_height);

                    //Scale the image
                    $plimage->thumbnailImage($plX, $plY);

                    //Write the new image to a file
                    $plimage->writeImage($PL_Imagepath);
                    $PL_Imagesize    = func_filesize($PL_Imagepath);
                    $pl_imagewidth   = $plX;
                    $pl_imageheight  = $plY;
                    $PL_image_path   = "./images/PL/PL_".$Original_Image;
                    $PL_image        = "PL_$Original_Image";
                    mysql_query("INSERT IGNORE INTO xcart_images_PL (id,image_path,image_type,image_x,image_y,image_size,filename,date,md5,pos)
                                 VALUES('$product_id','$PL_image_path','$image_type','$pl_imagewidth','$pl_imageheight','$PL_Imagesize','$PL_image','$newdate','$md5_image','0')");

                    // MID Image Processing
                    $midimage         = new Imagick($Check_image2);
                    list($midX,$midY) = scaleImage($midimage->getImageWidth(), $midimage->getImageHeight(), '500', '500');

                    //Scale the image
                    $midimage->thumbnailImage($midX,$midY);

                    //Write the new image to a file
                    $midimage->writeImage($MID_Imagepath);
                    $MID_Imagesize    = func_filesize($MID_Imagepath);
                    $m_imagewidth     = $midX;
                    $m_imageheight    = $midY;
                    $MID_image_path   = "./images/MID/MID_".$Original_Image;
                    $MID_image        = "MID_$Original_Image";
                    mysql_query("INSERT IGNORE INTO xcart_images_MID (id,image_path,image_type,image_x,image_y,image_size,filename,date,md5,pos)
                              VALUES('$product_id','$MID_image_path','$image_type','$m_imagewidth','$m_imageheight','$MID_Imagesize','$MID_image', '$newdate','$md5_image','0')");

                    // P Image Processing
                    copy($Check_image3, $P_Imagepath);
                    $P_Imagesize    = filesize($Check_image);
                    $P_image_path   = "./images/P/P_".$Original_Image;
                    $P_image        = "P_$Original_Image";
                    mysql_query("INSERT IGNORE INTO xcart_images_P (id,image_path,image_type,image_x,image_y,image_size,filename,date,md5,pos)
                                 VALUES('$product_id','$P_image_path','$image_type','$imagesize[0]','$imagesize[1]','$P_Imagesize', '$P_image', '$newdate','$md5_image','0')");
                }
            }
        }
    }

    if(empty($PACKS) === false)
    {
        mysql_query("DELETE FROM bvira_product_packs where productid = ".$product_id);

        mysql_query("INSERT INTO bvira_product_packs (productid, size, size_count) VALUES ('$product_id', '', '$PACKS')");
    }

    $count++;

    if($count == 10)
    {
        $count = 0;
        sleep(5);
    }
}