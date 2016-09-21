<?php

/**
 * Расширение модели Tag
 * @package     Esthetic_EBBC
 */
class Esthetic_EBBC_Model_Tag extends XFCP_Esthetic_EBBC_Model_Tag {
    /**
     * Подготовка результатов списка тегов
     * @param array $results
     * @param array|null $viewing_user
     * @return array
     */
    public function getTagResultsForDisplay (array $results, array $viewing_user = null) {
        
        $results = parent::getTagResultsForDisplay ($results, $viewing_user);

        if (empty ($results['results'])) {
            return $results;
        }
        
        foreach ($results['results'] as &$item) {
            
            if (empty ($item['content'])) {
                continue;
            }
            if (empty ($item['content']['message'])) {
                continue;
            }
            
            $this->_escapeMessageSecureTags($item['content']['message']);
        }
        
        return $results;
    }
    
    /**
     * Удаление скрытого контента из текста фида
     * @param   string  &$string
     * @return  null
     */
    protected function _escapeMessageSecureTags (&$string) {
        $secure_tags = implode ('|', Esthetic_EBBC_Service_Config::getSecureTags());
        $string = preg_replace ('#\[(' . $secure_tags . ')[^\]]*\].*\[/\\1\]#siU', sprintf ('[%s]', new XenForo_Phrase ('estebbc_hidden_content')), $string);
    }
}