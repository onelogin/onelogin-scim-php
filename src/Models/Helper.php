<?php

namespace App\Models;

abstract class Helper
{
    /**
     * @param \DateTime $dateTime
     *
     * @return string
     */
    public static function dateTime2string(\DateTime $dateTime = null)
    {
        if (!isset($dateTime)) {
            $dateTime = new \Datetime("NOW");
        }
        if ($dateTime->getTimezone()->getName() === \DateTimeZone::UTC) {
            return $dateTime->format('Y-m-d\TH:i:s\Z');
        } else {
            return $dateTime->format('Y-m-d\TH:i:sP');
        }
    }

    /**
     * @param string        $string
     * @param \DateTimeZone $zone
     *
     * @return \DateTime
     */
    public static function string2dateTime($string, \DateTimeZone $zone = null)
    {
        if (!$zone) {
            $zone = new \DateTimeZone('UTC');
        }
        $dt = new \DateTime('now', $zone);
        $dt->setTimestamp(self::string2timestamp($string));
        return $dt;
    }

    /**
     * @param $string
     *
     * @return int
     */
    public static function string2timestamp($string)
    {
        $matches = array();
        if (!preg_match(
                '/^(\\d\\d\\d\\d)-(\\d\\d)-(\\d\\d)T(\\d\\d):(\\d\\d):(\\d\\d)(?:\\.\\d+)?Z$/D',
                $string,
                $matches
            )
        ) {
            throw new \InvalidArgumentException('Invalid timestamp: '.$string);
        }
        $year = intval($matches[1]);
        $month = intval($matches[2]);
        $day = intval($matches[3]);
        $hour = intval($matches[4]);
        $minute = intval($matches[5]);
        $second = intval($matches[6]);
        // Use gmmktime because the timestamp will always be given in UTC?
        $ts = gmmktime($hour, $minute, $second, $month, $day, $year);
        return $ts;
    }

    public static function getUserNameFromFilter($filter)
    {
        $username = null;
        if (preg_match('/userName eq \"([a-z0-9\_\.\-\@]*)\"/i', $filter, $matches) === 1) {
            $username = $matches[1];
        }
        return $username;
    }

    public static function gen_uuid() {
        $uuid4 = \Ramsey\Uuid\Uuid::uuid4();
        return $uuid4->toString();
    }
}
