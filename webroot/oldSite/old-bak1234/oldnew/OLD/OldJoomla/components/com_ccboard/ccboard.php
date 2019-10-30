<?php
/**
 * @version		$Id: ccboard.php 109 2009-04-26 07:50:55Z thomasv $
 * @Project		ccBoard - Joomla! Bulletin Board Extension/Component
 * @author 		Thomas Varghese
 * @package		ccBoard
 * @copyright	Copyright (C) 2008-2009 codeclassic.org. All rights reserved.
 * @license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

	defined('_JEXEC') or die('Restricted access');

	require_once(JPATH_COMPONENT.DS.'controller.php');
	require_once(JPATH_COMPONENT_ADMINISTRATOR .DS.'defines.php');
	require_once(JPATH_COMPONENT_ADMINISTRATOR .DS.'ccboard-config.php');
	require_once(JPATH_COMPONENT.DS.'helpers'.DS.'helper.php');

	global $ccbConfig;

	$ccbConfig = new ccboardConfig();
	$doc = &JFactory::getDocument();
	$doc->addStyleSheet( CCBOARD_ASSETS_URL . '/ccboard.css');
	$controller = new ccboardController();
	$controller->execute(JRequest::getVar('task'), JRequest::getVar('view'));
	$controller->redirect();
?>
