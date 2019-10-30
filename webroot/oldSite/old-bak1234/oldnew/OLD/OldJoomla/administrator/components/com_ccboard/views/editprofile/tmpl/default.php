<?php
/**
 * @version		$Id: default.php 124 2009-05-01 09:05:56Z thomasv $
 * @Project		ccBoard - Joomla! Bulletin Board Extension/Component
 * @author 		Thomas Varghese
 * @package		ccBoard
 * @copyright	Copyright (C) 2008-2009 codeclassic.org. All rights reserved.
 * @license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

	defined('_JEXEC') or die('Restricted access');
	$config = new ccboardConfig();
?>
<script language="javascript" type="text/javascript">

var _CCB_JS_EDITOR_PATH = "<?php echo $this->path;?>components/com_ccboard/assets/ccbeditor";

function submitbutton(pressbutton) {
    if (pressbutton == 'cancelProfile') {
        submitform( pressbutton );
        return;
    }
    submitform( pressbutton );
}
</script>
<?php echo '<script src="' . $this->path . 'components/com_ccboard/assets/ccbeditor/ed.js" language="javascript" type="text/javascript"></script>'; ?>

<form action="<?php echo $this->action; ?>" method="post" name="adminForm" id="adminForm">
<div style="width: 100%;">
<fieldset class="adminform">
    <legend><?php echo $this->labels['pagetitle']; ?></legend>
    <table class="admintable">
        <tr>
            <td class="key"><label for="Name"><?php echo $this->labels['name']; ?>:</label></td>
            <td><b><?php echo $this->item['name']; ?></b></td>
        </tr>
        <tr>
            <td class="key"><label for="UserName"><?php echo $this->labels['username']; ?>:</label></td>
            <td><b><?php echo $this->item['username']; ?></b></td>
        </tr>
        <tr>
            <td class="key"><label for="usertype"><?php echo $this->labels['group']; ?>:</label></td>
            <td><b><?php echo $this->item['usertype']; ?></b></td>
        </tr>
        <tr>
            <td class="key"><?php echo $this->labels['avatar']; ?>:</td>
            <td><img src="<?php echo $this->item['avatar']; ?>" style="height:<?php echo $config->avatarheight; ?>px; width: <?php echo $config->avatarwidth; ?>px;" /></td>
        </tr>
        <tr>
            <td class="key"><label for="location"><?php echo $this->labels['location']; ?>:</label></td>
            <td>
            	<input class="text_area" type="text" name="location" id="location" size="45" maxlength="45" value="<?php echo $this->item['location']; ?>"/>
            </td>
        </tr>
        <tr>
            <td class="key" valign="top">
                <label for="forum_desc"><?php echo $this->labels['signature']; ?></label>
            </td>
            <td>
            	<div class="ccbusereditor">
					<?php echo '<script> edToolbar("signature", 1); </script>'; ?>
	                <textarea id="signature" class="text_area" name="signature" rows="5" cols="50"><?php echo $this->item['signature']; ?></textarea>
				</div>
            </td>
        </tr>
        <tr>
            <td class="key"  valign="top"><label for="rank"><?php echo $this->labels['rank_title']; ?>:</label></td>
			<td align="left"><?php echo $this->ranklist; ?></td>
        </tr>
        <tr>
            <td class="key"  valign="top"><label for="moderator"><?php echo $this->labels['moderator']; ?>:</label></td>
			<td align="left"><?php echo $this->moderatorradio; ?></td>
        </tr>
        <tr>
            <td class="key"  valign="top"><label for="selections"><?php echo $this->labels['forums']; ?>:</label></td>
			<td align="left"><?php echo $this->forumlist; ?></td>
        </tr>

    </table>
</fieldset>
</div>
<input type="hidden" name="option" id="option" value="com_ccboard" />
<input type="hidden" name="id" id="id" value="<?php echo $this->item['id']; ?>" />
<input type="hidden" name="user_id" id="user_id" value="<?php echo $this->item['user_id']; ?>" />
<input type="hidden" name="<?php echo JUtility::getToken(); ?>" value="1" />
<input type="hidden" name="task" id="task" value="" />
</form>
<?php echo CCBOARD_COPYRIGHT; ?>
