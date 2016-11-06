<?php
$ip = '118.68.119.117';//getenv('REMOTE_ADDR');
switch ($ip){
    case '118.68.119.117' :
        require_once "config/production/config.php";
        break;
    case '127.0.0.1' :
        require_once "config/local/config.php";
        break;
    //case '172.31.20.241' : require_once "config/production/config.php";
    //break;
}

$dbhost = $config['database']['host'];
$dbuser = $config['database']['username'];
$dbname = $config['database']['dbname'];
$dbpass = $config['database']['password'];

echo "<pre>";
print_r($config['database']);
echo "</pre>";

$options = array(
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
);

// Try connecting Database
try {
    # MySQL with PDO_MYSQL
    $DBH = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass, $options);
}
catch(PDOException $e) {
    echo $e->getMessage();
}

function cleanUserInput($input) {
    if (get_magic_quotes_gpc()) {
          $clean = mysql_real_escape_string(stripslashes($input));
    } else {
          $clean = mysql_real_escape_string($input);
    }
    $clean = htmlentities($input, ENT_QUOTES, 'UTF-8');
    
    return $clean;
}

/**
 * @param string $table
 * @param array $name - one dimension
 * @param array $where - two dimension
 */
function selectData($table = "", $name = array(), $where = array(), $is_single = 0, $offset=0, $limit=0, $groupby = '', $orderby = array(), $sort = 'ASC')
{
    if (empty($table)) return false;
    
    global $DBH;
    
    $aSelStr = array();
    $sSelStr = "*";
    if (!empty($name)) {
        foreach ($name as $value_1) {
            //$aSelStr[] = sprintf("%s", $value_1);
            $aSelStr[] = $value_1;
        }
        $sSelStr = implode(",", $aSelStr);
    }

    $sWhereStr = "";
    //$aWhereStr = array("description!=''");
    if (!empty($where)) {
        foreach ($where as $key_2 => $value_2) {
            $aWhereStr[] = sprintf("%s='%s'", $key_2, $value_2);
        }
        $sWhereStr = " WHERE ".implode(" AND ", $aWhereStr);
    }
    else {
        if (!empty($aWhereStr)) {
            $sWhereStr = " WHERE ".implode(" AND ", $aWhereStr);
        }
    }
    
    // Limit
    $sLimit = '';
    if($offset >=0 && $limit > 0){
        $sLimit = sprintf(" LIMIT %d,%d", $offset, $limit);    
    }
    

    // Group by
    $sGroupBy = "";
    if (!empty($groupby)) {
        $sGroupBy = sprintf(" GROUP BY %s", $groupby);
    }
    
    // Sorting
    $sSort = "";
    if (!empty($orderby)) {
        $sOrderBy = implode(",", $orderby);
        $sSort = sprintf(" ORDER BY %s %s", $sOrderBy, $sort);
    }
    $sql = sprintf("SELECT %s FROM %s%s%s%s%s", $sSelStr, $table, $sWhereStr, $sGroupBy, $sSort, $sLimit);
    //echo $sql; exit();
    $STH = $DBH->prepare($sql);
    $STH->setFetchMode(PDO::FETCH_ASSOC);
    $STH->execute();
    
    if (empty($is_single)) return $STH->fetchAll();
    else return $STH->fetch();
}

function updateData($table = "", $data = array(), $where = array())
{
    if (empty($table) || empty($data) || empty($where)) return false;
    
    global $DBH;
    
    $aUpdStr = array();
    foreach ($data as $key_1 => $value_1) {
        $aUpdStr[] = sprintf("%s='%s'", $key_1, $value_1);
    }
    
    $aWhereStr = array();
    foreach ($where as $key_2 => $value_2) {
        $aWhereStr[] = sprintf("%s='%s'", $key_2, $value_2);
    }
    
    $sql = "UPDATE $table SET ".implode(",", $aUpdStr)." WHERE ".implode(" AND ", $aWhereStr);//echo $sql;
    $STH = $DBH->prepare($sql);
    $STH->execute();
    
    return true;
}

function insertData($table = "", $data = array())
{
    if (empty($table) || empty($data)) return false;
    
    global $DBH;
    
    $aNameStr = array();
    foreach ($data as $key => $value) {
        $aNameStr[] = sprintf("`%s`", $key);
        $aValueStr[] = sprintf("'%s'", $value);
    }
    
    $sql = sprintf("INSERT INTO $table (%s) VALUES (%s)", implode(",", $aNameStr), implode(",", $aValueStr));
    //echo $sql; exit();
    $STH = $DBH->prepare($sql);
    $STH->execute();
    
    return true;
}
