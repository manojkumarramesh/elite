<?php

/*----- Include Files -----*/
require_once 'auth.php';
require_once 'include/classes/class.wordstream_api.php';

if(empty($start) && empty($end)) {

    echo "Missing : Start and End value";
    return false;
}

$wordstream_obj =   new WordStream('gapi@elitehour.com', '6I5n5hDLqu');

$limit          =   "LIMIT ".$start.", ".$end;

$sql            =   "SELECT
                        DISTINCT($sql_tbl[products].productid),
                        $sql_tbl[products].product,
                        $sql_tbl[products].fulldescr,
                        $sql_tbl[products].add_date
                    FROM
                        $sql_tbl[products]
                    WHERE
                        $sql_tbl[products].forsale = 'Y'
                    ORDER BY
                        $sql_tbl[products].add_date DESC
                    ".$limit;
$result         =   func_query($sql);

$count          =   1;

foreach($result as $key => $value) {

    $text       =   "'".trim($value['product'])."'";

    echo "<strong>Product Name : </strong>".$text."<br/>";

    $response   =   $wordstream_obj->getKeywords($text, $max);

    if(empty($response) === false) {

        $keywords   =   '';

        if(is_array($response)) {

            foreach($response as $key1 => $value1) {

                $keywords[]     =   $value1[0];
            }
            $keywords   =   implode(", ", $keywords);
        }
        else {

            $keywords   =   $value1[0];
        }
    }

    if(empty($keywords) === false) {

        echo "<br/><strong>keywords : </strong>".$keywords."<br/><br/>";
    }

    $count++;
    if($count == 5) {

        sleep(2);
        $count      =   1;
    }
}

?>