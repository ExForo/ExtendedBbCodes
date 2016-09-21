<?php

/**
 * Установки приложения
 * @package     Esthetic_EBBC
 */
abstract class Esthetic_EBBC_Service_Config {
    /**
     * Перечень кодов, скрывающих информацию
     * @return array
     */
    public static function getSecureTags ( ) {
        // Сортировка кодов на панели редактора производится в указанном порядке
        return array (
            'hide', 'posts', 'likes', 'trophies', 'days', 'users', 'userids', 'groups', 'club', 'shoppings', 'organized'
        );
    }
}