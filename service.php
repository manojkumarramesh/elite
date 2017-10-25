<?php

require_once 'auth.php';

ini_set("display_errors", 1);

final class Service {

    public function __construct()
    {
        global $sql_tbl, $q, $xcart_http_host, $xcart_web_dir;

        $this->sql_tbl      =   $sql_tbl;
        $this->method       =   (int) $q;
        $this->http_host    =   (string) $xcart_http_host;
        $this->web_dir      =   (string) $xcart_web_dir;

        $function_call      =   array(
                                    '101'   =>  'func_recently_viewed',
                                    '102'   =>  'func_recently_sold'
                                );

        if(array_key_exists($this->method, $function_call)) {

            $this->response =   self::$function_call[$this->method]();
        }
        else {

            $this->response =   self::func_index();
        }

        echo $this->response;
    }

    protected function func_index()
    {
        $result['response'] = 'index';

        return json_encode($result);
    }

    protected function func_recently_viewed()
    {
        $result['response'] = 'func_recently_viewed';

        return json_encode($result);
    }

    protected function func_recently_sold()
    {
        $sql    =   "SELECT
                        products.productid,
                        products.product,
                        products.list_price,
                        pricing.price,
                        CONCAT('".$this->http_host."', '".$this->web_dir."', images_T.image_path) AS image_path,
                        images_T.image_type,
                        images_T.image_x,
                        images_T.image_y
                     FROM
                        ".$this->sql_tbl['products']." AS products
                     INNER JOIN
                        ".$this->sql_tbl['order_details']." AS order_details
                     ON
                        (order_details.productid = products.productid)
                     INNER JOIN
                        ".$this->sql_tbl['orders']." AS orders
                     ON
                        (orders.orderid = order_details.orderid)
                     INNER JOIN
                        ".$this->sql_tbl['pricing']." AS pricing
                     ON
                        (pricing.productid = products.productid)
                     INNER JOIN
                        ".$this->sql_tbl['images_T']." AS images_T
                     ON
                        (images_T.id = products.productid)
                     WHERE
                        orders.status IN ('P','C')
                     AND
                        products.forsale = 'Y'
                     AND
                        products.avail > '0'
                     GROUP BY
                        products.productid
                     ORDER BY
                        orders.orderid DESC LIMIT 0, 10";
        $data   =   func_query($sql);
        //debug_array($data);die;

        if(!empty($data)) {

            $result['response'] = $data;
        }
        else {

            $result['response'] = "No Record(s) Found";
        }

        return json_encode($result);
    }

    public function __destruct()
    {
        unset($this->sql_tbl);
        unset($this->method);
        unset($this->response);
    }

}

new Service();