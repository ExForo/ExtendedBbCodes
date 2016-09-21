<?php

/**
 * Обработчик событий вызова моделей
 * @package     Esthetic_EBBC
 */
class Esthetic_EBBC_Listener_Model {
    /**
     * Обработка вызова класса
     * @param   string  &$class
     * @param   array   &$extend
     * @return  bool
     */
    public static function listen ($class, array &$extend) {
        
        switch ($class) {
            case 'XenForo_Model_NewsFeed':
                $extend[] = 'Esthetic_EBBC_Model_NewsFeed';
                break;
                
            case 'XenForo_Model_Search':
                $extend[] = 'Esthetic_EBBC_Model_Search';
                break;
                
            case 'XenForo_Model_Post':
                $extend[] = 'Esthetic_EBBC_Model_Post';
                break;

            case 'XenForo_Model_Tag':
                $extend[] = 'Esthetic_EBBC_Model_Tag';
                break;
                
            default:
        }
        
        return true;
    }
}