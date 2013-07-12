<?php

class Image {
    /* TODO: these settings should go into a configuration manager which
     * is part of a framework if we use one, or we have to create one
     * not doing it as part of this example. */
    const DB_HOST = "localhost";
    const DB_USER = "guest";
    const DB_PASSWORD = "guest";
    const DB_NAME = "asset";

    private $id;
    private $name;
    private $path;
    private $location;
    private $contentType;
    private $createDate;

    public function __construct() {
        $this->createDate = DateUtil::getDate();
    }

    public static function upload($file,$name) {
        $image = new self();
        $finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
        $mime = finfo_file($finfo, $file);
        $image->setContentType($mime);
        $image->setName($name);
        $image->persist();
        $image->saveFile($file);
        return $image;
    }

    public static function createFromDb($id) {
        /* TODO: abstract out the DB coneection so we can use connection pooling
         * and not re-connect every time.
        */
        $conn = new mysqli(self::DB_HOST,self::DB_USER,self::DB_PASSWORD,self::DB_NAME);
        if ($conn->connect_errno) {
            throw new Exception("Failed to connect to database: " . $conn->connect_error);
        }
        // TODO we would want to use prepared statements here or ORM like doctrine
        if ($result = $conn->query(sprintf("select id,name,path,contentType,createDate from asset where id = %s", $id))) {
            if ($result->num_rows == 0) {
                $result->close();
                $conn->close();
                throw new Exception("Image " . $id . " not found");
            }
            $imageArray = $result->fetch_assoc();
            $result->close();
            $image = new Image();
            $image->setId($imageArray['id']);
            $image->setCreateDate($imageArray['createDate']);
            $image->setContentType($imageArray['contentType']);
            $image->setPath($imageArray['path']);
            $image->setName($imageArray['name']);
        }
        $conn->close();
        return $image;
    }

    public function persist() {
        /* TODO: abstract out the DB connection so we can use connection pooling
         * and not re-connect every time.
        */
        $conn = new mysqli(self::DB_HOST,self::DB_USER,self::DB_PASSWORD,self::DB_NAME);
        if ($conn->connect_errno) {
            throw new Exception("Failed to connect to database: " . $conn->connect_error);
        }
        if ($this->getId()) {
            // TODO we would want to use prepared statements here or ORM like doctrine
            $SQL = sprintf("update asset set name = '%s', path = '%s', contentType = '%s' where id = %s",
                                $this->getName(),
                                $this->getPath(),
                                $this->getContentType(),
                                $this->getId());
            $conn->query($SQL);
            $conn->close();
        } else {
            // TODO we would want to use prepared statements here or ORM like doctrine
            $SQL = sprintf("insert into asset (name, path, contentType, createDate) values ('%s','%s', '%s','%s')",
                                $this->getName(),
                                $this->getPath(),
                                $this->getContentType(),
                                $this->getCreateDate());
            $conn->query($SQL);
            $this->setId($conn->insert_id);
            $conn->close();
        }
    }

    public static function getExt($mime) {
        switch ($mime) {
            case 'image/jpeg':
                $ext = 'jpg';
                break;
            case 'image/png':
                $ext = 'png';
                break;
            case 'image/svg+xml':
                $ext = 'svg';
                break;
            default:
                throw new Exception("$mime is not a supported content type");
        }
        return $ext;
    }

    public static function getMime($ext) {
        switch ($ext) {
            case 'jpg':
                $mime = 'image/jpeg';
                break;
            case 'png':
                $mime = 'image/png';
                break;
            case 'svg':
                $mime = 'image/svg+xml';
                break;
            default:
                throw new Exception("$ext is not a supported type");
        }
        return $mime;
    }


    private function saveFile($file) {
        if (!is_dir($this->getPath())) {
            mkdir($this->getPath(),0777,true);
        }
        move_uploaded_file($file,$this->getLocation());
    }

    /**
     * @param bool|string $createDate
     */
    public function setCreateDate($createDate)
    {
        $this->createDate = $createDate;
    }

    /**
     * @return mixed
     */
    public function getCreateDate()
    {
        return $this->createDate;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $location
     */
    public function setPath($location)
    {
        $this->path = $location;
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        if (!$this->path) {
            // TODO if this is part of a bigger system it is likely that this comes from an asset management class
            // if we don't know it yet then calculate the location  we don't do this in the
            // constructor because date can change of course.
            $createDateParts = DateUtil::parse($this->getCreateDate());
            $this->setPath(dirname(__DIR__) . '/assets/' . $createDateParts['year'] . '/' . $createDateParts['month'] . '/' . $createDateParts['day']);
        }
        return $this->path;
    }

    public function setLocation($location) {
        $this->location = $location;
    }

    public function getLocation()
    {
        if ($this->location) {
            return $this->location;
        } else {
            $ext = self::getExt($this->getContentType());
            return $this->getPath() . '/' . $this->getId() . '.' . $ext;
        }
    }

    /**
     * @param mixed $contentType
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
    }

    /**
     * @return mixed
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    public function toJson() {
        // this is just a quick and dirty way to return the object not exactly what we
        // would do in production.
        $hash = array();
        $hash['id'] = $this->getId();
        $hash['name'] = $this->getName();
        $hash['createDate'] = $this->getCreateDate();
        return json_encode($hash);
    }

    public function output() {
        $fileContents = file_get_contents($this->getLocation());
        header('Content-type: ' . $this->getContentType());
        header('Content-Length: ' . strlen($fileContents));
        $now = time();
        // we might as well let the browser cache this and for that matter if we are
        // behind a cache layer like an LB or Squid or Varnish then they will cache it
        // in the server side as well.
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $now) . 'GMT');
        header('Expires: ' . gmdate('D, d M Y H:i:s', $now+(3600*24*30)) . 'GMT');
        echo $fileContents;
    }
}