<?php
//require_once '../lib/rb.php';
//R::setup('mysql:host=localhost;dbname=reqs',
//        'redbean','redbean1');
require_once '../auth.php';
$obo = $_GET['obo'];
//$obo = $argv[1];
$ubean = R::findOne('requser', 'username = ?', [$obo]);
$deptnum = $ubean->deptnum;
if($deptnum == "") {
  echo "[]";
  exit();
}
$dept4 = substr($deptnum, -4);
$deptwc = "%" . $dept4 . "%";
$qry = "SELECT acctnum, description FROM account WHERE validfor LIKE ? ORDER BY description";
$accts = R::getAll($qry, [$deptwc]);
$output = array('dept'=>$dept4, 'accts'=>$accts);
$jout = json_encode($output);
echo $jout;
?>
