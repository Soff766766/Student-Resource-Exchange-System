<?php
require "config.php";

$student = requireLogin();
$studentId = (int) $student["id"];
$flash = pullFlash();
$message = $flash["message"] ?? "";
$messageType = $flash["type"] ?? "";
$notificationsQuery = $conn->prepare("SELECT resource_title, action, feedback, created_at FROM report_notifications WHERE student_id = ? ORDER BY created_at DESC LIMIT 10");
$notificationsQuery->bind_param("i", $studentId); $notificationsQuery->execute(); $notifications = $notificationsQuery->get_result();
$pendingReportsQuery = $conn->prepare("SELECT rr.reason, rr.details, rr.created_at, r.title FROM resource_reports rr JOIN resources r ON r.id = rr.resource_id WHERE rr.reported_by = ? AND rr.status = 'open' ORDER BY rr.created_at DESC");
$pendingReportsQuery->bind_param("i", $studentId); $pendingReportsQuery->execute(); $pendingReports = $pendingReportsQuery->get_result();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete_resource"])) {
    verifyCsrf();
    $resourceId = (int) ($_POST["resource_id"] ?? 0);
    $find = $conn->prepare("SELECT file_path FROM resources WHERE id = ? AND submitted_by = ? AND status = 'pending'");
    $find->bind_param("ii", $resourceId, $studentId);
    $find->execute();
    $resource = $find->get_result()->fetch_assoc();
    $find->close();

    if ($resource) {
        $delete = $conn->prepare("DELETE FROM resources WHERE id = ? AND submitted_by = ? AND status = 'pending'");
        $delete->bind_param("ii", $resourceId, $studentId);
        $delete->execute();
        $delete->close();
        $filePath = __DIR__ . "/" . $resource["file_path"];
        if (is_file($filePath)) unlink($filePath);
        $message = "Your pending submission was deleted.";
        $messageType = "success";
    } else {
        $message = "Only your pending submissions can be deleted.";
        $messageType = "error";
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["report_resource"])) {
    verifyCsrf();
    $resourceId = (int) ($_POST["resource_id"] ?? 0);
    $reason = trim($_POST["report_reason"] ?? "Other");
    $details = trim($_POST["report_details"] ?? "");
    $allowedReasons = ["Broken file", "Wrong subject", "Duplicate", "Inappropriate content", "Other"];

    if (!in_array($reason, $allowedReasons, true)) $reason = "Other";
    $check = $conn->prepare("SELECT id FROM resources WHERE id = ? AND status = 'approved'");
    $check->bind_param("i", $resourceId);
    $check->execute();
    $exists = $check->get_result()->fetch_assoc();
    $check->close();

    $duplicate = $conn->prepare("SELECT id FROM resource_reports WHERE resource_id = ? AND reported_by = ? AND status = 'open'");
    $duplicate->bind_param("ii", $resourceId, $studentId);
    $duplicate->execute();
    $alreadyReported = $duplicate->get_result()->fetch_assoc();
    $duplicate->close();

    if (!$exists) {
        $message = "That resource is no longer available for reporting.";
        $messageType = "error";
    } elseif ($alreadyReported) {
        $message = "You already reported this resource.";
        $messageType = "error";
    } else {
        $report = $conn->prepare("INSERT INTO resource_reports (resource_id, reported_by, reason, details) VALUES (?, ?, ?, ?)");
        $report->bind_param("iiss", $resourceId, $studentId, $reason, $details);
        $report->execute();
        $report->close();
        $message = "Thank you. Your report was sent to an administrator.";
        $messageType = "success";
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["resubmit_resource"])) {
    verifyCsrf();
    $resourceId = (int) ($_POST["resource_id"] ?? 0);
    $title = trim($_POST["title"] ?? "");
    $subjectName = preg_replace('/\s+/', ' ', trim($_POST["subject_name"] ?? ""));
    $levelId = (int) ($_POST["level_id"] ?? 0);
    $file = $_FILES["resource_file"] ?? null;
    $old = $conn->prepare("SELECT file_path FROM resources WHERE id = ? AND submitted_by = ? AND status = 'rejected'");
    $old->bind_param("ii", $resourceId, $studentId);
    $old->execute();
    $oldResource = $old->get_result()->fetch_assoc();
    $old->close();

    if (!$oldResource || $title === "" || $subjectName === "" || !$levelId || !$file || $file["error"] !== UPLOAD_ERR_OK) {
        $message = "Complete every field and choose a new file for resubmission.";
        $messageType = "error";
    } else {
        $extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
        $mimeTypes = ["pdf" => "application/pdf", "docx" => "application/vnd.openxmlformats-officedocument.wordprocessingml.document"];
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file["tmp_name"]);
        if (!isset($mimeTypes[$extension]) || $mimeTypes[$extension] !== $mime) {
            $message = "Only valid PDF and DOCX files are allowed.";
            $messageType = "error";
        } else {
            $subject = $conn->prepare("INSERT IGNORE INTO subjects (name) VALUES (?)");
            $subject->bind_param("s", $subjectName);
            $subject->execute();
            $subject->close();
            $subject = $conn->prepare("SELECT id FROM subjects WHERE name = ? LIMIT 1");
            $subject->bind_param("s", $subjectName);
            $subject->execute();
            $subjectRow = $subject->get_result()->fetch_assoc();
            $subject->close();
            $safeName = uniqid("resource_", true) . "." . $extension;
            $relativePath = "uploads/" . $safeName;
            if (!$subjectRow || !move_uploaded_file($file["tmp_name"], __DIR__ . "/" . $relativePath)) {
                $message = "The revised file could not be saved.";
                $messageType = "error";
            } else {
                $update = $conn->prepare("UPDATE resources SET title = ?, original_file_name = ?, file_path = ?, file_type = ?, subject_id = ?, academic_level_id = ?, status = 'pending', rejection_reason = NULL, reviewed_by = NULL, reviewed_at = NULL WHERE id = ? AND submitted_by = ? AND status = 'rejected'");
                $update->bind_param("ssssiiii", $title, $file["name"], $relativePath, $extension, $subjectRow["id"], $levelId, $resourceId, $studentId);
                $update->execute();
                $update->close();
                $oldPath = __DIR__ . "/" . $oldResource["file_path"];
                if (is_file($oldPath)) unlink($oldPath);
                $message = "Your revised resource was resubmitted for approval.";
                $messageType = "success";
            }
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["upload_resource"])) {
    verifyCsrf();
    $title = trim($_POST["title"] ?? "");
    $subjectName = preg_replace('/\s+/', ' ', trim($_POST["subject_name"] ?? ""));
    $levelId = (int) ($_POST["level_id"] ?? 0);
    $file = $_FILES["resource_file"] ?? null;

    if ($title === "" || $subjectName === "" || !$levelId || !$file || $file["error"] !== UPLOAD_ERR_OK) {
        $message = "Please complete every field and choose a file.";
        $messageType = "error";
    } else {
        $extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
        $mimeTypes = [
            "pdf" => "application/pdf",
            "docx" => "application/vnd.openxmlformats-officedocument.wordprocessingml.document"
        ];
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file["tmp_name"]);

        if (!isset($mimeTypes[$extension]) || $mimeTypes[$extension] !== $mime) {
            $message = "Only valid PDF and DOCX files are allowed.";
            $messageType = "error";
        } else {
            $addSubject = $conn->prepare("INSERT IGNORE INTO subjects (name) VALUES (?)");
            $addSubject->bind_param("s", $subjectName);
            $addSubject->execute();
            $addSubject->close();

            $findSubject = $conn->prepare("SELECT id FROM subjects WHERE name = ? LIMIT 1");
            $findSubject->bind_param("s", $subjectName);
            $findSubject->execute();
            $subject = $findSubject->get_result()->fetch_assoc();
            $findSubject->close();

            $uploadFolder = __DIR__ . "/uploads";
            $safeName = uniqid("resource_", true) . "." . $extension;
            $relativePath = "uploads/" . $safeName;

            if (!$subject) {
                $message = "The subject could not be saved. Please try again.";
                $messageType = "error";
            } elseif (!is_dir($uploadFolder) && !mkdir($uploadFolder, 0755, true)) {
                $message = "The uploads folder could not be created.";
                $messageType = "error";
            } elseif (!move_uploaded_file($file["tmp_name"], __DIR__ . "/" . $relativePath)) {
                $message = "The file could not be saved. Check the uploads folder permissions.";
                $messageType = "error";
            } else {
                $save = $conn->prepare("INSERT INTO resources (title, original_file_name, file_path, file_type, subject_id, academic_level_id, submitted_by, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
                $save->bind_param("ssssiii", $title, $file["name"], $relativePath, $extension, $subject["id"], $levelId, $studentId);
                $save->execute();
                $save->close();
                $message = "Your resource was submitted for admin approval.";
                $messageType = "success";
            }
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    setFlash($message, $messageType ?: "error");
    header("Location: student.php");
    exit;
}

$levels = $conn->query("SELECT id, label, sort_order FROM academic_levels ORDER BY sort_order, label")->fetch_all(MYSQLI_ASSOC);
$highSchool = array_filter($levels, fn($level) => $level["sort_order"] < 20);
$college = array_filter($levels, fn($level) => $level["sort_order"] >= 20);

$search = trim($_GET["search"] ?? "");
$filterLevel = (int) ($_GET["level"] ?? 0);
$selectedLevel = "All academic levels";
foreach ($levels as $level) if ((int) $level["id"] === $filterLevel) { $selectedLevel = $level["label"]; break; }
$sort = $_GET["sort"] ?? "newest";
$sortSql = ["oldest" => "r.created_at ASC", "title" => "r.title ASC"][ $sort ] ?? "r.created_at DESC";
$page = max(1, (int) ($_GET["page"] ?? 1));
$perPage = 6;
$where = " WHERE r.status = 'approved'";
$params = [];
$types = "";

if ($search !== "") {
    $where .= " AND (r.title LIKE ? OR s.name LIKE ?)";
    $pattern = "%$search%";
    $params = [$pattern, $pattern];
    $types = "ss";
}
if ($filterLevel) {
    $where .= " AND r.academic_level_id = ?";
    $params[] = $filterLevel;
    $types .= "i";
}
$count = $conn->prepare("SELECT COUNT(*) AS total FROM resources r JOIN subjects s ON s.id = r.subject_id $where");
if ($types) $count->bind_param($types, ...$params);
$count->execute();
$totalResources = (int) $count->get_result()->fetch_assoc()["total"];
$count->close();
$totalPages = max(1, (int) ceil($totalResources / $perPage));
$page = min($page, $totalPages);
$offset = ($page - 1) * $perPage;

$sql = "SELECT r.id, r.title, r.original_file_name, r.file_type,
               s.name AS subject_name, a.label AS level_name, u.display_name
        FROM resources r
        JOIN subjects s ON s.id = r.subject_id
        JOIN academic_levels a ON a.id = r.academic_level_id
        JOIN users u ON u.id = r.submitted_by
        $where ORDER BY $sortSql LIMIT ? OFFSET ?";
$params[] = $perPage;
$params[] = $offset;
$types .= "ii";
$catalogue = $conn->prepare($sql);
$catalogue->bind_param($types, ...$params);
$catalogue->execute();
$resources = $catalogue->get_result();

$mine = $conn->prepare("SELECT r.id, r.title, r.file_path, r.status, r.rejection_reason,
    s.name AS subject_name, a.label AS level_name
    FROM resources r
    JOIN subjects s ON s.id = r.subject_id
    JOIN academic_levels a ON a.id = r.academic_level_id
    WHERE r.submitted_by = ? ORDER BY r.created_at DESC");
$mine->bind_param("i", $studentId);
$mine->execute();
$myResources = $mine->get_result();

function levelOptions(array $levels, int $selected = 0): void {
    foreach ($levels as $level) {
        $isSelected = $selected === (int) $level["id"] ? " selected" : "";
        echo '<option value="' . (int) $level["id"] . '"' . $isSelected . '>';
        echo e($level["label"]) . '</option>';
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Page | Resource Exchange</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header class="topbar">
    <a class="brand" href="index.php">Student Resource Exchange</a>
    <nav>
      
      <a class="active" href="student.php">Student page</a>
      <?php if ($student["role"] === "admin"): ?><a href="admin.php">Admin page</a><?php endif; ?>
      <a href="users.php">Members</a><a href="profile.php">Profile</a><a href="logout.php">Log out</a>
    </nav>
  </header>

  <main class="container">
    <section class="hero">
      <p class="eyebrow">STUDENT PAGE</p>
      <h1>Find, share, and reuse study material.</h1>
      <p>Search by title or any subject name. Share material from high school, college, or any other academic area.</p>
    </section>

    <?php if ($message): ?><p class="notice <?= e($messageType) ?>"><?= e($message) ?></p><?php endif; ?>
    <?php if ($pendingReports->num_rows): ?><section class="panel report-pending"><p class="eyebrow">PENDING REPORTS</p><h2>Reports awaiting administrator review</h2><div class="status-list"><?php while ($pending = $pendingReports->fetch_assoc()): ?><div class="status-row"><div><strong><?= e($pending["title"]) ?></strong><small><?= e(ucfirst($pending["reason"])) ?> · submitted <?= e($pending["created_at"]) ?></small><?php if ($pending["details"]): ?><small><?= e($pending["details"]) ?></small><?php endif; ?></div><span class="badge pending">Pending</span></div><?php endwhile; ?></div></section><?php endif; ?>
    <?php if ($notifications->num_rows): ?><section class="panel report-notifications"><p class="eyebrow">REPORT UPDATES</p><h2>Administrator feedback</h2><div class="status-list"><?php while ($notice = $notifications->fetch_assoc()): ?><div class="status-row"><div><strong><?= e($notice["resource_title"]) ?></strong><small>Report action: <?= e(ucfirst($notice["action"])) ?> · <?= e($notice["created_at"]) ?></small><?php if ($notice["feedback"]): ?><small><?= e($notice["feedback"]) ?></small><?php endif; ?></div><span class="badge <?= $notice["action"] === "deleted" ? "rejected" : "approved" ?>"><?= e($notice["action"]) ?></span></div><?php endwhile; ?></div></section><?php endif; ?>

    <section class="two-column">
      <article class="panel">
        <p class="eyebrow">UPLOAD RESOURCE</p>
        <h2>Share a study file</h2>
        <form method="post" enctype="multipart/form-data" class="form-grid"><input type="hidden" name="csrf_token" value="<?php echo csrfToken(); ?>">
          <label>Resource title
            <input type="text" name="title" required maxlength="180" placeholder="Example: Introduction to Psychology Notes">
          </label>
          <label>Subject name
            <input type="text" name="subject_name" required maxlength="150" placeholder="Enter any subject, for example Myanmar">
          </label>
          <label>Academic level
            <select name="level_id" required>
              <option value="">Choose level</option>
              <optgroup label="High School"><?php levelOptions($highSchool); ?></optgroup>
              <optgroup label="College"><?php levelOptions($college); ?></optgroup>
            </select>
          </label>
          <label>PDF or DOCX file
            <input type="file" name="resource_file" accept=".pdf,.docx" required><small>PDF or DOCX files only. The server may apply its own upload settings.</small>
          </label>
          <button type="submit" name="upload_resource">Submit for approval</button>
        </form>
      </article>

      <article class="panel">
        <p class="eyebrow">MY SUBMISSIONS</p>
        <h2>Submission status</h2>
        <div class="status-list">
          <?php if (!$myResources->num_rows): ?><p class="muted">You have not submitted any resources yet.</p><?php endif; ?>
          <?php while ($item = $myResources->fetch_assoc()): ?>
            <div class="status-row">
              <div>
                <strong><?= e($item["title"]) ?></strong>
                <small><?= e($item["level_name"] . " · " . $item["subject_name"]) ?></small>
                <?php if ($item["status"] === "rejected"): ?>
                  <small class="error-text">Reason: <?= e($item["rejection_reason"] ?: "Please revise and resubmit.") ?></small>
                  <details class="resubmit-box">
                    <summary>Edit and resubmit</summary>
                    <form method="post" enctype="multipart/form-data" class="resubmit-form">
                      <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                      <input type="hidden" name="resource_id" value="<?= (int) $item["id"] ?>">
                      <input type="text" name="title" value="<?= e($item["title"]) ?>" maxlength="180" required>
                      <input type="text" name="subject_name" value="<?= e($item["subject_name"]) ?>" maxlength="150" required>
                      <select name="level_id" required><?php levelOptions($levels); ?></select>
                      <input type="file" name="resource_file" accept=".pdf,.docx" required>
                      <button type="submit" name="resubmit_resource">Resubmit</button>
                    </form>
                  </details>
                <?php endif; ?>
              </div>
              <div>
                <span class="badge <?= e($item["status"]) ?>"><?= e($item["status"]) ?></span>
                <?php if ($item["status"] === "pending"): ?>
                  <form method="post" class="inline-form"><input type="hidden" name="csrf_token" value="<?php echo csrfToken(); ?>">
                    <input type="hidden" name="resource_id" value="<?= (int) $item["id"] ?>">
                    <button type="submit" name="delete_resource" class="danger small-button">Delete</button>
                  </form>
                <?php endif; ?>
              </div>
            </div>
          <?php endwhile; ?>
        </div>
      </article>
    </section>

    <section class="panel catalogue">
      <p class="eyebrow">RESOURCE CATALOGUE</p>
      <h2>Approved resources</h2>
      <p class="muted">Search approved resources by title or any subject name.</p>
      <form class="filter-bar" method="get">
        <input type="search" name="search" value="<?= e($search) ?>" placeholder="Search title or subject name...">
        <select name="level" onchange="this.form.submit()">
          <option value="0">All academic levels</option>
          <optgroup label="High School"><?php levelOptions($highSchool, $filterLevel); ?></optgroup>
          <optgroup label="College"><?php levelOptions($college, $filterLevel); ?></optgroup>
        </select>
        <select name="sort" onchange="this.form.submit()">
          <option value="newest" <?= $sort === "newest" ? "selected" : "" ?>>Newest first</option>
          <option value="oldest" <?= $sort === "oldest" ? "selected" : "" ?>>Oldest first</option>
          <option value="title" <?= $sort === "title" ? "selected" : "" ?>>Title A–Z</option>
        </select>
        <button type="submit">Search</button><?php if ($search !== "" || $filterLevel || $sort !== "newest"): ?><a class="text-link" href="student.php">Clear filters</a><?php endif; ?>
      </form>

      <p class="filter-summary muted">Showing: <strong><?= e($selectedLevel) ?></strong><?php if ($search !== ""): ?> · Search: <strong><?= e($search) ?></strong><?php endif; ?> · <?= $sort === "oldest" ? "Oldest first" : ($sort === "title" ? "Title A–Z" : "Newest first") ?></p><div class="resource-grid">
        <?php if (!$resources->num_rows): ?><p class="muted">No approved resources match these filters. Try another academic level or clear the filters.</p><?php endif; ?>
        <?php while ($resource = $resources->fetch_assoc()): ?>
          <article class="resource-card">
            <span class="file-type"><?= e(strtoupper($resource["file_type"])) ?></span>
            <h3><?= e($resource["title"]) ?></h3>
            <p><?= e($resource["level_name"] . " · " . $resource["subject_name"]) ?></p>
            <small>Shared by <?= e($resource["display_name"]) ?></small>
            <div class="resource-actions">
              <a class="download" href="resource_view.php?id=<?= (int) $resource["id"] ?>">Open resource</a>
              <button type="button" class="download secondary-action js-download"
                data-download-url="download.php?id=<?= (int) $resource["id"] ?>"
                data-download-name="<?= e($resource["original_file_name"]) ?>">Download resource</button>
              <details class="report-box">
                <summary>Report resource</summary>
                <form method="post" class="report-form">
                  <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                  <input type="hidden" name="resource_id" value="<?= (int) $resource["id"] ?>">
                  <select name="report_reason" required>
                    <option>Broken file</option><option>Wrong subject</option>
                    <option>Duplicate</option><option>Inappropriate content</option><option>Other</option>
                  </select>
                  <input type="text" name="report_details" maxlength="500" placeholder="Optional details">
                  <button type="submit" name="report_resource" class="danger small-button">Send report</button>
                </form>
              </details>
            </div>
          </article>
        <?php endwhile; ?>
      </div>
      <?php if ($totalPages > 1): ?>
        <nav class="pagination" aria-label="Catalogue pages">
          <?php for ($number = 1; $number <= $totalPages; $number++): ?>
            <a class="<?= $number === $page ? "active" : "" ?>" href="?search=<?= urlencode($search) ?>&level=<?= $filterLevel ?>&sort=<?= urlencode($sort) ?>&page=<?= $number ?>"><?= $number ?></a>
          <?php endfor; ?>
        </nav>
      <?php endif; ?>
    </section>
  </main>

  <script>
    document.querySelectorAll(".js-download").forEach(button => {
      button.addEventListener("click", async () => {
        if (button.disabled) return;
        button.disabled = true;
        const label = button.textContent;
        button.textContent = "Preparing download...";
        try {
          const response = await fetch(button.dataset.downloadUrl, { credentials: "same-origin" });
          if (!response.ok) throw new Error();
          const url = URL.createObjectURL(await response.blob());
          const link = document.createElement("a");
          link.href = url;
          link.download = button.dataset.downloadName || "resource";
          link.click();
          URL.revokeObjectURL(url);
        } catch {
          alert("The resource could not be downloaded. Please try again.");
        } finally {
          button.disabled = false;
          button.textContent = label;
        }
      });
    });
  </script>
</body>
</html>
