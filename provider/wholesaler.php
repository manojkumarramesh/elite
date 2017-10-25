<?php

/**
 * @desc   Wholesaler Profile Interface
 */

/*----- Include Files -----*/
require_once('./auth.php');

/*----- x-cart default functions load -----*/
x_load('wholesaler','product');

/*----- action request -----*/
$actions = ($_POST['action'] != '') ? $_POST['action'] : $_GET['action'];
$seller_id     = $GLOBALS['user_account']['id'];

/*----- Constant Definition for switch case ----- */
define('ADD_WS_PROFILE','Add');
define('EDIT_WS_PROFILE','Edit');
define('UPDATE_WS_PROFILE','Update');
define('DELETE_WS_PROFILE','Delete');
define('GET_WS_PROFILE','get_ws_details');


switch($actions)
{
    case ADD_WS_PROFILE:        
        
        $query_data = array(
                
                'seller_id'                     => $seller_id,
                'wholesaler_name'               => $add_ws_name
                
        );
        
        $ws_add_query = func_array2insert('bvira_wholesalers',$query_data);
        
        // Profile is Added
        if($ws_add_query!="") {
            $top_message['content'] = func_get_langvar_by_name('msg_profile_add', false, false, true);            
        }
        func_header_location("wholesaler.php");
        break;
    
    case EDIT_WS_PROFILE:
        
        $ws_edit_details = func_get_ws_details($seller_id,$ws_id,'Edit');        
        $smarty->assign('ws_edit_details', $ws_edit_details);        
        break;
    
    case UPDATE_WS_PROFILE:
        
        /*Update wholesalers table*/
        $query_data = array(
                'list_price_markup_type'              => $ws_list_price_type,
                'list_price_markup'                   => $ws_list_price,
                'seller_cost_markup_type'             => $ws_seller_cost_type,
                'seller_cost_markup'                  => $ws_seller_cost,
                'sale_price_markup_type'              => $ws_sale_price_type,                    
                'sale_price_markup'                   => $ws_sale_price,                    
                'lowest_possible_price_markup_type'   => $ws_lowest_possible_price_type,
                'lowest_possible_price_markup'        => $ws_lowest_possible_price,                
                'shipping_type'                       => $ws_shipping_type,
                'fixed_shipping_price'                => $ws_fixed_shipping_price,
                'saleid'                              => $saleid
                
        );
        
        $ws_update_query = func_array2update('bvira_wholesalers',$query_data,"wholesaler_id='$htn_ws_id'");
        /*End Update wholesalers table*/

        if($saleid != '') {
            $products_query_data = array(
                'saleid'                   => $saleid
            );
            $ws_update_query = func_array2update('xcart_products',$products_query_data,"wholesaler_id='$htn_ws_id'");
        }

        if($ws_shipping_type=="FS") {
            $free_shipping = 'Y';
            $products_query_data = array(                
                
                'free_shipping'                   => $free_shipping,
                'shipping_freight'                => '0'
                
            );
            $ws_update_query = func_array2update('xcart_products',$products_query_data,"wholesaler_id='$htn_ws_id'");
        } elseif($ws_shipping_type=="FX") {
            $free_shipping = 'N';
            $products_query_data = array(                

                    'free_shipping'                   => $free_shipping,
                    'shipping_freight'                => $ws_fixed_shipping_price

            );
            $ws_update_query = func_array2update('xcart_products',$products_query_data,"wholesaler_id='$htn_ws_id'");
        }
        /*Update products table*/
        
        /*End Update products table*/
        
        //Checkbox is enable
        if($ws_checkbox_update=='1')
        {
            $ws_edit_details = func_update_product_all_price($htn_ws_id);
        }
        
        //Checkbox set product live is enable
        if($ws_chbox_live=='1')
        {
            $update_sql =   "UPDATE $sql_tbl[products] SET forsale='Y' WHERE wholesaler_id='$htn_ws_id'";           
            
        } 
        else {
            $update_sql =   "UPDATE $sql_tbl[products] SET forsale='N' WHERE wholesaler_id='$htn_ws_id'";
        }
        mysql_query($update_sql);
        
        // Profile is updated
        if($ws_update_query!="") {
            $top_message['content'] = func_get_langvar_by_name('msg_profile_upd', false, false, true);
        }

        $memcache_obj = new Memcache;
        $memcache_obj->connect('localhost', 11211);
        $memcache_obj->flush();

        func_header_location("wholesaler.php");
        break;
        
    case DELETE_WS_PROFILE:
        
        $ws_delete_func = func_delete_ws_details($seller_id,$ws_id);
        $smarty->assign('ws_delete_msg', $ws_delete_func);

        $memcache_obj = new Memcache;
        $memcache_obj->connect('localhost', 11211);
        $memcache_obj->flush();

        func_header_location("wholesaler.php?msg=$ws_delete_func");
        break;
    
    case GET_WS_PROFILE:
        
        echo $ws_edit_details = func_get_ws_details($seller_id,$ws_id,'Edit');
        die;
        break;
}
//Get wholesaler details
$ws_details = func_get_ws_details($seller_id);
$smarty->assign('ws_details', $ws_details);

/*----- smarty assign -----*/
$smarty->assign('main', 'wholesaler');
$smarty->assign('action', $actions);
$smarty->assign('userid', $GLOBALS['user_account']['id']);
$smarty->assign('product_sale_module_option', $product_sale_module_option);

func_display('provider/home.tpl', $smarty);
?>