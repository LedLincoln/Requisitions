<?php
require_once 'auth.php';
$glid = $_GET['lineid'];
$linedata = R::getAssoc('SELECT * FROM lineitem WHERE id = ?', [$glid]);
if(empty($linedata)) {
  echo "NOT FOUND";
} else {
  $jout = json_encode($linedata[$glid]);
  echo $jout;
}
?>
