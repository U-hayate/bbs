<?php

// HTML特殊文字をエスケープする(xss対策)
function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// すでにログインしている時に使う
function logined () {
    session_start();
    // ログインしていなかったらログイン画面に移動
    if (!isset($_SESSION['user'])) {
        header('location: login.php');
        exit;
    }
}

// まだログインしていない時に使う
function unlogined () {
    session_start();
    // ログインしていたらホームに移動
    if (isset($_SESSION['user'])) {
        header('location: index.php');
        exit;
    }
}

// 現在のページ
function get_page() {
    if (isset($_GET['page'])) {
        $page = $_GET['page'];
    } else {
        $page = 1;
    }
    return $page;
}

// ページング
function paging($items, $page) {
    $item_count = count($items);
    $max_item   = 10;
    $max_page   = ceil($item_count / $max_item);
    $start_page = $max_item * ($page - 1);
    $show_items = array_slice($items, $start_page, $max_item, true);

    return [$show_items, $max_page];
}

?>