<?php
/**
 * @version		$Id: view.html.php 110 2009-04-26 07:59:21Z thomasv $
 * @Project		ccBoard - Joomla! Bulletin Board Extension/Component
 * @author 		Thomas Varghese
 * @package		ccBoard
 * @copyright	Copyright (C) 2008-2009 codeclassic.org. All rights reserved.
 * @license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.view' );

class ccboardViewTools extends JView
{

	function display( $tpl = null )
	{
		$uri =& JFactory::getURI();
		$parser		=& JFactory::getXMLParser('Simple');
		$xml		= JPATH_COMPONENT . DS . 'ccboard.xml';

		$parser->loadFile( $xml );
		$doc		=& $parser->document;
		$element	=& $doc->getElementByPath( 'version' );
		$version	= $element->data();

		$this->assign( 'version'	, $version );
		$this->assignRef('action',$uri->toString());

		parent::display( $tpl );
	}
}

