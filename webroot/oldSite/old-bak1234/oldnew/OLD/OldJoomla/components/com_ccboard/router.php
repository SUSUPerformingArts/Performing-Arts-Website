<?php
/**
 * @version		$Id: router.php 109 2009-04-26 07:50:55Z thomasv $
 * @Project		ccBoard - Joomla! Bulletin Board Extension/Component
 * @author 		Thomas Varghese
 * @package		ccBoard
 * @copyright	Copyright (C) 2008-2009 codeclassic.org. All rights reserved.
 * @license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2

 * @Note: if anyone wishes to implement this simple but effective piece of Art, please do keep the copyright headers
*/

defined( '_JEXEC' ) or die( 'Restricted Access' );

function ccboardBuildRoute(&$query)
{
	JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ccboard'.DS.'tables');
	$frow = &JTable::getInstance('ccbforum', 'ccboardTable');
	$trow = &JTable::getInstance('ccbtopic', 'ccboardTable');

	$segments = array();
	foreach( $query as $key => $elem ) {
		if( strtolower($key) <> 'option' && strtolower($key) <> 'itemid' && strtolower($key) <> 'start') {
			if( $key == 'forum' && $frow ) {
				if( $frow->load( $elem ) ) {
					$elem  .= '-'. JFilterOutput::stringURLSafe($frow->forum_name);
				}
			}
			if( $key == 'topic' && $trow  ) {
				if( $trow->load( $elem ) ) {
					$elem  .= '-' . JFilterOutput::stringURLSafe($trow->post_subject);
				}
			}
			if( !in_array(($key .  ':' . $elem), $segments)) {
				$segments[] = $key . ':' . $elem ;
			}
			unset($query[$key]);
		}
	}

	return $segments;
}

function ccboardParseRoute($segments)
{
	$vars = array();

	foreach( $segments as $elem) {
		$sp = explode(':', $elem);
		$sp2 = explode('-',$sp[1]);
		$vars[ $sp[0] ] = $sp2[0];
	}

	return $vars;
}
?>
