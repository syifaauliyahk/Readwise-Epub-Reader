<?php
require "core/db.php";
require "core/lang_init.php"; 

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
if (!isset($_GET['id'])) { die("No book selected."); }

$id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Ambil data buku
$sql = "SELECT * FROM books WHERE id = $id"; 
$result = $conn->query($sql);
if (!$result || $result->num_rows == 0) { die("Book not found."); }
$current_book = $result->fetch_assoc();

// User info for header
$sql_user = "SELECT name FROM users WHERE id = $user_id";
$res_user = $conn->query($sql_user);
$user = $res_user->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title><?= sprintf($lang['page_title_reader'], htmlspecialchars($current_book['title'])) ?></title>
<link rel="stylesheet" href="styles/style.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="styles/reader.css?v=<?php echo time(); ?>">
</head>

<body>

<div class="header">
  <img src="logo.jpeg" alt="logo" /> <h1>READWISE</h1>
  <div style="margin-left: auto; display: flex; align-items: center; gap: 15px;">
    
    <div style="font-size:13px; font-weight:bold;">
        <a href="?id=<?= $id ?>&lang=id" style="text-decoration:none; color: <?= ($current_lang == 'id') ? '#d4a75b' : '#ccc'; ?>">ID</a>
        <span style="color:#ddd"> | </span>
        <a href="?id=<?= $id ?>&lang=en" style="text-decoration:none; color: <?= ($current_lang == 'en') ? '#d4a75b' : '#ccc'; ?>">EN</a>
    </div>

    <span style="color: #666; font-size:14px; font-weight:600;">👤 <?php echo htmlspecialchars($user['name']); ?></span>
    <a href="logout.php" style="background: #dc3545; color: white; padding: 8px 15px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight:bold;"><?= $lang['logout'] ?></a>
  </div>
</div>

<div class="container">
  
  <div class="sidebar">
    
    <a href="index.php" class="btn-back-lib">
        &larr; <?= $lang['back_to_library'] ?>
    </a>

    <div class="sidebar-card">
        <div class="card-header">
            <span>📑 <?= $lang['toc_title'] ?></span>
        </div>
        <div class="toc-scroll-box">
            <ul id="toc-list" class="toc-list">
                <li style="padding:15px; color:#999; text-align:center;"><?= $lang['toc_loading'] ?></li>
            </ul>
        </div>
    </div>

    <div class="sidebar-card">
        <div class="card-header">
            <span>🔍 <?= $lang['search_book_title'] ?></span>
        </div>
        <div class="search-row">
            <input type="text" id="search-input" class="sidebar-input" placeholder="<?= $lang['search_book_placeholder'] ?>" onkeypress="handleEnter(event)"/>
            <button class="search-btn" onclick="searchInBook()"><?= $lang['search_go_btn'] ?></button>
        </div>
        <div id="search-results" style="display:none; background:#f9f9f9; border:1px solid #eee; max-height:150px; overflow-y:auto; padding:10px; margin-top:10px; border-radius:6px; font-size:12px;"></div>
    </div>

    <div class="sidebar-card">
        <div class="card-header">
            <span>📌 <?= $lang['bookmarks_title'] ?></span>
        </div>
        
        <div style="display:flex; gap:5px; margin-bottom:10px;">
            <button class="btn-action-small btn-primary" onclick="saveBookmarkOnly()">
                <?= $lang['btn_bookmark'] ?>
            </button>
            <button class="btn-action-small btn-secondary" onclick="addNote()">
                <?= $lang['btn_note'] ?>
            </button>
        </div>

        <div id="bookmarks-list" class="bookmark-list">
            <div style="padding:15px; color:#999; text-align:center; font-size:13px;"><?= $lang['notes_loading'] ?></div>
        </div>
    </div>

  </div>

  <div class="reader-area">
    <div class="reader-header">
        <h2 id="chapter-title" style="margin:0; font-size:16px; color:#333; display:flex; align-items:center; gap:10px;">
            <span id="chapter-name"><?= $lang['reader_loading'] ?></span>
        </h2>
        <span id="page-info" style="font-size:12px; color:#666; background:#f0f0f0; padding:5px 12px; border-radius:15px; font-weight:600;">
            <?= $lang['reader_wait'] ?>
        </span>
    </div>

    <div id="viewer">
        <div style="text-align:center; padding-top:100px; color:#ccc;">
            <div style="font-size:50px; margin-bottom:20px;">📖</div>
            <p><?= $lang['reader_loading'] ?></p>
        </div>
    </div>
  </div>

</div>

