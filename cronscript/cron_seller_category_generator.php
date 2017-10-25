<?php

/**
 * To genearte sellers category products count
 * @added      17-Oct-2012
 * @modified   17-Oct-2012
 */

/*----- Include Files -----*/
require_once dirname(__FILE__) . '/../admin/auth.php';
require_once dirname(__FILE__) . '/../include/classes/class.product.php';

//get providers list - starts
$sql        =   "SELECT
                    customers.id
                 FROM
                    $sql_tbl[customers] AS customers
                 WHERE
                    customers.usertype = 'P'
                 ORDER BY
                    customers.id";
$result     =   func_query($sql);

if(empty($result)) {

    return false;
}
//get providers list - ends

//get parent categories - starts
$sql1       =   "SELECT
                    categories.categoryid,
                    categories.category
                FROM
                    $sql_tbl[categories] AS categories
                WHERE
                    categories.avail = 'Y'
                AND
                    categories.parentid = '0'
                ORDER BY
                    categories.categoryid";
$result1    =   func_query($sql1);

if(empty($result1)) {

    return false;
}

foreach($result1 as $key1 => $value1) {

    $array_1[]   =   $value1['categoryid'];
}

$array1     =   implode(',', $array_1);
//get parent categories - ends

//get first level categories list - starts
$sql2       =   "SELECT
                    categories.categoryid,
                    categories.category
                 FROM
                    $sql_tbl[categories] AS categories
                 WHERE
                    categories.parentid IN (".$array1.")
                 AND
                    categories.avail = 'Y'
                 ORDER BY
                    categories.categoryid";
$result2    =   func_query($sql2);

if(empty($result2)) {

    return false;
}

foreach($result2 as $key2 => $value2) {

    $array_2[]   =   $value2['categoryid'];
}

$array2     =   implode(',', $array_2);
//get first level categories list - ends

//merge parent and first level categories - starts
$category_array =   array_merge($array_1, $array_2);

$category_ids   =   implode(',', $category_array);
//merge parent and first level categories - ends

//get product count for providers and update result to db - starts
foreach($result as $key => $value) {

    $provider   =   $value['id'];

    $sql3       =   "SELECT
                        DISTINCT(categories.categoryid),
                        categories.category,
                        COUNT(products.productid) as product_count
                     FROM
                        $sql_tbl[products] AS products
                     JOIN
                        $sql_tbl[products_categories] AS products_categories
                     ON
                        (products_categories.productid = products.productid)
                     JOIN
                        $sql_tbl[categories] AS categories
                     ON
                        (categories.categoryid = products_categories.categoryid)
                     WHERE
                        products.provider = (".$provider.")
                     AND
                        products.forsale = 'Y'
                     AND
                        categories.avail = 'Y'
                     AND
                        categories.categoryid IN (".$category_ids.")
                     GROUP BY
                        categories.categoryid
                     ORDER BY
                        categories.categoryid";
    $result3    =   func_query($sql3);
    
    if(empty($result3) === false && is_array($result3)) {
    
        foreach($result3 as $key3 => $value3) {

            $categoryid     =   $value3['categoryid'];
            $product_count  =   $value3['product_count'];

            $sql4           =   "SELECT
                                    COUNT(*)
                                 FROM
                                    $sql_tbl[seller_categories] AS seller_categories
                                 WHERE
                                    seller_categories.categoryid = ".$categoryid."
                                 AND
                                    seller_categories.provider = ".$provider;
            $is_exists      =   func_query_first_cell($sql4);

            if($is_exists) {

                $this_data  =   array(
                                    'product_count' =>  $product_count
                                );
                func_array2update('seller_categories', $this_data, "categoryid = ".$categoryid." AND provider = ".$provider."");
            }
            else {

                $this_data  =   array(
                                    'categoryid'    =>  $categoryid,
                                    'provider'      =>  $provider,
                                    'product_count' =>  $product_count
                                );
                func_array2insert('seller_categories', $this_data, true);
            }
        }
    }
}
//get product count for providers and update result to db - ends