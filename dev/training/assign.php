<?php
include_once 'auth.php';
include_once 'functions.php';
include_once 'arrays.php';
$buyers = array_keys(getRoleMembers("Buyer"));
$admins = array_keys(getRoleMembers("Admin"));
$prid = $_POST['reqid'];
$pat = $_POST['assignto'];
if(!in_array($dn, $buyers) and !in_array($dn, $admins)) {
  //die messages are actually ignored at the client end; it simply doesn't work
  //if I choose to not be lazy at some point, this could/should be fixed
  die("<p>You are not authorized to assign requisitions.</p><p><a href='/'>Go Back</a></p>\n");
}
if($pat != '') {
  if(!in_array($pat, $buyers)) {
    die("<p>A requisition can only be assigned to a buyer.</p><p><a href='/'>Go Back</a></p>\n");
  }
  $reqbean = R::load('req', $prid);
  $rid = $reqbean->id;
  $logline = $reqbean->log;
  if($rid != 0) {
    //a req could be assigned to a buyer without being approved; especially a quote
    //if so, status is not changed
    $curstatus = $reqbean->status;
    if($curstatus == "Waiting for Assignment") {
      $reqbean->status = "Assigned";
    }
    $reqbean->assignedto = $pat;
    $addtolog = "\n$now $dn assigned to $pat.";
    $logline .= $addtolog;
    $reqbean->log = $logline;
    R::store($reqbean);
    R::close();
    //sendNotice('assigntrue', $proto);
    echo "dispreq.php?id=$rid";
  } else {
    die("<p>Error: Invalid Requisition specified.</p><a href='/'>Go Back</a></p>");
  }
} else {
  die("<p>Error: Assignee not specified.</p><p><a href='/'>Go Back</a></p>\n");
}
?>
