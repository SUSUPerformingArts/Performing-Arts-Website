<?php
/**
 * @version		$Id: topiclist.php 173 2009-09-21 14:43:37Z thomasv $
 * @Project		ccBoard - Joomla! Bulletin Board Extension/Component
 * @author 		Thomas Varghese
 * @package		ccBoard
 * @copyright	Copyright (C) 2008-2009 codeclassic.org. All rights reserved.
 * @license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

defined( '_JEXEC' ) or die( 'Restricted Access' );
jimport( 'joomla.application.component.model' );

class ccboardModelTopicList extends JModel
{

	var $_data;
    var $_forum_id;
    var $_limit;
    var $_limitstart;
    var $_ordering;
    var $_forum_name;
    var $_locked;
    var $_review;
    var $_moderated;
    var $_post_for;

	function ccboardModelTopicList()
	{
		parent::__construct();
	}

    function setForumId($forumId)
    {
        $this->_forum_id = $forumId;
        $this->_data = null;
        $this->_forum_name = null;
        $this->_locked = null;
        $this->_review = null;
        $this->_moderated = null;
        $this->_post_for = null;
    }


    function setOrdering($ordering, $orderingDirection)
    {
        $this->_ordering = $ordering;
        switch ($this->_ordering) {
            case 'post_subject':
                $this->_ordering = 't.post_subject';
                break;
           	case 'reply_count':
                $this->_ordering = 't.reply_count';
                break;
            case 'hits':
                $this->_ordering = 't.hits';
                break;
            case 'last_post_time':
                $this->_ordering = 't.last_post_time';
                break;
            default:
                $this->_ordering = 't.post_time';
                break;
        }

        $this->_ordering .= $orderingDirection == 'desc'?' DESC':' ASC';
    }

    function setLimits($limitstart, $limit)
    {
        $this->_limitstart = $limitstart;
        $this->_limit = $limit;
    }


    function _buildQuery()
    {
       	$user = &JFactory::getUser();
		$gid = 0;
		if(!$user->guest) $gid = $user->get('gid');

        $where = array(
            't.forum_id = '.(int)$this->_forum_id,
            't.hold = 0 ',
         	'f.view_for <= '.(int)$gid,
        	'f.published = 1'
        );

        $query = 'SELECT t.id, t.forum_id, t.post_subject, t.locked, ' .
        			't.post_time, t.post_user, t.reply_count, t.hits, ' .
        			't.last_post_time, t.last_post_id, t.last_post_user, t.topic_emoticon, ' .
					't.post_username, t.last_post_username, p.post_subject AS last_post_subject ' .
					'FROM #__ccb_topics AS t '.
        			'INNER JOIN #__ccb_forums AS f ON f.id = t.forum_id ' .
                    'LEFT JOIN #__ccb_posts AS p ON p.id = t.last_post_id ' .
        			'WHERE '.implode(' AND ', $where) . ' '.
        			'ORDER BY '. $this->_ordering;

        $this->_setForumStatus();  /* In house call */
        return $query;
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
		$gid = 0;
		if(!$user->guest) $gid = $user->get('gid');

        $where = array(
            't.forum_id = '.(int)$this->_forum_id,
            't.hold = 0 ',
         	'f.view_for <= '.(int)$gid,
        	'f.published = 1'
        );
    	$query = 'SELECT COUNT(t.id) ' .
					'FROM #__ccb_topics AS t ' .
        			'INNER JOIN #__ccb_forums AS f ON f.id = t.forum_id ' .
        			'WHERE '.implode(' AND ', $where) ;

        $this->_db->setQuery($query);
        return $this->_db->loadResult();
    }

    function getForumName()
    {
    	$this->_forum_name= isset($this->_forum_name)?$this->_forum_name:'';
        return $this->_forum_name;
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
		$count = $count + ( ($user->get('gid') >= $this->getAdministrator()) ? 1 : 0 );
        if( $count > 0 && $this->isForumModerated()) {
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
		$gid = 0;
		if(!$user->guest) $gid = $user->get('gid');

		if( $this->isModerator() || (!$this->isLocked() && $this->_post_for <= $gid)) {
			return 1;
		}
		return 0;
    }

    function _setForumStatus()
    {
       	$user = &JFactory::getUser();
		$gid = 0;
		if(!$user->guest) $gid = $user->get('gid');

    	$query = 'SELECT f.forum_name, f.locked, f.review, f.post_for, f.moderated  ' .
    		'FROM #__ccb_forums AS f ' .
    		'WHERE f.id='. (int)$this->_forum_id;
    	$data = null;
        $this->_db->setQuery($query);
        $data = $this->_db->loadObject();
		if( $data ) {
	        $this->_forum_name =  $data->forum_name;
	        $this->_locked =  $data->locked;
	        $this->_review = $data->review;
	        $this->_moderated = $data->moderated;
	        $this->_post_for = $data->post_for;
		}
    }

    function getSticky()
    {
    	// 0-Normal, 1-FrontPage, 2-Forum, 3-Global
    	$query = 'SELECT t.id, t.post_subject, t.topic_type, t.hits, ' .
    				't.forum_id, t.post_time, t.post_user, t.post_username ' .
        			'FROM #__ccb_topics AS t ' .
        			'WHERE (  ((t.forum_id = '. (int)$this->_forum_id . ') AND ' .
							'(t.topic_type = 2)) OR (t.topic_type = 3) AND (t.hold=0)) ' .
        			'ORDER BY t.topic_type, t.post_time DESC ';

        $sticky = ($sticky = $this->_getList($query))? $sticky :array();
		return $sticky;
    }

    function getAdministrator()
    {
    	$acl = &JFactory::getACL();
    	return $acl->get_group_id(null, 'Administrator');
    }
}
?>
