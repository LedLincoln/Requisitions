<pre>
<?php
session_name("Reqstraining");
//session_name("Reqs");
session_start();
echo '$_SESSION:' . "\n";
print_r($_SESSION);
echo '$_SERVER:' . "\n";
print_r($_SERVER);
?>
</pre>
