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

// CSRF対策のトークンの生成 & セッションに保存
$csrf_token = bin2hex(random_bytes(25));
$csrf_tokens = isset($session_values["csrf_tokens"]) ? $session_values["csrf_tokens"] : []; 
array_push($csrf_tokens, $csrf_token);
$session_values["csrf_tokens"] = $csrf_tokens;
$redis->set($redis_session_key, json_encode($session_values));

// データベースハンドラ作成 db名/ユーザー名/パスワードを独自のものに設定しているひとは書き換えてください。
$dbh = new PDO('mysql:host=mysql;dbname=2020techc_db', '2020techc_username', '2020techc_password');


// 主キーのIDが一致する1行だけ取得
$select_user_sth = $dbh->prepare('SELECT id, login_id, password, display_name FROM users WHERE id = :id LIMIT 1');
$select_user_sth->execute([
    ':id' => $session_values["login_user_id"],
]);
$login_user = $select_user_sth->fetch();

// 全行取得 id降順(新しい投稿が上にくる)
$select_sth = $dbh->prepare('
    SELECT
        bbs_entries.name AS bbs_entries__name,
        bbs_entries.body AS bbs_entries__body,
        bbs_entries.created_at AS bbs_entries__created_at,
        users.id AS users__id,
        users.login_id AS users__login_id,
        users.created_at AS users__created_at,
        users.display_name AS users__display_name
    FROM bbs_entries
    LEFT OUTER JOIN users
        ON bbs_entries.user_id = users.id
    ORDER BY bbs_entries.id DESC
 ');
$select_sth->execute();
$rows = $select_sth->fetchAll();

// 件数抽出
$sql = 'SELECT COUNT(body) AS num FROM bbs_entries';
$stmt = $dbh->query($sql);
$count = $stmt->fetchColumn();


?>
<!DOCTYPE html>
<head>
  <link rel="stylesheet" href="./css/style.css">
  <title>掲示板</title>
</head>
<body>
  <h1><span class="orange">掲</span>示板</h1>
  
  <!--
    投稿用フォーム
    読み込みページに投稿用フォームを用意してあげると利用者は便利です。
    ログインしている場合のみ表示します。
  -->
  <div class="write_container">
  <?php if(!empty($session_values["login_user_id"])): ?>
  <a href="../users/profile.php">プロフィールに進む</a>
  <form method="POST" action="./write.php" style="margin: 2em;">
    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
    <div>
      <?php if(empty($login_user["display_name"])): ?>
      名前(ログインid): <?= htmlspecialchars($login_user["login_id"]) ?>
      <?php else: ?>
      名前: <?= htmlspecialchars($login_user["display_name"]) ?>
      <?php endif; ?>
      <small><a href="/users/setting.php">設定はこちら</a></small>
    </div>
    <div>
      <textarea name="body" rows="5" cols="100" required></textarea>
    </div>
    <button type="submit">投稿</button>
  </form>
  <?php else: ?>
  <a href="/users/login.php">プロフィールに進む</a>
  投稿するには<a href="/users/login.php">ログイン</a>してください。
  <?php endif; ?>
  </div>
  <!-- 投稿用フォームここまで -->

  <div class="bbs_container">
    <h2>レス数：<?php print $count;?>レス</h2>
    <hr>
    <?php foreach ($rows as $row) : ?>
    <div style="margin: 0 2em 20px 2em;">
      <span>
        <?php if(empty($row['users__display_name'])): ?>
        <a href="../users/profile.php">名無し</a>さん
        <?php else: ?>
          <a href="../users/profile.php"><?= htmlspecialchars($row['users__display_name']) ?></a>さん
        <?php endif; ?>
        (<?= $row['users__created_at'] ?>登録) 
        の投稿
      </span>
      <span>投稿日: <?= $row['bbs_entries__created_at'] ?> ID:<?= htmlspecialchars($row['bbs_entries__name']) ?></span>
      <div>
        <?= nl2br(htmlspecialchars($row['bbs_entries__body'])) ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

</body>

