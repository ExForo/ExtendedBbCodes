<?php

/**
 * Расширенные BB-коды
 * @package     Esthetic_EBBC
 */
class Esthetic_EBBC_BbCode_Formatter_Extended {
    /**
     * Рендер кода hide
     * @param   array       $tag
     * @param   array       $renderer_states
     * @param   XenForo_BbCode_Formatter_Base $formatter
     * @return string
     */
    public static function renderTagHide (array $tag, array $renderer_states, XenForo_BbCode_Formatter_Base $formatter) {
        if (get_class ($formatter) == 'Esthetic_EBBC_BbCode_Formatter_Base') {
            return $formatter->renderTagsHidden ($tag, $renderer_states, 'hide');
        }
        return '';
    }

    /**
     * Рендер кода club
     * @param   array       $tag
     * @param   array       $renderer_states
     * @param   XenForo_BbCode_Formatter_Base $formatter
     * @return string
     */
    public static function renderTagClub (array $tag, array $renderer_states, XenForo_BbCode_Formatter_Base $formatter) {
        if (get_class ($formatter) == 'Esthetic_EBBC_BbCode_Formatter_Base') {
            return $formatter->renderTagsHidden ($tag, $renderer_states, 'club');
        }
        return '';
    }

    /**
     * Рендер кода groups
     * @param   array       $tag
     * @param   array       $renderer_states
     * @param   XenForo_BbCode_Formatter_Base $formatter
     * @return string
     */
    public static function renderTagShowToGroups (array $tag, array $renderer_states, XenForo_BbCode_Formatter_Base $formatter) {
        if (get_class ($formatter) == 'Esthetic_EBBC_BbCode_Formatter_Base') {
            return $formatter->renderTagsHidden ($tag, $renderer_states, 'group');
        }
        return '';
    }

    /**
     * Рендер кода likes
     * @param   array       $tag
     * @param   array       $renderer_states
     * @param   XenForo_BbCode_Formatter_Base $formatter
     * @return string
     */
    public static function renderLikes (array $tag, array $renderer_states, XenForo_BbCode_Formatter_Base $formatter) {
        if (get_class ($formatter) == 'Esthetic_EBBC_BbCode_Formatter_Base') {
            return $formatter->renderTagsHidden ($tag, $renderer_states, 'like');
        }
        return '';
    }

    /**
     * Рендер кода days
     * @param   array       $tag
     * @param   array       $renderer_states
     * @param   XenForo_BbCode_Formatter_Base $formatter
     * @return string
     */
    public static function renderDays (array $tag, array $renderer_states, XenForo_BbCode_Formatter_Base $formatter) {
        if (get_class ($formatter) == 'Esthetic_EBBC_BbCode_Formatter_Base') {
            return $formatter->renderTagsHidden ($tag, $renderer_states, 'day');
        }
        return '';
    }

    /**
     * Рендер кода users
     * @param   array       $tag
     * @param   array       $renderer_states
     * @param   XenForo_BbCode_Formatter_Base $formatter
     * @return string
     */
    public static function renderUsers (array $tag, array $renderer_states, XenForo_BbCode_Formatter_Base $formatter) {
        if (get_class ($formatter) == 'Esthetic_EBBC_BbCode_Formatter_Base') {
            return $formatter->renderTagsHidden ($tag, $renderer_states, 'user');
        }
        return '';
    }

    /**
     * Рендер кода userids
     * @param   array       $tag
     * @param   array       $renderer_states
     * @param   XenForo_BbCode_Formatter_Base $formatter
     * @return string
     */
    public static function renderUserids (array $tag, array $renderer_states, XenForo_BbCode_Formatter_Base $formatter) {
        if (get_class ($formatter) == 'Esthetic_EBBC_BbCode_Formatter_Base') {
            return $formatter->renderTagsHidden ($tag, $renderer_states, 'userid');
        }
        return '';
    }

    /**
     * Рендер кода trophies
     * @param   array       $tag
     * @param   array       $renderer_states
     * @param   XenForo_BbCode_Formatter_Base $formatter
     * @return string
     */
    public static function renderTrophies (array $tag, array $renderer_states, XenForo_BbCode_Formatter_Base $formatter) {
        if (get_class ($formatter) == 'Esthetic_EBBC_BbCode_Formatter_Base') {
            return $formatter->renderTagsHidden ($tag, $renderer_states, 'trophy');
        }
        return '';
    }

    /**
     * Рендер кода visitor
     * @param   array       $tag
     * @param   array       $renderer_states
     * @param   XenForo_BbCode_Formatter_Base $formatter
     * @return string
     */
    public static function renderVisitor (array $tag, array $renderer_states, XenForo_BbCode_Formatter_Base $formatter) {
        if (get_class ($formatter) == 'Esthetic_EBBC_BbCode_Formatter_Base') {
            return $formatter->renderTagVisitor ($tag, $renderer_states);
        }
        return '';
    }

    /**
     * Рендер кода button
     * @param   array       $tag
     * @param   array       $renderer_states
     * @param   XenForo_BbCode_Formatter_Base $formatter
     * @return string
     */
    public static function renderButton (array $tag, array $renderer_states, XenForo_BbCode_Formatter_Base $formatter) {
        if (get_class ($formatter) == 'Esthetic_EBBC_BbCode_Formatter_Base') {
            return $formatter->renderTagButton ($tag, $renderer_states);
        }
        return '';
    }

    /* ************************* Интеграция с Esthetic Collaborative Shopping ************************* */
    /**
     * Рендер кода shoppings
     * @param   array       $tag
     * @param   array       $renderer_states
     * @param   XenForo_BbCode_Formatter_Base $formatter
     * @return string
     */
    public static function renderShoppings (array $tag, array $renderer_states, XenForo_BbCode_Formatter_Base $formatter) {
        if (get_class ($formatter) == 'Esthetic_EBBC_BbCode_Formatter_Base') {
            return $formatter->renderTagsHidden ($tag, $renderer_states, 'shopping');
        }
        return '';
    }

    /**
     * Рендер кода organized
     * @param   array       $tag
     * @param   array       $renderer_states
     * @param   XenForo_BbCode_Formatter_Base $formatter
     * @return string
     */
    public static function renderOrganized (array $tag, array $renderer_states, XenForo_BbCode_Formatter_Base $formatter) {
        if (get_class ($formatter) == 'Esthetic_EBBC_BbCode_Formatter_Base') {
            return $formatter->renderTagsHidden ($tag, $renderer_states, 'organized');
        }
        return '';
    }
}