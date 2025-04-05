<?php

namespace text;

class TextTasks
{
    private static $dir = "text";

    public static function writeText($fileName, $content) {
        $filePath = self::$dir . DIRECTORY_SEPARATOR . $fileName;
        file_put_contents($filePath, $content . PHP_EOL, FILE_APPEND);
    }

    public static function readText($fileName) {
        $filePath = self::$dir . DIRECTORY_SEPARATOR . $fileName;
        return file_exists($filePath) ? file_get_contents($filePath) : "Файл не знайдено";
    }

    public static function clearText($fileName) {
        $filePath = self::$dir . DIRECTORY_SEPARATOR . $fileName;
        if (file_exists($filePath)) {
            file_put_contents($filePath, "");
        }
    }

}