<?php

/**
 * Обработчик событий вызова контроллеров
 * @package     Esthetic_EBBC
 */
class Esthetic_EBBC_Listener_Controller {
    /**
     * Обработка вызова класса
     * @param   string  &$class
     * @param   array   &$extend
     * @return  bool
     */
    public static function listen ($class, array &$extend) {
        
        switch ($class) {
            case 'XenForo_ControllerPublic_Thread':
                $extend[] = 'Esthetic_EBBC_ControllerPublic_Thread';
                break;

            case 'XenForo_ControllerPublic_Tag':
                $extend[] = 'Esthetic_EBBC_ControllerPublic_Tag';
                break;

            default:
        }
        
        return true;
    }
}