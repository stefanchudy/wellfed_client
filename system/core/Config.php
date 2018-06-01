<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace System;

/**
 * Description of Config
 *
 * @author martin
 */
class Config {

    private static $instance = NULL;
    private $configFolder = NULL;

    private $_data = array();
    private $_defaultResult = array();

    private function __construct() {
        
    }

    /*
     * @return /System/Config
     */

    public static function getInstance() {
        if (self::$instance === NULL) {
            self::$instance = new \System\Config();
        }
        return self::$instance;
    }

    public function setConfigFolder($folder) {
        $this->configFolder = $folder;
    }

    public function __get($name) {
        if(!isset($this->_data[$name])){
            $file = $this->configFolder . $name . '.php';
            $this->_data[$name] = $this->_defaultResult;
            if (file_exists($file) && is_file($file) && is_readable($file)) {
                $this->_data[$name] = include $file;
            }
        }

        return $this->_data[$name];
    }

    public function getConfig() {
        $result = Array();
        $dir = scandir($this->configFolder);        
        
        foreach ($dir as $value) {
            $file =  $this->configFolder.$value;
            if (file_exists($file) && is_file($file) && is_readable($file)) {                
                $result[str_replace('.php', '', $value)] = include $file;
            }
        }

        return $result;
    }

}
