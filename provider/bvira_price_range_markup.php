<?php

require_once('./auth.php');

x_load('product');

$actions    =   ($_POST['action'] != '') ? $_POST['action'] : $_GET['action'];
$seller_id  =   $GLOBALS['user_account']['id'];

define('UPDATE_PRICERANGE','Update');
define('GET_PRODUCTS_COUNT','get_products_count');

switch($actions)
{
    case UPDATE_PRICERANGE:
        
        $list_price_markup_type             =   $ws_list_price_type;
        $list_price_markup                  =   $ws_list_price;
        $sale_price_markup_type             =   $ws_sale_price_type;
        $sale_price_markup                  =   $ws_sale_price;
        $lowest_possible_price_markup_type  =   $ws_lowest_possible_price_type;
        $lowest_possible_price_markup       =   $ws_lowest_possible_price;


        $product_qry     =  "SELECT
                                products.productid,
                                products.whole_sale_price
                             FROM
                                $sql_tbl[products] AS products
                             INNER JOIN
                                $sql_tbl[order_details] AS order_details
                             ON
                                (order_details.productid = products.productid)
                             INNER JOIN
                                $sql_tbl[orders] AS orders
                             ON
                                (orders.orderid = order_details.orderid)
                             WHERE
                                orders.status IN ('P','B','C')
                             AND
                                products.provider = $seller_id";
        $products        =  db_query($product_qry);
        $products_count  =  mysql_num_rows($products);

        if($ws_checkbox_update=='1') {

            while($products_res = mysql_fetch_assoc($products)) {

                $productid                      =   '';
                $product_list_price             =   '';
                $product_lowest_possible_price  =   '';
                $product_whole_sale_price       =   '';
                $product_sale_price             =   '';
                $insert_pricing                 =   '';
                $productid                      =   $products_res['productid'];
                $product_whole_sale_price       =   $products_res['whole_sale_price'];

                //find list price
                if($list_price_markup_type == 'A')
                    $product_list_price =   $product_whole_sale_price + $list_price_markup;
                else
                    $product_list_price =   $product_whole_sale_price + (($list_price_markup / 100) * $product_whole_sale_price);

                //find lowest possible price
                if($lowest_possible_price_markup_type == 'A')
                    $product_lowest_possible_price =   $product_whole_sale_price + $lowest_possible_price_markup;
                else
                    $product_lowest_possible_price =   $product_whole_sale_price + (($lowest_possible_price_markup / 100) * $product_whole_sale_price);

                //find sale price
                if($sale_price_markup_type == 'A')
                    $product_sale_price =   $product_whole_sale_price + $sale_price_markup;
                else
                    $product_sale_price =   $product_whole_sale_price + (($sale_price_markup / 100) * $product_whole_sale_price);

                //update price detail in prodcut table
                db_query("UPDATE $sql_tbl[products] SET list_price=$product_list_price, lowest_possible_price=$product_lowest_possible_price WHERE productid=$productid");

                //get product sale price avail in pricing table
                $product_sale_price_qry     =   "SELECT price FROM $sql_tbl[pricing] WHERE productid=$productid";
                $product_sale_price_res     =   db_query($product_sale_price_qry);
                $product_sale_price_avail   =   mysql_num_rows($product_sale_price_res);

                if($product_sale_price_avail>0) {

                    $sale_price_query   =   "UPDATE $sql_tbl[pricing] SET price=$product_sale_price WHERE productid=$productid";
                }
                else {

                    $sale_price_query   =   "INSERT INTO $sql_tbl[pricing] (productid, quantity, price) VALUES ($productid, 1, $product_sale_price)";
                    $insert_pricing     =   1;
                }

                //update sale price in pricing table
                $insert_id  =   db_query($sale_price_query);

                if($insert_pricing == 1) {

                    $insert_id          =   mysql_insert_id();
                    $quick_price_query  =   "INSERT INTO $sql_tbl[quick_prices] (productid, priceid) VALUES ($productid, $insert_id)";
                    db_query($quick_price_query);
                }
            }
        }
        
        if($saleid != '') {

            $products_query_data    =   array(
                                            'saleid' => $saleid
                                        );
            $ws_update_query        =   func_array2update('xcart_products', $products_query_data, "provider=$seller_id");
        }

        if($ws_shipping_type == "FS") {

            $free_shipping          =   'Y';
            $products_query_data    =   array(                
                                            'free_shipping'    => $free_shipping,
                                            'shipping_freight' => '0'
                                        );
            $ws_update_query        = func_array2update('xcart_products', $products_query_data, "provider=$seller_id");
        }
        elseif($ws_shipping_type == "FX") {

            $free_shipping          =   'N';
            $products_query_data    =   array(                
                                            'free_shipping'    => $free_shipping,
                                            'shipping_freight' => $ws_fixed_shipping_price
                                        );
            $ws_update_query        =   func_array2update('xcart_products', $products_query_data, "provider=$seller_id");
        }

        //Checkbox set product live is enable
        if($ws_chbox_live=='1') {

            $update_sql =   "UPDATE $sql_tbl[products] SET forsale='Y' WHERE provider=$seller_id";           
        } 
        else {

            $update_sql =   "UPDATE $sql_tbl[products] SET forsale='N' WHERE provider=$seller_id";
        }

        mysql_query($update_sql);

        $top_message['content'] = "Prices have been successfully updated for $products_count products";

        /*
        $memcache_obj   =   new Memcache;
        $memcache_obj->connect('localhost', 11211);
        $memcache_obj->flush();
        */

        func_header_location("bvira_price_range_markup.php");
        break;

    case GET_PRODUCTS_COUNT:

        $product_qry    =   "SELECT count(1)
                             FROM $sql_tbl[products]
                             WHERE provider = ".$seller_id;
        $products_count =   func_query_first_cell($product_qry);

        echo "<b>".number_format($products_count)."</b> products available";    

        die;
        break;
}

/*----- smarty assign -----*/
$smarty->assign('main', 'bvira_price_range_markup');
$smarty->assign('action', $actions);
$smarty->assign('userid', $GLOBALS['user_account']['id']);
$smarty->assign('product_sale_module_option', $product_sale_module_option);

func_display('provider/home.tpl', $smarty);