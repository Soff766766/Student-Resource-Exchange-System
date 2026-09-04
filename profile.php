<?php
require "config.php";
$user = requireLogin();
$id = (int) $user["id"];
$flash = pullFlash();
$message = $flash["message"] ?? "";
$type = $flash["type"] ?? "success";
$avatarChoices = [
    "default_avatars/hamster-sad.jpg" => "Sad hamster",
    "default_avatars/hamster-shirt.jpg" => "Happy hamster",
    "default_avatars/hamster-diver.jpg" => "Diver hamster",
    "default_avatars/hamster-cap.jpg" => "Cap hamster",
    "default_avatars/hamster-unicorn.jpg" => "Unicorn hamster"
];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    verifyCsrf();
    $action = $_POST["action"] ?? "update";

    if ($action === "change_password") {
        $currentPassword = $_POST["current_password"] ?? "";
        $newPassword = $_POST["new_password"] ?? "";
        $confirmPassword = $_POST["confirm_password"] ?? "";
        $check = $conn->prepare("SELECT password_hash FROM users WHERE id = ?");
        $check->bind_param("i", $id); $check->execute(); $account = $check->get_result()->fetch_assoc(); $check->close();
        if (!$account || !password_verify($currentPassword, $account["password_hash"])) { $message = "The current password is incorrect."; $type = "error"; }
        elseif (strlen($newPassword) < 8) { $message = "The new password must contain at least 8 characters."; $type = "error"; }
        elseif ($newPassword !== $confirmPassword) { $message = "The new passwords do not match."; $type = "error"; }
        else { $hash = password_hash($newPassword, PASSWORD_DEFAULT); $save = $conn->prepare("UPDATE users SET password_hash = ? WHERE id = ?"); $save->bind_param("si", $hash, $id); if ($save->execute()) $message = "Password updated successfully."; else { $message = "The password could not be updated."; $type = "error"; } $save->close(); }
    } elseif ($action === "delete_account") {
        $password = $_POST["current_password"] ?? "";
        if ($user["role"] === "admin") {
            $message = "Administrator accounts cannot be deleted from this page. Contact the system owner.";
            $type = "error";
        } else {
            $check = $conn->prepare("SELECT password_hash FROM users WHERE id = ?");
            $check->bind_param("i", $id);
            $check->execute();
            $account = $check->get_result()->fetch_assoc();
            $check->close();
            if (!$account || !password_verify($password, $account["password_hash"])) {
                $message = "The current password is incorrect. Your account was not deleted.";
                $type = "error";
            } else {
                $paths = $conn->prepare("SELECT file_path FROM resources WHERE submitted_by = ?");
                $paths->bind_param("i", $id);
                $paths->execute();
                $files = $paths->get_result()->fetch_all(MYSQLI_ASSOC);
                $paths->close();
                $conn->begin_transaction();
                try {
                    $deleteReports = $conn->prepare("DELETE FROM resource_reports WHERE reported_by = ? OR resource_id IN (SELECT id FROM resources WHERE submitted_by = ?)");
                    $deleteReports->bind_param("ii", $id, $id); $deleteReports->execute(); $deleteReports->close();
                    $deleteHistory = $conn->prepare("DELETE h FROM moderation_history h JOIN resources r ON r.id = h.resource_id WHERE r.submitted_by = ?");
                    $deleteHistory->bind_param("i", $id); $deleteHistory->execute(); $deleteHistory->close();
                    $deleteResources = $conn->prepare("DELETE FROM resources WHERE submitted_by = ?");
                    $deleteResources->bind_param("i", $id); $deleteResources->execute(); $deleteResources->close();
                    $deleteUser = $conn->prepare("DELETE FROM users WHERE id = ? AND role = 'student'");
                    $deleteUser->bind_param("i", $id); $deleteUser->execute();
                    if ($deleteUser->affected_rows !== 1) throw new Exception("Account deletion failed");
                    $deleteUser->close();
                    $conn->commit();
                    foreach ($files as $file) { if (str_starts_with($file["file_path"], "uploads/")) @unlink(__DIR__ . "/" . $file["file_path"]); }
                    session_unset(); session_destroy();
                    header("Location: index.php?account_deleted=1"); exit;
                } catch (Throwable $error) {
                    $conn->rollback();
                    $message = "The account could not be deleted. Please try again.";
                    $type = "error";
                }
            }
        }
    } else {
        $name = trim($_POST["display_name"] ?? "");
        $email = strtolower(trim($_POST["email"] ?? ""));
        $academicLevel = trim($_POST["academic_level"] ?? ($user["academic_level"] ?? ""));
        $visibility = ($_POST["profile_visibility"] ?? ($user["profile_visibility"] ?? "public")) === "private" ? "private" : "public";
        $allowedLevels = ["Grade 10", "Grade 11", "Grade 12", "College Year 1", "College Year 2", "College Year 3", "College Year 4"];
        $newPassword = $_POST["new_password"] ?? "";
        $avatarPath = $_POST["avatar_choice"] ?? ($user["avatar_path"] ?? null);
        $newAvatarPath = null;
        if (!isset($avatarChoices[$avatarPath]) && !preg_match('/^uploads\/avatars\/[A-Za-z0-9._-]+$/', (string) $avatarPath)) $avatarPath = $user["avatar_path"] ?? null;

        if ($name === "" || !filter_var($email, FILTER_VALIDATE_EMAIL) || ($user["role"] === "student" && !in_array($academicLevel, $allowedLevels, true))) { $message = "Enter a valid name and email address."; $type = "error"; }
        elseif ($newPassword !== "" && strlen($newPassword) < 8) { $message = "The new password must contain at least 8 characters."; $type = "error"; }
        elseif (isset($_FILES["avatar"]) && $_FILES["avatar"]["error"] !== UPLOAD_ERR_NO_FILE) {
            $photo = $_FILES["avatar"];
            $allowed = ["image/jpeg" => "jpg", "image/png" => "png", "image/gif" => "gif", "image/webp" => "webp"];
            $imageInfo = $photo["error"] === UPLOAD_ERR_OK ? @getimagesize($photo["tmp_name"]) : false;
            $mime = $imageInfo["mime"] ?? "";
            if ($photo["error"] !== UPLOAD_ERR_OK || $photo["size"] > 2 * 1024 * 1024 || !isset($allowed[$mime])) { $message = "Choose a JPG, PNG, GIF, or WEBP image no larger than 2MB."; $type = "error"; }
            else { $newAvatarPath = "uploads/avatars/" . bin2hex(random_bytes(16)) . "." . $allowed[$mime]; if (!move_uploaded_file($photo["tmp_name"], __DIR__ . "/" . $newAvatarPath)) { $message = "The profile photo could not be saved."; $type = "error"; } else $avatarPath = $newAvatarPath; }
        }
        if ($type === "success") {
            $check = $conn->prepare("SELECT id FROM users WHERE email = ? AND id <> ?"); $check->bind_param("si", $email, $id); $check->execute(); $duplicate = $check->get_result()->fetch_assoc(); $check->close();
            if ($duplicate) { $message = "That email address is already in use."; $type = "error"; }
            else {
                if ($newPassword !== "") { $hash = password_hash($newPassword, PASSWORD_DEFAULT); $save = $conn->prepare("UPDATE users SET display_name = ?, email = ?, academic_level = ?, profile_visibility = ?, password_hash = ?, avatar_path = ? WHERE id = ?"); $save->bind_param("ssssssi", $name, $email, $academicLevel, $visibility, $hash, $avatarPath, $id); }
                else { $save = $conn->prepare("UPDATE users SET display_name = ?, email = ?, academic_level = ?, profile_visibility = ?, avatar_path = ? WHERE id = ?"); $save->bind_param("sssssi", $name, $email, $academicLevel, $visibility, $avatarPath, $id); }
                if ($save->execute()) {
                    $oldAvatar = $user["avatar_path"] ?? null; if ($newAvatarPath && $oldAvatar && str_starts_with($oldAvatar, "uploads/avatars/")) @unlink(__DIR__ . "/" . $oldAvatar);
                    $_SESSION["user"]["display_name"] = $name; $_SESSION["user"]["email"] = $email; $_SESSION["user"]["academic_level"] = $academicLevel; $_SESSION["user"]["profile_visibility"] = $visibility; $_SESSION["user"]["avatar_path"] = $avatarPath; $user = $_SESSION["user"]; $message = "Profile updated successfully.";
                } else { if ($newAvatarPath) @unlink(__DIR__ . "/" . $newAvatarPath); $message = "The profile could not be updated."; $type = "error"; }
                $save->close();
            }
        } elseif ($newAvatarPath) @unlink(__DIR__ . "/" . $newAvatarPath);
    }

    setFlash($message, $type);
    $redirect = $action === "change_password" ? "profile.php?password=1" : ($action === "delete_account" ? "profile.php?delete=1" : "profile.php?edit=1");
    header("Location: " . $redirect);
    exit;
}
?>
<?php $editMode = isset($_GET["edit"]); $passwordMode = isset($_GET["password"]); $deleteMode = isset($_GET["delete"]); ?>
<!doctype html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Profile | Resource Exchange</title><link rel="stylesheet" href="style.css?v=profile-visibility-4"></head><body>
<header class="topbar"><a class="brand" href="index.php">Student Resource Exchange</a><nav><?php if ($user["role"] === "admin"): ?><a href="admin.php">Admin page</a><?php else: ?><a href="student.php">Student page</a><?php endif; ?><a href="users.php">Members</a><a class="active" href="profile.php">Profile</a><a href="logout.php">Log out</a></nav></header>
<main class="container profile-layout"><section class="panel profile-content"><p class="eyebrow">MY PROFILE</p><h1><?= $editMode ? "Update profile" : ($passwordMode ? "Update password" : ($deleteMode ? "Delete profile" : "Profile")) ?></h1><p class="muted profile-intro"><?= $editMode ? "Update your details and choose a profile picture." : ($passwordMode ? "Change your password securely." : ($deleteMode ? "Confirm your password before permanently deleting your account." : "View your profile information and manage your account settings.")) ?></p><?php if ($message): ?><p class="notice <?= e($type) ?>"><?= e($message) ?></p><?php endif; ?>
<?php if (!$editMode && !$passwordMode && !$deleteMode): ?><section class="profile-overview"><div class="overview-avatar"><img src="<?= e(avatarUrl($user["avatar_path"] ?? null)) ?>" alt="Your current profile picture"></div><div class="overview-info"><p class="eyebrow">ACCOUNT HOLDER</p><h2><?= e($user["display_name"]) ?></h2><p><?= e($user["email"]) ?></p><p><strong>Academic level:</strong> <?= e($user["academic_level"] ?? "Not provided") ?></p><p><strong>Profile visibility:</strong> <?= ($user["profile_visibility"] ?? "public") === "private" ? "Private" : "Public" ?></p><span class="account-badge"><?= e(ucfirst($user["role"])) ?> account</span></div></section><div class="profile-actions"><a class="download" href="profile.php?edit=1">Update profile</a><a class="download" href="profile.php?password=1">Update password</a><?php if ($user["role"] !== "admin"): ?><a class="download danger" href="profile.php?delete=1">Delete profile</a><?php endif; ?></div><?php elseif ($editMode): ?><form method="post" enctype="multipart/form-data" class="stack-form"><input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>"><input type="hidden" name="action" value="update"><section class="settings-section"><h2>Account details</h2><div class="settings-grid"><label>Display name<input name="display_name" value="<?= e($user["display_name"]) ?>" required></label><label>Email address<input type="email" name="email" value="<?= e($user["email"]) ?>" required></label><label>Academic level<select name="academic_level" required><option value="">Choose your level</option><optgroup label="High School"><option <?= ($user["academic_level"] ?? "") === "Grade 10" ? "selected" : "" ?>>Grade 10</option><option <?= ($user["academic_level"] ?? "") === "Grade 11" ? "selected" : "" ?>>Grade 11</option><option <?= ($user["academic_level"] ?? "") === "Grade 12" ? "selected" : "" ?>>Grade 12</option></optgroup><optgroup label="College"><option <?= ($user["academic_level"] ?? "") === "College Year 1" ? "selected" : "" ?>>College Year 1</option><option <?= ($user["academic_level"] ?? "") === "College Year 2" ? "selected" : "" ?>>College Year 2</option><option <?= ($user["academic_level"] ?? "") === "College Year 3" ? "selected" : "" ?>>College Year 3</option><option <?= ($user["academic_level"] ?? "") === "College Year 4" ? "selected" : "" ?>>College Year 4</option></optgroup></select></label><label>Profile visibility<select name="profile_visibility"><option value="public" <?= ($user["profile_visibility"] ?? "public") === "public" ? "selected" : "" ?>>Public — visible to members</option><option value="private" <?= ($user["profile_visibility"] ?? "public") === "private" ? "selected" : "" ?>>Private — hidden from Members</option></select><small>Private profiles are not listed publicly.</small></label></div></section><section class="settings-section"><h2>Profile picture</h2><p class="muted">Choose one of the animal avatars or upload your own photo.</p><div class="field-label">Animal avatar<div class="avatar-choices professional-avatars"><?php foreach ($avatarChoices as $path => $label): ?><label class="avatar-option"><input type="radio" name="avatar_choice" value="<?= e($path) ?>" <?= ($user["avatar_path"] ?? "") === $path ? "checked" : "" ?>><img class="avatar-choice-image" src="<?= e($path) ?>" alt="<?= e($label) ?>" width="56" height="56" loading="lazy"><span><?= e($label) ?></span></label><?php endforeach; ?></div></div><label>Upload a personal photo<input type="file" name="avatar" accept="image/jpeg,image/png,image/gif,image/webp"><small>JPG, PNG, GIF, or WEBP. Maximum 2MB.</small></label></section><div class="profile-actions"><button type="submit">Save changes</button><a class="download secondary-action" href="profile.php">Back to Profile</a></div></form><?php elseif ($passwordMode): ?><form method="post" class="stack-form password-form"><input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>"><input type="hidden" name="action" value="change_password"><section class="settings-section"><h2>Update password</h2><label>Current password<input type="password" name="current_password" required autocomplete="current-password"></label><label>New password<input type="password" name="new_password" minlength="8" required autocomplete="new-password"><small>Use at least 8 characters.</small></label><label>Confirm new password<input type="password" name="confirm_password" minlength="8" required autocomplete="new-password"></label></section><div class="profile-actions"><button type="submit">Update password</button><a class="text-link" href="profile.php">Cancel</a></div></form><?php else: ?><section class="delete-confirm"><h2>Delete profile</h2><p class="muted">This permanently deletes your account and your submitted resources. This action cannot be undone.</p><form method="post" class="stack-form" onsubmit="return confirm('Delete your profile permanently?');"><input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>"><input type="hidden" name="action" value="delete_account"><label>Current password<input type="password" name="current_password" required autocomplete="current-password"></label><div class="profile-actions"><button type="submit" class="danger">Delete profile</button><a class="text-link" href="profile.php">Cancel</a></div></form></section><?php endif; ?></section></main></body></html>
