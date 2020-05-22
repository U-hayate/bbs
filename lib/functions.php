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



?>