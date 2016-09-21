<?php

/**
 * Обработчик событий рендеринга страниц
 * @package     Esthetic_EBBC
 */
class Esthetic_EBBC_Listener_View {
    /**
     * Обработка вызова класса
     * @param   string  &$class
     * @param   array   &$extend
     * @return  bool
     */
    public static function listen ($class, array &$extend) {
        
        switch ($class) {
            case 'XenForo_ViewPublic_Thread_View':
                $extend[] = 'Esthetic_EBBC_ViewPublic_Thread_View';
                break;
                
            default:
        }
        
        return true;
    }
}