<?php
require_once 'auth.php';
require_once 'functions8.php';
require_once 'arrays.php';
$pobo = $_POST['obo'];
$amt = $_POST['amount'];
$approvers = [];
$abean = R::findOne('requser', 'username = ?', [$pobo]);
$uid = $abean->id;
if($uid == 0) {
  die("Invalid User Specified");
}
$uname = $abean->username;
$mgr = $abean->manager;
//chain of command
//CEO should list either no one or himself/herself as manager
//this script could break if user's manager is someone below him/her,
//causing an infinite loop
//it currently does not guard against this
while($mgr != '' and $mgr != $uname) {
  $lletter = $abean->applevel;
  $level = $approvallevels[$lletter];
  if($pobo == $uname) {
    $level = min($level, $maxselfapproval);
  }
  if($amt <= $level) {
    array_push($approvers, $uname);
  }
  $abean = R::findOne('requser', 'username = ?', [$mgr]);
  $uname = $abean->username;
  $mgr = $abean->manager;
}
//this will add top level (CEO)
array_push($approvers, $uname);
$ca = count($approvers);
//array_push($approvers, "$ca-Wilma Flintstone");
if($ca <= 1) {
  //we need optiona other than just CEO, so return everyone who can
  //approve this amount, even if not in chain of command
  //get appropriate letter keys from $approvallevels
  $bigenough = array_filter($approvallevels, function($n) {
    global $amt;
    return $n >= $amt;
  });
  $bigkeys = implode(array_keys($bigenough), "', '");
  //exclude self approval if applicable
  if($amt > $maxselfapproval) {
    $qry = "SELECT username FROM (SELECT username FROM requser WHERE applevel IN ('" . $bigkeys . "')) AS alevel WHERE username <> '" . $pobo . "' ORDER BY username";
  } else {
    $qry = "SELECT username FROM requser WHERE applevel IN ('" . $bigkeys . "') ORDER BY username";
  }
  $approvers = R::getCol($qry);
  $ca = count($approvers);
  //array_push($approvers, "$ca-Bugs Bunny");
}
if($ca < 1) {
  //no approvers found, so this req will require board approval
  $approvers = ["Contact Finance for Board Approval"];
}
$jout = json_encode($approvers);
echo $jout;
?>
