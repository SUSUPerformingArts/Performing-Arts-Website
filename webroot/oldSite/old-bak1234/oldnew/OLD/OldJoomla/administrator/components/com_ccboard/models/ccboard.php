<?php
/**
 * @version		$Id: ccboard.php 110 2009-04-26 07:59:21Z thomasv $
 * @Project		ccBoard - Joomla! Bulletin Board Extension/Component
 * @author 		Thomas Varghese
 * @package		ccBoard
 * @copyright	Copyright (C) 2008-2009 codeclassic.org. All rights reserved.
 * @license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

defined( '_JEXEC' ) or die( 'Restricted Access' );
jimport( 'joomla.application.component.model' );

class ccboardModelccboard extends JModel
{
   	function ccboardModelccboard()
	{
		parent::__construct();
	}


    function getData()
    {
    	$item = new stdClass();

		$query = "SELECT count(id) from #__users";
	    $this->_db->setQuery($query);
        $item->totalusers =  $this->_db->loadResult();

        $query = "SELECT count(id) from #__ccb_forums";
	    $this->_db->setQuery($query);
        $item->totalforums =  $this->_db->loadResult();

        $query = "SELECT count(id) from #__ccb_topics";
	    $this->_db->setQuery($query);
        $item->totaltopics =  $this->_db->loadResult();

        $query = "SELECT count(id) from #__ccb_posts";
	    $this->_db->setQuery($query);
        $item->totalposts =  $this->_db->loadResult();

        return $item;
	}

}
?>
