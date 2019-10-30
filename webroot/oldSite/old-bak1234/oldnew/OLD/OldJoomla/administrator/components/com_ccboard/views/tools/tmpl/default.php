<?php
/**
 * @version		$Id: default.php 152 2009-06-20 17:26:28Z thomasv $
 * @Project		ccBoard - Joomla! Bulletin Board Extension/Component
 * @author 		Thomas Varghese
 * @package		ccBoard
 * @copyright	Copyright (C) 2008-2009 codeclassic.org. All rights reserved.
 * @license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

defined('_JEXEC') or die('Restricted access');
?>
<script type="text/javascript">
<!--
function processAction( action )
{
	var dv=document.getElementById("dvMain");
	dv.style.display = "none";
	dv=document.getElementById("dvSub");
	dv.style.display = "block";

	var form = document.adminForm;
	form.task.value=action;
	submitform();
}
//-->
</script>
<form action="<?php echo $this->action; ?>" method="post" name="adminForm" id="adminForm">
<div id="dvMain" name="dvMain" style="width:100%; float:left;">
	<table class="admintable" style="width:100%;">
		<tr>
			<td class="key" style="width:200px;"><?php echo JText::_( 'CCB_SYNC' ); ?>:</td>
			<td class="ccbgeneralvalue"><input type="button" value="<?php echo JText::_( 'CCB_BT_SYNC' ); ?>"  onclick="processAction('syncBoard');" /></td>
			<td class="ccbgeneraldesc"><?php echo JText::_( 'CCB_SYNC_DESCRIPTION' ); ?></td>
		</tr>
		<tr bgcolor="#F9F9FF">
			<td class="key"><?php echo JText::_( 'CCB_UPGRADE' ); ?>:</td>
			<td class="ccbgeneralvalue"><input type="button" value="<?php echo JText::_( 'CCB_BT_UPGRADE' ) . ' ' . $this->version; ?>" onclick="processAction('upgradeDB');" /></td>
			<td class="ccbgeneraldesc"><?php echo JText::_( 'CCB_UPGRADE_DESCRIPTION' ); ?></td>
		</tr>
		<tr bgcolor="#F9F9FF">
			<td class="key"><?php echo JText::_( 'CCB_MIRGATION' ); ?>:</td>
			<td class="ccbgeneralvalue">
				<select id="migrateCombo" name="migrateCombo">
					<option value="fireboard">FireBoard 1.0.x</option>
					<!-- option value="kunena">Kunena Forum 1.0.x</option>
					<option value="kunena">Frenzy!BB 1.5.x</option -->
				</select>
				<input type="button" value="<?php echo JText::_( 'CCB_BT_MIGRATION' ); ?>" onclick="processAction('migrateBoard');" />
			</td>
			<td class="ccbgeneraldesc"><?php echo JText::_( 'CCB_MIGRATION_DESCRIPTION' ); ?></td>
		</tr>
	</table>
</div>
<div id="dvSub" name="dvSub" style="display:none; width:100%; height: 300px; float:left; ">
	<br/><br/><br/><br/><br/><br/><br/>
	<div style="vertical-align:middle; text-align:center;">
		<img src="<?php echo CCBOARD_ASSETS_URL . '/wait.gif' ?>" />
		<br /><?php echo JText::_('CCB_PLEASE_WAIT'); ?><br />
		<br /><br /><br />
	</div>
</div>
<input type="hidden" name="task" id="task" value="" />
<br /><br /><br />
</form>

<?php echo CCBOARD_COPYRIGHT; ?>


