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
<script language="javascript" type="text/javascript">
function submitbutton(pressbutton) {
    var form = document.adminForm;
    if (pressbutton == 'cancelForum') {
        submitform( pressbutton );
        return;
    }

    // do field validation
    if (form.forum_name.value.length == 0) {
        alert( "<?php echo JText::_( 'CCB_THE_FORUM_CAN_NOT_BE_BLANK', true ); ?>" );
    } else {
        submitform( pressbutton );
    }
}
</script>
<form action="<?php echo $this->action; ?>" method="post" name="adminForm" id="adminForm">
<div style="width:50%;float:left;">
<fieldset class="adminform">
    <legend><?php echo JText::_( 'CCB_BASIC' ); ?></legend>
    <table class="admintable">
        <tr>
            <td class="key">
                <label for="forum_name">
                    <?php echo JText::_( 'CCB_FORUM' ); ?>:
                </label>
            </td>
            <td>
                <input class="text_area" type="text" name="forum_name" id="forum_name" size="50" maxlength="100" value="<?php echo isset($this->item->forum_name)?$this->item->forum_name:'';?>" />
            </td>
        </tr>
        <tr>
            <td class="key" valign="top">
                <label for="forum_desc">
                    <?php echo JText::_( 'CCB_DESCRIPTION' ); ?>:
                </label>
            </td>
            <td>
                <textarea id="forum_desc" class="text_area" name="forum_desc" rows="5" cols="40"><?php echo isset($this->item->forum_desc)?$this->item->forum_desc:''; ?></textarea>
            </td>
        </tr>
        <tr>
            <td class="key">
                <label for="cat_id">
                    <?php echo JText::_( 'CCB_CATEGORY' ); ?>:
                </label>
            </td>
            <td><?php echo $this->catcombo; ?></td>
        </tr>
        <tr>
            <td class="key" valign="top">
                <label for="published">
                    <?php echo JText::_('CCB_PUBLISHED'); ?>:
                </label>
            </td>
            <td>
		        <?php
		      		echo $this->publishedradio;
		        ?>
		     </td>
		</tr>
        <tr>
            <td class="key" valign="top">
                <label for="Locked">
                    <?php echo JText::_('CCB_LOCKED'); ?>:
                </label>
            </td>
            <td>
		        <?php
		      		echo $this->lockradio;
		        ?>
		     </td>
		</tr>
        <tr>
            <td class="key">
                <label for="ordering">
                    <?php echo JText::_('CCB_ORDER'); ?>:
                </label>
            </td>
            <td>
                <?php echo $this->ordering; ?>
            </td>
        </tr>
    </table>
</fieldset>
</div>
<div style="width:49%; float: right;">
	<fieldset class="adminform">
	    <legend><?php echo JText::_( 'CCB_ADVANCED' ); ?></legend>
	    <table class="admintable">
	        <tr>
	            <td class="key" valign="top">
	                <label for="viewfor_for">
	                    <?php echo JText::_('CCB_VIEW_ACCESS'); ?>:
	                </label>
	            </td>
	            <td><?php echo $this->viewfor; ?></td>
	        </tr>
	        <tr>
	            <td class="key" valign="top">
	                <label for="postfor_for">
	                    <?php echo JText::_('CCB_POST_ACCESS'); ?>:
	                </label>
	            </td>
	            <td><?php echo $this->postfor; ?></td>
	        </tr>
	        <tr>
	            <td class="key" valign="top">
	                <label for="published">
	                    <?php echo JText::_('CCB_MODERATED'); ?>:
	                </label>
	            </td>
	            <td>
			        <?php
			      		echo $this->moderatedradio;
			        ?>
			     </td>
			</tr>
	        <tr>
	            <td class="key" valign="top">
	                <label for="Locked">
	                    <?php echo JText::_('CCB_REVIEW'); ?>:
	                </label>
	            </td>
	            <td>
			        <?php
			      		echo $this->reviewradio;
			        ?>
			     </td>
			</tr>
		</table>
	</fieldset>
</div>
<div style="width:100%;float:left; margin-bottom: 15px;">
	<fieldset>
		<legend><?php echo JText::_('CCB_FORUM_MODERATORS'); ?></legend>
	<table class="adminlist">
		<thead>
			<tr>
				<th width="5" style="text-align:center;">#</th>
				<th style="text-align:left;"><?php echo JText::_('CCB_USERNAME'); ?></th>
				<th style="text-align:left;"><?php echo JText::_('CCB_NAME'); ?></th>
				<th style="text-align:center;" nowrap="nowrap"><?php echo JText::_('CCB_BLOCK'); ?></th>
				<th style="text-align:left;"><?php echo JText::_('CCB_EMAIL'); ?></th>
				<th style="text-align:center;" nowrap="nowrap"><?php echo JText::_('CCB_GROUP'); ?></th>
				<th style="width:10px; text-align:left;"><?php echo JText::_('CCB_ID'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
		$cntr=1;
		foreach ($this->moderators as $moduser) { ?>
			<tr>
				<td style="text-align:center;"><?php echo $cntr; ?></td>
				<td style="text-align:left;"><?php echo $moduser->username; ?></td>
				<td style="text-align:left;"><?php echo $moduser->name; ?></td>
				<td style="text-align:center;"><img src="images/<?php echo $moduser->block?'tick.png':'publish_x.png'; ?>" width="16" height="16" border="0" /></td>
				<td style="text-align:left;"><?php echo $moduser->email; ?></td>
				<td style="text-align:center;"><?php echo $moduser->usertype; ?></td>
				<td style="text-align:center;"><?php echo $moduser->user_id; ?></td>
			</tr>
		<?php
			$cntr++;
		}?>
		</tbody>
	</table>
</fieldset>
</div>
<input type="hidden" name="option" value="com_ccboard" />
<input type="hidden" name="id" value="<?php echo isset($this->item->id)?$this->item->id:''; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="topic_count" value="<?php echo isset($this->item->topic_count)?$this->item->topic_count:''; ?>" />
<input type="hidden" name="post_count" value="<?php echo isset($this->item->post_count)?$this->item->post_count:''; ?>" />
<input type="hidden" name="last_post_user" value="<?php echo isset($this->item->last_post_user)?$this->item->last_post_user:''; ?>" />
<input type="hidden" name="last_post_username" value="<?php echo isset($this->item->last_post_username)?$this->item->last_post_username:''; ?>" />
<input type="hidden" name="last_post_time" value="<?php echo isset($this->item->last_post_time)?$this->item->last_post_time:''; ?>" />
<input type="hidden" name="last_post_id" value="<?php echo isset($this->item->last_post_id)?$this->item->last_post_id:''; ?>" />
</form>

<?php echo CCBOARD_COPYRIGHT; ?>
