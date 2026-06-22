<?php
define("NO_KEEP_STATISTIC", true);
define("NO_AGENT_STATISTIC", true);
define("NO_AGENT_CHECK", true);
define("NOT_CHECK_PERMISSIONS", true);

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Application;
use Bitrix\Main\IO\Path;

header('Content-Type: application/json');

$request = Application::getInstance()->getContext()->getRequest();

if (!$request->isPost() || !check_bitrix_sessid()) {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    die();
}

if (!CModule::IncludeModule('main')) {
    echo json_encode(['success' => false, 'error' => 'Module not installed']);
    die();
}

global $USER;
if (!$USER->IsAuthorized()) {
    echo json_encode(['success' => false, 'error' => 'Authorization required']);
    die();
}

$file = $request->getFile('file');

if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'error' => 'File upload error']);
    die();
}

$uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/upload/comments_files/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0775, true);
}

$fileName = md5(time() . $file['name']) . '.' . Path::getExtension($file['name']);
$filePath = $uploadDir . $fileName;
$webPath = '/upload/comments_files/' . $fileName;

if (move_uploaded_file($file['tmp_name'], $filePath)) {
    $isImage = CFile::IsImage($fileName);

    echo json_encode([
        'success' => true,
        'name' => $file['name'],
        'size' => $file['size'],
        'url' => $webPath,
        'isImage' => $isImage
    ]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to save file']);
}

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_after.php");
