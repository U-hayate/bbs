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

require_once __DIR__ . '/lib/header.php';

?>


<h1><?php echo $user['name'] ?>さんのページ</h1>
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

<h2>過去に立てたスレ</h2>
<?php if (empty($threads)) : ?>
  <p>無し</p>
<?php else : ?>
  <table>
    <tr>
      <th>No.</th><th>タイトル</th><th>スレ主</th><th>作成日時</th><th></th>
    </tr>
    <?php foreach ($threads as $thread) : ?>
      <tr>
        <th><?php echo $thread['thread_id'] ?></th>
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
    <?php endforeach ?>
  </table>
<?php endif ?>


<h2>過去の投稿</h2>
<?php if (empty($responses)) : ?>
  <p>無し</p>
<?php else : ?>
  <?php foreach ($responses as $response) : ?>
    <div class="row">
      <div class="column">
        <p><a href="user.php?user_id=<?php echo $response['user_id'] ?>"><?php echo "{$response['name']} @{$response['user_id']}" ?></a>
        <?php echo " {$response['created_at']} " ?>
        <a href="thread.php?thread_id=<?php echo $response['thread_id'] ?>"><?php echo $response['title'] ?></a></p>
      </div>
    </div>
    <div class="row">
      <div class="column">
          <p><?php echo nl2br($response['response']) ?></p>
      </div>
    </div>
    <?php if (isset($_SESSION['user']) && $_SESSION['user'] === $user_id) : ?>
      <div class="row">
        <div class="column">
          <form action="delete.php" method="post">
            <input type="hidden" name="delete_item" value="response">
            <input type="hidden" name="delete_id" value="<?php echo $response['response_id'] ?>">
            <input type="submit" value="削除">
          </form>
        </div>
      </div>
    <?php endif ?><hr>
  <?php endforeach ?>
<?php endif ?>

<?php require_once __DIR__ . '/lib/footer.php' ?>