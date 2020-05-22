<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>掲示板</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="header">
  <div class="title">
    <h1><a href="index.php">掲示板</a></h1>
  </div>
  <div class="menu">
    <ul>
      <li><a href="index.php">ホーム</a></li>
      <?php if (isset($_SESSION['user'])) : ?>
        <li><a href="logout.php">ログアウト</a></li>
        <li><a href="user.php?user_id=<?php echo $_SESSION['user']['id'] ?>">マイページ</a></li>
      <?php else : ?>
        <li><a href="signup.php">新規登録</a></li>
        <li><a href="login.php">ログイン</a></li>
      <?php endif ?>
    </ul>
  </div>
</div><hr>