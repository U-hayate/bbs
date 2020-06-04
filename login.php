<?php
require_once __DIR__ . '/conf/db_conf.php';
require_once __DIR__ . '/lib/functions.php';
require_once __DIR__ . '/lib/validation.php';

unlogined();

if (isset($_POST['login_submit'])) {
    if (replace($_POST['user_id']) !== "") {
        try {
            $pdo  = new PDO("mysql:host={$db_server};dbname={$db_name};charset=utf8", "{$db_user}", "{$db_pass}");
            $pdo -> setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $pdo -> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

            $sql      = 'select * from users where user_id=:user_id';
            $prepare  = $pdo -> prepare($sql);
            $prepare -> bindValue(':user_id', $_POST['user_id']);
            $prepare -> execute();

            $result = $prepare -> fetch();

            if ($result !== false) {
                if (password_verify($_POST['password'], $result['password'])) {
                    session_regenerate_id(true);
                    $_SESSION['user'] = [
                        'id'   => $result['user_id'],
                        'name' => $result['name'],
                    ];

                    header('location: index.php');
                    exit;
                }
            }

        } catch (PDOExeption $e) {
            echo h($e -> getMessage());
        }

    }

    if (!isset($_SESSION['user'])) {
        $error_message = 'IDまたはパスワードが違います。';
    }
}

require_once __DIR__ . '/lib/header.php';

?>

<div class="title"><h1>ログイン</h1></div>
<div class="form">
  <form action="" method="post">
    <table>
      <tr>
        <th>ID</th>
        <td><input type="text" name="user_id" value="<?php if (isset($_POST['user_id'])) echo $_POST['user_id'] ?>"></td>
      </tr>
      <tr>
        <th>パスワード</th>
        <td><input type="password" name="password"></td>
      </tr>
    </table>
    <input type="submit" name="login_submit" value="ログイン">
  </form>
</div>
<div style="color:red;">
  <?php if (isset($error_message)) : ?>
    <p>※<?php echo $error_message ?></p>
  <?php endif ?>
</div>

<?php require_once __DIR__ . '/lib/footer.php' ?>