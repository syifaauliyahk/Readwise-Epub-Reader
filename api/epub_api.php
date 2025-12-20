<?php
require_once "../core/db.php";
require_once "../core/class/epubclass.php";
require_once "../core/lang_init.php"; 

header('Content-Type: application/json');

// Error Handling 
ini_set('display_errors', 0);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => $lang['api_unauthorized']]); 
    exit();
}

$action = $_GET['action'] ?? '';
$bookId = intval($_GET['book_id'] ?? 0);

if (!$bookId) {
    echo json_encode(['error' => $lang['api_missing_book_id']]); 
    exit();
}

// Ambil info buku dari DB 
$sql = "SELECT * FROM books WHERE id = $bookId";
$result = $conn->query($sql);

if (!$result || $result->num_rows === 0) {
    echo json_encode(['error' => $lang['api_book_not_found']]); 
    exit();
}

$book = $result->fetch_assoc();
$extractedPath = $book['extracted_path'];

$fullPath = __DIR__ . '/../' . $extractedPath;

if (!is_dir($fullPath)) {
    echo json_encode(['error' => $lang['api_book_files_missing'], 'path' => $fullPath]); 
    exit();
}

try {
    $epub = new EPubReader($fullPath);

    switch ($action) {
        case 'metadata':
            echo json_encode([
                'success' => true,
                'metadata' => $epub->getMetadata(),
                'total_chapters' => $epub->getTotalChapters()
            ]);
            break;

        case 'toc':
            $toc = $epub->getTOC();
            $spine = $epub->getSpine(); 
            
            echo json_encode([
                'success' => true,
                'toc' => $toc,
                'debug' => [
                    'total_spine' => count($spine),
                    'first_spine_item' => $spine[0] ?? 'empty',
                    'toc_count' => count($toc)
                ]
            ]);
            break;

        case 'chapter':
            $index = intval($_GET['index'] ?? 0);
            $content = $epub->getChapterContent($index);
            
            if ($content !== null) {
                echo json_encode([
                    'success' => true,
                    'content' => $content,
                    'title' => $epub->getChapterTitle($index),
                    'index' => $index
                ]);
            } else {
                echo json_encode(['error' => $lang['api_chapter_out_of_range']]);
            }
            break;

        case 'search':
            $query = $_GET['query'] ?? '';
            $results = $epub->searchText($query);
            echo json_encode([
                'success' => true,
                'results' => $results
            ]);
            break;

        // === BOOKMARKS ===
        case 'add_bookmark':
            $index = intval($_POST['index'] ?? 0);
            $note = $conn->real_escape_string($_POST['note'] ?? '');
            
            // AMBIL POSISI DARI JS (PENTING!)
            $position = $conn->real_escape_string($_POST['position'] ?? '0');
            
            // Masukkan position ke query
            $sql = "INSERT INTO bookmarks (user_id, book_id, chapter_index, note, position, created_at) 
                    VALUES ({$_SESSION['user_id']}, $bookId, $index, '$note', '$position', NOW())";
            
            if ($conn->query($sql)) {
                echo json_encode(['success' => true, 'msg' => $lang['api_bookmark_saved']]);
            } else {
                echo json_encode(['error' => $lang['api_db_error'] . $conn->error]);
            }
            break;

        case 'get_bookmarks':
            $sql = "SELECT * FROM bookmarks WHERE user_id = {$_SESSION['user_id']} AND book_id = $bookId ORDER BY chapter_index ASC";
            $res = $conn->query($sql);
            
            $bookmarks = [];
            while($row = $res->fetch_assoc()) {
                $row['chapter_title'] = $epub->getChapterTitle($row['chapter_index']);
                $bookmarks[] = $row;
            }
            
            echo json_encode(['success' => true, 'bookmarks' => $bookmarks]);
            break;

        case 'delete_bookmark':
            $bookmarkId = intval($_POST['bookmark_id']);
            $sql = "DELETE FROM bookmarks WHERE id = $bookmarkId AND user_id = {$_SESSION['user_id']}";
            
            if ($conn->query($sql)) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['error' => $lang['api_delete_failed']]);
            }
            break;
            
        default:
            echo json_encode(['error' => $lang['api_invalid_action']]);
    }

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>