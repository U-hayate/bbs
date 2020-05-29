<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>掲示板</title>
  <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Roboto:300,300italic,700,700italic">
  <link rel="stylesheet" href="//cdn.rawgit.com/necolas/normalize.css/master/normalize.css">
  <link rel="stylesheet" href="//cdn.rawgit.com/milligram/milligram/master/dist/milligram.min.css">
</head>
<body>
  <div class="wrapper">
    <div class="row" style="width:100%;">
      <div class="column" style="margin-left:1rem;">
        <h1 style="margin:0;"><a href="index.php">掲示板</a></h1>
      </div>
      <div class="column" style="text-align:right;margin:auto 1rem auto 0;">
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
  <hr style="margin:0;">
  <div class="container">