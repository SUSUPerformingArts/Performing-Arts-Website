<?php
/**
* @version		$Id: helper.php 14401 2010-01-26 14:10:00Z louis $
* @package		Joomla
* @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once (JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');

class modRandArticlesHelper
{
	function renderItem(&$item, &$params, &$access)
	{
		global $mainframe;

		$user 	=& JFactory::getUser();

		$item->text 	= $item->introtext;
		$item->groups 	= '';
		$item->readmore = (trim($item->fulltext) != '');
		$item->metadesc = '';
		$item->metakey 	= '';
		$item->created 	= '';
		$item->modified = '';

		if ($params->get('readmore') || $params->get('link_titles'))
		{
			//if ($params->get('intro'))
			{
				// Check to see if the user has access to view the full article
				if ($item->access <= $user->get('aid', 0)) {
					$itemparams=new JParameter($item->attribs);
					//$readmoretxt=$itemparams->get('readmore',JText::_('Read more text'));
					//$readmoretxt=JText::_('Read more text');

					$item->linkOn = JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug, $item->sectionid));
					$item->linkText = JText::_('Read more text');
				} else {
					$item->linkOn = JRoute::_('index.php?option=com_user&view=login');
					$item->linkText = JText::_('Login To Read More');
				}
			}
		}
		
		$item->image='';
		$matches=array();
		if (preg_match('/<img[^>]*>/',$item->text,$matches))
		$item->image=$matches[0];
		$item->text = preg_replace( '/<img[^>]*>/', '', $item->text );
		
		if (!$params->get('image')) {
			$item->image='';
		}
		
		if (!$params->get('intro')) {
			$item->text='';
			$item->linkText='';
		}
		
		$item->text=substr($item->text,0,(int)$params->get('intro_size'));
		
		$results = $mainframe->triggerEvent('onAfterDisplayTitle', array (&$item, &$params, 1));
		$item->afterDisplayTitle = trim(implode("\n", $results));

		$results = $mainframe->triggerEvent('onBeforeDisplayContent', array (&$item, &$params, 1));
		$item->beforeDisplayContent = trim(implode("\n", $results));

		require(JModuleHelper::getLayoutPath('mod_randarticles', '_item'));
	}

	function getList(&$params, &$access)
	{
		global $mainframe;

		$db 	=& JFactory::getDBO();
		$user 	=& JFactory::getUser();
		$aid	= $user->get('aid', 0);

		$catid 	= (int) $params->get('catid', 0);
		$items 	= (int) $params->get('items', 0);

		$contentConfig	= &JComponentHelper::getParams( 'com_content' );
		$noauth			= !$contentConfig->get('show_noauth');
		$date =& JFactory::getDate();
		$now = $date->toMySQL();

		$nullDate = $db->getNullDate();

		// query to determine article count
		$query = 'SELECT a.*,' .
			' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug,'.
			' CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug'.
			' FROM #__content AS a' .
			' INNER JOIN #__categories AS cc ON cc.id = a.catid' .
			' INNER JOIN #__sections AS s ON s.id = a.sectionid' .
			' WHERE a.state = 1 ' .
			($noauth ? ' AND a.access <= ' .(int) $aid. ' AND cc.access <= ' .(int) $aid. ' AND s.access <= ' .(int) $aid : '').
			' AND (a.publish_up = '.$db->Quote($nullDate).' OR a.publish_up <= '.$db->Quote($now).' ) ' .
			' AND (a.publish_down = '.$db->Quote($nullDate).' OR a.publish_down >= '.$db->Quote($now).' )' .
			(($catid>0)?' AND cc.id = '. (int) $catid:'') .
			' AND cc.section = s.id' .
			' AND cc.published = 1' .
			' AND s.published = 1' .
			' ORDER BY RAND()';
		$db->setQuery($query, 0, $items);
		$rows = $db->loadObjectList();

		return $rows;
	}
}
