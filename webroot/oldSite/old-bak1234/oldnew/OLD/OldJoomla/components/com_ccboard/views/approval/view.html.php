<?php
/**
 * @version		$Id: view.html.php 162 2009-08-22 18:33:26Z thomasv $
 * @Project		ccBoard - Joomla! Bulletin Board Extension/Component
 * @author 		Thomas Varghese
 * @package		ccBoard
 * @copyright	Copyright (C) 2008-2009 codeclassic.org. All rights reserved.
 * @license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

defined( '_JEXEC' ) or die( 'Restricted Access' );
jimport( 'joomla.application.component.view' );
jimport('joomla.html.pagination');

class ccboardViewApproval extends JView
{
    function display( $tmpl = null )
    {
    	global $ccbConfig;
    	global $mainframe;
    	$uri =& JFactory::getURI();

    	$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
        $limitstart = JRequest::getVar('limitstart',0);
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

        $theme = ccboardHelperConfig::getTheme();
		$userprofile = ccboardHelperConfig::getUserProfile();

		$model = $this->getModel('approval','ccboard');
        $model->setLimits($limitstart, $limit);
        $items = $model->getData();
    	$pagination = new JPagination($model->getTotal(), $limitstart, $limit);
    	$approvelink = 'index.php?option=com_ccboard&task=postApprove&post=';

		$labels = array();
		$labels['subject'] 	= JText::_('SUBJECT_P');
		$labels['forum'] 	= JText::_('FORUM_P');
		$labels['joined'] 	= JText::_('JOINED');
		$labels['post'] 	= JText::_('POSTS');
		$labels['location'] = JText::_('LOCATION');
		$labels['approve']  = JText::_('APPROVE');
		$labels['delete']   = JText::_('DELETE');

        $this->assignRef('items', $items);
		$this->assignRef('userprofile', $userprofile);
		$this->assignRef('model', $model);
	    $this->assignRef('theme', $theme);
	    $this->assignRef('labels', $labels);
        $this->assignRef('pagination', $pagination);
        $this->assignRef('approvelink', $approvelink);
        $this->assignRef('action', $uri->toString());

    	ccboardHelperConfig::setBreadCrumb('ccbhome');
		ccboardHelperConfig::setBreadCrumb(JText::_('APPROVE'));

        parent::display($tmpl);
    }
}

?>

