<?php
require_once 'auth.php';
require_once 'functions.php';
require_once 'arrays.php';
$puid = $_POST['userid'] ?? '';
$ubean = R::load('requser', $puid);
$uid = $ubean->id;

if($uid !== 0) {
  $ubean->updated = $today;
  $ubean->updatedby = $dn;
} else {
  die("Cannot find specified user.");
}
$papplevel = $_POST['applevel'] ?? 0;
$proles = $_POST['roles'] ?? [];
if(!array_key_exists($papplevel, $approvallevels)) {
  die("Invalid approval level provided.");
}
$ubean->applevel = $papplevel;
//clear previous values
$ubean->sharedRoleList = array();
foreach($proles as $pr) {
  $rbean = R::findOne('role', 'rolename = ?', [$pr]);
  $ubean->sharedRoleList[] = $rbean;
}
try {
  $uid = R::store($ubean);
} catch(Exception $e) {
  die($e->getMessage());
}
R::close();
echo "User updated!";
$redirect = "listusers.php";
//$redirect = "dispuser.php?id=$uid";
//header("Location:$redirect");
?>
