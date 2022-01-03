<?php
include_once 'auth.php';
include_once 'functions.php';
include_once 'arrays.php';
$buyers = array_keys(getRoleMembers("Buyer"));
$admins = array_keys(getRoleMembers("Admin"));
//Closed is not an option here, because there is a separate Close button
$statuses = ['New','Submitted for Approval','Waiting for Assignment','Assigned','Ordered','Quotation - Pending','Quotation - Closed'];
$prid = $_POST['reqid'];
$pns = $_POST['newstatus'];
if(!in_array($dn, $buyers) and !in_array($dn, $admins)) {
  //die messages are actually ignored at the client end; it simply doesn't work
  //if I choose to not be lazy at some point, this could/should be fixed
  die("<p>You are not authorized to change status.</p><p><a href='/'>Go Back</a></p>\n");
}
if($pns != '') {
  if(!in_array($pns, $statuses)) {
    die("<p>Invalid status provided.</p><p><a href='/'>Go Back</a></p>\n");
  }
  $reqbean = R::load('req', $prid);
  $rid = $reqbean->id;
  $logline = $reqbean->log;
  if($rid != 0) {
    $oldstatus = $reqbean->status;
    if($pns != $oldstatus) {
      $reqbean->status = $pns;
      $addtolog = "\n$now $dn changed status from $oldstatus to $pns.";
      $logline .= $addtolog;
      $reqbean->log = $logline;
      R::store($reqbean);
      R::close();
    }
    echo "dispreq.php?id=$rid";
  } else {
    die("<p>Error: Invalid Requisition specified.</p><a href='/'>Go Back</a></p>");
  }
} else {
  die("<p>Error: New status not specified.</p><p><a href='/'>Go Back</a></p>\n");
}
?>
