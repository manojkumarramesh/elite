<?php
/* vim: set ts=4 sw=4 sts=4 et: */
/*****************************************************************************\
+-----------------------------------------------------------------------------+
| X-Cart                                                                      |
| Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>                  |
| All rights reserved.                                                        |
+-----------------------------------------------------------------------------+
| PLEASE READ  THE FULL TEXT OF SOFTWARE LICENSE AGREEMENT IN THE "COPYRIGHT" |
| FILE PROVIDED WITH THIS DISTRIBUTION. THE AGREEMENT TEXT IS ALSO AVAILABLE  |
| AT THE FOLLOWING URL: http://www.x-cart.com/license.php                     |
|                                                                             |
| THIS  AGREEMENT  EXPRESSES  THE  TERMS  AND CONDITIONS ON WHICH YOU MAY USE |
| THIS SOFTWARE   PROGRAM   AND  ASSOCIATED  DOCUMENTATION   THAT  RUSLAN  R. |
| FAZLYEV (hereinafter  referred to as "THE AUTHOR") IS FURNISHING  OR MAKING |
| AVAILABLE TO YOU WITH  THIS  AGREEMENT  (COLLECTIVELY,  THE  "SOFTWARE").   |
| PLEASE   REVIEW   THE  TERMS  AND   CONDITIONS  OF  THIS  LICENSE AGREEMENT |
| CAREFULLY   BEFORE   INSTALLING   OR  USING  THE  SOFTWARE.  BY INSTALLING, |
| COPYING   OR   OTHERWISE   USING   THE   SOFTWARE,  YOU  AND  YOUR  COMPANY |
| (COLLECTIVELY,  "YOU")  ARE  ACCEPTING  AND AGREEING  TO  THE TERMS OF THIS |
| LICENSE   AGREEMENT.   IF  YOU    ARE  NOT  WILLING   TO  BE  BOUND BY THIS |
| AGREEMENT, DO  NOT INSTALL OR USE THE SOFTWARE.  VARIOUS   COPYRIGHTS   AND |
| OTHER   INTELLECTUAL   PROPERTY   RIGHTS    PROTECT   THE   SOFTWARE.  THIS |
| AGREEMENT IS A LICENSE AGREEMENT THAT GIVES  YOU  LIMITED  RIGHTS   TO  USE |
| THE  SOFTWARE   AND  NOT  AN  AGREEMENT  FOR SALE OR FOR  TRANSFER OF TITLE.|
| THE AUTHOR RETAINS ALL RIGHTS NOT EXPRESSLY GRANTED BY THIS AGREEMENT.      |
|                                                                             |
| The Initial Developer of the Original Code is Ruslan R. Fazlyev             |
| Portions created by Ruslan R. Fazlyev are Copyright (C) 2001-2011           |
| Ruslan R. Fazlyev. All Rights Reserved.                                     |
+-----------------------------------------------------------------------------+
\*****************************************************************************/

/**
 * Recommends list
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Customer interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: recommends.php,v 1.34.2.6 2011/06/15 12:04:35 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: home.php"); die("Access denied"); }

$config['Recommended_Products']['number_of_recommends'] = max(intval($config['Recommended_Products']['number_of_recommends']), 0);

/**
 * Defining the conditions for selecting products for the list
 */

$avail_group_by = $avail_where = $avail_join = '';

if (
    $config['General']['unlimited_products'] != 'Y'
    && $config['General']['show_outofstock_products'] != 'Y'
) {

    if (!empty($active_modules['Product_Options'])) {

        $avail_join     = "LEFT JOIN $sql_tbl[variants] ON $sql_tbl[variants].productid = $sql_tbl[products].productid";
        $avail_where     = "IFNULL($sql_tbl[variants].avail, $sql_tbl[products].avail) > 0 AND";
        if ($config['Recommended_Products']['select_recommends_list_randomly'] == 'Y')
            $avail_group_by = "GROUP BY $sql_tbl[product_rnd_keys].productid";
        else     
            $avail_group_by = "GROUP BY $sql_tbl[products].productid";

    } else {

        $avail_where = " $sql_tbl[products].avail>'0' AND";

    }

}

$_membershipid = intval($user_account['membershipid']);

$query_ids = array();

$condition = "";
$join      = "";

//Task : fetch only products from this provider id - starts
if(empty($product_info) === false && empty($product_info['provider']) === false) {

   // $condition .= "AND $sql_tbl[products].provider = ".$product_info['provider'];
     $condition .= "AND $sql_tbl[products].provider = $favorite_seller ";
}
//Task : fetch only products from this provider id - ends

//Task : fetch Related Products by extra_fields - starts
if(empty($extra_fields) === false) {

    foreach($extra_fields as $key => $value) {

        if(empty($value['field_value']) === false) {

            $fld[] = "$sql_tbl[extra_field_values].fieldid = ".$value['fieldid']." AND $sql_tbl[extra_field_values].value LIKE '".$value['field_value']."'";
        }
    }
    
    if(empty($fld) === false) {

        $join      .= "LEFT JOIN $sql_tbl[extra_field_values]
                              ON ($sql_tbl[extra_field_values].productid = $sql_tbl[products].productid)
                       LEFT JOIN $sql_tbl[extra_fields]
                              ON ($sql_tbl[extra_fields].fieldid = $sql_tbl[extra_field_values].fieldid)";
        $condition .= " AND (".implode(" OR ", $fld).") ";
    }
}
//Task : fetch Related Products by extra_fields - ends

