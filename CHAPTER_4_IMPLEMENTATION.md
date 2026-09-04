# Chapter 4: Implementation

## 4.1 Introduction

This chapter explains how the Student Resource Exchange System was implemented from the approved design. The implementation converts the requirements and database design into a working web application that allows students to share academic resources and discover approved resources contributed by other students. The chapter also explains the selected hardware, software, architecture, database implementation, security controls, user-interface implementation, testing approach, and critical implementation decisions.

The system was implemented as a locally hosted web application using PHP and MySQL/MariaDB within XAMPP. This approach was suitable for the project because it provided a realistic server-side environment while remaining simple to install, test, and demonstrate. Apache handles browser requests, PHP processes application logic, and the relational database stores persistent account, resource, moderation, academic-level, and report data.

The implementation was guided by four principles. First, student resources must be organised using unrestricted subject names and consistent academic levels. Second, uploaded resources must be reviewed before becoming available in the catalogue. Third, private account information must not be exposed to other users. Fourth, security and usability must be considered as part of implementation rather than added only after the main features were completed.

## 4.2 Software development architecture

The system uses a lightweight server-rendered architecture. A browser sends an HTTP request to an individual PHP page. The page loads the shared configuration, checks the current session and user role, validates submitted data, communicates with MySQL through MySQLi, and returns HTML styled by the shared CSS file. This architecture was selected because the project is a small-to-medium academic web system and does not require the complexity of a separate frontend framework or REST API. The textbook explains that a three-tier client/server architecture separates presentation, application analysis, and data-management functions into distinct logical modules, supporting maintainability and flexibility [5, pp. 433–434].

The architecture contains four logical layers. The presentation layer consists of HTML markup, CSS styling, and limited JavaScript interactions. The application layer contains PHP page logic for registration, login, uploading, moderation, profiles, reports, and catalogue filtering. The data-access layer uses MySQLi prepared statements and database queries. The data-storage layer consists of the relational MySQL/MariaDB schema and controlled upload directories. This is a practical adaptation of the textbook’s three-tier concept: the browser provides presentation, PHP provides application processing, and MySQL/MariaDB provides data management [5, pp. 433–434].

```text
+----------------------+       HTTP request/response       +----------------------+
| Student or admin     | <-------------------------------> | Apache web server   |
| web browser          |                                    | XAMPP               |
+----------------------+                                    +----------+-----------+
                                                                        |
                                                                        v
                                                         +--------------+---------------+
                                                         | PHP application pages        |
                                                         | Auth, uploads, catalogue,   |
                                                         | profiles, moderation,       |
                                                         | reports and downloads       |
                                                         +--------------+---------------+
                                                                        |
                                      prepared statements               |
                                                                        v
                                                         +--------------+---------------+
                                                         | MySQL/MariaDB database       |
                                                         | users, resources, subjects,  |
                                                         | levels, reports and history  |
                                                         +------------------------------+
```

The shared `config.php` file acts as a common foundation. It creates the database connection, sets the character encoding to UTF-8, starts a protected session, provides output escaping, generates and validates CSRF tokens, and exposes authentication helpers. This avoids repeating security logic in every page and makes the implementation easier to maintain.

### 4.2.1 Hardware requirements

The application does not require specialist hardware because it is a browser-based PHP/MySQL system. A standard computer capable of running Windows, XAMPP, a modern web browser, and a code editor is sufficient. The values below should be replaced with the exact specifications of the computer used for the final demonstration if the assignment requires measured hardware evidence.

| Hardware component | Implementation requirement or project use |
|---|---|
| Processor | A modern dual-core or better CPU is sufficient for local Apache, PHP, MySQL, browser, and code-editor processes. |
| Memory | At least 4 GB RAM is suitable for development; 8 GB or more provides a smoother experience when XAMPP, browser windows, phpMyAdmin, and the editor are open together. |
| Storage | An SSD or HDD stores XAMPP, the PHP project, database files, uploaded resources, profile images, screenshots, and documentation. An SSD improves application and browser loading times. |
| Graphics | Integrated graphics are sufficient because the system uses standard HTML/CSS and does not perform graphics-intensive processing. |
| Display and input | A standard monitor, keyboard, and mouse are required for development and responsive-layout testing. |
| Network | Internet access is not required for normal local execution, but it is useful for documentation research, updates, and optional deployment. |

