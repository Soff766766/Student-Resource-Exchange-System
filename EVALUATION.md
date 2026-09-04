# Student Resource Exchange System — Evaluation Outline

## Objectives achieved

The system provides a controlled platform for students to share and discover study resources. It supports flexible subject names, High School Grade 10–12 levels, College Year 1–4 levels, authentication, administrator moderation, secure resource access, catalogue search, filtering, sorting, pagination, reporting, and rejection feedback.

## Design decisions

The system separates users, subjects, academic levels, resources, and resource reports into related database tables. This avoids duplicating subject and level text for every resource. Prepared statements are used for user-controlled database values, and generated upload filenames prevent original filenames from becoming server paths.

The approval workflow was selected because shared study files need moderation before becoming public. A student can submit a resource, an administrator can approve or reject it, and a student can see a rejection reason. Rejected resources can be corrected and resubmitted, which avoids unnecessary duplicate records. The reporting workflow extends moderation beyond the initial approval decision, while moderation history provides an auditable record of administrator decisions.

## Security decisions

Passwords are stored using PHP password hashing. Sessions use HttpOnly and SameSite cookie settings. State-changing forms include CSRF tokens. Uploads are limited to valid PDF and DOCX MIME types and a maximum size of 10MB. Administrator pages require the administrator role, and student deletion checks ownership and pending status.

## Strengths

The system addresses a realistic student need and provides separate workflows for students and administrators. The database structure supports future growth because subjects are not hard-coded into the application. The resource catalogue is searchable and paginated, while the administrator dashboard provides measurable resource and user statistics.

## Limitations

The current system is designed for a local XAMPP demonstration. A production deployment would need HTTPS, environment-based database credentials, stronger login rate limiting, automated backups, email notifications, virus scanning, and a production file-storage strategy. DOCX files may download rather than display in an embedded browser viewer because browser support for DOCX is limited.

## Future improvements

Future versions could add student profiles, favourites, ratings, comments, email notifications, full moderation history, subject autocomplete, advanced analytics, cloud storage, and a mobile interface. These features should be prioritized according to user research rather than added without justification.

## Final evaluation method

The final evaluation should compare the implemented system against the original objectives and requirements. Use the test plan to identify successful and failed cases, explain any limitations, and propose realistic future work. Include screenshots and database evidence so that the evaluation is supported by demonstrable results rather than unsupported claims.
