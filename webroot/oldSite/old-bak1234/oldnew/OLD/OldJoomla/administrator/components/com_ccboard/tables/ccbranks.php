<?php
/**
 * @version		$Id: ccbranks.php 110 2009-04-26 07:59:21Z thomasv $
 * @Project		ccBoard - Joomla! Bulletin Board Extension/Component
 * @author 		Thomas Varghese
 * @package		ccBoard
 * @copyright	Copyright (C) 2008-2009 codeclassic.org. All rights reserved.
 * @license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

defined( '_JEXEC' ) or die( 'Restricted Access' );

class ccboardTableccbRanks extends JTable
{
    var $id = null;
    var $rank_title = null;
    var $rank_min = null;
	var $rank_special = null;
	var $rank_image = null;

    function  __construct(&$db) {
	        parent::__construct( '#__ccb_ranks', 'id', $db );
    }

} //end class
?>