### 4.2.2 Operating system and development software

The system was developed on Windows using XAMPP. XAMPP provides Apache and MariaDB/MySQL in one local package, allowing the project to be accessed through a localhost URL. Visual Studio Code was used as the main code editor because it supports PHP, SQL, CSS, HTML, JavaScript, and Markdown files in one workspace. A browser was used to test the application from the perspective of students and administrators.

| Software or tool | Purpose in the implementation |
|---|---|
| Windows | Development operating system and local file-management environment. |
| XAMPP | Local development package containing Apache and MariaDB/MySQL. |
| Apache | Web server that serves the PHP pages through localhost. |
| PHP 8.2 | Server-side language used for application logic and form processing. |
| MySQL/MariaDB | Relational database engine used for persistent application data. |
| phpMyAdmin | Database creation, table inspection, SQL import, and migration management. |
| Visual Studio Code | Editing PHP, SQL, CSS, HTML, JavaScript, SVG, and Markdown files. |
| Web browser | Functional, usability, download, responsive, and access-control testing. |
| PHP CLI linting | Syntax verification using `php -l` before browser testing. |
| SQL migration files | Controlled changes to an existing database without recreating all data. |
| Markdown | Technical documentation, test plan, evaluation, normalization evidence, and this chapter. |

## 4.3 Application implementation

### 4.3.1 Authentication and account registration

The registration page accepts a full name, email address, account type, password, password confirmation, and academic level for students. Academic levels are grouped into High School Grade 10–12 and College Year 1–4. The application validates the required fields on the server and rejects invalid email addresses, short passwords, mismatched confirmations, and invalid academic-level values.

Passwords are transformed into a one-way password hash before being inserted into the database. The original password is never stored or displayed. During login, the submitted password is checked against the stored hash. Login failures are counted in the session, and repeated failures trigger a temporary lockout. After successful authentication, the session contains the required user information and the user is redirected according to the account role.

The same login page supports both students and administrators, but administrator functions are protected separately. `requireLogin()` confirms that a user is authenticated, while `requireAdmin()` additionally checks that the authenticated user has the administrator role. This prevents a student from accessing administrative moderation and user-management actions by typing an administrator URL directly.

### 4.3.2 Resource submission and moderation

The resource submission form accepts a resource title, unrestricted subject name, academic level, and study file. Subjects are not hard-coded into the interface, which allows students to share different subjects, including Myanmar books and other academic materials. Academic levels are stored consistently through the academic-level structure, enabling reliable filtering.

A newly submitted resource is stored with a pending status. The resource does not appear in the public catalogue until an administrator reviews it. The administrator can approve or reject the resource. If rejected, a reason is saved and displayed to the submitting student. The student can correct the title, subject, academic level, or file and resubmit the resource rather than creating an unnecessary duplicate record.

The moderation design separates resource metadata from the physical file. The database stores the title, subject reference, academic-level reference, submitting user, status, file path, and review information. The uploaded file is stored in the controlled uploads directory. This allows the application to remove the physical file when an authorised deletion occurs.

### 4.3.3 Catalogue search and filtering

The student catalogue provides search by resource title or subject name. It also supports academic-level filtering, sorting, pagination, and clear empty-result feedback. The filter form submits the selected values to the server, where the values are validated and included in a prepared SQL query. The database returns only approved resources for the catalogue.

This implementation was selected because students may know a subject name but not the exact resource title. It also supports the requirement that the system handle flexible subject names rather than a small fixed list. The result count, active filter state, and clear-filters option improve usability when a search produces no matching resources.

### 4.3.4 Resource viewing and downloading

Approved resources can be opened through an in-site viewer and downloaded through a controlled download endpoint. The system does not rely only on a direct public file link. The viewer and download handler verify the resource identifier, approval status, and file location before serving a file. This supports the requirement that users can open or download a resource without an unwanted external download-manager popup.

