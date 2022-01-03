<?php
require_once 'auth.php';
require_once 'functions.php';
require_once 'arrays.php';
$pobo = $_POST['obo'];
$amt = $_POST['amount'];
$approvers = [];
if($amt > $approvallevels['A']) {
  //big req - get all the superapprovers instead the chain of command
  $supers = R::findAll('requser', "applevel = 'S'");
  foreach($supers as $s) {
    $sname = $s->username;
    array_push($approvers, $sname);
  }
} else {
  $abean = R::findOne('requser', 'username = ?', [$pobo]);
  $uid = $abean->id;
  if($uid == 0) {
    die("Invalid User Specified");
  }
  $uname = $abean->username;
  $mgr = $abean->manager;
  $lletter = $abean->applevel;
  $level = $approvallevels[$lletter];
  while($uname != $mgr and $mgr != '') {
    if($amt <= $level) {
      array_push($approvers, $uname);
    }
    $uname = $mgr;
    $abean = R::findOne('requser', 'username = ?', [$uname]);
    $mgr = $abean->manager;
    $lletter = $abean->applevel;
    $level = $approvallevels[$lletter];
  }
}
//add the highest level, if Bill is to be included
//array_push($approvers, $uname);
$jout = json_encode($approvers);
echo $jout;
?>
