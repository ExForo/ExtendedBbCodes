<?php

/**
 * Класс интеграции с дополнением Esthetic Collaborative Shopping
 * @package     Esthetic_EBBC
 */
class Esthetic_EBBC_Crossover_CS {
    /**
     * Получение данных о СП пользователя
     * @return  array|false
     */
    public static function getUserShoppingInfo ( ) {
        
        if (XenForo_Application::isRegistered('estebbc_shopping_info')) {
            return XenForo_Application::get('estebbc_shopping_info');
        }
        
        XenForo_Application::set('estebbc_shopping_info', false);
        
        $options = XenForo_Application::get('options');
        
        if (!$cs_addon = XenForo_Model::create('XenForo_Model_AddOn')->getAddOnById('estcs')) {
            return false;
        }
        
        if (!$cs_addon['active']) {
            return false;
        }
        
        if (!$user_ratings = XenForo_Model::create('Esthetic_CS_Model_Shopping')->getUserRatings(XenForo_Visitor::getUserId())) {
            return false;
        }
        
        XenForo_Application::set('estebbc_shopping_info', $user_ratings);
        
        return $user_ratings;
    }
}