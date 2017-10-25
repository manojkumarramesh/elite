<?php

ini_set('display_errors', 1);
ini_set('memory_limit', '512M');
ini_set( 'max_execution_time','0'); 

/*----- Include Files -----*/
include('core.class.update.php');
require_once dirname(__FILE__) . '/../admin/auth.php';
require_once dirname(__FILE__) . '/../include/func/func.image.php';

// Object initialization 
$finder = new BviraFinder();
$finder->init();

// Fetch the folde name 
$providerid     = '604';

//Process of Fetch Single product url

$sel_purl	= db_query("SELECT a.id as id,c.vendor_name as vendor_name,b.bvira_cat_id as bvira_cat_id,a.url as url,
                               b.type as type,b.category as category
                            FROM moeim_products_url a 
                            INNER JOIN moeim_vendor_categories b
                            ON b.id = a.vendor_cat_id 
                            INNER JOIN moeim_vendor_details c 
                            ON c.id=a.vendor_id 
                            Where c.status=1 and b.type LIKE'%Women%' and c.id IN(1,2,3,5,6,7,9,11,14,15,16,17,18,20,21)");
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
   
	$sel_proid   = mysql_query("SELECT productid from xcart_products where vendorsku='".$PRODUCTCODE."' and provider='$providerid' and wholesaler_id='$wholesaler_id'");
        $fetch_proid = mysql_fetch_array($sel_proid);
        $product_id  = $fetch_proid['productid'];
       
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
}
?>
