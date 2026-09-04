<?php
require "config.php";
requireLogin();

$resourceId = (int) ($_GET["id"] ?? 0);
$statement = $conn->prepare("SELECT r.id, r.title, r.original_file_name, r.file_type, r.subject_id, s.name AS subject_name, a.label AS level_name FROM resources r JOIN subjects s ON s.id = r.subject_id JOIN academic_levels a ON a.id = r.academic_level_id WHERE r.id = ? AND r.status = 'approved' LIMIT 1");
$statement->bind_param("i", $resourceId);
$statement->execute();
$resource = $statement->get_result()->fetch_assoc();
$statement->close();

if (!$resource) {
    http_response_code(404);
    exit("Resource not found or not approved.");
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo e($resource["title"]); ?> | Resource Viewer</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header class="topbar"><a class="brand" href="student.php">Student Resource Exchange</a><nav><a href="student.php">Back to resources</a><a href="logout.php">Log out</a></nav></header>
  <main class="container viewer-page">
    <section class="panel viewer-header">
      <div><p class="eyebrow">RESOURCE VIEWER</p><h1><?php echo e($resource["title"]); ?></h1><p class="muted"><?php echo e($resource["level_name"] . " · " . $resource["subject_name"]); ?></p></div>
      <a class="download" href="download.php?id=<?php echo $resourceId; ?>&download=1">Download resource</a>
    </section>
    <?php if ($resource["file_type"] === "pdf"): ?>
      <section class="panel document-viewer"><iframe title="<?php echo e($resource["title"]); ?>" src="view.php?id=<?php echo $resourceId; ?>"></iframe></section>
    <?php else: ?>
      <section class="panel empty-viewer"><h2>This file cannot be previewed in the browser.</h2><p class="muted">Download the DOCX file to open it with Microsoft Word or another compatible document application.</p><a class="download" href="download.php?id=<?php echo $resourceId; ?>&download=1">Download <?php echo e($resource["original_file_name"]); ?></a></section>
    <?php endif; ?>
  </main>
</body>
</html>
