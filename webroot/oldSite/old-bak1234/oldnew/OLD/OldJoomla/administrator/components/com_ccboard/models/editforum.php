<?php
/**
 * @version		$Id: editforum.php 110 2009-04-26 07:59:21Z thomasv $
 * @Project		ccBoard - Joomla! Bulletin Board Extension/Component
 * @author 		Thomas Varghese
 * @package		ccBoard
 * @copyright	Copyright (C) 2008-2009 codeclassic.org. All rights reserved.
 * @license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

defined( '_JEXEC' ) or die( 'Restricted Access' );
jimport( 'joomla.application.component.model' );

class ccboardModelEditForum extends JModel
{
    var $_data;
    var $_id;

    function ccboardModelEditForum()
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
                      FROM #__ccb_forums AS a
                      WHERE a.id = '.(int)$this->_id
            ;
            $this->_db->setQuery($query);
            $this->_data = ($this->_data = $this->_db->loadObject())?$this->_data:array();
        }

        return $this->_data;
    }

    function getOrderQuery()
    {
        return 'SELECT a.id AS value, a.forum_name AS text
                FROM #__ccb_forums AS a
                ORDER BY a.ordering';
    }

	function getCategories()
    {
        $query = 'SELECT a.id, a.cat_name
                  FROM #__ccb_category AS a
                  ORDER BY a.cat_name' ;
		$this->_db->setQuery($query);
		$data = array();
		$data = ($data = $this->_db->loadObjectList())?$data:array();
		return $data;
    }

    function getModerators()
    {
        $query = 'SELECT m.user_id, u.name, u.username, u.email, u.usertype, u.block
                  FROM #__ccb_moderators AS m
                  INNER JOIN #__users AS u on m.user_id = u.id
                  WHERE m.forum_id = ' . (int)$this->_id  . ' ORDER BY u.username' ;
		$this->_db->setQuery($query);
		$data = array();
		$data = ($data = $this->_db->loadObjectList())?$data:array();
		return $data;
    }

    function store($data)
    {
        require_once(JPATH_COMPONENT.DS.'elements'.DS.'permissions.php');
    	$row = $this->getTable('ccbforum', 'ccboardTable');

        $filter = new JFilterInput(array(), array(), 1, 1);
        $data['id'] 			= $filter->clean($data['id'],'integer');
		$data['forum_name'] 	= $filter->clean($data['forum_name'],'string');
        $data['forum_desc'] 	= $filter->clean($data['forum_desc'],'string');
        $data['cat_id']			= $filter->clean($data['cat_id'],'integer');
        $data['published']		= $filter->clean($data['published'],'integer');
        $data['locked']			= $filter->clean($data['locked'],'integer');
        $data['moderated']		= $filter->clean($data['moderated'],'integer');
        $data['review']			= $filter->clean($data['review'],'integer');
        $data['view_for'] 		= $filter->clean($data['view_for'],'integer');
        $data['post_for'] 		= $filter->clean($data['post_for'],'integer');
        $data['moderate_for']	= 0; /* Depricated */
        $data['ordering'] 		= $filter->clean($data['ordering'],'integer');


        if( $data['cat_id'] < 1 )
        {
        	$this->setError(JText::_('CCB_INVALID_CATEGORY')) ;
            return false;
        }

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
        if( !$row->moderated ) {
			$query = 'DELETE FROM #__ccb_moderators WHERE forum_id = ' . $row->id;
			$this->_db->setQuery($query);
			$this->_db->query();
        }
        return true;
    }

}
?>
