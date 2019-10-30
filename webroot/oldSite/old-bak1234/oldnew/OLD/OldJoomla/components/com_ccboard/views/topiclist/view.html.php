<?php
/**
 * @version		$Id: view.html.php 125 2009-05-01 09:35:58Z thomasv $
 * @Project		ccBoard - Joomla! Bulletin Board Extension/Component
 * @author 		Thomas Varghese
 * @package		ccBoard
 * @copyright	Copyright (C) 2008-2009 codeclassic.org. All rights reserved.
 * @license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

defined( '_JEXEC' ) or die( 'Restricted Access' );
jimport( 'joomla.application.component.view' );
jimport('joomla.html.pagination');

class ccboardViewTopicList extends JView
{

    function display( $tmpl = null )
    {
		global $mainframe;
    	global $ccbConfig;

        $limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
        $limitstart = JRequest::getVar('limitstart',0);
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
    	$forum_id = (int) JRequest::getVar('forum');
    	$uri =& JFactory::getURI();

        $model = $this->getModel('topiclist');
        $model->setForumId($forum_id);
        $model->setLimits($limitstart, $limit);
        $model->setOrdering('last_post_time','desc');

        $items = $model->getData();
        $sticky = $model->getSticky();

        $pagination = new JPagination($model->getTotal(), $limitstart, $limit);
        $forum_name = $model->getForumName();
        $postingAllowed = $model->isPostingAllowed();
		$theme = ccboardHelperConfig::getTheme();
		$userprofile = ccboardHelperConfig::getUserProfile();

		$postURL = 'index.php?option=com_ccboard&view=postlist';
        $newtopic = JRoute::_('index.php?option=com_ccboard&view=post&forum='.(int)$forum_id . '&topic=0&post=0&mode=post&Itemid=' . $ccbConfig->itemid);

        $lables = array();
        $labels['topics'] = JText::_('TOPICS');
        $labels['replies']  = JText::_('REPLIES');
        $labels['views']  	= JText::_('VIEWS');
        $labels['lastpost'] = JText::_('LAST_POST');
        $labels['newtopic'] = JText::_('BUTTON_NEW_TOPIC');

        $this->assignRef('forum_id', $forum_id);
        $this->assignRef('forum_name', $forum_name);
		$this->assignRef('postingAllowed', $postingAllowed);
		$this->assignRef('userprofile', $userprofile);
        $this->assignRef('items', $items);
        $this->assignRef('sticky', $sticky);
	    $this->assignRef('theme', $theme);
	    $this->assignRef('posturl', $postURL);
	    $this->assignRef('newtopic', $newtopic);
	    $this->assignRef('labels', $labels);
        $this->assignRef('pagination', $pagination);
        $this->assignRef('filter_order', $filter_order);
        $this->assignRef('filter_orderDir', $filter_orderDirection);
		$this->assignRef('action', $uri->toString());

		$document	=& JFactory::getDocument();
		$document->setTitle( $ccbConfig->boardname . '::'. $forum_name );
		$document->setDescription( $forum_name );
		$document->setMetadata('keywords',  $forum_name );

		ccboardHelperConfig::setBreadCrumb('ccbhome');
		ccboardHelperConfig::setBreadCrumb($forum_name);
		parent::display($tmpl);
    }
}
?>
