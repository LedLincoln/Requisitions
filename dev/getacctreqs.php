<?php
require_once 'auth.php';
$acctno = $_POST['acctnum'];
//$projdata = R::getAssoc('SELECT * FROM buyercode WHERE id = ?', [$gbcid]);
$projdata = R::getAssocRow('SELECT id, description, onbehalf, total FROM req WHERE account = ?', [$acctno]);
if(empty($projdata)) {
  echo "NOT FOUND";
} else {
  //$jout = json_encode($projdata['5']);
  $jout = json_encode($projdata);
  echo $jout;
}
?>
