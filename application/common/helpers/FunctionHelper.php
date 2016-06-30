<?php

namespace common\helpers;


class FunctionHelper
{
    /**
     * @param $value
     * @param null $callable
     * @return bool|mixed
     */
    public static function isEmpty($value, $callable = null)
    {
        if ($callable !== null) {
            return call_user_func($callable, $value);
        } else {
            return $value === null || $value === [] || $value === '';
        }
    }
}