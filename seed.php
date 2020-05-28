<?php

require_once __DIR__ . '/conf/db_conf.php';
require_once __DIR__ . '/lib/functions.php';


if (isset($_POST['submit'])) {
    try {
        $pdo  = new PDO("mysql:host={$db_server};dbname={$db_name};charset=utf8", "{$db_user}", "{$db_pass}");
        $pdo -> setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $pdo -> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

        for ($i = 1; $i <= 100; $i++) {
            $sql = 'select * from responses where thread_id = :thread_id';
            $stmt = $pdo -> prepare($sql);
            $stmt -> bindValue(':thread_id', $i);
            $stmt -> execute();
            $response_count = $stmt -> rowCount();

            $sql = 'insert into responses(response_id, thread_id, user_id, response) values(:response_id, :thread_id, :user_id, :response)';
            $stmt = $pdo -> prepare($sql);
            $stmt -> bindValue(':response_id', $response_count + 1);
            $stmt -> bindValue(':thread_id', $i);
            $stmt -> bindValue(':user_id', "TestUser{$i}");
            $stmt -> bindValue(':response', 'テストです。');
            $stmt -> execute();
        }

    } catch (PDOException $e) {
        echo h($e ->getMessage());
    }
}

/*

$sql = 'insert into threads(user_id, title) values(:user_id, :title)';
$stmt = $pdo -> prepare($sql);
$stmt -> bindValue(':user_id', $_SESSION['user']['id']);
$stmt -> bindValue(':title', $title);
$stmt -> execute();



$sql = 'insert into users(user_id, name, password) values(:user_id, :name, :password)';
$stmt = $pdo -> prepare($sql);
$stmt -> bindValue(':user_id', "TestUser{$i}");
$stmt -> bindValue(':name', "テスト{$i}");
$stmt -> bindValue(':password', "Testuser{$i}");
$stmt -> execute();



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


 */

?>

<html>
  <body>
    <?php if (!isset($_POST['submit'])) : ?>
    <form action="" method="post">
      <input type="submit" name="submit" value="保存">
    </form>
    <?php else : ?>
      <p>完了しました。</p>
    <?php endif ?>
  </body>
</html>
