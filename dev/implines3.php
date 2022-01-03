<?php
$okextlist = ['csv'];
//$uploaddir = 'files/';

require_once 'auth.php';
require_once 'functions.php';
//require_once 'arrays.php';

$uploaderrors = array(
  0 => 'Success.',
  1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
  2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
  3 => 'The uploaded file was only partially uploaded.',
  4 => 'No file was uploaded.',
  6 => 'Missing a temporary folder.',
  7 => 'Failed to write file to disk.',
  8 => 'A PHP extension stopped the file upload.',
);
//$result = array();
$result['hello'] = "Hello World!";
//php permits multiple files in the POST, but we only accept one called 'reqfile'
$filestuff = $_FILES['itemsfile'];
if(!isset($filestuff)) {
  //die("Invalid Post.");
  $result['error'] = "Invalid Post.";
}
if($filestuff['error'] != 0) {
  //die("Upload error " . $filestuff['error'] . ".");
  $phperror = $uploaderrors[$filestuff['error']];
  //$result['error'] = "Upload error " . $filestuff['error'] . ".";
  $result['error'] = $phperror;
}

$preqid = $_POST['reqid'];
//$preqid = '12';
//$reqbean = R::load('req', $preqid);
//$reqid = $reqbean->id;
if($preqid == 0) {
  //die("Cannot find associated requisition.");
  $result['error'] = "Cannot find associated requisition.";
}

$filepath = $filestuff['name'];
$filename = basename($filepath);

$filetmppath = $filestuff['tmp_name'];
$filesize = $filestuff['size'];
$filetype = $filestuff['type'];
$lastdot = strrpos($filename, ".");
if(!$lastdot) {
  //die("No extension on file.");
  $result['error'] = "No extension on file.";
}
$fileextension = strtolower(substr($filepath, $lastdot + 1));

if(!in_array($fileextension, $okextlist)) {
  //die("File type '$fileextension' not permitted.");
  $result['error'] = "File type '$fileextension' not permitted.";
}
//$result['itemsfile'] = $filetmppath;
$result['itemsfile'] = file_get_contents($filetmppath);
//$nameonly = substr($filepath, 0, $lastdot);
echo json_encode($result);
?>
