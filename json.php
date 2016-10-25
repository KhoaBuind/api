<?php
header('Content-Type: application/json');
include "dbconfig.php";
$result = selectData("food");
echo json_encode(array('restaurants' => $result));