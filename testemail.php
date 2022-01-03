<pre>
<?php
include 'dbsetup.php';
include 'functions.php';
$uname = "Larry Dietrich";
print_r(getEmail($uname));
print getEmail("Jon Hawthorne")['Jon Hawthorne'];
?>
</pre>
