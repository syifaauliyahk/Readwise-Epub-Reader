<?php
return [
    // LOGIN PAGE 
    'page_title_login' => 'Login - ReadMe',
    'welcome_back' => 'Welcome Back!',
    'login_subtitle' => 'Please login to your account',
    'username_label' => 'Username',
    'password_label' => 'Password',
    'username_placeholder' => 'Enter your username',
    'password_placeholder' => 'Enter your password',
    'login_btn' => 'LOGIN',
    'no_account' => 'Don\'t have an account?',
    'sign_up' => 'Sign Up',

    // Error Messages
    'error_wrong_pass' => 'Wrong password!',
    'error_user_not_found' => 'Username not found!',

    
    // REGISTER PAGE
    'page_title_register' => 'Sign Up - ReadMe',
    'create_account' => 'Create Account',
    'register_subtitle' => 'Join our community today',
    'fullname_label' => 'Full Name',
    'fullname_placeholder' => 'Enter your full name',
    'confirm_pass_label' => 'Confirm Password',
    'confirm_pass_placeholder' => 'Repeat password',
    'register_btn' => 'SIGN UP',
    'have_account' => 'Already have an account?',
    'login_link' => 'Login Here',

    // Register Messages
    'error_pass_mismatch' => 'Passwords do not match!',
    'error_username_taken' => 'Username is already taken!',
    'error_register_fail' => 'Registration failed, please try again.',
    'success_register' => 'Account created successfully! Please login.',

    
    // INDEX PAGE
    'library_title' => 'My Library',
    'books_available' => 'Books Available',
    'search_library_placeholder' => 'Search by title or author...',
    'no_books' => 'No Books Yet',
    'upload_first_hint' => 'Upload your first book from the sidebar!',
    'by_author' => 'by',
    'read_action' => 'READ',
    'delete_confirm' => 'Delete this book?',
    'delete_warning' => 'This action cannot be undone.',
    'delete_success' => 'Deleted successfully!',
    'delete_failed' => 'Delete failed!',

    // Sidebar Upload & Filter
    'upload_title' => 'Upload New Book',
    'book_title_label' => 'Book Title',
    'book_title_ph' => 'Enter title...',
    'author_label' => 'Author',
    'author_ph' => 'Enter author...',
    'category_label' => 'Category',
    'select_category' => '-- Select --',
    'cover_label' => 'Cover Image',
    'epub_label' => 'EPUB File (Required)',
    'select_image' => 'Select Image',
    'select_epub' => 'Select EPUB',
    'upload_submit' => 'UPLOAD BOOK',
    
    'filter_title' => 'Filter & Sort',
    'filter_category_label' => 'By Category',
    'show_all' => 'Show All',
    'sort_label' => 'Sort By',
    'sort_newest' => 'Newest First',
    'sort_oldest' => 'Oldest First',
    'sort_az' => 'Title A-Z',
    'logout' => 'Logout',

    
    // UPLOAD PAGE
    'page_title_upload' => 'Upload Status - ReadMe',
    'status_info_title' => 'ℹ️ Status Info',
    'status_info_desc' => 'This page shows the result of your EPUB upload process. If failed, please check the error log below.',
    
    // Upload Status Messages
    'status_success_title' => 'Upload Successful!',
    'status_success_msg' => 'Book <strong>%s</strong> has been successfully added to your library.',
    'status_failed_title' => 'Upload Failed',
    'status_failed_msg' => 'An error occurred while processing the file.',
    
    'btn_back_library' => 'Back to Library',
    'btn_read_now' => '📖 Read Now',
    'btn_try_again' => 'Try Again',
    
    'view_log' => 'View System Log',
    'error_log_label' => 'Error Log:',
    
    // Log Messages (Backend)
    'log_epub_uploaded' => 'EPUB uploaded successfully.',
    'log_epub_extracted' => 'EPUB extracted successfully.',
    'log_db_success' => 'SUCCESS: Data saved to Database.',
    'log_db_error' => 'DB ERROR: ',
    'log_move_error' => 'ERROR: Failed to move uploaded file.',
    'log_invalid_format' => 'ERROR: File is not a valid .epub format',
    'log_system_error' => 'SYSTEM ERROR CODE: ',

    
    // READER PAGE
    'page_title_reader' => 'Reading: %s - ReadMe',
    'back_to_library' => 'Back to Library',
    'toc_title' => 'Table of Contents',
    'toc_loading' => 'Loading chapters...',
    'search_book_title' => 'Search in Book',
    'search_book_placeholder' => 'Type keyword...',
    'search_go_btn' => 'Go',
    'search_searching' => 'Searching...',
    'search_found' => 'Found %s results:',
    'search_no_results' => 'No results found.',
    
    'bookmarks_title' => 'Notes & Bookmarks',
    'add_note_btn' => '+ Add New Note',
    'notes_loading' => 'Loading notes...',
    'no_notes' => 'No notes yet.',
    'note_prompt' => 'Type your note:',
    'note_saved' => 'Note saved!',
    'note_delete_confirm' => 'Delete this note?',
    
    'reader_loading' => 'Loading content...',
    'reader_wait' => 'Wait...',
    'reader_error' => 'Error: ',

    
    // DELETE BOOK PROCESS 
    'err_not_logged_in' => 'You are not logged in.',
    'err_invalid_method' => 'Invalid request method.',
    'err_no_book_id' => 'No book ID provided.',
    'err_book_not_found' => 'Book not found or you do not own this book.',
    'msg_delete_success' => 'Book deleted successfully.',
    'err_delete_db' => 'Failed to delete from database.',

    
    // API MESSAGES 
    'api_unauthorized' => 'Unauthorized.',
    'api_missing_book_id' => 'Missing Book ID.',
    'api_book_not_found' => 'Book not found.',
    'api_book_files_missing' => 'Book files missing.',
    'api_chapter_out_of_range' => 'Chapter index out of range or file missing.',
    'api_bookmark_saved' => 'Note saved!',
    'api_delete_failed' => 'Delete failed.',
    'api_invalid_action' => 'Invalid action.',
    'api_db_error' => 'Database Error: ',

    
    // DATABASE 
    'db_connection_failed' => 'Connection failed: ',

    
    // EPUB CLASS INTERNAL
    'epub_untitled' => 'Untitled',
    'epub_unknown_author' => 'Unknown Author',
    'epub_chapter_prefix' => 'Chapter ',
    
    // Error Reader
    'epub_err_file_not_found_title' => 'File Not Found',
    'epub_err_file_not_found_msg' => 'File: %s is missing.',
    'epub_err_read_title' => 'Read Error',
    'epub_err_read_msg' => 'Cannot read file content.',

    
    // READER PAGE (UPDATE) 
    'bookmarks_title' => 'Bookmarks & Notes',
    'btn_bookmark' => '🔖 Bookmark This Page',
    'btn_note' => '📝 Add Note',
    'msg_bookmark_added' => 'Bookmark saved!',
    'label_bookmark' => 'Bookmark',
];
?>
