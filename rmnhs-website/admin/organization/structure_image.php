<?php
require_once __DIR__ . '/../../connection/db_connection.php';

$dept = trim($_GET['dept'] ?? '');
if ($dept === '') {
    http_response_code(400);
    echo 'Department required';
    exit;
}

$sql = "SELECT mime, image FROM organizational_structure WHERE department = ? LIMIT 1";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo 'DB error';
    exit;
}
$stmt->bind_param('s', $dept);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows === 0) {
    $stmt->close();
    http_response_code(404);
    echo 'Not found';
    exit;
}
$stmt->bind_result($mime, $image);
$stmt->fetch();
$stmt->close();

if ($image === null || $image === '') {
    http_response_code(404);
    echo 'No image';
    exit;
}

header('Content-Type: ' . ($mime ?: 'application/octet-stream'));
header('Content-Length: ' . strlen($image));
echo $image;
exit;

?>
