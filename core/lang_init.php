<?php
// Cek apakah session sudah dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek apakah user mengklik tombol ganti bahasa
if (isset($_GET['lang'])) {
    $langCode = $_GET['lang'];
    if ($langCode == 'id' || $langCode == 'en') {
        $_SESSION['lang'] = $langCode;
    }
}

//Tentukan bahasa aktif (Default: id)
$current_lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'id';

//Load file bahasa yang sesuai
$lang_path = __DIR__ . "/../languages/" . $current_lang . ".php";

if (file_exists($lang_path)) {
    $lang = include($lang_path);
} else {
    $lang = include(__DIR__ . "/../languages/id.php");
}
?>