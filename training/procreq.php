<?php
$hotdays = 3;
require_once 'auth.php';
require_once 'functions.php';
require_once 'arrays.php';
require_once 'notices.php';
/*
if(!in_array_any($edroles, $userroles)) {
  R::close();
  die("<p>You are not authorized to edit requests.</p><p><a href='listreq.php'>Go Back</a></p>\n");
}
*/
//required fields
//$reqfields = ['csq','scientist','needdate','pm'];
//$linefields = ['prodname','description','location','notebook','pkgsize','storage','dessicant','shiplab','shipcond','labeltext','notes'];
//$pid = $_POST['id'];
$pid = $_POST['id'] ?? '';
$reqbean = R::load('req', $pid);
$reqid = $reqbean->id;

if($reqid !== 0) {
  $reqbean->updated = $now;
  $reqbean->updatedby = $dn;
  $createdby = $reqbean->createdby;
  $prevstatus = $reqbean->status;
  if($prevstatus == null) {
    $prevstatus = "New";
  }
  $prevorderdate = $reqbean->orderdate;
  //$hotreq = $reqbean->hotreq;
  $logline = $reqbean->log;
  $total = R::getCell("SELECT ROUND(SUM(qty * price), 2) FROM lineitem WHERE req_id = ?", [$reqid]);
} else {
  //this actually happens when the first line item is added
  //no, now we require the user to save the req first
  $reqbean->created = $now;
  $reqbean->createdby = $dn;
  $reqbean->updated = $now;
  $reqbean->updatedby = $dn;
  $reqbean->status = "New";
  $prevstatus = "New";
  $logline = "$now $dn created new requisition.";
}

$action = $_POST['action'] ?? '';
$submitto = $_POST['submitto'] ?? '';
$dept = $_POST['dept'] ?? '';
$description = trim($_POST['description'] ?? '');
$special = $_POST['special'] ?? '';
$obo = $_POST['onbehalf'] ?? '';
$account = $_POST['account'] ?? '';
$glaccount = $_POST['glaccount'] ?? '';
$project = $_POST['project'] ?? '';
$sugvnum = $_POST['sugvnum'] ?? '';
$sugvname = $_POST['sugvname'] ?? '';
$sugvphone = $_POST['sugvphone'] ?? '';
$actvnum = $_POST['actvnum'] ?? '';
$actvname = $_POST['actvname'] ?? '';
$actvphone = $_POST['actvphone'] ?? '';
$priority = $_POST['priority'] ?? '';
$dateneeded = $_POST['dateneeded'] ?? '';
$shipvia = $_POST['shipvia'] ?? '';
$deliverto = $_POST['deliverto'] ?? '';
$delinstr = $_POST['delinstr'] ?? '';
$comments = trim($_POST['comments'] ?? '');
$erppotype = $_POST['erppotype'] ?? '';
$assignedto = $_POST['assignedto'] ?? '';
$orderedby = $_POST['orderedby'] ?? '';
$orderdate = $_POST['orderdate'] ?? '';
$ordermethod = $_POST['ordermethod'] ?? '';
$vendorcontact = $_POST['vendorcontact'] ?? '';
$confnumber = $_POST['confnumber'] ?? '';
$payment = $_POST['payment'] ?? '';
$ccard = $_POST['ccard'] ?? '';
$erppotype = $_POST['erppotype'] ?? '';
$buyercode = $_POST['buyercode'] ?? '';
$prototype = $_POST['prototype'] ?? '';
$xfer = $_POST['xfer'] ?? '';

$reqbean->dept = $dept;
$reqbean->description = $description;
$reqbean->special = $special;
$reqbean->onbehalf = $obo;
$reqbean->account = $account;
$reqbean->glaccount = $glaccount;
$reqbean->project = $project;
$reqbean->sugvnum = $sugvnum;
$reqbean->sugvname = $sugvname;
$reqbean->sugvphone = $sugvphone;
$reqbean->actvnum = $actvnum;
$reqbean->actvname = $actvname;
$reqbean->actvphone = $actvphone;
$reqbean->priority = $priority;
if($dateneeded != "") {$reqbean->dateneeded = $dateneeded;}
$reqbean->shipvia = $shipvia;
$reqbean->deliverto = $deliverto;
$reqbean->delinstr = $delinstr;
$reqbean->comments = $comments;
$reqbean->erppotype = $erppotype;
//this is set by assign.php
//$reqbean->assignedto = $assignedto;
$reqbean->orderedby = $orderedby;
if($orderdate != "") {$reqbean->orderdate = $orderdate;}
$reqbean->ordermethod = $ordermethod;
$reqbean->vendorcontact = $vendorcontact;
$reqbean->confnumber = $confnumber;
$reqbean->payment = $payment;
$reqbean->ccard = $ccard;
$reqbean->erppotype = $erppotype;
$reqbean->buyercode = $buyercode;
$reqbean->prototype = $prototype;
$reqbean->xfer = $xfer;
$reqbean->total = $total;
//retransfer if user closes req again and asks for xfer
if($status = "Closed" && $xfer == 'auto') {
  $reqbean->senttoerp = null;
}

