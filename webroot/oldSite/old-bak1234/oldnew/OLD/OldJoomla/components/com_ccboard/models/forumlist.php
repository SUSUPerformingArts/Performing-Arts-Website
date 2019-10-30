<?php
/**
 * @version		$Id: forumlist.php 188 2009-09-26 07:11:56Z thomasv $
 * @Project		ccBoard - Joomla! Bulletin Board Extension/Component
 * @author 		Thomas Varghese
 * @package		ccBoard
 * @copyright	Copyright (C) 2008-2009 codeclassic.org. All rights reserved.
 * @license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

defined( '_JEXEC' ) or die( 'Restricted Access' );
jimport( 'joomla.application.component.model' );

class ccboardModelForumList extends JModel
{
    var $_data;
    var $_cat_id=null;

	function ccboardModelForumList()
	{
		parent::__construct();
	}

    function setCatId($cat_id)
    {
        $this->_cat_id = $cat_id;
    }

	function _buildWhere()
	{
		$user = &JFactory::getUser();
		$gid = 0;
		if(!$user->guest) $gid = $user->get('gid');

		$where = array(
            'f.published = 1',
            'f.view_for <= ' . $gid
        	);
        if( $this->_cat_id > 0 ) {
            $where[] = 'f.cat_id = ' . $this->_cat_id;
        }
		return $where;
	}

    function _buildQuery()
    {
    	$query = 'SELECT f.id, f.forum_name, f.forum_desc, f.topic_count, f.post_count, f.cat_id, ' .
    				'f.last_post_time, f.ordering, f.locked, f.review, ' .
    				'f.last_post_id, f.last_post_user, f.last_post_username, u.username, u.name, '.
    				'c.cat_name, c.ordering, p.topic_id, p.post_subject ' .
    				'FROM #__ccb_forums AS f '.
    				'INNER JOIN #__ccb_category AS c ON c.id = f.cat_id ' .
    				'LEFT JOIN #__users AS u ON u.id = f.last_post_user ' .
    				'LEFT JOIN #__ccb_posts AS p ON p.id = f.last_post_id ' .
					'WHERE '.implode(' AND ', $this->_buildWhere() ) . ' ' .
    				'ORDER BY c.ordering, f.cat_id, f.ordering ';

    	return $query;
    }

    function getData()
    {
        if (!isset($this->_data)) {
			$query = $this->_buildQuery();
			$this->_data = ($this->_data = $this->_getList($query))?$this->_data:array();
        }

        return $this->_data;
    }

    function getBoardSummary()
    {
        $db = &JFactory::getDBO();
        $boardsummary = new stdClass();

        $query = 'SELECT count(id) as cnt FROM #__ccb_category';
	    $db->setQuery($query);
        $boardsummary->catcount = ($obj = $db->loadObject()) ? $obj->cnt : 0;

        $query = 'SELECT count(id) as cnt FROM #__ccb_forums where published=1';
	    $db->setQuery($query);
        $boardsummary->forumcount = ($obj = $db->loadObject()) ? $obj->cnt : 0;

        $query = 'SELECT count(id) as cnt FROM #__ccb_topics where hold=0';
	    $db->setQuery($query);
        $boardsummary->topiccount = ($obj = $db->loadObject()) ? $obj->cnt : 0;

        $query = 'SELECT count(id) as cnt FROM #__ccb_posts  where hold=0';
	    $db->setQuery($query);
        $boardsummary->postcount = ($obj = $db->loadObject()) ? $obj->cnt : 0;

        $query = 'SELECT count(id) as cnt FROM #__users where block=0';
	    $db->setQuery($query);
        $boardsummary->usercount = ($obj = $db->loadObject()) ? $obj->cnt : 0;

        $query = 'SELECT id,username FROM #__users where (block=0) ORDER BY registerDate DESC LIMIT 0,5';
	    $db->setQuery($query);
        $objs = ($objs = $db->loadObjectList()) ? $objs : array();
        $boardsummary->latestusers = $objs;

        return $boardsummary;
    }


    function getSticky()
    {

    	// 0-Normal, 1-FrontPage, 2-Forum, 3-Global
    	$query = 'SELECT t.id, t.post_subject, t.topic_type, t.hits, ' .
    				't.forum_id, t.post_time, t.post_user, t.post_username ' .
        			'FROM #__ccb_topics AS t ' .
        			'WHERE ((t.topic_type = 1 OR t.topic_type = 3) AND (t.hold=0)) ' .
        			'ORDER BY t.topic_type, t.post_time DESC ';

		$sticky = ($sticky = $this->_getList($query))? $sticky :array();

		return $sticky;
    }

}

?>
