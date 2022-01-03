<pre>
<?php
require_once 'auth.php';
R::addDatabase('snapshots', 'mysql:host=localhost;dbname=snapshots;charset=utf8mb4','redbean','redbean1');
R::selectDatabase('snapshots');
//$ss = R::findAll('req');
$ss = R::find('req', 'origid = ?', [26]);
foreach($ss as $s) {
  echo $s->id . " " . $s->description . "\n";
}
R::selectDatabase('default');
?>
</pre>
