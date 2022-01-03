<?php
require_once 'auth.php';
$projno = $_POST['projnum'];
$fy = $_POST['fiscalyear'];
//sanitize these inputs better than this
if($fy > 0 and $fy < 13) {
  $projdata = R::getAssocRow('SELECT r.id, r.description, r.onbehalf, r.total FROM req r WHERE project = ? AND MONTH(created) = ?', [$projno, $fy]);
} else {
  $projdata = R::getAssocRow('SELECT r.id, r.description, r.onbehalf, r.total FROM req r WHERE project = ?', [$projno]);
}
if(empty($projdata)) {
  http_response_code(404);
  header("Message: Requisitions for project $projno not found.");
} else {
  $response['status'] = 'success';
  $response['data'] = $projdata;
  echo json_encode($response);
}
?>
