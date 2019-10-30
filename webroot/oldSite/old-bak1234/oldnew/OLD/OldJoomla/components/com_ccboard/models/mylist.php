<?php
/**
 * @version		$Id: mylist.php 131 2009-05-01 13:52:09Z thomasv $
 * @Project		ccBoard - Joomla! Bulletin Board Extension/Component
 * @author 		Thomas Varghese
 * @package		ccBoard
 * @copyright	Copyright (C) 2008-2009 codeclassic.org. All rights reserved.
 * @license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

defined( '_JEXEC' ) or die( 'Restricted Access' );
jimport( 'joomla.application.component.model' );

class ccboardModelMyList extends JModel
{

	var $_data;
    var $_limit;
    var $_limitstart;
    var $_viewmode;

	function ccboardModelMyList()
	{
		parent::__construct();
	}


    function setLimits($limitstart, $limit)
    {
        $this->_limitstart = $limitstart;
        $this->_limit = $limit;
    }

    function setViewMode( $mode )
    {
    	$this->_viewmode = $mode;
    }

    function getQuery()
    {
    	global $ccbConfig;
    	$query='';
    	$user = &JFactory::getUser();
    	if( $this->_viewmode == 'myposts') {
	        $query = 'SELECT p.topic_id, p.forum_id, p.post_subject, p.post_time, ' .
	        			't.topic_emoticon, t.hits, t.locked, f.forum_name ' .
	        			'FROM #__ccb_posts AS p ' .
	        			'INNER JOIN #__ccb_topics as t ON t.id = p.topic_id ' .
	        			'INNER JOIN #__ccb_forums as f ON f.id = p.forum_id ' .
	        			'WHERE p.hold=0 AND p.post_user = ' . $user->get('id') .
        				' ORDER BY p.id ' . $ccbConfig->postlistorder;
    	} elseif( $this->_viewmode == 'mysubs') {
	        $query = 'SELECT t.id AS topic_id, t.forum_id, t.post_subject, t.post_time, ' .
	        			't.topic_emoticon, t.hits, t.locked, f.forum_name ' .
	        			'FROM #__ccb_topics AS t ' .
	        			'INNER JOIN #__ccb_forums as f ON f.id = t.forum_id ' .
	        			'WHERE t.hold=0 AND INSTR(t.topic_email, "' . $user->get('id') . '") > 0 ' .
	        			'ORDER BY t.id ' . $ccbConfig->postlistorder;
    	} else {
	        $query = 'SELECT t.id AS topic_id, t.forum_id, t.post_subject, t.post_time, ' .
	        			't.topic_emoticon, t.hits, t.locked, f.forum_name ' .
	        			'FROM #__ccb_topics AS t ' .
	        			'INNER JOIN #__ccb_forums as f ON f.id = t.forum_id ' .
	        			'WHERE t.hold=0 AND INSTR(t.topic_favourite, "' . $user->get('id') . '") > 0 ' .
	        			'ORDER BY t.id ' . $ccbConfig->postlistorder;
    	}
    	return $query;
    }

    function getMyPosts()
    {
    	$query = $this->getQuery();
        $this->_db->setQuery($query, $this->_limitstart, $this->_limit);
        $this->_data = ($this->_data = $this->_db->loadObjectList())?$this->_data:array();
        return $this->_data;
    }

    function getTotal()
    {
    	$query='';
    	$user = &JFactory::getUser();

    	if( $this->_viewmode == 'myposts') {
	    	$query = 'SELECT COUNT(p.id) FROM #__ccb_posts AS p '.
	    				'WHERE p.hold=0 AND p.post_user = ' . $user->get('id');
    	} elseif( $this->_viewmode == 'mysubs') {
    		$query = 'SELECT COUNT(t.id) FROM #__ccb_topics AS t '.
    				 'WHERE t.hold=0 AND INSTR(t.topic_email, "' . $user->get('id') . '") > 0';
    	} else {
        	$query = 'SELECT COUNT(t.id) FROM #__ccb_topics AS t '.
			    	 'WHERE t.hold=0 AND INSTR(t.topic_favourite, "' . $user->get('id') . '") > 0';

    	}

        $this->_db->setQuery($query);
        return $this->_db->loadResult();
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
