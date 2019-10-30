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
jimport('joomla.html.pagination');

class ccboardViewPostList extends JView
{
    function display( $tmpl = null )
    {
    	global $ccbConfig;
    	$mainframe = &JFactory::getApplication();
    	$uri =& JFactory::getURI();

        $limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
        $limitstart = JRequest::getVar('limitstart',0);
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
        $topic_id = (int) JRequest::getVar('topic');
        $post_id = (int) JRequest::getVar('post');
        $postMove = (int) JRequest::getVar('postmove',0);
        $postReport = (int) JRequest::getVar('postreport',0);

        $theme = ccboardHelperConfig::getTheme();
		$userprofile = ccboardHelperConfig::getUserProfile();
		$postURL =  'index.php?option=com_ccboard&view=post';

		$model = $this->getModel('postlist','ccboard');
        $model->setTopicId($topic_id);
        $model->setLimits($limitstart, $limit);

        $items = array();
        $items = $model->getData();
    	$sticky = $model->getSticky();
		$modActions = $model->isModerator();
    	$emailSub = $model->isEmailSubscribed();
    	$allowPosting = $model->isPostingAllowed();
    	$favourite = $model->isFavourite();
    	$postLock = $model->isLocked();
    	$pagination = new JPagination($model->getTotal(), $limitstart, $limit);
    	$allowEditing = $model->isEditingAllowed();
    	$startPost = $model->getStartingPost();

        $trow = &JTable::getInstance('ccbtopic', 'ccboardTable');
    	$trow->hit($topic_id);

		$labels = array();
		$labels['subject'] 	= JText::_('SUBJECT_P');
		$labels['joined'] 	= JText::_('JOINED');
		$labels['post'] 	= JText::_('POSTS');
		$labels['location'] = JText::_('LOCATION');
		$labels['logip'] 	= JText::_('IP_LOGGED');
		$labels['postreply']= JText::_('BUTTON_POST_REPLY');
		$labels['postedit'] = JText::_('BUTTON_POST_EDIT');
		$labels['postdelete']= JText::_('BUTTON_POST_DELETE');
		$labels['postquote']= JText::_('BUTTON_POST_QUOTE');
		$labels['postmove'] = JText::_('BUTTON_POST_MOVE');
		$labels['postsplit'] = JText::_('BUTTON_POST_SPLIT');
		$labels['postlock'] = JText::_('BUTTON_POST_LOCK');
		$labels['postunlock'] = JText::_('BUTTON_POST_UNLOCK');
		$labels['notfound']	= JText::_('ACCESS_RESTRICTED_TO_THIS_SECTION');
		$labels['lastedit']= JText::_('LAST_EDIT');
		$labels['lasteditby']= JText::_('LAST_EDIT_BY');
		$labels['lasteditreason']= JText::_('LAST_EDIT_REASON');
        $labels['quickreply']= JText::_('QUICK_REPLY');
    	$labels['save'] = JText::_( 'SAVE');
    	$labels['cancel'] = JText::_( 'CANCEL');
        $labels['invalidtext'] = JText::_( 'POST_CAN_NOT_BE_BLANK');
        $labels['captcha'] = JText::_( 'SECURITY');

        
		if( $postMove > 0 && $modActions > 0 ) {
			$this->addTemplatePath( JPATH_COMPONENT_SITE.DS.'views'.DS.'postlist'.DS.'move');
			$this->assignRef('fcombo', $this->getForumCombo( $model->getForumList()));
			$this->assignRef('forumname', $model->getForumName());
			$this->assignRef('post_id', $post_id);
		}

		if( $postReport > 0 && $allowPosting > 0 ) {
			$this->addTemplatePath( JPATH_COMPONENT_SITE.DS.'views'.DS.'postlist'.DS.'report');
			$this->assignRef('forumname', $model->getForumName());
		}

        $cap_path = '';
		if( (($ccbConfig->showcaptcha == 1 && $user->guest) || ($ccbConfig->showcaptcha == 2)) && ( $ccbConfig->showquickreply > 0) ) {
    		$key = substr( md5(microtime() * mktime()), 0, 5);
			$temp_session = $_SESSION;
			session_write_close();
			ini_set('session.save_handler','files');
			session_start();
			$_SESSION['ccbkey'] = $key;
			session_write_close();
			ini_set('session.save_handler','user');
			$jd = new JSessionStorageDatabase();
			$jd->register('ccbkey');
			session_start();
			$_SESSION = $temp_session;
			$e = session_id();
			$se = &JFactory::getSession();
			$se->set('ccbkey',$key);
			$cap_path = 'components/com_ccboard/helpers/captcha.php?sid=' . $e;
		}


        $this->assignRef('items', $items);
        $this->assignRef('sticky', $sticky);
		$this->assignRef('userprofile', $userprofile);
		$this->assignRef('postlistmodel', $model);
        $this->assignRef('modactions',$modActions);
        $this->assignRef('emailsub',$emailSub);
        $this->assignRef('favourite',$favourite);
        $this->assignRef('allowposting',$allowPosting);
        $this->assignRef('allowEditing',$allowEditing);
        $this->assignRef('postLock',$postLock);
        $this->assignRef('startPost',$startPost);
	    $this->assignRef('theme', $theme);
	    $this->assignRef('posturl', $postURL);
	    $this->assignRef('labels', $labels);
        $this->assignRef('pagination', $pagination);
        $this->assignRef('action', $uri->toString());
        $this->assignRef('cap_path', $cap_path);


	    $document = &JFactory::getDocument();
		$document->setTitle( $ccbConfig->boardname . '::' . $model->getForumName() . '::' . (isset($items[0]->post_subject)?$items[0]->post_subject:'') );
		$document->setDescription( $ccbConfig->boardname  . '::' . $model->getForumName() . '::' . (isset($items[0]->post_subject)?$items[0]->post_subject:'') );
		$document->setMetadata('keywords',  isset($items[0]->post_subject)?$items[0]->post_subject:'' );

    	ccboardHelperConfig::setBreadCrumb('ccbhome');
		ccboardHelperConfig::setBreadCrumb($model->getForumName(), JRoute::_('index.php?option=com_ccboard&view=topiclist&forum='. $model->getForumId().'&Itemid=' . $ccbConfig->itemid ) );
		ccboardHelperConfig::setBreadCrumb(isset($items[0]->post_subject)?$items[0]->post_subject:'');

        parent::display($tmpl);
    }

    function getForumCombo( $flist )
    {
        $farr =  array();
        for($i=0; $i < count($flist); $i++) {
        	$farr[] = JHTML::_('select.option', $flist[$i]->id, $flist[$i]->forum_name);
        }
        $fcombo = JHTML::_( 'Select.genericlist', $farr, 'fmov_id','style="width: 400px;"','value','text',$flist[0]->id);
 		return $fcombo;
    }
}
?>
