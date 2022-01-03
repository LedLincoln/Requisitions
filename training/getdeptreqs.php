<?php
require_once 'auth.php';
$deptno = $_POST['deptnum'];
$projdata = R::getAssocRow('SELECT id, description, onbehalf, total FROM req WHERE RIGHT(glaccount, 4) = ?', [$deptno]);
if(empty($projdata)) {
  http_response_code(404);
  header("Message: Requisitions for department $deptno not found.");
} else {
  $response['status'] = 'success';
  $response['data'] = $projdata;
  echo json_encode($response);
}
?>
