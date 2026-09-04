# Student Resource Exchange System
## Developer Tools and Code Explanation Note

**Prepared for:** Tutor code review  
**Project type:** PHP and MySQL academic resource-sharing web application  
**Development environment:** XAMPP on Windows

## 1. Short explanation for the tutor

The Student Resource Exchange System is a server-rendered web application developed with procedural PHP, MySQL/MariaDB, HTML, CSS, and JavaScript. Students can register, log in, upload study resources, search approved resources, report unsuitable resources, manage their profiles, and view or download approved files. Administrators can moderate submissions, manage users, review reports, provide feedback, and protect the quality of the shared catalogue.

The application uses a conventional PHP request-response structure. A browser sends a request to a PHP page, the PHP page validates the request and the user’s permissions, prepared SQL statements communicate with the database, and the page returns HTML styled by the shared stylesheet. Sensitive operations are protected by authentication, role checks, CSRF tokens, server-side validation, and controlled file handling.

## 2. Tools and technologies used

| Tool or technology | How it was used in this project |
|---|---|
| **Visual Studio Code** | Used to create and edit PHP, CSS, SQL, Markdown, and SVG files. The Explorer and integrated terminal support project organisation and debugging. |
| **XAMPP** | Provides the local development environment. Apache serves the PHP website and MariaDB/MySQL stores the application data. |
| **Apache** | Receives browser requests such as `student.php`, `admin.php`, and `profile.php`, then runs the PHP application. |
| **PHP 8.2** | Implements page logic, authentication, form processing, validation, database queries, moderation, and file operations. |
| **MySQLi** | Connects PHP to the `student_resource_exchange` database and supports prepared statements. |
| **MariaDB/MySQL** | Stores users, resources, academic levels, subjects, reports, moderation records, and profile information. |
| **phpMyAdmin** | Used to create the database, inspect tables, and import SQL schema or migration files. |
| **HTML5** | Provides semantic forms, headings, navigation, buttons, links, labels, and resource cards. |
| **CSS3** | Provides the visual design, responsive layout, colour system, cards, buttons, forms, avatar styling, and mobile behaviour. |
| **JavaScript** | Supports small browser-side interactions such as confirmation dialogs and download-related behaviour. |
| **Web browser** | Used to test registration, login, uploads, moderation, filtering, profile settings, public profiles, downloads, and responsive layouts. |
| **PHP command-line linting** | Used with `php -l` to check changed PHP files for syntax errors before testing them in the browser. |
| **SQL migration files** | Used to apply controlled database changes without rebuilding the whole database. |
| **Markdown documentation** | Used for the user guide, test plan, normalization note, evaluation, report checklist, and this explanation note. |

## 3. Important project files

| File | Responsibility |
|---|---|
| `config.php` | Starts hardened sessions, connects to MySQL, sets UTF-8 encoding, escapes output, creates and verifies CSRF tokens, checks login state, and enforces administrator access. |
| `index.php` | Public landing page and navigation entry point. |
| `register.php` | Validates and creates student or administrator accounts. Student registration includes academic level selection. |
| `login.php` | Authenticates users and loads their account data into the session. |
| `logout.php` | Ends the authenticated session. |
| `student.php` | Handles resource uploads, student submissions, catalogue search, academic-level filtering, sorting, pagination, reports, and report notifications. |
| `admin.php` | Provides moderation, analytics, report review, administrator feedback, resource decisions, and role management. |
| `profile.php` | Displays account information and handles profile updates, avatar choices, password changes, privacy settings, and student account deletion. |
| `users.php` | Displays member accounts according to the project’s privacy rules. |
| `user_profile.php` | Displays safe public member information and approved shared resources. |
| `resource_view.php` | Provides an in-site viewing route for approved resources. |
| `download.php` | Provides controlled resource delivery instead of exposing unrestricted file paths. |
| `style.css` | Shared responsive design and component styling. |
| `default-avatar.svg` and `default_avatars/` | Supply the default and selectable animal avatars. |
| `uploads/` | Stores submitted resources and uploaded profile images. |
| `migration_report_actions.sql` | Adds report decision fields and the student notification table. |
| `TEST_PLAN.md` | Records functional, security, and usability test cases. |
| `USER_GUIDE.md` | Explains system use and local installation. |
| `NORMALIZATION_NOTE.md` | Explains the database design and 3NF evidence. |
| `EVALUATION.md` | Records strengths, limitations, and possible future improvements. |

