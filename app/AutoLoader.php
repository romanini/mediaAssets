<?php

class AutoLoader
{
    public static function init()
    {
        // Check  __autoload() has been declared before registering our loader.
        //
        if (function_exists('__autoload')) 
        {
            spl_autoload_register('__autoload');
        }

        // Now register our autoload method.
        //
        spl_autoload_register(array('AutoLoader', '__autoload'));        
    }

    public static function __autoload($className)
    {
		include_once($className . '.php');
    }
}    

AutoLoader::init();

?>
