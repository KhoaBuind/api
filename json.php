<?php
header('Content-Type: application/json');
include "dbconfig.php";
$page = 1;
$limit  = 10;

if(isset($_GET['page'])){
	$page = $_GET['page'] > 0 ? $_GET['page'] : 1;
}
$offset = ($page - 1) * $limit;
$result = selectData("food",array(), array(), 0, $offset,$limit);
echo json_encode(array('restaurants' => $result));