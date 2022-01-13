<?php
require_once 'auth.php';
//require_once 'functions.php';
require_once 'functions.php';
require_once 'arrays.php';
$created = $createdby = $approvedby = $approvaldate = $hotreq = $status = $dept = $description = $special = $onbehalf = $account = $glaccount = $project = $sugvnum = $sugvname = $sugvphone = $actvnum = $actvname = $actvphone = $priority = $dateneeded = $shipvia = $deliverto = $delinstr = $comments = $erppotype = $buyercode = $assignedto = $orderedby = $orderdate = $ordermethod = $vendorcontact = $confnumber = $payment = $ccard = $xfer = $log = $pcheck = $reqdate = '';
$gtype = $_GET['type'] ?? '';
if(array_key_exists('id', $_GET)) {
  $gid = $_GET['id'];
  $reqbean = R::load('req', $gid);
  $rid = $reqbean->id;
  if($rid !== 0) {
    //$reqno = $reqbean->reqno;
    $created = $reqbean->created;
    $createdby = $reqbean->createdby;
    $approvedby = $reqbean->approvedby;
    $approvaldate = $reqbean->approvaldate;
    $hotreq = $reqbean->hotreq;
    $status = $reqbean->status;
    $dept = $reqbean->dept;
    $description = $reqbean->description;
    $special = $reqbean->special;
    $onbehalf = $reqbean->onbehalf;
    $account = $reqbean->account;
    $glaccount = $reqbean->glaccount;
    $project = $reqbean->project;
    $sugvnum = $reqbean->sugvnum;
    $sugvname = $reqbean->sugvname;
    $sugvphone = $reqbean->sugvphone;
    $actvnum = $reqbean->actvnum;
    $actvname = $reqbean->actvname;
    $actvphone = $reqbean->actvphone;
    $priority = $reqbean->priority;
    $dateneeded = $reqbean->dateneeded;
    $shipvia = $reqbean->shipvia;
    $deliverto = $reqbean->deliverto;
    $delinstr = $reqbean->delinstr;
    $comments = $reqbean->comments;
    $erppotype = $reqbean->erppotype;
    $buyercode = $reqbean->buyercode;
    $assignedto = $reqbean->assignedto;
    $orderedby = $reqbean->orderedby;
    $orderdate = $reqbean->orderdate;
    $ordermethod = $reqbean->ordermethod;
    $vendorcontact = $reqbean->vendorcontact;
    $confnumber = $reqbean->confnumber;
    $payment = $reqbean->payment;
    $ccard = $reqbean->ccard;
    $xfer = $reqbean->xfer;
    $log = $reqbean->log;
    if($reqbean->prototype == 'true') {
      $pcheck = 'checked';
    } else {
      $pcheck = '';
    }
    $reqdate = $created;
    if($status == "New Quotation" or $status == "Quotation - Pending" or $status == "Quotation - Complete") {
      $title = "Quotation Q" . $rid;
    } else {
      $title = "Requisition R" . $rid;
    }
    $total = R::getCell("SELECT ROUND(SUM(qty * price), 2) FROM lineitem WHERE req_id = ?", [$rid]);
    if($total == '') { $total = 0; }
    $approvers = getApprovers($onbehalf, $total);
    if($status == '' or $status == 'New' or $status == 'New - Not Submitted' or $status == 'Submitted for Approval' or $status == "New Quotation" or $status == "Quotation - Pending" or $status == "Quotation - Complete") {
      if($dn != $createdby and $dn != $onbehalf and !in_array($dn, $approvers) and !in_array_any(["Buyer","Admin"], $userroles)) {
        //not permitted to edit this req
        header("Location:dispreq.php?id=$rid");
      }
    } else {
      //any other status cannot be edited by ordinary submitter
      if(!in_array($dn, $approvers) and !in_array_any(["Buyer","Admin"], $userroles)) {
        //not permitted to edit this req
        header("Location:dispreq.php?id=$rid");
      }
    }
  } else {
    $reqdate = $now;
    $title = "New Requisition";
    $status = "New";
    $total = 0;
  }
} else {
  $rid = '0';
  $reqdate = $now;
  $title = "New Requisition";
  $status = "New";
  $total = 0;
}
if($rid == '0') {
}
$shortdate = substr($reqdate, 0, 10);
$shortappdate = substr($approvaldate, 0, 10);
if($onbehalf == "") {
  $onbehalf = $dn;
}
if($status == "") {
  $status = "New";
}
//this will be changed below if this is a quotation
$reqno = "R$rid";
//yes, I know you can combine these cases, but I want all enumerated neatly here
switch($status) {
  case "":
  case "New":
  if($gtype == "rfq") {
    $title = "New Request for Quote";
    $reqno = "Q$rid";
    $rstyle = "reqsec-quote";
  } else {
    $title = "New Requisition";
    $reqno = "R$rid";
    $rstyle = "reqsec-new";
  }
  break;
  case "New Quotation":
  $gtype = "rfq";
  $title = "New Request for Quote";
  $reqno = "Q$rid";
  $rstyle = "reqsec-quote";
  break;
  case "Submitted for Approval":
  $rstyle = "reqsec-new";
  break;
  case "Approved":
  //not a real status; won't happen
  $rstyle = "reqsec-general";
  break;
  case "Waiting for Assignment":
  $rstyle = "reqsec-general";
  break;
  case "Assigned":
  $rstyle = "reqsec-general";
  break;
  case "Ordered":
  $rstyle = "reqsec-general";
  break;
  case "Closed":
  $rstyle = "reqsec-closed";
  break;
  case "Quotation - Pending":
  $rstyle = "reqsec-quote";
  $reqno = "Q$rid";
  break;
  case "Quotation - Complete":
  $rstyle = "reqsec-quote";
  $reqno = "Q$rid";
  break;
  case "Canceled":  //may not be used
  $rstyle = "reqsec-closed";
  break;
  default:
  $rstyle = "reqsec-new";
  break;
}
if($priority == "") {
  $priority = "Standard";
}
if($special == "") {
  $special = "Normal";
}

