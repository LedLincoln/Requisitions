<?php
require_once 'auth.php';
$gbcid = $_GET['bcid'];
$bcdata = R::getAssoc('SELECT * FROM buyercode WHERE id = ?', [$gbcid]);
if(empty($bcdata)) {
  echo "NOT FOUND";
} else {
  $jout = json_encode($bcdata[$gbcid]);
  echo $jout;
}
?>
