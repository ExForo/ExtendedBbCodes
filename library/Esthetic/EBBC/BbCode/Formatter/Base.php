<?php

/**
 * Дополнение функций парсера BB-кодов
 * @package     Esthetic_EBBC
 */
class Esthetic_EBBC_BbCode_Formatter_Base extends XFCP_Esthetic_EBBC_BbCode_Formatter_Base {
    /**
     * @var XenForo_Model_User
     */
    protected $_user_model = false;

    /**
     * Предварительная загрузка необходимых шаблонов
     * @param XenForo_View $view
     */
    public function preLoadTemplates (XenForo_View $view) {
        $view->preLoadTemplate('estebbc_bb_code_tag_hide');
        $view->preLoadTemplate('estebbc_bb_code_tag_button');
        parent::preLoadTemplates($view);
    }

    /**
     * Рендер кодов скрытой информации
     * @param   array       $tag
     * @param   array       $renderer_states
     * @param   string      $type
     * @return string
     */
    public function renderTagsHidden (array $tag, array $renderer_states, $type = 'post') {
        
        $keys = array_keys ($tag['children']);
        if (!$keys) {
            return '';
        }
        
        $options = XenForo_Application::get('options');
        $visitor = XenForo_Visitor::getInstance();
        
        $first = reset ($keys);
        $last = end ($keys);

        if (is_string ($tag['children'][$first])) {
            $tag['children'][$first] = ltrim ($tag['children'][$first]);
        }
        
        if (is_string ($tag['children'][$last])) {
            $tag['children'][$last] = rtrim ($tag['children'][$last]);
        }

        $content = $this->renderSubTree($tag['children'], $renderer_states);
        if ($content === '') {
            return '';
        }
        
        $is_owner = false;
        $is_silent = false;
        
        /**
         * Определение прав и лимитов
         */
        $permissions = $this->_getUserHideCodesPermissions();
        
        /**
         * Определение "тихого" режима
         */
        if (!empty ($tag['option']) && preg_match ('/^silent\!\s*([^\n]*)$/', $tag['option'], $matches)) {
            $is_silent = true;
            $tag['option'] = $matches[1];
        }
        
        if (!$visitor['user_id']) {
            return $is_silent ? '' : $this->_renderTagHideMessage(
                new XenForo_Phrase ('estebbc_warning_registration_required')
            );
        }
        
        /**
         * Поиск маркеров дополнительной информации
         */
        if (preg_match ('/\{\!\.estebbc\:(\{[^\}]+\})\}/', $content, $matches)) {
            $info = @json_decode (htmlspecialchars_decode ($matches[1]), true);
            if (!empty ($info['user_id'])) {
                $is_owner = (int)$info['user_id'] == $visitor['user_id'];
            }
            
            $posts = array ( );
            if (XenForo_Application::isRegistered('estebbc_viewing_posts')) {
                $posts = XenForo_Application::get('estebbc_viewing_posts');
            }
            
            /**
             * Снятие ограничений в старых сообщениях
             */
            if (!empty ($options->estebbc_hide_work_limit) && !empty ($info['post_id']) && $type != 'users' && $type != 'userids') {
                
                $time = 0;
                if (preg_match ('/^\d{2}\-\d{2}\-\d{4}$/', $options->estebbc_hide_work_limit)) {
                    $time = strtotime ($options->estebbc_hide_work_limit);
                } else if (is_numeric ($options->estebbc_hide_work_limit)) {
                    $time = strtotime (date ('d-m-Y', XenForo_Application::$time)) - ((int)$options->estebbc_hide_work_limit) * 86400;
                }
                
                if (isset ($posts[(int)$info['post_id']]) && $time > 0) {
                
                    $compared_date = $options->estebbc_hide_edit_as_create && $posts[(int)$info['post_id']]['last_edit_date'] > $posts[(int)$info['post_id']]['post_date'] ? 
                        $posts[(int)$info['post_id']]['last_edit_date'] : $posts[(int)$info['post_id']]['post_date'];
                    
                    if ($compared_date < $time) {
                        return '<br />' . preg_replace ('/^\{\!\.estebbc\:\{[^\}]*\}\}/', '', $content);
                    }
                }
            }
        }
        
        $message = new XenForo_Phrase ('estebbc_warning_hidden_content');
        
        $can_view_all = $visitor->hasPermission('estebbc', 'estebbc_hide_can_view_all');
        
        if ($type == 'shopping' && preg_match ('/^(organized)\:(\d+)$/i', $tag['option'], $matches)) {
            $type = strtolower ($matches[1]);
            $tag['option'] = $matches[2];
        }
        
        switch ($type) {
            
            /**
             * Вывод информации по условию принадлежности к указанным группам
             */
            case 'group':
                if (empty ($tag['option'])) {
                    return $content;
                }
                
                $groups_available = XenForo_Model::create('XenForo_Model_UserGroup')->getAllUserGroupTitles();
                $groups_declared = explode (',', $tag['option']);
                
                $groups_valid = array ( );
                $can_view = false;
                foreach ($groups_declared as $group_id) {
                    $group_id = trim ($group_id);
                    
                    if (!is_numeric ($group_id)) {
                        continue;
                    }
                    
                    if (!empty ($groups_available[intval ($group_id)])) {
                        $groups_valid[intval ($group_id)] = $groups_available[intval ($group_id)];
                        
                        if ($visitor->isMemberOf(intval ($group_id))) {
                            $can_view = true;
                        }
                    }
                }
                
                if (empty ($groups_valid)) {
                    if (!$can_view_all) {
                        return $is_silent ? '' : $this->_renderTagHideMessage($message);
                    }
                } else {
                    $message = new XenForo_Phrase ('estebbc_warning_hidden_content_for_groups_x', array ('x' => implode (', ', $groups_valid)));
                    if (!$can_view && !$can_view_all && !$is_owner) {
                        return $is_silent ? '' : $this->_renderTagHideMessage($message);
                    }
                }
                
                break;
            
            /**
             * Вывод информации по условию членства в клубе
             */
            case 'club':
                $message = new XenForo_Phrase ('estebbc_warning_club_hidden_content');
                $groups_available = $options->estebbc_club_groups;
                
                if ((!is_array ($groups_available) || empty ($groups_available)) && !$can_view_all) {
                    return $is_silent ? '' : $this->_renderTagHideMessage($message);
                }
                
                $can_view = false;
                foreach ($groups_available as $group_id) {
                    if ($visitor->isMemberOf($group_id)) {
                        $can_view = true;
                        break;
                    }
                }
                
                if (!$can_view && !$can_view_all && !$is_owner) {
                    return $is_silent ? '' : $this->_renderTagHideMessage($message);
                }
                
                break;
            
            /**
             * Вывод информации по условию наличия необходимого количества сообщений
             */
            case 'hide':
                if (preg_match ('/^\(.*\)$/', $tag['option'])) {
                    
                    $mask = strtolower ($tag['option']);
                    
                    $mask = str_replace (
                        array ('posts', 'likes', 'trophies', 'days'),
                        array ($permissions['posts'], $permissions['likes'], $permissions['trophies'], $permissions['days']),
                        $mask
                    );
                    
                    $shopping_info = Esthetic_EBBC_Crossover_CS::getUserShoppingInfo();
                    if ($shopping_info) {
                        
                        $mask = preg_replace ('/shoppings\s*([\>\=\<]{1,2})\s*organized\:(\d+)/', $shopping_info['organizer_shoppings_total'] . '$1$2', $mask);
                        
                        $mask = str_replace (
                            array ('shoppings', 'organized'),
                            array ($permissions['shoppings_paid'], $permissions['shoppings_organized']),
                            $mask
                        );
                    }
                    
                    $mask = str_replace (
                        array ('and', 'or', ' '),
                        array ('&', '|', ''),
                        $mask
                    );
                    
                    /**
                     * Выполнение условий неограниченного доступа по данным установок прав
                     */
                    $mask = preg_replace ('/\-1[\>\=\<]{1,2}\d+/', '1', $mask);
                    
                    $mask = preg_replace_callback ('/(groups|userids)(\!?\=)((\d+\,)+\d+|\d+)/', array ($this, '_compareSetExpression'), $mask);
                    
                    $message = new XenForo_Phrase ('estebbc_warning_invalid_rule');
                    
                    if (preg_match ('/^[\(\)\d\&\|\>\<\=]+$/', $mask)) {
                        
                        $mask = preg_replace_callback ('/(\d+)([\>\=\<]{1,2})(\d+)/', array ($this, '_compareMathExpression'), $mask);
                        
                        $runs = 0;
                        do {
                            
                            $mask = str_replace (
                                array ('0&0', '0&1', '1&0', '1&1', '0|0', '0|1', '1|0', '1|1'),
                                array ('0', '0', '0', '1', '0', '1', '1', '1'),
                                $mask
                            );
                            
                            $mask = str_replace (
                                array ('(0)', '(1)'),
                                array ('0', '1'),
                                $mask
                            );
                            
                            if (strlen ($mask) == 1) {
                                break;
                            }
                            
                            $runs++;
                            if ($runs > 100) {
                                $mask = '?';
                                break;
                            }
                        } while (true);
                        
                        if ($mask == '?') {
                            break;
                        } else if ($mask == '0' && !$can_view_all && !$is_owner) {
                            $message = new XenForo_Phrase ('estebbc_warning_hidden_content_extended_denied');
                            return $is_silent ? '' : $this->_renderTagHideMessage($message);
                        }
                        
                    } else break;
                    
                    $message = new XenForo_Phrase ('estebbc_warning_hidden_content_extended_accepted');
                }
            case 'post':
                if (!empty ($tag['option']) && is_numeric ($tag['option'])) {
                    $message_count = intval ($tag['option']);
                } else {
                    $message_count = 0;
                }
                
                if ($message_count > 0) {
                    $message = new XenForo_Phrase ('estebbc_warning_x_messages_required', array ('x' => $message_count));
                    if (!$can_view_all && !$is_owner && $permissions['posts'] < $message_count) {
                        return $is_silent ? '' : $this->_renderTagHideMessage($message);
                    }
                }
                break;
            
            /**
             * Вывод информации по условию наличия необходимого количества симпатий
             */
            case 'like':
                
                if (!empty ($tag['option']) && is_numeric ($tag['option'])) {
                    $like_count = intval ($tag['option']);
                } else {
                    $like_count = 0;
                }
                
                if ($like_count <= 0) {
                    return $content;
                }
                
                $message = new XenForo_Phrase ('estebbc_warning_x_likes_required', array ('x' => $like_count));
                if (!$can_view_all && !$is_owner && $permissions['likes'] < $like_count) {
                    return $is_silent ? '' : $this->_renderTagHideMessage($message);
                }
                
                break;
                
            /**
             * Вывод информации по условию наличия необходимого количества баллов за трофеи
             */
            case 'trophy':
                
                if (!empty ($tag['option']) && is_numeric ($tag['option'])) {
                    $trophy_count = intval ($tag['option']);
                } else {
                    $trophy_count = 0;
                }
                
                if ($trophy_count <= 0) {
                    return $content;
                }
                
                $message = new XenForo_Phrase ('estebbc_warning_x_trophies_required', array ('x' => $trophy_count));
                if (!$can_view_all && !$is_owner && $permissions['trophies'] < $trophy_count) {
                    return $is_silent ? '' : $this->_renderTagHideMessage($message);
                }
                
                break;

            /**
             * Вывод информации по условию количества дней с момента регистрации
             */
            case 'day':
            
                if (!empty ($tag['option']) && is_numeric ($tag['option'])) {
                    $days_count = intval ($tag['option']);
                } else {
                    $days_count = 0;
                }
                
                if ($days_count <= 0) {
                    return $content;
                }
                
                $message = new XenForo_Phrase ('estebbc_warning_x_days_required', array ('x' => $days_count));
                if (!$can_view_all && !$is_owner && $permissions['days'] < $days_count) {
                    return $is_silent ? '' : $this->_renderTagHideMessage($message);
                }
                
                break;
                
            /**
             * Вывод информации для указанных пользователей
             */
            case 'user':
                
                $users = array ( );
                if (!empty ($tag['option'])) {
                    $users = explode (',', $tag['option']);
                }
                
                if (empty ($users)) {
                    return $content;
                }
                
                /**
                 * Выборка пользователей
                 */
                $_users = $this->_getUserModel()->getUsersByNames(
                    $users,
                    array (
                        'join' => XenForo_Model_User::FETCH_USER_PRIVACY + XenForo_Model_User::FETCH_USER_OPTION,
                        'followingUserId' => (int)$visitor['user_id']
                    ),
                    $not_found
                );
                
                $can_view = false;
                
                if (!empty ($_users)) {
                    
                    $user_links = array ( );
                    foreach ($_users as $user) {
                        $user_links[] = sprintf ('<a class="username" href="%s">%s</a>', XenForo_Link::buildPublicLink('full:members', $user), $user['username']);
                        
                        if ($user['user_id'] == $visitor['user_id']) {
                            $can_view = true;
                        }
                    }
                    
                    $message = htmlspecialchars_decode (new XenForo_Phrase ('estebbc_warning_users_hidden_content', array ('users' => implode (', ', $user_links))));
                }
                
                if (!$can_view && !$can_view_all && !$is_owner) {
                    return $is_silent ? '' : $this->_renderTagHideMessage($message);
                }
                
                break;
                
            /**
             * Вывод информации для пользователей, с указанными идентификаторами
             */
            case 'userid':
                
                $check = $this->_userIdOneOf($tag['option']);

                if ($check === null) {
                    return $content;
                }
                
                $user_ids_str = str_replace (',', ', ', str_replace (' ', '', $tag['option']));
                
                $message = new XenForo_Phrase ('estebbc_warning_hidden_content_for_userids_x', array ('x' => $user_ids_str));
                if (!$check && !$can_view_all && !$is_owner) {
                    return $is_silent ? '' : $this->_renderTagHideMessage($message);
                }
                break;
                
            /* ************************* Интеграция с Esthetic Collaborative Shopping ************************* */
            
            /**
             * Информация для пользователей, оплативших указанное число покупок
             */
            case 'shopping':
                
                $shopping_info = Esthetic_EBBC_Crossover_CS::getUserShoppingInfo();
                
                if (!empty ($tag['option']) && is_numeric ($tag['option'])) {
                    $shoppings_count = intval ($tag['option']);
                } else {
                    $shoppings_count = 0;
                }
                
                if ($shoppings_count <= 0 || !$shopping_info) {
                    /**
                     * Скрытие информации при обнаружении ошибки. На тот случай, когда аддон СП отключен(предотвращает утечки информации в публичный доступ).
                     */
                    return $is_silent ? '' : $this->_renderTagHideMessage($message);
                }
                
                $message = new XenForo_Phrase ('estebbc_warning_x_shoppings_required', array ('x' => $shoppings_count));
                if ($permissions['shoppings_paid'] < $shoppings_count && !$can_view_all && !$is_owner) {
                    return $is_silent ? '' : $this->_renderTagHideMessage($message);
                }
                
                break;
                
            /**
             * Информация для пользователей, оплативших указанное число покупок
             */
            case 'organized':
                
                $shopping_info = Esthetic_EBBC_Crossover_CS::getUserShoppingInfo();
                
                if (!empty ($tag['option']) && is_numeric ($tag['option'])) {
                    $shoppings_count = intval ($tag['option']);
                } else {
                    $shoppings_count = 0;
                }
                
                if ($shoppings_count <= 0 || !$shopping_info) {
                    /**
                     * Скрытие информации при обнаружении ошибки. На тот случай, когда аддон СП отключен(предотвращает утечки информации в публичный доступ).
                     */
                    return $is_silent ? '' : $this->_renderTagHideMessage($message);
                }
                
                $message = new XenForo_Phrase ('estebbc_warning_x_organized_shoppings_required', array ('x' => $shoppings_count));
                if ($permissions['shoppings_organized'] < $shoppings_count && !$can_view_all && !$is_owner) {
                    return $is_silent ? '' : $this->_renderTagHideMessage($message);
                }
                
                break;
            
            default:
        }
        
        /**
         * Очистка маркеров
         */
        $content = preg_replace ('/^\{\!\.estebbc\:\{[^\}]*\}\}/', '', $content);
        
        if ($this->_view && $is_silent) {
            return $content;
        } else if ($this->_view) {
            $template = $this->_view->createTemplateObject('estebbc_bb_code_tag_hide', array (
                'content' => $content,
                'message' => $message
            ));
            return $template->render();
        } else {
            return $this->_renderTagHideMessage(false);
        }
    }
    
