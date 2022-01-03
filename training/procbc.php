<?php
require_once 'auth.php';
require_once 'functions.php';
require_once 'arrays.php';
if(!in_array("Admin", $userroles)) {
  die("You are not authorized to edit buyer codes.");
}
$pbcid = $_POST['bcid'] ?? '';
$bcbean = R::load('buyercode', $pbcid);
$bcid = $bcbean->id;

if($bcid !== 0) {
  $bcbean->updated = $now;
  $bcbean->updatedby = $dn;
} else {
  //die("Cannot find specified buyer code.");
  $bcid = $bcbean = R::dispense('buyercode');
}
$pbcnum = $_POST['codenumber'] ?? '';
$pbcname = $_POST['codename'] ?? '';
if($pbcnum . $pbcname != '') {
  $bcbean->rolenum = $pbcnum;
  $bcbean->rolename = $pbcname;
  try {
    $uid = R::store($bcbean);
    echo "Buyer code updated.";
  } catch(Exception $e) {
    die($e->getMessage());
  }
} else {
  echo "Must have code number and code name.";
}
R::close();
//$redirect = "listbc.php";
//echo $redirect;
?>
