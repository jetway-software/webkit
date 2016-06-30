<?php

namespace common\helpers;

class ArrayHelper extends \yii\helpers\ArrayHelper
{
    /**
     * @param $needle
     * @param array $haystack
     * @param null $strict
     * @return boolean
     */
    public static function inArray($needle, array $haystack, $strict = null) : bool
    {
        if (is_array($needle)) {
            foreach ($needle as $key) {
                if (in_array($key, $haystack, $strict)) {
                    return true;
                }
            }
        } elseif (is_string($needle)) {
            return in_array($needle, $haystack, $strict);
        }

        return false;
    }
}