The response headers and controlled delivery route allow the application to distinguish between viewing and downloading. File paths are not accepted directly from arbitrary user input. The physical path is resolved from trusted database data and checked against the intended upload directory.

### 4.3.5 Profiles, avatars, academic levels, and privacy

The Profile page first shows the signed-in user’s profile information. The user can update display name, email address, academic level, profile visibility, and profile picture. The password change is handled through a dedicated form that requires the current password, a new password, and confirmation of the new password.

Users may select one of the supplied animal avatars or upload a personal image. The selectable images are displayed as small circular thumbnails and are constrained with CSS so their original dimensions cannot enlarge the page. Image paths are validated before being used. When no custom image is selected, the system provides a local default animal avatar.

The Public/Private setting controls shared-information visibility. A private account can remain visible in the Members directory, but other users cannot see that member’s academic-level details, resource count, or shared-resource list. A public account can display permitted profile information and approved shared resources. Email addresses, passwords, and other private account data are never displayed on public member pages.

### 4.3.6 Reporting and administrator feedback

Students can report an approved resource by selecting a reason such as broken file, wrong subject, duplicate, inappropriate content, or another issue. Optional details provide additional context for the administrator. Duplicate open reports from the same student for the same resource are prevented.

The administrator report workflow provides two decisions: keep the resource or delete the resource. The administrator must enter feedback for the reporting student. The decision, feedback, administrator, and time are stored. If the resource is deleted, the database record and physical upload are removed through an authorised transaction. A notification is created for the reporting student, and the result appears under Report Updates on the Student Page.

This workflow extends moderation beyond the initial approval decision. It also creates an auditable process because the student’s report and the administrator’s response are stored rather than being handled informally.

## 4.4 Database implementation

The database was implemented as a relational MySQL/MariaDB database named `student_resource_exchange`. The design separates entities so that account information, resource information, subjects, academic levels, reports, and moderation decisions can be maintained independently.

| Entity | Main implementation purpose |
|---|---|
| `users` | Stores account identity, role, password hash, academic level, profile picture path, and profile visibility. |
| `resources` | Stores resource metadata, submitting user, academic-level reference, file path, approval status, and review data. |
| `subjects` | Stores reusable subject names, including user-created academic or Myanmar subject names. |
| `academic_levels` | Stores the controlled High School and College level values used for filtering. |
| `resource_reports` | Stores report reason, report details, reporting student, status, and administrator action. |
| `moderation_history` | Stores approval and rejection decisions with administrator, reason, and time. |
| `report_notifications` | Stores administrator feedback and keep/delete outcomes for reporting students. |

The design follows Third Normal Form. Each table represents a distinct subject, repeating groups are avoided, and non-key attributes depend on the key of their own table. For example, the academic-level label is maintained in the academic-level structure rather than repeated as uncontrolled text in every resource record. This follows the textbook’s explanation that normalization converts complex data structures into simpler, stable structures and produces well-structured relations [5, pp. 328–329, 337]. Detailed normalization evidence is provided in `NORMALIZATION_NOTE.md`.

The database is accessed through MySQLi. User-controlled values are passed through prepared statements with bound parameters. Static aggregate queries may use direct database queries where no user-controlled value is included, while search, filtering, account updates, reports, and resource actions use prepared statements. Separating data management from application processing also reflects the database and client/server design principles discussed in the textbook [5, pp. 321–361, 429–434].

## 4.5 Security implementation

Security was treated as a cross-cutting implementation requirement. The application uses multiple controls rather than relying on one technique.

