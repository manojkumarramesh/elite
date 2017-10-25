<?php

/*----- Include Files -----*/
require_once 'auth.php';
   
if(empty($action) || empty($product_id) || $action != 'rezently_viewed') {

    return false;
}

$sql    =   "SELECT
                DISTINCT(stats_shop.id),
                stats_shop.date
             FROM
                $sql_tbl[stats_shop] AS stats_shop
             WHERE
                stats_shop.action = 'V'
             AND
                stats_shop.id != ".$product_id."
             ORDER BY
                stats_shop.date DESC LIMIT 0, 6";
$result =   func_query($sql, USE_SQL_DATA_CACHE);

if(empty($result)) {

    return false;
}

foreach($result as $key => $value) {
    
    $product_ids[]  =   $value['id'];
}

x_load('product');

$query              =   " AND $sql_tbl[products].productid IN (".implode(',', $product_ids).")";

$orderby            =   " FIELD($sql_tbl[products].productid, ".implode(',', $product_ids).")";

$resently_viewed    =   func_search_products($query, 0, $orderby);

if(empty($resently_viewed)) {

    return false;
}

$smarty->assign('main', 'resently_viewed_product');
$smarty->assign('resently_viewed_products', $resently_viewed);

$data['html'] = func_display('customer/main/products_switchers.tpl', $smarty, false);

echo json_encode($data);