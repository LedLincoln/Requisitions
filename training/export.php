<?php
$sd = $_POST['startdate'];
$ed = $_POST['enddate'];
$title = "Requisitions Report $sd to $ed";
require_once 'dbsetup.php';
//$reqs = R::getAll("select id, created, approvaldate, orderdate, status, createdby, description, buyercode, datediff(approvaldate, created) as 'Orig to App', datediff(orderdate, approvaldate) as 'App to Order', project, glaccount, total from req WHERE created >= ? AND created <= ?", [$sd, $ed]);
//exclude canceled reqs
$reqs = R::getAll("select id, created, approvaldate, orderdate, status, createdby, description, buyercode, datediff(approvaldate, created) as 'Orig to App', datediff(orderdate, approvaldate) as 'App to Order', project, glaccount, total from req WHERE created >= ? AND created <= ? AND status != 'Canceled'", [$sd, $ed]);
//the following produce blanks for the dates - why?
//a regular mysql query produces the dates
//$reqs = R::getAll("SELECT id, DATE(created), DATE(approvaldate), DATE(orderdate), status, createdby, description AS 'Desc', buyercode, DATEDIFF(approvaldate, created) AS 'Orig to App', DATEDIFF(orderdate, approvaldate) AS 'App to Order', project, glaccount, total FROM req WHERE created >= ? AND created <= ?", [$sd, $ed]);
//$reqs = R::getAll("SELECT id, LEFT(created,10), LEFT(approvaldate,10), LEFT(orderdate,10), status, createdby, description AS 'Desc', buyercode, DATEDIFF(approvaldate, created) AS 'Orig to App', DATEDIFF(orderdate, approvaldate) AS 'App to Order', project, glaccount, total FROM req WHERE created >= ? AND created <= ?", [$sd, $ed]);
R::close();
if(count($reqs) < 1) {
  die("<p>No requisitions created within the specified dates ($sd to $ed).</p><p><a href='index.php'>Go Back</a></p>");
}
$filename = "Requisitions_Report_" . date('Y-m-d') . ".csv";
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Content-Type: text/csv");
$out = fopen("php://output", 'w');
fputcsv($out, [$title]);
$headings = array('Req No','Orig Date','Appr Date','Ord Date','Status','Requester','Description','Buyer','Orig to Appr','Appr to Ord','Project','GL-Acct','Total');
fputcsv($out, $headings);
foreach($reqs as $r) {
  //$rline = array($r['id'], $r['created'], $r['approvaldate'], $r['orderdate'], $r['status'], $r['createdby'], $r['Desc'], $r['buyercode'], $r['Orig to App'], $r['App to Order'], $r['project'], $r['glaccount'], $r['total']);
  $rline = array($r['id'], substr($r['created'], 0 , 10), substr($r['approvaldate'], 0, 10), substr($r['orderdate'], 0, 10), $r['status'], $r['createdby'], $r['description'], $r['buyercode'], $r['Orig to App'], $r['App to Order'], $r['project'], $r['glaccount'], $r['total']);
  fputcsv($out, $rline);
}
  fclose($out);
?>