$ubean = R::findOne('requser', 'username = ?', [$onbehalf]);
$deptnum = $ubean->deptnum;
//this will be redone via ajax if onbehalf is changed
if($deptnum !== "") {
  $qry = "SELECT acctnum, description FROM account WHERE validfor LIKE ? ORDER BY description";
  $dept4 = substr($deptnum, -4);
  $deptwc = "%" . $dept4 . "%";
  $accts = R::getAll($qry, [$deptwc]);
}
?>
<!DOCTYPE html>
<html lang='en'><head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link rel="shortcut icon" href="reqs-favicon.png" type="image/x-icon">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<link rel="stylesheet" href="css/custom.css">
<link rel="stylesheet" href="css/jqui.css">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<script
  src="https://code.jquery.com/jquery-3.5.1.min.js"
  integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
  crossorigin="anonymous">
</script>
<script src = "https://code.jquery.com/ui/1.12.0/jquery-ui.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<?php echo "<title>$title</title>\n"; ?>
</head>
<body>
<div class='space-48'></div>
<?php
if($gtype != "rfq") {
  include_once 'inc_reqheader';
} else {
  include_once 'inc_quoteheader';
}
echo "<div class='container reqsec $rstyle'>\n";
?>
<div class='row'>
<div class='col-sm'>
<h4 class='text-center'><em>LI-COR Biosciences&reg;</em></h4>
</div>
</div>
<?php
//echo "<h3 class='text-center'>$status Purchase Order/Requisition</h3>\n";
echo "<h3 class='text-center'>$title</h3>\n";
if($hotreq) { echo "<h3 class='text-danger text-center'>!!!!!! HOT REQ !!!!!!</h3>\n"; }
?>
</div>
<form id='reqform' method='POST' action='procreq.php' novalidate>
<!-- <form id='reqform' method='POST' action='disppost.php'> -->
<input type='hidden' name='action' id='action'>
<input type='hidden' name='submitto' id='submitto'>
<input type='hidden' name='status' id='status'>
<input type='hidden' name='xfer' id='xfer'>
<?php
echo "<input type='hidden' name='id' id='id' value='$rid'>\n";
echo "<input type='hidden' name='dept' id='dept' value='$dept4'>\n";
echo "<input type='hidden' name='glaccount' id='glaccount' value='$glaccount'>\n";
echo "<input type='hidden' name='sugvname' id='sugvname' value='$sugvname'>\n";
if($status != "New" and $status != "New Quotation" and $status != "Submitted for Approval") {
  echo "<input type='hidden' name='sugvnum' value='$sugvnum'>\n";
  echo "<input type='hidden' name='sugvphone' value='$sugvphone'>\n";
}
echo "<input type='hidden' name='actvname' id='actvname' value='$actvname'>\n";
echo "<div class='container reqsec $rstyle'>\n";
echo "<div class='row'>\n";
echo "<div class='col-sm-2 text-right labl'>Req number:</div><div class='col-sm dval text-danger bigger'>$reqno</div>\n";
echo "</div>\n";
echo "<div class='row'>\n";
echo "<div class='col-sm-2 text-right labl'>Status:</div><div class='col-sm-3 dval' id='dispstatus'>$status</div>\n";
if($approvedby != '') {
  echo "<div class='col-sm-1 text-right labl'>Approved by:</div><div class='col-sm-2 dval'>$approvedby</div>\n";
  echo "<div class='col-sm-2 text-right labl'>Date approved:</div><div class='col-sm-2 dval'>$shortappdate</div>\n";
}
echo "</div>\n";
echo "<div class='row'>\n";
echo "<div class='col-sm-2 text-right labl'>Total:</div><div class='col-sm dval'>$<span id='total'>$total</span></div>\n";
echo "</div>\n";
echo "<div class='row'>\n";
echo "<div class='col-sm-2 text-right labl'>Short description:</div><div class='col-sm'><input class='custom-control compact wide-90 val-always' type='text' name='description' value='$description' required></div>\n";
echo "</div>\n";
echo "<div class='row'>\n";
if($gtype != "rfq") {
echo "<div class='col-sm-2 text-right labl'>Advance status:</div><div class='col-sm-6 dval'>\n";
foreach($specials as $s) {
  if($s[0] == $special) {
    echo "<input type='radio' name='special' value='$s[0]' checked>&nbsp;$s[1] \n";
  } else {
    echo "<input type='radio' name='special' value='$s[0]'>&nbsp;$s[1] \n";
  }
}
echo "</div>\n";
}
echo "<div class='col-sm-4 dval'><input type='checkbox' name='prototype' value='true' $pcheck> Prototype parts</div>\n";
?>
</div>
</div>