| Risk | Implemented control |
|---|---|
| SQL injection | MySQLi prepared statements and bound parameters for user-controlled values. |
| Password exposure | Password hashes are stored instead of plain-text passwords. |
| Cross-site request forgery | Session-based CSRF tokens are generated and verified on state-changing forms. |
| Session theft and unwanted client access | HttpOnly and SameSite cookie settings are used, with the secure flag enabled when HTTPS is active. |
| Unauthorised student access to admin functions | `requireAdmin()` checks the authenticated role on administrator pages. |
| Cross-site scripting | Displayed user-controlled values are escaped through the shared `e()` helper. |
| Unsafe uploaded files | Upload type and file metadata are validated, names and paths are controlled, and uploaded-resource execution is restricted. |
| Unauthorised resource access | Resource identifiers, approval state, ownership, and role permissions are checked before file operations. |
| Account deletion abuse | The current password is required and student ownership is checked. Administrator deletion is restricted. |
| Administrator lockout | The final administrator cannot be demoted or removed through role management. |
| Privacy disclosure | Private member details and resources are hidden while public pages expose only permitted information. |

The shared security helpers reduce duplication. For example, `verifyCsrf()` is called before state-changing actions, and `e()` is used when displaying values in HTML. Centralising these controls makes future maintenance safer because a security improvement can be applied consistently.

The implementation follows established security guidance. PHP documents `password_hash()` as a function for creating a strong one-way password hash [1]. PHP also explains that prepared statements can protect against SQL injection when parameters are bound separately from the SQL statement [2]. OWASP recommends CSRF tokens as a defence for state-changing requests and recommends secure password-storage practices rather than storing passwords in plain text [3] [4]. The textbook further presents system implementation as a distinct stage in the systems development life cycle, supporting the decision to verify syntax, database setup, security controls, and end-to-end behaviour before demonstration [5, pp. 463–494].

## 4.6 User-interface and responsive implementation

The interface uses a consistent visual language across the landing page, Student Page, Admin Page, Profile, Members, and public member-profile pages. Shared classes in `style.css` provide consistent typography, spacing, buttons, form controls, panels, status messages, and responsive behaviour.

The layout uses flexible containers and grid structures rather than fixed desktop-only widths. On smaller screens, two-column sections become a single column, form fields stack vertically, analytics bars remain readable, member cards resize, and avatar choices remain compact. Labels are placed close to their inputs, and feedback messages are shown near the action that produced them.

The system also provides user-facing feedback for successful and unsuccessful operations. Examples include successful profile updates, invalid password messages, rejected resource reasons, empty catalogue results, report decisions, and unavailable resources. This reduces confusion and helps the user understand what happened after submitting a form.

## 4.7 Implementation testing

Implementation testing was performed in the local XAMPP environment through a browser and with PHP command-line syntax checks. The test process was organised around functional, security, usability, privacy, and responsive requirements.

| Test area | Example evidence |
|---|---|
| Registration | Valid account creation, invalid email, short password, mismatch confirmation, and required academic level. |
| Authentication | Successful student/admin login, incorrect credentials, rate limiting, logout, and role redirection. |
| Resource workflow | Upload, pending status, student deletion of pending work, approval, rejection feedback, correction, and resubmission. |
| Catalogue | Subject search, academic-level filtering, sort order, pagination, clear filters, and empty results. |
| Resource access | Approved resource viewing, direct download, blocked unapproved access, and file cleanup after deletion. |
| Profile | Account update, avatar selection, photo validation, password update, privacy setting, and account deletion. |
| Members | Public/private display behaviour, safe public information, academic-level display, and shared-resource visibility. |
| Reporting | Report submission, duplicate-report prevention, administrator keep/delete decision, feedback, notification, and file deletion. |
| Security | CSRF rejection, role restriction, output escaping, prepared SQL statements, and final-administrator protection. |
| Responsive usability | Student, admin, profile, and Members layouts at desktop and narrow mobile widths. |

Before browser execution, PHP files were checked using the following command format:

```text
D:\xampp\php\php.exe -l D:\xampp\htdocs\Student_Resource_Exchange_System\filename.php
```

The detailed results and screenshot references should be recorded in `TEST_PLAN.md`. A final demonstration should show one complete end-to-end scenario rather than only isolated screenshots: a student registers, uploads a resource, an administrator approves it, another student finds it using a filter, opens or downloads it, reports it, and then receives administrator feedback.

## 4.8 Implementation challenges and solutions

One implementation challenge was supporting unrestricted subject names while still providing useful filtering. The solution was to treat the subject name as user-entered data rather than limiting the interface to a hard-coded list. This supports academic subjects and Myanmar books while allowing search by name.

