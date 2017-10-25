<?php

ini_set('display_errors', 1);
ini_set('memory_limit', '512M');
ini_set( 'max_execution_time','0'); 

/*----- Include Files -----*/

require dirname(__FILE__) . '/../admin/auth.php';


$sel_purl	= db_query("SELECT a.productid as Id, a.productcode as PCode, concat('http://www.bvira.com/images/PL/', c.filename) as Image_Link
FROM xcart_products a
LEFT JOIN  xcart_images_PL c ON c.id=a.productid
where a.wholesaler_id=103
group by a.productid
ORDER BY a.productid DESC");
?>
<div style="width:1000px;height:auto;margin:10px 0px 0px 160px;float:left;border:1px solid #000;">
    <ul style="list-style: none;">
        
<?php
while ($product_url = db_fetch_array($sel_purl)) {
      $image = $product_url['Image_Link'];
      $pid= $product_url['Id'];
      $pcode= $product_url['PCode'];
      echo "<li style='width:200px;height:200px;padding:10px;text-align:center;display: inline;list-style-type: none;margin:10px;float:left';>
          <span style='float:left'>
              <img src='".$image."'/>
          </span><br/><br/>
          <span style='float:left'>".$pid."</span>
     </li> ";
      
}
?>
    </ul>
</div>
