<?php
$dbh = new PDO('mysql:host=mysql;dbname=2020techc_db', '2020techc_username', '2020techc_password');
// データベースハンドラ作成

// access_logs テーブルに1行insert
// 値は何も入れなくてもよい
$insert_sth = $dbh->prepare("INSERT INTO access_logs () VALUES ()");
$insert_sth->execute();

// access_logs テーブルの行数を取得
$count_sth= $dbh->prepare('SELECT COUNT(id) from access_logs');
$count_sth->execute();
$count = $count_sth->fetchColumn();

?>
<!DOCTYPE html>
<head>
  <title>アクセスカウンタ</title>
</head>
<body>
  これは <?= $count ?> 回目のアクセスです！
</body>
