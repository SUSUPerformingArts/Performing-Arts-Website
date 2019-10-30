<?php
/**
 * @version		$Id: postlist.php 162 2009-08-22 18:33:26Z thomasv $
 * @Project		ccBoard - Joomla! Bulletin Board Extension/Component
 * @author 		Thomas Varghese
 * @package		ccBoard
 * @copyright	Copyright (C) 2008-2009 codeclassic.org. All rights reserved.
 * @license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

defined( '_JEXEC' ) or die( 'Restricted Access' );
jimport( 'joomla.application.component.model' );

class ccboardModelPostList extends JModel
{
    var $_data;
    var $_forum_id;
    var $_forum_name;
    var $_topic_id;
    var $_locked;
    var $_review;
    var $_moderated;
    var $_post_email;
    var $_post_favourite;
    var $_start_post_id;

    var $_limit;
    var $_limitstart;

	function ccboardModelPostList()
	{
		parent::__construct();
	}

	function setTopicId($topic_id)
    {
        $this->_topic_id = $topic_id;
        $this->_data = null;
		$this->_forum_id = null;
        $this->_forum_name = null;
        $this->_locked = null;
        $this->_review = null;
        $this->_moderated = null;
        $this->_post_email = null;
        $this->_post_favourite = null;
        $this->_start_post_id = null;

        $this->_topic_id = $this->_checkPermission();
    }

  	function setLimits($limitstart, $limit)
    {
        $this->_limitstart = $limitstart;
        $this->_limit = $limit;
    }

    function _checkPermission()
    {
    	$user = &JFactory::getUser();
		$gid = 0;
		if(!$user->guest) $gid = $user->get('gid');

		$query = 'SELECT t.id, t.forum_id, t.topic_email, t.topic_favourite, (t.locked + f.locked) AS locked, ' .
					'f.forum_name, f.post_for, t.hold, t.start_post_id, f.moderated, f.review ' .
					'FROM #__ccb_topics AS t ' .
					'INNER JOIN #__ccb_forums AS f ON f.id = t.forum_id ' .
					'WHERE t.id = ' . $this->_topic_id . ' AND t.hold = 0 AND ' .
					'f.published = 1 AND f.view_for <= ' . $gid;

		$this->_db->setQuery($query);
        $perm = $this->_db->loadObject();

        if ( (int)(isset($perm->id)?$perm->id:0) > 0 ) {
			$this->_forum_id = $perm->forum_id;
        	$this->_forum_name = $perm->forum_name;
	        $this->_locked =  $perm->locked;
	        $this->_review = $perm->review;
	        $this->_moderated = $perm->moderated;
   			$this->_post_for = $perm->post_for;
   			$this->_post_email = $perm->topic_email;
			$this->_post_favourite = $perm->topic_favourite;
			$this->_start_post_id = $perm->start_post_id;
        	return $this->_topic_id;
        }
        return 0;
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
        if( ($count && $this->isForumModerated()) || ($user->get('gid') >= $this->getAdministrator()) ) {
        	return 1;
        }
        return 0;
    }

    function isLocked()
    {
    	$this->_locked = isset($this->_locked) ? $this->_locked : 1 ;
        return $this->_locked;
    }

    function isReview()
    {
    	$this->_review = isset($this->_review) ? $this->_review : 0 ;
        return $this->_review;
    }

    function isForumModerated()
    {
    	$this->_moderated = isset($this->_moderated) ? $this->_moderated: 0 ;
        return $this->_moderated;
    }

    function isPostingAllowed()
    {
    	$this->_post_for = isset($this->_post_for) ? $this->_post_for: 18 ;
       	$user = &JFactory::getUser();
		$gid = $user->get('gid');

		if( $this->isModerator() > 0 || ($this->isLocked() < 1 && $this->_post_for <= $gid)) {
			return 1;
		}
		return 0;
    }

    function isEditingAllowed()
    {
    	global $ccbConfig;
    	$user = &JFactory::getUser();

    	// Allow if Admin/Moderator
    	if($this->isModerator()) return 1;

    	// disable if editing not allowed for the rest of users
    	if( $ccbConfig->allowedit == 0) return 0;

    	// check guest can edit
    	if( $ccbConfig->allowedit == 1 && !$this->_locked ) return 1;

    	// check registered users can edit
    	if( $ccbConfig->allowedit == 2 && !$user->guest && !$this->_locked) return 1;

    	return 0;

    }

    function isEmailSubscribed()
    {
    	$user = &JFactory::getUser();
		if($user->guest) return 0;
		if(!strpos('a'.$this->_post_email , $user->get('id') )) return 0;
		return 1;
    }

    function isFavourite()
    {
    	$user = &JFactory::getUser();
		if($user->guest) return 0;
		if(!strpos('a'.$this->_post_favourite , $user->get('id') )) return 0;
		return 1;
    }

    function getStartingPost()
    {
    	$this->_start_post_id = isset($this->_start_post_id) ? $this->_start_post_id: 0 ;
        return $this->_start_post_id;
    }

    function _buildQuery()
    {
    	global $ccbConfig;
    	$user = &JFactory::getUser();
        $where = array(
            'p.topic_id = '.(int)$this->_topic_id ,
        	'p.hold = 0 ',
        	'f.view_for <= ' . $user->get('gid'),
            'f.published = 1'
        );

        $order = 'p.id '. $ccbConfig->postlistorder;

        $query = 'SELECT p.id, p.topic_id, p.forum_id, p.post_subject, p.post_text, '.
        			'p.post_user, p.post_username, p.post_time, p.ip, ' .
        			'p.modified_by, p.modified_time, p.modified_reason ' .
        			'FROM #__ccb_posts AS p ' .
        			'INNER JOIN #__ccb_forums AS f ON f.id = p.forum_id ' .
        			'WHERE ' . implode(' AND ', $where) . ' ORDER BY ' . $order;

        return $query;
    }


    function getData()
    {
        if (!isset($this->_data)) {
            $query = $this->_buildQuery();
            $this->_db->setQuery($query, $this->_limitstart, $this->_limit);
            $posts = ($posts = $this->_db->loadObjectList()) ? $posts:array();
            $this->_data = $posts;
        }
        return $this->_data;
    }


	function getSticky()
    {
    	// 0-Normal, 1-FrontPage, 2-Forum, 3-Global
    	$query = 'SELECT t.id, t.post_subject, t.topic_type, t.hits, ' .
    				't.forum_id, t.post_time, t.post_user, t.post_username ' .
        			'FROM #__ccb_topics AS t ' .
        			'WHERE ( ((t.forum_id = '. (int)$this->_forum_id .
        					' AND t.topic_type = 2)) OR (t.topic_type = 3) AND (t.hold=0)) ' .
        			'ORDER BY t.topic_type, t.post_time DESC ';

        $sticky = ($sticky = $this->_getList($query))? $sticky :array();
		return $sticky;
    }


    function getTotal()
    {
        $query = 'SELECT COUNT(*) ' .
                'FROM #__ccb_posts WHERE topic_id = ' . (int)$this->_topic_id .
    			' AND hold = 0 ';
        $this->_db->setQuery($query);
        return $this->_db->loadResult();
    }

    function getForumName()
    {
        return $this->_forum_name;
    }

    function getForumId()
    {
        return $this->_forum_id;
    }

    function getForumList()
    {
    	$query = 'SELECT id, forum_name FROM #__ccb_forums ' .
    				'WHERE published=1 and id != ' . $this->_forum_id ;

    	$this->_db->setQuery($query);
        $flist = ($flist = $this->_db->loadObjectList())? $flist :array();
		return $flist;
    }

	function emailSubscription( $fid, $tid)
	{
		global $ccbConfig;

		$user = &JFactory::getUser();
    	if($user->guest) {
    		$this->setError(JText::_('PLEASE_LOGIN_TO_SUB_EMAIL'));
            return false;
    	}

    	if($ccbConfig->emailsub <> 1 ) {
    		$this->setError(JText::_('EMAIL_SUBSCRIPTION_NOT_ENABLED'));
            return false;
    	}

    	$trow = &JTable::getInstance('ccbtopic', 'ccboardTable');
    	if(!$trow->load($tid)) {
    		$this->setError(JText::_('TOPIC_IS_MISSING'));
            return false;
        }
		// thms modified Message format changed to HTML to incorporate the changes done N6REJ
        $_URL = JRoute::_( JURI::root() . "index.php?option=com_ccboard&view=postlist&forum=" .	$fid . "&topic=" . $tid . "&Itemid=" . $ccbConfig->itemid );
        $uid = $user->get('id') . '-';
		$pos = strpos( 'a'.$trow->topic_email, $uid );
		if( !$pos ) {
			$trow->topic_email .= $uid;
			$subj = JText::_('EMAIL_TOPIC_SUBSCRIBED');
			$body = JText::_('EMAIL_GREETING') . "  " . $user->get('username') . ", <br /><br />" .
					JText::_('EMAIL_TOPIC_INTRO_SUBSCRIBED') . " <br /><br />" .
					JText::_('EMAIL_TOPIC_NAME') . " "  . $trow->post_subject . "<br /><br />" .
					JText::_('EMAIL_URL') . ' <a href="' . $_URL . '">'.$_URL. "</a><br /><br />" .
					JText::_('EMAIL_SENDER') . " <br />";
		}
		else {
			$trow->topic_email = str_replace($uid, '', $trow->topic_email);
			$subj = JText::_('EMAIL_TOPIC_UNSUBSCRIBED');
			$body = JText::_('EMAIL_GREETING') . "  " . $user->get('username') . ", <br /><br />" .
					JText::_('EMAIL_TOPIC_INTRO_UNSUBSCRIBED') . " <br /><br />" .
					JText::_('EMAIL_TOPIC_NAME') . " "  . $trow->post_subject . "<br /><br />" .
					JText::_('EMAIL_URL') . ' <a href="' . $_URL . '">'.$_URL. "</a><br /><br />" .
					JText::_('EMAIL_SENDER') . " <br />";
		}

		if( !$trow->store(false)) {
			$this->setError(JText::_('ERROR_EMAIL_SUB'));
            return false;
		}

		ccboardHelperConfig::sendMail($user->get('email'), $subj, $body );
		return true;
	}


    function delete($post_id)
    {
        if (!$post_id) {
            $this->setError(JText::_('POST_IS_MISSING'));
            return false;
        }

        $row = &JTable::getInstance('ccbpost', 'ccboardTable');
        if(!$row->load($post_id)) {
    		$this->setError(JText::_('POST_IS_MISSING'));
            return false;
        }

        $topic_id = $row->topic_id;
        $forum_id = $row->forum_id;
        $this->setTopicId($row->topic_id);

    	if( !$this->isModerator()) {
			$this->setError(JText::_('PLEASE_LOGIN_AS_MODERATOR'));
            return false;
    	}

    	$trow = &JTable::getInstance('ccbtopic', 'ccboardTable');
        if(!$trow->load($topic_id)) {
    		$this->setError(JText::_('TOPIC_IS_MISSING'));
            return false;
        }

		// If the user want to delete the entire thread
		if( $trow->start_post_id == $post_id ) {
            $query = 'SELECT id FROM #__ccb_posts WHERE topic_id = ' . (int) $topic_id;
            $this->_db->setQuery($query);
            $posts = ($posts = $this->_db->loadObjectList()) ? $posts :array();
            foreach ($posts as $item) {
        		if( $item->post_user > 0 ) {
                    ccboardHelperConfig::updateRank(-1, $item->post_user);
                }
                $this->deletePosts($item->id);
            }
            if(!$trow->delete($topic_id)) {
	        	$this->setError(JText::_('TOPIC_DELETION_FAILED'));
	            return false;
			}
		} 
        else  // If the user want to delete a single post 
        {
            if( $row->post_user > 0 ) {
                ccboardHelperConfig::updateRank(-1, $row->post_user);
            }
            $this->deletePosts($post_id);
            $this->_reCalcTopic( $topic_id );
        }

        $this->_reCalcForum( $forum_id );
        return true;
    }

    function dbExec($query)
    {
    	$this->_db->setQuery($query);
    	if( !$this->_db->query()) {
    		$this->setError($this->_db->getError() );
    	}
    }

    function deletePosts( $post_id )
    {
        $query = 'DELETE FROM #__ccb_posts WHERE post_id = ' . (int) $post_id;
        $this->dbExec( $query );
        $this->deleteAttachments($post_id);
    }

    function deleteAttachments( $post_id )
	{
        jimport('joomla.filesystem.file');
		$query = 'SELECT * FROM #__ccb_attachments WHERE post_id = ' . (int) $post_id;
		$this->_db->setQuery($query);
		$attachments = ($attachments = $this->_db->loadObjectList()) ? $attachments:array();
		foreach ($attachments as $item) {
            $folder	= JPATH_COMPONENT_SITE . DS . 'assets' .DS. 'uploads';
            $filepath = JPath::clean($folder.DS.strtolower($item->ccb_name));
            JFile::delete($filepath);
		}
	}

	function getAttachments( $post_id )
	{
		$query = 'SELECT * FROM #__ccb_attachments WHERE post_id = ' . (int) $post_id;
		$this->_db->setQuery($query);
		$attachments = ($attachments = $this->_db->loadObjectList()) ? $attachments:array();
		$retArr = array();
		foreach ($attachments as $item) {
			$elemAttach = array( $item->ccb_name , $item->real_name, $item->extension, $item->filesize, $item->filetime, $item->comment);
			$retArr[] = $elemAttach;
		}
		return $retArr;
	}

 	function getAdministrator()
    {
    	$acl = &JFactory::getACL();
    	return $acl->get_group_id(null, 'Administrator');
    }

	function postLock($forum, $topic, $lock)
	{
		$this->setTopicId( $topic );
		if( !$this->isModerator()) {
			$this->setError(JText::_('PLEASE_LOGIN_AS_MODERATOR'));
            return false;
    	}

		$query = 'UPDATE #__ccb_topics SET locked = ' . $lock .
				' WHERE forum_id = ' . $forum . ' AND id = ' . $topic;
		$this->_db->setQuery($query);
    	if( !$this->_db->query()) {
    		$this->setError($this->_db->getError() );
    		return 0;
    	}
		return 1;
	}

	function favourite( $fid, $tid)
	{
		global $ccbConfig;

		if($ccbConfig->showfavourites <> 1) {
    		$this->setError(JText::_('FAAVOURITES_NOT_ENABLED'));
            return false;
		}

		$user = &JFactory::getUser();
    	if($user->guest) {
    		$this->setError(JText::_('PLEASE_LOGIN_TO_FAVOURITE'));
            return false;
    	}

    	$trow = &JTable::getInstance('ccbtopic', 'ccboardTable');
    	if(!$trow->load($tid)) {
    		$this->setError(JText::_('TOPIC_IS_MISSING'));
            return false;
        }
        $uid = $user->get('id') . '-';
		$pos = strpos( 'a'.$trow->topic_favourite, $uid );
		if( !$pos ) {
			$trow->topic_favourite .= $uid;
		}
		else {
			$trow->topic_favourite = str_replace($uid, '', $trow->topic_favourite);
		}

		if( !$trow->store(false)) {
			$this->setError(JText::_('ERROR_TOPIC_FAVOURITE'));
            return false;
		}

		return true;
	}

	function split( $fid, $tid, $pid )
	{

		$this->setTopicId($tid);

		if( !$this->isModerator()) {
			$this->setError(JText::_('PLEASE_LOGIN_AS_MODERATOR'));
            return false;
    	}

    	$trow = &JTable::getInstance('ccbtopic', 'ccboardTable');
    	if(!$trow->load($tid)) {
    		$this->setError(JText::_('TOPIC_IS_MISSING'));
            return false;
        }

		$prow = &JTable::getInstance('ccbpost', 'ccboardTable');
    	if(!$prow->load($pid)) {
    		$this->setError(JText::_('POST_IS_MISSING'));
            return false;
        }

        if( $trow->start_post_id == $pid) {
    		$this->setError(JText::_('COULD_NOT_SPLIT_THE_FIRST_POST'));
            return false;
        }

        $data = array();
        $data['id'] = null;
        $data['forum_id'] = $prow->forum_id;
        $data['post_subject'] = $prow->post_subject;
        $data['reply_count'] =0;
        $data['post_time'] = $prow->post_time;
        $data['post_user'] = $prow->post_user;
        $data['post_username'] = $prow->post_username;
        $data['last_post_time'] = $prow->post_time;
        $data['last_post_user'] = $prow->post_user;
        $data['last_post_username'] = $prow->post_username;
        $data['last_post_time'] = $prow->post_time;
        $data['start_post_id'] = $pid;
        $data['topic_type'] = $trow->topic_type;
        $data['topic_email'] = $trow->topic_email;
        $data['topic_favourite'] = $trow->topic_favourite;
        $data['topic_emoticon'] = $trow->topic_emoticon;
        $data['locked'] = $trow->locked;

    	$trow = &JTable::getInstance('ccbtopic', 'ccboardTable');
        if( !$trow->bind($data)) {
    		$this->setError($trow->getError());
            return false;
		}
	    if (!$trow->store(true)) {
            $this->setError($trow->getError());
            return false;
        }
        $trow->load($trow->id);
		$topic_id = $trow->id;

		$query = 'UPDATE #__ccb_posts SET topic_id = ' . $topic_id .
				' WHERE topic_id = ' . $tid . ' AND id >= ' . $pid;
		$this->_db->setQuery($query);
    	if( !$this->_db->query()) {
    		$this->setError($this->_db->getError() );
    		return false;
    	}

		$this->_reCalcTopic( $tid );
		$this->_reCalcTopic( $topic_id );
		$this->_reCalcForum( $fid );

		return true;
	}

	function move( $fid, $tid, $fnew )
	{
		$this->setTopicId( $tid );
		if( !$this->isModerator()) {
			$this->setError(JText::_('PLEASE_LOGIN_AS_MODERATOR'));
            return false;
    	}

    	$trow = &JTable::getInstance('ccbtopic', 'ccboardTable');
    	if(!$trow->load($tid)) {
    		$this->setError(JText::_('TOPIC_IS_MISSING'));
            return false;
        }

		if( $fid == $fnew ) {
			$this->setError(JText::_('INVALID_MOVE'));
            return false;
		}

		$query = 'UPDATE #__ccb_posts SET forum_id = ' . $fnew .
				' WHERE topic_id = ' . $tid ;
		$this->_db->setQuery($query);
    	if( !$this->_db->query()) {
    		$this->setError($this->_db->getError() );
    		return false;
    	}
		$query = 'UPDATE #__ccb_topics SET forum_id = ' . $fnew .
				' WHERE id = ' . $tid ;
		$this->_db->setQuery($query);
    	if( !$this->_db->query()) {
    		$this->setError($this->_db->getError() );
    		return false;
    	}

    	$this->_reCalcTopic( $tid );
		$this->_reCalcForum( $fid );
		$this->_reCalcForum( $fnew );

		return true;
	}

	function _reCalcTopic($tp=0)
	{
        $prequery = 'UPDATE #__ccb_topics AS t '  .
                'SET t.reply_count   = 0, ' .
                    't.start_post_id     = 0, '.
                    't.post_user         = 0, '.
					't.post_username	 = "", ' .
					't.post_time         = 0, '.
					't.last_post_id      = 0, '.
                    't.last_post_user    = 0, '.
                    't.last_post_username= "", '.
                    't.last_post_time    = 0 ';

        $query = 'UPDATE #__ccb_topics AS t, ' ;
        if( $tp > 0 ) {
            $query  .= '(select count(p.id)-1 as pcnt, p.topic_id from #__ccb_posts as p where p.hold=0 and p.topic_id = ' . $tp . ' group by p.topic_id ORDER BY p.topic_id) AS pcount, '  .
                       '(select p1.id, p1.topic_id, p1.post_user, p1.post_username, p1.post_time from #__ccb_posts as p1 INNER JOIN (SELECT min(id) as id FROM #__ccb_posts where hold=0 and topic_id = ' . $tp . ' group by topic_id) AS ot ON ot.id = p1.id ORDER BY p1.topic_id) AS pfirst, ' .
                       '(select p2.id, p2.topic_id, p2.post_user, p2.post_username, p2.post_time from #__ccb_posts as p2 INNER JOIN (Select Max(post_time) max_time, topic_id from #__ccb_posts where hold=0 and topic_id = ' . $tp . ' group by topic_id) AS p1 ON p1.max_time = p2.post_time and p2.topic_id=p1.topic_id ORDER BY p2.topic_id) AS plast ' ;
        } else {
            $query  .= '(select count(p.id)-1 as pcnt, p.topic_id from #__ccb_posts as p where p.hold=0 group by p.topic_id ORDER BY p.topic_id) AS pcount, '  .
                       '(select p1.id, p1.topic_id, p1.post_user, p1.post_username, p1.post_time from #__ccb_posts as p1 INNER JOIN (SELECT min(id) as id FROM #__ccb_posts where hold=0 group by topic_id) AS ot ON ot.id = p1.id ORDER BY p1.topic_id) AS pfirst, ' .
                       '(select p2.id, p2.topic_id, p2.post_user, p2.post_username, p2.post_time from #__ccb_posts as p2 INNER JOIN (Select Max(post_time) max_time, topic_id from #__ccb_posts where hold=0 group by topic_id) AS p1 ON p1.max_time = p2.post_time and p2.topic_id=p1.topic_id ORDER BY p2.topic_id) AS plast ' ;
        }

        $query  .= 'SET t.reply_count   = pcount.pcnt, ' .
                    't.start_post_id    = pfirst.id, '.
                    't.post_user        = pfirst.post_user, '.
					't.post_username	= pfirst.post_username, ' .
					't.post_time        = pfirst.post_time, '.
					't.last_post_id     = plast.id, '.
                    't.last_post_user    = plast.post_user, '.
                    't.last_post_username= plast.post_username, '.
                    't.last_post_time    = plast.post_time ' .
                    'WHERE t.id = pcount.topic_id and t.id = pfirst.topic_id and t.id = plast.topic_id ' ;

        if( $tp > 0 ) {
            $prequery .= ' WHERE t.id = ' . $tp ;
            $query .=  ' and t.id = ' . $tp;
        }

        $this->dbExec($prequery);
        $this->dbExec($query);
	}

	function _reCalcForum($fr=0)
	{
        $prequery = 'UPDATE #__ccb_forums AS f ' .
                'SET f.topic_count          = 0, '.
                    'f.post_count           = 0, '.
                    'f.last_post_id			= 0, '.
                    'f.last_post_user 		= 0, '.
                    'f.last_post_username 	= "", '.
                    'f.last_post_time 		= 0 ';


        $query = 'UPDATE #__ccb_forums AS f, ';
        if( $fr > 0 ) {
            $query  .= '(select sum(t.reply_count) as replycnt, count(t.id) as tcnt, t.forum_id from #__ccb_topics as t where t.hold=0 and t.forum_id = ' . $fr  . ' group by t.forum_id ORDER BY t.forum_id) AS tcount, ' .
                       '(select p2.forum_id, p2.last_post_id, p2.last_post_user, p2.last_post_username, p2.last_post_time from #__ccb_topics as p2 INNER JOIN (Select Max(last_post_time) max_time, forum_id from #__ccb_topics where hold=0 and forum_id=' . $fr . ' group by forum_id) AS p1 ON p1.max_time = p2.last_post_time and p2.forum_id=p1.forum_id ORDER BY p2.forum_id) AS tlast ' ;
        } else {
            $query  .= '(select sum(t.reply_count) as replycnt, count(t.id) as tcnt, t.forum_id from #__ccb_topics as t where t.hold=0 group by t.forum_id ORDER BY t.forum_id) AS tcount, ' .
                       '(select p2.forum_id, p2.last_post_id, p2.last_post_user, p2.last_post_username, p2.last_post_time from #__ccb_topics as p2 INNER JOIN (Select Max(last_post_time) max_time, forum_id from #__ccb_topics where hold=0 group by forum_id) AS p1 ON p1.max_time = p2.last_post_time and p2.forum_id=p1.forum_id ORDER BY p2.forum_id)  AS tlast ' ;
        }
        $query  .=  'SET f.topic_count      = tcount.tcnt, '.
                    'f.post_count           = tcount.replycnt + tcount.tcnt, '.
                    'f.last_post_id			= tlast.last_post_id, '.
                    'f.last_post_user 		= tlast.last_post_user, '.
                    'f.last_post_username 	= tlast.last_post_username, '.
                    'f.last_post_time 		= tlast.last_post_time '.
                    'WHERE f.id = tcount.forum_id and f.id = tlast.forum_id ';

		if( $fr > 0 ) {
            $prequery .= ' WHERE f.id = ' . $fr;
			$query .=  ' and f.id = ' . $fr;
		}

        $this->dbExec($prequery);
		$this->dbExec($query);
	}

	function _reCalcUser($usr=0)
	{
        $prequery = 'UPDATE #__ccb_users AS u SET u.post_count = 0 ';
		$query = 'UPDATE #__ccb_users AS u, ';
        if( $usr > 0 ) {
            $query .= '(select count(p.id) as pcnt, p.post_user FROM #__ccb_posts as p where p.hold=0 and p.post_user = ' . $usr  . ' group by p.post_user ORDER BY p.post_user ) AS pcount ';
        } else {
            $query .= '(select count(p.id) as pcnt, p.post_user FROM #__ccb_posts as p where p.hold=0 and p.post_user > 0 group by p.post_user ORDER BY p.post_user ) AS pcount ';
        }

		$query .= 'SET u.post_count = pcount.pcnt WHERE u.user_id = pcount.post_user ';
		if( $usr > 0 ) {
            $prequery .= ' WHERE u.user_id = ' . $usr;
			$query .=  ' and u.user_id = ' . $usr;
		}

        $this->dbExec($prequery);
		$this->dbExec($query);

		$query = 'UPDATE #__ccb_users AS u ' .
					'SET u.rank = (SELECT r.id FROM #__ccb_ranks AS r WHERE r.rank_min <= u.post_count and r.rank_special = 0 order by r.rank_min DESC limit 1) ' .
					'WHERE (u.rank NOT IN (SELECT r2.id FROM #__ccb_ranks AS r2 WHERE r2.rank_special=1)) ';
		if( $usr > 0 ) {
			$query .=  ' AND u.user_id = ' . $usr;
		}
        //print_r( $query);
		$this->dbExec($query);
	}

	function reportAbuse( $fid, $tid, $pid )
	{
		global $ccbConfig;
		$user = &JFactory::getUser();
    	if($user->guest) {
    		$this->setError(JText::_('PLEASE_LOGIN_TO_REPORT_ABUSE'));
            return false;
    	}

    	$prow = &JTable::getInstance('ccbpost', 'ccboardTable');
    	if(!$prow->load($pid)) {
    		$this->setError(JText::_('POST_IS_MISSING ' . $pid));
            return false;
        }

        $data = JRequest::get('post');

        $_Host = JRoute::_(JURI::root());
        $_URL = JRoute::_( $_Host . "index.php?option=com_ccboard&view=postlist&forum=" .	$fid . "&topic=" . $tid . "&Itemid=" . $ccbConfig->itemid );
		$post_text = preg_replace("/(<img(.*?)src=\")(?!http)/i ", "$1" . $_Host, $prow->post_text  );

		$subj = JText::_('EMAIL_REPORT_ABUSE') . " " . $data['reporttitle'] ;
		$body = JText::_('EMAIL_GREETING_MODERATOR') . ", <br /><br />" .
				JText::_('EMAIL_REPORT_ABUSE_MESSAGE') . " <br /><br />" .
				$data['reporttext'] . " <br /><br />" .
				JText::_('EMAIL_URL') . ' <a href="' . $_URL . '">' . $_URL . "</a><br /><br />" .
				JText::_('EMAIL_REPORT_BY') . "<br />". $user->get('username') . " <br /><br />";
		$body .= " <br /><br /><--------------" . JText::_('EMAIL_DIVIDE') . " ----------------><br /><br />";
		$body .=  JText::_('EMAIL_POST_SUBJECT') . " "  . $prow->post_subject . "<br /><br />" ;
		$body .= $post_text . "<br />";

		$query = 'SELECT u.email FROM #__ccb_moderators AS m ' .
					'INNER JOIN #__users AS u ON m.user_id = u.id ' .
					'WHERE m.forum_id=' . $fid . ' AND m.user_id <> 62';
		$db = &JFactory::getDBO();
		$db->setQuery($query);
		$items = ($items = $db->loadObjectList())? $items : array();

		foreach($items as $item) {
			ccboardHelperConfig::sendMail($item->email, $subj, $body );
		}

		// Send to Main Administrator always
		$query = 'SELECT email FROM #__users WHERE id = 62';
		$db->setQuery($query);
		$item = $db->loadResult();
		if( isset($item)) {
			ccboardHelperConfig::sendMail($item, $subj, $body );
		}
		return true;
	}


}
?>
