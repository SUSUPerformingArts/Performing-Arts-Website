<?php
/**
 * @version		$Id: ccbeditor.php 109 2009-04-26 07:50:55Z thomasv $
 * @Project		ccBoard - Joomla! Bulletin Board Extension/Component
 * @author 		Thomas Varghese
 * @package		ccBoard
 * @copyright	Copyright (C) 2008-2009 codeclassic.org. All rights reserved.
 * @license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

defined( '_JEXEC' ) or die( 'Restricted Access' );

require_once ( JPATH_SITE . DS . 'components' . DS. 'com_ccboard' . DS . 'assets' . DS . 'ccbeditor' . DS . 'nbbc.php' );

class ccbEditor {

	var $_bbParser;

	function ccbEditor()
	{
		// Initializer
		$this->_bbParser = new BBCode();
		$this->_bbParser->SetSmileyURL( JURI::root() . "components/com_ccboard/assets/ccbeditor/images/smileys");
		$this->_bbParser->SetSmileyDir(JURI::root() . "components/com_ccboard/assets/ccbeditor/images/smileys");
		$this->_bbParser->SetTagMarker('[');
		$this->_bbParser->SetAllowAmpersand(true);
		$this->_bbParser->SetEnableSmileys(true);
		$this->_bbParser->SetDetectURLs(true);
		$this->_bbParser->SetPlainMode(false);
		$this->_bbParser->AddRule('file',
					Array( 'mode' => BBCODE_MODE_ENHANCED,
							'template' => '', 'class' => 'block',
							'allow_in' => Array('listitem', 'block', 'columns'), ));
	}

	function parseContent( $textContent)
	{
		return  $this->_bbParser->Parse( $textContent );
	}

}
?>