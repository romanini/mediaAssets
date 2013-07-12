<?php

ini_set('include_path', __DIR__ . PATH_SEPARATOR .
        dirname(__DIR__) . '/app' . PATH_SEPARATOR .
        dirname(__DIR__) . '/model' . PATH_SEPARATOR .
        dirname(__DIR__) . '/util' . PATH_SEPARATOR .
        ini_get('include_path'));

require_once("AutoLoader.php");