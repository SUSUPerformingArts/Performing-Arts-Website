<?php
/**
 * @version		$Id: categories.php 110 2009-04-26 07:59:21Z thomasv $
 * @Project		ccBoard - Joomla! Bulletin Board Extension/Component
 * @author 		Thomas Varghese
 * @package		ccBoard
 * @copyright	Copyright (C) 2008-2009 codeclassic.org. All rights reserved.
 * @license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

defined( '_JEXEC' ) or die( 'Restricted Access' );
jimport( 'joomla.application.component.model' );

class ccboardModelCategories extends JModel
{
    var $_data;
    var $_limit;
    var $_limitstart;
    var $_ordering;

	function ccboardModelCategories()
	{
		parent::__construct();
	}

    function setOrdering($ordering, $orderingDirection)
    {
        $this->_ordering = $ordering;
        switch ($this->_ordering) {
            case 'cat_name':
                $this->_ordering = 'a.cat_name';
                break;
            case 'id':
                $this->_ordering = 'a.id';
                break;
            case 'ordering':
                $this->_ordering = 'a.ordering';
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
        $query = 'SELECT a.id, a.cat_name, a.ordering
                  FROM #__ccb_category AS a
                  ORDER BY '.$this->_ordering  ;

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

        $query = 'SELECT count(*) as cntPost from #__ccb_forums WHERE cat_id IN ('.implode(',', $cid).')';
        $data = ($this->_data = $this->_getList($query))?$this->_data:array();
        $item = $data[0];
        if( $item->cntPost > 0 ) {
        	$this->setError(JText::_('FORUMS_EXISTS_FOR_THIS_CATEGORY'));
        	return false;
        }

        $query = 'DELETE FROM #__ccb_category WHERE id IN ('.implode(',', $cid).')';
        $this->_db->setQuery($query);

        if ($this->_db->query()) {
            return false;
        }

        return $this->_db->query();
    }


    function getTotal()
    {
        $query = 'SELECT COUNT(*)
                  FROM #__ccb_category';

        $this->_db->setQuery($query);
        return $this->_db->loadResult();
    }


    function saveOrder($cid, $orders)
    {
        foreach ($orders as $key => $value) {
            $cat = &JTable::getInstance('ccbcategory', 'ccboardTable');
            $cat->load($cid[$key]);
            $cat->ordering = $value;
            $cat->store();
        }
    }

    function move($cid, $direction)
    {
        $table = &JTable::getInstance('ccbcategory', 'ccboardTable');

        if (!$table->load($cid)) {
            $this->setError(JText::_('ERROR_COULD_NOT_LOAD_CATEGORY_TABLE'));
            return false;
        }
        $ret = $table->move($direction);
        if( $ret != '')  {
            $this->setError(JText::_('ERROR_COULD_NOT_MOVE_CATEGORY'));
            return false;
        }

        return true;
    }
}
?>