    /**
     * Обновление значений критериев доступа исходя из прав групп
     * @return array
     */
    protected function _getUserHideCodesPermissions ( ) {
        
        $visitor = XenForo_Visitor::getInstance();
        
        $shopping_info = Esthetic_EBBC_Crossover_CS::getUserShoppingInfo();
        
        $permissions = array (
            'posts'     => $visitor['message_count'], 
            'likes'     => $visitor['like_count'], 
            'trophies'  => $visitor['trophy_points'], 
            'days'      => intval ((XenForo_Application::$time - $visitor['register_date']) / 86400),
            
            'shoppings_paid'        => $shopping_info['participant_shoppings_total'],
            'shoppings_organized'   => $shopping_info['organizer_shoppings_total']
        );
        
        $posts = (int)$visitor->hasPermission('estebbc', 'estebbc_limit_posts');
        if ($permissions['posts'] < $posts || $posts < 0) {
            $permissions['posts'] = $posts;
        }
        
        $likes = (int)$visitor->hasPermission('estebbc', 'estebbc_limit_likes');
        if ($permissions['likes'] < $posts || $likes < 0) {
            $permissions['likes'] = $likes;
        }
        
        $trophies = (int)$visitor->hasPermission('estebbc', 'estebbc_limit_trophies');
        if ($permissions['trophies'] < $trophies || $trophies < 0) {
            $permissions['trophies'] = $trophies;
        }
        
        $days = (int)$visitor->hasPermission('estebbc', 'estebbc_limit_days');
        if ($permissions['days'] < $days || $days < 0) {
            $permissions['days'] = $days;
        }
        
        /* EstCS */
        $shoppings = (int)$visitor->hasPermission('estebbc', 'estebbc_limit_shoppings');
        if ($permissions['shoppings_paid'] < $shoppings || $shoppings < 0) {
            $permissions['shoppings_paid'] = $shoppings;
        }
        
        $shoppings = (int)$visitor->hasPermission('estebbc', 'estebbc_limit_organized');
        if ($permissions['shoppings_organized'] < $shoppings || $shoppings < 0) {
            $permissions['shoppings_organized'] = $shoppings;
        }
        
        return $permissions;
    }
    
