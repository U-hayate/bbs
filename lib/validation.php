<?php

function replace($str) {
    return preg_replace('/\A[\p{C}\p{Z}]++|[\p{C}\p{Z}]++\z/u', '', $str);
}

function id_validation($id) {
    $id_messages = [];
    $pattern     = '/\A(?=.*?[a-z])(?=.*?[A-Z])(?=.*?\d)[a-zA-Z\d]{6,10}+\z/';
    preg_match("$pattern", $id, $matches);
    if (empty($matches)) {
        $id_messages[] = 'IDは半角英小文字大文字数字を含む6文字以上10文字以下で入力してください。';
    }

    return $id_messages;
}

function name_validation($name) {
    $name_messages = [];
    if (!isset($name) || replace($name) === "") {
        $name_messages[] = 'ユーザー名を入力してください。';
    }

    if (mb_strlen($name) > 10) {
        $name_messages[] = 'ユーザー名は10文字以下で入力してください。';
    }

    return $name_messages;
}

function pass_validation($pass, $id) {
    $pass_messages = [];
    $pattern       = '/\A(?=.*?[a-z])(?=.*?[A-Z])(?=.*?\d)[a-zA-Z\d]{8,16}+\z/';
    preg_match("$pattern", $pass, $matches);
    if (empty($matches)) {
        $pass_messages[] = 'パスワードは半角英小文字大文字数字を含む8文字以上16文字以下で入力してください。';
    }

    if ($pass === $id) {
        $pass_messages[] = 'パスワードはIDと異なるものを入力してください。';
    }

    return $pass_messages;
}

function title_validation($title) {
    if (!isset($title) || replace($title) === "") {
        return 'スレッド名を入力してください。';
    }
    if (mb_strlen($title) > 50) {
        return 'スレッド名は50文字以内で入力してください。';
    }
    return 'none';
}

?>