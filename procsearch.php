<?php
require_once 'auth.php';
require_once 'functions.php';
require_once 'arrays.php';
$qarray = [];
$dtype = $_POST['selectdate'] ?? '';
if($dtype != '') {
  $sd = $_POST['startdate'] ?? '';
  if(isValidDate($sd)) {
    array_push($qarray, "DATE($dtype) >= '$sd'");
  }
  $ed = $_POST['enddate'] ?? '';
  if(isValidDate($ed)) {
    array_push($qarray, "DATE($dtype) <= '$ed'");
  }
}
$pfield = $_POST['personopt'] ?? '';
$person = $_POST['findperson'] ?? '';
if($pfield != '' and $person != '') {
  array_push($qarray, "$pfield = '$person'");
}
$project = $_POST['findproject'] ?? '';
if($project != '') {
  array_push($qarray, "project = '$project'");
}
$txt = trim($_POST['searchtext'] ?? '');
if($txt != '') {
  $startqry = "SELECT id, created, status, description FROM (";
  $nameqry = "SELECT id, created, status, description FROM req WHERE MATCH(createdby, updatedby, onbehalf, approvedby, assignedto, orderedby) AGAINST(:txt)";
  $dataqry = "SELECT id, created, status, description FROM req WHERE MATCH(glaccount, project, sugvnum, sugvname, actvnum, actvname) AGAINST(:txt)";
  $textqry = "SELECT id, created, status, description FROM req WHERE MATCH(description, comments, vendorcontact) AGAINST(:txt)";
  $liqry = "SELECT r.id, r.created, r.status, r.description FROM req r JOIN lineitem li ON r.id = li.req_id\nWHERE MATCH(li.vpartno, li.lpartno, li.description, li.comment) AGAINST(:txt)";
  $qry = "$startqry(\n$nameqry)\nUNION ($dataqry)\nUNION ($textqry)\nUNION ($liqry)) AS rtab\n";
  if(count($qarray) > 0) {
    $pdclause = "WHERE rtab.id IN\n(SELECT id FROM req WHERE " . implode($qarray, " AND ") . " GROUP BY id)\n";
  } else {
    $pdclause = '';
  }
  $fullqry = $qry . $pdclause . "ORDER BY id DESC";
} else {
  $fullqry = "SELECT id, created, status, description FROM req WHERE " . implode($qarray, " AND ") . " GROUP BY id ORDER BY id DESC";
}
//echo "<pre>$fullqry</pre>";
$results = R::getAll($fullqry, [':txt' => $txt]);
if(count($results) == 0) {
  echo "No Items Found";
  exit();
}
?>
<table>
<thead>
<tr><th>#</th><th>Created</th><th>Status</th><th>Description</th></tr>
</thead>
<tbody>
<?php
$cr = count($results);
$suf = '';
if($cr != 1) { $suf = "s"; }
echo "<p>$cr result$suf found</p>\n";
foreach($results as $r) {
  $rid = $r['id'];
  $cd = $r['created'];
  $st = $r['status'];
  $dcd = substr($cd, 0, strpos($cd, ' '));
  //$dcd = date('l jS \of F Y h:i:s A', strtotime($cd));
  $rd = $r['description'];
  $anch = "<a href='dispreq.php?id=$rid' target='_BLANK'>";
  echo "<tr><td>$anch$rid</a></td><td>$anch$dcd</a></td><td>$anch$st</a></td><td>$anch$rd</a></td></tr></a>\n";
}
?>
</tbody>
</table>
