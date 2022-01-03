<?php
require_once 'auth.php';
$gid = $_GET['reqid'];
$reqbean = R::load('req', $gid);
$rid = $reqbean->id;
if($rid == 0) {
  die('{"total":0,"lines":""}');
}
$files = $reqbean->ownFileList;
if(count($files) == 0) {
  die('{"total":0,"lines":"No Attachments"}');
}
//rounding has to happen in the proper place
//$total = R::getCell("SELECT ROUND(SUM(qty * price), 2) FROM lineitem WHERE req_id = ?", [$rid]);
$lc = 0;
$output = "<ul class='plain'>\n";
foreach($files as $f) {
  $lc++;
  $fid = $f->id;
  $fname = $f->filename;
  $furl = $f->url;
  $fuploaddate = $f->uploaded;
  $fuploadby = $f->uploadedby;
  $output .= "<li><a href='$furl' target='_blank'>$fname</a></li>\n";
}
$output .= "</ul>\n";
$result = new stdClass();
$result->files = $output;
echo json_encode($result, JSON_UNESCAPED_SLASHES);
?>
