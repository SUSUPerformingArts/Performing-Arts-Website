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

class ccboardViewPost extends JView
{
    function display( $tmpl = null )
    {
    	global $ccbConfig;
        $mainframe = &JFactory::getApplication();
        $uri =& JFactory::getURI();

        $model = &$this->getModel('post');
        $user = &JFactory::getUser();
        $post_id = JRequest::getVar('post');
        $topic_id = JRequest::getVar('topic');
		$forum_id = JRequest::getVar('forum');
        $mode = JRequest::getVar('mode');
        $preview = JRequest::getVar('task');

        $theme = ccboardHelperConfig::getTheme();
        $model->setPostId($post_id, $topic_id, $forum_id);

        if( $mode == 'edit' && $model->isEditingAllowed() < 1 ) {
			die( JText::_('UN_AUTHORIZED_PROCESS') );
        }
        elseif( $model->isPostingAllowed() < 1 ) {
			die(JText::_('UN_AUTHORIZED_PROCESS'));
        }

        $item = $model->getData();
        $modeActions = $model->isModerator();
        $startPost = $model->getStartingPost();
        $editor = & JFactory::getEditor();

		$post_subject='';
		$post_text='';
        $modified_reason='';
		$username='';
		$page_title='';
		$topictype = 0;
		$topic_emoticon = 0;
		$attachments = array();
		$guest = $user->guest ? 1 : 0;

        $uploadPermission = false;
        if( $guest == 1 && $ccbConfig->attachments == 1 ) $uploadPermission = true;
        if( $guest == 0 && $ccbConfig->attachments >= 1 && $ccbConfig->attachments <= 2 ) $uploadPermission = true;
        if( $modeActions && $ccbConfig->attachments == 3 ) $uploadPermission = true;


		if( $mode == 'reply') {
			$post_subject = JText::_('POST_REPLY_PREFIX') . $this->escape($item->topic_subject);
			$page_title = JText::_('POST_A_REPLY') . $this->escape($item->topic_subject);
			$post_id  = 0;
		} else if( $mode == 'quote') {
			$qname='';
			if( $item->post_user > 0 ) {
				 $quser = ccboardHelperConfig::getUserProfile( $item->post_user);
				 $qname = $quser['username'];
			} else {
				$qname = $item->post_username;
			}
			$qdate = JHTML::_('date', $item->post_time + $ccbConfig->timeoffset, $ccbConfig->dateformat);
			$post_subject = JText::_('POST_REPLY_PREFIX') . $this->escape($item->post_subject);
			if( $ccbConfig->ccbeditor == 'ccboard') {
				$post_text = '[quote name="'. $qname .'" date="' . $qdate .'"] '.  htmlspecialchars($item->post_text, ENT_QUOTES,'UTF-8') . ' [/quote]';
			} else {
				$post_text = '<br/><br/><br/><br/><hr/><div class="bbcode_quote"><br/><div class="bbcode_quote_head">['. $qname .' ' . $qdate .']:</div><br/><div class="bbcode_quote_body">'.  htmlspecialchars($item->post_text, ENT_QUOTES,'UTF-8') . '</div><br/></div>';
			}
			$page_title = JText::_('POST_REPLY_WITH_QUOTE') . $item->topic_subject;
			$post_id = 0;
		} else if( $mode == 'edit') {
			$post_subject = $this->escape($item->post_subject);
			$post_text = $this->escape($item->post_text);
			$page_title = JText::_('EDIT_POST') . $this->escape($item->topic_subject);
			$post_id = (int)$item->id;
			$topictype = $item->topic_type;
			$topic_emoticon = $item->topic_emoticon;
			$username = $item->post_username;
		} else if( $mode == 'post') {
			$post_subject = '';
			$post_text = '';
			$page_title = JText::_('NEW_TOPIC');
			$post_id = 0;
    	}

    	$labels = array();
    	$labels['invalidsub'] = JText::_( 'POST_SUBJECT_CAN_NOT_BE_BLANK');
    	$labels['invalidtext'] = JText::_( 'POST_CAN_NOT_BE_BLANK');
    	$labels['invalidusername'] = JText::_( 'INVALID_USERNAME');
    	$labels['username'] = JText::_('USERNAME');
    	$labels['subject'] = JText::_( 'SUBJECT');
    	$labels['preview'] = JText::_( 'PREVIEW');
    	$labels['save'] = JText::_( 'SAVE');
    	$labels['cancel'] = JText::_( 'CANCEL');
    	$labels['topictype'] = JText::_( 'POST_TYPE');
    	$labels['topicicon'] = JText::_( 'TOPIC_ICON');
    	$labels['captcha'] = JText::_( 'SECURITY');
    	$labels['attachments'] = JText::_('ATTACHMENTS');
    	$labels['attachmentcomment'] = JText::_('ATTACHMENT_COMMENT');
    	$labels['editreason'] = JText::_('EDIT_REASON');
        $labels['subscribe'] = JText::_('EMAIL_SUBSCRIBE');

    	$path = JURI::root();
        $tar =  array(
        	JHTML::_('select.option', 0, JText::_('STICKY_NORMAL') ),
        	JHTML::_('select.option', 1, JText::_('STICKY_SITE')),
        	JHTML::_('select.option', 2, JText::_('STICKY_FORUM')),
        	JHTML::_('select.option', 3, JText::_('STICKY_GLOBAL')) );
      	$topiccombo = JHTMLSelect::genericlist($tar, 'topic_type','style="width: 200px; border:solid 1px gray;"','value','text', $topictype);
        $autosub = JHTMLSelect::booleanlist('autosub', '', $ccbConfig->autosub, JText::_('Yes'), JText::_('No'),'autosub');
        $cap_path = '';
		if( ($ccbConfig->showcaptcha == 1 && $user->guest) || ($ccbConfig->showcaptcha == 2)) {
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

		// ----------- Retain the old value, page is reload after validation error
		$session = JFactory::getSession();
		if( $session->get('postnotload') > 0 ) {
    		$post_text = $session->get('post_text');
    		$post_subject = $session->get('post_subject');
            $username = $session->get('username');
            $topic_emoticon = $session->get('topic_emoticon');
            $modified_reason = $session->get('modified_reason');
            $attachments = $session->get('attachments');
     		if( !is_array($attachments)) $attachments = array();
    	} elseif ( $mode == 'edit') {
    		$attachments = $model->getAttachments();
    		$session->set('attachments', 	$attachments );
    	}

    	$this->assignRef('forum_id', $forum_id);
		$this->assignRef('topic_id', $item->topic_id);
    	$this->assignRef('id', $post_id);
    	$this->assignRef('username', $username);
        $this->assignRef('modified_reason', $modified_reason);
    	$this->assignRef('guest', $guest);
    	$this->assignRef('topictype', $topictype);
        $this->assignRef('autosub', $autosub);
        $this->assignRef('startPost', $startPost);
    	$this->assignRef('topic_emoticon', $topic_emoticon);
    	$this->assignRef('topiccombo', $topiccombo);
		$this->assignRef('modeactions', $modeActions);
    	$this->assignRef('post_subject',  $post_subject);
		$this->assignRef('post_text', $post_text);
		$this->assignRef('page_title', $page_title);
		$this->assignRef('mode', $mode);
		$this->assignRef('theme', $theme);
		$this->assignRef('path', $path);
		$this->assignRef('labels', $labels);
		$this->assignRef('editor',	$editor);
		$this->assignRef('cap_path', $cap_path);
		$this->assignRef('attachments', $attachments);
        $this->assignRef('uploadPermission', $uploadPermission);
		$this->assignRef('action', $uri->toString());
        $this->assignRef('preview', $preview);

		$document = &JFactory::getDocument();
		$document->setTitle( $page_title );
		$document->setDescription( $page_title );

		ccboardHelperConfig::setBreadCrumb('ccbhome');
		ccboardHelperConfig::setBreadCrumb($model->getForumName($forum_id), JRoute::_('index.php?option=com_ccboard&view=topiclist&forum='.$forum_id.'&Itemid=' . $ccbConfig->itemid ));
		if($mode <> 'post') {
	        ccboardHelperConfig::setBreadCrumb($item->topic_subject, JRoute::_('index.php?option=com_ccboard&view=postlist&forum=' . $forum_id . '&topic=' . $topic_id . '&Itemid=' .  $ccbConfig->itemid ));
		}
	    ccboardHelperConfig::setBreadCrumb($page_title);

        parent::display($tmpl);
    }
}
?>
