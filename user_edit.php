<?php
require_once __DIR__ . '/conf/db_conf.php';
require_once __DIR__ . '/lib/validation.php';
require_once __DIR__ . '/lib/functions.php';

logined();

try {
    $pdo  = new PDO("mysql:host={$db_server};dbname={$db_name};charset=utf8", "{$db_user}", "{$db_pass}");
    $pdo -> setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $pdo -> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo h($e -> getMessage());
}

if (isset($_POST['edit_submit'])) {
    try {
        $id   = $_POST['id'];
        $name = $_POST['name'];

        $id_message     = id_validation($id);
        $name_message   = name_validation($name);

        $error_messages = array_merge($id_message, $name_message);

        if (!empty($error_messages)) {
            foreach ($error_messages as $message) {
                echo "{$message}<br>";
            }
        } else {
            $sql      = 'select * from users where user_id = :id ';
            $prepare  = $pdo -> prepare($sql);
            $prepare -> bindValue(':id', $id);
            $prepare -> execute();

            if (!empty($prepare -> fetchAll())) {
                $sql      = 'update users set name = :name where user_id = :id';
                $prepare  = $pdo -> prepare($sql);
                $prepare -> bindValue(':id', $id);
                $prepare -> bindValue(':name', $name);
                $prepare -> execute();

                $_SESSION['user'] = [
                    'id'   => $id,
                    'name' => $name,
                ];

                header("location: user.php?user_id=".urlencode($_SESSION['user']['id']));
                exit;
            }
        }
    } catch (PDOException $e) {
        echo h($e -> getMessage());
    }
}

try {
    $sql      = 'select * from users where user_id = :id';
    $prepare  = $pdo -> prepare($sql);
    $prepare -> bindValue(':id', $_SESSION['user']['id']);
    $prepare -> execute();

    $user = $prepare -> fetch();
} catch (PDOException $e) {
    echo h($e -> getMessage());
}

require_once __DIR__ . '/lib/header.php';

?>


<div class="user_edit">
  <h2>編集</h2>
  <div class="form">
    <form action="user_edit.php" method="post">
      <table>
        <tr>
          <td>ID</td>
          <td><?php echo $user['user_id'] ?></td>
        </tr>
        <tr>
          <td>ユーザー名</td>
          <td><input type="text" name="name" value="<?php echo $user['name'] ?>"></td>
        </tr>
      </table>
      <input type="hidden" name="id" value=<?php echo $_SESSION['user']['id'] ?>>
      <td><input type="submit" name="edit_submit" value="保存"></td>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/lib/footer.php' ?>