<script>
    const bookId = <?php echo $id; ?>;
    let currentChapterIndex = 0;
    let totalChapters = 0;

    // VARIABLE BAHASA UNTUK JS
    const langSearchSearching = "<?= $lang['search_searching'] ?>";
    const langSearchFound     = "<?= $lang['search_found'] ?>";
    const langSearchNoResult  = "<?= $lang['search_no_results'] ?>";
    const langNotePrompt      = "<?= $lang['note_prompt'] ?>";
    const langNoteSaved       = "<?= $lang['note_saved'] ?>";
    const langNoteDelConfirm  = "<?= $lang['note_delete_confirm'] ?>";
    const langNoNotes         = "<?= $lang['no_notes'] ?>";
    const langMsgBookmark     = "<?= $lang['msg_bookmark_added'] ?>";
    const langLabelBookmark   = "<?= $lang['label_bookmark'] ?>";

    window.onload = function() {
        loadMetadata(); 
        loadBookmarks(); 
    };

    function loadMetadata() {
        fetch(`api/epub_api.php?action=metadata&book_id=${bookId}`)
            .then(r => r.json())
            .then(data => {
                if(data.success) {
                    totalChapters = data.total_chapters;
                    loadTOC();
                    loadChapter(0, null);
                }
            });
    }

    function loadTOC() {
        fetch(`api/epub_api.php?action=toc&book_id=${bookId}`)
            .then(r => r.json())
            .then(data => {
                if(data.success) {
                    const tocContainer = document.getElementById('toc-list');
                    tocContainer.innerHTML = ''; 
                    document.getElementById('page-info').innerText = `${data.toc.length} Chapters`;

                    data.toc.forEach((item, i) => {
                        let li = document.createElement('li');
                        li.className = 'toc-item';
                        let finalIndex = (item.spine_index !== undefined) ? item.spine_index : i;
                        
                        let anchor = "";
                        if(item.src && item.src.includes('#')) {
                            anchor = item.src.split('#')[1];
                        }

                        li.innerText = item.label;
                        li.onclick = function() {
                            document.querySelectorAll('.toc-item').forEach(el => el.classList.remove('active'));
                            this.classList.add('active');
                            loadChapter(finalIndex, anchor);
                        };
                        if (i === 0) li.classList.add('active');
                        tocContainer.appendChild(li);
                    });
                }
            });
    }

    
    // FUNGSI: LOAD CHAPTER & SCROLLING
    function loadChapter(index, specificTarget = null) {
        index = parseInt(index);
        if(isNaN(index)) return;

        currentChapterIndex = index;
        const viewer = document.getElementById('viewer');
        viewer.style.opacity = '0.5';

        fetch(`api/epub_api.php?action=chapter&book_id=${bookId}&index=${index}`)
            .then(r => r.json())
            .then(data => {
                viewer.style.opacity = '1';
                if(data.success) {
                    let content = data.content;
                    
                    let isSearch = false;
                    // Cek jika  pencarian teks atau ID elemen
                    if (specificTarget && isNaN(specificTarget) && specificTarget.length > 0) {
                        // Cek apakah targetnya bukan elemen ID yang valid
                        if (!document.getElementById(specificTarget)) {
                            isSearch = true;
                        }
                    }

                    if (isSearch && specificTarget) {
                        try {
                            const regex = new RegExp(`(?![^<]+>)(${specificTarget})`, 'gi');
                            content = content.replace(regex, '<mark>$1</mark>');
                        } catch(e) {}
                    }

                    viewer.innerHTML = content;
                    document.getElementById('chapter-name').innerText = data.title;
                    
                    const tocItems = document.querySelectorAll('.toc-item');
                    tocItems.forEach((el, idx) => {
                        if(idx === index) el.classList.add('active'); 
                        else el.classList.remove('active');
                    });

                    setTimeout(() => {
                        if (specificTarget) {
                            // 1: Bookmark Posisi Scroll 
                            if (!isNaN(specificTarget)) {
                                let percentage = parseFloat(specificTarget);
                                viewer.scrollTop = percentage * (viewer.scrollHeight - viewer.clientHeight);
                            } 
                            // 2: Pencarian Teks (Scroll ke Highlight)
                            else if (isSearch) {
                                const markEl = document.querySelector('mark');
                                if (markEl) markEl.scrollIntoView({behavior: "smooth", block: "center"});
                            } 
                            // 3: Anchor Link (#bab1)
                            else {
                                const anchorEl = document.getElementById(specificTarget);
                                if (anchorEl) {
                                    anchorEl.scrollIntoView({behavior: "smooth", block: "start"});
                                } else {
                                    // Fallback: cari by Name
                                    const namedEl = document.getElementsByName(specificTarget)[0];
                                    if(namedEl) namedEl.scrollIntoView({behavior: "smooth", block: "start"});
                                }
                            }
                        } else {
                            viewer.scrollTop = 0; // ke atas
                        }
                    }, 100);
                }
            });
    }

    function handleEnter(e) { if(e.key === 'Enter') searchInBook(); }

    function searchInBook() {
        const q = document.getElementById('search-input').value;
        const resBox = document.getElementById('search-results');
        if(!q) return;

        resBox.style.display = 'block';
        resBox.innerHTML = langSearchSearching; 

        fetch(`api/epub_api.php?action=search&book_id=${bookId}&query=${encodeURIComponent(q)}`)
            .then(r => r.json())
            .then(data => {
                if(data.success && data.results.length > 0) {
                    let foundText = langSearchFound.replace('%s', data.results.length);
                    let html = `<b>${foundText}</b><hr style="margin:5px 0; border:0; border-top:1px solid #eee;">`;
                    
                    data.results.forEach(r => {
                        const safeQuery = q.replace(/'/g, "\\'"); 
                        html += `
                        <div onclick="loadChapter(${r.chapter}, '${safeQuery}')" style="cursor:pointer; padding:8px; border-bottom:1px solid #eee;">
                            <strong style="color:#d4a75b;">📄 ${r.title}</strong><br>
                            <span style="color:#666;">${r.excerpt}</span>
                        </div>`;
                    });
                    resBox.innerHTML = html;
                } else {
                    resBox.innerHTML = langSearchNoResult; 
                }
            });
    }
    
    //  AMBIL POSISI SCROLL
    function getCurrentPosition() {
        const viewer = document.getElementById('viewer');
        if (viewer.scrollHeight - viewer.clientHeight === 0) return 0;
        return viewer.scrollTop / (viewer.scrollHeight - viewer.clientHeight);
    }

    // Simpan Bookmark + Posisi
    function saveBookmarkOnly() {
        const pos = getCurrentPosition();
        const fd = new FormData(); 
        fd.append('index', currentChapterIndex); 
        fd.append('note', ''); 
        fd.append('position', pos); 

        fetch(`api/epub_api.php?action=add_bookmark&book_id=${bookId}`, {method:'POST', body:fd})
        .then(r => r.json()).then(d => { 
            if(d.success) {
                loadBookmarks();
            } 
        });
    }

    // Simpan Note + Posisi
    function addNote() {
        const note = prompt(langNotePrompt); 
        if(note) {
            const pos = getCurrentPosition();
            const fd = new FormData(); 
            fd.append('index', currentChapterIndex); 
            fd.append('note', note);
            fd.append('position', pos); 

            fetch(`api/epub_api.php?action=add_bookmark&book_id=${bookId}`, {method:'POST', body:fd})
            .then(r => r.json()).then(d => { 
                if(d.success) {
                    loadBookmarks();
                    alert(langNoteSaved); 
                } 
            });
        }
    }

    function loadBookmarks() {
        fetch(`api/epub_api.php?action=get_bookmarks&book_id=${bookId}`)
        .then(r => r.json())
        .then(d => {
            const l = document.getElementById('bookmarks-list'); l.innerHTML='';
            
            if(d.success && d.bookmarks.length){
                d.bookmarks.forEach(bm => {
                    let isNote = (bm.note && bm.note.trim() !== "");
                    let icon = isNote ? '📝' : '🔖';
                    let titleStyle = isNote ? 'color:#333;' : 'color:#d4a75b; font-style:italic;';
                    let contentDisplay = isNote ? `<small style="color:#666; display:block; margin-top:3px;">"${bm.note}"</small>` : `<small style="color:#999; font-size:11px;">${langLabelBookmark}</small>`;

                    // Kirim bm.position (atau 0 jika null)
                    let scrollPos = bm.position ? bm.position : 0;

                    l.innerHTML += `
                    <div class="bookmark-item" onclick="loadChapter(${bm.chapter_index}, '${scrollPos}')" style="cursor:pointer; border-left:3px solid ${isNote ? '#ccc' : '#d4a75b'};">
                        <div style="display:flex; justify-content:space-between;">
                            <strong style="font-size:12px; ${titleStyle}">${icon} ${bm.chapter_title}</strong>
                            <span onclick="deleteBookmark(event, ${bm.id})" style="color:#999; font-weight:bold; font-size:14px;">&times;</span>
                        </div>
                        ${contentDisplay}
                    </div>`;
                });
            } else { 
                l.innerHTML = `<div style="padding:10px;color:#ccc; text-align:center;">${langNoNotes}</div>`; 
            }
        });
    }

    function deleteBookmark(e, id) {
        e.stopPropagation(); 
        if(!confirm(langNoteDelConfirm)) return; 
        
        const fd = new FormData(); fd.append('bookmark_id', id);
        fetch(`api/epub_api.php?action=delete_bookmark&book_id=${bookId}`, {method:'POST', body:fd}).then(() => loadBookmarks());
    }
</script>

</body>
</html>