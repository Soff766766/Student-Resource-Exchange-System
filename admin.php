<?php
require "config.php";

$admin = requireAdmin();
$adminId = (int) $admin["id"];
$flash = pullFlash();
$message = $flash["message"] ?? "";
$messageType = $flash["type"] ?? "success";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["review_resource"])) {
    verifyCsrf();
    $resourceId = (int) $_POST["resource_id"];
    $decision = $_POST["review_resource"] === "approved" ? "approved" : "rejected";
    $reason = trim($_POST["rejection_reason"] ?? "");
    if ($decision === "rejected" && $reason === "") $reason = "The submission did not meet the review requirements.";
    $statement = $conn->prepare("UPDATE resources SET status = ?, rejection_reason = ?, reviewed_by = ?, reviewed_at = NOW() WHERE id = ? AND status = 'pending'");
    $statement->bind_param("ssii", $decision, $reason, $adminId, $resourceId);
    $statement->execute();
    if ($statement->affected_rows) {
        $history = $conn->prepare("INSERT INTO moderation_history (resource_id, admin_id, decision, reason) VALUES (?, ?, ?, ?)");
        $history->bind_param("iiss", $resourceId, $adminId, $decision, $reason);
        $history->execute();
        $history->close();
    }
    $message = "Resource has been " . $decision . ".";
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["resolve_report"])) {
    verifyCsrf();
    $reportId = (int) ($_POST["report_id"] ?? 0);
    $action = ($_POST["report_action"] ?? "kept") === "deleted" ? "deleted" : "kept";
    $feedback = trim($_POST["admin_feedback"] ?? "");
    $find = $conn->prepare("SELECT rr.resource_id, rr.reported_by, r.title, r.file_path FROM resource_reports rr JOIN resources r ON r.id = rr.resource_id WHERE rr.id = ? AND rr.status = 'open' LIMIT 1");
    $find->bind_param("i", $reportId); $find->execute(); $reported = $find->get_result()->fetch_assoc(); $find->close();
    if (!$reported) { $message = "This report has already been handled."; $messageType = "error"; }
    else {
        $conn->begin_transaction();
        try {
            $notification = $conn->prepare("INSERT INTO report_notifications (report_id, student_id, resource_title, action, feedback) VALUES (?, ?, ?, ?, ?)");
            $notification->bind_param("iisss", $reportId, $reported["reported_by"], $reported["title"], $action, $feedback); $notification->execute(); $notification->close();
            $resolve = $conn->prepare("UPDATE resource_reports SET status = 'resolved', admin_feedback = ?, admin_action = ?, actioned_by = ?, actioned_at = NOW(), resolved_by = ?, resolved_at = NOW() WHERE id = ? AND status = 'open'");
            $resolve->bind_param("ssiii", $feedback, $action, $adminId, $adminId, $reportId); $resolve->execute(); $resolve->close();
            if ($action === "deleted") { $delete = $conn->prepare("DELETE FROM resources WHERE id = ?"); $delete->bind_param("i", $reported["resource_id"]); $delete->execute(); $delete->close(); }
            $conn->commit();
            if ($action === "deleted" && is_file(__DIR__ . "/" . $reported["file_path"])) @unlink(__DIR__ . "/" . $reported["file_path"]);
            $message = $action === "deleted" ? "Report handled: resource deleted and student notified." : "Report handled: resource kept and student notified.";
        } catch (Throwable $error) { $conn->rollback(); $message = "The report could not be processed."; $messageType = "error"; }
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["change_role"])) {
    verifyCsrf();
    $userId = (int) $_POST["user_id"];
    $role = $_POST["role"] === "admin" ? "admin" : "student";
    $adminCount = (int) $conn->query("SELECT COUNT(*) AS total FROM users WHERE role = 'admin'")->fetch_assoc()["total"];

    if ($userId === $adminId && $role !== "admin") {
        $message = "You cannot remove your own administrator access.";
        $messageType = "error";
    } elseif ($role !== "admin" && $adminCount <= 1) {
        $message = "The final administrator account must remain active.";
        $messageType = "error";
    } else {
        $statement = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
        $statement->bind_param("si", $role, $userId);
        $statement->execute();
        $statement->close();
        $message = "User role updated.";
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    setFlash($message, $messageType);
    header("Location: admin.php");
    exit;
}

$pending = $conn->query("SELECT r.id, r.title, r.original_file_name, r.file_path, r.file_type, r.created_at, s.name AS subject_name, a.label AS level_name, u.display_name FROM resources r JOIN subjects s ON r.subject_id = s.id JOIN academic_levels a ON r.academic_level_id = a.id JOIN users u ON r.submitted_by = u.id WHERE r.status = 'pending' ORDER BY r.created_at ASC");
$users = $conn->query("SELECT id, display_name, email, role FROM users ORDER BY display_name");
$approvedTotal = $conn->query("SELECT COUNT(*) AS total FROM resources WHERE status = 'approved'")->fetch_assoc()['total'];
$rejectedTotal = $conn->query("SELECT COUNT(*) AS total FROM resources WHERE status = 'rejected'")->fetch_assoc()['total'];
$resourceTotal = $conn->query("SELECT COUNT(*) AS total FROM resources")->fetch_assoc()['total'];
$subjectTotal = $conn->query("SELECT COUNT(*) AS total FROM subjects")->fetch_assoc()['total'];
$levelStats = $conn->query("SELECT a.label, COUNT(r.id) AS total FROM academic_levels a LEFT JOIN resources r ON r.academic_level_id = a.id GROUP BY a.id, a.label, a.sort_order ORDER BY a.sort_order")->fetch_all(MYSQLI_ASSOC);
$reports = $conn->query("SELECT rr.id, rr.reason, rr.details, rr.created_at, r.title, u.display_name, u.id AS reporter_id FROM resource_reports rr JOIN resources r ON r.id = rr.resource_id JOIN users u ON u.id = rr.reported_by WHERE rr.status = 'open' ORDER BY rr.created_at ASC");
$history = $conn->query("SELECT h.decision, h.reason, h.created_at, r.title, u.display_name FROM moderation_history h JOIN resources r ON r.id = h.resource_id JOIN users u ON u.id = h.admin_id ORDER BY h.created_at DESC LIMIT 12");
$pendingTotal = $pending->num_rows;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Page | Resource Exchange</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header class="topbar"><a class="brand" href="index.php">Student Resource Exchange</a><nav><a class="active" href="admin.php">Admin page</a><a href="users.php">Members</a><a href="profile.php">Profile</a><a href="logout.php">Log out</a></nav></header>
  <main class="container">
    <section class="hero"><p class="eyebrow">ADMIN PAGE</p><h1>Keep the resource library useful and safe.</h1><p>Review new submissions, decide what becomes visible to students, and manage account roles.</p></section>
    <?php if ($message): ?><p class="notice <?php echo htmlspecialchars($messageType); ?>"><?php echo htmlspecialchars($message); ?></p><?php endif; ?>
    <section class="stats">
      <article><strong><?php echo $resourceTotal; ?></strong><span>Total resources</span></article>
      <article><strong><?php echo $pendingTotal; ?></strong><span>Pending review</span></article>
      <article><strong><?php echo $approvedTotal; ?></strong><span>Approved resources</span></article>
      <article><strong><?php echo $rejectedTotal; ?></strong><span>Rejected resources</span></article>
      <article><strong><?php echo $users->num_rows; ?></strong><span>Registered users</span></article>
      <article><strong><?php echo $subjectTotal; ?></strong><span>Subjects</span></article>
    </section>
    <section class="panel analytics">
      <p class="eyebrow">RESOURCE ANALYTICS</p>
      <h2>Resources by academic level</h2>
      <?php $maxLevelTotal = max(1, ...array_column($levelStats, "total")); ?>
      <div class="analytics-list">
        <?php foreach ($levelStats as $level): ?>
          <div class="analytics-row">
            <span><?php echo htmlspecialchars($level["label"]); ?></span>
            <div class="bar"><i style="width: <?php echo (int) ($level["total"] / $maxLevelTotal * 100); ?>%"></i></div>
            <strong><?php echo (int) $level["total"]; ?></strong>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
    <section class="panel">
      <p class="eyebrow">MODERATION HISTORY</p>
      <h2>Recent decisions</h2>
      <div class="status-list">
        <?php if (!$history->num_rows): ?><p class="muted">No moderation decisions have been recorded yet.</p><?php endif; ?>
        <?php while ($event = $history->fetch_assoc()): ?>
          <div class="status-row"><div><strong><?= e($event["title"]) ?></strong><small><?= e($event["display_name"] . " · " . $event["created_at"]) ?></small><?php if ($event["reason"]): ?><small><?= e($event["reason"]) ?></small><?php endif; ?></div><span class="badge <?= e($event["decision"]) ?>"><?= e($event["decision"]) ?></span></div>
        <?php endwhile; ?>
      </div>
    </section>
    <section class="panel">
      <p class="eyebrow">RESOURCE REPORTS</p>
      <h2>Pending reports</h2>
      <p class="muted">Review student reports and send a keep or delete decision.</p>
      <div class="moderation-list">
        <?php if (!$reports->num_rows): ?><p class="muted">There are no open reports.</p><?php endif; ?>
        <?php while ($report = $reports->fetch_assoc()): ?>
          <article class="moderation-card">
            <div><strong><?= htmlspecialchars($report["title"]) ?></strong><small><?= htmlspecialchars($report["reason"] . " · reported by " . $report["display_name"]) ?></small><?php if ($report["details"]): ?><small><?= htmlspecialchars($report["details"]) ?></small><?php endif; ?></div>
            <form method="post" class="report-action-form"><input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>"><input type="hidden" name="report_id" value="<?= (int) $report["id"] ?>"><textarea name="admin_feedback" rows="2" maxlength="500" required placeholder="Feedback to the reporting student"></textarea><select name="report_action" required><option value="kept">Keep resource</option><option value="deleted">Delete resource</option></select><button name="resolve_report" type="submit">Send decision</button></form>
          </article>
        <?php endwhile; ?>
      </div>
    </section>
    <section class="panel"><p class="eyebrow">MODERATION QUEUE</p><h2>Pending uploads</h2><div class="moderation-list"><?php if ($pending->num_rows === 0): ?><p class="muted">There are no pending resources to review.</p><?php endif; ?><?php while ($resource = $pending->fetch_assoc()): ?><article class="moderation-card"><div><span class="file-type"><?php echo strtoupper(htmlspecialchars($resource['file_type'])); ?></span><h3><?php echo htmlspecialchars($resource['title']); ?></h3><p><?php echo htmlspecialchars($resource['level_name'] . " · " . $resource['subject_name'] . " · submitted by " . $resource['display_name']); ?></p><a class="text-link" href="<?php echo htmlspecialchars($resource['file_path']); ?>" target="_blank">Open file for review</a></div><form method="post" class="decision-form"><input type="hidden" name="csrf_token" value="<?php echo csrfToken(); ?>">
  <input type="hidden" name="resource_id" value="<?php echo (int) $resource['id']; ?>">
  <textarea name="rejection_reason" rows="2" maxlength="500" placeholder="Reason if rejecting (optional)"></textarea>
  <button name="review_resource" value="approved" type="submit">Approve</button>
  <button class="danger" name="review_resource" value="rejected" type="submit">Reject</button>
</form></article><?php endwhile; ?></div></section>
    <section class="panel"><p class="eyebrow">USER MANAGEMENT</p><h2>Change a user role</h2><div class="table-wrap"><table><thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Save</th></tr></thead><tbody><?php while ($user = $users->fetch_assoc()): ?><tr><form method="post"><input type="hidden" name="csrf_token" value="<?php echo csrfToken(); ?>"><td><?php echo htmlspecialchars($user['display_name']); ?><input type="hidden" name="user_id" value="<?php echo $user['id']; ?>"></td><td><?php echo htmlspecialchars($user['email']); ?></td><td><select name="role"><option value="student" <?php if ($user['role'] === 'student') echo 'selected'; ?>>Student</option><option value="admin" <?php if ($user['role'] === 'admin') echo 'selected'; ?>>Admin</option></select></td><td><button type="submit" name="change_role">Update</button></td></form></tr><?php endwhile; ?></tbody></table></div></section>
  </main>
</body>
</html>
