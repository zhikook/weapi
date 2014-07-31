<?php
require __DIR__ . '/includes/WebStart.php';
require __DIR__ . '/includes/weapi/api.php';
require __DIR__ . '/includes/weapi/wikifile.php';
require __DIR__ . '/includes/weapi/template_api.php';

$id = 15;
$myImagepage = ImageWikiRequest::newImageRequest( $id );
$myImagepage->getDisplayedFile();
$mythumbnail = $myImagepage->transformImage();
print_r($mythumbnail);
?>