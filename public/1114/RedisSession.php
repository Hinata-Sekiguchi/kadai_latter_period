<?php
$session_id_cookie_key = "session_id";

// まだセッションが開始されていない(セッションIDが発行されていない)場合のみ，新規にセッションIDの発行を行います。
$session_id = isset($_COOKIE[$session_id_cookie_key]) ? ($_COOKIE[$session_id_cookie_key]) : null;
if ($session_id === null) {
// セッションIDの発行 (生成)
// 安全なランダム値を取得するために random_bytes() 関数を使います。バイト文字列を生成します。
// セッションIDは文字列であってほしいので bin2hex() でhexな(16進数な)文字列に変換しています。
	$session_id = bin2hex(random_bytes(25));
                            
// セッションIDはブラウザに送り，保持しておいてもらう必要があります。
// cookieにすると勝手に保存してくれます。また，毎回のHTTPリクエストで送ってくれるので便利ですね。
	$setcookie($session_id_cookie_key, $session_id);
 }
