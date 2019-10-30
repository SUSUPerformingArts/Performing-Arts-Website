<?php
/**
 * @version		$Id: defines.php 110 2009-04-26 07:59:21Z thomasv $
 * @Project		ccBoard - Joomla! Bulletin Board Extension/Component
 * @author 		Thomas Varghese
 * @package		ccBoard
 * @copyright	Copyright (C) 2008-2009 codeclassic.org. All rights reserved.
 * @license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/
defined('_JEXEC') or die('Restricted access');

define( 'CCBOARD_ASSETS_PATH' 		, 	JPATH_COMPONENT . DS . 'assets' );
define( 'CCBOARD_ASSETS_URL' 		, 	JURI::base() . 'components/com_ccboard/assets' );
define( 'CCBOARD_BASE_PATH'		, 		dirname( JPATH_BASE ) . DS . 'components' . DS . 'com_ccboard' );
define( 'CCBOARD_BASE_ASSETS_PATH', 	JPATH_BASE . DS . 'components' . DS . 'com_ccboard' . DS . 'assets' );
define( 'CCBOARD_BASE_ASSETS_URL'	, 	JURI::root() . 'components/com_ccboard/assets' );
define( 'CCBOARD_COPYRIGHT'	, 			'<div style="text-align:center; margin:auto; float:left; width: 100%; font-family: Arial, Verdana; font-size: 12px; color: orange;">Powered by <a href="http://codeclassic.org"><b>ccBoard</b></a></div>');

