<?php

/**
 * Контроллер Tag
 * @package     Esthetic_EBBC
 */
class Esthetic_EBBC_ControllerPublic_Tag extends XFCP_Esthetic_EBBC_ControllerPublic_Tag {
    /**
     * Дополнение функционала действия Tag
     * @return  XenForo_ControllerResponse_View
     */
	public function actionTag()
	{
		$tagModel = $this->_getTagModel();

		$tagUrl = $this->_input->filterSingle('tag_url', XenForo_Input::STRING);
		$tag = $tagModel->getTagByUrl($tagUrl);
		if (!$tag)
		{
			return $this->responseError(new XenForo_Phrase('requested_tag_not_found'), 404);
		}

		$page = max(1, $this->_input->filterSingle('page', XenForo_Input::UINT));
		$perPage = XenForo_Application::getOptions()->searchResultsPerPage;

		$unpreparedResults = null;

		$cache = $tagModel->getTagResultsCache($tag['tag_id']);
		if ($cache)
		{
			$contentTags = json_decode($cache['results'], true);
		}
		else
		{
			$limit = XenForo_Application::getOptions()->maximumSearchResults;
			$contentTags = $tagModel->getContentIdsByTagId($tag['tag_id'], $limit);
			$insertCache = (count($contentTags) > $perPage); // if we would have more than one page, lets cache this to save work

			$contentTags = $tagModel->getViewableTagResults(array_values($contentTags), null, $unpreparedResults);
			if (!$contentTags)
			{
				return $this->responseMessage(new XenForo_Phrase('no_results_found'));
			}

			if ($insertCache)
			{
				$tagModel->insertTagResultsCache($tag['tag_id'], $contentTags);
			}
		}

		$totalResults = count($contentTags);

		$this->canonicalizePageNumber($page, $perPage, $totalResults, 'tags', $tag);
		$this->canonicalizeRequestUrl(
			XenForo_Link::buildPublicLink('tags', $tag, array('page' => $page))
		);

		$pageResultIds = array_slice($contentTags, ($page - 1) * $perPage, $perPage);

		if ($unpreparedResults)
		{
			// we already queried and filtered this data, we just need to filter it down and prepare it
			$results = $tagModel->finalizeUnpreparedResults($unpreparedResults, $pageResultIds);
		}
		else
		{
			$results = $tagModel->getTagResultsForDisplay($pageResultIds);
		}

		$resultStartOffset = ($page - 1) * $perPage + 1;
		$resultEndOffset = ($page - 1) * $perPage + count($pageResultIds);

		$ignoredNames = array();
		foreach ($results['results'] AS $result)
		{
			$content = $result['content'];
			if (!empty($content['isIgnored']) && !empty($content['user_id']) && !empty($content['username']))
			{
				$ignoredNames[$content['user_id']] = $content['username'];
			}
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

		$viewParams = array(
			'tag' => $tag,
			'results' => $results,
			'ignoredNames' => $ignoredNames,

			'resultStartOffset' => $resultStartOffset,
			'resultEndOffset' => $resultEndOffset,

			'page' => $page,
			'perPage' => $perPage,
			'totalResults' => $totalResults
		);
		return $this->responseView('XenForo_ViewPublic_Tag_View', 'tag_view', $viewParams);
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