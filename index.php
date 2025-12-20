<?php
require_once "core/db.php";
require_once "core/lang_init.php"; 
// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user info
$sql_user = "SELECT name FROM users WHERE id = $user_id";
$result_user = $conn->query($sql_user);
$user = $result_user->fetch_assoc();

// Get all books 
$sql_books = "SELECT * FROM books ORDER BY uploaded_at DESC";
$result_books = $conn->query($sql_books);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title><?= $lang['library_title'] ?> - ReadMe</title>
<link rel="stylesheet" href="styles/index.css?v=<?php echo time(); ?>">
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
    
    <div class="sidebar-card">
      <div class="card-header">
        <span>📤 <?= $lang['upload_title'] ?></span>
      </div>
      
      <form action="upload.php" method="POST" enctype="multipart/form-data">
        
        <div class="form-group">
            <label class="sidebar-label"><?= $lang['book_title_label'] ?></label>
            <input type="text" name="title" class="sidebar-input" placeholder="<?= $lang['book_title_ph'] ?>" required>
        </div>

        <div class="form-group">
            <label class="sidebar-label"><?= $lang['author_label'] ?></label>
            <input type="text" name="author" class="sidebar-input" placeholder="<?= $lang['author_ph'] ?>" required>
        </div>

        <div class="form-group">
            <label class="sidebar-label"><?= $lang['category_label'] ?></label>
            <select name="category" class="sidebar-select" required>
                <option value=""><?= $lang['select_category'] ?></option>
                <option value="Agribisnis">Agribisnis</option>
                <option value="Teknik Informatika">Teknik Informatika</option>
                <option value="Sistem Informasi">Sistem Informasi</option>
                <option value="Matematika">Matematika</option>
                <option value="Fisika">Fisika</option>
                <option value="Biologi">Biologi</option>
                <option value="Kimia">Kimia</option>
                <option value="Teknik Pertambangan">Teknik Pertambangan</option>
            </select>
        </div>

        <div class="form-group">
            <label class="sidebar-label">🖼️ <?= $lang['cover_label'] ?></label>
            <div class="file-input-wrapper">
                <span><?= $lang['select_image'] ?></span>
                <input type="file" name="cover" accept="image/*">
            </div>
        </div>

        <div class="form-group">
            <label class="sidebar-label">📖 <?= $lang['epub_label'] ?></label>
            <div class="file-input-wrapper">
                <span><?= $lang['select_epub'] ?></span>
                <input type="file" name="file" accept=".epub" required>
            </div>
        </div>

        <button type="submit" class="btn-upload-submit">
          <?= $lang['upload_submit'] ?>
        </button>
      </form>
    </div>

    <div class="sidebar-card">
      <div class="card-header">
        <span>🏷️ <?= $lang['filter_title'] ?></span>
      </div>
      
      <div class="form-group">
          <label class="sidebar-label"><?= $lang['filter_category_label'] ?></label>
          <select id="category-filter" class="sidebar-select" onchange="filterBooks(this.value)">
            <option value=""><?= $lang['show_all'] ?></option>
            <option value="Agribisnis">Agribisnis</option>
            <option value="Teknik Informatika">Teknik Informatika</option>
            <option value="Sistem Informasi">Sistem Informasi</option>
            <option value="Matematika">Matematika</option>
            <option value="Fisika">Fisika</option>
            <option value="Biologi">Biologi</option>
            <option value="Kimia">Kimia</option>
            <option value="Teknik Pertambangan">Teknik Pertambangan</option>
          </select>
      </div>
      
      <div class="form-group">
          <label class="sidebar-label"><?= $lang['sort_label'] ?></label>
          <select id="sort-filter" class="sidebar-select" onchange="sortBooks(this.value)">
            <option value="newest"><?= $lang['sort_newest'] ?></option>
            <option value="oldest"><?= $lang['sort_oldest'] ?></option>
            <option value="title"><?= $lang['sort_az'] ?></option>
          </select>
      </div>
    </div>

  </div>

  <div class="library-content">
    
    <div class="library-header">
        <h2 style="margin: 0; font-size: 24px;"><?= $lang['library_title'] ?></h2>
        <span style="font-size:13px; color:#999; font-weight:600;">
            <?php echo $result_books->num_rows; ?> <?= $lang['books_available'] ?>
        </span>
    </div>

    <div class="search-container">
      <input type="text" id="search-library" class="search-bar-input" placeholder="🔍 <?= $lang['search_library_placeholder'] ?>" onkeyup="searchLibrary(this.value)">
    </div>

    <div class="books-container-scroll">
        <div class="books-grid" id="books-container">
        
        <?php if ($result_books && $result_books->num_rows > 0): ?>
            
            <?php while ($book = $result_books->fetch_assoc()): ?>
            
            <div class="book-card" 
                data-title="<?php echo strtolower($book['title']); ?>" 
                data-author="<?php echo strtolower($book['author']); ?>"
                data-category="<?php echo isset($book['category']) ? strtolower($book['category']) : ''; ?>">
                
                <div class="book-cover">
                <?php if (!empty($book['cover_image']) && file_exists($book['cover_image'])): ?>
                    <img src="<?php echo $book['cover_image']; ?>" alt="Cover">
                <?php else: ?>
                    <div class="book-cover-placeholder">
                        <span style="font-size: 30px;">📖</span>
                        <div style="margin-top:5px; font-size:10px;">No Cover</div>
                    </div>
                <?php endif; ?>
                </div>
                
                <div class="book-info">
                    <span class="category-tag"><?php echo isset($book['category']) ? htmlspecialchars($book['category']) : 'General'; ?></span>
                    <h3 class="book-title"><?php echo htmlspecialchars($book['title']); ?></h3>
                    <p class="book-author"><?= $lang['by_author'] ?> <?php echo htmlspecialchars($book['author']); ?></p>
                    
                    <div class="book-actions">
                        <a href="reader.php?id=<?php echo $book['id']; ?>" class="btn-read"><?= $lang['read_action'] ?></a>
                        
                        <?php if ($book['user_id'] == $_SESSION['user_id']): ?>
                            <button class="btn-delete" onclick="deleteBook(<?php echo $book['id']; ?>, '<?php echo addslashes($book['title']); ?>')">🗑️</button>
                        <?php endif; ?>
                    </div>
                </div>
                
            </div>
            
            <?php endwhile; ?>
            
        <?php else: ?>
            <div class="empty-state">
                <div style="font-size: 50px; opacity:0.3; margin-bottom:10px;">📚</div>
                <h3><?= $lang['no_books'] ?></h3>
                <p><?= $lang['upload_first_hint'] ?></p>
            </div>
        <?php endif; ?>
        
        </div>
    </div>

  </div>

