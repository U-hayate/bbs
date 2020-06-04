<?php

session_start();
require_once __DIR__ . '/conf/db_conf.php';
require_once __DIR__ . '/lib/validation.php';
require_once __DIR__ . '/lib/functions.php';

if (isset($_GET['thread_id'])) {
    $thread_id = $_GET['thread_id'];
}

if (isset($_POST['response'])) {
    $response = $_POST['response'];
}

try {
    $pdo  = new PDO("mysql:host={$db_server};dbname={$db_name};charset=utf8", "{$db_user}", "{$db_pass}");
    $pdo -> setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $pdo -> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo h($e -> getMessage());
}

if (isset($_SESSION['user']) && isset($_POST['btn_submit'])) {
    if (!isset($response) || empty(replace($response))) {
        $error_message = '内容を入力してください。';
    } elseif (isset($_POST['token']) && isset($_SESSION['token']) && $_POST['token'] == $_SESSION['token']) {
        try {
            $sql = 'select * from responses where thread_id = :thread_id';
            $stmt = $pdo -> prepare($sql);
            $stmt -> bindValue(':thread_id', $thread_id);
            $stmt -> execute();
            $response_count = $stmt -> rowCount();

            $sql = 'insert into responses(response_id, thread_id, user_id, response) values(:response_id, :thread_id, :user_id, :response)';
            $stmt = $pdo -> prepare($sql);
            $stmt -> bindValue(':response_id', $response_count + 1);
            $stmt -> bindValue(':thread_id', $thread_id);
            $stmt -> bindValue(':user_id', $_SESSION['user']['id']);
            $stmt -> bindValue(':response', $response);
            $stmt -> execute();
        } catch (PDOException $e) {
            echo h($e -> getMessage());
        }
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

$_SESSION['token'] = $token = mt_rand();

$page = get_page();

[$show_responses, $max_page] = paging($responses, $page);

require_once __DIR__ . '/lib/header.php';

?>

<div style="color:red;">
  <?php if (isset($error_message)) echo $error_message ?>
</div>
<div class="form">
  <h2>投稿</h2>
  <form action="" method="post">
      <textarea name="response" cols="30" rows="10"></textarea><br>
      <input type="hidden" name="token" value="<?php echo $token ?>">
      <div class="submit"><input type="submit" name="btn_submit" value="投稿"></div>
  </form>
</div>
<div class="title">
  <h2><?php echo $title['title'] ?></h2><hr>
</div>
<?php foreach ($show_responses as $response) : ?>
  <div class="row">
    <div class="column">
      <p><?php echo "{$response['response_id']}. " ?><a href="user.php?user_id=<?php echo $response['user_id'] ?>"><?php echo "{$response['name']} @{$response['user_id']}" ?></a>
      <?php echo " {$response['created_at']}" ?></p>
    </div>
  </div>
  <div class="row">
    <div class="column">
      <p><?php echo nl2br($response['response']) ?></p>
    </div>
  </div><hr>
<?php endforeach ?>
<ul style="list-style:none;display:flex;justify-content:center;">
  <?php for ($i = 1; $i <= $max_page; $i++) : ?>
    <li style="margin-right:5px;"><a class="button" href="thread.php?thread_id=<?php echo $thread_id ?>&page=<?php echo $i ?>"><?php echo $i ?></a></li>
  <?php endfor ?>
</ul>

<?php require_once __DIR__ . '/lib/footer.php' ?>