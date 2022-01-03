<?php
$okextlist = ['csv'];
$linefields = ['qty','unit','price','vpartno','lpartno','description','comment'];
require_once 'auth.php';
//require_once 'functions.php';
//require_once 'dbsetup.php';
$units = R::getCol('SELECT code FROM unit ORDER BY unitname');
//make them all lower case
$lowerunits = array_map('strtolower', $units);

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
//$result['hello'] = "Hello World!";
//php permits multiple files in the POST, but we only accept one called 'itemsfile'
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

$preqid = $_POST['reqid'] ?? '';
if($preqid == '') {
  $result['error'] = "Req ID not provided.";
} else {
  $reqbean = R::load('req', $preqid);
  $reqid = $reqbean->id;
  if($reqid == 0) {
    $result['error'] = "Requisition $preqid not found.";
  }
}
$desc = $reqbean->description;
$result['hello'] = $desc;
$result['reqid'] = $reqid;
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
$row = 1;
$resultstr = "<table border = '1'>";
if(($handle = fopen($filetmppath, "r")) !== false) {
  while(($dline = fgetcsv($handle, 1000, ",")) !== false) {
    //$num = count($dline);
    $num = count($linefields);
    $row++;
    $linebean = R::dispense('lineitem');
    $linebean->req_id = $reqid;
    $resultstr .= "<tr>";
    for($c = 0; $c < $num; $c++) {
      $fieldname = $linefields[$c];
      $fval = $dline[$c];
      //some validation
      switch($fieldname) {
      case "qty":
        if(!is_numeric($fval)) {
          $fval = 0;
        }
        break;
      case "unit":
        $uindex = array_search(strtolower($fval), $lowerunits);
        if($uindex === false) {
          $fval = "EA";
        } else {
          $fval = $units[$uindex];
        }
        break;
      case "price":
        if(!is_numeric($fval)) {
          $fval = 0;
        }
        break;
      default:
        //validation not needed; redbeanphp should take care of sanitizing input
        //limit length, just in case
        $fval = substr($fval, 0, 500);
      }
      $resultstr .= "<td>" . $fval . "</td>\n";
      $linebean[$fieldname] = $fval;
    }
    $resultstr .= "</tr>\n";
    $lineid = R::store($linebean);
  }
  $resultstr .= "</table>\n";
  $result['itemsfile'] = $resultstr;
} else {
  $result['error'] = "Cannot find $filetmppath.";
}
R::close();
echo json_encode($result);
?>
