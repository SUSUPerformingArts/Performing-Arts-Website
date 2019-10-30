<?php
/**
 * @version		$Id: controller.php 174 2009-09-21 16:48:57Z thomasv $
 * @Project		ccBoard - Joomla! Bulletin Board Extension/Component
 * @author 		Thomas Varghese
 * @package		ccBoard
 * @copyright	Copyright (C) 2008-2009 codeclassic.org. All rights reserved.
 * @license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

defined( '_JEXEC' ) or die( 'Restricted Access' );
jimport('joomla.application.component.controller');
require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'helper.php';

class ccboardController extends JController
{
    function ccboardController()
    {
        parent::__construct();
    }

    function display($tmpl = null)
    {
    	global $ccbConfig;
    	$page = JRequest::getVar('view');
	    $user = &JFactory::getUser();
	    $mainframe = &JFactory::getApplication();
	    $userprof = $ccbConfig->userprofile;
	    $uri = &JFactory::getURI();
	    $returnURL = base64_encode($uri->toString());

    	if( ((int)$ccbConfig->boardlocked) == 1 ) {
    		$mainframe->enqueueMessage( JText::_('BOARD_LOCKED') . '<br/>' . $ccbConfig->lockedmsg , 'error');
            return;
    	}

       	if( $userprof == 'combuilder') {
        	$link =  'index.php?option=com_comprofiler&task=login';
       	} elseif( $userprof == 'jomsocial') {
       		$link = 'index.php?option=com_community&view=frontpage';
       	} else {
       		$link = 'index.php?option=com_user&view=login';
       	}

	    $link = JRoute::_( $link . '&return=' . $returnURL, false);


		if (!$page) {
	 	   JRequest::setVar('view', 'forumlist');
    	}
    	elseif( $page == 'myprofile') {
			$id = JRequest::getVar('id', JRequest::getVar('cid'));
			if (is_array($id)) { $id = $id[0]; }
			$id = isset($id) ? $id: $user->get('id');
			if( $id < 1 ) {
            	$mainframe->enqueueMessage( JText::_('INVALID_PROFILE_ACCESS'), 'error');
            	$mainframe->redirect($link);
			}
    	}

    	if( $page <> 'post') {
    		$session = JFactory::getSession();
			$session->set('postnotload', 0);
			$session->set('attachments', array());
    	}

    	parent::display($tmpl);
    }

    function savePost($tmpl = null)
    {
    	global $ccbConfig;
    	$mainframe = &JFactory::getApplication();
	    $user = &JFactory::getUser();
    	$data = JRequest::get('post');
		$data['post_text'] = JRequest::getVar('post_text', '', 'post', 'string', JREQUEST_ALLOWRAW);
    	$session = JFactory::getSession();
	    $session->set('postnotload', 1);

    	$session->set('post_subject', 	$data['post_subject']);
    	$session->set('post_text', 		$data['post_text']);
        $session->set('username', 		$data['username']);
        $session->set('topic_emoticon', $data['topic_emoticon']);
        $session->set('modified_reason',$data['modified_reason']);
        
    	if(($ccbConfig->showcaptcha == 1 && $user->guest) || ($ccbConfig->showcaptcha == 2)) {
    	    if( $data['captcha'] <> $session->get('ccbkey') ) {
	            $mainframe->enqueueMessage( JText::_('INVALID_SECURITY_CODE'), 'error');
	    		parent::display();
                return false;
		    }
    	}

    	$forum_id 	= JRequest::getVar('forum_id');
    	$topic_id 	= JRequest::getVar('topic_id');
    	$post_id 	= JRequest::getVar('id');
    	$mode 		= JRequest::getVar('mode');
    	$files 		= JRequest::get('files');
    	$token		= JUtility::getToken();
        $model 		= $this->getModel('post');
        $msgtype    = 'message';
        
    	if(!JRequest::getInt($token, 0, 'ccbpost')) {
            $message = JText::_('REQUEST_FORBIDDEN');
            $msgtype = 'error';
		} else if (!$model->store($data )) {
            $message = $model->getError();
            $msgtype = 'error';
        } else {
            $message = JText::_('YOUR_POST_HAS_BEEN_SUBMITTED');
            $msgtype = 'message';
	    	$session->set('postnotload', 0);
	    	$session->set('attachments', array());
        }

        if ($msgtype == 'error') {
            if (!$post_id) {
                JRequest::setVar('view', 'topiclist');
            } else {
                JRequest::setVar('view', 'postlist');
            }
            $mainframe->enqueueMessage($model->getError(), $msgtype);
            parent::display();
            return;
        }

        if( $mode != 'post' ) {
        	$link = JRoute::_('index.php?option=com_ccboard&view=postlist&forum='.$forum_id.'&topic='. $topic_id . '&Itemid=' . $ccbConfig->itemid, false);
        } else {
        	$link = JRoute::_('index.php?option=com_ccboard&view=topiclist&forum='.$forum_id . '&Itemid=' . $ccbConfig->itemid, false);
        }

	    $this->setRedirect($link,$message, $msgtype );

    }

    function quickReply($tmpl = null)
    {
    	global $ccbConfig;
    	$mainframe = &JFactory::getApplication();
		$uri = &JFactory::getURI();
		$link = $uri->toString();
	    $user = &JFactory::getUser();
    	$data = JRequest::get('post');
		$data['post_text'] = JRequest::getVar('post_text', '', 'post', 'string', JREQUEST_ALLOWRAW);
    	$session = JFactory::getSession();

    	if(($ccbConfig->showcaptcha == 1 && $user->guest) || ($ccbConfig->showcaptcha == 2)) {
    	    if( $data['captcha'] <> $session->get('ccbkey') ) {
	            $mainframe->enqueueMessage( JText::_('INVALID_SECURITY_CODE'), 'error');
	    		parent::display();
                return false;
		    }
    	}

    	$token		= JUtility::getToken();
        $model 		= $this->getModel('post');
        $msgtype    = 'message';

    	if(!JRequest::getInt($token, 0, 'ccbpost')) {
            $message = JText::_('REQUEST_FORBIDDEN');
            $msgtype = 'error';
		} else if (!$model->store($data )) {
            $message = $model->getError();
            $msgtype = 'error';
        } else {
            $message = JText::_('YOUR_POST_HAS_BEEN_SUBMITTED');
            $msgtype = 'message';
        }

        $mainframe->enqueueMessage($model->getError(), $msgtype);
	    $this->setRedirect($link,$message, $msgtype );
    }

    function cancelPost()
    {
    	global $ccbConfig;
    	$mode = JRequest::getVar('mode');
    	$forum_id = JRequest::getVar('forum_id');
    	$topic_id = JRequest::getVar('topic_id');
    	$session = JFactory::getSession();
	    $session->set('postnotload', 0);
    	$session->set('attachments', array());
    	if( $mode != 'post' ) {
        	$link = JRoute::_('index.php?option=com_ccboard&view=postlist&forum='.$forum_id.'&topic='. $topic_id . '&Itemid=' . $ccbConfig->itemid, false);
        } else {
        	$link = JRoute::_('index.php?option=com_ccboard&view=topiclist&forum='.$forum_id . '&Itemid=' . $ccbConfig->itemid, false);
        }
        $message = JText::_('OPERATION_CANCELLED');
        $msgtype = 'message';
        $this->setRedirect($link, $message, $msgtype);
    }

    function emailsub()
    {
    	global $ccbConfig;
    	$forum_id = JRequest::getVar('forum',0);
		$topic_id = JRequest::getVar('topic',0);
    	$model = $this->getModel('postlist');
        $msgtype = 'message';
        
        if ($model->emailSubscription($forum_id, $topic_id)) {
            $message = JText::_('THE_TOPIC_HAS_BEEN_SUB');
        } else {
            $message = $model->getError();
            $msgtype = 'error';
        }

        $link = JRoute::_('index.php?option=com_ccboard&view=postlist&forum='.$forum_id .'&topic=' . $topic_id . '&Itemid=' . $ccbConfig->itemid, false);
        $this->setRedirect($link, $message, $msgtype);
    }

  	function deletePost()
    {
    	global $ccbConfig;

    	$forum_id = JRequest::getVar('forum',0);
        $topic_id = JRequest::getVar('topic',0);
    	$post_id = JRequest::getVar('post',0);
    	$user = &JFactory::getUser();
    	$message = JText::_('INVALID_OPERATION');
        $msgtype = 'message';

    	$link = JRoute::_('index.php?option=com_ccboard&view=postlist&forum='.$forum_id .'&topic=' . $topic_id . '&Itemid=' . $ccbConfig->itemid, false);

    	if($user->guest) {
			$message = JText::_('ACCESS_RESTRICTED_FOR_THIS_OPERATION');
		}
    	else {
	    	$filter = new JFilterInput(array(), array(), 1, 1);
	    	$post_id = $filter->clean($post_id,'integer');
	    	$topic_id = $filter->clean($topic_id,'integer');
	    	$model = $this->getModel('postlist');
	        if ($model->delete($post_id)) {
	            $message = JText::_('THE_POST_HAS_BEEN_DELETED');
		        $model->setTopicId($topic_id);
		        if( $model->getTotal() < 1 ) {
					$link = JRoute::_('index.php?option=com_ccboard&view=topiclist&forum='.$forum_id . '&Itemid=' . $ccbConfig->itemid, false);
		        }
	        }
	        else {
	            $message = $model->getError();
                $msgtype = 'error';
	        }
    	}
    	$this->setRedirect($link, $message, $msgtype);
    }

	function saveProfile()
	{
		global $ccbConfig;

        $data = JRequest::get('post');
        $model = $this->getModel('myprofile');
		$user = &JFactory::getUser();
        $msgtype = 'message';
        
		JRequest::checkToken( 'request' ) or jexit( 'Invalid Token' );

		$message = '';
		$link = JRoute::_('index.php?option=com_ccboard&view=forumlist&Itemid=' . $ccbConfig->itemid, false);

		if($user->guest) {
			$message = JText::_('ACCESS_RESTRICTED_TO_THIS_SECTION');
		}
		else {
	        if ($model->store($data)) {
	            $message = JText::_('PROFILE_SAVED_SUCCESSFULLY');
	        } else {
	            $message = $model->getError();
                $msgtype = 'error';
	            $link = JRoute::_('index.php?option=com_ccboard&view=myprofile&cid='. $user->get('id') . '&Itemid=' . $ccbConfig->itemid, false);
	        }
		}
		$this->setRedirect($link, $message, $msgtype);
	}

	function uploadAvatar()
    {
    	jimport('joomla.filesystem.file');
    	global $ccbConfig;
    	global $mainframe;

    	$file = JRequest::getVar( 'Filedata', '', 'files', 'array' );
		$data = JRequest::get('post');
        $message = JText::_('OPERATION_CANCELLED');
        $msgtype = 'message';
        // Check for request forgeries
		JRequest::checkToken( 'request' ) or jexit( 'Invalid Token' );

		if( $ccbConfig->avatarupload < 1 ) {
			$mainframe->enqueueMessage(JText::_('AVATAR_UPLOAD_NOT_ALLOWED'), 'error');
			parent::display();
            return false;
		}

		if(empty($file['name'])) {
			$mainframe->enqueueMessage(JText::_('EMPTY_FILE'), 'error');
			parent::display();
            return false;
		}

		if ($file['name'] !== JFile::makesafe($file['name'])) {
			$mainframe->enqueueMessage(JText::_('INVALID_FILE_NAME'), 'error');
			parent::display();
            return false;
		}
		// Make the filename safe
		$file['name']	= JFile::makeSafe($file['name']);

		$format = strtolower(JFile::getExt($file['name']));
		//$allowable = explode( ',', $ccbConfig->extensions);
		$allowable = array('gif','jpg','jpeg','jpe','bmp','png','tiff');
		if (!in_array($format, $allowable)) {
			$mainframe->enqueueMessage(JText::_('THIS_FILE_TYPE_NOT_ALLOWED'), 'error');
			parent::display();
            return false;
		}

		$maxSize = ((int) $ccbConfig->avataruploadsize) * 1024;
		if ($maxSize > 0 && (int) $file['size'] > $maxSize) {
			$mainframe->enqueueMessage(JText::_('FILE_SIZE_IS_TOO_LARGE'), 'error');
			parent::display();
            return false;
		}

		// Set FTP credentials, if given
		jimport('joomla.client.helper');
		JClientHelper::setCredentialsFromRequest('ftp');

		$folder	= JPATH_COMPONENT_SITE . DS . 'assets' .DS. 'avatar' .DS. 'personal';
		$upfile = $data['user_id'] . '_avatar.'. $format;
		$filepath = JPath::clean($folder.DS.strtolower($upfile));
/*      // Use unique file name for each user. Each file per user, over writes each time
        if( JFile::exists($filepath) ) {
			$mainframe->enqueueMessage(JText::_('FILE_ALREADY_EXISTS'), 'error');
			parent::display();
            return false;
*/
		if (!JFile::upload($file['tmp_name'], $filepath)) {
			$mainframe->enqueueMessage(JText::_('UNABLE_TO_UPLOAD_FILE'), 'error');
			parent::display();
            return false;
		}

        $db = &JFactory::getDBO();
        $query = 'UPDATE #__ccb_users ' .
            'set avatar = "personal/' . $upfile . '", thumb = "personal/' . $upfile . '" ' .
            'WHERE user_id=' . $data['user_id'];
		$db->setQuery( $query );
		if( !$db->query() ) {
			$mainframe->enqueueMessage($db->getError(),'error');
			parent::display();
			return false;
		}

		$mainframe->enqueueMessage(JText::_('AVATAR_SUCCESSFULLY_UPLOADED'), $msgtype);
        parent::display();
        return;
    }

	function cancelProfile()
    {
    	global $ccbConfig;
	    $link = JRoute::_('index.php?option=com_ccboard&view=forumlist&Itemid=' . $ccbConfig->itemid, false);
        $this->setRedirect($link, JText::_('OPERATION_CANCELLED'), 'message' );
    }

    function uploadAttachment()
    {
    	jimport('joomla.filesystem.file');
    	global $ccbConfig;
    	$mainframe = &JFactory::getApplication();
		$file = JRequest::getVar( 'Filedata', '', 'files', 'array' );
		$uri = &JFactory::getURI();
		$link = $uri->toString();
		$data = JRequest::get('post');
        $data['post_text'] = JRequest::getVar('post_text', '', 'post', 'string', JREQUEST_ALLOWRAW);
        // Check for request forgeries
		JRequest::checkToken( 'request' ) or jexit( 'Invalid Token' );

        $session = JFactory::getSession();
    	$filear = $session->get('attachments');
    	if(!is_array( $filear )) {
    		$filear = array();
    	}
	    $session->set('postnotload', 1);
    	$session->set('post_subject', 	$data['post_subject']);
    	$session->set('post_text', 		$data['post_text']);
        $session->set('username', 		$data['username']);
        $session->set('topic_emoticon', $data['topic_emoticon']);
        $session->set('modified_reason',$data['modified_reason']);
  		$session->set('attachments', 	$filear );
        
		if( $ccbConfig->attachments < 1 ) {
			$mainframe->enqueueMessage(JText::_('FILE_UPLOAD_NOT_ALLOWED'), 'error');
			parent::display();
            return false;
		}

		if(empty($file['name'])) {
			$mainframe->enqueueMessage(JText::_('EMPTY_FILE'), 'error');
			parent::display();
            return false;
		}

		if ($file['name'] !== JFile::makesafe($file['name'])) {
			$mainframe->enqueueMessage(JText::_('INVALID_FILE_NAME'), 'error');
			parent::display();
            return false;
		}
		// Make the filename safe
		$file['name']	= JFile::makeSafe($file['name']);

		$format = strtolower(JFile::getExt($file['name']));
		$allowable = explode( ',', $ccbConfig->extensions);
		if (!in_array($format, $allowable)) {
			$mainframe->enqueueMessage(JText::_('THIS_FILE_TYPE_NOT_ALLOWED'), 'error');
			parent::display();
            return false;
		}

		$maxSize = (int) $ccbConfig->fileuploadsize ;
		$maxSize =  $maxSize * 1024 ;
		if ($maxSize > 0 && (int) $file['size'] > $maxSize) {
			$mainframe->enqueueMessage(JText::_('FILE_SIZE_IS_TOO_LARGE'), 'error');
			parent::display();
            return false;
		}

		// Set FTP credentials, if given
		jimport('joomla.client.helper');
		JClientHelper::setCredentialsFromRequest('ftp');

		$folder	= JPATH_COMPONENT_SITE . DS . 'assets' .DS. 'uploads';
		$user = &JFactory::getUser();
		$upfile = $user->get('id') . '_' . md5(microtime() * mktime()) . '.' . $format ;
		$filepath = JPath::clean($folder.DS.strtolower($upfile));
		while( JFile::exists($filepath) ) {
			$upfile = $user->get('id') . '_' . md5(microtime() * mktime());
			$filepath = JPath::clean($folder.DS.strtolower($upfile));
		}

		if (!JFile::upload($file['tmp_name'], $filepath)) {
			$mainframe->enqueueMessage(JText::_('UNABLE_TO_UPLOAD_FILE'), 'error');
			parent::display();
            return false;
		}

    	$elemAttach = array($upfile, $file['name'], $format, (int) $file['size'], time(), JFilterInput::clean($data['attachmentcomment'],'string'));
  		$filear[] = $elemAttach;
  		$session->set('attachments', 	$filear );

        parent::display();
        return;
    }

    function previewpost()
    {
        $session = JFactory::getSession();
        $data = JRequest::get('post');
	    $session->set('postnotload', 1);
    	$session->set('post_subject', 	$data['post_subject']);
    	$session->set('post_text', 		$data['post_text']);

        parent::display();
        return;
    }

    function deleteAttachment()
    {
    	$data = JRequest::get('post');
        $data['post_text'] = JRequest::getVar('post_text', '', 'post', 'string', JREQUEST_ALLOWRAW);
   		$session = JFactory::getSession();
    	$filear = $session->get('attachments');
    	if(is_array( $filear )) {
    		foreach($filear as $key => $value) {
    			if( $value[0] == $data['attachid'] ) {
    				jimport('joomla.filesystem.file');
    				$folder	= JPATH_COMPONENT_SITE . DS . 'assets' .DS. 'uploads';
    				$filepath = JPath::clean($folder.DS.strtolower($value[0]));
    				JFile::delete($filepath);
    				$db = &JFactory::getDBO();
					$db->setQuery('DELETE FROM #__ccb_attachments where ccb_name="' . $value[0] . '"');
					$db->query();
    				unset( $filear[$key]);
    				break;
    			}
    		}
    		$filear = array_values( $filear );
    		$session->set('postnotload', 1);
    		$session->set('post_subject', 	$data['post_subject']);
    		$session->set('post_text', 		$data['post_text']);
            $session->set('username', 		$data['username']);
            $session->set('topic_emoticon', $data['topic_emoticon']);
            $session->set('modified_reason',$data['modified_reason']);
            $session->set('attachments', 	$filear );
    	}
        parent::display();
        return;
    }

    function postReport()
    {
    	global $ccbConfig;
    	$data = JRequest::get('post');
    	$forum_id = JRequest::getVar('forum',0);
		$topic_id = JRequest::getVar('topic',0);
		$post_id = JRequest::getVar('post',0);
        $msgtype = 'message';
    	$model = $this->getModel('postlist');
        if ($model->reportAbuse($forum_id, $topic_id, $post_id)) {
            $message = JText::_('REPORT_SUBMITTED_SUCCESSFULLY');
        } else {
            $message = $model->getError();
            $msgtype ='error';
        }

    	$link = JRoute::_('index.php?option=com_ccboard&view=postlist&forum='.$forum_id .'&topic=' . $topic_id . '&Itemid=' . $ccbConfig->itemid , false);
        $this->setRedirect($link, $message, $msgtype);

    }

    function karmaUp()
    {
    	$this->updateKarma( 1 );
    }

    function karmaDown()
    {
    	$this->updateKarma( -1 );
    }

    function updateKarma( $karma )
    {
    	global $ccbConfig;
    	$forum_id 	= JRequest::getVar('forum');
    	$topic_id 	= JRequest::getVar('topic');
    	$post_id 	= JRequest::getVar('post');
        $model 		= $this->getModel('postlist');
        $user = &JFactory::getUser();
    	$post_user 	= JRequest::getVar('post_user');
        $message = JText::_('REQUEST_FORBIDDEN');
        $msgtype = 'error';
        $link = JRoute::_('index.php?option=com_ccboard&view=postlist&forum=' . $forum_id . '&topic=' . $topic_id . '&Itemid=' . $ccbConfig->itemid);

        if( !$user->guest && $post_user > 0 ) {
	        $message = ccboardHelperConfig::updateKarma( $user->get('id'), $post_user, $karma );
            $msgtype = 'message';
        }

        $this->setRedirect($link,$message, $msgtype);
    }

    function postLock()
    {
    	$this->updatePostLock(1);
    }

    function postUnlock()
    {
    	$this->updatePostLock(0);
    }

    function updatePostLock( $lock )
    {
    	global $ccbConfig;
        $msgtype = 'message';
    	$forum_id = JRequest::getVar('forum',0);
		$topic_id = JRequest::getVar('topic',0);
    	$model = $this->getModel('postlist');
        if ($model->postLock($forum_id, $topic_id, $lock )) {
            $message = JText::_('TOPIC_LOCKED_UNLOCKED');
        } else {
            $message = $model->getError();
            $msgtype = 'error';
        }

    	$link = JRoute::_('index.php?option=com_ccboard&view=postlist&forum='.$forum_id .'&topic=' . $topic_id . '&Itemid=' . $ccbConfig->itemid , false);
        $this->setRedirect($link, $message, $msgtype);
    }

    function favourite()
    {
    	global $ccbConfig;
    	$forum_id = JRequest::getVar('forum',0);
		$topic_id = JRequest::getVar('topic',0);
    	$model = $this->getModel('postlist');
        $msgtype = 'message';
        if ($model->favourite($forum_id, $topic_id)) {
            $message = JText::_('TOPIC_HAS_BEEN_FAVOURITE');
        } else {
            $message = $model->getError();
            $msgtype = 'error';
        }
        $link = JRoute::_('index.php?option=com_ccboard&view=postlist&forum='.$forum_id .'&topic=' . $topic_id . '&Itemid=' . $ccbConfig->itemid, false);
        $this->setRedirect($link, $message, $msgtype);
    }

    function postSplit()
    {
    	global $ccbConfig;
    	$forum_id = JRequest::getVar('forum',0);
		$topic_id = JRequest::getVar('topic',0);
		$post_id = JRequest::getVar('post',0);
        $msgtype = 'message';
    	$model = $this->getModel('postlist');
        if ($model->split($forum_id, $topic_id, $post_id)) {
            $message = JText::_('TOPIC_HAS_BEEN_SPLITTED');
        } else {
            $message = $model->getError();
            $msgtype = 'error';
        }
        $link = JRoute::_('index.php?option=com_ccboard&view=postlist&forum='.$forum_id .'&topic=' . $topic_id . '&Itemid=' . $ccbConfig->itemid, false);
        $this->setRedirect($link, $message, $msgtype);
    }

    function postMove()
    {
    	global $ccbConfig;
    	$forum_id = JRequest::getVar('forum',0);
		$topic_id = JRequest::getVar('topic',0);
		$fnew = JRequest::getVar('fmov_id',0);
        $msgtype = 'message';

		JRequest::checkToken( 'request' ) or jexit( 'Invalid Token' );

		$model = $this->getModel('postlist');
        if ($model->move($forum_id, $topic_id, $fnew)) {
            $message = JText::_('TOPIC_HAS_BEEN_MOVED');
        } else {
            $message = $model->getError();
            $msgtype = 'error';
        }
        $link = JRoute::_('index.php?option=com_ccboard&view=topiclist&forum='.$forum_id .'&Itemid=' . $ccbConfig->itemid, false);
        $this->setRedirect($link, $message, $msgtype);
    }

    function postApprove()
    {
    	global $ccbConfig;
    	$post = JRequest::getVar('post',0);
    	$model = $this->getModel('approval');
        $msgtype = 'message';
        if ($model->approve($post)) {
            $message = JText::_('TOPIC_HAS_BEEN_APPROVED');
        } else {
            $message = $model->getError();
            $msgtype = 'error';
        }
    	$link = 'index.php?option=com_ccboard&view=approval&Itemid=' . $ccbConfig->itemid;
    	$this->setRedirect($link, $message, $msgtype);
    }

    function delMyPosts()
    {
    	global $ccbConfig;
        $msgtype = 'message';
    	$topic = JRequest::getVar('topic',0);
		$forum = JRequest::getVar('forum',0);
    	$viewmode = JRequest::getVar('viewmode','');
    	$model = $this->getModel('postlist');

    	if( $viewmode == 'mysubs') {
	        if ($model->emailSubscription($forum, $topic)) {
	            $message = JText::_('THE_TOPIC_HAS_BEEN_SUB');
	        } else {
	            $message = $model->getError();
                $msgtype = 'error';
	        }
    	} elseif( $viewmode == 'myfavours') {
	        if ($model->favourite($forum, $topic)) {
	            $message = JText::_('TOPIC_HAS_BEEN_FAVOURITE');
	        } else {
	            $message = $model->getError();
                $msgtype = 'error';
	        }
    	}

    	$link = JRoute::_('index.php?option=com_ccboard&view=mylist&viewmode='. $viewmode .'&Itemid=' . $ccbConfig->itemid);
    	$this->setRedirect($link, $message, $msgtype);
    }

}
?>
