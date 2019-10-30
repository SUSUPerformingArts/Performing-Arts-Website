<?php
/**
 * @version		$Id: admin.ccboard.php 110 2009-04-26 07:59:21Z thomasv $
 * @Project		ccBoard - Joomla! Bulletin Board Extension/Component
 * @author 		Thomas Varghese
 * @package		ccBoard
 * @copyright	Copyright (C) 2008-2009 codeclassic.org. All rights reserved.
 * @license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

defined('_JEXEC') or die('Restricted access');
require_once(JPATH_COMPONENT.DS.'defines.php');
require_once(JPATH_COMPONENT.DS.'controller.php');

	if (!JRequest::getVar('view') ) {
	    JRequest::setVar('view', 'ccboard');
	}

	$doc = &JFactory::getDocument();
	$doc->addStyleSheet(JURI::base().'components/com_ccboard/assets/admin-ccboard.css');

	$view = strtolower(JRequest::getVar('view')) ;
	$item1 = 'index.php?option=com_ccboard&view=ccboard';
	$item2 = 'index.php?option=com_ccboard&view=general';
	$item3 = 'index.php?option=com_ccboard&view=categories';
	$item4 = 'index.php?option=com_ccboard&view=forums';
	$item5 = 'index.php?option=com_ccboard&view=users';
	$item6 = 'index.php?option=com_ccboard&view=ranks';
	$item7 = 'index.php?option=com_ccboard&view=editcss';
	$item8 = 'index.php?option=com_ccboard&view=tools';
	$item9 = 'index.php?option=com_ccboard&view=about';

	if($view == 'general') {
		JToolBarHelper::title(JText::_('CCB_TB_GLOBAL_CONFIGURATION'), 'configuration');
	} elseif($view == 'categories') {
		JToolBarHelper::title(JText::_('CCB_TB_CATEGORIES'), 'category');
	} elseif($view == 'forums') {
		JToolBarHelper::title(JText::_('CCB_TB_FORUMS'), 'forums');
	} elseif($view == 'users') {
		JToolBarHelper::title(JText::_('CCB_TB_USERS'), 'users');
	} else if( $view == 'ccboard') {
		JToolBarHelper::title('ccBoard', 'ccboard');
	} else if( $view == 'about') {
		JToolBarHelper::title(JText::_('CCB_TB_ABOUT'), 'about');
	} else if( $view == 'editcss') {
		JToolBarHelper::title(JText::_('CCB_TB_EDITCSS'), 'editcss');
	} else if( $view == 'ranks') {
		JToolBarHelper::title(JText::_('CCB_TB_RANKS'), 'ranks');
	} else if( $view == 'tools') {
		JToolBarHelper::title(JText::_('CCB_TB_TOOLS'), 'tools');
	}


	JSubMenuHelper::addEntry('ccBoard', $item1, ($view == 'ccboard') );
	JSubMenuHelper::addEntry(JText::_('CCB_MENU_GENERAL'), $item2, ($view == 'general') );
	JSubMenuHelper::addEntry(JText::_('CCB_MENU_CATEGORIES'), $item3, ($view == 'categories'));
	JSubMenuHelper::addEntry(JText::_('CCB_MENU_FORUMS'), $item4, ($view == 'forums'));
	JSubMenuHelper::addEntry(JText::_('CCB_MENU_PROFILES'), $item5, ($view == 'users'));
	JSubMenuHelper::addEntry(JText::_('CCB_MENU_RANKS'), $item6, ($view == 'ranks'));
	JSubMenuHelper::addEntry(JText::_('CCB_MENU_CSS'), $item7, ($view == 'editcss'));
	JSubMenuHelper::addEntry(JText::_('CCB_MENU_TOOLS'), $item8, ($view == 'tools'));
	JSubMenuHelper::addEntry(JText::_('CCB_MENU_ABOUT'), $item9, ($view == 'about'));

	$controller = new ccboardController();
	$controller->execute(JRequest::getVar('task'));
	$controller->redirect();
?>
