<?php

require_once dirname(__FILE__) . '/../admin/auth.php';

//ini_set("display_errors", 1);

if($config['General']['show_outofstock_products'] != 'Y') {

    $condition = " AND products.avail > 0 ";
}

$sql    =   "SELECT
                categories.categoryid,
                products.provider,
                COUNT(products.productid) as total
            FROM
                $sql_tbl[products] AS products
            INNER JOIN
                $sql_tbl[customers] AS customers
            ON
                customers.id = products.provider
            AND
                customers.activity = 'Y'
            INNER JOIN
                $sql_tbl[products_categories] AS products_categories
            ON
                products_categories.productid = products.productid
            INNER JOIN
                $sql_tbl[categories] AS categories
            ON
                categories.categoryid = products_categories.categoryid
            AND
                categories.avail = 'Y'
            WHERE
                customers.usertype = 'P'
            AND
                products.forsale = 'Y'
            ".$condition."
            GROUP BY
                categories.categoryid, products.provider
            ORDER BY
                categories.categoryid";
$result =   func_query($sql);

if(empty($result)) {

    return false;
}

db_query("TRUNCATE TABLE bvira_seller_categories");

foreach($result as $key => $value) {

    $data   =   array(
                    'categoryid'    =>  $value['categoryid'],
                    'provider'      =>  $value['provider'],
                    'product_count' =>  $value['total']
                );
    func_array2insert('seller_categories', $data, true);
}

require_once 'cron_store_category_generator_two.php';