<?php
require_once 'auth.php';
require_once 'functions.php';
require_once 'arrays.php';
$pobo = $_POST['obo'];
$amt = $_POST['amount'];
$ownreq = $_POST['ownreq'];
$approvers = [];
$supers = [];
$superbeans = R::findAll('requser', "applevel = 'S'");
foreach($superbeans as $s) {
  $sname = $s->username;
  if($pobo != $sname || $amt <= $maxSelfapproval) {
    array_push($supers, $sname);
  }
}
if($amt > $approvallevels['A']) {
  //big req - use the superapprovers instead the chain of command
  $approvers = $supers;
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
  if($ownreq == 'true') {
    $level = min($level, $maxselfapproval);
  }
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
//found no one that qualifies
if(empty($approvers)) {
  $approvers = $supers;
}
//add the highest level, if Bill is to be included
//array_push($approvers, $uname);
$jout = json_encode($approvers);
echo $jout;
?>
