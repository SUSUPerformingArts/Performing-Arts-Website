<?php
/**
 * @version		$Id: forums.php 110 2009-04-26 07:59:21Z thomasv $
 * @Project		ccBoard - Joomla! Bulletin Board Extension/Component
 * @author 		Thomas Varghese
 * @package		ccBoard
 * @copyright	Copyright (C) 2008-2009 codeclassic.org. All rights reserved.
 * @license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

defined( '_JEXEC' ) or die( 'Restricted Access' );
jimport( 'joomla.application.component.model' );

class ccboardModelForums extends JModel
{
    var $_data;
    var $_limit;
    var $_limitstart;
    var $_ordering;

	function ccboardModelForums()
	{
		parent::__construct();
	}

    function setOrdering($ordering, $orderingDirection)
    {
        $this->_ordering = $ordering;
        switch ($this->_ordering) {
            case 'id':
                $this->_ordering = 'a.id';
                break;
            case 'forum_name':
                $this->_ordering = 'a.forum_name';
                break;
            case 'cat_name':
                $this->_ordering = 'b.cat_name';
                break;
            case 'published':
                $this->_ordering = 'a.published';
                break;
            case 'locked':
                $this->_ordering = 'a.locked';
                break;
            case 'moderated':
                $this->_ordering = 'a.moderated';
                break;
            case 'review':
                $this->_ordering = 'a.review';
                break;
            case 'view_group':
                $this->_ordering = 'view_group';
                break;
            case 'post_group':
                $this->_ordering = 'post_group';
                break;
            default:
                $this->_ordering = 'a.ordering';
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
        $query = 'SELECT a.id, a.forum_name, a.forum_desc, b.cat_name,
        				 a.ordering, a.published, a.locked, a.moderated, a.review,
        				 a.view_for, v.name AS view_group, p.name AS post_group
                  FROM #__ccb_forums AS a INNER JOIN #__ccb_category AS b ON a.cat_id = b.id
        		  LEFT JOIN #__core_acl_aro_groups AS v ON v.id = a.view_for
        		  LEFT JOIN #__core_acl_aro_groups AS p ON p.id = a.post_for
                  ORDER BY '.$this->_ordering  ;

        return $query;
    }

    function getData()
    {
        if (empty($this->_data)) {
            $query = $this->_buildQuery();
            $this->_data = $this->_getList($query, $this->_limitstart, $this->_limit);
        }
        return $this->_data;
    }

    function delete($cid)
    {
        if (!$cid || !is_array($cid)) {
            return false;
        }

        $query = 'SELECT count(*) as cntPost from #__ccb_topics WHERE forum_id IN ('.implode(',', $cid).')';
        $data = ($this->_data = $this->_getList($query))?$this->_data:array();
        $item = $data[0];
        if( $item->cntPost > 0 ) {
        	$this->setError(JText::_('CCB_TOPICS_EXISTS_FOR_THIS_FORUM'));
        	return false;
        }

        $query = 'DELETE FROM #__ccb_forums WHERE id IN ('.implode(',', $cid).')';
        $this->_db->setQuery($query);

        if ($this->_db->query()) {
            return false;
        }

        return $this->_db->query();
    }


    function getTotal()
    {
        $query = 'SELECT COUNT(*)
                  FROM #__ccb_forums';

        $this->_db->setQuery($query);
        return $this->_db->loadResult();
    }


    function saveOrder($cid, $orders)
    {
    	$forum = &JTable::getInstance('ccbforum', 'ccboardTable');
        foreach ($orders as $key => $value) {
            $forum->load($cid[$key]);
            $forum->ordering = $value;
            $forum->store();
        }
    }

    function publish($cid)
    {
        $forum = &JTable::getInstance('ccbforum', 'ccboardTable');
        if( !$forum->publish($cid, 1) ) {
        	$this->setError($forum->getError());
        	return false;
        }
        return true;
    }

    function unpublish($cid)
    {
        $forum = &JTable::getInstance('ccbforum', 'ccboardTable');
        if( !$forum->publish($cid, 0) ) {
        	$this->setError($forum->getError());
        	return false;
        }
        return true;
    }

    function toggle_lock($cid)
    {
    	$forum = &JTable::getInstance('ccbforum', 'ccboardTable');
		if( $forum->load($cid[0]) ) {
			$forum->locked = $forum->locked ? 0 : 1;
            $forum->store();
		}
        return true;
    }


    function toggle_moderate($cid)
    {
    	$forum = &JTable::getInstance('ccbforum', 'ccboardTable');
		if( $forum->load($cid[0]) ) {
			$forum->moderated = $forum->moderated ? 0 : 1;
            $forum->store();
		}
        return true;
    }

    function toggle_review($cid)
    {
    	$forum = &JTable::getInstance('ccbforum', 'ccboardTable');
		if( $forum->load($cid[0]) ) {
			$forum->review = $forum->review ? 0 : 1;
            $forum->store();
		}
        return true;
    }

    function move($cid, $direction)
    {
        $table = &JTable::getInstance('ccbforum', 'ccboardTable');

        if (!$table->load($cid)) {
            $this->setError(JText::_('CCB_ERROR_COULD_NOT_LOAD_FORUMS_TABLE'));
            return false;
        }
        $ret = $table->move($direction);
        if( $ret != '')  {
            $this->setError(JText::_('CCB_ERROR_COULD_NOT_MOVE_FORUM'));
            return false;
        }

        return true;
    }
}
?>