</div>

<script>
// Variabel Bahasa untuk Alert JS
const langDeleteConfirm = "<?= $lang['delete_confirm'] ?>";
const langDeleteWarning = "<?= $lang['delete_warning'] ?>";
const langDeleteSuccess = "<?= $lang['delete_success'] ?>";
const langDeleteFailed  = "<?= $lang['delete_failed'] ?>";

// Search functionality
function searchLibrary(query) {
  const cards = document.querySelectorAll('.book-card');
  const searchLower = query.toLowerCase();
  
  cards.forEach(card => {
    const title = card.getAttribute('data-title');
    const author = card.getAttribute('data-author');
    const category = card.getAttribute('data-category');
    
    if (title.includes(searchLower) || author.includes(searchLower) || (category && category.includes(searchLower))) {
      card.style.display = 'flex'; 
    } else {
      card.style.display = 'none';
    }
  });
}

// Filter by category
function filterBooks(category) {
  const cards = document.querySelectorAll('.book-card');
  const filterLower = category.toLowerCase();

  cards.forEach(card => {
    const cardCategory = card.getAttribute('data-category');
    if (category === "" || (cardCategory && cardCategory === filterLower)) {
      card.style.display = 'flex';
    } else {
      card.style.display = 'none';
    }
  });
}

// Sort books logic
function sortBooks(sortBy) {
    const container = document.getElementById('books-container');
    const cards = Array.from(container.getElementsByClassName('book-card'));

    cards.sort((a, b) => {
        if (sortBy === 'title') {
            return a.dataset.title.localeCompare(b.dataset.title);
        } else if (sortBy === 'newest') {
            return 0; // Already sorted by SQL
        }
        return 0;
    });

    cards.forEach(card => container.appendChild(card));
}

// Delete book function
function deleteBook(bookId, bookTitle) {
  if (!confirm(`${langDeleteConfirm} "${bookTitle}"?\n\n${langDeleteWarning}`)) {
    return;
  }
  
  const formData = new FormData();
  formData.append('book_id', bookId);
  
  fetch('delete_book.php', { method: 'POST', body: formData })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      alert(langDeleteSuccess);
      location.reload();
    } else {
      alert(langDeleteFailed + ': ' + data.error);
    }
  })
  .catch(err => alert('Error: ' + err.message));
}
</script>

</body>
</html>