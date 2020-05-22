<?php
session_start();
require_once __DIR__ . '/conf/db_conf.php';
require_once __DIR__ . '/lib/functions.php';

try {
    $pdo  = new PDO("mysql:host={$db_server};dbname={$db_name};charset=utf8", "{$db_user}", "{$db_pass}");
    $pdo -> setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $pdo -> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

    $sql   = 'select threads.thread_id, threads.title, threads.user_id, users.name, threads.created_at ';
    $sql  .= 'from threads INNER JOIN users on threads.user_id = users.user_id order by thread_id desc';
    $stmt  = $pdo -> prepare($sql);
    $stmt -> execute();

    $threads = $stmt -> fetchAll();

} catch (PDOException $e) {
    echo h($e -> getMessage());
}

require_once __DIR__ . '/lib/header.php';

?>

<div class="index">
  <h2><a href="thread_create.php">スレを立てる</a></h2>
  <div class="threads">
    <table border="1" width="80%">
      <tr>
        <th>No.</th><th width="60%">タイトル</th><th>投稿者</th><th>作成日時</th>
      </tr>
      <?php foreach ($threads as $thread) : ?>
        <tr>
          <td><?php echo $thread['thread_id'] ?></td>
          <td><a href="thread.php?thread_id=<?php echo $thread['thread_id'] ?>"><?php echo $thread['title'] ?></a></td>
          <td><a href="user.php?user_id=<?php echo $thread['user_id'] ?>"><?php echo $thread['name'] ?></a></td>
          <td><?php echo $thread['created_at'] ?></td>
        </tr>
      <?php endforeach ?>
    </table>
  </div>
</div>
<?php require_once __DIR__ . '/lib/footer.php' ?>