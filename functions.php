<?php
function debug($arr, $die = false)
{
    echo "<pre>" . print_r($arr, true) . "</pre>";
    if ($die) {
        die("смертельный дебугер!");
    }
}

