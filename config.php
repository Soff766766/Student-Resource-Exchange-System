<?php
$secureCookie = !empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off";
session_set_cookie_params([
    "lifetime" => 0,
    "path" => "/",
    "secure" => $secureCookie,
    "httponly" => true,
    "samesite" => "Lax"
]);
session_start();

$host = "localhost";
$username = "root";
$password = "";
$database = "student_resource_exchange";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, "UTF-8");
}

function avatarUrl(?string $path): string
{
    if (!$path || !preg_match('/^(uploads\/avatars|default_avatars)\/[A-Za-z0-9._-]+$/', $path)) {
        return "default-avatar.svg";
    }
    return $path;
}

function csrfToken(): string
{
    if (empty($_SESSION["csrf_token"])) {
        $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
    }
    return $_SESSION["csrf_token"];
}

function verifyCsrf(): void
{
    $submitted = $_POST["csrf_token"] ?? "";
    $stored = $_SESSION["csrf_token"] ?? "";
    if (!$stored || !$submitted || !hash_equals($stored, $submitted)) {
        http_response_code(419);
        exit("Security check failed. Please go back and try again.");
    }
}

function loginBlocked(): bool
{
    return !empty($_SESSION["login_lock_until"]) && $_SESSION["login_lock_until"] > time();
}

function recordLoginFailure(): void
{
    $_SESSION["login_failures"] = ($_SESSION["login_failures"] ?? 0) + 1;
    if ($_SESSION["login_failures"] >= 5) {
        $_SESSION["login_lock_until"] = time() + 300;
        $_SESSION["login_failures"] = 0;
    }
}

function clearLoginFailures(): void
{
    unset($_SESSION["login_failures"], $_SESSION["login_lock_until"]);
}

function setFlash(string $message, string $type = "success"): void
{
    $_SESSION["flash"] = ["message" => $message, "type" => $type];
}

function pullFlash(): ?array
{
    $flash = $_SESSION["flash"] ?? null;
    unset($_SESSION["flash"]);
    return is_array($flash) ? $flash : null;
}

function currentUser(): ?array
{
    $user = $_SESSION["user"] ?? null;
    if (!is_array($user)) {
        unset($_SESSION["user"]);
        return null;
    }
    if (!array_key_exists("avatar_path", $user)) $user["avatar_path"] = null;
    return $user;
}

function requireLogin(): array
{
    $user = currentUser();
    if (!$user) {
        header("Location: login.php");
        exit;
    }
    return $user;
}

function requireAdmin(): array
{
    $user = requireLogin();
    if ($user["role"] !== "admin") {
        http_response_code(403);
        exit("Access denied. Administrator permission is required.");
    }
    return $user;
}
?>
