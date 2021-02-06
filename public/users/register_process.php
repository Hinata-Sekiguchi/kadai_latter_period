<?php
// 必須項目のログインIDとパスワードのどちらでも欠けていたら
// 何も処理せず会員登録フォームに戻す (リダイレクト)
if (empty($_POST['login_id']) || empty($_POST['password'])) {
  header("HTTP/1.1 302 Found");
  header("Location: ./register.php");
  return;
}

// データベースハンドラ作成
$dbh = new PDO('mysql:host=mysql;dbname=2020techc_db', '2020techc_username', '2020techc_password');

// 既に同じログインIDで登録されていないか確認 既に登録されていたらエラー
$select_sth = $dbh->prepare('SELECT login_id, password FROM users WHERE login_id = :login_id LIMIT 1');
$select_sth->execute([
    ':login_id' => $_POST['login_id'],
]);
if (!empty($select_sth->fetch())) {
    print('そのログインIDは既に登録されています。<a href="./register.php">戻る</a>');
    return;
}

// 会員テーブル users に1行insert
// SQLインジェクションを防ぐためにプレースホルダを使う
$insert_sth = $dbh->prepare("INSERT INTO users (login_id, password) VALUES (:login_id, :password)");
$insert_sth->execute([
    ':login_id' => $_POST['login_id'],
    // パスワードは暗号化して保存
    ':password' => password_hash($_POST['password'], PASSWORD_BCRYPT),
]);

// 登録完了したら会員登録完了画面に飛ばす
header("HTTP/1.1 302 Found");
header("Location: ./register_finish.php");
return;
?>