<?php
session_start();
require_once __DIR__ . '/conf/db_conf.php';
require_once __DIR__ . '/lib/functions.php';

try {
    $pdo  = new PDO("mysql:host={$db_server};dbname={$db_name};charset=utf8", "{$db_user}", "{$db_pass}");
    $pdo -> setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $pdo -> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

    if (isset($_GET['search'])) {
        $sql   = 'select threads.thread_id, threads.title, threads.user_id, users.name, threads.created_at ';
        $sql  .= 'from threads INNER JOIN users on threads.user_id = users.user_id ';
        $sql  .= 'where title like :search order by thread_id desc';
        $stmt  = $pdo -> prepare($sql);
        $stmt -> bindValue(':search', sprintf('%%%s%%', addcslashes($_GET['search'], '\_%')));
    } else {
        $sql   = 'select threads.thread_id, threads.title, threads.user_id, users.name, threads.created_at ';
        $sql  .= 'from threads INNER JOIN users on threads.user_id = users.user_id order by thread_id desc';
        $stmt  = $pdo -> prepare($sql);
    }
    $stmt   -> execute();
    $threads = $stmt -> fetchAll();

} catch (PDOException $e) {
    echo h($e -> getMessage());
}

$page = get_page();

[$show_threads, $max_page] = paging($threads, $page);

require_once __DIR__ . '/lib/header.php';

?>

<div style="text-align:center;">
  <h2><a class="button" href="thread_create.php">スレを立てる</a></h2>
</div>
<div class="search">
  <h3>タイトル検索</h3>
  <form action="" method="get">
    <div class="row">
      <div class="column"><input type="search" name="search" value="<?php if (isset($_GET['search'])) echo $_GET['search'] ?>"></div>
      <div class="column-10"><input type="submit" value="検索"></div>
    </div>
  </form>
</div>
<div class="threads">
  <p style="text-align:center;"><?php echo "{$page} / {$max_page}" ?>ページ目</p>
  <table>
    <tr>
      <th>No.</th><th>タイトル</th><th>投稿者</th><th>作成日時</th>
    </tr>
    <?php foreach ($show_threads as $thread) : ?>
      <tr>
        <td><?php echo $thread['thread_id'] ?></td>
        <td><a href="thread.php?thread_id=<?php echo $thread['thread_id'] ?>"><?php echo $thread['title'] ?></a></td>
        <td><a href="user.php?user_id=<?php echo $thread['user_id'] ?>"><?php echo $thread['name'] ?></a></td>
        <td><?php echo $thread['created_at'] ?></td>
      </tr>
    <?php endforeach ?>
  </table>
  <p style="text-align:center;"><?php echo "{$page} / {$max_page}" ?>ページ目</p>
  <ul style="list-style:none;display:flex;justify-content:center;">
    <?php if ($page != 1) : ?>
      <li style="margin-right:5px;"><a class="button" href="index.php?search=<?php if (isset($_GET['search'])) echo $_GET['search'] ?>&page=<?php echo ($page - 1) ?>">←</a></li>
    <?php endif ?>
    <?php if ($page > 5 && $max_page > 9) : ?>
      <?php for ($i = ($page - 4); $i <= $page; $i++) : ?>
        <li style="margin-right:5px;"><a class="button" href="index.php?search=<?php if (isset($_GET['search'])) echo $_GET['search'] ?>&page=<?php echo $i ?>"><?php echo $i ?></a></li>
      <?php endfor ?>
      <?php for ($i = ($page + 1); $i <= $page + 4; $i++) : ?>
        <li style="margin-right:5px;"><a class="button" href="index.php?search=<?php if (isset($_GET['search'])) echo $_GET['search'] ?>&page=<?php echo $i ?>"><?php echo $i ?></a></li>
      <?php endfor ?>
    <?php elseif ($max_page > 9) : ?>
      <?php for ($i = 1; $i <= 9; $i++) : ?>
        <li style="margin-right:5px;"><a class="button" href="index.php?search=<?php if (isset($_GET['search'])) echo $_GET['search'] ?>&page=<?php echo $i ?>"><?php echo $i ?></a></li>
      <?php endfor ?>
    <?php else : ?>
      <?php for ($i = 1; $i <= $max_page; $i++) : ?>
        <li style="margin-right:5px;"><a class="button" href="index.php?search=<?php if (isset($_GET['search'])) echo $_GET['search'] ?>&page=<?php echo $i ?>"><?php echo $i ?></a></li>
      <?php endfor ?>
    <?php endif ?>
    <?php if ($page != $max_page) : ?>
      <li style="margin-right:5px;"><a class="button" href="index.php?search=<?php if (isset($_GET['search'])) echo $_GET['search'] ?>&page=<?php echo ($page + 1) ?>">→</a></li>
    <?php endif ?>
  </ul>
</div>

<?php require_once __DIR__ . '/lib/footer.php' ?>