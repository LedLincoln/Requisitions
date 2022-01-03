<?php
require_once 'auth.php';
//require_once 'functions.php';
//require_once 'arrays.php';
//require_once 'notices.php';
//$pid = $_POST['id'] ?? '';
$pid = $_POST['origid'] ?? '';
$reqbean = R::load('req', $pid);
$reqid = $reqbean->id;

if($reqid !== 0) {
  $now = date('Y-m-d H:i:s');
  $newcopy = R::duplicate($reqbean);
  $prevstatus = "New";
  $newcopy->created = $now;
  $newcopy->createdby = $dn;
  $newcopy->updated = $now;
  $newcopy->updatedby = $dn;
  $newcopy->status = "New";
  //remove certain fields;
  //maybe this could be done with an array of column names, but...
  $newcopy->actvnum = null;
  $newcopy->actvname = null;
  $newcopy->actvphone = null;
  $newcopy->priority = null;
  $newcopy->dateneeded = null;
  $newcopy->erppotype = null;
  $newcopy->orderedby = null;
  $newcopy->ordermethod = null;
  $newcopy->vendorcontact = null;
  $newcopy->confnumber = null;
  $newcopy->payment = null;
  $newcopy->ccard = null;
  $newcopy->buyercode = null;
  $newcopy->xfer = null;
  $newcopy->hotreq = null;
  $newcopy->approvedby = null;
  $newcopy->approvaldate = null;
  $newcopy->assignedto = null;
  $newcopy->orderdate = null;
  $newcopy->senttoerp = null;
  $newcopy->submitted = null;
  //do not copy attachments
  $newcopy->ownFileList = [];
  $logline = "$now $dn created new requisition as copy of R$reqid.";
  $newcopy->log = $logline;
  try {
    $newid = R::store($newcopy);
  } catch(Exception $e) {
    die($e->getMessage());
  }
  //echo "New req id: $newid\n";
  header("Location:edreq.php?id=$newid");
} else {
  die("Cannot find existing req with id $pid.\n");
}
exit();
?>
