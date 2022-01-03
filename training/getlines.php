<?php
require_once 'auth.php';
function isValidDate($d) {
  $dateparts = explode('-', $d);
  return sizeof($dateparts) == 3 && checkdate($dateparts[1], $dateparts[2], $dateparts[0]);
}
//require_once 'functions.php';
$gid = $_GET['reqid'];
$mode = $_GET['mode'];
$reqbean = R::load('req', $gid);
$rid = $reqbean->id;
if($rid == 0) {
  die('{"total":0,"lines":"New Requisition"}');
}
$lineitems = $reqbean->xownLineitemList;
if(count($lineitems) == 0) {
  die('{"total":0,"lines":"No Line Items"}');
}
//rounding has to happen in the proper place
//$total = R::getCell("SELECT ROUND(SUM(qty * price), 2) FROM lineitem WHERE req_id = ?", [$rid]);
$total = R::getCell("SELECT SUM(ROUND(qty * price, 2)) FROM lineitem WHERE req_id = ?", [$rid]);
$lc = 0;
$table = "";
$table .= "<table class='rtable'>\n";
$table .= "<thead>\n";
$table .= "<tr class='lineitem rowbottom'><th scope='col'>Line</th><th class='boxin' scope='col'>Qty</th><th scope='col'>Vendor #</th><th scope='col'>LI-COR #</th><th scope='col'>Del Date</th><th scope='col' class='text-right'>Unit Price</th><th scope='col' class='text-right'>Ext Price</th><th scope='col'>Type</th>";
if($mode == 'edit') {
  $table .= "<th scope='col'>Edit</th>";
  $lastspan = 5;
} else {
  $lastspan = 4;
}
$table .= "</tr>\n";
$table .= "</thead>\n";
$table .= "<tbody>\n";
$linesreviewed = true;
foreach($lineitems as $l) {
  $lc++;
  $lid = $l->id;
  $lqty = $l->qty;
  $lunit = $l->unit;
  $lqu = "$lqty $lunit";
  $lprice = $l->price;
  $lprice3 = number_format($l->price, 3);
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
  $table .= "<tr class='lineitem'><th class='rowbottom rowhead' rowspan='$llines' data-line='$lc'>$lc</th><td rowspan='$llines' class='stronger rowbottom boxin'>$lqu</td><td>$lvpn</td><td>$llpn</td><td name='deldate'>$deldate</td><td class='text-right'>$lprice3</td><td class='text-right'>$extprice</td><td>$ltype</td>";
  if($mode == 'edit') {
    $table .= "<td rowspan='$llines' class='rowbottom boxin point actions' data-id='$lid'><i class='material-icons edline'>edit</i> <i class='material-icons dumpline'>delete</i></td>";
  }
  $table .= "</tr>\n";
  if($llines == 3) {
    $table .= "<tr><td class='descline' colspan='6'>$ldesc</td></tr>\n";
    $table .= "<tr><td class='comline rowbottom' colspan='6'>$lcom</td></tr>\n";
  } else {
    $table .= "<tr><td class='descline rowbottom' colspan='6'>$ldesc</td></tr>\n";
  }
  //check lines for completeness, i.e. req is ready to close
  if(trim($ltype) == '') {
    $linesreviewed = false;
  }
  if($ltype != "C" and !isValidDate($deldate)) {
    $linesreviewed = false;
  }
}
$table .= "<tr class='lineitem'><th>&nbsp;</th><th>&nbsp;</th><th colspan='$lastspan'>TOTAL</td><th>$total</th><th>&nbsp;</th></tr>\n";
$table .= "</tbody>\n";
$table .= "</table>\n";
$result = new stdClass();
$result->lines = $table;
$result->linesreviewed = $linesreviewed;
$result->total = $total;
echo json_encode($result, JSON_UNESCAPED_SLASHES);
//echo json_encode($result);
?>
