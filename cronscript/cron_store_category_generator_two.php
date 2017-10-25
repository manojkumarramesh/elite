<?php

function func_fetch_childs($categoryid, $providerid)
{
    $sql    =   "SELECT
                    categories.categoryid,
                    categories.category,
                    seller_categories.product_count as total
                FROM
                    xcart_categories AS categories
                INNER JOIN
                    bvira_seller_categories AS seller_categories
                ON
                    seller_categories.categoryid = categories.categoryid
                AND
                    seller_categories.provider = ".$providerid."
                 WHERE
                    categories.parentid = ".$categoryid."
                 AND
                    categories.avail = 'Y'";
    $result =   func_query($sql, USE_SQL_DATA_CACHE);

    if(empty($result)) {

        return false;
    }

    foreach($result as $key => $value) {

        $array[$key]['categoryid']  = $value['categoryid'];
        $array[$key]['category']    = $value['category'];
        $array[$key]['total']       = $value['total'];
        $array[$key]['childs']      = func_fetch_childs($value['categoryid'], $providerid);
    }

    return $array;
}

$sql    =   "SELECT
                DISTINCT(customers.id) as providerid,
                customers.email
            FROM
                xcart_customers AS customers
            INNER JOIN
                bcse_xmall_stores AS xmall_stores
            ON
                xmall_stores.provider = customers.email
            LEFT JOIN
                bcse_xmall_store_categories AS xmall_store_categories
            ON
                xmall_store_categories.provider = xmall_stores.provider
            WHERE
                customers.usertype = 'P'
            ORDER BY
                customers.id";
$result =   func_query($sql, USE_SQL_DATA_CACHE);

if(empty($result)) {

    return false;
}

foreach($result as $key => $value)
{
    $array   =  array();

    $id      =  trim($value['providerid']);
    $email   =  trim($value['email']);

    $sql1    =  "SELECT
                    xmall_store_categories.categoryid
                 FROM
                    bcse_xmall_store_categories AS xmall_store_categories
                 WHERE
                    xmall_store_categories.provider = '".$email."'";
    $result1 =  func_query($sql1, USE_SQL_DATA_CACHE);

    if(empty($result1) === false) {

        foreach($result1 as $key1 => $value1)
        {
            $sql2       =   "SELECT
                                categories.categoryid,
                                categories.category,
                                seller_categories.product_count as total
                             FROM
                                xcart_categories AS categories
                             LEFT JOIN
                                bvira_seller_categories AS seller_categories
                             ON
                                seller_categories.categoryid = categories.categoryid
                             AND
                                seller_categories.provider = ".$id."
                             WHERE
                                seller_categories.categoryid = ".$value1['categoryid']."
                             AND
                                categories.avail = 'Y'";
            $result2    =   func_query_first($sql2, USE_SQL_DATA_CACHE);

            if(empty($result2)) {

                $sql2       =   "SELECT
                                    categories.categoryid,
                                    categories.category
                                 FROM
                                    xcart_categories AS categories
                                 WHERE
                                    categories.categoryid = ".$value1['categoryid']."
                                 AND
                                    categories.avail = 'Y'";
                $result2    =   func_query_first($sql2, USE_SQL_DATA_CACHE);
            }

            $array[$key1]['categoryid'] = $result2['categoryid'];
            $array[$key1]['category']   = $result2['category'];
            $array[$key1]['total']      = $result2['total'];
            $array[$key1]['childs']     = func_fetch_childs($value1['categoryid'], $id);
        }
    }

    $data = json_encode($array);

    if(empty($data) === false) {

        func_array2update('xmall_stores', array('vertical_menu_list' => addslashes($data)), "provider = '$email'");
    }
    else {

        func_array2update('xmall_stores', array('vertical_menu_list' => ''), "provider = '$email'");
    }
}