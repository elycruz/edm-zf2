<?php
require('../init_autoloader.php');

use Zend\Code;

class Zf1LibToZf2Lib {

    protected $directory;
    
    protected $splitFilePathAt = '/vender/';
    
    public function setLibDirectory (string $dir) {
        $this->directory = $dir;
    }
    
    public function splitPathAtLibRoot($pathStr) {
        $retVal = $pathStr;
        if (preg_match($this->splitPathAt, $pathStr) != -1) {
            $retVal = preg_split($this->splitPathAt, $pathStr)[1];
        }
        return $retVal;
    }

    public function getNamespaceForFileItem(SplFileInfo $item) {
        return splitPathAtLibRoot($item->getPathInfo());
    }

    public function getClassNameForItem(SplFileInfo $item) {
        return preg_split('/\./', $item->getFilename())[0];
    }

    public function processDirectoryItem(SplFileInfo $item) {
        $output = '';
        if ($item->isDir()) {
            return;
        }
        if (strtolower($item->getExtension()) == 'php') {
            $file = $item->openFile('w');
            $output .= 'namespace ' . getNamespaceForFileItem($item) . ';' .
                    'use Blank;';
            // Save changes to file
            // close file
            // and break loop
            // continue to next file

            $output .= '\n';
        }
        return $output;
    }

    public function run() {
        //var_dump(spl_classes());
//        $testFile = new \SplFileObject(__DIR__ . '/testfile.php', 'w');

        $rdi = new RecursiveIteratorIterator(
                        new RecursiveDirectoryIterator(
                                'C:\Users\ElyDeLaCruz\Documents\GitHub\edmzf2\vendor'));
        foreach ($rdi as $dir) {
//        $testFile->fwrite(processDirectoryItem($dir));
            processDirectoryItem($dir);
        }
    }
}
