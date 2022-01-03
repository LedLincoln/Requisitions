<?php
require_once '../lib/rb.php';
R::setup('mysql:host=localhost;dbname=reqs',
        'redbean','redbean1');
$term = $_GET['term'];
//$term = $argv[1];
$termp = "%$term%";
$vlist = R::getAll("SELECT CONCAT(vendornum, ' ', vendorname) AS label, vendornum as value FROM vendor HAVING label LIKE ?", [$termp]);
$jout = json_encode($vlist);
echo $jout;
?>
