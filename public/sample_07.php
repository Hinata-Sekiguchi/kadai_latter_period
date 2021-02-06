<?
$dbh = new PDO('mysql:host=mysql;dbname=2020techc_db', '2020techc_username', '2020techc_password');

$insert_sth = $dbh->prepare("INSERT INTO hogehoge (text) VALUES (:text)");
$insert_sth->execute(array(
    ':text' => 'hello world!!!!!!!!!'
));
print('insertできました');
