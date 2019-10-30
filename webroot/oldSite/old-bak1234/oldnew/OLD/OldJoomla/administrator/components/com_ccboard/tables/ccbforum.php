<?php
/**
 * @version		$Id: ccbforum.php 118 2009-05-01 07:46:59Z thomasv $
 * @Project		ccBoard - Joomla! Bulletin Board Extension/Component
 * @author 		Thomas Varghese
 * @package		ccBoard
 * @copyright	Copyright (C) 2008-2009 codeclassic.org. All rights reserved.
 * @license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

defined( '_JEXEC' ) or die( 'Restricted Access' );

class ccboardTableccbForum extends JTable
{
    var $id = null;
	var $forum_name = null;
	var $forum_desc = null;
	var $topic_count = null;
	var $post_count = null;
	var $last_post_user = null;
	var $last_post_time = null;
	var $last_post_id = null;
	var $cat_id = null;
	var $ordering = null;
	var $published = null;
	var $locked = null;
	var $view_for = null;
	var $post_for = null;
	var $moderate_for = null;  /* Depricated */
	var $forum_image = null;
	var $moderated = null;
	var $review = null;

    function  __construct(&$db) {
        parent::__construct('#__ccb_forums',  'id', $db);
    }

}
?>
