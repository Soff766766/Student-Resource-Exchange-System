# Assignment Report Alignment Checklist

## Overall assessment

The report describes the correct project idea and technology stack, but it currently contains several claims that are broader than the implemented website. Before submission, update the report so every claim is supported by the current code, database, screenshots, or test evidence. Do not claim that a feature was implemented or tested if it was only planned.

## Claims that align with the current website

| Report area | Alignment |
|---|---|
| PHP, HTML, CSS, MySQL, and XAMPP | Matches the implemented environment. |
| Student and administrator roles | Matches the implemented authentication and role protection. |
| Student registration and login | Implemented. |
| Student resource upload | Implemented for PDF and DOCX files with a 10MB limit. |
| Flexible subject names | Implemented; subjects are stored in a separate relation. |
| High School Grade 10–12 and College Year 1–4 | Matches the current academic-level design. |
| Administrator approval and rejection | Implemented, including rejection reasons. |
| Search and academic-level filtering | Implemented. |
| Pagination and sorting | Implemented. |
| Resource reports and moderation history | Implemented with migrations and admin controls. |
| CSRF protection, password hashing, and authenticated profile updates | Implemented in the current PHP code, including validation for duplicate emails and minimum password length. |

## Claims that need correction or evidence

| Report claim | Required action |
|---|---|
| “Administrators have entire access” and can edit or remove all files | Change this to the actual workflow: administrators review pending resources, approve or reject them, resolve reports, and manage user roles. Do not claim unrestricted editing unless it is implemented. |
| “Manage profile”, password change, and email reset | Profile management and password changes are now implemented. Do not claim email-reset-by-email unless a recovery workflow is added; describe the current authenticated email update instead. |
| Backup and restore data and system logs | These are described in the admin use case but are not implemented. Remove them from implemented functionality or label them as future improvements. |
| Student and Admin classes with inheritance | The current application is written in procedural PHP rather than a PHP class hierarchy. Replace the class diagram description with a component/data-flow model that matches the code, or implement the classes before claiming object-oriented inheritance. |
| “Cross-browser connectivity proved” | Add actual browser test evidence for Edge, Chrome, and Firefox, or change the statement to describe the browsers that were actually tested. |
| “Security resistance against SQL injection” | Include test cases showing that prepared statements and invalid inputs were tested. Do not use an unsupported general claim. |
| “Performance optimization guarantees” | Replace guarantees with measured evidence such as catalogue response time before and after pagination, or explain pagination and indexed foreign-key searches as design decisions. |
| “Advanced download and direct file sharing” | Describe the actual secure approved-resource viewer and download endpoints. Explain that only approved resources are served. |
| “Customer” terminology | Replace “customer” with “student” throughout the requirement catalogue. |
| Rating and comments | These are currently Could-Have or future features and are not implemented. Keep them clearly labelled as future work. |

## Structural report corrections

The table of contents lists Chapters 4, 5, and 6, but the extracted report content mainly contains Chapters 1–3 followed by references and an appendix. Either add the missing implementation, testing, and evaluation chapters or correct the table of contents and chapter references. A distinction-level report should include a complete implementation chapter, a testing chapter with actual evidence, and a conclusion/evaluation chapter.

The report also contains repeated figure captions, inconsistent numbering, duplicated references, and references that should be checked for accuracy and relevance. Update the table of contents after editing, remove duplicated figure captions, correct section numbering, and ensure every in-text citation appears once in the reference list.

## Required database section

Insert the accompanying `NORMALIZATION_NOTE.md` content into the database-design chapter. Include the ER diagram and explain that `users`, `subjects`, `academic_levels`, `resources`, `resource_reports`, and `moderation_history` are separate relations linked with foreign keys. Explain First, Second, and Third Normal Form using examples from this system.

## Evidence checklist

Before submission, collect screenshots or outputs for registration, login, role protection, upload validation, pending approval, rejection feedback, resubmission, admin analytics, search, filtering, sorting, pagination, reporting, moderation history, resource viewing, and downloading. Include the database schema, migration results, PHP syntax checks, and the final test-plan results.

## Recommended report wording

Use precise wording such as “The implemented system supports...” for completed features, “The system was tested using...” for evidenced tests, and “This feature is outside the current scope and is proposed as future work” for incomplete features. This distinction between implementation, testing, and future work will make the report more credible and academically defensible.
