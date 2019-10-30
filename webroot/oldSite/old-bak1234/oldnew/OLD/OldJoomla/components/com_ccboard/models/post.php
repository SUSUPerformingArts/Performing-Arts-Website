<?php
/**
 * @version		$Id: post.php 173 2009-09-21 14:43:37Z thomasv $
 * @Project		ccBoard - Joomla! Bulletin Board Extension/Component
 * @author 		Thomas Varghese
 * @package		ccBoard
 * @copyright	Copyright (C) 2008-2009 codeclassic.org. All rights reserved.
 * @license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

defined( '_JEXEC' ) or die( 'Restricted Access' );
jimport( 'joomla.application.component.model' );

class ccboardModelPost extends JModel
{
	var $_data;
	var $_id;
	var $_topic_id;
	var $_forum_id;

	var $_locked;
    var $_review;
    var $_moderated;
    var $_post_for;
    var $_post_email;
    var $_post_user;
    var $_post_time;
    var $_hold;
    var $_published;
    var $_post_favourite;
    var $_start_post_id;

	function ccboardModelPost()
	{
		$this->_id=0;
		$this->_topic_id=0;
		parent::__construct();
	}

	function setPostId($id, $topicid, $forumid)
	{
		$this->_id = $id;
		$this->_topic_id = $topicid;
		$this->_forum_id = $forumid;
		$this->_data = null;

		$this->_locked = null;
        $this->_review = null;
        $this->_moderated = null;
        $this->_post_for = null;
        $this->_post_email = null;
    	$this->_post_user = null;
    	$this->_post_time = null;
    	$this->_hold = null;
    	$this->_published = null;
        $this->_post_favourite = null;
        $this->_start_post_id = null;

        $this->_checkPermission();
	}


    function _checkPermission()
    {
    	$user = &JFactory::getUser();
		$gid = $user->get('gid');

		if( $this->_id > 0 ) {
			$query = 'SELECT p.id AS pid, p.post_user, p.post_time, p.hold, p.forum_id, p.topic_id ' .
						'FROM #__ccb_posts AS p ' .
						'WHERE p.id = ' . $this->_id;
			$this->_db->setQuery($query);
	        $perm = $this->_db->loadObject();
			if( isset($perm->pid) ) {
		    	$this->_post_user = $perm->post_user;
		    	$this->_post_time = $perm->post_time;
		    	$this->_hold = $perm->hold;
		    	$this->_forum_id = $perm->forum_id;
		    	$this->_topic_id = $perm->topic_id;
			}
		}

		if( $this->_topic_id > 0 ) {
			$query = 'SELECT t.id, t.forum_id, t.topic_email, t.topic_favourite, t.locked, t.hold, t.start_post_id ' .
						'FROM #__ccb_topics AS t ' .
						'WHERE t.id = ' . $this->_topic_id;
			$this->_db->setQuery($query);
	        $perm = $this->_db->loadObject();
			if( isset($perm->id) ) {
		    	$this->_hold = (isset($this->_hold)?$this->_hold:0) +  $perm->hold;
		    	$this->_forum_id = $perm->forum_id;
   				$this->_post_email = $perm->topic_email;
				$this->_post_favourite = $perm->topic_favourite;
				$this->_start_post_id = $perm->start_post_id;
	        	$this->_locked =  $perm->locked;
			}
		}

		if( $this->_forum_id > 0 ) {
			$query = 'SELECT f.id, f.locked, f.forum_name, f.post_for, f.moderated, f.review, f.published ' .
						'FROM #__ccb_forums AS f ' .
						'WHERE f.id =' . $this->_forum_id;
			$this->_db->setQuery($query);
	        $perm = $this->_db->loadObject();
			if( isset($perm->id) ) {
        		$this->_forum_name = $perm->forum_name;
	        	$this->_review = $perm->review;
	        	$this->_post_for = $perm->post_for;
	        	$this->_moderated = $perm->moderated;
	        	$this->_published = $perm->published;
	        	$this->_locked = (isset($this->_locked)?$this->_locked:0) + $perm->locked;
			}
		}

		return 1;
    }

    function isModerator()
    {
    	$user = &JFactory::getUser();
    	if($user->guest) return 0;
        $query = 'SELECT count(m.id) ' .
                  	'FROM #__ccb_moderators AS m ' .
        			'WHERE m.forum_id = ' . (int)$this->_forum_id .
        			' AND m.user_id = ' . $user->get('id');
        $this->_db->setQuery($query);
		$count = ($count = $this->_db->loadResult())?$count:0;
        if( ($count > 0 && $this->isForumModerated()) || ($user->get('gid') >= $this->getAdministrator()) ) {
        	return 1;
        }
        return 0;
    }

    function isForumModerated()
    {
    	$this->_moderated = isset($this->_moderated) ? $this->_moderated: 0 ;
        return $this->_moderated;
    }

    function isPostingAllowed()
    {
    	$this->_post_for = isset($this->_post_for) ? $this->_post_for: 18 ;   // Guest=18
       	$user = &JFactory::getUser();
		$gid = $user->get('gid');

		if( $this->_published < 1) { return 0; }
		if( $this->isModerator() > 0 ) { return 1; }
		if( $this->isHold() > 0 ) { return 0; }
		if( $this->isLocked() > 0 ) { return 0; }
		if( $this->_post_for <= $gid) {
			return 1;
		}
		return 0;
    }

    function isLocked()
    {
    	$this->_locked = isset($this->_locked) ? $this->_locked : 1 ;
        return $this->_locked;
    }

    function isHold()
    {
    	$this->_hold = isset($this->_hold) ? $this->_hold: 0 ;
        return $this->_hold;
    }

    function isEditingAllowed()
    {
    	global $ccbConfig;
    	$user = &JFactory::getUser();

    	if( $this->_published < 1) { return 0;	}
    	if( $this->_id < 1 ) { return 0; }
    	if( $this->isModerator() > 0 ) { return 1; }
    	if( $ccbConfig->allowedit < 1 ) { return 0; }
    	if( $this->isHold() > 0 ) { return 0; }
		if( $this->isLocked() > 0) { return 0; }
    	if( $this->_post_user <> $user->get('id')) { return 0; }

		$canedit = false;
		$tdiff = time() - $this->_post_time;
		$locktime = $ccbConfig->editgracetime > 0 ? $ccbConfig->editgracetime  : $tdiff + 1;
		if( $tdiff > $locktime ) { return 0; }

		// Guest Can edit
		if( $ccbConfig->allowedit == 1 ) { return 1; }

    	// check registered users can edit
    	if( $ccbConfig->allowedit == 2 && !$user->guest ) return 1;

    	return 0;

    }

    function getAdministrator()
    {
    	$acl = &JFactory::getACL();
    	return $acl->get_group_id(null, 'Administrator');
    }

	function getData()
	{
		if (empty($this->_data)) {
			$query = 'SELECT f.id AS forum_id, f.locked, f.post_for, f.published, ' .
        		't.id AS topic_id,  t.post_subject as topic_subject, t.topic_type, t.topic_emoticon, ' .
				'p.id, p.post_subject, p.post_text, p.post_user, p.post_username, p.post_time ' .
        		'FROM #__ccb_forums AS f ' .
		        'LEFT JOIN #__ccb_topics AS t ON t.forum_id = f.id  AND t.id = ' . (int)$this->_topic_id . ' ' .
        		'LEFT JOIN #__ccb_posts AS p ON p.topic_id = t.id AND p.id =' .(int)$this->_id . ' ' .
            	'WHERE f.id = '. (int)$this->_forum_id ;
			$query .=  ' order by p.id LIMIT 1';
			$this->_db->setQuery($query);
			$this->_data = $this->_db->loadObject();
		}

		return $this->_data;
	}


	function filterBadWords( $txt='' )
	{
		global $ccbConfig;
		$badwords = trim( $ccbConfig->badwords );
		if( strlen($badwords) > 0) {
			$badwords = explode(',',$badwords);
			for($i=0;$i < sizeof($badwords);$i++) {
				$badword = trim($badwords[$i]);
				if( strlen($badword) > 0) {
					// $txt = eregi_replace($badword, substr($badword,0,1).sprintf("%'".'*'.(strlen($badword)-1)."s", NULL), $txt);
                    $txt = preg_replace("/\b".$badword."\b/", substr($badword,0,1).sprintf("%'".'*'.(strlen($badword)-1)."s", NULL), $txt);
				}
			}
		}
		return $txt;
	}

	function cleanUP( $data )
	{
		$filter = new JFilterInput(array(), array(), 1, 1);
		$data['mode']			= $filter->clean( $data['mode'], 'string');
		$data['forum_id']		= $filter->clean( $data['forum_id'], 'integer');
		$data['topic_id']		= $filter->clean( $data['topic_id'], 'integer');
		$data['topic_type']		= $filter->clean( $data['topic_type'], 'integer');
        $data['autosub']		= $filter->clean( $data['autosub'], 'integer');
		$data['topic_emoticon']	= $filter->clean( $data['topic_emoticon'], 'integer');
		$data['id']				= $filter->clean( $data['id'], 'integer');
        $data['post_subject']	= trim($filter->clean( $data['post_subject'], 'string'));
		$data['username']		= trim($filter->clean( $data['username'], 'string'));

		$data['post_subject'] = $this->filterBadWords($data['post_subject']);
		$data['post_text'] = trim($this->filterBadWords( $data['post_text'] ));

		return $data;
	}

	function check($data)
	{
        if( $data['post_subject'] == '' ) {
            $this->setError(JText::_('POST_SUBJECT_CAN_NOT_BE_BLANK'));
			return false;
        }
        if( $data['post_text'] == '' ) {
            $this->setError(JText::_('POST_CAN_NOT_BE_BLANK'));
			return false;
        }
    	return true;
	}

	function getForumName( $forum_id )
	{
		$query = 'SELECT Forum_Name FROM #__ccb_forums WHERE id = ' . (int)$forum_id;
		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	function store($data)
	{
		global $ccbConfig;
		$user = &JFactory::getUser();
		$session = JFactory::getSession();
		$data = $this->cleanUP($data);

		if( !$this->check($data)) {
			return false;
		}

		$post_user = $user->id;
		$post_time = time();
		$mode = $data['mode'];
		$post_text = $data['post_text'];


		$this->setPostId($data['id'],$data['topic_id'],$data['forum_id'] );
        if( $mode == 'edit' && $this->isEditingAllowed() < 1 ) {
        	$this->setError(JText::_('UN_AUTHORIZED_PROCESS'));
			return false;
        }
        elseif( $this->isPostingAllowed() < 1 ) {
        	$this->setError(JText::_('UN_AUTHORIZED_PROCESS'));
			return false;
        }
        $moderator = $this->isModerator();

		// ------------------------------------------------------ Check
		$frow = &JTable::getInstance('ccbforum', 'ccboardTable');
		if( !$frow->load( $data['forum_id']) ) {
			$this->setError(JText::_('FORUM_IS_MISSING'));
			$session->set('postnotload', 0);
			$session->set('attachments', array());
			return false;
		}

		// --------------------------------------------------> TOPIC
		$trow = &JTable::getInstance('ccbtopic', 'ccboardTable');
		if( !$trow->load( $data['topic_id']) ) {       // New Topic
			if( $mode <> 'post') {
				$this->setError('TOPIC_IS_MISSING');
				$session->set('postnotload', 0);
				$session->set('attachments', array());
				return false;
			}
			$trow->id = null;
			$trow->forum_id = $data['forum_id'];
			$trow->post_subject = $data['post_subject'];
			$trow->post_time = $post_time;
			$trow->post_user = $post_user;
			$trow->reply_count = 0;
			$trow->locked = 0;
			$trow->hits = 0;
			$trow->hold=0;
			$trow->topic_type = (int)$data['topic_type'];
			$trow->topic_emoticon = (int)$data['topic_emoticon'];
			$trow->post_username = $data['username'];
			$trow->topic_email = '';
			if (!$trow->store(true)) {
				$this->setError($trow->getError());
				$session->set('postnotload', 0);
				$session->set('attachments', array());
				return false;
			}
			$trow->load($trow->id);  // Reload
		}
		$sendemail = $trow->topic_email . '';
		// if( $moderator > 0 && $mode == 'edit' && $trow->topic_type > 0) {
        // Commented out for Promoting existing thread to Announcement / Viceversa
        if( $moderator > 0 ) {
			$trow->topic_type = (int)$data['topic_type'];
		}

		// --------------------------------------------------> POST
		$row = &JTable::getInstance('ccbpost', 'ccboardTable');
		if( !$row->load($data['id']) ) {
			if( $mode == 'edit') {
				$this->setError('POST_IS_MISSING');
				$session->set('postnotload', 0);
				$session->set('attachments', array());
				return false;
			}
			$row->id = null;
			$row->post_user = $post_user;
		}
		$row->topic_id = $trow->id;
		$row->forum_id = $trow->forum_id;
		$row->post_subject = $data['post_subject'];
		$row->post_text = $post_text;
		$row->post_username = $data['username'];

		if($mode == 'edit') {
			$row->modified_time = $post_time;
			$row->modified_by = $post_user;
			$row->modified_reason = $data['modified_reason'];
			if( $trow->start_post_id == $row->id ) {
				$trow->post_subject = $row->post_subject;
				$trow->topic_emoticon = (int)$data['topic_emoticon'];
			}
		} else {
            // Post Time is updated only at the time of creation
            $row->post_time = $post_time;
        }

		if( $moderator < 1 && $frow->review > 0 ) {
			$row->hold=1;
		} else {
			$row->hold=0;
		}

		if( $ccbConfig->logip > 0 ) {
			$row->ip =  $_SERVER['REMOTE_ADDR'];
		}

		if (!$row->store(true)) {
			$this->setError($row->getError());
			$session->set('postnotload', 0);
			$session->set('attachments', array());
			return false;
		}
		$row->load($row->id); // Reload

		// --------------------------------------------------> Attachments
    	$filear = $session->get('attachments');
    	if(is_array( $filear )) {
    		$this->_db->setQuery('DELETE FROM #__ccb_attachments where post_id=' . $row->id );
			$this->_db->query();
    		foreach($filear as $key => $value) {
    			$query = "INSERT INTO #__ccb_attachments " .
    				"( post_id, ccb_name, real_name, extension, filesize, filetime, comment  )  VALUES " .
    				" ( '" . $row->id  . "','" . $value[0] . "','" . $value[1] . "','" .
    					$value[2] . "','" . $value[3] . "','" . $value[4] . "','" . $value[5] . "')";
    			$this->_db->setQuery( $query );
				$this->_db->query();
    		}
    	}

    	if( $mode == 'post') {
			$trow->start_post_id = $row->id;
    	}

    	// Change Topic on Hold for New and Edit
		if( $trow->start_post_id == $row->id && $frow->review > 0 && $moderator < 1) {
			$trow->hold=1;
		}

		// UPDATE COUNTERS & OTHER LEADS
    	if( $row->hold < 1 ) {
			$trow->last_post_time = $post_time;
			$trow->last_post_id = $row->id;
			$trow->last_post_user = $post_user;
			$trow->last_post_username = $data['username'];

			$frow->last_post_time = $post_time;
			$frow->last_post_id = $row->id;
			$frow->last_post_user = $post_user;
			$frow->last_post_username = $data['username'];

			if( $mode == 'post') {
				$frow->topic_count += 1;
			} elseif ($mode <> 'edit') {
				$frow->post_count += 1;
				$trow->reply_count += 1;
			}
    	}

        // Auto Email Subscription
        if( $post_user > 0 && $ccbConfig->autosub > 0 ) {
            $uid = $post_user . '-';
            $pos = strpos( 'a'.$trow->topic_email, $uid );
            if( !$pos ) {
                $trow->topic_email .= $uid;
            } else {
                $trow->topic_email = str_replace($uid, '', $trow->topic_email);
            }
        }

    	// ----------------------------- SAVE TOPIC AND FORUM COUNTERS
		if (!$trow->store(true)) {
			$this->setError($trow->getError());
			$session->set('postnotload', 0);
			$session->set('attachments', array());
			return false;
		}

		if (!$frow->store(true)) {
			$this->setError($frow->getError());
			$session->set('postnotload', 0);
			$session->set('attachments', array());
			return false;
		}

		if ($mode <> 'edit' && $row->hold < 1) {
			ccboardHelperConfig::updateRank( 1, $post_user );
		}

		if( $row->hold < 1 ) {
			ccboardHelperConfig::emailSubscription($trow->topic_email, $trow->post_subject, $trow->forum_id, $trow->id, $row->post_text );
		}
		return true;
	}

	function getAttachments()
	{
		$query = 'SELECT * FROM #__ccb_attachments WHERE post_id = ' . (int)$this->_id;
		$this->_db->setQuery($query);
		$attachments = ($attachments = $this->_db->loadObjectList()) ? $attachments:array();
		$retArr = array();
		foreach ($attachments as $item) {
			$elemAttach = array( $item->ccb_name, $item->real_name, $item->extension, $item->filesize, $item->filetime, $item->comment);
			$retArr[] = $elemAttach;
		}
		return $retArr;
		print_r( $retArr);
	}

    function getStartingPost()
    {
    	$this->_start_post_id = isset($this->_start_post_id) ? $this->_start_post_id: 0 ;
        return $this->_start_post_id;
    }
}

?>
