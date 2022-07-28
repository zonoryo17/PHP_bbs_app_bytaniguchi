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

$db = dbconnect();
//メッセージの投稿　投稿ボタンが押された時の処理
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_SESSION['id'];
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);
    //postsテーブルのmessageカラムとmember_idカラムにデータを投稿していく処理
    $stmt = $db->prepare('INSERT INTO posts (message, member_id) values(?, ?)');
    if (!$stmt) {
        die($db->error);
    }

    $stmt->bind_param('si', $message, $id);
    $success = $stmt->execute();
    if (!$success) {
        die($db->error);
    }
    //post（フォーム）の情報をクリアするために自分のページを再読み込みする
    header('Location: index.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ひとこと掲示板</title>

    <link rel="stylesheet" href="style.css" />
</head>

<body>
    <div id="wrap">
        <div id="head">
            <h1>ひとこと掲示板</h1>
        </div>
        <div id="content">
            <div style="text-align: right"><a href="logout.php">ログアウト</a></div>
            <form action="" method="post">
                <dl>
                    <dt><?php echo h($name); ?>さん、メッセージをどうぞ</dt>
                    <dd>
                        <textarea name="message" cols="50" rows="5"></textarea>
                    </dd>
                </dl>
                <div>
                    <p>
                        <input type="submit" value="投稿する" />
                    </p>
                </div>
            </form>

            <?php $stmt = $db->prepare("SELECT p.id, p.member_id, p.message, p.created, m.name, m.picture FROM posts p, members m WHERE m.id=p.member_id ORDER BY id DESC");
            if (!$stmt) {
                die($db->error);
            }
            $success = $stmt->execute();
            if (!$success) {
                die($db->error);
            }
            //idなどの各パラメータをbind_resultで取得する
            $stmt->bind_result($id, $member_id, $message, $created, $name, $picture);
            while ($stmt->fetch()) :
            ?>
                <div class="msg">
                    <!-- 投稿された投稿一覧に画像を表示させる -->
                    <?php if ($picture) : ?>
                        <img src="member_picture/<?php echo h($picture); ?>" width="48" height="48" alt="" />
                    <?php endif; ?>
                    <p><?php echo h($message); ?><span class="name">（<?php echo h($name); ?>）</span></p>
                    <p class="day"><a href="view.php?id=<?php echo h($id); ?>"><?php echo h($created); ?></a>
                        <?php if ($_SESSION['id'] === $member_id) : ?>
                            [<a href="delete.php?id=<?php echo h($id); ?>" style="color: #F33;">削除</a>]
                        <?php endif; ?>
                    </p>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>

</html>