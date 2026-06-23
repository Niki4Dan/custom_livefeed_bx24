<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

$response = ['success' => false];

if ($_FILES['image']) {
    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/upload/images/editor/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $filename = 'editor_' . time() . '_' . md5($_FILES['image']['name']) . '.' . $extension;
    $localPath = $uploadDir . $filename;
    $webPath = '/upload/images/editor/' . $filename;
    
    if (move_uploaded_file($_FILES['image']['tmp_name'], $localPath)) {
        $response = ['success' => true, 'file' => $webPath];
    }
}

header('Content-Type: application/json');
echo json_encode($response);