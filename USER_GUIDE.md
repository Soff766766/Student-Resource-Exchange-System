# Student Resource Exchange System — User and Installation Guide

## System purpose

The Student Resource Exchange allows authenticated students to share study resources and discover approved material by title, subject name, academic level, and sort order. Administrators review submissions, manage users, view analytics, resolve reports, and keep the catalogue safe.

## Student workflow

A student registers an account and logs in. From the Student page, the student enters a resource title, any subject name, an academic level, and a valid PDF or DOCX file. The maximum upload size is 10MB. The submission remains pending until an administrator reviews it.

Students can view the status of their own submissions. Pending submissions can be deleted. Rejected submissions display the administrator’s rejection reason. Students can edit the title, subject, academic level, and file, then resubmit the corrected resource for a new approval decision.

Students search the approved catalogue by title or subject name. They can filter by High School levels Grade 10–12 or College levels College Year 1–4, sort the results, use pagination, open resources in the website viewer, download them, or report a broken, duplicate, incorrectly categorized, or inappropriate resource.

## Administrator workflow

An administrator logs in through the same login page and is redirected to the Admin page. The dashboard displays totals for resources, pending submissions, approved resources, rejected resources, registered users, and subjects. It also displays resource counts by academic level.

The administrator reviews pending files and either approves or rejects them. A rejection reason can be entered and is shown to the submitting student. Every approval or rejection is recorded in moderation history with the administrator, reason, decision, and time. Open resource reports are listed on the admin page and can be marked as resolved. Administrators can also update user roles. The system prevents an administrator from removing their own administrator access or demoting the final administrator account.

## Profile settings

After logging in, students and administrators can open **Profile** to update their display name and email address. Users can upload a JPG, PNG, GIF, or WEBP profile picture up to 2MB, choose one of the supplied animal avatars, or keep the built-in cute animal avatar. Users can also set a new password of at least eight characters. The **Update profile** button saves these changes. Students may permanently delete their account after confirming their current password; their submitted resources are deleted as part of the process. Administrator deletion is disabled to protect the administration workflow. Duplicate email addresses, invalid images, and invalid password values are rejected by the server.

The **Members** page shows public information only: display name, avatar, and the number of approved resources shared. Email addresses and passwords are never displayed. Selecting **View public profile** opens another member’s public profile.

## Installation with XAMPP

Install XAMPP and start Apache and MySQL. Copy the project folder into:

```text
D:\xampp\htdocs\Student_Resource_Exchange_System
```

Create a MySQL database named `student_resource_exchange` using phpMyAdmin. Import the complete project schema if available. For an existing database, apply the migration files in this order:

```text
migration_subjects_levels.sql
migration_resource_management.sql
migration_resource_reports.sql
migration_profile_picture.sql
```

Confirm that `config.php` contains the correct local database connection values. Open the system at:

```text
http://localhost:8080/Student_Resource_Exchange_System/
```

## Important project files

| File | Purpose |
|---|---|
| `config.php` | Database connection, sessions, authentication, CSRF helpers, and escaping. |
| `login.php` | Student and administrator login. |
| `register.php` | Account registration. |
| `student.php` | Uploading, searching, filtering, reporting, and submission management. |
| `admin.php` | Analytics, approval, rejection, reports, and user roles. |
| `resource_view.php` | In-website resource viewing. |
| `download.php` | Secure resource delivery. |
| `style.css` | Shared responsive design and avatar styling. |
| `default-avatar.svg` | Built-in cute animal avatar used when no photo is uploaded. |
| `migration_profile_picture.sql` | Adds the user avatar path column. |
| `users.php` | Lists public member profiles. |
| `user_profile.php` | Displays one member’s public information. |
| `uploads/.htaccess` | Prevents executable scripts from running in the uploaded-resource directory. |
| `TEST_PLAN.md` | Functional, security, and usability test evidence. |

## Demonstration checklist

For a demonstration, show a student registration, login, upload, pending status, administrator approval, catalogue search, academic-level filtering, resource viewing, resource download, rejection feedback, and report resolution. Explain that CSRF tokens, server-side file validation, upload limits, prepared statements, password hashing, and role-based access are used to protect the system.
