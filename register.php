<?php
require_once "core/db.php";
require_once "core/lang_init.php"; 

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Ambil data dari form
    $nama = trim($_POST['nama']); 
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm = trim($_POST['confirm']);

    // Cek password sama
    if ($password !== $confirm) {
        $error = $lang['error_pass_mismatch']; 
    } else {
        // Cek username
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = $lang['error_username_taken']; 
        } else {
            // Hash password
            $hashed = password_hash($password, PASSWORD_DEFAULT);

            // Insert ke DB 
            $stmt = $conn->prepare("INSERT INTO users (name, username, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $nama, $username, $hashed);
            
            if ($stmt->execute()) {
                $success = $lang['success_register']; 
            } else {
                $error = $lang['error_register_fail'] . ": " . $stmt->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $lang['page_title_register'] ?></title>
    <link rel="stylesheet" href="styles/register.css?v=<?php echo time(); ?>">
</head>
<body>

    <div style="position: absolute; top: 20px; right: 20px; z-index: 10;">
        <a href="?lang=id" style="text-decoration:none; font-weight:bold; color: <?= ($current_lang == 'id') ? '#d4a75b' : '#ccc'; ?>">ID</a>
        <span style="color:#ddd"> | </span>
        <a href="?lang=en" style="text-decoration:none; font-weight:bold; color: <?= ($current_lang == 'en') ? '#d4a75b' : '#ccc'; ?>">EN</a>
    </div>

    <div class="register-container">
        
        <div class="text-signup">
            <img src="logo.jpeg" alt="Logo" class="register-logo" style="height: 60px; margin-bottom: 10px;">
            <h1><?= $lang['create_account'] ?></h1>
            <p style="font-size: 14px; color: #666; font-weight: normal;"><?= $lang['register_subtitle'] ?></p>
        </div>

        <div class="signup-card">
            <form action="register.php" method="POST">

                <?php if (!empty($error)): ?>
                    <div class="error-message">
                        ⚠️ <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="success-message">
                        ✅ <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>

                <div class="input-group">
                    <label><?= $lang['fullname_label'] ?></label>
                    <input class="textfield-signup" type="text" name="nama" placeholder="<?= $lang['fullname_placeholder'] ?>" required>
                </div>

                <div class="input-group">
                    <label><?= $lang['username_label'] ?></label> <input class="textfield-signup" type="text" name="username" placeholder="<?= $lang['username_placeholder'] ?>" required>
                </div>

                <div class="input-group">
                    <label><?= $lang['password_label'] ?></label> <input class="textfield-signup" type="password" name="password" placeholder="<?= $lang['password_placeholder'] ?>" required>
                </div>

                <div class="input-group">
                    <label><?= $lang['confirm_pass_label'] ?></label>
                    <input class="textfield-signup" type="password" name="confirm" placeholder="<?= $lang['confirm_pass_placeholder'] ?>" required>
                </div>

                <div>
                    <button class="signup-btn" type="submit"><?= $lang['register_btn'] ?></button>
                </div>

            </form>
        </div>

        <div class="footer-signup">
            <p><?= $lang['have_account'] ?> <a href="login.php"><?= $lang['login_link'] ?></a></p>
        </div>

    </div>

</body>
</html>