<?php

/**
 * @version		$Id: view.html.php 131 2009-05-01 13:52:09Z thomasv $
 * @Project		ccBoard - Joomla! Bulletin Board Extension/Component
 * @author 		Thomas Varghese
 * @package		ccBoard
 * @copyright	Copyright (C) 2008-2009 codeclassic.org. All rights reserved.
 * @license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

defined( '_JEXEC' ) or die( 'Restricted Access' );
jimport( 'joomla.application.component.view' );

class ccboardViewMyList extends JView
{

	function display( $tmpl = null )
    {
        jimport('joomla.html.pagination');
    	global $mainframe;
    	global $ccbConfig;

    	$user = &JFactory::getUser();
    	if($user->guest ) {
    		$mainframe->enqueueMessage( JText::_('UNAUTHORIZED_ACCESS'), 'error');
    		return;
    	}
        $limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
        $limitstart = JRequest::getVar('limitstart',0);
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
    	$uri =& JFactory::getURI();

    	$viewmode=JRequest::getVar('viewmode','myposts');

    	$model = $this->getModel('mylist','ccboard');
        $model->setLimits($limitstart, $limit);
        $model->setViewMode( $viewmode );	// myposts, mysubs, myfavours (3)
   		$items = $model->getMyPosts();
   		$sticky = $model->getSticky();

        $pagination = new JPagination($model->getTotal(), $limitstart, $limit);
        $theme = ccboardHelperConfig::getTheme();
		$userprofile = ccboardHelperConfig::getUserProfile();

		if( $viewmode == 'myposts') {
			$labels['pagetitle'] = JText::_('MYPOSTS_LINK');
		} elseif( $viewmode == 'mysubs') {
			$labels['pagetitle'] = JText::_('MYSUBS_LINK');
		} else {
			$labels['pagetitle'] = JText::_('MYFAVOURS_LINK');
		}

        $lables = array();
        $labels['subject'] = JText::_('SUBJECT');
        $labels['forum']  = JText::_('FORUM');
        $labels['date']  	= JText::_('POST_DATE');
        $labels['hits'] = JText::_('VIEWS');
        $labels['myposts'] = JText::_('MYPOSTS_LINK');
		$labels['mysubs'] = JText::_('MYSUBS_LINK');
        $labels['myfavours'] = JText::_('MYFAVOURS_LINK');
        $labels['delete'] = JText::_('DELETE');

		$this->assignRef('userprofile', $userprofile);
        $this->assignRef('items', $items);
        $this->assignRef('sticky', $sticky);
        $this->assignRef('theme', $theme);
		$this->assignRef('viewmode', $viewmode);
        $this->assignRef('labels', $labels);
        $this->assignRef('pagination', $pagination);
		$this->assignRef('action', $uri->toString());

		$document	=& JFactory::getDocument();
		$document->setTitle( $ccbConfig->boardname . '::'. $labels['pagetitle'] );

		ccboardHelperConfig::setBreadCrumb('ccbhome');
		ccboardHelperConfig::setBreadCrumb(JText::_('MYPOSTS'));
		parent::display($tmpl);

    }

}
?>
