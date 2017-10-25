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
 * Products search library
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: search.php,v 1.233.2.22 2011/09/12 15:00:15 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

x_session_unregister('product_search_query');

$advanced_options = array(
    'productcode',
    'productid',
    'provider',
    'price_max',
    'avail_max',
    'weight_max',
    'forsale',
    'flag_free_ship',
    'flag_ship_freight',
    'flag_global_disc',
    'flag_free_tax',
    'flag_min_amount',
    'flag_low_avail_limit',
    'flag_list_price',
    'flag_vat',
    'flag_gstpst',
    'manufacturers',
    'wholesalers',
    'categoryid',
    'fclassid',
);

/*$sort_fields = array(
    'productcode' => 'lbl_sku',
    'title'       => 'lbl_product',
    'price'       => 'lbl_price',
    'orderby'     => 'lbl_default',
);*/  //Elite hour - 23 nov 2012

$sort_fields = array(
        'price'       => 'lbl_price'
);

if (
    $config['Appearance']['display_productcode_in_list'] != 'Y'
    && (
        $current_area == 'C'
        || $current_area == 'B'
    )
) {
    unset($sort_fields['productcode']);
}

if (
    $config['General']['skip_categories_checking'] == 'Y'
    && $current_area == 'C'
) {
    unset($sort_fields['orderby']);
}

if (
    $config['General']['use_simple_product_sort'] == 'Y'
    && $current_area == 'C'
) {
    $sort_fields = array();
}


$_inner_search = !isset($_inner_search)
    ? false
    : $_inner_search;

x_load(
    'files',
    'image'
);

if (
    $current_area == 'A'
    || $current_area == 'P'
) {
    $sort_fields['quantity'] = 'lbl_in_stock';
}

if (empty($search_data)) {
    $search_data = array();
}

if (
    isset($search_data['products'])
    && !is_array($search_data['products'])
) {
    $search_data['products'] = array();
}

if (
    $REQUEST_METHOD == 'POST'
    && $mode == 'search'
) {

    // Update the session $search_data variable from $posted_data

    if (
        !empty($posted_data)
        && is_array($posted_data)
    ) {

        $need_advanced_options = false;

        foreach ($posted_data as $k => $v) {

            if (
                !is_array($v)
                && !is_numeric($v)
            ) {
                $posted_data[$k] = stripslashes($v);
            }

            if (
                in_array($k, $advanced_options)
                && $v !== ''
            ) {
                $need_advanced_options = true;
            }
        }

        if (!$need_advanced_options)
            $need_advanced_options = (doubleval(@$posted_data['price_min']) != 0 || intval(@$posted_data['avail_min']) != 0 || doubleval(@$posted_data['weight_min']) != 0);

        if (!$need_advanced_options && $current_area == 'C' && !empty($posted_data['categoryid']))
            $need_advanced_options = true;

        $posted_data['need_advanced_options'] = $need_advanced_options;

        // Data convertation for Feature comparison module

        if(!empty($active_modules['Feature_Comparison'])) {
            include $xcart_dir . '/modules/Feature_Comparison/search_define.php';
        }

        if (empty($search_data['products']['sort_field'])) {

            if (
                $current_area == 'C'
                && !empty($config['Appearance']['products_order'])
            ) {

                $posted_data['sort_field'] = $config['Appearance']['products_order'];
                $posted_data['sort_direction'] = 1;

            } else {

                $posted_data['sort_field'] = 'title';
                $posted_data['sort_direction'] = 0;

            }

        } else {

            $posted_data['sort_field'] = $search_data['products']['sort_field'];
            $posted_data['sort_direction'] = $search_data['products']['sort_direction'];

        }

        func_unset($posted_data, '_');

        if (!is_array($posted_data)) {

            $posted_data = array();

        }

        $search_data['products'] = $posted_data;

    }

    $url = 'search.php?mode=search&page=1';

    if (defined('X_SEARCH_URL')) {

        $url = X_SEARCH_URL . (strpos(X_SEARCH_URL, '?') === false ? '?' : '&') . 'mode=search&page=1';

    }

    func_header_location($url);
}

