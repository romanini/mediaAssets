<?php

require_once("../app/bootstrap.php");

$imageID = $_REQUEST['id'];
$ext = $_REQUEST['ext'];
$height = $_REQUEST['height'];
$width = $_REQUEST['width'];

// TODO Clearly I would not normally do this with an entry point like this but rather use routes
// which would be provided for free by teh framework of choice like Symfony or Cake, or if not
// using a framework then write some kind of routing logic to make this more RESTful.
if ($imageID && $height && $width) {
    try {
        // TODO if the images were to be private then we should have a check to make sure that
        // only the owner (the account who uploaded it) is the account making this request.  Again
        // of course you will need Auth for this.
        $image = Image::createFromDb($imageID);
        $transformation = new Transformation($image,$width,$height,Image::getMime($ext));
        $transformedImage = $transformation->transform();
        $transformedImage->output();
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