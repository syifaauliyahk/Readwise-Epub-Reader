<?php
require "core/db.php";
require "core/lang_init.php"; 

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => $lang['err_not_logged_in']]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => $lang['err_invalid_method']]);
    exit();
}

$book_id = intval($_POST['book_id'] ?? 0);
$user_id = $_SESSION['user_id'];

if (!$book_id) {
    echo json_encode(['error' => $lang['err_no_book_id']]);
    exit();
}

// Get book info 
$sql = "SELECT * FROM books WHERE id = $book_id AND user_id = $user_id";
$result = $conn->query($sql);

if (!$result || $result->num_rows === 0) {
    echo json_encode(['error' => $lang['err_book_not_found']]);
    exit();
}

$book = $result->fetch_assoc();

// Delete files logic
$files_deleted = true;

// Delete EPUB file
if (file_exists($book['file_path'])) {
    if (!unlink($book['file_path'])) {
        $files_deleted = false;
    }
}

// Delete extracted folder logic
if (is_dir($book['extracted_path'])) {
    function deleteDirectory($dir) {
        if (!is_dir($dir)) return false;
        
        $files = array_diff(scandir($dir), ['.', '..']);
        
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? deleteDirectory($path) : unlink($path);
        }
        
        return rmdir($dir);
    }
    
    if (!deleteDirectory($book['extracted_path'])) {
        $files_deleted = false;
    }
}

// Delete from database
$sql_delete = "DELETE FROM books WHERE id = $book_id AND user_id = $user_id";

if ($conn->query($sql_delete)) {
    // Also delete related bookmarks
    $sql_bookmarks = "DELETE FROM bookmarks WHERE book_id = $book_id AND user_id = $user_id";
    $conn->query($sql_bookmarks);
    
    echo json_encode([
        'success' => true,
        'message' => $lang['msg_delete_success'], 
        'files_deleted' => $files_deleted
    ]);
} else {
    echo json_encode([
        'error' => $lang['err_delete_db'], 
        'sql_error' => $conn->error
    ]);
}
?>