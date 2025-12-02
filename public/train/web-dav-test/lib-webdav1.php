<?php

class RemoteFile1 extends \Sabre\DAV\FS\File {
    protected $path;

    function __construct($path) {
        $this->path = $path;
    }

    function getName() {
        return basename($this->path);
    }

    function get() {
        // Replace this with a call to your API
        return file_get_contents($this->path);
    }

    function getSize() {
        // Replace this with a call to your API
        return filesize($this->path);
    }
}



class RemoteDirectory1 extends \Sabre\DAV\FS\Directory {
    protected $path;

    function __construct($path) {
        $this->path = $path;
    }

    function getName() {
        return basename($this->path);
    }

    function getChildren() {
        // Replace this with a call to your API
        $children = [];
        foreach (scandir($this->path) as $file) {
            if ($file === '.' || $file === '..') continue;
            $children[] = is_dir($this->path . '/' . $file)
                ? new RemoteDirectory1($this->path . '/' . $file)
                : new RemoteFile1($this->path . '/' . $file);
        }
        return $children;
    }

    function childExists($name) {
        // Replace this with a call to your API
        return file_exists($this->path . '/' . $name);
    }

    public function createFile($name, $data = null)
    {
        $newPath = $this->path.'/'.$name;

//        die("PATH = " . $newPath);

        file_put_contents($newPath, $data);
        clearstatcache(true, $newPath);
    }


//    function getChild($name) {
//        // Replace this with a call to your API
//        $path = $this->path . '/' . $name;
//        return is_dir($path)
//            ? new RemoteDirectory1($path)
//            : new RemoteFile1($path);
//    }
}
