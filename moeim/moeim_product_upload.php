<?php

ini_set('display_errors', 1);
ini_set('memory_limit', '512M');
ini_set( 'max_execution_time','0'); 

/*----- Include Files -----*/
include('core.class.php');
require_once dirname(__FILE__) . '/../admin/auth.php';
require_once dirname(__FILE__) . '/../include/func/func.image.php';

// Object initialization 
$finder = new BviraFinder();
$finder->init();

// Fetch the folde name 
$providerid     = '604';
$sel_folder     = mysql_query("SELECT folder_name from xcart_customers where id=$providerid");
$fetch_folder   = mysql_fetch_array($sel_folder);
$folder_name    = $fetch_folder['folder_name'];
$vendor_folder  = 'moeim';

// CREATE DIRECTORY FOR BACKUP IMAGE 
/*
$back_dirname =$xcart_dir."/".$vendor_folder."/".$folder_name;
if(!is_dir($back_dirname)) {
    mkdir($back_dirname,0777);
} 
$image_dirname =$xcart_dir."/".$vendor_folder."/".$folder_name."/images/";
if(!is_dir($image_dirname)) {
    mkdir($image_dirname,0777);
}
*/

$back_dirname ="/disk2/feed_images/$folder_name";
if(!is_dir($back_dirname)) {
  mkdir($back_dirname, 0777);
}
$image_dirname ="/disk2/feed_images/$folder_name/images";
if(!is_dir($image_dirname)){
 mkdir($image_dirname, 0777);
}



// Allowed Image types
$arrimage_ext = array('jpeg','jpg','png','gif');

//Process of Fetch Single product url

