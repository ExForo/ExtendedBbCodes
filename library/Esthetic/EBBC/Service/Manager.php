<?php

/**
 * Менеджер установки/удаления
 * @package     Esthetic_EBBC
 */
class Esthetic_EBBC_Service_Manager {
    /**
     * Объект менеджера установки/удаления продукта
     * @var Esthetic_CS_Service_Manager
     */
    private static $_instance;

    /**
     * Получение тела объекта
     * @return Esthetic_CS_Service_Manager
     */
    public static final function getInstance ( ) {
        if (!self::$_instance) {
            self::$_instance = new self ( );
        }
        return self::$_instance;
    }

    /**
     * Инсталятор приложения
     * @return true
     */
    public static function install ($existing_addon, $addon_data) {
        
        $from_version = 1;
        if ($existing_addon) {
            $from_version = $existing_addon['version_id'] + 1;
        }
        
        $class = self::getInstance();
        for ($i = $from_version; $i <= $addon_data['version_id']; $i++) {
            $method = '_v_' . $i;
            if (false === method_exists($class, $method)) {
                continue;
            }

            $class->$method();
        }
        
        self::registerApplication();
        
        return true;
    }

    /**
     * Регистрация приложения
     * @return  null
     */
    public static function registerApplication ( ) { }

    /**
     * v.1.0.0
     */
    protected function _v_1000 ( ) {
    
		$db = XenForo_Application::get('db');
        
        return true;
    }

    /**
     * Удаление элементов приложения
     */
    public static function uninstall ( ) {
    
        $db = XenForo_Application::get('db');
        
        return true;
    }
}