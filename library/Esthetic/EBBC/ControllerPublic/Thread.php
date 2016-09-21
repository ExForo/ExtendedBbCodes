<?php

/**
 * Контроллер Thread
 * @package     Esthetic_EBBC
 */
class Esthetic_EBBC_ControllerPublic_Thread extends XFCP_Esthetic_EBBC_ControllerPublic_Thread {
    /**
     * Дополнение функционала действия Index
     * @return  XenForo_ControllerResponse_View
     */
    public function actionIndex ( ) {

        $response   = parent::actionIndex();
        $params     = &$response->params;
        
        if (!empty ($params['firstPost'])) {
            $secure_tags = implode ('|', Esthetic_EBBC_Service_Config::getSecureTags());
            $params['firstPost']['message'] = preg_replace ('#\[(' . $secure_tags . ')[^\]]*\].*\[/\\1\]#siU', '', $params['firstPost']['message']);
        }
        
        return $response;
    }
}