$sel_purl	= db_query("SELECT a.id as id,c.vendor_name as vendor_name,b.bvira_cat_id as bvira_cat_id,a.url as url,
                               b.type as type,b.category as category
                            FROM moeim_products_url a 
                            INNER JOIN moeim_vendor_categories b
                            ON b.id = a.vendor_cat_id 
                            INNER JOIN moeim_vendor_details c 
                            ON c.id=a.vendor_id 
                            Where c.status=1 and a.is_done=0 "); 
while ($product_url = db_fetch_array($sel_purl)) {
        $murl_id    = $product_url['id'];
	$wname	    = $product_url['vendor_name'];
	$url	    = $product_url['url'];
	$catid	    = $product_url['bvira_cat_id'];
        $GENDER     = $product_url['type'];
        $TYPE       = $product_url['category'];
	// Process of Whole salers name
	$select_query    = mysql_query("SELECT wholesaler_id from bvira_wholesalers where wholesaler_name='$wname'");
	$fetch_id        = mysql_fetch_array($select_query);
	$wholesaler_id   = $fetch_id['wholesaler_id'];
	if(empty($wholesaler_id)) {
		// Insert into bvira_wholesaler
		$insert_wholesaler = mysql_query("INSERT into bvira_wholesalers (seller_id,wholesaler_name)VALUES('$providerid','$wname')");
		$wholesaler_id     = mysql_insert_id();
	}

	// Get the Product Details
	
	$products = $finder->get_products($url);
        
	$PRODUCTCODE      = $products['details']['style_no'];
	$PRODUCT          = $products['details']['title'];
	$FULLDESCR        = $products['details']['description'];
	$PACKS            = $products['details']['packs'];
	$SIZE	          = $products['details']['size'];
	$COLOR	          = $products['details']['color'];
	$MATERIAL	  = $products['details']['matrial'];
        $MADE	          = $products['details']['made'];
	$IMAGE            = $products['images'];
	$VENDOR_COST      = $products['details']['price'];
	$AVAIL            = $products['stock'];
	$CATEGORY         = '';
	$SHIPPING_FREIGHT = '';
	$FREE_SHIPPING    = 'Y';
	$UPC              = '';
	$MPN              = '';
	$WEIGHT           = '';
	$MANUFACTURER     = '';	
	$VENDOR_COST      = str_replace("$",'',$VENDOR_COST);
   
	// Replace the special Characters.
	$PRODUCT     = str_replace("'", "",$PRODUCT);
	$PRODUCT     = str_replace('"', '',$PRODUCT);
	$PRODUCT     = str_replace('“', '',$PRODUCT);
	$PRODUCT     = str_replace('”','',$PRODUCT);
	$FULLDESCR   = str_replace("'", "",$FULLDESCR);
	$FULLDESCR   = str_replace('"', '',$FULLDESCR);
	$FULLDESCR   = str_replace('“', '',$FULLDESCR);
	$FULLDESCR   = str_replace('”','',$FULLDESCR);
	$FULLDESCR   = strip_tags($FULLDESCR,'<br><br/>');
	$Date        = time();

	// Pricing Process
	$rand_val	= rand(80,100);
	$PRICE          = $VENDOR_COST+(($rand_val/100)*$VENDOR_COST);
	$lowest_price   = $VENDOR_COST+((30/100)*$VENDOR_COST);
	$sale_price	= $VENDOR_COST+((40/100)*$VENDOR_COST);
        
	//CHECK THE PRODUCE ALREADY EXISTS OR NOT	
	$prd            = 0;
	$prdchk         = mysql_query("SELECT count(*) as prd,productcode from xcart_products where vendorsku='".$PRODUCTCODE."' and provider='$providerid' and 					    wholesaler_id='$wholesaler_id'");
	$fetch_products = mysql_fetch_array($prdchk);
	$prd            = $fetch_products['prd'];
	$productcode    = $fetch_products['productcode'];	
	//MANUFACTURER (Check and Insert) Details
	$manufacturerid = 0;
	if(empty($MANUFACTURER) === false) { 
		$sel_manquery   = mysql_query(' SELECT manufacturerid from xcart_manufacturers where manufacturer="'.$MANUFACTURER.'"');
		if(mysql_num_rows($sel_manquery) == 0) {
		  $fetch_manufact  = mysql_fetch_array($sel_manquery);
		  $manufacturerid  = $fetch_manufact['manufacturerid'];
		} 
		else {
		  $insert_manufact = mysql_query(" INSERT into xcart_manufacturers (manufacturer,provider)VALUES('$MANUFACTURER','$providerid')");
		  $manufacturerid  = mysql_insert_id($insert_manufact);
		}
	}
        
	//PRODUCT CODE DETAILS
	if(empty($prd)) { 
		$sel_bvsku   = mysql_query("SELECT max(productid) as sku from xcart_products");
		$fecth_bvsku = mysql_fetch_array($sel_bvsku);
		$productid   = $fecth_bvsku['sku'];
		if(empty($productid)) {
			$productid  = 0;
		}
		$newbvsk    = $productid+1;
		$bvsk = "BVA".$newbvsk;
	} else {
		$bvsk =  $productcode;
	}
        // Insert OR Update the data's into tables
        if($prd == 0) {
            // 1. Insert the data into xcart_products
             $Insert_products = mysql_query("INSERT INTO xcart_products(productcode,product,provider,wholesaler_id,fulldescr,list_price,lowest_possible_price,quantity_type,avail,forsale,add_date,weight,
    shipping_freight, free_shipping,manufacturerid, vendor_cost,whole_sale_price,vendorsku,keywords,saleid) VALUES ('$bvsk','$PRODUCT','$providerid','$wholesaler_id','$FULLDESCR','$PRICE','$lowest_price','2','99999','N','$Date','$WEIGHT','$SHIPPING_FREIGHT','Y','$manufacturerid','$VENDOR_COST',
    '$VENDOR_COST','$PRODUCTCODE','$CATEGORY','1')");
        } 
        else {
            // 1. Update the data into xcart_products

            $Update_products = mysql_query("UPDATE xcart_products set product='$PRODUCT',fulldescr='$FULLDESCR',provider=$providerid,wholesaler_id='$wholesaler_id',list_price='$PRICE',vendor_cost='$VENDOR_COST',whole_sale_price='$VENDOR_COST', lowest_possible_price='$lowest_price', quantity_type='2',avail='99999',forsale='N',weight='$WEIGHT',free_shipping='Y',shipping_freight='$SHIPPING_FREIGHT', manufacturerid='$manufacturerid',saleid='1'  WHERE vendorsku='$PRODUCTCODE' and provider=$providerid and wholesaler_id='$wholesaler_id'");  
        }
        $sel_proid   = mysql_query("SELECT productid from xcart_products where vendorsku='".$PRODUCTCODE."' and provider='$providerid' and wholesaler_id='$wholesaler_id'");
        $fetch_proid = mysql_fetch_array($sel_proid);
        $product_id  = $fetch_proid['productid'];
        if($prd == 0) {
            // 2. Insert the data into xcart_pricing
            $Insert_price = mysql_query("INSERT into xcart_pricing (productid,quantity,price)VALUES('$product_id','1','$sale_price')");

            // 3. Insert the data into xcart_products_lng
            $Insert_lang  = mysql_query(" INSERT IGNORE INTO xcart_products_lng (code,productid,product,fulldescr) VALUES ('en','$product_id','$PRODUCT','$FULLDESCR')");

            //4. Insert the data into xcart_products_categories
            $Insert_products_categories  = mysql_query(" INSERT IGNORE INTO xcart_products_categories (categoryid,productid,main) VALUES ('$catid','$product_id','Y')"); 

        }
        else {
            // 2. Update the data into xcart_pricing
            $Update_price = mysql_query(" UPDATE xcart_pricing set price='$sale_price' WHERE productid='$product_id'");   

            // 3. Update the data into xcart_products_lng
            $Update_price = mysql_query(" UPDATE xcart_products_lng set product='$PRODUCT',fulldescr='$FULLDESCR' WHERE productid='$product_id'");   
        }
        
        // Image Processing
    
        // Image fetch from URL
   
        if($prd == 0) {

            // Inserted in to bvira_scraped_images
            for($k=0;$k<count($IMAGE);$k++) {
                $image_url = $IMAGE[$k]['src'];
                mysql_query(" INSERT INTO bvira_scraped_images (productid,image_url) VALUES('$product_id','$image_url')");
            }
            if(count($IMAGE)>0) {
                for($k=0;$k<count($IMAGE);$k++) {
                    $pos=$k;
                    $image_exten1 = @getimagesize($IMAGE[$k]['src']);
                    $image_type1  = $image_exten1['mime'];
                    $image_ext1   = explode("/",$image_type1);
                    if(in_array(strtolower($image_ext1[1]),$arrimage_ext)) {
                        $image_name  = $bvsk."_".$k.".".$image_ext1[1];
                        $fullpath    = $image_dirname."/".$image_name;
                        $ch          = curl_init($IMAGE[$k]['src']);
                        curl_setopt($ch, CURLOPT_HEADER, 0);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
                        $rawdata     = curl_exec($ch);   
                        curl_close ($ch);
                        if(file_exists($fullpath)){
                            @unlink($fullpath);
                        }
                        $fp          = fopen($fullpath,'x');
                        fwrite($fp, $rawdata);
                        fclose($fp);
                        if(file_exists($fullpath)){
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
                            $thumb = new Imagick($Check_image);
                            $t_width       = $config['images_dimensions']['T']['width'];
                            $t_height      = $config['images_dimensions']['T']['height']; 
                            list($newX,$newY)=scaleImage($thumb->getImageWidth(),$thumb->getImageHeight(),$t_width,$t_height);

                            //Scale the image
                            $thumb->thumbnailImage($newX,$newY);

                            //Write the new image to a file
                            $thumb->writeImage($T_Imagepath);
                            $T_Imagesize	 = func_filesize($T_Imagepath);
                            $t_imagewidth  = $newX;
                            $t_imageheight = $newY;
                            $T_image_path = "./images/T/T_".$Original_Image;
                            $T_image      = "T_$Original_Image";
                            $Insert_thump_image = mysql_query(" INSERT IGNORE INTO xcart_images_T (id,image_path,image_type,image_x,image_y,image_size,filename,date,md5,pos)
                                                                VALUES('$product_id','$T_image_path','$image_type','$t_imagewidth','$t_imageheight','$T_Imagesize','$T_image','$newdate','$md5_image','$pos')");

                            // PL Image Processing
                            $plimage = new Imagick($Check_image1);
                            $pl_width       = $config['images_dimensions']['PL']['width'];
                            $pl_height      = $config['images_dimensions']['PL']['height']; 
                            list($plX,$plY)=scaleImage($plimage->getImageWidth(),$plimage->getImageHeight(),$pl_width,$pl_height);

                            //Scale the image
                            $plimage->thumbnailImage($plX,$plY);

                            //Write the new image to a file
                            $plimage->writeImage($PL_Imagepath);
                            $PL_Imagesize   = func_filesize($PL_Imagepath);
                            $pl_imagewidth  = $plX;
                            $pl_imageheight = $plY;
                            $PL_image_path = "./images/PL/PL_".$Original_Image;
                            $PL_image      = "PL_$Original_Image";
                            $Insert_pl_image = mysql_query(" INSERT IGNORE INTO xcart_images_PL (id,image_path,image_type,image_x,image_y,image_size,filename,date,md5,pos)
                                                                VALUES('$product_id','$PL_image_path','$image_type','$pl_imagewidth','$pl_imageheight','$PL_Imagesize','$PL_image','$newdate','$md5_image','$pos')");

                            // MID Image Processing
                            $midimage = new Imagick($Check_image2);
                            list($midX,$midY)=scaleImage($midimage->getImageWidth(),$midimage->getImageHeight(),'500','500');

                            //Scale the image
                            $midimage->thumbnailImage($midX,$midY);

                            //Write the new image to a file
                            $midimage->writeImage($MID_Imagepath);
                            $MID_Imagesize = func_filesize($MID_Imagepath);
                            $m_imagewidth  = $midX;
                            $m_imageheight = $midY;
                            $MID_image_path = "./images/MID/MID_".$Original_Image;
                            $MID_image      = "MID_$Original_Image";
                            $Insert_mid_image = mysql_query(" INSERT IGNORE INTO xcart_images_MID (id,image_path,image_type,image_x,image_y,image_size,filename,date,md5,pos)
                                                                VALUES('$product_id','$MID_image_path','$image_type','$m_imagewidth','$m_imageheight','$MID_Imagesize','$MID_image', '$newdate','$md5_image','$pos')");

                            // P Image Processing
                            copy($Check_image3, $P_Imagepath);
                            $P_Imagesize  = filesize($Check_image);
                            $P_image_path = "./images/P/P_".$Original_Image;
                            $P_image      = "P_$Original_Image";
                            $Insert_p_image = mysql_query(" INSERT IGNORE INTO xcart_images_P (id,image_path,image_type,image_x,image_y,image_size,filename,date,md5,pos)
                                                            VALUES('$product_id','$P_image_path','$image_type','$imagesize[0]','$imagesize[1]','$P_Imagesize', '$P_image', '$newdate','$md5_image','$pos')");
                        }
                    }
                } 
            }
        }
         // Made in  process
        if(empty($MADE) === false) {
            mysql_query("DELETE from xcart_extra_field_values where productid='$product_id' and fieldid=64");
            //Insert the data into xcart_extra_field_values
            mysql_query("INSERT INTO xcart_extra_field_values (productid,fieldid,value) VALUES ('$product_id','64','$MADE')");
        }

        // Material process
        if(empty($MATERIAL) === false) {
            mysql_query("DELETE from xcart_extra_field_values where productid='$product_id' and fieldid=63");

            //Insert the data into xcart_extra_field_values
            for($m=0;$m<count($MATERIAL);$m++) {
                    $MATERIAL_RP	 = $MATERIAL[$m];
                    $Q = "INSERT INTO xcart_extra_field_values (productid,fieldid,value) VALUES ('$product_id','63','$MATERIAL_RP')";
                    mysql_query("INSERT INTO xcart_extra_field_values (productid,fieldid,value) VALUES ('$product_id','63','$MATERIAL_RP')");
            }
        }

        // Packs Process
        if(empty($PACKS) === false) {
            mysql_query("DELETE from bvira_product_packs where productid=$product_id");
            $pack_keys	= array_keys($PACKS);
            for($p=0;$p<count($pack_keys);$p++) {
                $size       = $pack_keys[$p];
                $size_count = $PACKS[$pack_keys[$p]];

                // Insert the data into bvira_product_packs
                mysql_query("INSERT INTO bvira_product_packs (productid,size,size_count) VALUES ('$product_id','$size','$size_count')");
            } 
        }
        
        // Type process 
        if(empty($TYPE) === false) {
            mysql_query("DELETE from xcart_extra_field_values where productid='$product_id' and fieldid=58");
            //Insert the data into xcart_extra_field_values
            mysql_query("INSERT INTO xcart_extra_field_values (productid,fieldid,value) VALUES ('$product_id','58','$TYPE')");

        }

        // Gender process
        if(empty($GENDER) === false) {
            mysql_query("DELETE from xcart_extra_field_values where productid='$product_id' and fieldid=57");
            //Insert the data into xcart_extra_field_values
            mysql_query("INSERT INTO xcart_extra_field_values (productid,fieldid,value) VALUES ('$product_id','57','$GENDER')");
        }

        // Color process
        if(empty($COLOR) === false) {
            $sel_clasid  = mysql_query("SELECT classid from xcart_classes where productid='".$product_id."' and class='Color'");
            $fetch_proid = mysql_fetch_array($sel_clasid);
            $classid	 = $fetch_proid['classid'];
            if(empty($classid) === false) {
                mysql_query("DELETE from xcart_classes where classid=$classid");
                mysql_query("DELETE from xcart_class_lng where classid=$classid");
                mysql_query("DELETE from xcart_class_options where classid=$classid");
            }

            //Insert the data into xcart_classes
            $Insert_classes  	= mysql_query("INSERT INTO xcart_classes (productid,class,classtext,avail) VALUES ('$product_id','Color','Color','Y')");
            $last_classid	= mysql_insert_id();

            //Insert the data into xcart_class_lng
            $Insert_class_lng  = mysql_query("INSERT INTO xcart_class_lng (code,classid,class,classtext) VALUES ('en','$last_classid','Color','Color')");
            $COLORS = substr($COLOR,0,-1);
            $color_code = explode(" /",$COLORS);
            for($c=0;$c<count($color_code);$c++) {
                //Insert the data into xcart_options
                $pcolor = str_replace("A","",$color_code[$c]);
                $pcolor = urlencode($pcolor);
                $pcolor = str_replace("%A0","",$pcolor);
                $pcolor = str_replace("+","",$pcolor);
                $pcolor = str_replace("%2F","/",$pcolor);
                $Insert_classes_options  = mysql_query("INSERT INTO xcart_class_options (classid,option_name,orderby,avail) VALUES ('$last_classid','$pcolor','1','Y')");
            }
        } 

        // Size process
        if(empty($SIZE) === false) {
            $sel_clasid  = mysql_query("SELECT classid from xcart_classes where productid='".$product_id."' and class='Size'");
            $fetch_proid = mysql_fetch_array($sel_clasid);
            $classid	 = $fetch_proid['classid'];
            if(empty($classid) === false) {
                mysql_query("DELETE from xcart_classes where classid=$classid");
                mysql_query("DELETE from xcart_class_lng where classid=$classid");
                mysql_query("DELETE from xcart_class_options where classid=$classid");
            }
            //Insert the data into xcart_classes
            $Insert_classes = mysql_query("INSERT INTO xcart_classes (productid,class,classtext,avail) VALUES ('$product_id','Size','Size','Y')");
            $last_classid	= mysql_insert_id();
            //Insert the data into xcart_class_lng
            $Insert_class_lng  = mysql_query("INSERT INTO xcart_class_lng (code,classid,class,classtext) VALUES ('en','$last_classid','Size','Size')");
            $size_code = explode("-",$SIZE);
            for($c=0;$c<count($size_code);$c++) {
                //Insert the data into xcart_options
                $Insert_classes_options  = mysql_query("INSERT INTO xcart_class_options (classid,option_name,orderby,avail) VALUES ('$last_classid','$size_code[$c]','1','Y')");
            }
      }
     $product_update_status=mysql_query("UPDATE moeim_products_url set is_done='1' WHERE id='$murl_id'");
     
}
?>
