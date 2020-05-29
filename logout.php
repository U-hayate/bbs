<?php

require_once __DIR__ . '/conf/db_conf.php';
require_once __DIR__ . '/lib/functions.php';

logined();

if (isset($_SESSION['user']) && isset($_POST['logout_submit'])) {
    $_SESSION = [];
    setcookie(session_name(), '', time() - 1, '/');
    session_destroy();
    header('location: index.php');
    exit;
}

require_once __DIR__ . '/lib/header.php';

?>

<div style="text-align:center;">
  <p style="margin-top:1rem;">ログアウトしますか？</p>
  <form action="" method="post">
    <input type="submit" name="logout_submit" value="ログアウト">
  </form>
</div>

<?php require_once __DIR__ . '/lib/footer.php' ?>