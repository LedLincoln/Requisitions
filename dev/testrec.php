<?php
require_once 'lib/rb.php';
R::setup('mysql:host=localhost;dbname=reqsdev;charset=utf8mb4','redbean','redbean1');
function getEmail($username) {
  $ubean = R::findOne('requser', 'username = ?', [$username]);
  $uid = $ubean->id;
  if($uid == 0) {
    return [];
  } else {
    return [$username => $ubean->email];
  }
}
$createdby = "Larry Dietrich";
$obo = "Ivan Johnson";
$recipients = getEmail($createdby);
//$recipients['Ivan Johnson'] = "ij@lic.com";
//array_push($recipients, getEmail($obo));
$recipients += getEmail($obo);
print_r($recipients);
foreach($recipients as $k => $v) {
  echo "$k $v\n";
}
echo "\n";
?>