A second challenge was preventing uploaded resources from being intercepted by an external download manager or opened through an uncontrolled file path. The solution was to provide in-site viewing and a controlled download endpoint that resolves approved resources by identifier.

A third challenge was preserving privacy while allowing the Members directory to remain useful. The final implementation distinguishes the account’s directory presence from the visibility of its shared information. A private member may be listed and opened, but academic and resource-sharing details are hidden.

A fourth challenge was ensuring that database migrations and PHP code remained aligned. Missing columns or tables produced visible runtime errors during development. The migration files and live database checks were therefore treated as part of implementation evidence, and the final setup instructions identify which migrations must be imported before testing.

## 4.9 Critical evaluation of the implementation

The implementation meets the main functional objective: it provides a structured and moderated place for students to share and discover academic resources. The strongest technical aspects are the approval workflow, flexible subject support, role-based administration, secure resource delivery, profile management, privacy controls, and report-feedback cycle.

The implementation is also maintainable for the project scale. Shared configuration helpers reduce duplicated authentication and security code, related data is separated into relational tables, and the documentation identifies the purpose of the main files. The responsive CSS allows the same system to operate on desktop and smaller screens without requiring separate pages.

There are still realistic limitations. The current system is designed for local XAMPP use, so a production deployment would require HTTPS, environment-based credentials, stronger operational monitoring, automated backups, antivirus scanning, and production file storage. The current notification mechanism is in-application rather than email-based. DOCX files may need to be downloaded because browser support for embedded DOCX viewing is less consistent than PDF viewing.

These limitations do not invalidate the implementation. They provide a clear basis for future work and demonstrate critical evaluation. A future version could add email notifications, favourites, ratings, comments, advanced audit logs, cloud storage, and automated backup. These improvements should be prioritised through user research rather than added without a defined need.

## 4.10 Chapter summary

This chapter has explained the implementation of the Student Resource Exchange System. The system was built with PHP, MySQL/MariaDB, Apache, XAMPP, HTML, CSS, and JavaScript. It uses a server-rendered architecture with shared authentication and security helpers, a normalised relational database, controlled resource uploads, moderation, searchable catalogue functions, profiles, privacy settings, reports, feedback, and secure resource delivery.

The implementation demonstrates more than basic page creation. It connects requirements to design decisions, applies security controls to state-changing operations, validates user input, protects private information, supports responsive use, and provides a testable end-to-end workflow. The test plan, screenshots, normalization evidence, user guide, and evaluation document should be submitted with this chapter as supporting distinction evidence.

## References

[1]: https://www.php.net/manual/en/function.password-hash.php "PHP Manual: password_hash"

[2]: https://www.php.net/manual/en/mysqli.quickstart.prepared-statements.php "PHP Manual: MySQLi Prepared Statements"

[3]: https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html "OWASP: Cross-Site Request Forgery Prevention Cheat Sheet"

[4]: https://cheatsheetseries.owasp.org/cheatsheets/Password_Storage_Cheat_Sheet.html "OWASP: Password Storage Cheat Sheet"

[5]: Modern Systems Analysis and Design, 9th ed., Global Edition, Joseph S. Valacich and Joey F. George, Pearson Education Limited, Harlow, England, 2021. Relevant sections: Chapter 9, Designing Databases; Chapter 12, Designing Distributed and Internet Systems; and Chapter 13, System Implementation.

## Appendix A: Tutor code-review explanation

> “The application is a PHP and MySQL/MariaDB system hosted locally through XAMPP. Apache receives requests and PHP processes the application logic. The database stores users, resources, subjects, academic levels, reports, and moderation history. I used prepared statements for user-controlled SQL values, password hashing for authentication, CSRF tokens for state-changing forms, session and role checks for access control, output escaping for displayed values, and server-side upload validation. Resources remain pending until an administrator reviews them. Public profiles can display approved information, while private profiles hide shared information. The system was tested through browser scenarios and PHP syntax checks, and the evidence is recorded in the test plan.”
