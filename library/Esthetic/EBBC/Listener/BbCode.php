<?php

/**
 * Обработчик событий вызова рендера BB-кодов
 * @package     Esthetic_EBBC
 */
class Esthetic_EBBC_Listener_BbCode {
    /**
     * Обработка вызова класса
     * @param   string  &$class
     * @param   array   &$extend
     * @return  bool
     */
    public static function listen ($class, array &$extend) {
        
        switch ($class) {
            case 'XenForo_BbCode_Formatter_Base':
                $extend[] = 'Esthetic_EBBC_BbCode_Formatter_Base';
                break;
                
            default:
        }
        
        return true;
    }
}