<?php
namespace Bin\System;


class Config
{
    private static $instance = NULL;

    /**
     * @return Config|null
     */
    public static function getInstance()
    {
        if (self::$instance == NULL) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private static $loadConfigFile;

    /**
     * @return mixed
     */
    public static function getLoadConfigFile()
    {
        return self::$loadConfigFile;
    }

    /**
     * @param mixed $loadConfigFile
     */
    public static function setLoadConfigFile($loadConfigFile)
    {
        $incFile = $_SERVER["DOCUMENT_ROOT"]."/databaseclass/Config/";
        $loadConfigFile=include($incFile.$loadConfigFile.".php");
        self::$loadConfigFile = $loadConfigFile;
    }
}