<?php

/**
 * Products search grid interface
 *
 * @category   X-Cart
 * @subpackage Provider interface
 * @author     Manoj <manoj@elitehour.com>
 * @modified   December-26-2012
 */

class Search {

    protected function func_index()
    {
        $sql    =   "SELECT
                        wholesalers.wholesaler_id,
                        wholesalers.wholesaler_name
                     FROM
                        ".$this->sql_tbl['wholesalers']." AS wholesalers
                     ORDER BY
                        wholesalers.wholesaler_name";
        $result =   func_query($sql, USE_SQL_DATA_CACHE);

        if(empty($result)) {

            return false;
        }

        if(empty($result) === false && empty($this->wholesaler_id) === false) {

            foreach($result as $key => $value) {

                if(in_array($this->wholesaler_id, $value)) {

                    $result[$key]['selectbox'] = 'selected';
                }
            }
        }

        $this->smarty->assign('wholesalers', $result);
    }

    protected function func_list($limit="")
    {
        if(empty($this->wholesaler_id) || empty($this->provider_id)) {

            return false;
        }

        if(empty($this->setlive) === false) {

            $condition = " AND products.forsale = '".$this->setlive."'";
        }

        $sql    =   "SELECT
                        DISTINCT(products.productid),
                        products.product,
                        products.list_price,
                        pricing.price,
                        products.lowest_possible_price,
                        products.vendor_cost,
                        products.forsale,
                        products.paid_listing,
                        products.featured,
                        products.whole_sale_price,
                        products.product_packs,
                        social.facebook_status,
                        social.twitter_status,
                        social.pinterest_status,
                        image.image_path,
                        image.image_x,
                        image.image_y
                     FROM
                        ".$this->sql_tbl['products']." AS products
                     INNER JOIN
                        ".$this->sql_tbl['pricing']." AS pricing
                     ON
                        pricing.productid = products.productid
                     INNER JOIN
                        ".$this->sql_tbl['images_PL']." AS image
                     ON
                        (image.id = products.productid
                     AND
                        image.pos = 0)
                     LEFT JOIN
                        ".$this->sql_tbl['product_socialmedia']." AS social
                     ON
                        social.productid = products.productid   
                     WHERE
                        products.provider = ".$this->provider_id."
                     AND
                        products.wholesaler_id = ".$this->wholesaler_id."
                     ".$condition."
                     GROUP BY
                        products.productid 
                     ORDER BY
                        products.productid".$limit;
        $result =   func_query($sql, USE_SQL_DATA_CACHE);

        if(empty($result)) {

            return false;
        }

        //Elitehour - 21-12-2012
        foreach($result as $key => $val) {

            $sql = "SELECT size, size_count FROM ".$this->sql_tbl['product_packs']." WHERE productid = '".$val['productid']."'";
            $result_packs = func_query($sql, "productid");
            $result[$key]['packs'] = $result_packs;
        }

        foreach($result as $key => $val) {

            $sql_cnt = "SELECT SUM(size_count) AS packscnt FROM ".$this->sql_tbl['product_packs']." WHERE productid = '".$val['productid']."'";
            $packs_cnt = func_query($sql_cnt, "productid");
            $result[$key]['packs_cnt'] = $packs_cnt;
        }
        //Elitehour - 21-12-2012

        return json_encode($result);
    }

    protected function func_search()
    {
        $limit = 50;
        $page  = "";

        if(isset($this->page) && empty($this->page) === false) {

            $page  = $this->page;
            $start = ($page - 1) * $limit;
        }
        else {

            $start = 0;
        }
         
        if(isset($this->mode) && ($this->mode == 'search')) {

            $page_show = "mode=search&wholesaler_id=".$this->wholesaler_id."&setlive=".$this->setlive;
        }

        $varname     = "page";
        $targetpage  = "search_grid.php?".$page_show;

        $lists       = self::func_list();
        $lists       = json_decode($lists, true);
        $total_pages = count($lists);
        
        $limit_cond  = " LIMIT $start, $limit";
        $result      = self::func_list($limit_cond);
        $result      = json_decode($result, true);

        $pagination  = self::func_pagination($total_pages, $limit, $targetpage, $page, $start, $varname);

        $this->smarty->assign('products'      , $result);
        $this->smarty->assign('pagination'    , $pagination);
        $this->smarty->assign('page'          , $page);
        $this->smarty->assign('wholesaler_id' , $this->wholesaler_id);
        $this->smarty->assign('setlive'       , $this->setlive);
    }
    
