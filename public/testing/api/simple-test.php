<?php
// Ultra simple test
echo "OK: Test endpoint is working";
echo "\nPHP Version: " . phpversion();
echo "\nCurrent Dir: " . __DIR__;
echo "\nParent Dir: " . dirname(__DIR__);
echo "\nFiles Dir: " . dirname(__DIR__) . '/files';
echo "\nFiles Dir Exists: " . (is_dir(dirname(__DIR__) . '/files') ? 'YES' : 'NO');
?>
