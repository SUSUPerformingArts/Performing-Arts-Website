<?php
/**
 * @version		$Id: view.html.php 110 2009-04-26 07:59:21Z thomasv $
 * @Project		ccBoard - Joomla! Bulletin Board Extension/Component
 * @author 		Thomas Varghese
 * @package		ccBoard
 * @copyright	Copyright (C) 2008-2009 codeclassic.org. All rights reserved.
 * @license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

defined( '_JEXEC' ) or die( 'Restricted Access' );
jimport( 'joomla.application.component.view' );
jimport('joomla.filesystem.file');

class ccboardViewEditcss extends JView
{
    function display( $tmpl = null )
    {
        global $mainframe;
		$uri =& JFactory::getURI();

        $text = JText::_('CCB_EDIT');
        $icon = 'edit_css';
        JToolBarHelper::title(JText::_('CCB_EDIT_CSS').': <small><small>[ ' . $text.' ]</small></small>',$icon );
        JToolBarHelper::save('saveCss');
        JToolBarHelper::cancel('cancelCss');

        $file = JPATH_COMPONENT_SITE . DS . 'assets' . DS . 'ccboard.css';
        $content = JFile::read($file);
        $content = htmlspecialchars($content, ENT_COMPAT, 'UTF-8');

        $this->assignRef('content', $content);
        $this->assignRef('filename', $file);
        $this->assignRef('action',$uri->toString());

        parent::display($tmpl);
    }
}
?>

