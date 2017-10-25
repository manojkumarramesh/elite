<?php
ini_set('display_errors', 1);
ini_set('max_execution_time', 0);

// include the finder class
include('core.class.php');
require dirname(__FILE__) . '/../admin/auth.php';


function func_get_all_products_url() {   
    
    return func_query("SELECT id,vendor_id,vendor_category_id,url FROM moeim_vendor_category_url");   
}



$products_url_details  =   func_get_all_products_url();

if($products_url_details) {
    
    $finder = new BviraFinder();
    $finder->init();    
    
    foreach($products_url_details as $value) {        
        
        $single_product_url     =   $finder->get_single_product_url($value['url']);       
        $single_product_url_cnt =   count($single_product_url);
        
        if($single_product_url_cnt) {
            
            for($i=0;$i<$single_product_url_cnt;$i++) {
                
                func_array2insert(
                    'moeim_products_url',
                    array(
                    'vendor_id'   => $value['vendor_id'],
                    'vendor_cat_id'   => $value['vendor_category_id'],
                    'url'   => $single_product_url[$i]
                    )
                );                   
            }
        }
        echo 'Done.';
    }
}


?>