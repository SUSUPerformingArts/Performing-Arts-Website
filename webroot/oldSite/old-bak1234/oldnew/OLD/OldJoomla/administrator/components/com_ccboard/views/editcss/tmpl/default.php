<?php
/**
 * @version		$Id: default.php 110 2009-04-26 07:59:21Z thomasv $
 * @Project		ccBoard - Joomla! Bulletin Board Extension/Component
 * @author 		Thomas Varghese
 * @package		ccBoard
 * @copyright	Copyright (C) 2008-2009 codeclassic.org. All rights reserved.
 * @license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

	defined('_JEXEC') or die('Restricted access');
?>
<form action="<?php echo $this->action; ?>" method="post" name="adminForm" id="adminForm">
<div style="width:100%;float:left;">
	<h3><?php echo $this->filename; ?></h3>
	<textarea style="width:100%; height:500px" cols="110" rows="25" name="filecontent" class="inputbox"><?php echo $this->content; ?></textarea>
</div>
<?php echo CCBOARD_COPYRIGHT; ?>
<input type="hidden" name="option" value="com_ccboard" />
<input type="hidden" name="task" value="" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>
