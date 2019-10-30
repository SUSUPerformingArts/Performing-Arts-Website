<?php
/**
 * @version		$Id: view.html.php 173 2009-09-21 14:43:37Z thomasv $
 * @Project		ccBoard - Joomla! Bulletin Board Extension/Component
 * @author 		Thomas Varghese
 * @package		ccBoard
 * @copyright	Copyright (C) 2008-2009 codeclassic.org. All rights reserved.
 * @license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

defined( '_JEXEC' ) or die( 'Restricted Access' );
jimport( 'joomla.application.component.view' );

class ccboardViewForumList extends JView
{
    function display( $tmpl = null )
    {
    	global $ccbConfig;
    	$theme = ccboardHelperConfig::getTheme();
		$userprofile = ccboardHelperConfig::getUserProfile();

		$topicURL = 'index.php?option=com_ccboard&view=topiclist';
		$postURL =  'index.php?option=com_ccboard&view=postlist';

    	$model = &$this->getModel('forumlist');
        $cat_id = (int) JRequest::getVar('cat', 0);
        $model->setCatId( $cat_id);
        $items = $model->getData();
        $sticky = $model->getSticky();
        if( $ccbConfig->showboardsummary == 1) {
            $boardsummary = $model->getBoardSummary();
        } else {
            $boardsummary = null;
        }
        
        $lables = array();
        $labels['forums'] = JText::_('FORUMS');
        $labels['topics'] = JText::_('TOPICS');
        $labels['posts']  = JText::_('POSTS');
        $labels['lastpost'] = JText::_('LAST_POST');

        $this->assignRef('userprofile', $userprofile);
        $this->assignRef('forummodel', $model);
        $this->assignRef('boardsummary', $boardsummary);
        $this->assignRef('theme', $theme);
        $this->assignRef('items', $items);
        $this->assignRef('sticky', $sticky);
	    $this->assignRef('topicurl', $topicURL);
	    $this->assignRef('posturl', $postURL);
	    $this->assignRef('labels', $labels);

		$document	=& JFactory::getDocument();
		$document->setTitle( $ccbConfig->boardname );
		$document->setDescription( $ccbConfig->boardname );
		$document->setMetadata('keywords',  $ccbConfig->boardname );

	    ccboardHelperConfig::setBreadCrumb('ccbhome');
		parent::display($tmpl);
    }
}

?>