$addtolog = "";
switch($action) {
  case "submit":
    $reqbean->submitted = $now;
    $notifyaction = "submit";
    $newstatus = "Submitted for Approval";
    $addtolog = "\n$now $dn submitted to $submitto for approval.";
    break;
  case "approve":
    //error_log($currentusermax . " " . $total);
    if($currentusermax >= $total and $total <= $maxselfapproval) {
      $reqbean->approvedby = $dn;
      $reqbean->approvaldate = $now;
      if($assignedto == '') {
        $newstatus = "Waiting for Assignment";
      } else {
        $newstatus = "Assigned";
      }
      $notifyaction = "approve";
      $addtolog = "\n$now $dn approved requisition for $$total.";
    } else {
      //user is not authorized
      $notifyaction = "";
      $addtolog .= "\n$now $dn is not authorized to approve this requisition.";
    }
    break;
  case "savequote":
  case "quoteandedit":
    //$reqbean->status = "Quotation - Pending";
    $newstatus = "New Quotation";
    $notifyaction = "";
    break;
  case "quote":
    //$reqbean->status = "Quotation - Pending";
    $newstatus = "Quotation - Pending";
    $notifyaction = "quote";
    $addtolog = "\n$now $dn submitted for quotation.";
    break;
  case "rtnquote":
    //$reqbean->status = "Quotation - Complete";
    $newstatus = "Quotation - Complete";
    $notifyaction = "rtnquote";
    $addtolog = "\n$now $dn returned quotation to $obo.";
    break;
  case "close":
    //$reqbean->status = "Closed";
    $newstatus = "Closed";
    $notifyaction = "";
    $addtolog = "\n$now $dn closed requisition.";
    break;
  case "cancel":
    $newstatus = "Canceled";
    $notifyaction = "";
    $addtolog = "\n$now $dn canceled requisition.";
    break;
  default:
    //includes "save"
    $notifyaction = "";
    $newstatus = $prevstatus;
}
//if previous status is anything but Closed, a valid order date will move it to Ordered
//we don't do this
/*
if($prevstatus != "Closed" and $newstatus != "Closed") {
  if(isValidDate($orderdate)) {
    $newstatus = "Ordered";
  }
}
*/
//if orderdate has been added or changed, log it
if($prevorderdate != $orderdate) {
  if($orderdate == '') {
    $od = "(blank)";
  } else {
    $od = $orderdate;
  }
  $logline .= "\n$now $dn set order date to $od";
  if($orderedby != '') {
    $logline .= ", ordered by $orderedby.";
  } else {
    $logline .= ".";
  }
}
//determine whether this is hot, as of now - this could change if req is re-submitted
//this can change for any req other than Ordered or Closed
$hotornot = '';
if($newstatus != "Ordered" and $newstatus != "Closed") {
  switch($priority) {
    case "Expedited":
    case "Nextday":
      $hotornot = 'true';
      break;
    case "specify":
      //analyze specified date
      //might use jddayofweek here if we want to take weekends into account
      //actually DateTime::diff has some weekday options that I don't see documentntaion for
      $submitdate = new DateTime($today);
      $needdate = new DateTime($dateneeded);
      if($submitdate->diff($needdate)->days <= $hotdays) {
        $hotornot = 'true';
      }
      break;
    default:
      //not hot should be blank if false
      //$hotornot = false;
  }
}
//unless it's for prototype parts
if($prototype == 'true') {
  $hotornot = 'true';
}
$reqbean->hotreq = $hotornot;
$reqbean->status = $newstatus;
$logline .= $addtolog;
$reqbean->log = $logline;

if($notifyaction == "ERROR") {
    error_log("Invalid Form");
    header("Location:$errredirect");
} else {
  //we will save
  try {
    $reqid = R::store($reqbean);
    //sendNotice($action);
  } catch(Exception $e) {
    die($e->getMessage());
  }
  /*
if($action == 'approve') {
  //create snapshot of req as approved
  $ss = R::duplicate($reqbean);
  $ss->origid = $reqid;
  R::addDatabase('snapshots', 'mysql:host=localhost;dbname=snapshots;charset=utf8mb4','redbean','redbean1');
  R::selectDatabase('snapshots');
  $ssid = R::store($ss);
  R::selectDatabase('default');
}
  */
  R::close();
  if($notifyaction !== "") {
    sendNotice($notifyaction . $hotornot, $prototype);
  }
  if($action == 'saveandedit' or $action == 'quoteandedit') {
    $redirect = "edreq.php?id=$reqid";
  } else {
    $redirect = "dispreq.php?id=$reqid";
    //or $redirect = "listreqs.php";
  }
  header("Location:$redirect");
}
?>
