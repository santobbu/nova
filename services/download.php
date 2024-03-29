<?php
if(!defined('_VALID_SETTING')){define( '_VALID_SETTING', 0x0001 );}

ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

require_once( 'module/applicationSettings.php' );

$data = get_param( 'GET' );
$dir = './slip/' . $data['startdate'];
$zip_file = './slip/archive/'.$data['startdate'] .'_slip.zip';

// Get real path for our folder
$rootPath = realpath($dir);
if(!is_dir($dir)) {
    echo '<h2>ไม่พบรูปตามวันที่ระบุ</h2>';
    return;
}

// Initialize archive object
$zip = new ZipArchive();
$zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE);

// Create recursive directory iterator
/** @var SplFileInfo[] $files */
$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($rootPath),
    RecursiveIteratorIterator::LEAVES_ONLY
);

foreach ($files as $name => $file)
{
    // Skip directories (they would be added automatically)
    if (!$file->isDir())
    {
        // Get real and relative path for current file
        $filePath = $file->getRealPath();
        $relativePath = substr($filePath, strlen($rootPath) + 1);

        // Add current file to archive
        $zip->addFile($filePath, $relativePath);
    }
}

// Zip archive will be created only after closing object
$zip->close();


header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename='.basename($zip_file));
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($zip_file));
readfile($zip_file);

?>