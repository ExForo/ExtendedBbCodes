<?php

/**
 * Рендер шаблона "Esthetic_EBBC_ViewPublic_Thread_View"
 * @package     Esthetic_EBBC
 */
class Esthetic_EBBC_ViewPublic_Thread_View extends XFCP_Esthetic_EBBC_ViewPublic_Thread_View {
    /**
     * Обработка параметров рендера
     * @return  NULL
     */
	public function renderHtml ( ) {
        
        $posts = array ( );
        
        if (!empty ($this->_params['posts'])) {
            
            $secure_tags = implode ('|', Esthetic_EBBC_Service_Config::getSecureTags());
            
            foreach ($this->_params['posts'] as &$post) {
                
                $code = sprintf ('$0{!.estebbc:{"post_id":%d,"user_id":%d}}', $post['post_id'], $post['user_id']);
                
                if (preg_match ('#\[(' . $secure_tags . ')[^\]]*\]#siU', $post['message'])) {
                    $post['message'] = preg_replace ('#\[(' . $secure_tags . ')[^\]]*\]#siU', $code, $post['message']);
                    $posts[(int)$post['post_id']] = array (
                        'post_id' => $post['post_id'],
                        'user_id' => $post['user_id'],
                        'post_date' => $post['post_date'],
                        'last_edit_date' => $post['last_edit_date']
                    );
                }
            }
        }
        
        XenForo_Application::set('estebbc_viewing_posts', $posts);
        
        $response = parent::renderHtml();
        
        if (!empty ($this->_params['posts'])) {

            foreach ($this->_params['posts'] as &$post) {
                
                if (!empty ($post['messageHtml'])) {
                    $post['messageHtml'] = preg_replace ('/\{\!\.estebbc\:\{[^\}]*\}\}/', '', $post['messageHtml']);
                }
                
                if (!empty ($post['message'])) {
                    $post['message'] = preg_replace ('/\{\!\.estebbc\:\{[^\}]*\}\}/', '', $post['message']);
                }
                
            }
        }
        
        XenForo_Application::set('estebbc_viewing_posts', false);
        
        return $response;
    }
}