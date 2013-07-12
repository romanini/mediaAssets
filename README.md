This is a demo on creating a media asset server to match the following requirements:

Create a small web service (no user interface necessary; POST from
curl/wget is fine) with two APIs. One that accepts uploads and stores SVG,
PNG, and JPEG files, and another that takes requests for a thumbnail (with
format and size info) and converts, resizes, and displays the uploaded
image as JPEG or PNG. Your code should shell out to ImageMagick to do the
actual resizing. The focus of your application should be in excellent
management of the upload process and on providing a sane service for
automated use; a user interface is unnecessary.


To make this work you should add the following to your mySQL server:

GRANT ALL PRIVILEGES ON *.* TO 'guest'@'localhost' IDENTIFIED BY PASSWORD '*11DB58B0DD02E290377535868405F11E4CBEFF58'

CREATE TABLE `asset` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) DEFAULT NULL,
  `path` varchar(256) DEFAULT NULL,
  `contentType` varchar(64) DEFAULT NULL,
  `createDate` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB

if you wish to use a mysql server on some other host you will need to edit the constants in model/Image.php

