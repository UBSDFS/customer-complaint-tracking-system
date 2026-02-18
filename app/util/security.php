<?php

class Security {
    public static function checkHTTPS() {
        if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
            echo "<h1>HTTPS is Required</h1>";
            exit();
        }
    }
}