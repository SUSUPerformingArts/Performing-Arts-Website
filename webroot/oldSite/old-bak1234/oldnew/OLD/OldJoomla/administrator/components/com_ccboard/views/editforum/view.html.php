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

class ccboardViewEditForum extends JView
{
    function display( $tmpl = null )
    {
        require_once(JPATH_COMPONENT.DS.'elements'.DS.'permissions.php');
    	global $mainframe;
    	$uri =& JFactory::getURI();

        $model = &$this->getModel();
        $id = JRequest::getVar('id', JRequest::getVar('cid'));

        if (is_array($id)) {
            $id = $id[0];
        }

        $model->setId($id);
        $item = $model->getData();
        $catItems = $model->getCategories();
        $moderators = $model->getModerators();
        $isNew = isset($item->id)?($item->id < 1):1;

        $catarr =  array();
        for($i=0; $i < count($catItems); $i++) {
        	$catarr[] = JHTML::_('select.option', $catItems[$i]->id, $catItems[$i]->cat_name);
        }

        $cat_id = isset($item->cat_id)?$item->cat_id:0;
       	$catcombo 		= JHTML::_( 'Select.genericlist', $catarr, 'cat_id','style="width:255px;"','value','text',$cat_id);
      	$publishedradio	= JHTML::_( 'select.booleanlist','published','',isset($item->published)?$item->published:1);
      	$lockradio 		= JHTML::_( 'select.booleanlist','locked','',isset($item->locked)?$item->locked:0);
      	$moderatedradio = JHTML::_( 'select.booleanlist','moderated','',isset($item->moderated)?$item->moderated:0);
      	$reviewradio 	= JHTML::_( 'select.booleanlist','review','',isset($item->review)?$item->review:0);

        //set up the tool bar
        if( $isNew ) {
        	$text = JText::_('CCB_NEW');
        	$icon = 'forum_add';
        } else {
        	$text = JText::_('CCB_EDIT');
        	$icon = 'forum_edit';
        }

        JToolBarHelper::title(JText::_('Forum').': <small><small>[ ' . $text.' ]</small></small>', $icon );
        JToolBarHelper::save('saveForum');
        if ($isNew)  {
            JToolBarHelper::cancel('cancelForum');
        } else {
            // for existing items the button is renamed `close`
            JToolBarHelper::cancel('cancelForum', JText::_('CLOSE'));
        }

        $ordering = $this->_getOrdering($item, $model);
        if( $isNew ) {
        	$vfor = 0;
        	$pfor = JElementPermissions::getRegistered();
        }
        else
        {
        	$vfor = $item->view_for;
        	$pfor = $item->post_for;
        }

        $this->assignRef('item', 			$item);
        $this->assignRef('catItems', 		$catItems);
        $this->assignRef('moderators', 		$moderators);
        $this->assignRef('ordering', 		$ordering);
		$this->assignRef('catcombo', 		$catcombo);
		$this->assignRef('publishedradio',	$publishedradio);
		$this->assignRef('lockradio', 		$lockradio);
		$this->assignRef('moderatedradio',	$moderatedradio);
		$this->assignRef('reviewradio',		$reviewradio);
		$this->assignRef('viewfor',     	JElementPermissions::fetchElement('view_for',     $vfor, $this, ''));
        $this->assignRef('postfor', 		JElementPermissions::fetchElement('post_for',     $pfor, $this, ''));

        $this->assignRef('action', $uri->toString());

        parent::display($tmpl);
    }

    function _getOrdering(&$item, &$model)
    {
        return JHTML::_('list.specificordering', $item, @$item->id, $model->getOrderQuery(), @$item->id == 0);
    }

}
?>

