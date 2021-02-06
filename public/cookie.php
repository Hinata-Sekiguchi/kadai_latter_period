
<?php

$count = intval($_COOKIE["access_count"]);

$count++;

setcookie("access_count","1");

printf("%s回目のアクセスです。", $_COOKIE["access_count"]);

?>
