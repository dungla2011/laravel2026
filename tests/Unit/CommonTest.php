<?php

namespace Tests\Unit;

error_reporting(E_ALL);
ini_set('display_errors', 1);

use PHPUnit\Framework\TestCase;

class CommonTest extends TestCase
{
    //Todo: kiểm tra tất cả các file, phải có 'return' trước rtJsonApi...
    /**
     * @return void
     */
    public function t1estValidReturnApiCode()
    {
        echo "\n \n testValidReturnApiCode 123 ";
        //return rtJsonApiError
        $fold = dirname(dirname(__DIR__)).'/app';
        $rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($fold));
        $cc = 0;
        foreach ($rii as $file) {
            if ($file->isDir()) {
                continue;
            }
            $cc++;
            $fn = $file->getPathname();
            if (! is_file($fn)) {
                continue;
            }
            $files[] = $fn;
            echo "\n$cc. '$fn' , ";
            echo "\n FS = $file / ".filesize($fn);
            //
            $cont = file_get_contents($fn);
            $mline = file($fn);
            foreach ($mline as $line) {
                echo "<br/>\n $line";
                if (strstr($line, 'rtJsonApiError(') && strstr($line, 'function rtJsonApiError(') === false && strstr($line, '_rtJson') === false) {
                    $this->assertStringContainsString('return rtJsonApiError(', $line);
                }
                if (strstr($line, 'rtJsonApiDone(') && strstr($line, 'function rtJsonApiDone(') === false && strstr($line, '_rtJson') === false) {
                    $this->assertStringContainsString('return rtJsonApiDone(', $line);
                }
            }
        }
    }
}
