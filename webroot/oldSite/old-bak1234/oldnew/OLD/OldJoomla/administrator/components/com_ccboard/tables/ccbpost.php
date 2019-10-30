<?php
/**
 * @version		$Id: ccbpost.php 110 2009-04-26 07:59:21Z thomasv $
 * @Project		ccBoard - Joomla! Bulletin Board Extension/Component
 * @author 		Thomas Varghese
 * @package		ccBoard
 * @copyright	Copyright (C) 2008-2009 codeclassic.org. All rights reserved.
 * @license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

defined( '_JEXEC' ) or die( 'Restricted Access' );

class ccboardTableccbPost extends JTable
{
    var $id = null;
    var $topic_id = null;
    var $forum_id = null;
    var $post_subject = null;
    var $post_text = null;
    var $post_user = null;
    var $post_time = null;
    var $ip = null;
    var $hold = 0;
    var $modified_by = 0;
    var $modified_time = 0;
    var $modified_reason = null;
    var $post_username = null;

    function  __construct(&$db) {
            parent::__construct( '#__ccb_posts', 'id', $db );
    }

    function check()
    {
        if (isset($this->post_text) && !strlen($this->post_text)) {
            $this->setError(JText::_('ERROR_POST_CAN_NOT_BE_BLANK'));
            return false;
        }

        if (isset($this->post_subject) && !strlen($this->post_subject)) {
            $this->setError(JText::_('ERROR_SUBJECT_CAN_NOT_BE_BLANK'));
            return false;
        }

        return parent::check();
    } //end check

} //end class
?>
