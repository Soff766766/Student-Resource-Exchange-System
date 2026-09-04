<?php
require "config.php";

if (currentUser() && ($_GET["new"] ?? "") !== "1") {
    header("Location: " . (currentUser()["role"] === "admin" ? "admin.php" : "student.php"));
    exit;
}

$flash = pullFlash();
$message = $flash["message"] ?? "";
$messageType = $flash["type"] ?? "error";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    verifyCsrf();
    $displayName = trim($_POST["display_name"] ?? "");
    $email = strtolower(trim($_POST["email"] ?? ""));
    $password = $_POST["password"] ?? "";
    $confirmPassword = $_POST["confirm_password"] ?? "";
    $role = ($_POST["role"] ?? "student") === "admin" ? "admin" : "student";
    $academicLevel = trim($_POST["academic_level"] ?? "");
    $allowedLevels = ["Grade 10", "Grade 11", "Grade 12", "College Year 1", "College Year 2", "College Year 3", "College Year 4"];

    if ($role === "student" && !in_array($academicLevel, $allowedLevels, true)) {
        $message = "Choose your academic level.";
    } elseif ($displayName === "" || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
        $message = "Enter your name, a valid email, and a password with at least 8 characters.";
    } elseif ($password !== $confirmPassword) {
        $message = "The passwords do not match.";
    } else {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $statement = $conn->prepare("INSERT INTO users (display_name, email, password_hash, role, academic_level) VALUES (?, ?, ?, ?, ?)");
        if ($role === "admin") $academicLevel = null;
        $statement->bind_param("sssss", $displayName, $email, $passwordHash, $role, $academicLevel);
        if ($statement->execute()) {
            $statement->close();
            header("Location: login.php?registered=1");
            exit;
        }
        $message = $statement->errno === 1062 ? "An account with this email already exists." : "The account could not be created. Please try again.";
        $statement->close();
        setFlash($message, "error");
        header("Location: register.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Register | Resource Exchange</title><link rel="stylesheet" href="style.css"></head>
<body><header class="topbar"><a class="brand" href="index.php">Student Resource Exchange</a><nav><a href="index.php">Welcome</a><a href="login.php">Log in</a><a class="active" href="register.php">Register</a></nav></header>
<main class="container auth-container"><section class="panel auth-panel"><p class="eyebrow">CREATE ACCOUNT</p><h1>Join the exchange.</h1><?php if ($message): ?><p class="notice <?= e($messageType) ?>"><?= e($message) ?></p><?php endif; ?><form method="post" class="form-grid"><input type="hidden" name="csrf_token" value="<?php echo csrfToken(); ?>"><label>Full name<input type="text" name="display_name" required maxlength="100" autocomplete="name"></label><label>Email address<input type="email" name="email" required maxlength="150" autocomplete="email"></label><label>Account type<select name="role" id="account-role"><option value="student">Student</option><option value="admin">Administrator</option></select></label><label id="academic-level-field">Academic level<select name="academic_level"><option value="">Choose your level</option><optgroup label="High School"><option>Grade 10</option><option>Grade 11</option><option>Grade 12</option></optgroup><optgroup label="College"><option>College Year 1</option><option>College Year 2</option><option>College Year 3</option><option>College Year 4</option></optgroup></select></label><label>Password<input type="password" name="password" required minlength="8" autocomplete="new-password"><small>Use at least 8 characters.</small></label><label>Confirm password<input type="password" name="confirm_password" required minlength="8" autocomplete="new-password"></label><button type="submit">Create account</button></form><p class="muted auth-footer">Already registered? <a class="text-link" href="login.php">Log in</a>.</p></section></main><script>const role=document.getElementById('account-role'), level=document.querySelector('[name="academic_level"]'); function toggleLevel(){const student=role.value==='student'; level.required=student; document.getElementById('academic-level-field').style.display=student?'grid':'none';} role.addEventListener('change',toggleLevel); toggleLevel();</script></body></html>
