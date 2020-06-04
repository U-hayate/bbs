<?php

require_once __DIR__ . '/conf/db_conf.php';
require_once __DIR__ . '/lib/functions.php';

logined();

if (isset($_POST['delete_item'])) {
    $delete_item = $_POST['delete_item'];
}

if (isset($_POST['delete_id'])) {
    $delete_id   = $_POST['delete_id'];
}


if (isset($_POST['delete_submit'])) {
    try {
        $pdo  = new PDO("mysql:host={$db_server};dbname={$db_name};charset=utf8","{$db_user}","{$db_pass}");
        $pdo -> setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $pdo -> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

        switch ($delete_item) {
            case 'user':
                $delete_tables = ['threads', 'responses', 'users'];
                foreach ($delete_tables as $table) {
                    $sql   = "delete from {$table} where user_id = :user_id";
                    $stmt  = $pdo -> prepare($sql);
                    $stmt -> bindValue(':user_id', $_SESSION['user']['id']);
                    $stmt -> execute();
                }

                $_SESSION['user'] = [];
                setcookie(session_name(), '', time() - 1, '/');
                session_destroy();
                header('location: index.php');
                exit;
                break;

            case 'thread':
                $delete_tables = ['threads', 'responses',];
                foreach ($delete_tables as $table) {
                    $sql   = "delete from {$table} where thread_id = :thread_id";
                    $stmt  = $pdo -> prepare($sql);
                    $stmt -> bindValue(':thread_id', $delete_id);
                    $stmt -> execute();
                }
                break;

            case 'response':
                $sql   = 'delete from responses where response_id = :response_id';
                $stmt  = $pdo -> prepare($sql);
                $stmt -> bindValue(':response_id', $delete_id);
                $stmt -> execute();
                break;
        }
    } catch (PDOException $e) {
        echo h($e -> getMessage());
    }
}

require_once __DIR__ . '/lib/header.php';

?>

<div style="text-align:center;">
  <?php if (!isset($_POST['delete_submit'])) : ?>
    <div class="form">
      <?php if (isset($_SESSION['user'])) : ?>
        <h2>注意</h2>
        <?php if ($delete_item === 'user') : ?>
          <p>アカウントを削除する場合、スレッドと投稿もすべて削除されます。</p>
        <?php elseif ($delete_item) : ?>
          <p>スレッドを削除する場合、そのスレッドの投稿もすべて削除されます。</p>
        <?php endif ?>
        <p>削除すると元に戻すことは出来ません。</p>
        <p>本当に削除しますか？</p>
        <form action="delete.php" method="post">
          <input type="hidden" name="delete_item" value="<?php echo $delete_item ?>">
          <input type="hidden" name="delete_id" value="<?php if (isset($delete_id)) echo $delete_id ?>">
          <input type="submit" name="delete_submit" value="削除する">
        </form>
      <?php endif ?>
    </div>
  <?php else : ?>
    <p>削除しました。</p>
  <?php endif ?>
</div>

<?php require_once __DIR__ . '/lib/footer.php' ?>