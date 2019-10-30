<?php
/**
 * @version		$Id: ranks.php 110 2009-04-26 07:59:21Z thomasv $
 * @Project		ccBoard - Joomla! Bulletin Board Extension/Component
 * @author 		Thomas Varghese
 * @package		ccBoard
 * @copyright	Copyright (C) 2008-2009 codeclassic.org. All rights reserved.
 * @license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

defined( '_JEXEC' ) or die( 'Restricted Access' );
jimport( 'joomla.application.component.model' );

class ccboardModelRanks extends JModel
{
    var $_data;
    var $_limit;
    var $_limitstart;
    var $_ordering;

	function ccboardModelRanks()
	{
		parent::__construct();
	}

    function setOrdering($ordering, $orderingDirection)
    {
        $this->_ordering = $ordering;
        switch ($this->_ordering) {
            case 'rank_title':
                $this->_ordering = 'a.rank_title';
                break;
            case 'id':
                $this->_ordering = 'a.id';
                break;
            case 'rank_min':
                $this->_ordering = 'a.rank_min';
                break;
            default:
                $this->_ordering = 'a.rank_min';
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
        $query = 'SELECT a.id, a.rank_title, a.rank_min, a.rank_special, a.rank_image ' .
                  'FROM #__ccb_ranks AS a ' .
                  'ORDER BY '.$this->_ordering  ;
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

    function delete($cid)
    {
        if (!$cid || !is_array($cid)) {
            return false;
        }

        $query = 'SELECT count(*) as cntPost from #__ccb_users WHERE rank IN (' . implode(',', $cid).')';
        $data = ($this->_data = $this->_getList($query))?$this->_data:array();
        $item = $data[0];
        if( $item->cntPost > 0 ) {
        	$this->setError(JText::_('CCB_USERS_EXISTS_FOR_THIS_RANK'));
        	return false;
        }

        $query = 'DELETE FROM #__ccb_ranks WHERE id IN ('.implode(',', $cid).')';
        $this->_db->setQuery($query);

        return $this->_db->query();
    }

    function toggleRank_Special($cid)
    {
        if (!$cid || !is_array($cid)) {
            return false;
        }

        foreach( $cid as $did ) {
	        $ranks = &JTable::getInstance('ccbranks', 'ccboardTable');
			if( $ranks->load( $did )) {
				$ranks->rank_special = $ranks->rank_special ? 0 : 1;
				$ranks->rank_min = $ranks->rank_special ? 0 : $ranks->rank_min;
	            $ranks->store();
			} else {
				$this->setError( $ranks->getError());
				return false;
	        }
        }
        return true;
    }


    function getTotal()
    {
        $query = 'SELECT COUNT(*) FROM #__ccb_ranks';
        $this->_db->setQuery($query);
        return $this->_db->loadResult();
    }

}
?>
