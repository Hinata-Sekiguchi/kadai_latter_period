<?php
$dbh = new PDO('mysql:host=mysql;dbname=2020techc_db', '2020techc_username', '2020techc_password');

// テーブルに1行insertする
$insert_sth = $dbh->prepare("INSERT INTO access_logs1017 () VALUES ()");
$insert_sth->execute();

// 行数からカウント回数の読み取り
$count_sth= $dbh->prepare('SELECT COUNT(id) from access_logs1017');
$count_sth->execute();
$count = $count_sth->fetchColumn();

?>
<!DOCTYPE html>
<head>
  <title>アクセスカウンター1017</title>
</head>
<body>
アクセス回数：<?= $count ?> 回アクセスしました(・ω・)
</body>
