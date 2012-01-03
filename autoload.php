<?php

spl_autoload_register(function ($class) {
    $class = ltrim($class, '\\');
    
    if (0 === strpos($class, 'Karotz')) {
        $file = __DIR__.'/'.str_replace('\\', '/', $class).'.php';
        if (file_exists($file))
            require_once $file;
    }
});