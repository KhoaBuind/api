<?php
include "dbconfig.php";
$string = file_get_contents("http://latte.lozi.vn/v1.2/topics/1/photos?t=popular&cityId=50");
$json_a = json_decode($string, true);
foreach ($json_a as $key => $value) {
	foreach ($value as $k =>$v) {
		if(isset($v['dish']['eatery'])){
			$id = $v['dish']['eatery']['_id'];
			$result = selectData("food",array(),array('_id'=>$id));
			//echo count($result); exit();
			if(count($result) <=0){
				$aData = array();
				$aData['name'] = $v['dish']['eatery']['name'];
				$aData['address'] = $v['dish']['eatery']['address']['full'];
				$aData['description'] = $v['dish']['eatery']['description'];
				$aData['lat'] = $v['dish']['eatery']['latitude'];
				$aData['long'] = $v['dish']['eatery']['longitude'];
				//$aData['start'] = $v['dish']['eatery']['name'];
				//$aData['end'] = $v['dish']['eatery']['name'];
				$aData['image'] = $v['dish']['eatery']['avatar'];
				$aData['_id'] = $id;
				insertData('food',$aData);
			}
			
		}
		
	}
}