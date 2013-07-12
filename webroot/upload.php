<?php

require_once("../app/bootstrap.php");

$uploadedFile = $_FILES;

if ($uploadedFile['Filedata']['name'] && $uploadedFile['Filedata']['tmp_name'] ) {
    try {
        // TODO record the user who is uploading the image, of course to do that you will
        // need to have set up Auth which I did not do as part of this.
        $image = Image::upload($uploadedFile['Filedata']['tmp_name'],$uploadedFile['Filedata']['name']);
        echo $image->toJson();
    } catch (Exception $ex) {
        echo json_encode(array("error"=>$ex->getMessage()));
    }
} else {
    echo json_encode(array("error"=>"missing file Filedata!"));
}
