<?php
$okextlist = ['pdf','docx','xlsx','pptx','jpg','jpeg','gif','tiff','png','txt','zip','msg'];
$uploaddir = 'files/';

require_once 'auth.php';
require_once 'functions.php';
require_once 'arrays.php';
function safefilename($v) {
  //optional
  //$v = strtolower($v);
  $v = preg_replace("/[^a-zA-Z0-9]+/", "-", $v);
  $v = trim($v, "-");
  if(strlen($v) == 0) {
    $v = "unnamed";
  }
  if(mb_strlen($v) > 48) {
    $v = mb_substr($v, 0, 48);
  }
  return $v;
}

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
//php permits multiple files in the POST, but we only accept one called 'reqfile'
$filestuff = $_FILES['reqfile'];
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
$reqbean = R::load('req', $preqid);
$reqid = $reqbean->id;
if($reqid == 0) {
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
$nameonly = substr($filepath, 0, $lastdot);
$safenameonly = safefilename($nameonly);
$cleanfilename = $safenameonly . "." . $fileextension;
$uploaddest = $uploaddir . "R$reqid-" . $cleanfilename;
if(file_exists($uploaddest)) {
  //die("File with that name has already been uploaded.");
  $result['error'] = "File with that name has already been uploaded.";
}
if($result['error'] == "") {
  if(move_uploaded_file($filestuff['tmp_name'], $uploaddest)) {
    $filebean = R::dispense('file');
    $filebean->filename = $cleanfilename;
    $filebean->uploaddate = $now;
    $filebean->url = $uploaddest;
    $filebean->size = $filestuff['size'];
    $reqbean->xownFileList[] = $filebean;
    R::store($reqbean);
    //echo "File is valid and was successfully uploaded to $uploaddest.";
    $result['success'] = "File is valid and was successfully uploaded to $uploaddest.";
  } else {
    //die("Unable to place file in filesystem.");
    $result['error'] = "Unable to place file in filesystem.";
  }
}
echo json_encode($result);
?>
