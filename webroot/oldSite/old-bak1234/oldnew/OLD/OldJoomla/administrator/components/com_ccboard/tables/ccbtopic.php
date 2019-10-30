<?php
/**
 * @version		$Id: ccbtopic.php 110 2009-04-26 07:59:21Z thomasv $
 * @Project		ccBoard - Joomla! Bulletin Board Extension/Component
 * @author 		Thomas Varghese
 * @package		ccBoard
 * @copyright	Copyright (C) 2008-2009 codeclassic.org. All rights reserved.
 * @license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

defined( '_JEXEC' ) or die( 'Restricted Access' );

class ccboardTableccbTopic extends JTable
{
    var $id = null;
    var $forum_id = null;
    var $post_subject = null;
	var $post_time = null;
	var $post_user = null;
	var $reply_count = null;
	var $hits = null;
    var $last_post_time = null;
    var $last_post_id = null;
    var $last_post_user = null;
    var $start_post_id = null;
    var $topic_type = 0;
    var $locked = 0;
	var $topic_email = null;
	var $hold = 0;
	var $topic_emoticon = 0;
	var $post_username = null;
	var $last_post_username = null;
	var $topic_favourite=null;

    function  __construct(&$db) {
	        parent::__construct( '#__ccb_topics', 'id', $db );
    }

} //end class
?>
