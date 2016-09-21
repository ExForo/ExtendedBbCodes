<?php

/**
 * Инициализация зависимостей приложения
 * @package     Esthetic_EBBC
 */
class Esthetic_EBBC_Listener_Dependencies {
    /**
     * Сортировка списка кодов, скрывающих информацию
     * @param       XenForo_Dependencies_Abstract   $dependencies
     * @param       array                           $data
     * @return  bool
     */
    public static function listen (XenForo_Dependencies_Abstract $dependencies, array $data) {
        
        if (XenForo_Application::isRegistered('bbCode')) {
            
            $bb_code_array = XenForo_Application::get('bbCode');
            
            if (!empty ($bb_code_array['bbCodes'])) {
                
                $bb_codes = $bb_code_array['bbCodes'];
                
                $result = array ( );
                
                $mask = Esthetic_EBBC_Service_Config::getSecureTags();
                if ($mask) {
                    foreach ($mask as $key => $value) {
                        
                        if (isset ($bb_codes[$value])) {
                            $result[$value] = $bb_codes[$value];
                        }
                    }
                }
                foreach ($bb_codes as $key => $value) {
                    if (!isset ($result[$key])) {
                        $result[$key] = $value;
                    }
                }
                
                $bb_code_array['bbCodes'] = $result;
                XenForo_Application::set('bbCode', $bb_code_array);
            }
        }
        
        return true;
    }
}