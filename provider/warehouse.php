<?php

/**
 * Warehouse interface
 *
 * @category   X-Cart
 * @subpackage Provider interface
 * @author     Manoj <manoj@elitehour.com>
 * @modified   January-31-2013
 */

require_once './auth.php';
require_once $xcart_dir.'/include/security.php';

ini_set("display_errors", 1);

$provider_id = $GLOBALS['logged_userid'];

final class Warehouse {

    private static $limit  = 20;
    private static $status = "'P', 'C', 'I'";

    public function __construct()
    {
        global $sql_tbl, $smarty, $mode, $posted_data, $provider_id, $page, $filter_by;

        $this->sql_tbl      =   $sql_tbl;
        $this->smarty       =   $smarty;
        $this->mode         =   (string) $mode;
        $this->posted_data  =   (array)  $posted_data;
        $this->provider_id  =   (int)    $provider_id;
        $this->page         =   (int)    $page;
        $this->filter_by    =   (array)  $filter_by;

        self::func_wholesalers();

        self::func_search();

        $function_call      =   array(
                                    'update' => 'func_update'
                                );

        if(array_key_exists($this->mode, $function_call))
            self::$function_call[$this->mode]();

        $location[]         =   array('Bvira Warehouse', 'warehouse.php');
        $this->smarty->assign('location', $location);
        $this->smarty->assign('main', 'bvira_warehouse');

        func_display('provider/home.tpl', $this->smarty);
    }

    protected function func_wholesalers()
    {
        $sql    =   "SELECT
                        wholesalers.wholesaler_id,
                        wholesalers.wholesaler_name
                     FROM
                        ".$this->sql_tbl['wholesalers']." AS wholesalers
                     ORDER BY
                        wholesalers.wholesaler_name";
        $result =   func_query($sql, USE_SQL_DATA_CACHE);

        if(empty($result))
            return false;

        if(!empty($result) && !empty($this->filter_by['wholesaler_id'])) {

            foreach($result as $key => $value) {

                if(in_array($this->filter_by['wholesaler_id'], $value))
                    $result[$key]['selectbox'] = 'selected';
            }
        }

        $this->smarty->assign('wholesalers', $result);
    }

    protected function func_list($limit = '')
    {
        if(empty($this->provider_id))
            return false;

        $condition  =   '';

        if(isset($this->filter_by)) {

            if(!empty($this->filter_by['productcode']))
                $condition .= " AND products.productcode = '".$this->filter_by['productcode']."'";

            if(!empty($this->filter_by['vendorsku']))
                $condition .= " AND products.vendorsku = '".$this->filter_by['vendorsku']."'";

            if(!empty($this->filter_by['wholesaler_id']))
                $condition .= " AND products.wholesaler_id = ".$this->filter_by['wholesaler_id'];
        }

        $sql        =   "SELECT
                            products.productid,
                            products.productcode,
                            products.product,
                            images_PL.image_path,
                            images_PL.image_x,
                            images_PL.image_y
                        FROM
                            ".$this->sql_tbl['products']." AS products
                        LEFT JOIN
                            ".$this->sql_tbl['order_details']." AS order_details
                        ON
                            (products.productid = order_details.productid)
                        LEFT JOIN
                            ".$this->sql_tbl['orders']." AS orders
                        ON
                            (order_details.orderid = orders.orderid
                        AND
                            orders.status IN (".self::$status."))
                        LEFT JOIN
                            ".$this->sql_tbl['images_PL']." AS images_PL
                        ON
                            (products.productid = images_PL.id
                        AND
                            images_PL.pos = 0)
                        WHERE
                            products.provider = ".$this->provider_id."
                        ".$condition."
                        GROUP BY
                            products.productid 
                        ORDER BY
                            products.productid ".$limit;
        echo $sql;
        $products   =   func_query($sql);

        if(empty($products))
            return false;

        foreach($products as $key => $value) {

            $variants   =   func_get_product_variants($value['productid']);

            if(isset($variants) && empty($variants) === false)
                $products[$key]['variants'] = $variants;

            $sql2       =   "SELECT
                                warehouse.variantid,
                                warehouse.quantity
                            FROM
                                ".$this->sql_tbl['warehouse']." AS warehouse
                            INNER JOIN
                                ".$this->sql_tbl['products']." AS products
                            ON
                                warehouse.productid = products.productid
                            WHERE
                                warehouse.productid = ".$value['productid'];
            $warehouse  = func_query($sql2);

            if(isset($warehouse) && empty($warehouse) === false)
                $products[$key]['warehouse'] = $warehouse;
        }

        return $products;
    }

