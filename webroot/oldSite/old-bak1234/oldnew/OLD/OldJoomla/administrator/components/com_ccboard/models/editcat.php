<?php
/**
 * @version		$Id: editcat.php 110 2009-04-26 07:59:21Z thomasv $
 * @Project		ccBoard - Joomla! Bulletin Board Extension/Component
 * @author 		Thomas Varghese
 * @package		ccBoard
 * @copyright	Copyright (C) 2008-2009 codeclassic.org. All rights reserved.
 * @license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

defined( '_JEXEC' ) or die( 'Restricted Access' );
jimport( 'joomla.application.component.model' );

class ccboardModelEditCat extends JModel
{
    var $_data;
    var $_id;

    function ccboardModelEditCat()
	{
		parent::__construct();
	}

    function setId($id)
    {
        $this->_id = $id;
        $this->_data = null;
    }

    function getData()
    {
        if (empty($this->_data)) {
            $query = 'SELECT a.*
                      FROM #__ccb_category AS a
                      WHERE a.id = '.(int)$this->_id
            ;
            $this->_db->setQuery($query);
            $this->_data = ($this->_data = $this->_db->loadObject())?$this->_data:array();
        }

        return $this->_data;
    }

    function getOrderQuery()
    {
        return 'SELECT a.id AS value, a.cat_name AS text
                FROM #__ccb_category AS a
                ORDER BY a.ordering';
    }



    function store($data)
    {
        $row = $this->getTable('ccbcategory', 'ccboardTable');
		$filter = new JFilterInput(array(), array(), 1, 1);
		$data['id'] 		= $filter->clean( $data['id'],'integer');
		$data['cat_name'] 	= $filter->clean( $data['cat_name'],'string');

		if (!$row->bind($data)) {
            $this->setError($row->getError());
            return false;
        }

		if( $row->ordering < 1)  $row->ordering = $row->getNextOrder();

        if (!$row->check()) {
            $this->setError($row->getError());
            return false;
        }

        if (!$row->store(true)) {
            $this->setError($row->getError());
            return false;
        }

        return true;
    }

}
?>
