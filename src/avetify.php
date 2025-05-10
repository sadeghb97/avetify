<?php
$AVETIFY_ROOT_PATH = "";
$PHP_DOCUMENT_ROOT = "";

define("AVETIFY_VERSION", "0.11");
define("AVETIFY_BUILD", 2);

function initAvetify($rootPath, $phpDocumentRoot = ""){
    global $AVETIFY_ROOT_PATH;
    global $PHP_DOCUMENT_ROOT;
    $AVETIFY_ROOT_PATH = $rootPath;
    $PHP_DOCUMENT_ROOT = $phpDocumentRoot;
}
