<?php

date_default_timezone_set('Asia/Tokyo');
require_once __DIR__ . '/conf/db_conf.php';
require_once __DIR__ . '/lib/functions.php';
require_once __DIR__ . '/lib/validation.php';

unlogined();

if (isset($_POST['btn_submit'])) {
    try {
        $pdo  = new PDO("mysql:host={$db_server};dbname={$db_name};charset=utf8", "{$db_user}", "{$db_pass}");
        $pdo -> setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $pdo -> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

        $user_id  = $_POST['user_id'];
        $name     = $_POST['name'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $time     = date("Y-m-d H:i:s");

        $sql = 'select * from users where user_id=:user_id';
        $stmt = $pdo -> prepare($sql);
        $stmt -> bindValue(':user_id', $user_id);
        $stmt -> execute();

        if (empty($stmt->fetchAll())) {
            $id_message   = id_validation($user_id);
            $name_message = name_validation($name);
            $pass_message = pass_validation($_POST['password'], $user_id);

            $error_messages = array_merge($id_message, $name_message, $pass_message);

            if (empty($error_messages)) {
                $sql  = 'insert into users(user_id, name, password) values(:user_id, :name, :password)';
                $stmt = $pdo -> prepare($sql);
                $stmt -> bindValue(':user_id', $user_id);
                $stmt -> bindValue(':name', $name);
                $stmt -> bindValue(':password', $password);
                $stmt -> execute();

                session_regenerate_id(true);
                $_SESSION['user'] = [
                    'id'   => $user_id,
                    'name' => $name,
                ];

                header('location: index.php');
                exit;
            }
        } else {
            $error_messages[] = 'このIDはすでに使用されています。';
        }
    } catch (PDOException $e) {
        echo h($e -> getMessage());
    }
}

require_once __DIR__ . '/lib/header.php';

?>

<div class="signup">
  <div class="title">
    <h1>新規登録</h1>
  </div>
  <div class="form">
    <form action="" method="post">
      <table>
        <tr>
          <th>ID</th>
          <td><input type="text" name="user_id" value="<?php if (isset($_POST['user_id'])) echo $_POST['user_id'] ?>"></td>
        </tr>
        <tr>
          <th>名前</th>
          <td><input type="text" name="name" value="<?php if (isset($_POST['name'])) echo $_POST['name'] ?>"></td>
        </tr>
        <tr>
          <th>パスワード</th>
          <td><input type="password" name="password"></td>
        </tr>
      </table>
      <div class="submit"><input type="submit" name="btn_submit" value="登録"></div>
    </form>
  </div>
  <div class="error">
    <?php if (isset($error_messages)) : ?>
      <ul>
        <?php foreach ($error_messages as $message) : ?>
          <li>※<?php echo $message ?></li>
        <?php endforeach ?>
      </ul>
    <?php endif ?>
  </div>
  <div class="rule">
    <ul>
      <li>IDは半角英小文字大文字数字を含む6文字以上10文字以下で入力してください。</li>
      <li>ユーザー名を入力してください。</li>
      <li>パスワードは半角英小文字大文字数字を含む8文字以上16文字以下で入力してください。</li>
      <li>パスワードはIDと異なるものを入力してください。</li>
    </ul>
  </div>
</div>
<?php require_once __DIR__ . '/lib/footer.php' ?>