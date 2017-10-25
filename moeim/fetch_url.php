<?php
ini_set('display_errors', 1);
ini_set('max_execution_time', 0);

// include the finder class
include('core.class.php');
require dirname(__FILE__) . '/../admin/auth.php';


function func_get_all_vendor_category_url() {   
    
    return func_query("SELECT id,vendor_name,url FROM moeim_vendor_details WHERE status = 1");   
}

function func_check_exist_type_category($type,$category) {   
   
    $exist_cat_id   =    func_query_first_cell("SELECT id FROM moeim_vendor_categories 
                                    WHERE type = '".$type."' AND category = '".$category."'");   
    if($exist_cat_id == ''){
        return 0;
    }
    return $exist_cat_id;
}



$vendor_categories_details  =   func_get_all_vendor_category_url();

if($vendor_categories_details){
    
    $finder = new BviraFinder();
    $finder->init();    
    
    foreach($vendor_categories_details as $value) {        
        
        $products = $finder->get_categories_url($value['url']);
        
        foreach($products as $type => $new_value) {                        
            
            $cat_url_count  =   count($new_value);

            for($i=0; $i<$cat_url_count; $i++) {        
                
                if(!empty($new_value[$i]['category']) && !empty($new_value[$i]['url'])) {
                    $vendor_cat_id   =   func_check_exist_type_category($type,$new_value[$i]['category']);
                    if($vendor_cat_id == 0){
                        $vendor_cat_id = func_array2insert(
                        'moeim_vendor_categories',
                        array(
                        'type'   => $type,
                        'category'   => $new_value[$i]['category']
                        )
                        );                
                    }
                    if($vendor_cat_id) {
                        $vendor_cat_id = func_array2insert(
                        'moeim_vendor_category_url',
                        array(
                        'vendor_id'   => $value['id'],
                        'vendor_category_id'   => $vendor_cat_id,
                        'url'   => $new_value[$i]['url']
                        )
                        );                    
                    }
                }  
            }
        }

    }
}

?>