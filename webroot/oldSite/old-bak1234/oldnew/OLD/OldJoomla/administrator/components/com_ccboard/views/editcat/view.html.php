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

class ccboardViewEditcat extends JView
{
    function display( $tmpl = null )
    {
        global $mainframe;
		$uri =& JFactory::getURI();

        $model = &$this->getModel();
        $id = JRequest::getVar('id', JRequest::getVar('cid'));

        if (is_array($id)) {
            $id = $id[0];
        }

        $model->setId($id);
        $item = $model->getData();
        $isNew = isset($item->id)?($item->id < 1):1;

        //set up the tool bar
        if( $isNew ) {
        	$text = JText::_('CCB_NEW');
        	$icon = 'cat_add';
        } else {
        	$text = JText::_('CCB_EDIT');
        	$icon = 'cat_edit';
        }
        JToolBarHelper::title(JText::_('CATEGORY').': <small><small>[ ' . $text.' ]</small></small>',$icon );
        JToolBarHelper::save('saveCat');
        if ($isNew)  {
            JToolBarHelper::cancel('cancelCat');
        } else {
            // for existing items the button is renamed `close`
            JToolBarHelper::cancel('cancelCat', JText::_('CLOSE'));
        }

        $ordering = $this->_getOrdering($item);

        $this->assignRef('item', $item);
        $this->assignRef('ordering', $ordering);
        $this->assignRef('action',$uri->toString());

        parent::display($tmpl);
    }

    function _getOrdering(&$item)
    {
        $model = &$this->getModel();

        return JHTML::_('list.specificordering', $item, @$item->id, $model->getOrderQuery(), @$item->id == 0);
    }
}
?>

