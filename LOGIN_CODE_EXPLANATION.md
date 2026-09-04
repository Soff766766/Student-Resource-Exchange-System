# Login Code Explanation

## 1. Overview

The system uses one shared login page, `login.php`, for both students and administrators. The account role is stored in the `users` table. After successful authentication, PHP reads the role and redirects the user to either `student.php` or `admin.php`.

The login process has six main stages: loading the shared configuration, checking whether the user is already logged in, validating the submitted request, searching for the account, verifying the password hash, and creating a secure login session.

## 2. Loading the shared configuration

At the beginning of `login.php`, the following statement is executed:

```php
require "config.php";
```

This loads the common configuration file. `config.php` starts the PHP session, applies cookie protections, connects to the MySQL/MariaDB database, sets UTF-8 character encoding, and provides shared security functions such as `csrfToken()`, `verifyCsrf()`, `currentUser()`, and `e()`.

The session cookie uses `HttpOnly` so browser scripts cannot read it, `SameSite=Lax` to reduce cross-site request risks, and the `secure` flag when HTTPS is active. The database connection uses the database named `student_resource_exchange`.

## 3. Checking for an existing login

The code checks whether a valid user already exists in the session:

```php
if (currentUser()) {
    header("Location: " . (currentUser()["role"] === "admin" ? "admin.php" : "student.php"));
    exit;
}
```

If a logged-in administrator visits `login.php`, the administrator is sent to `admin.php`. If a logged-in student visits the page, the student is sent to `student.php`. This prevents an already authenticated user from seeing the login form again.

## 4. Detecting form submission

The login form uses the POST method:

```php
<form method="post" class="form-grid">
```

PHP checks whether the form has been submitted:

```php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
```

The credentials are therefore processed only after the user submits the form. The form sends the email address and password to the same `login.php` page.

## 5. CSRF protection

Before processing the credentials, the code verifies the CSRF token:

```php
verifyCsrf();
```

The form contains a hidden token:

```php
<input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
```

`csrfToken()` creates a random token using `random_bytes(32)` and stores it in the session. `verifyCsrf()` compares the submitted token with the session token using `hash_equals()`. If the values do not match, the request stops with a security error. This protects the login form from unauthorised cross-site form submissions.

## 6. Failed-login protection

The code checks whether the session is temporarily blocked:

```php
if (loginBlocked()) {
    $message = "Too many failed attempts. Please wait five minutes and try again.";
}
```

Every invalid login calls:

```php
recordLoginFailure();
```

The helper increments the failure count in the session. After five failed attempts, it sets a lockout time five minutes in the future. During the lockout period, another login attempt is not processed. This helps reduce password-guessing attacks.

## 7. Reading and cleaning the submitted values

The email address and password are obtained from the POST request:

```php
$email = strtolower(trim($_POST["email"] ?? ""));
$password = $_POST["password"] ?? "";
```

`trim()` removes accidental spaces from the email address. `strtolower()` makes email matching consistent. The null-coalescing operator provides an empty value if a field is missing. The password is not trimmed because spaces could technically be part of a password.

The HTML form also uses browser-level validation:

```php
<input type="email" name="email" required autocomplete="email">
<input type="password" name="password" required autocomplete="current-password">
```

However, the important security checks still occur on the server, because browser validation can be bypassed.

## 8. Securely finding the account

The account is searched using a prepared statement:

```php
$statement = $conn->prepare(
    "SELECT id, display_name, email, password_hash, role, academic_level,
     profile_visibility, avatar_path
     FROM users WHERE email = ? LIMIT 1"
);
$statement->bind_param("s", $email);
$statement->execute();
$user = $statement->get_result()->fetch_assoc();
$statement->close();
```

The question mark is a parameter placeholder. `bind_param("s", $email)` binds the email as a string instead of joining the user’s value directly into the SQL command. This reduces the risk of SQL injection. `LIMIT 1` ensures that at most one matching account is returned.

The query retrieves only the information needed to create the session and perform role-based redirection. It retrieves the password hash temporarily so PHP can verify the password.

## 9. Verifying the password

The password check is performed using:

```php
if ($user && password_verify($password, $user["password_hash"])) {
```

