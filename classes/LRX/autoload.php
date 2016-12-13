<?php

namespace LRX;

//require_once "configuracao.php";
//require_once "functions.php";

/**
 * PSR-4 Complient Autoloader adapted from their examples
 *
 * After registering this autoload function with SPL, the following line
 * would cause the function to attempt to load the \LRX\Qux class
 * from /path/to/project/class/Qux.class.php:
 *
 *      new \Foo\Bar\Baz\Qux;
 *
 * @param string $class The fully-qualified class name.
 * @return void
 */
spl_autoload_register(function ($class) {

    // project-specific namespace prefix
    $prefix = 'LRX\\';

    // base directory for the namespace prefix
    $base_dir = __DIR__ . '/';

    // does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // no, move to the next registered autoloader
        return;
    }

    // get the relative class name
    $relative_class = substr($class, $len);

    // replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    $fileClass     = $base_dir . str_replace('\\', '/', $relative_class) . '.class.php';
    $fileInterface = $base_dir . str_replace('\\', '/', $relative_class) . '.interface.php';
    $fileException = $base_dir . str_replace('\\', '/', $relative_class) . '.exception.php';

    // echo $file;

    // if the file exists, require it
    if (file_exists($fileClass)) {
        require_once $fileClass;
    } else if (file_exists($fileInterface)) {
        require_once $fileInterface;
    } else if (file_exists($fileException)) {
        require_once $fileException;
    }
});