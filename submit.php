<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$name       = trim($_POST['num_id'] ?? '');
$visitorId  = trim($_POST['visitor_id'] ?? '');
$components = $_POST['components'] ?? '';
$consent    = $_POST['consent'] ?? '';

// Validation
if ($consent !== '1'
    || $name === ''
    || mb_strlen($name) > 255
    || !preg_match('/^[a-f0-9]{32}$/', $visitorId)
    || $components === ''
    || strlen($components) > 10000000   // MEDIUMTEXT holds ~16 MB; this keeps it under
    || json_decode($components) === null) {
    header('Location: index.php?status=error');
    exit;
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset('utf8mb4');

    $stmt = $db->prepare(
        'INSERT INTO participants (num_id, visitor_id, components) VALUES (?, ?, ?)'
    );
    $stmt->bind_param('sss', $name, $visitorId, $components);
    $stmt->execute();
    $stmt->close();
    $db->close();

    header('Location: index.php?status=ok');
} catch (Exception $e) {
    error_log($e->getMessage());
    header('Location: index.php?status=error');
}
exit;