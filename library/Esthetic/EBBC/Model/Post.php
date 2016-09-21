<?php

/**
 * Расширение модели Post
 * @package     Esthetic_EBBC
 */
class Esthetic_EBBC_Model_Post extends XFCP_Esthetic_EBBC_Model_Post {
    /**
     * Получение текста цитирования
     * @param array $post
     * @param integer $max_quote_depth
     * @return string
     */
    public function getQuoteTextForPost (array $post, $max_quote_depth = 0) {
        
        if (!empty ($post['message'])) {
            $this->_escapeMessageSecureTags($post['message']);
        }
        return parent::getQuoteTextForPost($post, $max_quote_depth);
    }

    /**
     * Удаление скрытого контента из текста фида
     * @param   string  &$string
     * @return  null
     */
    protected function _escapeMessageSecureTags (&$string) {
        $secure_tags = implode ('|', Esthetic_EBBC_Service_Config::getSecureTags());
        $string = preg_replace ('#\[(' . $secure_tags . ')[^\]]*\].*\[/\\1\]#siU', '', $string);
    }
}