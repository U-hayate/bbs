<?php
session_start();
require_once __DIR__ . '/conf/db_conf.php';
require_once __DIR__ . '/lib/functions.php';

$user_id = $_GET['user_id'];

try {
    $pdo  = new PDO("mysql:host={$db_server};dbname={$db_name};charset=utf8","{$db_user}","{$db_pass}");
    $pdo -> setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $pdo -> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo h($e -> getMessage());
}

if (isset($_POST['delete'])) {
    $sql = 'delete from threads where thread_id = :id';
    $stmt = $pdo -> prepare($sql);
    $stmt -> bindValue(':id', $_POST['thread_id']);
    $stmt -> execute();
}

try {
    $sql   = 'select * from users where user_id = :user_id';
    $stmt  = $pdo -> prepare($sql);
    $stmt -> bindValue(':user_id', $user_id);
    $stmt -> execute();
    $user  = $stmt -> fetch();

    $sql     = 'select * from threads where user_id = :user_id';
    $stmt    = $pdo -> prepare($sql);
    $stmt   -> bindValue(':user_id', $user_id);
    $stmt   -> execute();
    $threads = $stmt -> fetchAll();

    $sql  = 'select responses.thread_id, threads.title, users.user_id, users.name, responses.response_id, responses.response, responses.created_at ';
    $sql .= 'from responses INNER JOIN threads on responses.thread_id = threads.thread_id INNER JOIN users ';
    $sql .= 'on responses.user_id = users.user_id WHERE responses.user_id = :user_id';
    $stmt = $pdo -> prepare($sql);
    $stmt -> bindValue(':user_id', $user_id);
    $stmt -> execute();
    $responses = $stmt -> fetchAll();
} catch (PDOException $e) {
    echo h($e -> getMessage());
}

$num = 1;

require_once __DIR__ . '/lib/header.php';

?>

<div class="user">
  <div class="user_info">
    <div class="page_name">
      <h1><?php echo $user['name'] ?>さんのページ</h1>
    </div>
      <table>
      <tr>
        <th>ID</th><td><?php echo $user['user_id'] ?></td>
      </tr>
      <tr>
        <th>名前</th><td><?php echo $user['name'] ?></td>
      </tr>
      <?php if (isset($_SESSION['user']) && $_SESSION['user']['id'] === $_GET['user_id']) : ?>
        <tr>
          <form action="user_edit.php" method="post">
            <td><input type="submit" value="編集"></td>
          </form>
          <form action="delete.php" method="post">
            <input type="hidden" name="delete_item" value="user">
            <td><input type="submit" value="アカウントの削除"></td>
          </form>
        </tr>
      <?php endif ?>
    </table>
  </div>

  <div class="threads">
    <h2>過去に立てたスレ</h2>
    <table border="1" width="80%">
      <tr>
        <th>No.</th><th width="60%">タイトル</th><th>スレ主</th><th>作成日時</th>
      </tr>
      <?php foreach ($threads as $thread) : ?>
        <tr>
          <th><?php echo $num ?></th>
          <td><a href="thread.php?thread_id=<?php echo $thread['thread_id'] ?>"><?php echo $thread['title'] ?></a></td>
          <td><a href="user.php?user_id=<?php echo $thread['user_id'] ?>"><?php echo $thread['user_id'] ?></a></td>
          <td><?php echo $thread['created_at'] ?></td>
          <?php if (isset($_SESSION['user']) && $thread['user_id'] === $_SESSION['user']['id']) : ?>
            <form action="delete.php" method="post">
              <input type="hidden" name="delete_item" value="thread">
              <input type="hidden" name="delete_id" value="<?php echo $thread['thread_id'] ?>">
              <td><input type="submit" value="削除"></td>
            </form>
          <?php endif ?>
        </tr>
        <?php $num++ ?>
      <?php endforeach ?>
    </table>
  </div>

  <div class="responses">
    <h2>過去の投稿</h2>
    <ul>
      <?php foreach ($responses as $response) : ?>
        <li>
          <p><a href="user.php?user_id=<?php echo $response['user_id'] ?>"><?php echo "{$response['name']} @{$response['user_id']}" ?></a>
          <?php echo " {$response['created_at']} スレッド「" ?>
          <a href="thread.php?thread_id=<?php echo $response['thread_id'] ?>"><?php echo "{$response['title']}" ?></a>」
        </p>
          <p><?php echo nl2br($response['response']) ?></p>
          <?php if (isset($_SESSION['user']) && $user_id === $_SESSION['user']['id']) : ?>
            <form action="delete.php" method="post">
              <input type="hidden" name="delete_item" value="response">
              <input type="hidden" name="delete_id" value="<?php echo $response['response_id'] ?>">
              <input type="submit" value="削除">
            </form>
          <?php endif ?>
        </li>
      <?php endforeach ?>
    </ul>
  </div>
<?php require_once __DIR__ . '/lib/footer.php' ?>