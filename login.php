<?php

require_once "core/db.php"; 
require_once "core/lang_init.php"; 

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    // Query user
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_pw);
        $stmt->fetch();

        if (password_verify($password, $hashed_pw)) {
            $_SESSION["user_id"] = $id;
            header("Location: index.php");
            exit();
        } else {
            $error = $lang['error_wrong_pass'];
        }
    } else {
        $error = $lang['error_user_not_found'];
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $lang['page_title_login'] ?></title>
    <link rel="stylesheet" href="styles/login.css?v=<?php echo time(); ?>">
</head>
<body>

    <div style="position: absolute; top: 20px; right: 20px; z-index: 10;">
        <a href="?lang=id" style="text-decoration:none; font-weight:bold; color: <?= ($current_lang == 'id') ? '#d4a75b' : '#ccc'; ?>">ID</a>
        <span style="color:#ddd"> | </span>
        <a href="?lang=en" style="text-decoration:none; font-weight:bold; color: <?= ($current_lang == 'en') ? '#d4a75b' : '#ccc'; ?>">EN</a>
    </div>

    <div class="login-container">
        
        <div class="text-login">
            <img src="logo.jpeg" alt="Logo" class="login-logo" style="height: 60px; margin-bottom: 10px;">
            <h1><?= $lang['welcome_back'] ?></h1>
            <p style="font-size: 14px; color: #666; font-weight: normal;"><?= $lang['login_subtitle'] ?></p>
        </div>

        <div class="login-card">
            <form action="login.php" method="POST">

            <?php if (!empty($error)): ?>
                <div class="error-message">
                    ⚠️ <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <div class="input-group">
                <label><?= $lang['username_label'] ?></label>
                <input class="textfield-login" type="text" name="username" placeholder="<?= $lang['username_placeholder'] ?>" required>
            </div>

            <div class="input-group">
                <label><?= $lang['password_label'] ?></label>
                <input class="textfield-login" type="password" name="password" placeholder="<?= $lang['password_placeholder'] ?>" required>
            </div>

            <div>
                <button class="login-btn" type="submit"><?= $lang['login_btn'] ?></button>
            </div>

            </form>
        </div>

        <div class="footer-login">
            <p><?= $lang['no_account'] ?> <a href="register.php"><?= $lang['sign_up'] ?></a></p>
        </div>

    </div>

</body>
</html>