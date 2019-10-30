<?php
/**
 * @version		$Id: recentlist.php 162 2009-08-22 18:33:26Z thomasv $
 * @Project		ccBoard - Joomla! Bulletin Board Extension/Component
 * @author 		Thomas Varghese
 * @package		ccBoard
 * @copyright	Copyright (C) 2008-2009 codeclassic.org. All rights reserved.
 * @license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

defined( '_JEXEC' ) or die( 'Restricted Access' );
jimport( 'joomla.application.component.model' );

class ccboardModelRecentList extends JModel
{
    var $_data;
    var $_limit;
    var $_limitstart;
    var $_myList=0;

	function ccboardModelRecentList()
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
    	global $ccbConfig;
    	$user = &JFactory::getUser();
        $where = array(
        	'p.hold = 0 ',
        	'f.view_for <= ' . $user->get('gid'),
            'f.published = 1'
        );

        $order = 'p.id DESC ';

        $query = 'SELECT p.id, p.topic_id, p.forum_id, p.post_subject, p.post_text, '.
        			'p.post_user, p.post_username, p.post_time, p.ip, ' .
        			'p.modified_by, p.modified_time, p.modified_reason, f.forum_name, t.post_subject AS topic_subject ' .
        			'FROM #__ccb_posts AS p ' .
        			'INNER JOIN #__ccb_forums AS f ON f.id = p.forum_id ' .
        			'INNER JOIN #__ccb_topics AS t ON t.id = p.topic_id ' .
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


    function getTotal()
    {
    	$user = &JFactory::getUser();
        $where = array(
        	'p.hold = 0 ',
        	'f.view_for <= ' . $user->get('gid'),
            'f.published = 1'
        );
    	$query = 'SELECT COUNT(p.id) ' .
        		'FROM #__ccb_posts AS p ' .
        		'INNER JOIN #__ccb_forums AS f ON f.id = p.forum_id ' .
				'INNER JOIN #__ccb_topics AS t ON t.id = p.topic_id ' .
                'WHERE ' .implode(' AND ', $where);
        $this->_db->setQuery($query);
        return $this->_db->loadResult();
    }

	function getAttachments( $post_id )
	{
		$query = 'SELECT *
                  FROM #__ccb_attachments WHERE post_id = ' . (int) $post_id;
		$this->_db->setQuery($query);
		$attachments = ($attachments = $this->_db->loadObjectList()) ? $attachments:array();
		$retArr = array();
		foreach ($attachments as $item) {
			$elemAttach = array( $item->ccb_name , $item->real_name, $item->extension, $item->filesize, $item->filetime, $item->comment);
			$retArr[] = $elemAttach;
		}
		return $retArr;
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

    function getPageNumber($topic_id, $post_id)
    {
        $ccbConfig = new ccboardConfig();
        $pagelink = "";
        $mainframe = &JFactory::getApplication();
	    $db = &JFactory::getDBO();
	    $query = 'SELECT p.id FROM #__ccb_posts p ' .
                    'WHERE p.topic_id = ' . $topic_id .  ' ' .
                    'ORDER BY p.id '. $ccbConfig->postlistorder;

	    $db->setQuery($query);
        $items = ($items = $db->loadObjectList())?$items:array();
        $pgnum = 0;
        $pglimit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
        $pcnt = 0;
        foreach ($items as $item) {
            if( $item->id == $post_id ) break;
            $pcnt++;
            if( $pcnt == $pglimit) {
                $pcnt = 0;
                $pgnum += $pglimit;
            }
        }
        if( $pgnum > 0 ) {
            $pagelink = "&limitstart=" . $pgnum . "#ccbp".$post_id;
        }
        else {
            $pagelink = "#ccbp".$post_id;
        }
        return $pagelink;
    }

}
?>
