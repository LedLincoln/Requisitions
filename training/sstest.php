<pre>
<?php
$reqid = 35;
require_once 'dbsetup.php';
R::addDatabase('snapshots', 'mysql:host=localhost;dbname=snapshots;charset=utf8mb4','redbean','redbean1');
$rb = R::load('req', $reqid);
//$reqnum = "R" . $reqid;
echo $rb->sugvname . "\n";
$ss = R::duplicate($rb);
//$ss->reqnum = $reqnum;
$ss->origid = $reqid;
R::selectDatabase('snapshots');
$newid = R::store($ss);
echo "Snapshot created with ID $newid\n";
?>
</pre>