<?php
echo "<div class='container reqsec $rstyle'>\n";
echo "<div class='row'>\n";
echo "<div class='col-sm-2 text-right labl'>Created by:</div><div class='col-sm-3 dval'>$createdby</div>\n";
echo "<div class='col-sm-1 text-right labl'>On behalf of:</div><div class='col-sm-3 dval'>\n";
echo "<select class='custom-control compact' name='onbehalf' id='onbehalf'>\n";
foreach($users as $u) {
  $uname = $u->username;
  if($uname == $onbehalf) {
  echo "<option value='$uname' selected>$uname</option>\n";
  } else {
  echo "<option value='$uname'>$uname</option>\n";
  }
}
echo "</select>\n";
echo "</div>\n";
echo "<div class='col-sm-1 text-right labl'>Date:</div><div class='col-sm-2 dval'>$shortdate</div>\n";
echo "</div>\n";
echo "<div class='row'>\n";
echo "<div class='col-sm-2 text-right labl'>Project #:</div><div class='col-sm-3 dval'>\n";
echo "<select class='compact' name='project'>\n";
echo "<option value=''>-select-</option>\n";
foreach($projects as $p) {
  $pnum = $p->projnum;
  $pnam = substr($p->projname, 0, 28);
  if($pnum == $project) {
    echo "<option value='$pnum' selected>$pnum $pnam</option>\n";
  } else {
    echo "<option value='$pnum'>$pnum $pnam</option>\n";
  }
}
echo "</select>\n";
echo "</div>\n";
if($gtype != "rfq") {
  echo "<div class='col-sm-1 text-right labl'>Account:</div><div class='col-sm-3 dval'>\n";
  echo "<select class='compact val-submit' name='account'>\n";
  echo "<option value=''>-select-</option>\n";
  foreach($accts as $a) {
    $anum = $a['acctnum'];
    $adesc = substr($a['description'], 0, 28);
    if($anum == $account) {
      echo "<option value='$anum' selected>$anum $adesc</option>\n";
    } else {
      echo "<option value='$anum'>$anum $adesc</option>\n";
    }
  }
  echo "</select>\n";
  echo "</div>\n";
  echo "<div class='col-sm-1 text-right labl'>GL account:</div><div class='col-sm-2 dval'><span id='dispglaccount'>$glaccount</span></div>\n";
  echo "</div>\n";
}
echo "</div>\n";
if($status == "New" or $status == "New Quotation" or $status == "Submitted for Approval") {
  include_once 'inc_vendor_new';
} else {
  include_once 'inc_vendor_approved';
}
?>
<!--
<div class='row'>
<div class='col-sm-2 text-right labl'>Special delivery instructions:</div><div class='col-sm dval'>
<?php echo "<input type='text' class='compact narrow-80' name='delinstr' value='$delinstr'>\n"; ?>
</div>
</div> -->
</div>