## 4. Database explanation

The application uses the `student_resource_exchange` database. The database separates different types of information into related tables instead of storing everything in one large table. Typical entities include `users`, `resources`, `academic_levels`, `subjects`, `resource_reports`, and `moderation_history`.

The `users` table stores account information such as display name, email, password hash, role, academic level, profile picture path, and profile visibility. The `resources` table stores uploaded-resource metadata such as title, subject, academic-level reference, submitting user, file path, status, and moderation information. Foreign keys connect resources to users and academic levels. Reports connect a reporting student to a resource, while moderation history records administrator decisions.

This separation supports **Third Normal Form** because each table represents one main subject, repeating groups are avoided, and non-key fields depend on the key of their own table rather than on another non-key field. The existing `NORMALIZATION_NOTE.md` provides the detailed table-by-table evidence.

## 5. Security methods used

### Prepared SQL statements

User input is not concatenated directly into SQL queries. PHP uses MySQLi prepared statements with bound parameters. This reduces the risk of SQL injection and keeps database values separate from SQL instructions.

### Password hashing

Passwords are not stored as plain text. The application uses PHP’s password hashing and verification functions, including `password_hash()` when creating or changing a password and `password_verify()` during login or account confirmation.

### CSRF protection

Forms receive a session-based CSRF token generated using secure random bytes. State-changing requests verify the submitted token with `hash_equals()`. This protects profile updates, resource actions, reports, moderation decisions, role changes, and account deletion from unauthorised cross-site form submissions.

### Session protection and role control

The session cookie uses `HttpOnly` and `SameSite` settings, and the secure flag is enabled when HTTPS is active. `requireLogin()` protects authenticated pages, while `requireAdmin()` prevents students from entering administrator functions. Administrator role management prevents the final administrator from being removed accidentally.

### Output escaping

User-controlled values are escaped through the shared `e()` helper before being inserted into HTML. This reduces the risk of stored or reflected cross-site scripting when displaying names, subjects, titles, report details, and feedback.

### Upload protection

Resource and avatar uploads are checked on the server. The system validates the accepted file type, stores files under controlled directories, generates safe stored names where appropriate, and uses an uploads-directory rule to prevent executable scripts from running as uploaded content. Resource access also checks ownership, approval state, or administrator permission before delivery.

### Privacy and access control

Public member pages show only approved and permitted information. Private members may remain visible in the Members directory according to the selected project rule, but their shared information is hidden. Email addresses, password hashes, and other private account data are not displayed publicly.

## 6. Main workflows to explain during the code review

### Student resource workflow

A student submits a title, unrestricted subject name, academic level, and PDF or DOCX file. The resource is stored with a pending status. An administrator reviews it and either approves or rejects it. Only approved resources enter the catalogue and become available for public viewing or downloading.

### Administrator moderation workflow

An administrator opens the moderation queue, reviews a submitted file, and records an approval or rejection decision. Rejection feedback is stored and shown to the submitting student. Moderation history records the administrator, decision, reason, and time.

### Resource-report workflow

A student reports an approved resource by selecting a reason and optionally adding details. The administrator reviews the report, enters feedback, and chooses either **Keep resource** or **Delete resource**. The resource file is deleted only when the administrator chooses deletion. A notification is saved for the reporting student so the student can see the action and feedback on the Student Page.

### Profile workflow

