<?php
/**
 * @version		$Id: users.php 119 2009-05-01 08:05:15Z thomasv $
 * @Project		ccBoard - Joomla! Bulletin Board Extension/Component
 * @author 		Thomas Varghese
 * @package		ccBoard
 * @copyright	Copyright (C) 2008-2009 codeclassic.org. All rights reserved.
 * @license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

defined( '_JEXEC' ) or die( 'Restricted Access' );
jimport( 'joomla.application.component.model' );

class ccboardModelUsers extends JModel
{
    var $_data;
    var $_limit;
    var $_limitstart;
    var $_ordering;

	function ccboardModelUsers()
	{
		parent::__construct();
	}

    function setOrdering($ordering, $orderingDirection)
    {
        $this->_ordering = $ordering;
        switch ($this->_ordering) {
            case 'name':
                $this->_ordering = 'a.name';
                break;
           	case 'username':
                $this->_ordering = 'a.username';
                break;
            case 'block':
                $this->_ordering = 'a.block';
                break;
            case 'usertype':
                $this->_ordering = 'a.usertype';
                break;
            case 'rank':
                $this->_ordering = 'b.moderator';
                break;
            case 'email':
                $this->_ordering = 'a.email';
                break;
            case 'post_count':
                $this->_ordering = 'b.post_count';
                break;
            case 'lastvisitdate':
                $this->_ordering = 'a.lastvisitDate';
                break;
            default:
                $this->_ordering = 'a.id';
                break;
        }

        $this->_ordering .= $orderingDirection == 'desc'?' DESC':' ASC';
    }

    function setLimits($limitstart, $limit)
    {
        $this->_limitstart = $limitstart;
        $this->_limit = $limit;
    }

    function _buildQuery( $where )
    {
        $query = 'SELECT a.*, b.moderator, b.post_count '.
                  'FROM #__users  AS a '.
        		  'LEFT JOIN #__ccb_users AS b ON b.user_id = a.id ';
        if( isset( $where ) && $where != '') {
        	$query .= ' WHERE ' . $where ;
    	}
        $query .= ' ORDER BY '.$this->_ordering  ;

        return $query;
    }

    function getData( $where )
    {
        if (empty($this->_data)) {
            $query = $this->_buildQuery( $where );
            $this->_data = $this->_getList($query, $this->_limitstart, $this->_limit);
        }
        return $this->_data;

    }

    function getTotal( $where )
    {
        $query = 'SELECT COUNT(*) FROM #__users ';
        if( isset( $where ) && $where != '') {
        	$query .= ' WHERE ' . $where ;
    	}

        $this->_db->setQuery($query);
        return $this->_db->loadResult();
    }


}
?>
