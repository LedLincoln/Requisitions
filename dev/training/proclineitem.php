<?php
$now = date('Y-m-d H:i:s');
$today = date('Y-m-d');
require_once 'auth.php';
require_once 'functions.php';
$plid = $_POST['lineid'];
$linebean = R::load('lineitem', $plid);
$lid = $linebean->id;
if($lid == 0) {
  //error_log("Not an existing line item.");
  $prid = $_POST['reqid'];
  $reqbean = R::load('req', $prid);
  $reqid = $reqbean->id;
  if($reqid !== 0) {
    $reqbean->updated = $now;
    $reqbean->updatedby = $dn;
    $linebean = R::dispense('lineitem');
    $newreq = false;
  } else {
    //this is a bad approach, because it blanks other fields already filled in on req form
    $newreq = true;
    $reqbean->created = $now;
    $reqbean->createdby = $dn;
    $reqbean->updated = $now;
    $reqbean->updatedby = $dn;
    $logline = "$now $dn created new requisition.";
    $reqbean->log = $logline;
  }
} else {
  $reqbean = $linebean->req;
  $reqid = $reqbean->id;
  $newreq = false;
}
//we have a new or existing lineitem
$qty = $_POST['liqty'] ?? '';
$unit = $_POST['liunit'] ?? '';
$price = $_POST['liprice'] ?? '';
$vpartno = $_POST['livendpn'] ?? '';
$lpartno = $_POST['lilicorpn'] ?? '';
$ltype = $_POST['litype'] ?? '';
$ldeldate = $_POST['deldate'] ?? '';
$desc = trim($_POST['lidesc']) ?? '';
$comment = trim($_POST['licomment'] ?? '');

$linebean->litype = $ltype;
$linebean->description = $desc;
$linebean->comment = $comment;
//comment lineitems only have type, description, and comment
if($ltype != "C") {
  if($qty != '') {
    $linebean->qty = $qty;
  }
  $linebean->unit = $unit;
  if($price != '') {
    $linebean->price = $price;
  }
  $linebean->vpartno = $vpartno;
  $linebean->lpartno = $lpartno;
  if(isValidDate($ldeldate)) {
    $linebean->deldate = $ldeldate;
  } else {
    $linebean->deldate = null;
  }
} else {
  $linebean->qty = null;
  $linebean->unit = null;
  $linebean->price = null;
  $linebean->vpartno = null;
  $linebean->lpartno = null;
  $linebean->deldate = null;
}
$reqbean->xownLineitemList[] = $linebean;
$reqid = R::store($reqbean);
R::close();
if($newreq) {
  //reload req with its new req#
  $redirect = "edreq.php?id=$reqid";
} else {
  //just reload line item table
  $redirect = "";
}
echo $redirect;
