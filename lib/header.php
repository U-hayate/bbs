<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>掲示板</title>
  <link rel="stylesheet" href="css\milligram.min.css">
</head>
<body>
  <div class="wrapper">
    <div class="row">
      <div class="column">
        <h1><a href="index.php">掲示板</a></h1>
      </div>
      <div class="column">
        <div class="menu">
          <a class="button" href="index.php">ホーム</a>
          <?php if (isset($_SESSION['user'])) : ?>
            <a class="button" href="logout.php">ログアウト</a>
            <a class="button" href="user.php?user_id=<?php echo $_SESSION['user']['id'] ?>">マイページ</a>
          <?php else : ?>
            <a class="button" href="signup.php">新規登録</a>
            <a class="button" href="login.php">ログイン</a>
          <?php endif ?>
        </div>
      </div>
    </div>
  </div>
  <hr>
  <div class="container">