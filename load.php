<?php

use \Doctrine\Common\ClassLoader;
use \my\package\Class_Name as CN1;
use \my\package_name\Class_Name as CN;


function autoloader($className) {
    $path = '/some/path';
    $explodePath = explode('\\', $className);
    
    $fileName = array_pop($explodePath);
    $fileName = str_replace("_" , "/", $fileName);

    foreach($explodePath as $exp){
        $path .= "/" . $exp;
    }

    $file = str_replace("\\", '/', $path . '/' . $fileName . '.php');
    
    echo $file;

    if (file_exists($file)) {
        require $file;
    }
}
spl_autoload_register('autoloader');


$sd = new ClassLoader();