if ($mode == 'search') {
    
    // Perform search and display results

    $data = array();

    $flag_save = false;

    // Initialize service arrays

    $fields       = array();
    $fields_count = array();
    $from_tbls    = array();
    $inner_joins  = array();
    $left_joins   = array();
    $where        = array();
    $groupbys     = array();
    $having       = array();
    $orderbys     = array();
    $possible_groupbys = array();

    // Prepare the search data

    if (
        !empty($sort) 
        && is_scalar($sort)
        && isset($sort_fields[$sort])
    ) {

        // Store the sorting type in the session
        $search_data['products']['sort_field'] = $sort;
        $flag_save = true;

    } else {

        unset($sort);

    }

    if (isset($sort_direction)) {
        // Store the sorting direction in the session
        $search_data['products']['sort_direction'] = $sort_direction;
        $flag_save = true;
    }

    if (
        $current_area == 'C'
        && !empty($config['Appearance']['products_order'])
        && empty($search_data['products']['sort_field'])
    ) {
        $search_data['products']['sort_field'] = $config['Appearance']['products_order'];
        $search_data['products']['sort_direction'] = 0;
        
    }

    if (
        !empty($page)
        && (
            !isset($search_data['products']['page'])
            || $search_data['products']['page'] != intval($page)
        )
    ) {
        // Store the current page number in the session
        $search_data['products']['page'] = $page;
        $flag_save = true;
    }

    if (is_array($search_data['products'])) {

        $data = $search_data['products'];

        // convert fields to the appropriate types
        $num_fields_to_convert_names = array(
            'price'  => 'doubleval',
            'avail'  => 'intval',
            'weight' => 'doubleval',
        );

        $num_fields_to_convert_types = array(
            'min',
            'max',
        );

        foreach ($num_fields_to_convert_names as $name => $func_to_convert) {

            foreach ($num_fields_to_convert_types as $type) {

                $field = $name . '_' . $type;

                if (!empty($data[$field])) {

                    $data[$field] = $func_to_convert($data[$field]);

                    $search_data['products'][$field] = $data[$field] = ($data[$field] < 0) ? 0 : $data[$field];

                }

            }

        }

        foreach ($data as $k => $v) {

            if (!is_array($v) && !is_numeric($v)) {

                $data[$k] = addslashes($v);

            }

        }

    } else {

        $search_data['products'] = array();

    }
    

    // Translate service data to inner service arrays

    if (!empty($data['_'])) {

        foreach ($data['_'] as $saname => $sadata) {

            if (isset($$saname) && is_array($$saname) && empty($$saname))
                $$saname = $sadata;

        }

    }

    $sort_string = '';

    $membershipid = isset($user_account['membershipid']) ? $user_account['membershipid'] : 0;

    $membershipid_string = ($membershipid == 0 || empty($active_modules['Wholesale_Trading'])) ? "= '0'" : "IN ('" . $membershipid . "', '0')";

    $fields[] = $sql_tbl['products'] . '.*';

    $inner_joins['pricing'] = array(
        'on' => $sql_tbl['pricing'] . '.productid = ' . $sql_tbl['products'] . '.productid'
    );
    $possible_groupbys[] = $sql_tbl['pricing'];

    $inner_joins['quick_flags'] = array(
        'on' => $sql_tbl['quick_flags'] . '.productid = ' . $sql_tbl['products'] . '.productid'
    );
    $possible_groupbys[] = $sql_tbl['quick_flags'];

    $fields[] = $sql_tbl['quick_flags'] . '.*';

    $inner_joins['quick_prices'] = array(
        'on' => $sql_tbl['quick_prices'] . '.productid = ' . $sql_tbl['products'] . '.productid AND ' . $sql_tbl['quick_prices'] . '.membershipid ' . $membershipid_string . ' AND ' . $sql_tbl['quick_prices'] . '.priceid = ' . $sql_tbl['pricing'] . '.priceid'
    );

    $possible_groupbys[] = $sql_tbl['quick_prices'];
    $fields[] = $sql_tbl['quick_prices'] . '.variantid';

    //Task : Multiple product listing - starts
    if($current_area == 'P' || $current_area == 'A') {
        $fields[] = $sql_tbl['categories'].'.full_category_path,'.$sql_tbl['wholesalers'].'.wholesaler_name';
        
        $left_joins['wholesalers'] = array(
            'on' => $sql_tbl['wholesalers'].'.wholesaler_id = '.$sql_tbl['products'].'.wholesaler_id'
        );
    }
    //Task : Multiple product listing - ends

    if ($membershipid == 0) {

        $fields[] = $sql_tbl['pricing'] . '.price';

    } else {

        $fields[] = 'MIN(' . $sql_tbl['pricing'] . '.price) as price';

    }

    if (!$single_mode && AREA_TYPE != 'A' && AREA_TYPE != 'P') {
        $inner_joins['ACHECK'] = array(
            'tblname' => 'customers',
            'on'      => $sql_tbl['products'] . '.provider = ACHECK.id AND ACHECK.activity=\'Y\''
        );
    }

    if (!empty($data['substring']))
        $data['substring'] = trim($data['substring']);

    $search_by_variants = false;
    $search_by_price = false;
    $skip_categories_checking = false;

    if (
        $config['General']['skip_categories_checking'] == 'Y'
        && empty($data['categoryid'])
        && $current_area == 'C'
    ) {
        $skip_categories_checking = true;
    }
    
    
    if (!empty($data['substring'])) {
        

        $condition = array();
        $search_string_fields = array();

        if (
            empty($data['by_title'])
            && empty($data['by_shortdescr'])
            && empty($data['by_fulldescr'])
            && empty($data['extra_fields'])
            && empty($data['by_sku'])
        ) {
            $search_data['products']['by_title'] = $data['by_title'] = 'Y';
            $flag_save = true;
        }
        
        // Search for substring in some fields...

        if (!empty($data['by_title'])) {
            $search_string_fields[] = 'product';
        }

        if (!empty($data['by_keywords'])) {
            $search_string_fields[] = 'keywords';
        }

        if (!empty($data['by_shortdescr'])) {
            $search_string_fields[] = 'descr';
        }

        if (!empty($data['by_fulldescr'])) {
            $search_string_fields[] = 'fulldescr';
        }

        if (
            (
                !empty($data['by_shortdescr'])
                || !empty($data['by_fulldescr'])
            )
            && $current_area == 'C'
            && !in_array('keywords', $search_string_fields)
        ) {
            $search_string_fields[] = 'keywords';
        }

        $search_words = array();

        if (in_array($data['including'], array("all", "any"))) {

            $tmp = trim($data['substring']);

            if (preg_match_all('/"([^"]+)"/', $tmp, $match)) {
                $search_words = $match[1];
                $tmp = str_replace($match[0], '', $tmp);
            }

            $tmp = explode(" ", $tmp);

            $tmp = func_array_map('trim', $tmp);

            $search_words = array_merge($search_words, $tmp);

            unset($tmp);

            // Check word length limit
            if ($search_word_length_limit > 0) {
                $search_words = preg_grep("/^..+$/", $search_words);
            }

            // Check stop words
            x_load('product');

            $stopwords = func_get_stopwords();

            if (!empty($stopwords) && is_array($stopwords)) {

                $tmp = preg_grep("/^(" . implode('|', $stopwords) . ")$/i", $search_words);

                if (!empty($tmp) && is_array($tmp)) {
                    $search_words = array_diff($search_words, $tmp);
                    $search_words = array_values($search_words);
                }

                unset($tmp);

            }

            // Check word count limit
            if ($search_word_limit > 0 && count($search_words) > $search_word_limit) {
                $search_words = array_splice($search_words, $search_word_limit - 1);
            }

            // RegExp quoting
            if (    
                !empty($search_words) 
                && $data['including'] == 'any'
            ) {
                foreach ($search_words as $k => $v) {
                    $search_words[$k] = addslashes(preg_quote(stripslashes($v)));
                }
            }

        }

        foreach ($search_string_fields as $ssf) {

            if (!empty($search_words) && in_array($data['including'], array("all", "any"))) {

                if ($data['including'] == 'all') {

                    $tmp = array();

                    foreach ($search_words as $sw) {

                        if (
                            in_array($current_area, array('C', 'B'))
                            && $config['General']['skip_lng_tables_join'] != 'Y'
                        ) {

                            $tmp[] = ($ssf == 'keywords')
                                ? '(' . $sql_tbl['products_lng'] . '.' . $ssf . ' LIKE \'% ' . $sw . ' %\' OR ' . $sql_tbl['products'] . '.' . $ssf . ' LIKE \'% ' . $sw . ' %\')'
                                : 'IF(' . $sql_tbl['products_lng'] . '.productid != \'\', ' . $sql_tbl['products_lng'] . '.' . $ssf . ', ' . $sql_tbl['products'] . '.' . $ssf . ') LIKE \'% ' . $sw . ' %\'';

                        } else {

                            $tmp[] = $sql_tbl['products'] . '.' . $ssf . ' LIKE \'% ' . $sw . ' %\'';

                        }

                    }

                    if (!empty($tmp)) {

                        $condition[] = '(' . implode(' AND ', $tmp) . ')';

                    }

                    unset($tmp);

                } else {

                    if (
                        in_array($current_area, array('C', 'B'))
                        && $config['General']['skip_lng_tables_join'] != 'Y'
                    ) {

                        $searchWordsImplode = ' REGEXP \'' . implode(' | ', $search_words) . '\'';

                        if ($ssf == 'keywords') {

                            $condition[] = $sql_tbl['products_lng'] . '.' . $ssf . $searchWordsImplode;
                            $condition[] = $sql_tbl['products'] . '.' . $ssf . $searchWordsImplode;

                        } else {

                            $condition[] = 'IF(' . $sql_tbl['products_lng'] . '.productid != \'\', ' . $sql_tbl['products_lng'] . '.' . $ssf . ', ' . $sql_tbl['products'] . '.' . $ssf . ') ' . $searchWordsImplode;

                        }

                    } else {

                        $condition[] = "$sql_tbl[products].$ssf REGEXP '".implode(" | ", $search_words)."'";

                    }

                }

            } elseif (
                in_array($current_area, array('C', 'B'))
                && $config['General']['skip_lng_tables_join'] != 'Y'
            ) {

                $condition[] = 'IF(' . $sql_tbl['products_lng'] . '.productid != \'\', ' . $sql_tbl['products_lng'] . '.' . $ssf . ', ' . $sql_tbl['products'] . '.' . $ssf . ') LIKE \'% ' . $data['substring'] . ' %\'';

            } else {

                $condition[] = $sql_tbl['products'] . '.' . $ssf . ' LIKE \'% ' . $data['substring'] . ' %\'';

            }

        }
        

        if (!empty($data['by_sku'])) {

            $search_by_variants = true;

            $condition[] = (empty($active_modules['Product_Options'])
                ? $sql_tbl['products'] . '.productcode'
                : 'IFNULL(search_variants.productcode, ' . $sql_tbl['products'] . '.productcode)'
            ) . ' LIKE \'%' . $data['substring'] . '%\'';

        }

        if (
            !empty($active_modules['Extra_Fields'])
            && !empty($data['extra_fields'])
        ) {

            foreach ($data['extra_fields'] as $k => $v) {
                $condition[] = '(' . $sql_tbl['extra_field_values'] . '.value LIKE \'%' . $data['substring'] . '%\' AND ' . $sql_tbl['extra_fields'] . '.fieldid = \'' . $k . '\')';
            }

            $left_joins['extra_field_values'] = array(
                'on' => $sql_tbl['products'] . '.productid = ' . $sql_tbl['extra_field_values'] . '.productid'
            );

            $left_joins['extra_fields'] = array(
                'on' => $sql_tbl['extra_field_values'] . '.fieldid = ' . $sql_tbl['extra_fields'] . '.fieldid AND ' . $sql_tbl['extra_fields'] . '.active = \'Y\''
            );

        }

        if (!empty($condition)) {

            $where[] = '(' . implode(' OR ', $condition) . ')';

        }

        unset($condition);

    } // /if (!empty($data['substring']))

    // Search by product features

    if (!empty($active_modules['Feature_Comparison'])) {
        include $xcart_dir . '/modules/Feature_Comparison/search_define.php';
    }
    

    // Internation names & descriptions

    if (
        in_array($current_area, array('C', 'B'))
        && $config['General']['skip_lng_tables_join'] != 'Y'
    ) {
        
        $sort_by_international_product_name = true;

        $fields[] = "IF($sql_tbl[products_lng].productid != '', $sql_tbl[products_lng].product, $sql_tbl[products].product) as product";
        $fields[] = "IF($sql_tbl[products_lng].productid != '', $sql_tbl[products_lng].descr, $sql_tbl[products].descr) as descr";
        $fields[] = "IF($sql_tbl[products_lng].productid != '', $sql_tbl[products_lng].fulldescr, $sql_tbl[products].fulldescr) as fulldescr";

        if (!empty($data['by_title']) || !empty($data['by_keywords']) || !empty($data['by_shortdescr']) || !empty($data['by_fulldescr'])) {

            $left_joins['products_lng'] = array(
                'on' => "$sql_tbl[products_lng].productid = $sql_tbl[products].productid AND $sql_tbl[products_lng].code = '$shop_language'"
            );

        } else {

            $left_joins['products_lng'] = array(
                'on'          => "$sql_tbl[products_lng].productid = $sql_tbl[products].productid AND $sql_tbl[products_lng].code = '$shop_language'",
                'only_select' => true
            );
        }
    }

    if (!empty($active_modules['Manufacturers']) && !empty($data['manufacturers']) && is_array($data['manufacturers'])) {
        $where[] = "$sql_tbl[products].manufacturerid IN ('".implode("','", $data["manufacturers"])."')";
    }
    
    if (!empty($data['wholesalers']) && is_array($data['wholesalers'])) {
        $where[] = "$sql_tbl[products].wholesaler_id IN ('".implode("','", $data["wholesalers"])."')";
    }

    if ($current_area == 'C') {

        if ($membershipid == 0) {

            if (!$skip_categories_checking)
                $where[] = "$sql_tbl[category_memberships].membershipid IS NULL AND $sql_tbl[product_memberships].membershipid IS NULL";

        } else {

            if (!$skip_categories_checking)
                $where[] = "($sql_tbl[category_memberships].membershipid IS NULL OR $sql_tbl[category_memberships].membershipid = '$membershipid')";

            $where[] = "($sql_tbl[product_memberships].membershipid IS NULL OR $sql_tbl[product_memberships].membershipid = '$membershipid')";

        }
    }

    if (!$skip_categories_checking) {
        $inner_joins['products_categories'] = array(
            'on' => "$sql_tbl[products_categories].productid = $sql_tbl[products].productid",
        );
        $possible_groupbys[] = $sql_tbl['products_categories'];
        

        $inner_joins['categories'] = array(
            'on' => "$sql_tbl[products_categories].categoryid = $sql_tbl[categories].categoryid",
        );

        if ($current_area == 'C') 
            $inner_joins['categories']['on'] .= " AND $sql_tbl[categories].avail = 'Y'";

        $left_joins['category_memberships'] = array(
            'on'     => "$sql_tbl[category_memberships].categoryid = $sql_tbl[categories].categoryid",
        );
    }    

    $left_joins['product_memberships'] = array(
        'on' => "$sql_tbl[product_memberships].productid = $sql_tbl[products].productid",
    );

    if (
        !empty($data['categoryid'])
        && !$skip_categories_checking
    ) {
        // Search by category...

        $data['categoryid'] = intval($data['categoryid']);

        $category_sign = '';

        if (empty($data['category_main']) && empty($data['category_extra'])) {
            $category_sign = 'NOT';
        }

        if (!empty($data['search_in_subcategories'])) {

            // Search also in all subcategories
            x_load('category');

            $cat_pos = func_category_get_position($data['categoryid']);

            if ($cat_pos)
                $categoryids = func_query_column("SELECT categoryid FROM $sql_tbl[categories] WHERE lpos BETWEEN " . $cat_pos['lpos'] . ' AND ' .  $cat_pos['rpos']);
            else
                $categoryids = '';

            if (is_array($categoryids) && !empty($categoryids)) {
                $where[] = "$sql_tbl[products_categories].categoryid $category_sign IN (" . implode(",", $categoryids) . ")";
            }

        } else {

            //elitehour - for xmall issue starts
            /*if (!empty($active_modules['BCSE_Xmall']) && !empty($xmall_store_info)) {

                $where_provider_condition = " AND $sql_tbl[products].provider = ".$xmall_store_info['providerid'];
            }*/
            //elitehour - for xmall issue ends

            $where[] = "$category_sign $sql_tbl[products_categories].categoryid='$data[categoryid]' $where_provider_condition ";//elitehour

        }

        $condition = array();

        if (!empty($data['category_main']))
            $condition[] = "$sql_tbl[products_categories].main='Y'";

        if (!empty($data['category_extra']))
            $condition[] = "$sql_tbl[products_categories].main='N'";

        if (count($condition) == 1)
            $where[] = "(" . implode(" OR ", $condition) . ")";
    } elseif (
        $config['General']['check_main_category_only'] == 'Y'
        && $current_area == 'C'
        && !$skip_categories_checking
    ) {
        $where[] = "$sql_tbl[products_categories].main='Y'";
    } // /if (!empty($data['categoryid']))

    if (!empty($data['productcode'])) {
        $search_by_variants = true;
        $productcode_cond_string = empty($active_modules['Product_Options']) ? "$sql_tbl[products].productcode" : "IFNULL(search_variants.productcode, $sql_tbl[products].productcode)";
        $where[] = "$productcode_cond_string LIKE '%".$data["productcode"]."%'";
    }

    // EliteHour - to lists products as per sale condition - starts
    if(empty($product_type) === false) {

        define('SALE_IS_BUY_NOW', 1);
        define('SALE_IS_BARGAIN', 2);

        if($product_type == SALE_IS_BUY_NOW) {

            $where[] = " $sql_tbl[products].saleid = 0";
        }
        else if($product_type == SALE_IS_BARGAIN) {

            $where[] = " $sql_tbl[products].saleid = 1";
        }
    }
    // EliteHour - to lists products as per sale condition - ends

    if (!empty($data['productid'])) {
        $where[] = "$sql_tbl[products].productid ".(is_array($data['productid']) ? " IN ('".implode("','", $data["productid"])."')": "= '".$data["productid"]."'");
    }

    if (!empty($data['provider'])) {
        $where[] = is_array($data['provider'])
            ? "$sql_tbl[products].provider IN ('".implode("','", $data['provider'])."')"
            : "$sql_tbl[products].provider = '".$data["provider"]."'";
    }

    if (!empty($data['price_min'])) {
        $where[] = "$sql_tbl[pricing].price >= '".$data["price_min"]."'";
        $search_by_price = true;
    }

    if (strlen(@$data["price_max"]) > 0) {
        $where[] = "$sql_tbl[pricing].price <= '".$data["price_max"]."'";
        $search_by_price = true;
    }

    // If price limitation is enabled, don't show configurable products (configurable product has zero price always)
    if ($search_by_price) {
        $where[] = "$sql_tbl[products].product_type != 'C'";
    }

    $avail_cond_string = empty($active_modules['Product_Options']) ?
            "$sql_tbl[products].avail" : "IFNULL(search_variants.avail, $sql_tbl[products].avail)";

    if (!empty($data['avail_min'])) {
        $search_by_variants = true;
        $where[] = "$avail_cond_string >= '" . $data["avail_min"] . "'";
    }

    if (strlen(@$data["avail_max"]) > 0) {
        $search_by_variants = true;
        $where[] = "$avail_cond_string <= '" . $data["avail_max"] . "'";
    }

    $weight_cond_string = empty($active_modules['Product_Options'])
        ? "$sql_tbl[products].weight"
        : "IFNULL(search_variants.weight, $sql_tbl[products].weight)";

    if (!empty($data['weight_min'])) {
        $search_by_variants = true;
        $where[] = "$weight_cond_string >= '" . $data["weight_min"] . "'";
    }

    if (strlen(@$data["weight_max"]) > 0) {
        $search_by_variants = true;
        $where[] = "$weight_cond_string <= '" . $data["weight_max"] . "'";
    }

    if (!empty($data['forsale']) && $current_area == 'C') {
        $inner_joins['pricing']['on'] .= " AND $sql_tbl[products].forsale = '" . $data["forsale"] . "'";
        
        if (empty($active_modules['Product_Configurator'])) {
            $inner_joins['pricing']['on'] .= ' AND ' . $sql_tbl['products'] . '.product_type <> \'C\'';
        }
    } elseif ($current_area == 'C') {
        if (defined('SO_CUSTOMER_OFFERS')) {
            // Display all products (including hidden)
            $inner_joins['pricing']['on'] .= " AND $sql_tbl[products].forsale <> 'N'";
        } 
        
        if (empty($active_modules['Product_Configurator'])) {
            $inner_joins['pricing']['on'] .= ' AND ' . $sql_tbl['products'] . '.product_type <> \'C\'';
            $inner_joins['pricing']['on'] .= ' AND ' . $sql_tbl['products'] . '.forsale <> \'B\'';
        }
    }
    
    if (!empty($data['flag_free_ship']))
        $where[] = "$sql_tbl[products].free_shipping = '" . $data["flag_free_ship"] . "'";

    if (!empty($data['flag_ship_freight'])) {
        $where[] = $data['flag_ship_freight'] == 'Y'
            ? "$sql_tbl[products].shipping_freight > 0"
            : "$sql_tbl[products].shipping_freight = 0";
    }

    if (!empty($data['flag_global_disc'])) {
        $where[] = $data['flag_global_disc'] == 'Y'
            ? "$sql_tbl[products].discount_avail = 'Y'"
            : "($sql_tbl[products].discount_avail = 'N' OR $sql_tbl[products].discount_avail = '')";
    }

    if (!empty($data['flag_free_tax']))
        $where[] = "$sql_tbl[products].free_tax = '" . $data["flag_free_tax"]."'";

    if (!empty($data['flag_min_amount'])) {
        if ($data['flag_min_amount'] == 'Y')
            $where[] = "$sql_tbl[products].min_amount != '1'";
        else
            $where[] = "$sql_tbl[products].min_amount = '1'";
    }

    if (!empty($data['flag_low_avail_limit'])) {
        if ($data['flag_low_avail_limit'] == 'Y')
            $where[] = "$sql_tbl[products].low_avail_limit != '10'";
        else
            $where[] = "$sql_tbl[products].low_avail_limit = '10'";
    }

    if (!empty($data['flag_list_price'])) {
        if ($data['flag_list_price'] == 'Y')
            $where[] = "$sql_tbl[products].list_price != '0'";
        else
            $where[] = "$sql_tbl[products].list_price = '0'";
    }

    if(!empty($active_modules['Product_Options'])) {
        if ($search_by_variants) {
            $left_joins['search_variants'] = array(
                'tblname' => 'variants',
                'on'      => "search_variants.productid = $sql_tbl[products].productid",
            );
        }

        $left_joins['variants'] = array(
            'on'     => "$sql_tbl[variants].productid = $sql_tbl[products].productid AND $sql_tbl[quick_prices].variantid = $sql_tbl[variants].variantid"
        );

        foreach ($variant_properties as $property) {
            $fields[] = "IFNULL($sql_tbl[variants].$property, $sql_tbl[products].$property) as " . $property;
        }
    }
    
    // Elite hour - Customer type Finding
    $fields[] = "IF($sql_tbl[customers].trusted_provider = 'Y'
                        || $sql_tbl[customers].usertype !='p', 1, 0) as is_valid_provider";
    //Elitehour - Product customer name
    $fields[] = "$sql_tbl[customers].firstname, $sql_tbl[customers].lastname, $sql_tbl[customers].username";
   
    //Elitehour - Product customer
    $fields[] = " $sql_tbl[images_PL].image_path as pl_image_path, $sql_tbl[images_PL].image_x
                    as pl_image_x, $sql_tbl[images_PL].image_y as pl_image_y";

    //Elitehour - store provider
    $fields[] = "$sql_tbl[xmall_stores].provider AS store_provider, $sql_tbl[xmall_stores].directory AS store_directory";

    $left_joins['customers'] = array(
        'on'     => "$sql_tbl[customers].id = $sql_tbl[products].provider"
    );

    //Elitehour - store provider
    $left_joins['xmall_stores'] = array(
        'on'     => "$sql_tbl[xmall_stores].provider = $sql_tbl[customers].username"
    );
    
     $left_joins['images_PL'] = array(
        'on'     => "$sql_tbl[images_PL].id = $sql_tbl[products].productid"
    );
     
    //Task : Filter by - starts
    $filter_by_query        = "";
    $filter_by_extra_fields = false;
    $filter_by_variants     = false;

    if(isset($XCART_SESSION_VARS['filter_by_from_price']) && empty($XCART_SESSION_VARS['filter_by_from_price']) === false) {

        $filter_by_query .= " AND $sql_tbl[pricing].price >= '".$XCART_SESSION_VARS['filter_by_from_price']."'";
    }

    if(isset($XCART_SESSION_VARS['filter_by_to_price']) && empty($XCART_SESSION_VARS['filter_by_to_price']) === false) {

        $filter_by_query .= " AND $sql_tbl[pricing].price <= '".$XCART_SESSION_VARS['filter_by_to_price']."'";
    }
    
    if(isset($XCART_SESSION_VARS['filter_by_brand']) && empty($XCART_SESSION_VARS['filter_by_brand']) === false) {

        $filter_by_query .= " AND $sql_tbl[products].manufacturerid IN (".$XCART_SESSION_VARS['filter_by_brand'].")";
    }
    
    if(isset($XCART_SESSION_VARS['filter_by_provider']) && empty($XCART_SESSION_VARS['filter_by_provider']) === false) {

        $filter_by_query .= " AND $sql_tbl[products].provider IN (".$XCART_SESSION_VARS['filter_by_provider'].")";
    }

        // filter by extra fields - starts
        if(isset($XCART_SESSION_VARS['filter_by_gender']) && empty($XCART_SESSION_VARS['filter_by_gender']) === false) {

            $filter_by_extra_fields = true;
            $gender = explode(',', $XCART_SESSION_VARS['filter_by_gender']);
            $flds[] = " AND ($sql_tbl[extra_field_values].value LIKE '%".implode("%' OR $sql_tbl[extra_field_values].value LIKE '%", $gender)."%') ";
        }
        
        if(isset($XCART_SESSION_VARS['filter_by_type']) && empty($XCART_SESSION_VARS['filter_by_type']) === false) {

            $filter_by_extra_fields = true;
            $type   = explode(',', $XCART_SESSION_VARS['filter_by_type']);
            $flds[] = " AND ($sql_tbl[extra_field_values].value LIKE '%".implode("%' OR $sql_tbl[extra_field_values].value LIKE '%", $type)."%') ";
        }

        if(isset($XCART_SESSION_VARS['filter_by_occasion']) && empty($XCART_SESSION_VARS['filter_by_occasion']) === false) {

            $filter_by_extra_fields = true;
            $occasion = explode(',', $XCART_SESSION_VARS['filter_by_occasion']);
            $flds[]   = " AND ($sql_tbl[extra_field_values].value LIKE '%".implode("%' OR $sql_tbl[extra_field_values].value LIKE '%", $occasion)."%') ";
        }

        if(isset($XCART_SESSION_VARS['filter_by_material']) && empty($XCART_SESSION_VARS['filter_by_material']) === false) {

            $filter_by_extra_fields = true;
            $material = explode(',', $XCART_SESSION_VARS['filter_by_material']);
            $flds[]   = " AND ($sql_tbl[extra_field_values].value LIKE '%".implode("%' OR $sql_tbl[extra_field_values].value LIKE '%", $material)."%') ";
        }

        if(isset($XCART_SESSION_VARS['filter_by_made_in']) && empty($XCART_SESSION_VARS['filter_by_made_in']) === false) {

            $filter_by_extra_fields = true;
            $made_in = explode(',', $XCART_SESSION_VARS['filter_by_made_in']);
            $flds[]  = " AND ($sql_tbl[extra_field_values].value LIKE '%".implode("%' OR $sql_tbl[extra_field_values].value LIKE '%", $made_in)."%') ";
        }

        if($filter_by_extra_fields == true) {

            $fieldz = implode(' ', $flds);

            $inner_joins['extra_field_values'] = array(
                'on' => $sql_tbl['products'].'.productid = '.$sql_tbl['extra_field_values'].'.productid'
            );

            $inner_joins['extra_fields'] = array(
                'on' => $sql_tbl['extra_field_values'].'.fieldid = '.$sql_tbl['extra_fields'].'.fieldid AND '.$sql_tbl['extra_fields'].'.active = \'Y\' '.$fieldz
            );
        }
        // filter by extra fields - ends

        // filter by variants - starts
        if(isset($XCART_SESSION_VARS['filter_by_color']) && empty($XCART_SESSION_VARS['filter_by_color']) === false) {

            $filter_by_variants = true;
            $color   = explode(',', $XCART_SESSION_VARS['filter_by_color']);
            $vants[] = " AND ($sql_tbl[class_options].option_name LIKE '%".implode("%' OR $sql_tbl[class_options].option_name LIKE '%", $color)."%') ";
        }
        
        if(isset($XCART_SESSION_VARS['filter_by_size']) && empty($XCART_SESSION_VARS['filter_by_size']) === false) {

            $filter_by_variants = true;
            $size    = explode(',', $XCART_SESSION_VARS['filter_by_size']);
            $vants[] = " AND ($sql_tbl[class_options].option_name LIKE '%".implode("%' OR $sql_tbl[class_options].option_name LIKE '%", $size)."%') ";
        }

        if($filter_by_variants == true) {

            $variantz = implode(' ', $vants);

            $inner_joins['classes'] = array(
                'on' => $sql_tbl['products'].'.productid = '.$sql_tbl['classes'].'.productid'
            );

            $inner_joins['class_options'] = array(
                'on' => $sql_tbl['classes'].'.classid = '.$sql_tbl['class_options'].'.classid AND '.$sql_tbl['classes'].'.avail = \'Y\' '.$variantz
            );
        }
        // filter by variants - starts

    //Task : Filter by - ends

    if (!empty($data['sort_field'])) {
        // Sort the search results...

        $direction = ($data['sort_direction'] ? 'DESC' : 'ASC');
            
        if (
            $config['Appearance']['display_productcode_in_list'] != 'Y'
            && (
                $current_area == 'C'
                || $current_area == 'B'
            )
            && $data['sort_field'] == 'productcode'
        ) {
            $data['sort_field'] = 'orderby';
        }

        if (
            $skip_categories_checking
            && $data['sort_field'] == 'orderby'
        ) {
            $data['sort_field'] = 'title';
        }    

        switch ($data['sort_field']) {
            case 'productcode':
                $sort_string = "$sql_tbl[products].productcode $direction";
                break;

            case 'title':
                if (!empty($sort_by_international_product_name))
                    $sort_string = "product $direction";
                else    
                    $sort_string = "$sql_tbl[products].product $direction";
                break;

            case 'orderby':
               // $sort_string = "$sql_tbl[products_categories].orderby $direction";
                $sort_string = "$sql_tbl[products].productid DESC";
                break;
            
            case 'recency':
               // $sort_string = "$sql_tbl[products_categories].orderby $direction";
                 $sort_string = "$sql_tbl[products].productid $direction";
                break;

            case 'quantity':
                $sort_string = empty($active_modules['Product_Options'])
                    ? "$sql_tbl[products].avail $direction"
                    : "IFNULL($sql_tbl[variants].avail, $sql_tbl[products].avail) $direction";
                break;

            case 'price':
                if (
                    !empty($active_modules['Special_Offers'])
                    && isset($search_data['products']['show_special_prices'])
                    && $search_data['products']['show_special_prices']
                ) {
                    $sort_string = "x_special_price $direction, price $direction";

                } else {

                    $sort_string = "price $direction";
                }

                break;
                
            default:
                 $sort_string = "$sql_tbl[products].product";
        }

    } else {

         $sort_string = "$sql_tbl[products].productid";

    }

    if(!empty($data['sort_condition'])) {
        $sort_string = $data['sort_condition'];
    }

    if (
        (
            $current_area == 'C'
            || $current_area == 'B'
        )
        && $config['General']['show_outofstock_products'] != 'Y'
    ) {
        $where[] = !empty($active_modules['Product_Options'])
            ? "(IFNULL($sql_tbl[variants].avail, $sql_tbl[products].avail) > 0 OR $sql_tbl[products].product_type NOT IN ('','N'))"
            : "($sql_tbl[products].avail > 0 OR $sql_tbl[products].product_type NOT IN ('','N'))";
    }

    $groupbys[] = "$sql_tbl[products].productid";
    $possible_groupbys[] = "$sql_tbl[products]";

    if (
        $config['General']['use_simple_product_sort'] == 'Y'
        && $current_area == 'C'
    ) {
        $orderbys = array("$sql_tbl[products].productid ASC");
    } else {
        $orderbys[] = $sort_string;
        if ($data['sort_field'] != 'title') {
            if (!empty($sort_by_international_product_name))
                $orderbys[] = "product ASC";
            else    
                $orderbys[] = "$sql_tbl[products].product ASC";
        }

        if ($data['sort_field'] != 'productcode')
            $orderbys[] = "$sql_tbl[products].productcode ASC";

        $orderbys[] = "$sql_tbl[products].productid ASC";
        $orderbys[] = "$sql_tbl[images_PL].imageid ASC";
    }

    // Generate search query

    foreach ($inner_joins as $j) {
        if (!empty($j['fields']) && is_array($j['fields']))
            $fields = func_array_merge($fields, $j['fields']);
    }

    foreach ($left_joins as $j) {
        if (!empty($j['fields']) && is_array($j['fields']))
            $fields = func_array_merge($fields, $j['fields']);
    }
    

    $fields_count[] = "COUNT(*)";
    $search_query = "SELECT ".implode(", ", $fields)." FROM ";
    $search_query_count = "SELECT ".implode(", ", $fields_count)." FROM ";

    $product_search_query = "SELECT $sql_tbl[products].productid FROM ";

    if (!empty($from_tbls)) {
        foreach ($from_tbls as $k => $v) {
            $from_tbls[$k] = $sql_tbl[$v];
        }
        $search_query .= implode(", ", $from_tbls).", ";
        $search_query_count .= implode(", ", $from_tbls).", ";

        $product_search_query .= implode(", ", $from_tbls).", ";
    }

    $search_query .= $sql_tbl['products'];
    $search_query_count .= $sql_tbl['products'];

    $product_search_query .= $sql_tbl['products'];

    $joins = array();
    $joins_count = array();

    foreach ($inner_joins as $ijname => $ij) {
            $ij['is_inner'] = true;
        if (!isset($ij['only_select']) || !$ij['only_select'])
            $joins_count[$ijname] = $ij;
        $joins[$ijname] = $ij;
    }

    foreach ($left_joins as $ljname => $lj) {
        if (!isset($lj['only_select']) || !$lj['only_select'])
            $joins_count[$ljname] = $lj;
        $joins[$ljname] = $lj;
    }

    $search_query .= func_generate_joins($joins);
    $search_query_count .= func_generate_joins($joins_count);

    $product_search_query .= func_generate_joins($joins);

    if (!empty($where)) {
        $search_query .= " WHERE ".implode(" AND ", $where);
        $search_query_count .= " WHERE ".implode(" AND ", $where);

        $product_search_query .= " WHERE ".implode(" AND ", $where);
    }

    //elitehour - Vendor shop - starts
    if(empty($active_modules['BCSE_Xmall']) === false && empty($shop) === false) {

        $shop_query  = "";

        $shop_query .= " AND $sql_tbl[products].provider = '".$shop['providerid']."'";

        if(isset($shop['search_query']) && empty($shop['search_query']) === false) {

            $shop_query .= " AND $sql_tbl[products].product LIKE '%".$shop['search_query']."%'";
        }
        /*
        if(isset($shop['cat']) && empty($shop['cat']) === false) {

            $shop_query .= " AND $sql_tbl[products_categories].categoryid = '".$shop['cat']."'";
        }
        */
        $search_query .= $shop_query;
        $search_query_count .= $shop_query;
    }
    //elitehour - Vendor shop - ends

    //Task : Filter by - starts
    if(empty($filter_by_query) === false) {

        $search_query       .= $filter_by_query;
        $search_query_count .= $filter_by_query;

        $product_search_query .= $filter_by_query;
    }
    //Task : Filter by - ends

    $product_query = $product_search_query;

    if (
        count($groupbys) == 1
        && $groupbys[0] == "$sql_tbl[products].productid"
    ) {
        $groupbys = func_get_low_cost_sql_groupby($search_query, $possible_groupbys, $having, $orderbys, $groupbys, 'productid');

        if (
            $config['General']['use_simple_product_sort'] == 'Y'
            && $current_area == 'C'
        ) {
            // Use the same order by as group by in simple product sort mode
            $orderbys = array($groupbys[0] . " ASC");
        }
    }        
    if (!empty($groupbys)) {
        $search_query .= " GROUP BY ".implode(", ", $groupbys);
        $search_query_count .= " GROUP BY ".implode(", ", $groupbys);

        $product_search_query .= " GROUP BY ".implode(", ", $groupbys);
    }

    if (!empty($having)) {
        $search_query .= " HAVING ".implode(" AND ", $having);
        $search_query_count .= " HAVING ".implode(" AND ", $having);

        $product_search_query .= " HAVING ".implode(" AND ", $having);
    }

    if (!empty($orderbys)) {
        $search_query .= " ORDER BY ".implode(", ", $orderbys);
    }

    // Calculate the number of rows in the search results

    db_query("SET OPTION SQL_BIG_SELECTS=1");

    $total_items = 0;

    //echo $product_search_query;

    //Task : Provide option to navigate between next and previous products on the single product upload interface
    $product_search_query = base64_encode($product_search_query);
    x_session_register('product_search_query');

    if (USE_SQL_DATA_CACHE) {

        $_res_tmp = func_query($search_query_count, true);

        $total_items = is_array($_res_tmp) ? count($_res_tmp) : 0;

        unset($_res_tmp);

    } else {

        if ($_res = db_query($search_query_count)) {

            $total_items = db_num_rows($_res);

            db_free_result($_res);

        }

    }
    
    

    $is_rating_data = false;
    $first_page = isset($first_page) ? $first_page : 0;

    if ('C' === $current_area) {

        x_session_register('store_objects_per_page', 0);

        if (
            !isset($objects_per_page)
            && (0 < intval($store_objects_per_page))
        ) {
            $objects_per_page = $store_objects_per_page;
        }

        if (isset($_GET['objects_per_page'])) {

            $objects_per_page = $store_objects_per_page = $_GET['objects_per_page'];

        }

        $perPageValues = array();

        for ($i = 5; 50 >= $i; $i = $i + 5) {
            $perPageValues[] = $i;
        }

        $smarty->assign('per_page',         'Y');
        $smarty->assign('per_page_values',  $perPageValues);

    }

    $objects_per_page = isset($objects_per_page) ? intval($objects_per_page) : 0;

    if ($total_items > 0) {

        if (
            !isset($do_not_use_navigation) 
            || !$do_not_use_navigation
        ) {

            $page = isset($search_data['products']['page'])
                ? $search_data['products']['page']
                : 1;

            // Prepare the page navigation

            if ($objects_per_page <= 0) {

                $objects_per_page = ($current_area == 'C' || $current_area == 'B')
                    ? $config['Appearance']['products_per_page']
                    : $config['Appearance']['products_per_page_admin'];

            }

            include $xcart_dir . '/include/navigation.php';

        }

        $smarty->assign('objects_per_page', $objects_per_page);

        // Perform the SQL query and getting the search results

        if (!empty($data['is_modify'])) {

            // Get the products and go to modify them

            $res = db_query($search_query);

            if ($res) {

                $geid = false;
                $productid = false;

                x_load('product');

                while ($pid = db_fetch_row($res)) {

                    if (empty($productid))
                        $productid = $pid[0];

                    $geid = func_ge_add($pid[0], $geid);

                }

                func_header_location("product_modify.php?productid=$productid&geid=".$geid);
            }

        } elseif (
            (
                !empty($data['is_export'])
                && $data['is_export'] == 'Y'
            ) || (
                !empty($export)
                && $export == 'export_found')
        ) {

            x_load('export');

            // Save the SQL query and go to export them
            func_export_range_save('PRODUCTS', $search_query);

            $top_message['content'] = func_get_langvar_by_name("lbl_export_products_add");
            $top_message['type'] = 'I';

            func_header_location("import.php?mode=export");

        } else {

            if (!isset($do_not_use_navigation) || !$do_not_use_navigation)
                $search_query .= " LIMIT $first_page, $objects_per_page";

            //echo $search_query; //die;

            if($current_area == 'P' || $current_area == 'A') {
                $products = func_query($search_query);
            } else {
                $products = func_query($search_query, USE_SQL_DATA_CACHE);
            }
        }

        // Clear service arrays
        unset($fields, $fields_count, $from_tbls, $inner_joins, $left_joins, $where, $groupbys, $having, $orderbys, $possible_groupbys);

        if (
            !empty($products) 
            && (
                $current_area == 'C' 
                || $current_area == 'B'
            )
        ) {

            x_session_register('cart');

            // Get tax rates cache
            $ids = array();

            foreach ($products as $v) {
                if ($v['is_taxes'] == 'Y')
                    $ids[] = $v;
            }

            $_taxes = array();

            if (!empty($ids)) {

                x_load('taxes');

                $_taxes = func_get_product_tax_rates($products, $logged_userid);

            }

            unset($ids);
            

            if (!empty($active_modules['Extra_Fields'])) {

                // Get Extra fields cache
                $ids = array();

                foreach ($products as $k => $v) {
                    $ids[] = intval($v['productid']);
                }

                $products_ef = func_query_hash("
                SELECT $sql_tbl[extra_fields].*, $sql_tbl[extra_field_values].*," . 

                    (in_array($current_area, array('C', 'B')) && $config['General']['skip_lng_tables_join'] == 'Y'
                        ? ""
                        : "IF($sql_tbl[extra_fields_lng].field != '', $sql_tbl[extra_fields_lng].field, $sql_tbl[extra_fields].field) as field," )

                . "$sql_tbl[extra_field_values].value as field_value 
                  FROM $sql_tbl[extra_field_values] INNER JOIN $sql_tbl[extra_fields] ON
                    $sql_tbl[extra_fields].fieldid =  $sql_tbl[extra_field_values].fieldid
                    AND $sql_tbl[extra_field_values].productid IN (".implode(",", $ids).")
                    AND $sql_tbl[extra_fields].active =  'Y' " .

                      (in_array($current_area, array('C', 'B')) && $config['General']['skip_lng_tables_join'] == 'Y'
                        ?   ""
                        :   "LEFT JOIN $sql_tbl[extra_fields_lng]
                                ON $sql_tbl[extra_fields].fieldid
                                =  $sql_tbl[extra_fields_lng].fieldid
                               AND $sql_tbl[extra_fields_lng].code
                               =  '$shop_language'")

                 . "
                 ORDER BY $sql_tbl[extra_fields].orderby
                ", "productid");

                unset($ids);
            }
            
            

            if (!empty($active_modules['Product_Options'])) {

                // Get Product options markups cache
                $ids = array();

                foreach ($products as $v) {

                    if (!empty($v['is_product_options']))
                        $ids[$v['productid']] = doubleval($v['price']);

                }
                
                $options_markups = array();

                if (!empty($ids))
                    $options_markups = func_get_default_options_markup_list($ids);

                unset($ids);
            }

            $is_customer_voting = !empty($active_modules['Customer_Reviews']) && $config['Customer_Reviews']['customer_voting'] == 'Y';

            if ($is_customer_voting) {

                $smarty->assign('stars', func_get_vote_stars());

            }

            // Get thumbnails dimensions
            if (!function_exists('_return_productid')) {
                function _return_productid($value) 
                {
                    return $value['productid'];
                }
            }
   
            $thumb_dims = func_query_hash("SELECT id, image_x, image_y FROM $sql_tbl[images_T] WHERE id IN ('" . implode("','", array_map('_return_productid', $products)) . "')", "id", false);
            $max_images_width = 0;
            x_load('product');
            
            

            foreach ($products as $k => $v) {
                
                if (
                    !empty($active_modules['Feature_Comparison'])
                    && !isset($products_has_fclasses)
                ) {
                    $products_has_fclasses = $v['fclassid'];
                }

                $products[$k]['taxed_price'] = $v['taxed_price'] = $v['price'];

                if (
                    !empty($active_modules['Product_Options']) 
                    && !empty($v['is_product_options'])
                ) {

                    if (!empty($options_markups[$v['productid']])) {

                        // Add product options markup
                        if ($products[$k]['price'] != 0)
                            $products[$k]['price'] += $options_markups[$v['productid']];

                        $products[$k]['taxed_price'] = $products[$k]['price'];

                        $v = $products[$k];

                    }

                    if (
                        $products[$k]['taxed_price'] == 0 
                        && $v['variantid']
                    ) {
                        $products[$k]['variants_has_price'] = func_query_first_cell("SELECT COUNT($sql_tbl[pricing].variantid) FROM $sql_tbl[variants], $sql_tbl[pricing] WHERE $sql_tbl[variants].productid = '$v[productid]' AND $sql_tbl[variants].variantid = $sql_tbl[pricing].variantid AND $sql_tbl[pricing].price > 0") > 0;
                    }

                }
                 
                $in_cart = 0;

                if (
                    !empty($cart['products']) 
                    && is_array($cart['products'])
                ) {
                    // Modify product's quantity based the cart data
                    foreach ($cart['products'] as $cv) {
                        if (
                            $cv['productid'] == $v['productid']
                            && intval($v['variantid']) == intval($cv['variantid'])
                        ) {
                            $in_cart += $cv['amount'];
                        }
                    }

                    $products[$k]['in_cart'] = $in_cart;
                    $products[$k]['avail'] -= $in_cart;

                    if ($products[$k]['avail'] < 0) {
                        $products[$k]['avail'] = 0;
                    }

                }

                if (
                    !empty($active_modules['Extra_Fields']) 
                    && isset($products_ef[$v['productid']])
                ) {
                    // Get extra fields data
                    $products[$k]['extra_fields'] = $products_ef[$v['productid']];
                }

                // Elitehour
                if(empty($products[$k]['condition']) === false) {

                    $products[$k]['condition'] = 
                                  $product_condition[$products[$k]['condition']];
                }

                // Elitehour
                if(empty($products[$k]['ifused']) === false) {

                    $products[$k]['ifused'] = 
                             $product_if_used_condition[$products[$k]['ifused']];
                }

                // Get thumbnail URL
                $products[$k]['is_image_T'] = !is_null($v['image_path_T']);
              
                //Elitehour query optimization
                 $products[$k]['tmbn_url'] = func_get_image_url($v['productid'], "T", $v['image_path_T']);
                //$imagepath[]=$v['image_path_T'];
                unset($products[$k]['image_path_T']);
               
                $dims_tmp = !isset($thumb_dims[$v['productid']]) ? $config["setup_images"]["T"] : $thumb_dims[$v['productid']];

                $products[$k] = func_array_merge($products[$k], $dims_tmp);

                $_limit_width = $config['Appearance']['thumbnail_width'];
                $_limit_height = $config['Appearance']['thumbnail_height'];
                $products[$k] = func_get_product_tmbn_dims($products[$k], $_limit_width, $_limit_height);
                
                $max_images_width = max($max_images_width,  $products[$k]['tmbn_x'], 125);
               
                
                // Calculate product taxes
                if (!empty($active_modules["Special_Offers"]) && !empty($search_data["products"]["show_special_prices"])) {

                    func_offers_search_apply_special_price($products[$k], $logged_userid);

                } elseif (
                    $v['is_taxes'] == 'Y' 
                    && isset($_taxes[$v['productid']])
                ) {

                    $products[$k]['taxes'] = func_get_product_taxes($products[$k], $logged_userid, false, $_taxes[$v['productid']]);

                }
                

                // List price corrections
                if (($products[$k]['taxed_price'] != $v['price']) && ($v['list_price'] > 0))
                    $products[$k]['list_price'] = price_format($v['list_price'] * $products[$k]['taxed_price'] / $v['price']);

                if ($products[$k]['descr'] == strip_tags($products[$k]['descr']))
                    $products[$k]['descr'] = str_replace("\n", "<br />", $products[$k]['descr']);

                if ($products[$k]['fulldescr'] == strip_tags($products[$k]['fulldescr']))
                    $products[$k]['fulldescr'] = str_replace("\n", "<br />", $products[$k]['fulldescr']);

                //Elitehour - fix for optimize the query
                if (! $products[$k]['is_valid_provider']) {

                    $products[$k]['fulldescr'] = func_xss_free($products[$k]['fulldescr']);
                    $products[$k]['descr'] = func_xss_free($products[$k]['descr']);

                }
                // Appearance options for Products list page
                $products[$k]['appearance'] = func_get_appearance_data($products[$k]);

                //Megahour info start
                if($products[$k]['saleid'] == 2) {
                    $mh_productid   =   $products[$k]['productid'];
                    //check product is in mega hour time or normal time
                    $var_megahour_query     =   "SELECT
                                                        timeslot_id, start_time, end_time
                                                FROM $sql_tbl[megahour_time_slot]
                                                WHERE
                                                    product_id=$mh_productid AND
                                                    NOW()>=start_time AND
                                                    NOW()<=end_time ORDER BY start_time ASC LIMIT 0,1";
                    $arr_megahour_qry_dtl       =   db_query($var_megahour_query);
                    $mhtime_in_count            =   mysql_num_rows($arr_megahour_qry_dtl);

                    if($mhtime_in_count > 0) {
                        $products[$k]['mhpresent'] = 1;
                        
                        //get remianing time of mega hour end in seconds
                        $rel_mh_time_dtl    =   mysql_fetch_array($arr_megahour_qry_dtl);
                        $end_time           =   $rel_mh_time_dtl['end_time'];

                        $arr_current_time           =   func_query("SELECT NOW() as curtime");
                        $var_current_time           =   $arr_current_time[0]['curtime'];

                        $remain_time_in_sec         =   func_time_difference($var_current_time, $end_time);
                        $products[$k]['mhtime_sec'] =   $remain_time_in_sec;
                        $products[$k]['tsid']       =   $rel_mh_time_dtl['timeslot_id'];
                    } else {
                        $products[$k]['mhpresent']  = 0;

                        //get next time of mega hour starts
                        $var_megahour_query     =   "SELECT
                                                            start_time, end_time
                                                    FROM $sql_tbl[megahour_time_slot]
                                                    WHERE
                                                        product_id=$mh_productid AND
                                                        start_time>NOW() ORDER BY start_time ASC LIMIT 0,1";
                        $arr_megahour_qry_dtl       =   db_query($var_megahour_query);
                        $mhnext_in_count            =   mysql_num_rows($arr_megahour_qry_dtl);

                        if($mhnext_in_count>0) {
                            $rel_mh_time_dtl            =   mysql_fetch_array($arr_megahour_qry_dtl);
                            $start_time                 =   explode(' ', $rel_mh_time_dtl['start_time']);
                            $end_time                   =   explode(' ', $rel_mh_time_dtl['end_time']);

                            $arr_date                   =   explode('-',$start_time[0]);
                            $products[$k]['st_time']    =   date('D M d Y', mktime(0, 0, 0, $arr_date[1], $arr_date[2], $arr_date[0])).' '.$start_time[1].' '.$config[server_gmt_time];

                            $arr_enddate                =   explode('-',$end_time[0]);
                            $products[$k]['end_time']    =   date('D M d Y', mktime(0, 0, 0, $arr_enddate[1], $arr_enddate[2], $arr_enddate[0])).' '.$end_time[1].' '.$config[server_gmt_time];
                        }
                    }

                    //calculate discount percentage
                    $sale_price_discnt              =   round((1-($products[$k]['taxed_price']/$products[$k]['list_price']))*100);
                    $megahour_price_discnt          =   round((1-($products[$k]['lowest_possible_price']/$products[$k]['list_price']))*100);
                    $products[$k]['sale_price_dc']  =   $sale_price_discnt;
                    $products[$k]['megahour_dc']    =   $megahour_price_discnt;

                }
                //Megahour info end

                //Elitehour optimize
                $products_id[]=$products[$k]['productid'];
                
                /*if ($is_customer_voting) {
                    $products[$k]['rating_data'] = func_get_product_rating($products[$k]['productid']);

                    if ($products[$k]['rating_data'])
                      $is_rating_data = true;
                }*/
                $cat_str  = (isset($cat)) ? '&cat=' . $cat : '';
                $page_str = (isset($page)) ? '&page=' . $page : '';

                $products[$k]['page_url'] = 'product.php?productid=' . $v['productid'] . $cat_str . $page_str;

                if (isset($data['add_page_url']))
                    $products[$k]['page_url'] .= $data['add_page_url'];

            } // foreach ($products as $k => $v)
            //$imagetype = func_get_image_url_new($products_id, 'T', $imagepath);
            //for($k=0;$k<count($imagetype);$k++){
            //    $products[$k]['tmbn_url'] = $imagetype[$k];
            //}
            unset($thumb_dims);
         
            if (!empty($active_modules["Special_Offers"]) && empty($search_data["products"]["show_special_prices"])) {

                func_offers_check_products($logged_userid, $current_area, $products);

            }

        } // if current area is customer or partner one
        
        //Task : Multiple product listing - starts
        if(!empty($products) && ($current_area == 'P' || $current_area == 'A')) {

            if(!empty($active_modules['Extra_Fields'])) {

                $ids = array();

                foreach($products as $k => $v) {
                    $ids[] = intval($v['productid']);
                }

                //Extra Fields
                $products_ef = func_query_hash("
                    SELECT $sql_tbl[extra_fields].*, $sql_tbl[extra_field_values].*
                    FROM $sql_tbl[extra_field_values]
                    INNER JOIN $sql_tbl[extra_fields]
                    ON $sql_tbl[extra_fields].fieldid = $sql_tbl[extra_field_values].fieldid
                    AND $sql_tbl[extra_field_values].productid IN (".implode(",", $ids).")
                    AND $sql_tbl[extra_fields].active = 'Y'
                    AND $sql_tbl[extra_fields].type != 'variant'
                    ORDER BY $sql_tbl[extra_fields].orderby", "productid");

                //Product Packs
                $products_pc = func_query_hash("
                    SELECT $sql_tbl[product_packs].*
                    FROM $sql_tbl[product_packs]
                    INNER JOIN $sql_tbl[products]
                    ON $sql_tbl[product_packs].productid = $sql_tbl[products].productid
                    AND $sql_tbl[product_packs].productid IN (".implode(",", $ids).")
                    ORDER BY $sql_tbl[product_packs].id", "productid");

                //Product Variants
                $products_pv = func_query_hash("
                    SELECT $sql_tbl[classes].*, $sql_tbl[class_options].*
                    FROM $sql_tbl[classes]
                    INNER JOIN $sql_tbl[products]
                    ON $sql_tbl[classes].productid = $sql_tbl[products].productid
                    INNER JOIN $sql_tbl[class_options]
                    ON $sql_tbl[class_options].classid = $sql_tbl[classes].classid
                    AND $sql_tbl[classes].productid IN (".implode(",", $ids).")
                    ORDER BY $sql_tbl[classes].classid", "productid");

                unset($ids);
            }
            
            foreach($products as $k => $v) {

                //Extra Fields
                if(!empty($active_modules['Extra_Fields']) && isset($products_ef[$v['productid']])) {
                    $products[$k]['extra_fields'] = $products_ef[$v['productid']];
                }

                //Product Packs
                if(isset($products_pc[$v['productid']])) {
                    $products[$k]['packs'] = $products_pc[$v['productid']];
                }

                // Elitehour - 20-12-2012
                $pack_price = func_query_first("SELECT SUM(size_count) AS packs_price FROM $sql_tbl[product_packs] WHERE productid='".$v['productid']."'");
                if($pack_price){
                    $products[$k]['packs_price'] = $pack_price['packs_price'];
                }
                // Elitehour - 20-12-2012
                // 
                //Product Variants
                if(isset($products_pv[$v['productid']])) {
                    $products[$k]['variants'] = $products_pv[$v['productid']];
                }

                if($products[$k]['fulldescr'] == strip_tags($products[$k]['fulldescr'])) {
                    $products[$k]['fulldescr'] = str_replace("\n", "<br />", $products[$k]['fulldescr']);
                }

                //sale module
                $products[$k]['salemodule'] = $product_sale_module_option[$products[$k]['saleid']];
            }
        }
        //Task : Multiple product listing - ends
        
        //Default rating settings
        $result = array(
                    'allow_add_rate' => true,
                    'forbidd_reason' => false,
                    'total' => 0,
                    'rating' => 0,
                    'rating_level' => 0,
                    'full_stars' => 0,
                    'percent' => 0
                );

        //Elitehour add a function for optimize query
        $products_rating_data   = func_get_product_rating_new($products_id);
        for($i=0;$i<count($products_id);$i++) {

             $products[$i]['rating_data']   =  $products_rating_data[$products_id[$i]];
             if (empty($products[$i]['rating_data']) === false)
                $is_rating_data = true;
             else 
                 $products[$i]['rating_data']   =   $result;

            $smarty->assign('rating_data_exists', $is_rating_data);
        }
     
        if (isset($products_ef))
            unset($products_ef);

        if (isset($options_markups))
            unset($options_markups);

        if (!$_inner_search) {
            // Assign the Smarty variables

            if (
                !isset($do_not_use_navigation)
                || !$do_not_use_navigation
            ) {
                $smarty->assign('navigation_script', 'search.php?mode=search' . (!empty($input_args) ? '&' . $input_args : ''));
            }

            $smarty->assign_by_ref('products',         $products);

            if (isset($max_images_width))
                $smarty->assign('max_images_width', $max_images_width);

            $smarty->assign('first_item',       $first_page + 1);
            $smarty->assign('last_item',        min($first_page + $objects_per_page, $total_items));

            if (
                !empty($active_modules['Feature_Comparison']) 
                && isset($products_has_fclasses)
            ) {
                $smarty->assign('products_has_fclasses', $products_has_fclasses);
            }    

            $smarty->assign('total_items',  $total_items);
            $smarty->assign('mode',         'search');

            if ($flag_save) {
                x_session_save('search_data');
            }

        }

    } // if ($total_items > 0)

} // if ($mode == 'search')

if (!$_inner_search) {

    if (
        !empty($active_modules['Feature_Comparison'])
        && $current_area != 'C'
        && $current_area != 'B'
    ) {
        $fclasses = func_query("SELECT $sql_tbl[feature_classes].*, IFNULL($sql_tbl[feature_classes_lng].class, $sql_tbl[feature_classes].class) as class FROM $sql_tbl[feature_classes] LEFT JOIN $sql_tbl[feature_classes_lng] ON $sql_tbl[feature_classes].fclassid = $sql_tbl[feature_classes_lng].fclassid AND $sql_tbl[feature_classes_lng].code = '$shop_language' WHERE $sql_tbl[feature_classes].avail = 'Y' ORDER BY $sql_tbl[feature_classes].orderby");

        if(!empty($fclasses)) {
            $smarty->assign('fclasses', $fclasses);
        }

    }

    if (!empty($active_modules['Manufacturers'])) {

        if (
            $mode == 'search' 
            && $total_items > 0
        ) {

            if (!isset($search_data['products']['manufacturers'])) {
                $search_data['products']['manufacturers'] = array();
            }

        } else {

            $selected_manufacturers = false;

            if ($current_area == 'C') {

                include $xcart_dir.'/modules/Manufacturers/customer_manufacturers.php';

            } else {

                $manufacturers = func_query("SELECT manufacturerid, manufacturer FROM $sql_tbl[manufacturers] WHERE avail = 'Y' ORDER BY orderby, manufacturer");
            }

            if (isset($search_data['products']['manufacturers'])) {
                $selected_manufacturers = $search_data['products']['manufacturers'];
            }

            if (!empty($manufacturers)) {
                $search_data['products']['manufacturerids'] = func_manufacturer_selected_for_search($manufacturers, $selected_manufacturers);
                $smarty->assign('manufacturers', $manufacturers);
            }

        }
    }
    
    //Task : Search by Wholesaler - starts
    if($mode == 'search' && $total_items > 0) {

        if(!isset($search_data['products']['wholesalers'])) {
            $search_data['products']['wholesalers'] = array();
        }
    }
    else {

        $selected_wholesalers = false;

        if($current_area != 'C') {
            $wholesalers = func_query("SELECT wholesaler_id, wholesaler_name FROM $sql_tbl[wholesalers] WHERE 1 ORDER BY wholesaler_name");
        }

        if(isset($search_data['products']['wholesalers'])) {
            $selected_wholesalers = $search_data['products']['wholesalers'];
        }

        if(!empty($wholesalers)) {
            //$search_data['products']['wholesalersids'] = func_manufacturer_selected_for_search($manufacturers, $selected_manufacturers);
            $smarty->assign('wholesalers', $wholesalers);
        }
    }
    //Task : Search by Wholesaler - ends

    $smarty->assign('search_prefilled', func_stripslashes($search_data['products']));

    if (
        !empty($active_modules['Extra_Fields'])
        && (
            empty($products)
            || $mode !== 'search'
        )
    ) {

        $ef_provider_condition = (!$single_mode && $current_area == 'P' ? "AND $sql_tbl[extra_fields].provider = '$logged_userid'" : '');

        $extra_fields = func_query("SELECT $sql_tbl[extra_fields].*, IF($sql_tbl[extra_fields_lng].field != '', $sql_tbl[extra_fields_lng].field, $sql_tbl[extra_fields].field) as field FROM $sql_tbl[extra_fields] LEFT JOIN $sql_tbl[extra_fields_lng] ON $sql_tbl[extra_fields].fieldid = $sql_tbl[extra_fields_lng].fieldid AND $sql_tbl[extra_fields_lng].code = '$shop_language' WHERE active = 'Y' $ef_provider_condition ORDER BY field");

        if ($extra_fields) {

            $tmp = explode("\n", $config['Search_products']['search_products_extra_fields']);

            foreach ($extra_fields as $k => $v) {

                if (
                    !empty($tmp)
                    && is_array($tmp)
                    && $current_area == 'C'
                    && !in_array($v['fieldid'], $tmp)
                ) {
                    unset($extra_fields[$k]);
                    continue;
                }

                if (!empty($search_data['products']['extra_fields'][$v['fieldid']]))
                    $extra_fields[$k]['selected'] = 'Y';
            }

            if ($extra_fields) {
                $smarty->assign('extra_fields', $extra_fields);
            }

        }

    }

    foreach ($sort_fields as $k => $v) {

        $sort_fields[$k] = func_get_langvar_by_name($v);

    }

    $smarty->assign('sort_fields', $sort_fields);

    $smarty->assign('main', 'search');

    if (
        $mode != 'search'
        || $total_items == 0
    ) {
        x_load('category');
        $smarty->assign('search_categories', func_data_cache_get("get_categories_tree", array(0, true, $shop_language, $user_account['membershipid'])));
    }
}

?>