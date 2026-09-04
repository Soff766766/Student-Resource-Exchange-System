<?php
require "config.php";
requireLogin();

$resourceId = (int) ($_GET["id"] ?? 0);
$statement = $conn->prepare("SELECT file_path, file_type FROM resources WHERE id = ? AND status = 'approved' LIMIT 1");
$statement->bind_param("i", $resourceId);
$statement->execute();
$resource = $statement->get_result()->fetch_assoc();
$statement->close();

if (!$resource || $resource["file_type"] !== "pdf") {
    http_response_code(404);
    exit("Only approved PDF resources can be opened in the browser.");
}

$uploadsRoot = realpath(__DIR__ . "/uploads");
$filePath = realpath(__DIR__ . "/" . $resource["file_path"]);
if (!$uploadsRoot || !$filePath || strpos($filePath, $uploadsRoot . DIRECTORY_SEPARATOR) !== 0 || !is_file($filePath)) {
    http_response_code(404);
    exit("The resource file is unavailable.");
}

header("Content-Type: application/pdf");
header("Content-Disposition: inline");
header("Content-Length: " . filesize($filePath));
header("Accept-Ranges: bytes");
readfile($filePath);
exit;
?>