    protected function func_search()
    {
        $page = "";

        if(isset($this->page) && empty($this->page) === false) {

            $page  = $this->page;
            $start = ($page - 1) * self::$limit;
        }
        else {

            $start = 0;
        }

        if(isset($this->mode) && ($this->mode == 'search'))
            $page_show = "mode=search";

        $varname     = "page";
        $targetpage  = "warehouse.php?".$page_show;

        $lists       = self::func_list();
        $total_pages = count($lists);

        $result      = self::func_list("LIMIT ".$start.", ".self::$limit);

        $pagination  = self::func_pagination($total_pages, self::$limit, $targetpage, $page, $start, $varname);

        $this->smarty->assign('products', $result);
        $this->smarty->assign('pagination', $pagination);
        $this->smarty->assign('page', $page);
        $this->smarty->assign('filter_by', $this->filter_by);
    }

    private function func_pagination($total_pages, $limit, $targetpage, $page, $start, $var_name, $tag_name = '')
    {
        $adjacents  = 1;

        if($page == 0)
            $page = 1;

        $prev       = $page - 1;
        $next       = $page + 1;
        $lastpage   = ceil($total_pages/$limit);
        $lpm1       = $lastpage - 1;

        $pagination = "";

        $start1     = ($start + 1);
        $remaining  = $start1 + $limit-1;

        if($total_pages < $remaining)
            $remaining = $total_pages;
        
        if($lastpage >= 1)
        {
            $pagination1 = $start1."&nbsp;-&nbsp;".$remaining."&nbsp;of&nbsp;".$total_pages."&nbsp;&nbsp;";

            if($page == 1)
                $pagination .= "".$pagination1."<strong>&laquo;</strong>&nbsp;First&nbsp;&nbsp;";
            else
                $pagination .= "".$pagination1."<strong>&laquo;</strong>&nbsp;<a href='$targetpage&$var_name=1$tag_name'>First</a>&nbsp;&nbsp;";

            if($lastpage < 7 + ($adjacents * 2))
            {
                for($counter = 1;$counter <= $lastpage;$counter++)
                {
                    if($counter == $page)
                        $pagination .= "$counter";
                    else
                        $pagination .= "<a href='$targetpage&$var_name=$counter$tag_name'>  $counter  </a>";
                }
            }
            elseif($lastpage > 5 + ($adjacents * 2))
            {
                if($page < 1 + ($adjacents * 2))
                {
                    for($counter = 1;$counter < 4 + ($adjacents * 2);$counter++)
                    {
                        if($counter == $page)
                            $pagination .= "$counter";
                        else
                            $pagination .= "<a href='$targetpage&$var_name=$counter$tag_name'>  $counter  </a>";
                    }
                    $pagination .= "...";
                    $pagination .= "<a href='$targetpage&$var_name=$lpm1$tag_name'>  $lpm1  </a>";
                    $pagination .= "<a href='$targetpage&$var_name=$lastpage$tag_name'>  $lastpage  </a>";
                }
                elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
                {
                    $pagination .= "<a href='$targetpage&$var_name=1$tag_name'>  1  </a>";
                    $pagination .= "<a href='$targetpage&$var_name=2$tag_name'>  2  </a>";
                    $pagination .= "...";
                    for($counter = $page - $adjacents;$counter <= $page + $adjacents;$counter++)
                    {
                        if($counter == $page)
                            $pagination .= "$counter";
                        else
                            $pagination .= "<a href='$targetpage&$var_name=$counter$tag_name'>  $counter  </a>";
                    }
                    $pagination .= "...";
                    $pagination .= "<a href='$targetpage&$var_name=$lpm1$tag_name'>  $lpm1  </a>";
                    $pagination .= "<a href='$targetpage&$var_name=$lastpage$tag_name'>  $lastpage  </a>";
                }
                else
                {
                    $pagination .= "<a href='$targetpage&$var_name=1$tag_name'>  1  </a>";
                    $pagination .= "<a href='$targetpage&$var_name=2$tag_name'>  2  </a>";
                    $pagination .= "...";
                    for($counter = $lastpage - (2 + ($adjacents * 2));$counter <= $lastpage;$counter++)
                    {
                        if($counter == $page)
                            $pagination .= "$counter";
                        else
                            $pagination .= "<a href='$targetpage&$var_name=$counter$tag_name'>  $counter  </a>";
                    }
                }
            }

            if($page > 1)
                $pagination .= "&nbsp;&nbsp;<strong>&#8249;</strong><a href='$targetpage&$var_name=$prev$tag_name'>Previous</a>&nbsp;&nbsp;&nbsp;&nbsp;";
            else
                $pagination .= "&nbsp;&nbsp;<strong>&#8249;</strong> Previous&nbsp;&nbsp;&nbsp;&nbsp;";

            if($page < $counter - 1)
                $pagination .= "<a href='$targetpage&$var_name=$next$tag_name'>Next&nbsp;</a><strong>&#8250;</strong>&nbsp;&nbsp;";
            else
                $pagination .= "Next&nbsp;<strong>&#8250;</strong>&nbsp;&nbsp;";

            if($page == $lastpage)
                $pagination .= "Last&nbsp;<strong>&raquo;</strong>&nbsp;&nbsp;&nbsp;&nbsp;";
            else
                $pagination .= "<a href=$targetpage&$var_name=$lastpage$tag_name>Last&nbsp;</a><strong>&raquo;</strong>&nbsp;&nbsp;&nbsp;&nbsp;";

            $pagination = "Showing : ".$pagination;
        }

        return $pagination;
    }

