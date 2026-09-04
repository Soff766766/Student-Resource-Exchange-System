<?php
require "config.php";
requireLogin();

$resourceId = (int) ($_GET["id"] ?? 0);
$statement = $conn->prepare("SELECT original_file_name, file_path, file_type FROM resources WHERE id = ? AND status = 'approved' LIMIT 1");
$statement->bind_param("i", $resourceId);
$statement->execute();
$resource = $statement->get_result()->fetch_assoc();
$statement->close();

if (!$resource) {
    http_response_code(404);
    exit("Resource not found or not approved.");
}

$uploadsRoot = realpath(__DIR__ . "/uploads");
$filePath = realpath(__DIR__ . "/" . $resource["file_path"]);
if (!$uploadsRoot || !$filePath || strpos($filePath, $uploadsRoot . DIRECTORY_SEPARATOR) !== 0 || !is_file($filePath)) {
    http_response_code(404);
    exit("The resource file is unavailable.");
}

$originalName = basename($resource["original_file_name"]);
$downloadName = preg_replace('/[\x00-\x1F\x7F"\\\\]/u', "_", $originalName);
$downloadName = trim($downloadName);
if ($downloadName === "") {
    $downloadName = "resource_" . $resourceId . "." . $resource["file_type"];
}
$asciiFallback = @iconv("UTF-8", "ASCII//TRANSLIT//IGNORE", $downloadName);
$asciiFallback = preg_replace('/[^A-Za-z0-9._ -]/', "_", $asciiFallback ?: "");
$asciiFallback = trim($asciiFallback);
if ($asciiFallback === "" || $asciiFallback === "." . $resource["file_type"]) {
    $asciiFallback = "resource_" . $resourceId . "." . $resource["file_type"];
}
$mimeTypes = [
    "pdf" => "application/pdf",
    "docx" => "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
];
header("Content-Type: " . ($mimeTypes[$resource["file_type"]] ?? "application/octet-stream"));
$disposition = (($_GET["download"] ?? "") === "1") ? "attachment" : "inline";
header("Content-Disposition: " . $disposition . "; filename=\"" . $asciiFallback . "\"; filename*=UTF-8''" . rawurlencode($downloadName));
header("Content-Length: " . filesize($filePath));
readfile($filePath);
exit;
?>
