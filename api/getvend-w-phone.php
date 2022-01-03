<?php
require_once '../lib/rb.php';
R::setup('mysql:host=localhost;dbname=reqs',
        'redbean','redbean1');
$term = $_GET['term'];
//$term = $argv[1];
$termp = "%$term%";
//$vlist = R::getAll("SELECT vendorname AS label, vendornum AS value FROM vendor WHERE vendorname LIKE ?", [$termp]);
$vlist = R::getAll("SELECT CONCAT(vendornum, ' ', vendorname) AS label, vendornum as value, vendorname, phone FROM vendor HAVING label LIKE ?", [$termp]);
$jout = json_encode($vlist);
echo $jout;
//print_r($jout);
?>
