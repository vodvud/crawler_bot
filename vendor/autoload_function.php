<?php
return function ($className){     
    $class = __DIR__.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.str_replace('\\', DIRECTORY_SEPARATOR, $className).'.php';

    if(is_file($class)){
        return include($class);
    }else{
        return false;
    }
};
