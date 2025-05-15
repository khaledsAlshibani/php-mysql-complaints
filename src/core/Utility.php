<?php

class Utility
{
    public static function escape($value)
    {
        if ($value === null || $value === false) {
            return '';
        }
        
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }

    public static function escapeArray($array)
    {
        if (!is_array($array)) {
            return self::escape($array);
        }
        
        return array_map(function($value) {
            return is_array($value) ? self::escapeArray($value) : self::escape($value);
        }, $array);
    }
}