<?php echo "<div class='container reqsec $rstyle'>\n"; ?>
<div class='row'>
<div class='col-sm-2'><h5>Line Items</h5>
<div class='lierror'>Requisition must have at least one line item.</div>
</div>
<?php
if($rid == 0) {
  echo "<div class='col-sm-10'><button type='button' class='btn btn-sm btn-primary' id='firstsave'>Click here to add line items</button></div>\n";
} else {
  echo "<div class='col-sm-10'><button type='button' class='btn btn-sm btn-primary' name='newline'>Add Line Item</button> \n";
  echo "<span class='alert-warning' id='lineiteminfo'>Buyer must review line items before closing requisition.</span></div></div>\n";
  echo "<div id='lineitems'></div>\n";
  echo "<div class='row'>\n";
  echo "<div class='col-sm-10 offset-sm-2'><button type='button' class='btn btn-sm btn-primary' name='newline'>Add Line Item</button>";
  if($status == "New" || $status == "New Quotation" || $status == "Quotation - Pending") {
    echo " <a class='labl' href='import.php?id=$rid'>Import</a>\n";
  }
}
?>
</div>
<div class='spacer'></div>
</div>
</div>

<?php echo "<div class='container reqsec $rstyle'>\n"; ?>
<div class='row'>
<div class='col-sm-8 labl'>Internal comments:<br>
<?php echo "<textarea name='comments' class='compact medweight' rows='3' cols='84'>$comments</textarea>\n"; ?>
</div>
<?php
if($rid != 0) echo "<div class='col-sm-4' id='dropzone'><span class='text-right dval point oi oi-paperclip' id='paperclip'></span> Attachments <span class='popchar' id='atthelp'>?</span><br><span class='medweight' id='attachments'>\n";
?>
</span></div>
</div>
</div>
<?php
switch($status) {
//if($status <> "New") {
  case "":
  case "New":
  case "New - Not Submitted":
  case "Submitted for Approval":
  case "New Quotation":
  case "Waiting for Assignment":
  case "Quotation - Pending":
  case "Quotation - Complete":
  break;
  default:
    include_once 'inc_supplychain';
}
?>
</form>
<input name='reqfile' id='reqfile' type='file' accept='.pdf,.docx,.xlsx,.pptx,.jpg,.jpeg,.gif,.tiff,.png,.txt,.zip' style='display:none'>
<?php echo "<div class='container reqsec $rstyle'>\n"; ?>
<div class='labl'>Activity Log</div>
<div class='row'>
<?php echo "<pre class='col-sm log'>$log</pre>\n"; ?>
</div>
</div>
<div class='spacer'></div>
<?php
if($status == '' or $status == 'New' or $status == 'Submitted for Approval' or $status == 'Waiting for Assignment' or $status == 'Quotation - Pending') {
  include 'inc_line_user';
} else {
  include 'inc_line_buyer';
}
?>
<div id='confirmdlg' class='modal fade' role='dialog'>
<div class='modal-dialog' role='document'>
<div class='modal-content'>
<div class='modal-header'>
<h4 class='modal-title' id='confirmhead'>Delete Line</h4>
<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
</div>
<div class='modal-body'>
<p id='confirmprompt'>Are you sure you want to delete this line item?</p>
<div id='xferdiv'>
<input type='radio' name='autoxfer' value='auto'> Transfer to Dataflo<br>
<input type='radio' name='autoxfer' value='none'> Manual/No Transfer
</div>
</div>
<div class='modal-footer'>
<button type='button' class='btn btn-default' id='cancelbtn' data-dismiss='modal'>Cancel</button>
<button class='btn btn-primary' id='confirmbtn'>Yes</button>
</div> <!-- modal-footer -->
</div> <!-- modal-content -->
</div> <!-- modal-dialog -->
</div> <!-- confirmdlg -->

