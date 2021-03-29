<?php
declare(strict_types=1);


namespace EasySwoole\Skeleton\Helpers;

use DateTime;

class DateHelper
{
    static public function getTodayZero()
    {
        return strtotime(date('Y-m-d 00:00:00', time()));
    }

    /**
     * @param $time
     * @return string
     * @throws \Exception
     */
    static public function gmt_iso8601($time) {
        $dtStr = date("c", $time);
        $mydatetime = new DateTime($dtStr);
        $expiration = $mydatetime->format(DateTime::ISO8601);
        $pos = strpos($expiration, '+');
        $expiration = substr($expiration, 0, $pos);
        return $expiration."Z";
    }

    /**
     * 时间格式化
     *
     * @param string /int  $date  时间/时间戳
     * @param bool $isInt 是否为int
     *
     * @return array
     * @author lengbin(lengbin0@gmail.com)
     */
    public static function formatDay($date, $isInt = true)
    {
        return self::formatDays($date, $date, $isInt);
    }

    /**
     * 双日期 格式化
     *
     * @param string $date      双日期
     * @param string $separator 分割符
     * @param bool   $isInt     是否为int
     *
     * @return array
     */
    public static function formatDoubleDate($date, $separator = ' - ', $isInt = true)
    {
        $dates = explode($separator, $date);
        return self::formatDays($dates[0], $dates[1], $isInt);
    }

    /**
     * 时间格式化
     *
     * @param string /int  $start  时间/时间戳
     * @param string /int  $end  时间/时间戳
     * @param bool $isInt 是否为int
     *
     * @return array
     * @author lengbin(lengbin0@gmail.com)
     */
    public static function formatDays($start, $end, $isInt = true)
    {
        if (is_int($start)) {
            $start = date('Y-m-d', $start);
        }
        if (is_int($end)) {
            $end = date('Y-m-d', $end);
        }
        $start = $start . ' 00:00:00';
        $end = $end . ' 23:59:59';
        if ($isInt) {
            $start = strtotime($start);
            $end = strtotime($end);
        }
        return [$start, $end];
    }

    /**
     * 时间格式化
     *
     * @param int  $month 月份
     * @param bool $isInt 是否为int
     *
     * @return array
     * @author lengbin(lengbin0@gmail.com)
     */
    public static function formatMonth($month, $isInt = true)
    {
        if (strlen($month) < 3) {
            $month = date("Y-{$month}-d");
        }
        $timestamp = strtotime($month);
        $startTime = date('Y-m-1 00:00:00', $timestamp);
        $mdays = date('t', $timestamp);
        $endTime = date('Y-m-' . $mdays . ' 23:59:59', $timestamp);
        if ($isInt) {
            $startTime = strtotime($startTime);
            $endTime = strtotime($endTime);
        }
        return [$startTime, $endTime];
    }

    /**
     * 人性化时间显示
     *
     * @param int $time
     *
     * @return false|string
     */
    public static function formatTime($time)
    {
        $rtime = date("Y-m-d H:i:s", $time);
        $time = time() - $time;
        if ($time < 60) {
            $str = '刚刚';
        } elseif ($time < 60 * 60) {
            $min = floor($time / 60);
            $str = $min . '分钟前';
        } elseif ($time < 60 * 60 * 24) {
            $h = floor($time / (60 * 60));
            $str = $h . '小时前 ';
        } elseif ($time < 60 * 60 * 24 * 3) {
            $d = floor($time / (60 * 60 * 24));
            if ($d == 1) {
                $str = '昨天 ' . $rtime;
            } else {
                $str = '前天 ' . $rtime;
            }
        } else {
            $str = $rtime;
        }
        return $str;
    }
}
