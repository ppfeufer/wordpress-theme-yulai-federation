<?php

namespace WordPress\Themes\YulaiFederation\Helper;

\defined('ABSPATH') or die();

class FilesystemHelper {
    /**
     * instance
     *
     * static variable to keep the current (and only!) instance of this class
     *
     * @var Singleton
     */
    protected static $instance = null;

    public static function getInstance() {
        if(null === self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * clone
     *
     * no cloning allowed
     */
    protected function __clone() {
        ;
    }

    /**
     * constructor
     *
     * no external instanciation allowed
     */
    protected function __construct() {
        ;
    }

    /**
     * Removing either the content of a directory or the directory recursively
     *
     * @param string $directory
     * @param boolean $removeDirectory Remove the given Directoy as well? (true or false)
     */
    public function deleteDirectoryRecursive($directory, $removeDirectory = false) {
        // open dir and save it in a handle
        $entry = \opendir($directory);

        // read content of $dir and save it in $file
        while($file = \readdir($entry)) {
            $path = $directory . '/' . $file;

            if($file !== '.' && $file !== '..') {
                // check if handle is a dir or a file
                if(\is_dir($path)) {
                    $this->deleteDirectoryRecursive($path);
                } else {
                    \unlink($path);
                }
            }
        }

        // close dir handle
        \closedir($entry);

        if($removeDirectory === true) {
            //remove dir
            rmdir($directory);
        }
    }
}
