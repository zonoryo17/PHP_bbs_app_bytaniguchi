<?php
session_start();
require('library.php');

//ログインしているかどうかの確認
if (isset($_SESSION['id']) && isset($_SESSION['name'])) {
  $name = $_SESSION['name'];
} else {
  header('Location: login.php');
  exit();
}

$post_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
if (!$post_id) {
  header('Location: index.php');
  exit();
}

$db = dbconnect();

//delete機能の実装 id=? and member_id=?とすることで自分の投稿データのみを削除できるようにする
$stmt = $db->prepare('DELETE FROM posts WHERE id = ? AND member_id=? LIMIT 1');
if (!$stmt) {
  die($db->error);
}
$stmt->bind_param('ii', $post_i, $id);
$success = $stmt->execute();
if (!$success) {
  die($db->error);
}




header('Location: index.php');
exit();
