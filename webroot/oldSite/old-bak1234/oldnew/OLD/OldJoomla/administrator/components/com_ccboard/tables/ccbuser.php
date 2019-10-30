<?php
/**
 * @version		$Id: ccbuser.php 110 2009-04-26 07:59:21Z thomasv $
 * @Project		ccBoard - Joomla! Bulletin Board Extension/Component
 * @author 		Thomas Varghese
 * @package		ccBoard
 * @copyright	Copyright (C) 2008-2009 codeclassic.org. All rights reserved.
 * @license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

defined( '_JEXEC' ) or die( 'Restricted Access' );
class ccboardTableccbUser extends JTable
{
    var $id = null;
	var $dob = null;
	var $location = null;
	var $signature = null;
	var $avatar = null;
	var $rank = null;
	var $post_count = null;
	var $gender = null;
	var $www = null;
	var $icq = null;
	var $aol = null;
	var $msn = null;
	var $yahoo = null;
	var $jabber = null;
	var $skype = null;
	var $thumb = null;
	var $user_id = null;
	var $showemail = 0;
	var $moderator = 0;
	var $karma = 0;
	var $karma_time = 0;
	var $hits = 0;

    function  __construct(&$db) {
	        parent::__construct( '#__ccb_users', 'id', $db );
    }

} //end class
?>
