<?php

require dirname(__FILE__) . '/../admin/auth.php';

function get_all_categories($parent, $indent = 0){ 
	global $sql_tbl;
	$output        = array(); 
	$sqlResult     = mysql_query("SELECT categoryid FROM $sql_tbl[categories] WHERE parentid = '$parent'");  
	$num_rows     = mysql_num_rows($sqlResult);  
	 
	if ($num_rows > 0) {  
		while($row = mysql_fetch_assoc($sqlResult)) {  
			  
			if (has_sub($row['categoryid'])) {  
				$output [$row['categoryid']]= get_all_categories($row['categoryid'], $indent++);  
			}  
			else
				$output[] = $row['categoryid'] . '<br>';
		}  
	}  
	return $output; 
} 
 
function has_sub($id){ 
	global $sql_tbl;
	$sqlResult     = mysql_query("SELECT categoryid FROM $sql_tbl[categories] WHERE parentid=".$id);  
	$num_rows     = mysql_num_rows($sqlResult);  
	 
	return $num_rows >= 1 ? true : false; 
} 

$google_cat_list    =   func_query('SELECT c.categoryid, c.category, 
                                    cu.google_category FROM `xcart_categories` c 
                                    LEFT JOIN custom_category_mapping cu 
                                    ON cu.cid = c.categoryid WHERE c.parentid =9');

$result = get_all_categories(9);

$google_cant_cnt    =   count($google_cat_list);

foreach($google_cat_list as $value){
    
    foreach($result[$value['categoryid']] as $key=>$new_val) {   
        if(is_array($new_val)){
            foreach($new_val as $sub_new_val) {                     
                func_array2insert(              
                'custom_category_mapping',
                array(
                'cid' =>  $sub_new_val,                
                'google_category' =>  $value['google_category'],                
                )
                );
            }
        }
    }

    if(!is_array($new_val)){
        $cnt_ary = count($result[$value['categoryid']]);
        for($i=0;$i<$cnt_ary;$i++) {                
            func_array2insert(              
            'custom_category_mapping',
            array(
            'cid' =>  $result[$value['categoryid']][$i],                
            'google_category' =>  $value['google_category'],                
            )
            );                    
        }                                          
    }

}
echo 'Completed';
?>