    private function func_pagination($total_pages, $limit, $targetpage, $page, $start, $var_name, $tag_name="")
    {
        $adjacents  = 1;

        if($page == 0) {
            $page = 1;
        }

        $prev       = $page - 1;
        $next       = $page + 1;
        $lastpage   = ceil($total_pages/$limit);
        $lpm1       = $lastpage - 1;

        $pagination = "";

        $start1     = ($start + 1);
        $remaining  = $start1 + $limit-1;

        if($total_pages < $remaining) {
            $remaining = $total_pages;
        }
        
        if($lastpage >= 1)
        {
            $pagination1 = $start1."&nbsp;-&nbsp;".$remaining."&nbsp;of&nbsp;".$total_pages."&nbsp;&nbsp;";

            if($page == 1) {
                $pagination .= "".$pagination1."<strong>&laquo;</strong>&nbsp;First&nbsp;&nbsp;";
            }
            else {
                $pagination .= "".$pagination1."<strong>&laquo;</strong>&nbsp;<a href='$targetpage&$var_name=1$tag_name'>First</a>&nbsp;&nbsp;";
            }

            if($lastpage < 7 + ($adjacents * 2))
            {
                for($counter = 1;$counter <= $lastpage;$counter++)
                {
                    if($counter == $page) {
                        $pagination .= "$counter";
                    }
                    else {
                        $pagination .= "<a href='$targetpage&$var_name=$counter$tag_name'>  $counter  </a>";
                    }
                }
            }
            elseif($lastpage > 5 + ($adjacents * 2))
            {
                if($page < 1 + ($adjacents * 2))
                {
                    for($counter = 1;$counter < 4 + ($adjacents * 2);$counter++)
                    {
                        if($counter == $page) {
                            $pagination .= "$counter";
                        }
                        else {
                            $pagination .= "<a href='$targetpage&$var_name=$counter$tag_name'>  $counter  </a>";
                        }
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
                        if($counter == $page) {
                            $pagination .= "$counter";
                        }
                        else {
                            $pagination .= "<a href='$targetpage&$var_name=$counter$tag_name'>  $counter  </a>";
                        }
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
                        if($counter == $page) {
                            $pagination .= "$counter";
                        }
                        else {
                            $pagination .= "<a href='$targetpage&$var_name=$counter$tag_name'>  $counter  </a>";
                        }
                    }
                }
            }

            if($page > 1) {
                $pagination .="&nbsp;&nbsp;<strong>&#8249;</strong><a href='$targetpage&$var_name=$prev$tag_name'>Previous</a>&nbsp;&nbsp;&nbsp;&nbsp;";
            }
            else {
                $pagination .= "&nbsp;&nbsp;<strong>&#8249;</strong> Previous&nbsp;&nbsp;&nbsp;&nbsp;";
            }

            if($page < $counter - 1) {
                $pagination .= "<a href='$targetpage&$var_name=$next$tag_name'>Next&nbsp;</a><strong>&#8250;</strong>&nbsp;&nbsp;";
            }
            else {
                $pagination .= "Next&nbsp;<strong>&#8250;</strong>&nbsp;&nbsp;";
            }

            if($page == $lastpage) {
                $pagination .= "Last&nbsp;<strong>&raquo;</strong>&nbsp;&nbsp;&nbsp;&nbsp;";
            }
            else {
                $pagination .= "<a href=$targetpage&$var_name=$lastpage$tag_name>Last&nbsp;</a><strong>&raquo;</strong>&nbsp;&nbsp;&nbsp;&nbsp;";
            }

            $pagination = "Showing: ".$pagination;
        }

        return $pagination;
    }

    protected function func_update()
    {
        if(empty($this->posted_data) === false) {

            foreach($this->posted_data as $key => $value) {

                $key        =   (int) $key;

                $update     =   array();

                $update[]   =   (isset($value['forsale']))      ? "forsale = 'Y'"      : "forsale = 'N'";

                $update[]   =   (isset($value['paid_listing'])) ? "paid_listing = 'Y'" : "paid_listing = 'N'";

                $update[]   =   (isset($value['featured']))     ? "featured = 'Y'"     : "featured = 'N'";

                db_query("UPDATE ".$this->sql_tbl['products']." SET ".implode(",", $update)." WHERE productid = '$key'");

                if(isset($value['social_media'])) {

                    db_query("INSERT IGNORE INTO ".$this->sql_tbl['product_socialmedia']."(productid,facebook_status,twitter_status,pinterest_status) VALUES('$key','Q','Q','Q')");
                }
            }
        }

        func_header_location('search_grid.php?mode=search&wholesaler_id='.$this->wholesaler_id."&setlive=".$this->setlive.'&page='.$this->page);
    }

    protected function func_generate_retail_packs()
    {
        require_once 'retail_packs.php';

        func_header_location('search_grid.php?mode=search&wholesaler_id='.$this->wholesaler_id."&setlive=".$this->setlive.'&page='.$this->page);
    }

}