A user opens Profile to view account information. The user can update display name, email, academic level, profile visibility, and profile picture, or change the password through a dedicated password form. Students can delete their own account after current-password confirmation. Administrator deletion is restricted to protect system governance.

## 7. How the code was tested

Testing was performed in the local XAMPP environment through a web browser and PHP syntax checks. The test plan covers successful actions, invalid inputs, access-control failures, moderation decisions, file handling, filtering, privacy behaviour, and responsive presentation.

Before browser testing, changed PHP files were checked using the PHP command-line lint command:

```text
D:\xampp\php\php.exe -l D:\xampp\htdocs\Student_Resource_Exchange_System\filename.php
```

A tutor demonstration should show a complete end-to-end scenario: register a student, log in, upload a resource, approve it as administrator, find it using search and academic-level filters, open and download it, report it as a student, resolve the report as administrator, and confirm that the reporting student receives the administrator’s feedback.

## 8. Limitations to acknowledge honestly

The application is designed for a local XAMPP environment, so production deployment would require HTTPS, stronger database credentials, server-level upload configuration, regular backups, and a production web-server configuration. The system currently provides in-application notifications rather than email notifications. These are reasonable future improvements and can be recorded in the evaluation section.

The application-level resource-size rule may be configured according to the assignment requirement, but Apache/PHP can still impose server-level limits through `upload_max_filesize` and `post_max_size`. This distinction should be explained clearly if large-file testing is discussed.

## 9. A short presentation script

> “I built the website using PHP, MySQL, HTML, CSS, JavaScript, and XAMPP. XAMPP provides Apache and MariaDB for local development. PHP handles authentication, form processing, moderation, reports, profile management, and access control. MySQL stores users, resources, academic levels, reports, and moderation history. I used prepared statements to reduce SQL-injection risk, Bcrypt-compatible password hashing, CSRF tokens for state-changing forms, session protection, role-based access control, output escaping, and server-side upload validation. I tested the system with browser test cases and PHP syntax checks. The most important complete workflow is resource submission, administrator moderation, reporting, administrator feedback, and student notification.”

## 10. Questions the tutor may ask

| Possible question | Suggested answer |
|---|---|
| Why did you use prepared statements? | To keep user input separate from SQL commands and reduce SQL-injection risk. |
| Why are passwords hashed? | A password hash is stored instead of the original password, and the entered password is verified against the hash during login. |
| Why is a resource pending first? | Moderation prevents unsuitable, duplicate, broken, or incorrectly categorized files from entering the shared catalogue. |
| How are students told about moderation? | Rejection reasons are stored with the submission, and report decisions generate student-facing feedback notifications. |
| Why use academic-level references? | They make filtering consistent and prevent repeated level text from being stored in unrelated records. |
| What does CSRF protection do? | It verifies that state-changing forms came from the legitimate application session. |
| How do you protect administrator pages? | The page requires a logged-in user with the administrator role; students receive an access-denied response. |
| How did you test the system? | I used functional, security, usability, and responsive test cases, supported by PHP syntax checks and browser evidence screenshots. |
| What would you improve next? | Production HTTPS, stronger deployment credentials, automated backups, email notifications, and more detailed audit logging. |

## References

The following project files provide direct implementation evidence for this explanation:

1. [`config.php`](config.php) — database connection, session handling, CSRF helpers, escaping, and access control.
2. [`student.php`](student.php) — student uploads, catalogue functions, reports, and notifications.
3. [`admin.php`](admin.php) — moderation, analytics, reports, and administrator controls.
4. [`profile.php`](profile.php) — profile, password, avatar, privacy, and account-management features.
5. [`NORMALIZATION_NOTE.md`](NORMALIZATION_NOTE.md) — database normalization evidence.
6. [`TEST_PLAN.md`](TEST_PLAN.md) — functional, security, and usability testing evidence.
7. [`USER_GUIDE.md`](USER_GUIDE.md) — system workflow and installation guidance.
