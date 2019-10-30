<?php
/**
 * @version		$Id: approval.php 109 2009-04-26 07:50:55Z thomasv $
 * @Project		ccBoard - Joomla! Bulletin Board Extension/Component
 * @author 		Thomas Varghese
 * @package		ccBoard
 * @copyright	Copyright (C) 2008-2009 codeclassic.org. All rights reserved.
 * @license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

defined( '_JEXEC' ) or die( 'Restricted Access' );
jimport( 'joomla.application.component.model' );
require_once JPATH_COMPONENT_SITE . DS . 'models' .DS. 'post.php';
class ccboardModelApproval extends JModel
{

	var $_data;
    var $_limit;
    var $_limitstart;

	function ccboardModelApproval()
	{
		parent::__construct();
	}

    function setLimits($limitstart, $limit)
    {
        $this->_limitstart = $limitstart;
        $this->_limit = $limit;
    }

    function _buildQuery()
    {
       	$user = &JFactory::getUser();
		$query = 'SELECT p.id, p.forum_id, p.topic_id, p.post_subject, p.post_text, ' .
					'p.post_time, p.post_user, p.post_username, p.modified_time, '.
					'p.modified_reason, p.modified_by,  ' .
					'f.forum_name, t.post_subject ' .
					'FROM #__ccb_posts AS p ' .
					'INNER JOIN #__ccb_forums AS f ON f.id = p.forum_id ' .
					'INNER JOIN #__ccb_topics AS t ON t.id = p.topic_id ';
		if( $user->get('gid') < $this->getAdministrator() ) {
			$query .= 'INNER JOIN #__ccb_moderators AS m ON m.forum_id = p.forum_id ' .
					  'WHERE (p.hold=1) AND (m.user_id=' . $user->get('id'). ') ';
		} else {
			$query .= 'WHERE (p.hold=1) ';
		}
		$query .= 'ORDER BY p.post_time ';
        return $query;
    }

    function getAdministrator()
    {
    	$acl = &JFactory::getACL();
    	return $acl->get_group_id(null, 'Administrator');
    }

    function getData()
    {
        if (empty($this->_data)) {
            $query = $this->_buildQuery();
            $this->_db->setQuery($query, $this->_limitstart, $this->_limit);
            $this->_data = ($this->_data = $this->_db->loadObjectList())?$this->_data:array();
        }
        return $this->_data;
    }

    function getTotal()
    {
    	$user = &JFactory::getUser();
        $query = 'SELECT count(p.id) '.
					'FROM #__ccb_posts AS p ' .
					'LEFT JOIN #__ccb_moderators AS m ON m.forum_id = p.forum_id ' .
					'WHERE (p.hold=1) ';
		if( $user->get('gid') < $this->getAdministrator() ) {
			$query .= 'AND (m.user_id=' . $user->get('id'). ') ';
		}

        $this->_db->setQuery($query);
        return $this->_db->loadResult();
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

    function approve( $post_id )
	{
		global $ccbConfig;
		$user = &JFactory::getUser();

		if( $user->guest ) {
			$this->setError('REQUEST_FORBIDDEN');
			return false;
		}

		$row = &JTable::getInstance('ccbpost', 'ccboardTable');
		if( !$row->load($post_id) ) {
			$this->setError('POST_IS_MISSING');
			return false;
		}
		$post_user = $row->post_user;
		$forum_id = $row->forum_id;
		$topic_id = $row->topic_id;

		if( $row->hold < 1 ) {
			$this->setError('POST_IS_ALREADY_APPROVED');
			return false;
		}

		$trow = &JTable::getInstance('ccbtopic', 'ccboardTable');
		if( !$trow->load( $topic_id ) ) {
			$this->setError('TOPIC_IS_MISSING');
			return false;
		}

		$frow = &JTable::getInstance('ccbforum', 'ccboardTable');
		if( !$frow->load( $forum_id ) ) {
			$this->setError(JText::_('FORUM_IS_MISSING'));
			return false;
		}

		$model = new ccboardModelPost();
		$model->setPostId($post_id, $topic_id, $forum_id);
		if( $model->isModerator() < 1) {
			$this->setError('REQUEST_FORBIDDEN');
			return false;
		}

		// Update
		$row->hold=0;
		$trow->last_post_time = $row->post_time;
		$trow->last_post_id = $row->id;
		$trow->last_post_user = $row->post_user;
		$trow->last_post_username = $row->post_username;

		$frow->last_post_time = $row->post_time;
		$frow->last_post_id = $row->id;
		$frow->last_post_user = $row->post_user;
		$frow->last_post_username = $row->post_username;

		if( $trow->start_post_id == $row->id && $row->modified_time < 1) {
			$frow->topic_count += 1;
			$trow->hold = 0;
		} elseif ($row->modified_time < 1 ) {
			$frow->post_count += 1;
			$trow->reply_count += 1;
		}

		// ----------------------------- SAVE TOPIC AND FORUM COUNTERS
		if (!$row->store(true)) {
			$this->setError($row->getError());
			return false;
		}

		if (!$trow->store(true)) {
			$this->setError($trow->getError());
			return false;
		}

		if (!$frow->store(true)) {
			$this->setError($frow->getError());
			return false;
		}

		if ($row->modified_time < 1) {
			ccboardHelperConfig::updateRank( 1, $post_user );
		}
		ccboardHelperConfig::emailSubscription($trow->topic_email, $trow->post_subject, $trow->forum_id, $trow->id, $row->post_text );

		return true;

	}

}
?>