    /**
     * Проверка принадлежности идентификатора пользователя к указанной в строке последовательности
     * @param string    $tag_option
     * @return bool|null
     */
    protected function _userIdOneOf ($tag_option) {
        
        $visitor = XenForo_Visitor::getInstance();
        
        if (empty ($tag_option)) {
            return null;
        }
        
        $tag_option = str_replace (' ', '', $tag_option);
        
        $user_ids = array ( );
        if (preg_match ('/^(\d+\,)+\d+$/', $tag_option)) {
            $user_ids = explode (',', $tag_option);
        } else if (is_numeric ($tag_option)) {
            $user_ids[] = (int)$tag_option;
        } else {
            return null;
        }
        
        if (in_array ($visitor['user_id'], $user_ids)) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Рендер кода visitor
     * @param   array       $tag
     * @param   array       $renderer_states
     * @return string
     */
    public function renderTagVisitor (array $tag, array $renderer_states) {
        
        $visitor = XenForo_Visitor::getInstance();
        if (!empty ($visitor['user_id'])) {
            return $visitor['username'];
        }
        
        return new XenForo_Phrase ('estebbc_visitor');
    }
    
    /**
     * Рендер кода button
     * @param   array       $tag
     * @param   array       $renderer_states
     * @return string
     */
    public function renderTagButton (array $tag, array $renderer_states) {
        
        $url = $tag['option'];
        $text = $this->renderSubTree($tag['children'], $renderer_states);
        
        if (empty ($text)) {
            $text = new XenForo_Phrase ('estebbc_link');
        }
        
        if (empty ($url)) {
            return $text;
        }
        
        if ($this->_view) {
            $template = $this->_view->createTemplateObject('estebbc_bb_code_tag_button', array (
                'url'   => $url,
                'text'  => $text
            ));
            return $template->render();
        } else {
            return sprintf ('<a href="%s" target="_blank">%s</a>', $url, $text);
        }
    }
    
    
    /**
     * Отображение текста предупреждений скрытого контента
     * @param   XenForo_Phrase      $message
     * @return  string
     */
    protected function _renderTagHideMessage ($message = false) {
        
        if (empty ($message)) {
            $message = new XenForo_Phrase ('estebbc_warning_hidden_content');
        }
        
        if ($this->_view) {
            $template = $this->_view->createTemplateObject('estebbc_bb_code_tag_hide', array (
                'content' => $message,
            ));
            return $template->render();
        }
        
        return '<div><b>' . $message . '</b></div>';
    }
    
    
    /**
     * Сравнение множеств
     * @param   array   $matches
     * @return string
     */
    protected function _compareSetExpression ($matches) {
        
        $visitor = XenForo_Visitor::getInstance();
        
        $type = $matches[1];
        $op = $matches[2];
        $set = $matches[3];
        
        if ($op != '=' && $op != '!=') {
            return '?';
        }
        
        $check = false;
        switch ($type) {
        
            case 'groups':
                $set = explode (',', $set);
                
                if (empty ($set)) {
                    return '?';
                }
                
                foreach ($set as $group_id) {
                    if ($visitor->isMemberOf(intval ($group_id))) {
                        $check = true;
                        break;
                    }
                }
                
                break;
            
            case 'userids':
                $check = $this->_userIdOneOf($set);
                
                if ($check === null) {
                    return '?';
                }

                break;
                
            default:
                return '?';
        }
        
        if ($op == '=' && !$check) {
            return '0';
        } else if ($op == '!=' && $check) {
            return '0';
        }
        
        return '1';
    }
    
    
    /**
     * Математическое сравнение чисел
     * @param   array   $matches
     * @return string
     */
    protected function _compareMathExpression ($matches) {
        
        $a = (int)$matches[1];
        $b = (int)$matches[3];
        $op = $matches[2];
        
        if ($op != '<' && $op != '<=' && $op != '=' && $op != '>=' && $op != '>') {
            return '?';
        }
        
        switch ($op) {
            case '<':
                return $a < $b ? '1' : '0';
            case '<=':
                return $a <= $b ? '1' : '0';
            case '=':
                return $a == $b ? '1' : '0';
            case '>=':
                return $a >= $b ? '1' : '0';
            case '>':
                return $a > $b ? '1' : '0';
            default:
                return '?';
        }
    }

	/**
	 * @return XenForo_Model_User
	 */
    protected function _getUserModel ( ) {
    
        if (!$this->_user_model) {
            $this->_user_model = XenForo_Model::create('XenForo_Model_User');
        }
        return $this->_user_model;
    }
}