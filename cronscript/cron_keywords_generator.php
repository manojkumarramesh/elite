<?php

require_once dirname(__FILE__) . '/../admin/auth.php';

ini_set("max_execution_time", 0);

set_time_limit(0);

final class Keywords_Generator {

    private static $file_path    = 'keywords_generator.txt';
    private static $file_log     = 'keywords_generator_log.txt';
    private static $yql_base_url = 'http://query.yahooapis.com/v1/public/yql';
    private static $yql_query    = 'select * from contentanalysis.analyze where text = ';

    public function __construct()
    {
        if(!function_exists('curl_init'))
            die('CURL is not installed!');

        if(!file_exists(self::$file_path))
            die('missing file '.self::$file_path);

        $todays_date    = date('d/m/y');
        $dates          = file(self::$file_path);

        if(empty($dates))
            die('missing date record(s)');

        foreach($dates as $value) {

            $date       = explode(" ", trim($value));

            if($todays_date == $date[0]) {

                $start  = (int) $date[1];
                $end    = (int) $date[2];
            }
        }

        if(empty($start) || empty($end))
            die('missing start or end value');

        self::func_generate_keywords($start, $end);
    }

    protected function func_strip_special_chars($string)
    {
        return preg_replace("/[^A-Za-z0-9]+/", " ", $string);
    }

    protected function func_content_analysis_for_text($text)
    {
        if(empty($text)) {

            echo "Missing Text for Content Analysis";
            return false;
        }

        $query          = self::$yql_query.htmlentities("'".$text."'");

        $request        = self::$yql_base_url.'?q='.urlencode($query).'&format=json';

        $ch             = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        $response       = curl_exec($ch);
        curl_close($ch);

        //echo "<strong>Request  : </strong>".$request."<br/>";
        //echo "<strong>Response : </strong>".$response."<br/><br/>";

        return $response;
    }

    protected function func_generate_keywords($start, $end)
    {
        global $sql_tbl;

        $sql    =   "SELECT
                        products.productid,
                        products.product,
                        products.fulldescr
                     FROM
                        $sql_tbl[products] AS products
                     WHERE
                        products.productid
                     BETWEEN
                        ".$start."
                     AND
                        ".$end;
        $result =   func_query($sql);

        if(empty($result))
            die('no product(s) found');

        $count  =   1;

        foreach($result as $value) {

            $name       = (string) self::func_strip_special_chars(trim($value['product']));
            $desc       = (string) self::func_strip_special_chars(trim($value['fulldescr']));

            $response   = self::func_content_analysis_for_text($name.' '.$desc);

            $current    = file_get_contents(self::$file_log);
            $current   .= 'Product ID : '.$value['productid'].' -> '.$response."\n\n";
            file_put_contents(self::$file_log, $current);

            if(empty($response) === false) {

                $yql_response   = json_decode($response);

                $keywords       = '';

                if(!is_null($yql_response->query->results)) {

                    if(is_array($yql_response->query->results->entities->entity)) {

                        foreach($yql_response->query->results->entities->entity as $key1 => $value1) {

                            if(empty($value1->text->content) === false) {

                                $keywords[] = $value1->text->content;
                            }
                        }
                        $keywords = implode(", ", $keywords);
                    }
                    else {

                        $keywords = $yql_response->query->results->entities->entity->text->content;
                    }
                }
            }

            if(empty($keywords) === false) {

                $insert_data    =   array(
                                        'keywords'      => $keywords,
                                        'meta_keywords' => $keywords
                                    );
                func_array2update('products', $insert_data, "productid = '".$value['productid']."'");
            }

            $count++;

            if($count == 10) {

                sleep(2);
                $count = 1;
            }
        }
    }

}

new Keywords_Generator();