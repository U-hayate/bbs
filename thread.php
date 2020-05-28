<?php

session_start();
require_once __DIR__ . '/conf/db_conf.php';
require_once __DIR__ . '/lib/validation.php';
require_once __DIR__ . '/lib/functions.php';

$thread_id = $_GET['thread_id'];

try {
    $pdo  = new PDO("mysql:host={$db_server};dbname={$db_name};charset=utf8", "{$db_user}", "{$db_pass}");
    $pdo -> setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $pdo -> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo h($e -> getMessage());
}

if (isset($_SESSION['user']) && isset($_POST['btn_submit'])) {
    $response = $_POST['response'];
    if (!isset($response) || replace($response) !== "") {
        try {
            $sql = 'insert into responses(thread_id, user_id, response) values(:thread_id, :user_id, :response)';
            $stmt = $pdo -> prepare($sql);
            $stmt -> bindValue(':thread_id', $thread_id);
            $stmt -> bindValue(':user_id', $_SESSION['user']['id']);
            $stmt -> bindValue(':response', $_POST['response']);
            $stmt -> execute();
        } catch (PDOException $e) {
            echo h($e -> getMessage());
        }
    } else {
        $error_message = '内容を入力してください。';
    }
} elseif (!isset($_SESSION['user'])) {
    $error_message = '投稿するにはログインする必要があります。';
}

try {
    $sql = 'select title from threads where thread_id = :thread_id';
    $stmt = $pdo -> prepare($sql);
    $stmt -> bindValue(':thread_id', $thread_id);
    $stmt -> execute();
    $title = $stmt -> fetch();
} catch (PDOException $e) {
    echo h($e -> getMessage());
}

try {
    $sql   = 'select responses.response_id, users.user_id, users.name, responses.response, responses.created_at ';
    $sql  .= 'from users INNER JOIN responses ';
    $sql  .= 'on users.user_id = responses.user_id ';
    $sql  .= 'where thread_id = :thread_id ';
    $sql  .= 'order by response_id desc';
    $stmt  = $pdo -> prepare($sql);
    $stmt -> bindValue(':thread_id', $thread_id);
    $stmt -> execute();

    $responses = $stmt -> fetchAll();
} catch (PDOException $e) {
    echo h($e -> getMessage());
}

$num = 1;

require_once __DIR__ . '/lib/header.php';

?>

  <div class="unlogin">
    <?php if (isset($error_message)) echo $error_message ?>
  </div>
  <div class="form">
    <h2>投稿</h2>
    <form action="" method="post">
      <textarea name="response" cols="30" rows="10"></textarea><br>
      <div class="submit"><input type="submit" name="btn_submit" value="投稿"></div>
    </form>
  </div>
  <div class="title">
    <h2><?php echo $title['title'] ?></h2><hr>
  </div>
  <?php foreach ($responses as $response) : ?>
    <div class="row">
      <div class="column">
        <p><a href="user.php?user_id=<?php echo $response['user_id'] ?>"><?php echo "{$response['name']} @{$response['user_id']}" ?></a>
        <?php echo " {$response['created_at']}" ?></p>
      </div>
    </div>
    <div class="row">
      <div class="column">
        <p><?php echo nl2br($response['response']) ?></p>
      </div>
    </div><hr>
  <?php endforeach ?>
<?php require_once __DIR__ . '/lib/footer.php' ?>