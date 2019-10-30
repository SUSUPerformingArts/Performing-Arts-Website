<?php
/**
 * @version		$Id: view.html.php 109 2009-04-26 07:50:55Z thomasv $
 * @Project		ccBoard - Joomla! Bulletin Board Extension/Component
 * @author 		Thomas Varghese
 * @package		ccBoard
 * @copyright	Copyright (C) 2008-2009 codeclassic.org. All rights reserved.
 * @license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

defined( '_JEXEC' ) or die( 'Restricted Access' );
jimport( 'joomla.application.component.view' );
jimport('joomla.html.pagination');

class ccboardViewRecentList extends JView
{
    function display( $tmpl = null )
    {
    	global $mainframe;
    	global $ccbConfig;

    	$uri =& JFactory::getURI();

        $limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
        $limitstart = JRequest::getVar('limitstart',0);
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$theme = ccboardHelperConfig::getTheme();
		$userprofile = ccboardHelperConfig::getUserProfile();
		$postURL =  'index.php?option=com_ccboard&view=post';
		$boardname = $ccbConfig->boardname;

		$model = &$this->getModel('recentlist');
        $model->setLimits($limitstart, $limit);

        $items = $model->getData();
	    $pagination = new JPagination($model->getTotal(), $limitstart, $limit);
	    $sticky = $model->getSticky();

	    $labels = array();
		$labels['forum'] 	= JText::_('FORUM_P');
		$labels['topic'] 	= JText::_('TOPIC_P');
		$labels['subject'] 	= JText::_('SUBJECT_P');
		$labels['joined'] 	= JText::_('JOINED');
		$labels['post'] 	= JText::_('POSTS');
		$labels['location'] = JText::_('LOCATION');
		$labels['logip'] 	= JText::_('IP_LOGGED');
		$labels['notfound']	= JText::_('ACCESS_RESTRICTED_TO_THIS_SECTION');
		$labels['lastedit']= JText::_('LAST_EDIT');
		$labels['lasteditby']= JText::_('LAST_EDIT_BY');
		$labels['lasteditreason']= JText::_('LAST_EDIT_REASON');

        $this->assignRef('userprofile', $userprofile);
		$this->assignRef('postlistmodel', $model);
        $this->assignRef('pagination', $pagination);
        $this->assignRef('posturl', $postURL);
        $this->assignRef('items', $items);
        $this->assignRef('sticky', $sticky);
	    $this->assignRef('theme', $theme);
	    $this->assignRef('labels', $labels);
		$this->assignRef('action', $uri->toString());

		$document = &JFactory::getDocument();
		$document->setMetadata('keywords',  $boardname );
		$document->setTitle( $boardname . '::' .JText::_('BC_LATEST_POST') );
		$document->setDescription( $boardname . ' ' . JText::_('BC_LATEST_POST') );

		ccboardHelperConfig::setBreadCrumb('ccbhome');
        ccboardHelperConfig::setBreadCrumb( JText::_('BC_LATEST_POST') , '');

        parent::display($tmpl);
    }

	function setMyTemplate()
	{

	}
}
?>
