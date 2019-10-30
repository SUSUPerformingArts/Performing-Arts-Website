<?php
/**
 * @version		$Id: ccbattachment.php 110 2009-04-26 07:59:21Z thomasv $
 * @Project		ccBoard - Joomla! Bulletin Board Extension/Component
 * @author 		Thomas Varghese
 * @package		ccBoard
 * @copyright	Copyright (C) 2008-2009 codeclassic.org. All rights reserved.
 * @license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

defined( '_JEXEC' ) or die( 'Restricted Access' );

class ccboardTableccbAttachment extends JTable
{
    var $id = null;
    var $post_id = null;
    var $forum_id = null;
    var $ccb_name = null;
    var $real_name = null;
    var $filesize = null;
    var $filetime = null;
    var $hits = null;
    var $comment = null;
    var $extension = null;
    var $mimetype = null;

    function  __construct(&$db) {
        parent::__construct( '#__ccb_attachments', 'id', $db );
    }


} //end class
?>
