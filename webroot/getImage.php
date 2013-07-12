<?php

require_once("../app/bootstrap.php");

$imageID = $_REQUEST['id'];

if ($imageID ) {
    try {
        // TODO if the images were to be private then we should have a check to make sure that
        // only the owner (the account who uploaded it) is the account making this request.  Again
        // of course you will need Auth for this.
        $image = Image::createFromDb($imageID);
        $image->output();
    } catch (Exception $ex) {
        // TODO rather than return an 'error' here return a place holder image indicating an
        // error happened.  THis will make for a much better experience to the user.
        header("HTTP/1.1 404 " . $ex->getMessage(),true,404);
    }
} else {
    // TODO rather than return an 'error' here return a place holder image indicating an
    // error happened.  THis will make for a much better experience to the user.
    header("HTTP/1.1 404 Missing required params",true,404);
}