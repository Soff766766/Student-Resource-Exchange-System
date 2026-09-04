# Student Resource Exchange System — Test Plan

## Purpose

This test plan provides evidence that the Student Resource Exchange has been tested for functionality, security, usability, and reliability.

## Test cases

| ID | Area | Test | Expected result | Status |
|---|---|---|---|---|
| T01 | Registration | Submit a valid student account | Account is created and the user is redirected to login | Pass |
| T02 | Registration | Submit an invalid email | Registration is rejected with a clear message | Pass |
| T03 | Login | Submit valid student credentials | Student is redirected to the student page | Pass |
| T04 | Login | Submit invalid credentials | Login fails without revealing which field was incorrect | Pass |
| T05 | Authorization | Open `admin.php` as a student | Access is denied | Pass |
| T06 | Authorization | Open `student.php` while logged out | User is redirected to login | Pass |
| T07 | Upload | Submit a valid PDF under 10MB | Resource is saved with pending status | Pass |
| T08 | Upload validation | Submit an unsupported file type | File is rejected | Pass |
| T09 | Upload validation | Submit a file above 10MB | File is rejected with a size message | Pass |
| T10 | CSRF | Submit a state-changing form without a valid token | Request is rejected | Pass |
| T11 | Moderation | Approve a pending resource | Resource becomes visible in the catalogue | Pass |
| T12 | Moderation | Reject a pending resource with a reason | Student sees the rejection reason | Pass |
| T13 | Student management | Delete another student’s pending resource | Deletion is rejected | Pass |
| T14 | Search | Search by title | Matching approved resources are shown | Pass |
| T15 | Search | Search by subject name | Matching approved resources are shown | Pass |
| T16 | Filtering | Filter by Grade 10–12 or College Year 1–4 | Only the selected academic level is shown | Pass |
| T17 | Pagination | Open a catalogue page | Correct page of approved resources is displayed | Pass |
| T18 | Reporting | Report an approved resource | An open report is created for administrators | Pass |
| T19 | Reporting | Submit the same open report twice | Duplicate open report is rejected | Pass |
| T20 | Download | Download an approved resource | File is returned with the correct type and name | Pass |
| T21 | Security | Request an unapproved resource directly | Resource is not served | Pass |
| T22 | Responsive design | Open the site on a narrow viewport | Content remains readable and usable | Pass |
| T23 | Resubmission | Edit a rejected submission and upload a revised file | Resource returns to pending status and rejection data is cleared | Pass |
| T24 | Moderation history | Approve or reject a pending resource | Decision, administrator, reason, and time are recorded | Pass |
| T25 | Login protection | Submit five invalid login attempts | Further attempts are temporarily blocked | Pass |
| T26 | Accessibility | Navigate controls with the keyboard | Focus is visible and controls remain usable | Pass |
| T27 | Account safety | Administrator attempts to demote the final admin | Action is rejected and access remains protected | Pass |
| T28 | Upload security | Request a script from the uploads directory | Directory blocks executable scripts | Pass |
| T29 | Profile management | Update display name and email | Valid values are saved and shown after refresh | Pass |
| T30 | Password validation | Submit a password shorter than eight characters | Update is rejected with a clear message | Pass |
| T31 | Email validation | Submit an email already used by another account | Update is rejected without changing the account | Pass |
| T32 | Profile picture | Upload a valid JPG or PNG up to 2MB | Photo is saved and displayed on the profile and navigation | Pass |
| T33 | Default avatar | Open a profile without an uploaded photo | Cute animal avatar is displayed automatically | Pass |
| T34 | Image validation | Upload an unsupported or oversized image | Photo is rejected with a clear validation message | Pass |

## Evidence to collect

For the final submission, capture screenshots of the login page, registration page, student dashboard, successful upload, rejected submission with feedback, admin analytics, moderation queue, report resolution, search results, pagination, resource viewing, the default animal avatar, and a successful uploaded profile picture. Include the database structure, the avatar migration result, and the result of PHP syntax checks.

## Evaluation criteria

A test is considered successful when the observed behavior matches the expected result, the application gives a useful message where appropriate, and no unrelated data is changed.
