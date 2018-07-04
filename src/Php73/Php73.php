<?php

namespace Symfony\Polyfill\Php73;

/**
 * @author Gabriel Caruso <carusogabriel34@gmail.com>
 * @author Ion Bazan <ion.bazan@gmail.com>
 *
 * @internal
 */
final class Php73
{
    const NANO_IN_SEC = 1000000000;
    const NANO_IN_MSEC = 1000;
    const MSEC_IN_SEC = 1000000.0;

    private static $startAt = null;

    private static $startAtArr = null;

    /**
     * @param bool $asNum
     *
     * @return array|float|int
     */
    public static function hrtime($asNum = false)
    {
        if (null === self::$startAtArr) {
            self::$startAtArr = self::microtime();
        }

        if ($asNum) {
            if (null === self::$startAt) {
                self::$startAt = self::$startAtArr[0] + self::$startAtArr[1];
                if (\PHP_INT_SIZE !== 4) {
                    // In this case $startAt is a int, number of micro seconds
                    self::$startAt = (int) (self::$startAt * self::MSEC_IN_SEC);
                }
            }

            if (\PHP_INT_SIZE === 4) {
                // Floor removes rounding errors from floating point
                return floor((microtime(true) - self::$startAt) * self::NANO_IN_SEC);
            }
            $nowNanos = (int) (microtime(true) * self::MSEC_IN_SEC);

            return ($nowNanos - self::$startAt) * self::NANO_IN_MSEC;
        }

        $time = self::microtime();

        $secs = $time[1] - self::$startAt[1];
        // $msecs is in seconds, but its the fractional part i.e. 0.x
        $msecs = $time[0] - self::$startAt[0];
        if ($msecs < 0) {
            $msecs += 1;
            $secs -= 1;
        }

        return array($secs, (int) ($msecs * self::NANO_IN_SEC));
    }

    private static function microtime()
    {
        $time = explode(' ', microtime());

        return array((float) $time[0], (int) $time[1]);
    }
}