<div id='statusdlg' class='modal fade' role='dialog'>
<div class='modal-dialog' role='document'>
<div class='modal-content'>
<div class='modal-header'>
<h4 class='modal-title' id='statushead'>Change Status</h4>
<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
</div>
<div class='modal-body'>
<p id='statusprompt'>Select New Status</p>
<select id='newstatus'>
<option value=''>-select-</option>
<option value='New'>New</option>
<option value='Submitted for Approval'>Submitted for Approval</option>
<option value='Quotation - Pending'>Quotation - Pending</option>
<option value='Quotation - Closed'>Quotation - Closed</option>
<option value='Waiting for Assignment'>Waiting for Assignment</option>
<option value='Assigned'>Assigned</option>
<option value='Ordered'>Ordered</option>
</select>
</div>
<div class='modal-footer'>
<button type='button' class='btn btn-default' data-dismiss='modal'>Cancel</button>
<button class='btn btn-primary' id='statusconfirm'>Change</button>
</div> <!-- modal-footer -->
</div> <!-- modal-content -->
</div> <!-- modal-dialog -->
</div> <!-- statusdlg -->

<div id='selsubmit' class='modal fade' role='dialog'>
<div class='modal-dialog' role='document'>
<div class='modal-content'>
<div class='modal-header'>
<h4 class='modal-title'>Submit Requisition</h4>
<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
</div>
<div class='modal-body'>
<p>Submit to:<br>
<select id='approverlist'>
</select></p>
<p id='selfappprompt'>You are permitted to approve this requisition yourself. Do you wish to submit it to someone else?</p>
</div>
<div class='modal-footer'>
<button class='btn btn-danger' id='dlgapprove'>Approve</button>
<button class='btn btn-primary' id='dlgsubmit'>Submit</button>
<button type='button' class='btn btn-default' data-dismiss='modal'>Cancel</button>
</div> <!-- modal-footer -->
</div> <!-- modal-content -->
</div> <!-- modal-dialog -->
</div> <!-- selsubmit -->

<div id='assigndlg' class='modal fade' role='dialog'>
<div class='modal-dialog' role='document'>
<div class='modal-content'>
<div class='modal-header'>
<h4 class='modal-title'>Assign Requisition</h4>
<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
</div>
<div class='modal-body'>
<p>Assign to:<br>
<select id='assignee'>
<option value=''>-select-</option>
<?php
foreach($buyers as $b) {
  $bn = $b->username;
  if($bn == $assignedto) {
    echo "<option value='$bn' selected>$bn</option>\n";
  } else {
    echo "<option value='$bn'>$bn</option>\n";
  }
}
?>
</select></p>
</div>
<div class='modal-footer'>
<button class='btn btn-primary' id='assignconfirm'>Assign</button>
<button type='button' class='btn btn-default' data-dismiss='modal'>Cancel</button>
</div> <!-- modal-footer -->
</div> <!-- modal-content -->
</div> <!-- modal-dialog -->
</div> <!-- assigndlg -->
<script>
<?php
echo "var currentuser = '$dn';\n";
echo "var createdby = '$createdby';\n";
echo "var currentusermax = $currentusermax;\n";
echo "var reqid = $rid;\n";
echo "var status = '$status';\n";
//echo "var amt = $total;\n";
echo "var selfapplimit = $maxselfapproval;\n";
if($gtype == 'rfq') {
  echo "var reqtype = 'rfq';\n";
} else {
  echo "var reqtype = '';\n";
}
?>
</script>
<!-- required for IE11 support -->
<script src="https://polyfill.io/v3/polyfill.min.js?features=Array.prototype.find,Promise,Object.assign"></script>
<script src="https://unpkg.com/@popperjs/core@2"></script>
<script src="https://unpkg.com/tippy.js@6"></script>
<script src='js/edreq8.js'></script>
</body>
</html>
