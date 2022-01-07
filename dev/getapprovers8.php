<?php
require_once 'auth.php';
require_once 'functions8.php';
require_once 'arrays.php';
$pobo = $_POST['obo'];
$amt = $_POST['amount'];
$ownreq = $_POST['ownreq'];
$approvers = [];
/*
$supers = [];
$superbeans = R::findAll('requser', "applevel = 'S'");
foreach($superbeans as $s) {
  $sname = $s->username;
  if($pobo != $sname || $amt <= $maxSelfapproval) {
    array_push($supers, $sname);
  }
}
*/
//if($amt > $approvallevels['A']) {
  //big req - use the superapprovers instead the chain of command
  //$approvers = $supers;
//} else {
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
//add the highest level, if CEO is to be included
//remark next line if CEO does not want to approve reqs
array_push($approvers, $uname);
//must have more than just CEO, so return everyone who can
//approve this amount, even if not in chain of command
//array_push($approvers, "Fred Flintstone");
if(count($approvers) < 2) {
  $bigenough = array_filter($approvallevels, function($n) {
    global $amt;
    return $n >= $amt;
  });
  $bigkeys = implode(array_keys($bigenough), "', '");
  $qry = "SELECT username FROM requser WHERE applevel IN ('" . $bigkeys . "') ORDER BY username";
  $approvers = R::getCol($qry);
  //array_push($approvers, "Bugs Bunny");
}
$jout = json_encode($approvers);
echo $jout;
?>
