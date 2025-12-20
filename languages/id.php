<?php
return [
    // LOGIN PAGE
    'page_title_login' => 'Masuk - ReadMe',
    'welcome_back' => 'Selamat Datang Kembali!',
    'login_subtitle' => 'Silakan masuk ke akun Anda',
    'username_label' => 'Nama Pengguna',
    'password_label' => 'Kata Sandi',
    'username_placeholder' => 'Masukkan nama pengguna',
    'password_placeholder' => 'Masukkan kata sandi',
    'login_btn' => 'MASUK',
    'no_account' => 'Belum punya akun?',
    'sign_up' => 'Daftar',
    
    // Error Messages
    'error_wrong_pass' => 'Kata sandi salah!',
    'error_user_not_found' => 'Nama pengguna tidak ditemukan!',

    
    // REGISTER PAGE
    'page_title_register' => 'Daftar Akun - ReadMe',
    'create_account' => 'Buat Akun Baru',
    'register_subtitle' => 'Bergabunglah dengan komunitas kami sekarang',
    'fullname_label' => 'Nama Lengkap',
    'fullname_placeholder' => 'Masukkan nama lengkap Anda',
    'confirm_pass_label' => 'Konfirmasi Kata Sandi',
    'confirm_pass_placeholder' => 'Ulangi kata sandi',
    'register_btn' => 'DAFTAR',
    'have_account' => 'Sudah punya akun?',
    'login_link' => 'Masuk di sini',

    // Register Messages
    'error_pass_mismatch' => 'Kata sandi tidak cocok!',
    'error_username_taken' => 'Username sudah digunakan!',
    'error_register_fail' => 'Gagal mendaftar, silakan coba lagi.',
    'success_register' => 'Akun berhasil dibuat! Silakan login.',

    
    // INDEX PAGE
    'library_title' => 'Pustaka Saya',
    'books_available' => 'Buku Tersedia',
    'search_library_placeholder' => 'Cari judul atau penulis...',
    'no_books' => 'Belum Ada Buku',
    'upload_first_hint' => 'Upload buku pertamamu dari sidebar!',
    'by_author' => 'oleh',
    'read_action' => 'BACA',
    'delete_confirm' => 'Hapus buku ini?',
    'delete_warning' => 'Tindakan ini tidak bisa dibatalkan.',
    'delete_success' => 'Berhasil dihapus!',
    'delete_failed' => 'Gagal menghapus!',
    
    // Sidebar Upload & Filter
    'upload_title' => 'Upload Buku Baru',
    'book_title_label' => 'Judul Buku',
    'book_title_ph' => 'Masukkan judul...',
    'author_label' => 'Penulis',
    'author_ph' => 'Masukkan nama penulis...',
    'category_label' => 'Kategori (Prodi)',
    'select_category' => '-- Pilih Kategori --',
    'cover_label' => 'Gambar Sampul',
    'epub_label' => 'File EPUB (Wajib)',
    'select_image' => 'Pilih Gambar',
    'select_epub' => 'Pilih EPUB',
    'upload_submit' => 'UPLOAD BUKU',
    
    'filter_title' => 'Filter & Urutkan',
    'filter_category_label' => 'Berdasarkan Kategori',
    'show_all' => 'Tampilkan Semua',
    'sort_label' => 'Urutkan',
    'sort_newest' => 'Terbaru',
    'sort_oldest' => 'Terlama',
    'sort_az' => 'Judul A-Z',
    'logout' => 'Keluar',

    
    // UPLOAD PAGE
    'page_title_upload' => 'Status Upload - ReadMe',
    'status_info_title' => 'ℹ️ Info Status',
    'status_info_desc' => 'Halaman ini menampilkan hasil proses upload file EPUB Anda. Jika gagal, silakan cek log error di bawah.',
    
    // Upload Status Messages
    'status_success_title' => 'Upload Berhasil!',
    'status_success_msg' => 'Buku <strong>%s</strong> berhasil ditambahkan ke perpustakaan Anda.',
    'status_failed_title' => 'Upload Gagal',
    'status_failed_msg' => 'Terjadi kesalahan saat memproses file.',
    
    'btn_back_library' => 'Ke Library',
    'btn_read_now' => '📖 Baca Sekarang',
    'btn_try_again' => 'Coba Lagi',
    
    'view_log' => 'Lihat Log Sistem',
    'error_log_label' => 'Log Error:',
    
    // Log Messages (Backend)
    'log_epub_uploaded' => 'EPUB berhasil diupload.',
    'log_epub_extracted' => 'EPUB berhasil diekstrak.',
    'log_db_success' => 'SUKSES: Data tersimpan di Database.',
    'log_db_error' => 'DB ERROR: ',
    'log_move_error' => 'ERROR: Gagal memindahkan file.',
    'log_invalid_format' => 'ERROR: File bukan format .epub',
    'log_system_error' => 'ERROR SYSTEM CODE: ',

    
    // READER PAGE
    'page_title_reader' => 'Membaca: %s - ReadMe',
    'back_to_library' => 'Kembali ke Pustaka',
    'toc_title' => 'Daftar Isi',
    'toc_loading' => 'Memuat bab...',
    'search_book_title' => 'Cari di Buku',
    'search_book_placeholder' => 'Ketik kata kunci...',
    'search_go_btn' => 'Cari',
    'search_searching' => 'Mencari...',
    'search_found' => 'Ditemukan %s hasil:',
    'search_no_results' => 'Tidak ditemukan hasil.',
    
    'bookmarks_title' => 'Catatan & Bookmark',
    'add_note_btn' => '+ Tambah Catatan',
    'notes_loading' => 'Memuat catatan...',
    'no_notes' => 'Belum ada catatan.',
    'note_prompt' => 'Tulis catatan Anda:',
    'note_saved' => 'Catatan tersimpan!',
    'note_delete_confirm' => 'Hapus catatan ini?',
    
    'reader_loading' => 'Memuat konten...',
    'reader_wait' => 'Tunggu...',
    'reader_error' => 'Error: ',

    
    // DELETE BOOK PROCESS
    'err_not_logged_in' => 'Anda belum login.',
    'err_invalid_method' => 'Metode request tidak valid.',
    'err_no_book_id' => 'ID buku tidak ditemukan.',
    'err_book_not_found' => 'Buku tidak ditemukan atau Anda bukan pemilik buku ini.',
    'msg_delete_success' => 'Buku berhasil dihapus.',
    'err_delete_db' => 'Gagal menghapus data dari database.',

    
    // API MESSAGES
    'api_unauthorized' => 'Tidak diizinkan.',
    'api_missing_book_id' => 'ID Buku tidak ditemukan.',
    'api_book_not_found' => 'Buku tidak ditemukan.',
    'api_book_files_missing' => 'File buku hilang atau rusak.',
    'api_chapter_out_of_range' => 'Bab tidak ditemukan atau di luar jangkauan.',
    'api_bookmark_saved' => 'Catatan tersimpan!',
    'api_delete_failed' => 'Gagal menghapus.',
    'api_invalid_action' => 'Aksi tidak valid.',
    'api_db_error' => 'Database Error: ',

    
    // DATABASE
    'db_connection_failed' => 'Koneksi database gagal: ',

    
    // EPUB CLASS INTERNAL
    'epub_untitled' => 'Tanpa Judul',
    'epub_unknown_author' => 'Penulis Tidak Diketahui',
    'epub_chapter_prefix' => 'Bab ',
    
    // Error Reader
    'epub_err_file_not_found_title' => 'File Tidak Ditemukan',
    'epub_err_file_not_found_msg' => 'File: %s hilang.',
    'epub_err_read_title' => 'Gagal Membaca',
    'epub_err_read_msg' => 'Tidak dapat membaca konten file.',

    
    // READER PAGE (UPDATE) 
    'bookmarks_title' => 'Penanda & Catatan', 
    'btn_bookmark' => '🔖 Tandai Halaman Ini', 
    'btn_note' => '📝 Tambah Catatan',     
    'msg_bookmark_added' => 'Bookmark tersimpan!',
    'label_bookmark' => 'Penanda Buku',

];

?>