//Task : fetch Related Products by variants - starts
if(empty($product_options) === false) {

    foreach($product_options as $key => $value) {

        if(empty($value['options']) === false) {

            foreach($value['options'] as $key1 => $value1) {

                if(empty($value1['option_name']) === false) {

                    //$class[$value1['classid']][] = $value1['option_name'];
                    //$sql_tbl[class_options].classid = ".$value1['classid']." AND
                    $fld1[] = "$sql_tbl[class_options].option_name LIKE '".$value1['option_name']."'";
                }
            }
        }
    }

    if(empty($fld1) === false) {

        $join      .= "LEFT JOIN $sql_tbl[classes]
                              ON ($sql_tbl[classes].productid = $sql_tbl[products].productid)
                       LEFT JOIN $sql_tbl[class_options]
                              ON ($sql_tbl[class_options].classid = $sql_tbl[classes].classid)";
        $condition .= " AND (".implode(" OR ", $fld1).") ";
    }
}
//Task : fetch Related Products by variants - ends

if ($config['Recommended_Products']['select_recommends_list_randomly'] == 'Y') {

    func_refresh_product_rnd_keys();

    $max_rnd_number = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[products]");
    $rnd = rand(1, $max_rnd_number);
    $_sort_order = $rnd % 2 ? 'DESC' : 'ASC';
    /*$sql = "
      SELECT DISTINCT($sql_tbl[products].productid)
        FROM ($sql_tbl[products], $sql_tbl[categories], $sql_tbl[products_categories], $sql_tbl[product_rnd_keys])
   LEFT JOIN $sql_tbl[category_memberships]
          ON $sql_tbl[category_memberships].categoryid   =  $sql_tbl[products_categories].categoryid
   LEFT JOIN $sql_tbl[product_memberships]
          ON $sql_tbl[product_memberships].productid     =  $sql_tbl[products_categories].productid
             ".$join."
             $avail_join
       WHERE $avail_where $sql_tbl[products].forsale     =  'Y'
         AND $sql_tbl[products].productid                =  $sql_tbl[products_categories].productid
         AND $sql_tbl[products_categories].main          =  'Y'
         AND $sql_tbl[categories].categoryid             =  $sql_tbl[products_categories].categoryid
         AND $sql_tbl[categories].avail                  =  'Y'
         AND ($sql_tbl[category_memberships].membershipid IS NULL
          OR $sql_tbl[category_memberships].membershipid =  '$_membershipid')
         AND ($sql_tbl[product_memberships].membershipid IS NULL
          OR $sql_tbl[product_memberships].membershipid  =  '$_membershipid')
         AND $sql_tbl[products].productid                =  $sql_tbl[product_rnd_keys].productid  
         AND $sql_tbl[product_rnd_keys].rnd_key          >= $rnd
         AND $sql_tbl[products_categories].categoryid    =  '$cat'
             ".$condition."
             $avail_group_by
    ORDER BY $sql_tbl[product_rnd_keys].rnd_key $_sort_order LIMIT
             ".($config["Recommended_Products"]["number_of_recommends"] + 1);
     * 
     */
    
$sql = "
      SELECT DISTINCT($sql_tbl[products].productid)
        FROM ($sql_tbl[products], $sql_tbl[categories], $sql_tbl[products_categories], $sql_tbl[product_rnd_keys])
             ".$join."
             $avail_join
       WHERE $avail_where $sql_tbl[products].forsale     =  'Y'
         AND $sql_tbl[products].productid                =  $sql_tbl[products_categories].productid
         AND $sql_tbl[products_categories].main          =  'Y'
         AND $sql_tbl[categories].categoryid             =  $sql_tbl[products_categories].categoryid
         AND $sql_tbl[categories].avail                  =  'Y'
         AND $sql_tbl[products].productid                =  $sql_tbl[product_rnd_keys].productid  
         AND $sql_tbl[product_rnd_keys].rnd_key          >= $rnd ";
        
if($product_info['provider']    ==  $favorite_seller)
    $sql    .=   "AND $sql_tbl[products_categories].categoryid    =  '$cat' ";
        
$sql    .=   $condition."
             $avail_group_by
    ORDER BY $sql_tbl[product_rnd_keys].rnd_key $_sort_order LIMIT
             ".($config["Recommended_Products"]["number_of_recommends"] + 1);

//echo $sql; die;
    $query_ids =  func_query_column($sql);
}
else {

    $nr = $config["Recommended_Products"]["number_of_recommends"] * 2 + 1;
    if (defined('X_MYSQL41_COMP_MODE')) {
        $query_ids = func_query_column($sql
        = "
            SELECT sp2.productid
             FROM (
                    SELECT DISTINCT userid
                      FROM $sql_tbl[stats_customers_products]
                    WHERE productid                              =  '$productid'
                  ) AS sp1
             INNER JOIN $sql_tbl[stats_customers_products] AS sp2
                ON sp1.userid                                     =  sp2.userid
               AND sp2.productid                                 != '$productid'
             INNER JOIN $sql_tbl[products_categories]
                ON sp2.productid                                 =  $sql_tbl[products_categories].productid
               AND $sql_tbl[products_categories].main            =  'Y'
             INNER JOIN $sql_tbl[categories]
                ON $sql_tbl[products_categories].categoryid      =  $sql_tbl[categories].categoryid
               AND $sql_tbl[categories].avail                    =  'Y'
             INNER JOIN $sql_tbl[products]
                ON $sql_tbl[products].productid                  =  sp2.productid
               AND $sql_tbl[products].forsale                    =  'Y'
              LEFT JOIN $sql_tbl[category_memberships]
                ON $sql_tbl[category_memberships].categoryid     =  $sql_tbl[products_categories].categoryid
              LEFT JOIN $sql_tbl[product_memberships]
                ON $sql_tbl[product_memberships].productid       =  $sql_tbl[products_categories].productid 
             $avail_join
             WHERE $avail_where 1
               AND ( $sql_tbl[category_memberships].membershipid =  '$_membershipid'
                OR $sql_tbl[category_memberships].membershipid IS NULL)
               AND ( $sql_tbl[product_memberships].membershipid IS NULL
                OR $sql_tbl[product_memberships].membershipid    =  '$_membershipid') 
             $avail_group_by
             ORDER BY $sql_tbl[products].product 
             LIMIT $nr");         
    } else {
        $query_ids = func_query_column($sql
        = "
           SELECT DISTINCT sp2.productid, $sql_tbl[products].productid
             FROM ($sql_tbl[stats_customers_products] as sp1, $sql_tbl[stats_customers_products] AS
                  sp2, $sql_tbl[products], $sql_tbl[categories], $sql_tbl[products_categories])
             LEFT JOIN $sql_tbl[category_memberships]
               ON $sql_tbl[category_memberships].categoryid    =  $sql_tbl[products_categories].categoryid
             LEFT JOIN $sql_tbl[product_memberships]
               ON $sql_tbl[product_memberships].productid      =  $sql_tbl[products_categories].productid 
            $avail_join
            WHERE $avail_where sp1.productid                   =  '$productid'
              AND sp1.userid                                   =  sp2.userid
              AND $sql_tbl[products].productid                 =  sp2.productid
              AND $sql_tbl[products].forsale                   =  'Y'
              AND $sql_tbl[products].productid                 =  $sql_tbl[products_categories].productid
              AND $sql_tbl[products_categories].main           =  'Y'
              AND $sql_tbl[categories].categoryid              =  $sql_tbl[products_categories].categoryid
              AND $sql_tbl[categories].avail                   =  'Y'
              AND ($sql_tbl[category_memberships].membershipid =  '$_membershipid'
               OR $sql_tbl[category_memberships].membershipid IS NULL)
              AND ($sql_tbl[product_memberships].membershipid IS NULL
               OR $sql_tbl[product_memberships].membershipid   =  '$_membershipid') 
            $avail_group_by
            ORDER BY $sql_tbl[products].product 
            LIMIT $nr");
    } 
}

if (is_array($query_ids)) {
    $_own_ind = array_search($productid, $query_ids);
    if ($_own_ind !== false)
        unset($query_ids[$_own_ind]);
}    

x_load('product');

$recommends = func_search_products(
    " AND $sql_tbl[products].productid IN ('" . implode("','", $query_ids) . "')",
    (isset($user_account) && isset($user_account['membershipid']))
        ? max(intval($user_account['membershipid']), 0)
        : 0,
    false,
    $config['Recommended_Products']['number_of_recommends']
);

//Elitehour - No need this bloc all the neccssary info coming from main products, so comment it
/*
# Ability Template
if (!empty($recommends))
foreach ($recommends as $rid => $prod) {
$prod_tmp = func_select_product($prod['productid'], (isset($user_account) && isset($user_account["membershipid"])) ? max(intval($user_account["membershipid"]), 0) : 0);
$recommends["$rid"]['descr'] = $prod_tmp['descr'];

foreach($recommends as $key=> $product)
{
    $recommends[$key]["customer_details"] = func_query("
            SELECT $sql_tbl[customers].firstname, $sql_tbl[customers].lastname, $sql_tbl[images_PL].image_path, $sql_tbl[images_PL].image_x, $sql_tbl[images_PL].image_y
            FROM $sql_tbl[customers]
            LEFT JOIN $sql_tbl[products]
            ON $sql_tbl[products].provider = $sql_tbl[customers].id
            LEFT JOIN $sql_tbl[images_PL]
            ON $sql_tbl[images_PL].id = $sql_tbl[products].productid
            WHERE $sql_tbl[products].productid = ".$product["productid"]
        );
}

}
# /Ability Template
 * */

if(empty($recommends) === false) {

    $smarty->assign('recommends', $recommends);
}

?>