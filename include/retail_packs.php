<?php

/**
 * To generate retail packs interface
 *
 * @author     Manoj <manoj@elitehour.com>
 * @modified   December-26-2012
 */

require_once './auth.php';

ini_set("max_execution_time", 0);
set_time_limit(0);

final class Retail_Packs {

    private static $pack_size = 4;

    public function __construct()
    {
        global $sql_tbl, $smarty, $wholesaler_id, $provider_id, $setlive;

        $this->sql_tbl          =   $sql_tbl;
        $this->smarty           =   $smarty;
        $this->wholesaler_id    =   (int) $wholesaler_id;
        $this->provider_id      =   (int) $provider_id;
        $this->setlive          =   (string) $setlive;

        self::func_index();
    }

    protected function func_index()
    {
        if(empty($this->wholesaler_id) || empty($this->provider_id)) {

            return false;
        }

        if(empty($this->setlive) === false) {

            $condition = " AND products.forsale = '".$this->setlive."'";
        }

        $sql    =   "SELECT
                        products.productid,
                        product_packs.size_count
                     FROM
                        ".$this->sql_tbl['products']." AS products
                     INNER JOIN
                        ".$this->sql_tbl['product_packs']." AS product_packs
                     ON
                        product_packs.productid = products.productid
                     WHERE
                        products.provider = ".$this->provider_id."
                     AND
                        products.wholesaler_id = ".$this->wholesaler_id."
                     ".$condition."
                     AND
                        product_packs.size = ''
                     AND
                        product_packs.size_count >= ".self::$pack_size."
                     GROUP BY
                        products.productid 
                     ORDER BY
                        products.productid";
        $result =   func_query($sql, USE_SQL_DATA_CACHE);

        if(empty($result)) {

            return false;
        }

        $count  =   1;

        foreach($result as $key => $value) {

            self::func_generate_packs($value['productid'], $value['size_count']);

            $count++;

            if($count == 100) {

                sleep(5);
                $count = 1;
            }
        }
    }

    protected function func_generate_packs($productid, $size_count)
    {
        $packs = (int) $size_count / self::$pack_size;
        $packs = floor($packs);

        for($i=1;$i<=self::$pack_size;$i++) {

            $product_packs[] = $packs * $i;
        }

        if(empty($product_packs) === false) {

            db_query("UPDATE ".$this->sql_tbl['products']." SET product_packs = '".implode(',', $product_packs)."' WHERE productid = '$productid'");
        }
    }

    public function __destruct()
    {
        unset($this->sql_tbl);
        unset($this->smarty);
        unset($this->wholesaler_id);
        unset($this->provider_id);
        unset($this->setlive);
    }

}

new Retail_Packs();