<?php

namespace app\components;

class HtmlTools
{
    /**
     * @param string $date
     * @return string
     */
    public static function formatDate($date)
    {
        $y = explode(" ", $date);
        $x = explode("-", $y[0]);
        if (count($x) != 3) {
            return $date;
        }
        return $x[2] . "." . $x[1] . "." . $x[0];
    }
}