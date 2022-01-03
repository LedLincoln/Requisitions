<?php
require_once 'auth.php';
//require_once 'functions.php';
//require_once 'arrays.php';
R::addDatabase('snapshots', 'mysql:host=localhost;dbname=snapshots;charset=utf8mb4','redbean','redbean1');
R::selectDatabase('snapshots');
$gid = $_GET['ssid'] ?? '';
if($gid == '') {
  die("Snapshot ID not specified.");
}
$reqbean = R::load('req', $gid);
$ssid = $reqbean->id;
if($ssid !== 0) {
  $rid = $reqbean->origid;
  //$reqno = $reqbean->reqno;
  $created = $reqbean->created;
  $createdby = $reqbean->createdby;
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
  $approvedby = $reqbean->approvedby;
  $approvaldate = $reqbean->approvaldate;
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
  $prototype = $reqbean->prototype;
  $total = $reqbean->total;
  $log = $reqbean->log;
  $reqdate = $created;
 // if($status != "Approved") {
 //   die("Invalid snapshot found. Status is $status.");
 // }
  $title = "R$rid Snapshot as Approved";
    //$total = R::getCell("SELECT ROUND(SUM(qty * price), 2) FROM lineitem WHERE req_id = ?", [$rid]);
  $lineitems = $reqbean->xownLineitemList;
  $files = $reqbean->xownFileList;
} else {
  die("Snapshot not found.");
}
$shortdate = substr($reqdate, 0, 10);
$shortappdate = substr($approvaldate, 0, 10);
if($onbehalf == "") {
  $onbehalf = $dn;
}
if($status == "") {
  $status = "New";
}
if($priority == "") {
  $priority = "standard";
}
if($special == "") {
  $special = "Normal";
}
//$rstyle = "reqsec-closed";
?>
<!DOCTYPE html>
<html lang='en'>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="shortcut icon" href="reqs-favicon.png" type="image/x-icon">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link rel="stylesheet" href="skeleton/normalize.css">
<link rel="stylesheet" href="skeleton/skeleton.css">
<link rel="stylesheet" href="skeleton/custom.css">
<!-- <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"> -->
<script
  src="https://code.jquery.com/jquery-3.5.1.min.js"
  integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
  crossorigin="anonymous">
</script>
<?php echo "<title>$title</title>\n"; ?>
</head>
<body>
<div class='container'>
<?php echo "<div class='reqsec reqsec-closed'>\n"; ?>
<div class='row'><div class='two columns'><a class='lbut' href='listreqs.php'>List <i class='material-icons md-22' data-content='List'>list</i></a></div>
<div class='eight columns header'><em>LI-COR Biosciences</em> <sup>&reg;</sup></div>
<?php echo "<div class='two columns jr'><a class='lbut' href='dispreq.php?id=$rid'>Current <i class='material-icons md-22' data-content='Current'>visibility</i></a></div>\n"; ?>
</div>
<div class='row'>
<?php
  echo "<div class='twelve columns header'>Requisition R$rid - Snapshot as Approved";
if($hotreq) {
  echo "<br><span class='midanger text-center'>!!!!!! HOT REQ !!!!!!</span>\n";
  $mkpriority = "midanger";
} else {
  $mkpriority = "";
}
echo "</div>\n";
?>
</div>
</div>
<?php
echo "<div class='reqsec reqsec-closed'>\n";
echo "<div class='row'>\n";
echo "<div class='three columns lpad'><div class='labl'>Created by</div><div class='dval'>$createdby</div></div>\n";
echo "<div class='three columns lpad'><div class='labl'>On behalf of</div><div class='dval'>$onbehalf</div></div>\n";
echo "<div class='three columns lpad'><div class='labl'>Date</div><div class='dval'>$shortdate</div></div>\n";
echo "<div class='three columns lpad'><div class='labl'>Advance status</div><div class='dval'>$special";
if($prototype == 'true') {
  echo "<br><span class='midanger'>Prototype Parts</span>";
}
echo "</div></div>\n";
echo "</div>\n";
echo "<div class='row'>\n";
echo "<div class='three columns lpad'><div class='labl'>Status</div><div class='dval x-big'>$status</div></div>\n";
if($status != "New" and $status != "Quotation - Pending" and $status != "Quotation - Complete") {
  echo "<div class='three columns lpad'><div class='labl'>Approved by</div><div class='dval'>$approvedby</div></div>\n";
  echo "<div class='three columns lpad'><div class='labl'>Date approved</div><div class='dval'>$shortappdate</div></div>\n";
}
?>
</div>
<div class='row'>
<?php
echo "<div class='three columns lpad'><div class='labl'>Total</div><div class='dval x-big'>$total</div></div>\n";
echo "<div class='three columns lpad'><div class='labl'>Account</div><div class='dval'>$account</div></div>\n";
echo "<div class='three columns lpad'><div class='labl'>Project</div><div class='dval'>$project</div></div>\n";
echo "<div class='three columns lpad'><div class='labl'>GL Account</div><div class='dval'>$glaccount</div></div>\n";
?>
</div>
<div class='row'>
<?php echo "<div class='twelve columns lpad'><div class='labl'>Description</div><div class='dval'>$description</div></div>\n"; ?>
</div>
</div>
<?php
echo "<div class='reqsec reqsec-closed'>\n";
echo "<div class='row'>\n";
echo "<div class='three columns lpad'><div class='labl'>Suggested vendor number</div><div class='dval'>$sugvnum</div></div>\n";
echo "<div class='three columns lpad'><div class='labl'>Actual vendor number</div><div class='dval'>$actvnum</div></div>\n";
if($priority == 'specify') {
echo "<div class='three columns lpad'><div class='labl'>Priority</div><div class='dval $mkpriority'>Need by $dateneeded</div></div>\n";
} else {
echo "<div class='three columns lpad'><div class='labl'>Priority</div><div class='dval'>$priority</div></div>\n";
}
echo "</div>\n";

