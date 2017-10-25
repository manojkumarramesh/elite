<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'auth.php';

/**
 * Get recently viewed products from the product table
 *
 * @param  bool  $detailed return short or detailed information about product
 * @return array numeric array with products data
 */

$sql    =      "SELECT
              	$sql_tbl[recently_viewed].view_id,
                $sql_tbl[products].productid,
                $sql_tbl[products].product,
                $sql_tbl[products].provider,
                $sql_tbl[images_PL].image_path,
                $sql_tbl[images_PL].image_type,
                $sql_tbl[images_PL].image_x,
                $sql_tbl[images_PL].image_y,
                $sql_tbl[images_PL].image_size,
                $sql_tbl[images_PL].filename,
                $sql_tbl[pricing].price
                FROM
                $sql_tbl[recently_viewed]
                INNER JOIN
                $sql_tbl[products]
                ON
                    ($sql_tbl[products].productid = $sql_tbl[recently_viewed].productid)
                JOIN
                $sql_tbl[images_PL]
                ON
                    ($sql_tbl[images_PL].id = $sql_tbl[recently_viewed].productid)
                JOIN
                    $sql_tbl[pricing]
                 ON
                    ($sql_tbl[pricing].productid = $sql_tbl[recently_viewed].productid)
                GROUP BY
                    $sql_tbl[recently_viewed].productid
                ORDER BY
                    $sql_tbl[recently_viewed].view_id DESC
                LIMIT 0,20";
    
     $listarr =   func_query($sql);

    if(empty($listarr)) {

        return false;
    }
    else {

        foreach($listarr as $key => $value) {

            if(empty($value['image_path'])) {

                unset($listarr[$key]);
            }
            else {

                $pattern = '/(png|jpg|jpeg)/';
                $var = preg_match($pattern, $value['image_path'], $matches);

                if(empty($var)) {

                    unset($listarr[$key]);
                }
            }
        }
    }
$smarty->assign('recently_viewed',$listarr);
echo func_display('modules/Recently_Viewed/custom_home_recently_viewed.tpl', $smarty, false);

/*$date_condition = "AND $sql_tbl[stats_shop].date > $last_viewed_date";
     
      $sql    =  "SELECT
                DISTINCT($sql_tbl[stats_shop].id),
                $sql_tbl[products].productid,
                $sql_tbl[products].product,
                $sql_tbl[products].provider,
                $sql_tbl[products].saleid,
                $sql_tbl[images_PL].image_path,
                $sql_tbl[images_PL].image_type,
                $sql_tbl[images_PL].image_x,
                $sql_tbl[images_PL].image_y,
                $sql_tbl[images_PL].image_size,
                $sql_tbl[images_PL].filename,
                $sql_tbl[pricing].price,
                $sql_tbl[stats_shop].date
                FROM
                $sql_tbl[stats_shop]
                INNER JOIN
                $sql_tbl[products]
                ON
                    ($sql_tbl[products].productid = $sql_tbl[stats_shop].id)
                JOIN
                $sql_tbl[images_PL]
                ON
                    ($sql_tbl[images_PL].id = $sql_tbl[products].productid)
                JOIN
                    $sql_tbl[pricing]
                 ON
                    ($sql_tbl[pricing].productid = $sql_tbl[products].productid)
                WHERE
                    $sql_tbl[stats_shop].action='V'
                AND 
                    xcart_stats_shop.date >= UNIX_TIMESTAMP(curdate())
                $date_condition
                ORDER BY
                    $sql_tbl[stats_shop].date DESC";
 
    $listarr =   func_query($sql);

    if(empty($listarr)) {

        return false;
    }
    else {

        foreach($listarr as $key => $value) {

            if(empty($value['image_path'])) {

                unset($listarr[$key]);
            }
            else {

                $pattern = '/(png|jpg|jpeg)/';
                $var = preg_match($pattern, $value['image_path'], $matches);

                if(empty($var)) {

                    unset($listarr[$key]);
                }
            }
        }
    }
  $content = "";
  $li_count = count($listarr);
  for($i=0; $i<$li_count; $i++){
      $content .= '<li class="newArrivalsLI" id="'.$listarr[$i]['productid'].'">
                                    <div class="na_prodblock">
                                        <div class="na_thumbcol" style="width: 140px; overflow: hidden;">
                                        <a href="product.php?productid='.$listarr[$i]['productid'].'" title="'.$listarr[$i]['product'].'">';
                  $content .= '<img src="image.php?type=PL&id='.$listarr[$i]['productid'].'" />';
                  $content .= '</a>
                            </div>';
                  $content .= '<div style="text-align:center;">
                                  <a href="product.php?productid='.$listarr[$i]['productid'].'" title="'.$listarr[$i]['product'].'">';
                  $content .= $listarr[$i]['product'];
                  $content .= '</a>
                                <span style="color:#ff0000; font-size: 1.2em;">'.$listarr[$i]['price'].'</span>
                            </div>
                           <div class="clearing"></div>
                        </div>
                    </li>';
  }
    
$result['html'] = $content;

$result['last'] = $listarr[0]['date'];
$result['li_length'] = $li_count;
echo json_encode($result);*/

    
?>

