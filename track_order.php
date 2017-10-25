<?php

require_once 'auth.php';
require_once $xcart_dir.'/include/common.php';

//ini_set("display_errors", 1);

final class Track_Order {

    public function __construct()
    {
        global $sql_tbl, $smarty, $posted_data, $mode;

        $this->sql_tbl      =   $sql_tbl;
        $this->smarty       =   $smarty;
        $this->posted_data  =   (array)  $posted_data;
        $this->mode         =   (string) $mode;

        $function_call      =   array(
                                    "search" => "func_search"
                                );

        if(array_key_exists($this->mode, $function_call)) {

            self::$function_call[$this->mode]();
        }

        $location[]         =   array('Track Order', 'track_order.php');
        $this->smarty->assign('location', $location);
        $this->smarty->assign('main', 'track_order');
        func_display('customer/home.tpl', $this->smarty);
    }

    protected function func_search()
    {
        $orderid    =   (int)    $this->posted_data['orderid'];
        $emailid    =   (string) $this->posted_data['emailid'];

        if(empty($orderid) || 
           empty($emailid) || 
           (filter_var($orderid, FILTER_VALIDATE_INT) === false) || 
           (filter_var($emailid, FILTER_VALIDATE_EMAIL) === false)) {

            return false;
        }

        $sql        =   "SELECT
                            orders.orderid,
                            orders.userid,
                            orders.tracking,
                            orders.tracking_details,
                            orders.status,
                            orders.email
                         FROM
                            ".$this->sql_tbl['orders']." AS orders
                         INNER JOIN
                            ".$this->sql_tbl['order_details']." AS order_details
                         ON
                            order_details.orderid = orders.orderid
                         WHERE
                            orders.orderid = ".$orderid."
                         AND
                            orders.email = '".$emailid."'
                         ORDER BY
                            orders.orderid";
        $result     =   func_query_first($sql);

        if(empty($result) === false) {

            $this->smarty->assign('orders_list', $result);
        }
        $this->smarty->assign('orderid', $orderid);
        $this->smarty->assign('emailid', $emailid);
    }

    public function __destruct()
    {
        unset($this->sql_tbl);
        unset($this->smarty);
        unset($this->posted_data);
        unset($this->mode);
    }

}

new Track_Order();