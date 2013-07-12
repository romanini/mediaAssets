<?php

class Transformation {

    const CONVERT = '/usr/bin/convert';
    private $sourceImage;
    private $targetContentType;
    private $targetHeight;
    private $targetWidth;

    public function __construct(Image $sourceImage, $width, $height, $contentType = null) {
        $this->sourceImage = $sourceImage;
        $this->targetWidth = $width;
        $this->targetHeight = $height;
        $this->targetContentType = $contentType;
    }

    public function transform() {
        $transformedFile = sys_get_temp_dir() . '/' .
                            $this->getSourceImage()->getId() . '-' .
                            $this->getTargetHeight() . '-' .
                            $this->getTargetWidth() . '.' .
                            Image::getExt($this->getTargetContentType());

        $cmd = self::CONVERT . ' ' . $this->getSourceImage()->getLocation() . ' -resize ' .
                $this->getTargetWidth() . 'x' . $this->getTargetHeight() . '\> ' .
                $transformedFile;
        SysUtil::runExternal($cmd,$output);
        if (!is_file($transformedFile)) {
            throw new Exception("Could not transform image");
        }
        $transformedImage = new Image();
        $transformedImage->setName($this->getSourceImage()->getName());
        $transformedImage->setId($this->getSourceImage()->getId());
        $transformedImage->setCreateDate($this->getSourceImage()->getCreateDate());
        $transformedImage->setContentType($this->getTargetContentType());
        $transformedImage->setLocation($transformedFile);
        return $transformedImage;
    }

    /**
     * @param mixed $sourceImage
     */
    public function setSourceImage(Image $sourceImage)
    {
        $this->sourceImage = $sourceImage;
    }

    /**
     * @return mixed
     */
    public function getSourceImage()
    {
        return $this->sourceImage;
    }

    /**
     * @param mixed $targetContentType
     */
    public function setTargetContentType($targetContentType)
    {
        $this->targetContentType = $targetContentType;
    }

    /**
     * @return mixed
     */
    public function getTargetContentType()
    {
        if ($this->targetContentType) {
            return $this->targetContentType;
        } else {
            return $this->getSourceImage()->getContentType();
        }
    }

    /**
     * @param mixed $targetHeight
     */
    public function setTargetHeight($targetHeight)
    {
        $this->targetHeight = $targetHeight;
    }

    /**
     * @return mixed
     */
    public function getTargetHeight()
    {
        return $this->targetHeight;
    }

    /**
     * @param mixed $targetWidth
     */
    public function setTargetWidth($targetWidth)
    {
        $this->targetWidth = $targetWidth;
    }

    /**
     * @return mixed
     */
    public function getTargetWidth()
    {
        return $this->targetWidth;
    }


}