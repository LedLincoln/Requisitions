<?php
require_once '../lib/rb.php';
R::setup('mysql:host=localhost;dbname=reqs',
        'redbean','redbean1');
$term = $_GET['term'];
//$term = $argv[1];
$termp = "%$term%";
//$vlist = R::getCol("SELECT CONCAT(vendornum, ' ', vendorname) FROM vendor WHERE vendorname LIKE ? OR vendornum LIKE ?", [$termp, $termp]);
//another way:
$vlist = R::getCol("SELECT CONCAT(vendornum, ' ', vendorname) AS comb FROM vendor HAVING comb LIKE ?", [$termp]);
$jout = json_encode($vlist);
echo $jout;
//print_r($jout);
?>
