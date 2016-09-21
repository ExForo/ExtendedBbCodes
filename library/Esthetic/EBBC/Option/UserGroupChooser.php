<?php

/**
 * Обработка настроек выбора групп
 * @package     Esthetic_EBBC
 */
abstract class Esthetic_EBBC_Option_UserGroupChooser {
    /**
     * Рендер списка групп для чекбоксов
     * @param XenForo_View $view 
     * @param string $field_prefix
     * @param array $prepared_option
     * @param boolean $can_edit
     * @return XenForo_Template_Abstract
     */
    public static function renderCheckbox (XenForo_View $view, $field_prefix, array $prepared_option, $can_edit) {
    
        $userGroupModel = XenForo_Model::create('XenForo_Model_UserGroup');
        $prepared_option['formatParams'] = $userGroupModel->getUserGroupOptions(
            $prepared_option['option_value']
        );

        return XenForo_ViewAdmin_Helper_Option::renderOptionTemplateInternal(
            'option_list_option_checkbox', $view, $field_prefix, $prepared_option, $can_edit
        );
    }
}