    protected function func_update()
    {
        if(empty($this->posted_data) === false) {

            foreach($this->posted_data as $key => $value) {

                $productid  = (int) $key;

                $if_exists  = func_query_first_cell("SELECT count(*) FROM ".$this->sql_tbl['warehouse']." WHERE productid = ".$productid);

                if($if_exists) {

                    if(isset($value['variantid'])) {

                        foreach($value['variantid'] as $key1 => $value1) {

                            $is_exists = func_query_first_cell("SELECT count(*) FROM ".$this->sql_tbl['warehouse']." WHERE productid = ".$productid." AND variantid = ".$key1);

                            if($is_exists) {

                                $this_data  =   array(
                                                    'quantity' => $value1['quantity']
                                                );
                                func_array2update('warehouse', $this_data, "productid = ".$productid." AND variantid = ".$key1);
                            }
                            else {

                                $this_data  =   array(
                                                'productid' => $productid,
                                                'variantid' => $key1,
                                                'quantity'  => $value1['quantity']
                                            );
                                func_array2insert('warehouse', $this_data, true);
                            }
                        }
                    }
                    else {

                        $this_data  =   array(
                                            'quantity' => $value['quantity']
                                        );
                        func_array2update('warehouse', $this_data, "productid = ".$productid);
                    }
                }
                else {

                    if(isset($value['variantid'])) {

                        foreach($value['variantid'] as $key1 => $value1) {

                            $this_data  =   array(
                                                'productid' => $productid,
                                                'variantid' => $key1,
                                                'quantity'  => $value1['quantity']
                                            );
                            func_array2insert('warehouse', $this_data, true);
                        }
                    }
                    else {

                        $this_data  =   array(
                                            'productid' => $productid,
                                            'variantid' => NULL,
                                            'quantity'  => $value['quantity']
                                        );
                        func_array2insert('warehouse', $this_data, true);
                    }
                }
            }
        }

        func_header_location("warehouse.php?mode=search&page=".$this->page);
    }

    public function __destruct()
    {
        unset($this->sql_tbl);
        unset($this->smarty);
        unset($this->posted_data);
        unset($this->provider_id);
        unset($this->page);
        unset($this->filter_by);
    }

}

new Warehouse();