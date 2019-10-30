<?php
/**
 * @version		$Id: view.html.php 171 2009-09-21 14:36:52Z thomasv $
 * @Project		ccBoard - Joomla! Bulletin Board Extension/Component
 * @author 		Thomas Varghese
 * @package		ccBoard
 * @copyright	Copyright (C) 2008-2009 codeclassic.org. All rights reserved.
 * @license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

defined( '_JEXEC' ) or die( 'Restricted Access' );
jimport( 'joomla.application.component.view' );

class ccboardViewGeneral extends JView
{

    function display($tmpl = null)
    {
    	JToolBarHelper::save('saveGeneral');
    	$uri = &JFactory::getURI();
    	$model = &$this->getModel(); // General
    	$item = $model->getData();

    	$lockradio = JHTML::_('select.booleanlist',  'boardlocked', '', $item->boardlocked );
    	$showrealname = JHTML::_('select.booleanlist',  'showrealname', '', $item->showrealname );
    	$showeditmarkup = JHTML::_('select.booleanlist',  'showeditmarkup', '', $item->showeditmarkup );
    	$emailsub = JHTML::_('select.booleanlist',  'emailsub', '', $item->emailsub );
        $autosub = JHTML::_('select.booleanlist',  'autosub', '', $item->autosub );
    	$showrank = JHTML::_('select.booleanlist',  'showrank', '', $item->showrank );
		$showfavourites = JHTML::_('select.booleanlist',  'showfavourites', '', $item->showfavourites );
    	$showkarma = JHTML::_('select.booleanlist',  'showkarma', '', $item->showkarma );
        $showboardsummary = JHTML::_('select.booleanlist',  'showboardsummary', '', $item->showboardsummary );
        $showreglink = JHTML::_('select.booleanlist',  'showreglink', '', $item->showreglink);
        $showloginlink = JHTML::_('select.booleanlist',  'showloginlink', '', $item->showloginlink);
        $showtopicavatar = JHTML::_('select.booleanlist',  'showtopicavatar', '', $item->showtopicavatar);
        $showquickreply = JHTML::_('select.booleanlist',  'showquickreply', '', $item->showquickreply );
    	$logipradio = JHTML::_('select.booleanlist',  'logip', '', $item->logip );
    	$avatarupload = JHTML::_('select.booleanlist',  'avatarupload', '', $item->avatarupload );
		$attachments = JHTML::_('select.booleanlist',  'attachments', '', $item->attachments );

		jimport('joomla.html.pane');
		$pane =& JPane::getInstance('sliders');

		$this->assignRef('pane', $pane );
		$this->assignRef('item', $item);
    	$this->assignRef('lockradio', $lockradio);
    	$this->assignRef('showrealname',$showrealname);
    	$this->assignRef('showeditmarkup',$showeditmarkup);
    	$this->assignRef('emailsub',$emailsub);
        $this->assignRef('autosub',$autosub);
    	$this->assignRef('showrank',$showrank);
    	$this->assignRef('showfavourites',$showfavourites);
    	$this->assignRef('showkarma',$showkarma);
    	$this->assignRef('showboardsummary',$showboardsummary);
        $this->assignRef('showloginlink',$showloginlink);
        $this->assignRef('showreglink',$showreglink);
        $this->assignRef('showtopicavatar',$showtopicavatar);
        $this->assignRef('showquickreply',$showquickreply);
    	$this->assignRef('logipradio', $logipradio);
    	$this->assignRef('attachments', $attachments);
    	$this->assignRef('avatarupload', $avatarupload);
    	$this->assignRef('action', $uri->toString());

    	parent::display($tmpl);
    }

}
?>
