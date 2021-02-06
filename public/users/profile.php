<?php
/* セッションについて共通処理 ここから */
$redis = new Redis();
$redis->connect("redis", 6379);
$session_id_cookie_key = "session_id";
$session_id = isset($_COOKIE[$session_id_cookie_key]) ? ($_COOKIE[$session_id_cookie_key]) : null;
if ($session_id === null) {
    $session_id = bin2hex(random_bytes(25));
    setcookie($session_id_cookie_key, $session_id, 0, '/');
}
$redis_session_key = "session-" . $session_id; 
$session_values = $redis->exists($redis_session_key)
    ? json_decode($redis->get($redis_session_key), true)
    : []; 
/* ここまで */


// データベースハンドラ作成 db名/ユーザー名/パスワードを独自のものに設定しているひとは書き換えてください。
$dbh = new PDO('mysql:host=mysql;dbname=2020techc_db', '2020techc_username', '2020techc_password');


// 主キーのIDが一致する1行だけ取得
$select_user_sth = $dbh->prepare('SELECT id, login_id, password, display_name, self_intro FROM users WHERE id = :id LIMIT 1');
$select_user_sth->execute([
    ':id' => $session_values["login_user_id"],
]);
$login_user = $select_user_sth->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../bbs/css/style.css">
    <title>Profile</title>
</head>
<body>
  <h1><span class="orange">プ</span>ロフィールページ</h1>
<div class="bbs_container">
      <?php if(!empty($login_user["display_name"])): ?>
        <p>名前: <?= htmlspecialchars($login_user["display_name"]) ?></p>
      <?php else: ?>
        <p>名前:名無しさん</p>
      <?php endif; ?>

        <p>id: <?= htmlspecialchars($login_user["login_id"]) ?></p>

      <?php if(!empty($login_user["self_intro"])): ?>
        <p>自己紹介</p>
        <p><?= htmlspecialchars($login_user["self_intro"]) ?></p>
      <?php endif; ?>

      <small><a href="setting.php">設定はこちら</a></small>
      <a href="../bbs/read.php">掲示板に戻る</a>
    </div>
    <hr>
</body>
</html>