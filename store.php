<?php

/**
 * Vendor Shop Interface
 *
 * @subpackage Customer interface
 * @author     manoj@elitehour.com
 * @modified   07-Dec-2012
 */

require_once 'auth.php';
require_once $xcart_dir.'/include/classes/class.product.php';

//ini_set("display_errors", 1);

$config['Ability_Template']['abi_store_layout'] = '2_cols_left';
$config['Ability_Template']['abi_vert_menu']    = 'flyout';

if(empty($xmall_store_info) || empty($xmall_store_info['providerid'])) {

    func_header_location("home.php");
    return false;
}

$cat = isset($cat) ? abs(intval($cat)) : 0;

if(empty($cat) === false) {

    $shop['cat'] = $cat;
    $smarty->assign('cat', $cat);
}

$shop['provider']           = $xmall_store_info['provider'];
$shop['welcome_message']    = $xmall_store_info['config']['welcome_message'];
$shop['providerid']         = $xmall_store_info['providerid'];
$shop['vertical_menu']      = json_decode($xmall_store_info['vertical_menu_list'], true);

if(empty($shop['vertical_menu'])) {

    $shop['vertical_menu']  = Product::func_store_categories($shop['providerid']);
}

$data = Product::func_fetch_vendor_total_products($shop['providerid']);

if(empty($data) === false) {

    $vendor_data            = json_decode($data);
    $shop['total_products'] = $vendor_data->total;
    $shop['feedback']       = Product::func_fetch_vendor_feedback($shop['providerid']);
    $shop['total_views']    = $vendor_data->viewed;
    $shop['total_sold']     = $vendor_data->sold;
}

$shop['vendor_info']        = Product::func_fetch_vendor_info($shop['providerid']);

if(isset($search_query) && empty($search_query) === false) {

    $shop['search_query']   = urldecode($search_query);
    $search                 = '&search_query='.urlencode($shop['search_query']);
}

require_once $xcart_dir.'/include/common.php';
require_once $xcart_dir.'/products.php';

if(empty($cat) === false) {

     $sql   =   "SELECT
                    categories.category
                 FROM
                    $sql_tbl[categories] AS categories
                 WHERE
                    categories.categoryid = ".$cat;
    $result =   func_query_first($sql, USE_SQL_DATA_CACHE);
    $smarty->assign('category_name', $result['category']);

    $child_categories = Product::func_store_child_categories($shop['providerid'], $cat);

    if(empty($child_categories) === false) {

        $smarty->assign('child_categories', json_decode($child_categories, true));
    }
    $page_url = 'cat='.$cat;
}

$smarty->assign('main', 'vendor_store');
$smarty->assign('shop', $shop);
$smarty->assign('navigation_script', $xmall_store_info['full_directory'].'/store.php?'.$page_url.$search);

func_display('customer/home.tpl', $smarty);