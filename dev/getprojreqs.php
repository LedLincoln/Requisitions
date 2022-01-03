<?php
require_once 'auth.php';
$projno = $_POST['projnum'];
$projdata = R::getAssocRow('SELECT r.id, r.description, r.onbehalf, r.total FROM req r WHERE project = ?', [$projno]);
if(empty($projdata)) {
  http_response_code(404);
  header("Message: Requisitions for project $projno not found.");
} else {
  $response['status'] = 'success';
  $response['data'] = $projdata;
  echo json_encode($response);
}
?>
