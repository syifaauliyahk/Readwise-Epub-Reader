<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require "core/db.php";
require "core/class/epubclass.php"; 
require "core/lang_init.php"; 

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$user_id = $_SESSION['user_id'];
$uploadSuccess = false;
$log = "";
$bookId = 0;
$bookTitle = "";

// LOGIKA UPLOAD
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        $errorCode = $_FILES['file']['error'];
        $log .= $lang['log_system_error'] . $errorCode . "\n";
    } else {
        $manualTitle = $conn->real_escape_string($_POST['title']);
        $manualAuthor = $conn->real_escape_string($_POST['author']);
        $category = $conn->real_escape_string($_POST['category']);
        
        $uploadDir = 'uploads/user_' . $user_id . '/';
        if (!is_dir($uploadDir)) { mkdir($uploadDir, 0777, true); }

        $fileTmpPath = $_FILES['file']['tmp_name'];
        $fileName = $_FILES['file']['name'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if ($fileExt === 'epub') {
            $newFileName = 'book_' . time() . '.epub';
            $dest_path = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $log .= $lang['log_epub_uploaded'] . "\n";
                
                // Ekstrak EPUB
                $extractFolderName = 'book_' . time();
                $extractedPath = $uploadDir . $extractFolderName;
                
                $zip = new ZipArchive;
                if ($zip->open($dest_path) === TRUE) {
                    $zip->extractTo($extractedPath);
                    $zip->close();
                    $log .= $lang['log_epub_extracted'] . "\n";
                }

                // Proses Cover
                $coverPathDB = "";
                if (isset($_FILES['cover']) && $_FILES['cover']['error'] == 0) {
                    $coverName = $_FILES['cover']['name'];
                    $coverExt = strtolower(pathinfo($coverName, PATHINFO_EXTENSION));
                    $newCoverName = 'cover_' . time() . '.' . $coverExt;
                    $coverDest = $uploadDir . $newCoverName;
                    if (move_uploaded_file($_FILES['cover']['tmp_name'], $coverDest)) {
                        $coverPathDB = $coverDest;
                    }
                }

                // Simpan DB
                $sql = "INSERT INTO books (user_id, title, author, category, file_path, extracted_path, cover_image) 
                        VALUES ($user_id, '$manualTitle', '$manualAuthor', '$category', '$dest_path', '$extractedPath', '$coverPathDB')";
                
                if ($conn->query($sql) === TRUE) {
                    $uploadSuccess = true;
                    $bookId = $conn->insert_id;
                    $bookTitle = $manualTitle;
                    $log .= $lang['log_db_success'] . " (ID: $bookId)\n";
                } else {
                    $log .= $lang['log_db_error'] . $conn->error . "\n";
                }
            } else {
                $log .= $lang['log_move_error'] . "\n";
            }
        } else {
            $log .= $lang['log_invalid_format'] . "\n";
        }
    }
}

// Ambil info user untuk header
$sql_user = "SELECT name FROM users WHERE id = $user_id";
$res_user = $conn->query($sql_user);
$user = $res_user->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $lang['page_title_upload'] ?></title>
<link rel="stylesheet" href="styles/index.css?v=<?php echo time(); ?>">

<style>
    .result-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        text-align: center;
        padding: 40px;
    }
    .status-card {
        background: white;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        border: 1px solid #e0e0e0;
        max-width: 500px;
        width: 100%;
    }
    .icon-status { font-size: 60px; display: block; margin-bottom: 20px; }
    
    .btn-action {
        display: inline-block;
        padding: 12px 24px;
        margin: 10px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: bold;
        transition: 0.2s;
        font-size: 14px;
    }
    
    .btn-primary-action { 
        background: #333; 
        color: white; 
        border: none; 
    }
    .btn-primary-action:hover { 
        background: #555; 
        transform: translateY(-2px);
    }
    
    .btn-secondary-action { 
        background: #fff; 
        color: #333; 
        border: 1px solid #ccc; 
    }
    .btn-secondary-action:hover { 
        background: #f9f9f9; 
        border-color: #bbb;
    }
    
    details.log-box {
        margin-top: 30px;
        text-align: left;
        background: #222;
        color: #0f0;
        padding: 15px;
        border-radius: 8px;
        font-size: 12px;
        overflow: auto;
        font-family: monospace;
        border: 1px solid #444;
    }
    
    summary { outline: none; }
