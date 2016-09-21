<?php

/**
 * Расширение модели NewsFeed
 * @package     Esthetic_EBBC
 */
class Esthetic_EBBC_Model_NewsFeed extends XFCP_Esthetic_EBBC_Model_NewsFeed {
    /**
     * Наполнение данными ленты новостей
     * @param array $news_feed
     * @param array $viewing_user
     * @return array
     */
    public function fillOutNewsFeedItems (array $news_feed, array $viewing_user) {
        
        $news_feed = parent::fillOutNewsFeedItems ($news_feed, $viewing_user);
        
        if (!is_array ($news_feed) || empty ($news_feed)) {
            return $news_feed;
        }
        
        foreach ($news_feed as &$item) {
            
            if (empty ($item['content'])) {
                continue;
            }
            if (empty ($item['content']['message'])) {
                continue;
            }
            
            $this->_escapeMessageSecureTags($item['content']['message']);
        }
        
        return $news_feed;
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