echo "<div class='row'>\n";
echo "<div class='three columns lpad'><div class='labl'>Suggested vendor name</div><div class='dval'>$sugvname</div></div>\n";
echo "<div class='three columns lpad'><div class='labl'>Actual vendor name</div><div class='dval'>$actvname</div></div>\n";
echo "<div class='three columns lpad'><div class='labl'>Ship via</div><div class='dval'>$shipvia</div></div>\n";
echo "</div>\n";

echo "<div class='row'>\n";
echo "<div class='three columns lpad'><div class='labl'>Suggested vendor phone</div><div class='dval'>$sugvphone</div></div>\n";
echo "<div class='three columns lpad'><div class='labl'>Actual vendor phone</div><div class='dval'>$actvphone</div></div>\n";
echo "<div class='three columns lpad'><div class='labl'>Deliver to</div><div class='dval'>$deliverto</div></div>\n";
?>
</div>
</div>

<?php echo "<div class='reqsec reqsec-closed'>\n"; ?>
<div class='spacer'>&nbsp;</div>
<div id='lineitems'>
<?php
if(count($lineitems) > 0) {
  echo "<table class='rtable'>\n";
  echo "<thead>\n";
  echo "<tr class='lineitem rowbottom'><th scope='col'>Line</th><th class='boxin' scope='col'>Qty</th><th scope='col'>Vendor #</th><th scope='col'>LI-COR #</th><th scope='col'>Del Date</th><th scope='col' class='text-right'>Unit Price</th><th scope='col' class='text-right'>Ext Price</th><th scope='col'>Type</th></tr>\n";
  echo "</thead>\n";
  echo "<tbody>\n";
  $lc = 0;
  foreach($lineitems as $l) {
    $lc++;
    $lid = $l->id;
    $lqty = $l->qty;
    $lunit = $l->unit;
    $lqu = "$lqty $lunit";
    $lprice = $l->price;
    $extprice = number_format($lqty * $lprice, 2);
    $ltype = $l->litype;
    $deldate = $l->deldate;
    $lvpn = $l->vpartno;
    $llpn = $l->lpartno;
    $ldesc = $l->description;
    $lcom = $l->comment;
    if(trim($lcom) == '') {
      $llines = 2;
    } else {
      $llines = 3;
    }
    echo "<tr class='lineitem'><th class='rowbottom rowhead' rowspan='$llines' data-line='$lc'>$lc</th><td rowspan='$llines' class='stronger rowbottom boxin'>$lqu</td><td>$lvpn</td><td>$llpn</td><td>$deldate</td><td class='text-right'>$lprice</td><td class='text-right'>$extprice</td><td>$ltype</td></tr>";
    if($llines == 3) {
      echo "<tr><td class='descline' colspan='6'>$ldesc</td></tr>\n";
      echo "<tr><td class='comline rowbottom' colspan='6'>$lcom</td></tr>\n";
    } else {
      echo "<tr><td class='descline rowbottom' colspan='6'>$ldesc</td></tr>\n";
    }
  }
  echo "<tr class='lineitem'><th>&nbsp;</th><th>&nbsp;</th><th colspan='4'>TOTAL</td><th>$total</th><th>&nbsp;</th></tr>\n";
  echo "</tbody>\n";
  echo "</table>\n";
} else {
  echo "<h5>No line items</h5>";
}
?>
</div>
</div>
<?php
echo "<div class='reqsec reqsec-closed'>\n";
echo "<div class='row'>\n";
echo "<div class='eight columns lpad'><div class='labl'>Internal comments</div><div>$comments</div></div>\n";
echo "<div class='four columns lpad'><div class='labl'>Attachments</div><div class='medweight'>\n";
echo "<ul class='plain'>\n";
foreach($files as $f) {
  $fname = $f->filename;
  $furl = $f->url;
  echo "<li><a href='$furl' target='_blank'>$fname</a></li>\n";
}
?>
</ul>
</div>
</div>
</div>
</div>
<?php echo "<div class='reqsec reqsec-closed'>\n"; ?>
<div class='labl'>Activity Log</div>
<div class='row'>
<div class='col-sm log lpad'>
<?php echo "<pre class='col-sm log'>$log</pre>\n"; ?>
</div>
</div>
</div>
<div class='spacer'>&nbsp;</div>
</body>
</html>
