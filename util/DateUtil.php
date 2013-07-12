<?php
/**
 * Created by JetBrains PhpStorm.
 * User: shawn
 * Date: 3/6/12
 * Time: 2:54 PM
 * To change this template use File | Settings | File Templates.
 */
class DateUtil
{
    const DATE_FORMAT = "Y-m-d H:i:s";

    public static function getTime()
    {
        return time();
    }

    public static function getDate($time = null)
    {
        return date(self::DATE_FORMAT, ($time ? $time : self::getTime()));
    }

    public static function parse($dateString) {
        return date_parse_from_format(self::DATE_FORMAT,$dateString);
    }
}

?>
