<?php

if (!function_exists('findIndex')) {
    function findIndex($array, $key, $value) {
        foreach ($array as $index => $item) {
            if (isset($item[$key]) && $item[$key] === $value) {
                return $index;
            }
        }
        return null;
    }
}