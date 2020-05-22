<?php
date_default_timezone_set('Asia/Tokyo');
require_once __DIR__ . '/conf/db_conf.php';
require_once __DIR__ . '/lib/functions.php';
require_once __DIR__ . '/lib/validation.php';

logined();

if (isset($_SESSION['user']) && isset($_POST['thread_submit'])) {
    $title = $_POST['title'];
    $error_message = title_validation($title);
    if ($error_message === 'none') {
        try {
            $pdo  = new PDO("mysql:host={$db_server};dbname={$db_name};charset=utf8", "{$db_user}", "{$db_pass}");
            $pdo -> setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $pdo -> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

            $sql   = 'select * from threads where title=:title';
            $stmt  = $pdo -> prepare($sql);
            $stmt -> bindValue(':title', $_POST['title']);
            $stmt -> execute();

            if (empty($stmt -> fetchAll())) {
                $sql = 'insert into threads(user_id, title) values(:user_id, :title)';
                $stmt = $pdo -> prepare($sql);
                $stmt -> bindValue(':user_id', $_SESSION['user']['id']);
                $stmt -> bindValue(':title', $title);
                $stmt -> execute();

                header('location: index.php');
                exit;
            } else {
                $error_message = "「{$title}」はすでに存在しています。";
            }

        } catch (PDOException $e) {
            echo h($e -> getMessage());
        }
    }
}

require_once __DIR__ . '/lib/header.php';

?>

<div class="thread_create">
  <p>50字以内で入力してください。</p>
  <form action="" method="post">
    <p>
      <label for="">スレッド名：</label>
      <input type="text" name="title">
    </p>
    <input type="submit" name="thread_submit" value="確定">
  </form>
  <div class="error">
    <p><?php if (isset($error_message)) echo "※{$error_message}" ?></p>
  </div>
</div>

<?php require_once __DIR__ . '/lib/footer.php' ?>