<?php

require_once __DIR__ . '/conf/db_conf.php';
require_once __DIR__ . '/lib/functions.php';

logined();

if (isset($_SESSION['user']) && isset($_POST['logout_submit'])) {
    $_SESSION = [];
    session_destroy();
    header('location: index.php');
    exit;
}

require_once __DIR__ . '/lib/header.php';

?>

<div class="logout">
  <p>ログアウトしますか？</p>
  <form action="" method="post">
    <input type="submit" name="logout_submit" value="ログアウト">
  </form>
</div>

  <?php require_once __DIR__ . '/lib/footer.php' ?>