</style>
</head>

<body>

<div class="header">
  <img src="logo.jpeg" alt="logo" />
  <h1>READWISE</h1>
  <div style="margin-left: auto; display: flex; align-items: center; gap: 15px;">
    
    <div style="font-size:13px; font-weight:bold;">
        <a href="?lang=id" style="text-decoration:none; color: <?= ($current_lang == 'id') ? '#d4a75b' : '#ccc'; ?>">ID</a>
        <span style="color:#ddd"> | </span>
        <a href="?lang=en" style="text-decoration:none; color: <?= ($current_lang == 'en') ? '#d4a75b' : '#ccc'; ?>">EN</a>
    </div>

    <span style="color: #666; font-size:14px; font-weight:600;">👤 <?php echo htmlspecialchars($user['name']); ?></span>
    <a href="logout.php" style="background: #dc3545; color: white; padding: 8px 15px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight:bold;"><?= $lang['logout'] ?></a>
  </div>
</div>

<div class="container">
  
  <div class="sidebar">
    
    <a href="index.php" class="btn-upload-submit" style="text-align:center; text-decoration:none; display:block; background-color:#333; color:white; border:none;">
        &larr; <?= $lang['btn_back_library'] ?>
    </a>

    <div class="sidebar-card" style="margin-top:15px;">
      <div class="card-header">
        <span><?= $lang['status_info_title'] ?></span>
      </div>
      <p style="font-size:13px; color:#666; line-height:1.5; margin:0;">
        <?= $lang['status_info_desc'] ?>
      </p>
    </div>

  </div>

  <div class="library-content">
      <div class="result-container">
        <?php if ($uploadSuccess): ?>
            <div class="status-card">
                <span class="icon-status">✅</span>
                <h2 style="margin-top:0; color:#28a745; margin-bottom:10px;"><?= $lang['status_success_title'] ?></h2>
                <p style="color:#555; font-size:14px;"><?= sprintf($lang['status_success_msg'], htmlspecialchars($bookTitle)) ?></p>
                
                <div style="margin-top: 30px;">
                    <a href="index.php" class="btn-action btn-secondary-action"><?= $lang['btn_back_library'] ?></a>
                    <a href="reader.php?id=<?php echo $bookId; ?>" class="btn-action btn-primary-action"><?= $lang['btn_read_now'] ?></a>
                </div>
            </div>

            <details class="log-box" style="width:100%; margin-top:20px; max-width:500px;">
                <summary style="cursor:pointer; color:#fff;"><?= $lang['view_log'] ?></summary>
                <pre style="margin-top:10px; white-space:pre-wrap;"><?php echo htmlspecialchars($log); ?></pre>
            </details>

        <?php else: ?>
            <div class="status-card" style="border-top: 5px solid #dc3545;">
                <span class="icon-status">❌</span>
                <h2 style="margin-top:0; color:#dc3545;"><?= $lang['status_failed_title'] ?></h2>
                <p style="color:#666;"><?= $lang['status_failed_msg'] ?></p>
                
                <div style="margin-top: 30px;">
                    <a href="index.php" class="btn-action btn-primary-action"><?= $lang['btn_try_again'] ?></a>
                </div>
            </div>

            <div class="log-box" style="width:100%; margin-top:20px; max-width:500px;">
                <strong style="color:#ff5252;"><?= $lang['error_log_label'] ?></strong>
                <pre style="margin-top:10px; white-space:pre-wrap; color:#ddd;"><?php echo htmlspecialchars($log); ?></pre>
            </div>
        <?php endif; ?>
    </div>
  </div>

</div>

</body>
</html>