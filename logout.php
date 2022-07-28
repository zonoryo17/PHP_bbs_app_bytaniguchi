<?php
session_start();

//ログアウト機能の実装　セッション情報を削除してあげる
unset($_SESSION['id']);
unset($_SESSION['name']);

header('Location: login.php');
exit();
