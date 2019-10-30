<?php
/**
 * @version		$Id: view.html.php 142 2009-05-02 15:56:18Z thomasv $
 * @Project		ccBoard - Joomla! Bulletin Board Extension/Component
 * @author 		Thomas Varghese
 * @package		ccBoard
 * @copyright	Copyright (C) 2008-2009 codeclassic.org. All rights reserved.
 * @license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

defined( '_JEXEC' ) or die( 'Restricted Access' );
jimport( 'joomla.application.component.view' );

class ccboardViewEditrank extends JView
{
    function display( $tmpl = null )
    {
        global $mainframe;
		$uri =& JFactory::getURI();

        $model = &$this->getModel('editrank');
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
        	$icon = 'rankadd';
        } else {
        	$text = JText::_('CCB_EDIT');
        	$icon = 'rankedit';
        }
        JToolBarHelper::title(JText::_('CCB_RANK').': <small><small>[ ' . $text.' ]</small></small>',$icon );
        JToolBarHelper::save('saveRank');
        if ($isNew)  {
            JToolBarHelper::cancel('cancelRank');
        } else {
            // for existing items the button is renamed `close`
            JToolBarHelper::cancel('cancelRank', JText::_('CLOSE'));
        }

		$path = 'components/com_ccboard/assets/ranks/';
		$fullpath = JURI::root() . $path;
		$customJS = 'onchange="javascript:
			if (document.forms.adminForm.rank_image.options[selectedIndex].value!=\'\') {
				document.imagelib.src=\''.$fullpath.'\' + document.forms.adminForm.rank_image.options[selectedIndex].value;
			}
			else
			{
				document.imagelib.src=\'../images/blank.png\';
			}""';
        $rankimg = isset($item->rank_image) ? $item->rank_image : '';
		$rankcombo = JHTML::_( 'list.images', 'rank_image', $rankimg , $customJS, $path );
        $rankcombo .= '<br/><br/><img src="' . $fullpath . $rankimg  .'" name="imagelib"  border="0" alt="' . JText::_( 'CCB_PREVIEW' ) . '" /><br />';

        $ranksp = isset($item->rank_special)?$item->rank_special:0;
        $rankspecial = JHTML::_( 'select.booleanlist', 'rank_special', null, $ranksp );
        $this->assignRef('item', $item);
        $this->assignRef('rankspecial', $rankspecial);
        $this->assignRef('rankcombo', $rankcombo);
        $this->assignRef('action',$uri->toString());

        parent::display($tmpl);
    }

}
?>

