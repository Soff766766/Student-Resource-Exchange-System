<?php
require "config.php";

if (currentUser()) {
    header("Location: " . (currentUser()["role"] === "admin" ? "admin.php" : "student.php"));
    exit;
}

$flash = pullFlash();
$message = $flash["message"] ?? "";
$messageType = $flash["type"] ?? "error";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    verifyCsrf();

    if (loginBlocked()) {
        setFlash("Too many failed attempts. Please wait five minutes and try again.", "error");
        header("Location: login.php");
        exit;
    } else {
        $email = strtolower(trim($_POST["email"] ?? ""));
        $password = $_POST["password"] ?? "";
        $statement = $conn->prepare("SELECT id, display_name, email, password_hash, role, academic_level, profile_visibility, avatar_path FROM users WHERE email = ? LIMIT 1");
        $statement->bind_param("s", $email);
        $statement->execute();
        $user = $statement->get_result()->fetch_assoc();
        $statement->close();

        if ($user && password_verify($password, $user["password_hash"])) {
            clearLoginFailures();
            session_regenerate_id(true);
            unset($user["password_hash"]);
            $_SESSION["user"] = $user;
            header("Location: " . ($user["role"] === "admin" ? "admin.php" : "student.php"));
            exit;
        }

        recordLoginFailure();
        setFlash("The email or password is incorrect.", "error");
        header("Location: login.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Log in | Resource Exchange</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header class="topbar">
    <a class="brand" href="index.php">Student Resource Exchange</a>
    <nav><a href="index.php">Welcome</a><a class="active" href="login.php">Log in</a><a href="register.php">Register</a></nav>
  </header>
  <main class="container auth-container">
    <section class="panel auth-panel">
      <p class="eyebrow">ACCOUNT ACCESS</p>
      <h1>Log in to continue.</h1>
      <?php if ($message): ?><p class="notice <?= e($messageType) ?>"><?= e($message) ?></p><?php endif; ?>
      <form method="post" class="form-grid">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
        <label>Email address<input type="email" name="email" required autocomplete="email"></label>
        <label>Password<input type="password" name="password" required autocomplete="current-password"></label>
        <button type="submit">Log in</button>
      </form>
      <p class="muted auth-footer">New to the exchange? <a class="text-link" href="register.php">Create an account</a>.</p>
    </section>
  </main>
</body>
</html>