During registration, the original password is converted into a one-way hash using `password_hash()`. The plain-text password is not stored. During login, `password_verify()` compares the password entered by the user with the stored hash. The application does not decrypt the hash and does not need to know the original password.

The login succeeds only when both conditions are true: an account was found and the entered password matches the stored password hash.

## 10. Creating the authenticated session

After a successful password check, the code performs the following actions:

```php
clearLoginFailures();
session_regenerate_id(true);
unset($user["password_hash"]);
$_SESSION["user"] = $user;
```

`clearLoginFailures()` removes the failed-attempt count because the user has authenticated successfully. `session_regenerate_id(true)` creates a new session identifier and removes the old one, helping protect against session fixation. The password hash is removed from the user array before the array is stored in the session. Therefore, the password hash is not unnecessarily carried through the active session.

The remaining user information is stored in `$_SESSION["user"]`. Other pages call `currentUser()` to retrieve this information and determine whether the visitor is authenticated.

## 11. Role-based redirection

The final login decision is:

```php
header("Location: " . ($user["role"] === "admin" ? "admin.php" : "student.php"));
exit;
```

If the database role is `admin`, the user is redirected to `admin.php`. Otherwise, the user is redirected to `student.php`. The `exit` statement stops PHP from continuing to output the login page after the redirect.

This means there is no separate administrator login form. Both account types use the same login page, while the stored role controls the destination and later permissions.

## 12. Protecting pages after login

The login page creates the session, but each protected page must still check that session. `requireLogin()` is used for pages available to authenticated users:

```php
function requireLogin(): array
{
    $user = currentUser();
    if (!$user) {
        header("Location: login.php");
        exit;
    }
    return $user;
}
```

`requireAdmin()` provides an additional administrator check:

```php
function requireAdmin(): array
{
    $user = requireLogin();
    if ($user["role"] !== "admin") {
        http_response_code(403);
        exit("Access denied. Administrator permission is required.");
    }
    return $user;
}
```

A student who manually types `admin.php` into the browser is therefore denied access. Hiding an administrator link is not the security control; the server-side role check is the actual control.

## 13. Displaying login errors safely

If the credentials are invalid, the application uses one general message:

```php
$message = "The email or password is incorrect.";
```

This does not reveal whether the email address exists. The message is escaped before being displayed:

```php
<?php if ($message): ?>
  <p class="notice error"><?= e($message) ?></p>
<?php endif; ?>
```

The shared `e()` helper uses `htmlspecialchars()` to prevent user-controlled text from being interpreted as HTML or JavaScript.

## 14. Tutor-ready explanation

> The login system uses one shared `login.php` page for students and administrators. When the form is submitted, the server first validates the CSRF token and checks whether the account is temporarily blocked after repeated failures. The email is normalised and used in a MySQLi prepared statement, which prevents the value from being interpreted as part of the SQL command. PHP then uses `password_verify()` to compare the entered password with the one-way password hash stored during registration. If authentication succeeds, the failed-login counter is cleared, the session identifier is regenerated, the password hash is removed from the session data, and the authenticated user is stored in `$_SESSION["user"]`. The role field determines whether the user is redirected to `student.php` or `admin.php`. Protected pages use `requireLogin()` and `requireAdmin()` so that authentication and authorisation are checked on the server rather than relying only on visible links.

## 15. Important files to show during the tutor review

| File | Purpose |
|---|---|
| `login.php` | Receives credentials, verifies CSRF, queries the account, verifies the password, creates the session, and redirects by role. |
| `config.php` | Starts the protected session, connects to MySQL/MariaDB, and defines authentication and security helpers. |
| `register.php` | Creates the account and stores a password hash rather than the original password. |
| `student.php` | Uses the logged-in student session to display student functions. |
| `admin.php` | Uses administrator authorisation checks to protect moderation and management functions. |
| `logout.php` | Ends the authenticated session. |

## 16. Short process summary

```text
Open login.php
       ↓
Enter email and password
       ↓
Verify CSRF token and lockout status
       ↓
Find the account with a prepared SQL statement
       ↓
Compare password with password_verify()
       ↓
If invalid: record failure and show a general error
       ↓
If valid: regenerate session ID and save safe user data
       ↓
Redirect admin to admin.php or student to student.php
```
