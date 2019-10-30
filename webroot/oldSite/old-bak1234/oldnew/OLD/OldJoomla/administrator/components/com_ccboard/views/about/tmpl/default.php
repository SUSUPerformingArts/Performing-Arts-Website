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
<table cellpadding="4" cellspacing="0" border="0" width="100%">
	<tr>
		<td style="font-weight: 700; text-align: right;">
			<?php echo JText::sprintf( 'Version: %1$s', $this->version ); ?>
		</td>
	</tr>
	<tr>
		<td width="100%">
			<img src="<?php echo CCBOARD_ASSETS_URL . '/logo.png'; ?>" />
			<font size="48" color="black">ccBoard</font>
		</td>
	</tr>
	<tr>
		<td>
			<blockquote>
				<p class="ccbheaderdesc"><br />
				ccBoard is created by The Team at www.codeclassic.org comprising of Thomas (Architect/Lead Developer), Alan, Amal and Tina (Documentation and others).</p>
				<p class="ccbheaderdesc"class="ccbheaderdesc">Please visit <a href="http://codeclassic.org">www.codeclassic.org</a> to find out more about us. </p>
				<p>&nbsp;</p>
			</blockquote>
		</td>
	</tr>
</table>
<?php echo CCBOARD_COPYRIGHT; ?>


