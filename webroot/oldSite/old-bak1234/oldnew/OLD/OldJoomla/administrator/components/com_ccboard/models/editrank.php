<?php
/**
 * @version		$Id: editrank.php 110 2009-04-26 07:59:21Z thomasv $
 * @Project		ccBoard - Joomla! Bulletin Board Extension/Component
 * @author 		Thomas Varghese
 * @package		ccBoard
 * @copyright	Copyright (C) 2008-2009 codeclassic.org. All rights reserved.
 * @license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

defined( '_JEXEC' ) or die( 'Restricted Access' );
jimport( 'joomla.application.component.model' );

class ccboardModelEditRank extends JModel
{
    var $_data;
    var $_id;

    function ccboardModelEditRank()
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
                      FROM #__ccb_ranks AS a
                      WHERE a.id = '.(int)$this->_id
            ;
            $this->_db->setQuery($query);
            $this->_data = ($this->_data = $this->_db->loadObject())?$this->_data:array();
        }

        return $this->_data;
    }


    function store($data)
    {
        $row = $this->getTable('ccbranks', 'ccboardTable');
		$filter = new JFilterInput(array(), array(), 1, 1);
		$data['id'] 		= $filter->clean( $data['id'],'integer');
		$data['rank_title'] = $filter->clean( $data['rank_title'],'string');
        $data['rank_min'] 	= $filter->clean( $data['rank_min'],'integer');
        $data['rank_special'] = $filter->clean( $data['rank_special'],'integer');
        $data['rank_image'] = $filter->clean( $data['rank_image'],'string');

        if( $data['rank_special']) {
        	$data['rank_min'] = 0;
        }

        if( !$data['rank_special'] && $data['rank_min'] < 0 ) {
        	$this->setError(JText::_('CCB_INVALID_POST_MIN_COUNT'));
        	return false;
        }

        if( $data['rank_image'] == '' ) {
        	$this->setError(JText::_('CCB_INVALID_IMAGE'));
        	return false;
        }

        if (!$row->bind($data)) {
            $this->setError($row->getError());
            return false;
        }

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
