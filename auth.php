<?php
//ten days
$sesslife = 864000;
session_name("Reqs");
session_set_cookie_params($sesslife);
session_start();
$username = $_SESSION["username"] ?? '';
//$dn = $_SESSION['username'];
$saveloc = false;
$redirectscripts = ['dispreq.php','dispuser.php','edreq.php','listreqs.php','listusers.php','search.php'];
foreach($redirectscripts as $r) {
  if(strpos($_SERVER['SCRIPT_NAME'], $r)) {
    $saveloc = true;
  }
}
if($saveloc) {
  $_SESSION['prevpage'] = $_SERVER['REQUEST_URI'] ?? 'index.php';
} else {
  $_SESSION['prevpage'] = 'index.php';
}
if(empty($username)) {
  header("location:login.php");
  exit();
}
$dn = $username;
require_once 'dbsetup.php';
?>
