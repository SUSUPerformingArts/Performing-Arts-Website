<?php
/**
 * @version		$Id: default.php 171 2009-09-21 14:36:52Z thomasv $
 * @Project		ccBoard - Joomla! Bulletin Board Extension/Component
 * @author 		Thomas Varghese
 * @package		ccBoard
 * @copyright	Copyright (C) 2008-2009 codeclassic.org. All rights reserved.
 * @license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/
	defined('_JEXEC') or die('Restricted access');
	$elem = $this->item;
?>
<fieldset>
	<p class="ccbsubheader">
		<?php echo JText::_('CCB_GLOBAL_HEADER_DESCRIPTION'); ?>
	</p>
</fieldset>

<form action="<?php echo $this->action; ?>" method="post" name="adminForm" id="adminForm">
<div class="ccbgendiv">
		<?php
			echo $this->pane->startPane( 'stat-pane' );
			echo $this->pane->startPanel( JText::_('CCB_GENERAL_BASIC'), 'basic' );
		?>
            <table class="admintable" style="width:100%;">
	        <tr>
	            <td class="key" style="width:200px;">
	                <label for="boardname"><?php echo JText::_( 'CCB_BOARD_NAME' ); ?>:</label>
	            </td>
	            <td class="ccbgeneralvalue">
	                <input class="text_area" type="text" name="boardname" id="boardname" size="70" maxlength="50" value="<?php echo $elem->boardname; ?>" />
	            </td>
	            <td class="ccbgeneraldesc"><?php echo JText::_( 'CCB_BOARD_NAME_DESCRIPTION' ); ?></td>
	        </tr>
	        <tr bgcolor="#F9F9FF">
	            <td class="key">
	                <label for="locked"><?php echo JText::_( 'CCB_BOARD_OFFLINE' ); ?>:</label>
	            </td>
	            <td>
	                <?php echo $this->lockradio; ?>
	            </td>
	            <td class="ccbgeneraldesc"><?php echo JText::_( 'CCB_BOARD_OFFLINE_DESCRIPTION' ); ?></td>
	        </tr>
	        <tr>
	            <td class="key">
	                <label for="lockedmsg"><?php echo JText::_( 'CCB_BOARD_OFFLINE_MESSAGE' ); ?>:</label>
	            </td>
	            <td>
	                <textarea id="lockedmsg" class="text_area" name="lockedmsg" rows="3" cols="40"><?php echo $elem->lockedmsg; ?></textarea>
	            </td>
	            <td class="ccbgeneraldesc"><?php echo JText::_( 'CCB_BOARD_OFFLINE_MESSAGE_DESCRIPTION' ); ?></td>
	        </tr>
	        <tr bgcolor="#F9F9FF">
	            <td class="key">
	                <label for="theme"><?php echo JText::_( 'CCB_TEMPLATE' ); ?>:</label>
	            </td>
	            <td>
	                <select id="theme" name="theme">
						<option value="mix" <?php if( $elem->theme == 'mix') echo 'selected'; ?> >Default (Mixed)</option>
						<option value="red" <?php if( $elem->theme == 'red') echo 'selected'; ?> >Red</option>
						<option value="green" <?php if( $elem->theme == 'green') echo 'selected'; ?> >Green</option>
						<option value="blue" <?php if( $elem->theme == 'blue') echo 'selected'; ?> >Blue</option>
						<option value="gray" <?php if( $elem->theme == 'gray') echo 'selected'; ?> >Gray</option>
					</select>
	            </td>
	            <td class="ccbgeneraldesc"><?php echo JText::_( 'CCB_TEMPLATE_DESCRIPTION' ); ?></td>
	        </tr>
	        <tr>
	            <td class="key">
	                <label for="ccbeditor"><?php echo JText::_( 'CCB_EDITOR_TO_USE' ); ?>:</label>
	            </td>
	            <td>
	                <select id="ccbeditor" name="ccbeditor">
						<option value="ccboard" <?php if( $elem->ccbeditor == 'ccboard') echo 'selected'; ?> >ccBoard (BBCode)</option>
						<option value="joomla" <?php if( $elem->ccbeditor == 'joomla') echo 'selected'; ?>>Joomla</option>
					</select>
	            </td>
	            <td class="ccbgeneraldesc"><?php echo JText::_( 'CCB_EDITOR_TO_USE_DESCRIPTION' ); ?></td>
	        </tr>
	        <tr bgcolor="#F9F9FF">
	            <td class="key">
	                <label for="editorwidth"><?php echo JText::_( 'CCB_EDITOR_WIDTH' ); ?>:</label>
	            </td>
	            <td>
	                <input class="text_area" type="text" name="editorwidth" id="editorwidth" size="10" maxlength="4" value="<?php echo $elem->editorwidth; ?>" />
	            </td>
	            <td class="ccbgeneraldesc"><?php echo JText::_( 'CCB_EDITOR_WIDTH_DESCRIPTION' ); ?></td>
	        </tr>
	        <tr>
	            <td class="key">
	                <label for="editorheight"><?php echo JText::_( 'CCB_EDITOR_HEIGHT' ); ?>:</label>
	            </td>
	            <td>
	                <input class="text_area" type="text" name="editorheight" id="editorheight" size="10" maxlength="4" value="<?php echo $elem->editorheight; ?>" />
	            </td>
	            <td class="ccbgeneraldesc"><?php echo JText::_( 'CCB_EDITOR_HEIGHT_DESCRIPTION' ); ?></td>
	        </tr>
	        <tr bgcolor="#F9F9FF">
	            <td class="key">
	                <label for="subjwidth"><?php echo JText::_( 'CCB_MAX_SUBJECT_LENGTH' ); ?>:</label>
	            </td>
	            <td>
	                <input class="text_area" type="text" name="subjwidth" id="subjwidth" size="10" maxlength="3" value="<?php echo $elem->subjwidth; ?>" />
	            </td>
	            <td class="ccbgeneraldesc"><?php echo JText::_( 'CCB_MAX_SUBJECT_LENGTH_DESCRIPTION' ); ?></td>
	        </tr>
	        <tr>
	            <td class="key">
	                <label for="sigmax"><?php echo JText::_( 'CCB_MAX_SIGNATURE_LENGTH' ); ?>:</label>
	            </td>
	            <td>
	                <input class="text_area" type="text" name="sigmax" id="sigmax" size="10" maxlength="8" value="<?php echo $elem->sigmax; ?>" />
	            </td>
	            <td class="ccbgeneraldesc"><?php echo JText::_( 'CCB_MAX_SIGNATURE_LENGTH_DESCRIPTION' ); ?></td>
	        </tr>
	        <tr bgcolor="#F9F9FF">
	            <td class="key">
	                <label for="timeoffset"><?php echo JText::_( 'CCB_TIME_OFFSET' ); ?>:</label>
	            </td>
	            <td>
	                <input class="text_area" type="text" name="timeoffset" id="timeoffset" size="10" maxlength="8" value="<?php echo $elem->timeoffset; ?>" />&nbsp;(+/-)
	            </td>
	            <td class="ccbgeneraldesc"><?php echo JText::_( 'CCB_TIME_OFFSET_DESCRIPTION' ); ?></td>
	        </tr>
	        <tr>
	            <td class="key">
	                <label for="dateformat"><?php echo JText::_( 'CCB_DATE_FORMAT' ); ?>:</label>
	            </td>
	            <td>
	                <input class="text_area" type="text" name="dateformat" id="dateformat" size="40" maxlength="100" value="<?php echo $elem->dateformat; ?>" />
	            </td>
	            <td class="ccbgeneraldesc"><?php echo JText::_( 'CCB_DATE_FORMAT_DESCRIPTION' ); ?></td>
	        </tr>
	        <tr bgcolor="#F9F9FF">
	            <td class="key">
	                <label for="postlistorder"><?php echo JText::_( 'CCB_POSTS_DISPLAY_ORDER' ); ?>:</label>
	            </td>
	            <td>
	                <select id="postlistorder" name="postlistorder">
						<option value="ASC" <?php if( $elem->postlistorder == 'ASC') echo 'selected'; ?> ><?php echo JText::_( 'CCB_FIRST_POST_FIRST' ); ?></option>
						<option value="DESC" <?php if( $elem->postlistorder == 'DESC') echo 'selected'; ?>><?php echo JText::_( 'CCB_LAST_POST_FIRST' ); ?></option>
					</select>
	            </td>
	            <td class="ccbgeneraldesc"><?php echo JText::_( 'CCB_POSTS_DISPLAY_ORDER_DESCRIPTION' ); ?></td>
	        </tr>
	        <tr>
	            <td class="key">
	                <label for="showquickreply"><?php echo JText::_( 'CCB_SHOW_QUICKREPLY' ); ?>:</label>
	            </td>
	            <td>
	                <?php echo $this->showquickreply; ?>
	            </td>
	            <td class="ccbgeneraldesc"><?php echo JText::_( 'CCB_SHOW_QUICKREPLY_DESCRIPTION' ); ?></td>
	        </tr>

		</table>
	<?php
		echo $this->pane->endPanel();
		echo $this->pane->startPanel( JText::_('CCB_GENERAL_ADVANCED') , 'advanced' );
	?>
            <table class="admintable" style="width:100%;">
	        <tr>
	            <td class="key" style="width:200px;">
	                <label for="userprofile"><?php echo JText::_( 'CCB_USER_PROFILE_TO_USE' ); ?>:</label>
	            </td>
	            <td class="ccbgeneralvalue">
	                <select id="userprofile" name="userprofile">
						<option value="ccboard" <?php if( $elem->userprofile == 'ccboard') echo 'selected'; ?> >Default (ccBoard)</option>
						<option value="combuilder" <?php if( $elem->userprofile == 'combuilder') echo 'selected'; ?>>Community Builder</option>
						<option value="jomsocial" <?php if( $elem->userprofile == 'jomsocial') echo 'selected'; ?>>jomSocial</option>
					</select>
	            </td>
	            <td class="ccbgeneraldesc"><?php echo JText::_( 'CCB_USER_PROFILE_TO_USE_DESCRIPTION' ); ?></td>
	        </tr>
	        <tr bgcolor="#F9F9FF">
	            <td class="key">
	                <label for="showrealname"><?php echo JText::_( 'CCB_SHOW_USER_REAL_NAME' ); ?>:</label>
	            </td>
	            <td>
	                <?php echo $this->showrealname; ?>
	            </td>
	            <td class="ccbgeneraldesc"><?php echo JText::_( 'CCB_SHOW_USER_REAL_NAME_DESCRIPTION' ); ?></td>
	        </tr>
	        <tr>
	            <td class="key">
	                <label for="allowedit"><?php echo JText::_( 'CCB_ALLOW_EDIT_FOR' ); ?>:</label>
	            </td>
	            <td>
	                <select id="allowedit" name="allowedit">
	                	<option value="0" <?php if( $elem->allowedit == '0' ) echo 'selected'; ?>>Disabled</option>
	                	<option value="1" <?php if( $elem->allowedit == '1' ) echo 'selected'; ?>>All (Including Guest)</option>
	                	<option value="2" <?php if( $elem->allowedit == '2' ) echo 'selected'; ?>>Registered Only</option>
	                	<option value="3" <?php if( $elem->allowedit == '3' ) echo 'selected'; ?>>Moderator Only</option>
	                	<option value="4" <?php if( $elem->allowedit == '4' ) echo 'selected'; ?>>Administrator Only</option>
					</select>
	            </td>
	            <td class="ccbgeneraldesc"><?php echo JText::_( 'CCB_ALLOW_EDIT_FOR_DESCRIPTION' ); ?></td>
	        </tr>
	        <tr bgcolor="#F9F9FF">
	            <td class="key">
	                <label for="editgracetime"><?php echo JText::_( 'CCB_EDIT_GRACE_TIME' ); ?>:</label>
	            </td>
	            <td>
	                <input class="text_area" type="text" name="editgracetime" id="editgracetime" size="10" maxlength="8" value="<?php echo $elem->editgracetime; ?>" />
	            </td>
	            <td class="ccbgeneraldesc"><?php echo JText::_( 'CCB_EDIT_GRACE_TIME_DESCRIPTION' ); ?></td>
	        </tr>
	        <tr>
	            <td class="key">
	                <label for="showeditmarkup"><?php echo JText::_( 'CCB_SHOW_EDIT_MARK_UP' ); ?>:</label>
	            </td>
	            <td>
	                <?php echo $this->showeditmarkup; ?>
	            </td>
	            <td class="ccbgeneraldesc"><?php echo JText::_( 'CCB_SHOW_EDIT_MARK_UP_DESCRIPTION' ); ?></td>
	        </tr>
	        <tr bgcolor="#F9F9FF">
	            <td class="key">
	                <label for="emailsub"><?php echo JText::_( 'CCB_ALLOW_EMAIL_SUBSCRIPTION' ); ?>:</label>
	            </td>
	            <td>
	                <?php echo $this->emailsub; ?>
	            </td>
	            <td class="ccbgeneraldesc"><?php echo JText::_( 'CCB_ALLOW_EMAIL_SUBSCRIPTION_DESCRIPTION' ); ?></td>
	        </tr>
	        <tr>
	            <td class="key">
	                <label for="autosub"><?php echo JText::_( 'CCB_AUTO_EMAIL_SUBSCRIPTION' ); ?>:</label>
	            </td>
	            <td>
	                <?php echo $this->autosub; ?>
	            </td>
	            <td class="ccbgeneraldesc"><?php echo JText::_( 'CCB_AUTO_EMAIL_SUBSCRIPTION_DESCRIPTION' ); ?></td>
	        </tr>
	        <tr bgcolor="#F9F9FF">
	            <td class="key">
	                <label for="showrank"><?php echo JText::_( 'CCB_ALLOW_RANKING' ); ?>:</label>
	            </td>
	            <td>
	                <?php echo $this->showrank; ?>
	            </td>
	            <td class="ccbgeneraldesc"><?php echo JText::_( 'CCB_ALLOW_RANKING_DESCRIPTION' ); ?></td>
	        </tr>
	        <tr>
	            <td class="key">
	                <label for="showfavourites"><?php echo JText::_( 'CCB_ALLOW_FAVOURITES' ); ?>:</label>
	            </td>
	            <td>
	                <?php echo $this->showfavourites; ?>
	            </td>
	            <td class="ccbgeneraldesc"><?php echo JText::_( 'CCB_ALLOW_FAVOURITES_DESCRIPTION' ); ?></td>
	        </tr>
	        <tr bgcolor="#F9F9FF">
	            <td class="key">
	                <label for="showkarma"><?php echo JText::_( 'CCB_SHOW_KARMA' ); ?>:</label>
	            </td>
	            <td>
	                <?php echo $this->showkarma; ?>
	            </td>
	            <td class="ccbgeneraldesc"><?php echo JText::_( 'CCB_SHOW_KARMA_DESCRIPTION' ); ?></td>
	        </tr>
	        <tr>
	            <td class="key">
	                <label for="smallavatarheight"><?php echo JText::_( 'CCB_SMALL_AVATAR_HEIGHT' ); ?>:</label>
	            </td>
	            <td>
	                <input class="text_area" type="text" name="smallavatarheight" id="smallavatarheight" size="10" maxlength="8" value="<?php echo $elem->smallavatarheight; ?>" />
	            </td>
	            <td class="ccbgeneraldesc"><?php echo JText::_( 'CCB_SMALL_AVATAR_HEIGHT_DESCRIPTION' ); ?></td>
	        </tr>
	        <tr bgcolor="#F9F9FF">
	            <td class="key">
	                <label for="smallavatarwidth"><?php echo JText::_( 'CCB_SMALL_AVATAR_WIDTH' ); ?>:</label>
	            </td>
	            <td>
	                <input class="text_area" type="text" name="smallavatarwidth" id="smallavatarwidth" size="10" maxlength="8" value="<?php echo $elem->smallavatarwidth; ?>" />
	            </td>
	            <td class="ccbgeneraldesc"><?php echo JText::_( 'CCB_SMALL_AVATAR_WIDTH_DESCRIPTION' ); ?></td>
	        </tr>
	        <tr>
	            <td class="key">
	                <label for="avatarheight"><?php echo JText::_( 'CCB_AVATAR_HEIGHT' ); ?>:</label>
	            </td>
	            <td>
	                <input class="text_area" type="text" name="avatarheight" id="avatarheight" size="10" maxlength="8" value="<?php echo $elem->avatarheight; ?>" />
	            </td>
	            <td class="ccbgeneraldesc"><?php echo JText::_( 'CCB_AVATAR_HEIGHT_DESCRIPTION' ); ?></td>
	        </tr>
	        <tr bgcolor="#F9F9FF">
	            <td class="key">
	                <label for="avatarwidth"><?php echo JText::_( 'CCB_AVATAR_WIDTH' ); ?>:</label>
	            </td>
	            <td>
	                <input class="text_area" type="text" name="avatarwidth" id="avatarwidth" size="10" maxlength="8" value="<?php echo $elem->avatarwidth; ?>" />
	            </td>
	            <td class="ccbgeneraldesc"><?php echo JText::_( 'CCB_AVATAR_WIDTH_DESCRIPTION' ); ?></td>
	        </tr>
	        <tr>
	            <td class="key">
	                <label for="showtopicavatar"><?php echo JText::_( 'CCB_SHOW_TOPIC_AVATAR' ); ?>:</label>
	            </td>
	            <td><?php echo $this->showtopicavatar; ?></td>
	            <td class="ccbgeneraldesc"><?php echo JText::_( 'CCB_SHOW_TOPIC_AVATAR_DESCRIPTION' ); ?></td>
	        </tr>
	        <tr bgcolor="#F9F9FF">
	            <td class="key">
	                <label for="showboardsummary"><?php echo JText::_( 'CCB_SHOW_BOARD_SUMMARY' ); ?>:</label>
	            </td>
	            <td><?php echo $this->showboardsummary; ?></td>
	            <td class="ccbgeneraldesc"><?php echo JText::_( 'CCB_SHOW_BOARD_SUMMARY_DESCRIPTION' ); ?></td>
	        </tr>
	        <tr>
	            <td class="key">
	                <label for="showreglink"><?php echo JText::_( 'CCB_SHOW_REGISTERATION_LINK' ); ?>:</label>
	            </td>
	            <td><?php echo $this->showreglink; ?></td>
	            <td class="ccbgeneraldesc"><?php echo JText::_( 'CCB_SHOW_REGISTERATION_LINK_DESCRIPTION' ); ?></td>
	        </tr>
	        <tr bgcolor="#F9F9FF">
	            <td class="key">
	                <label for="showloginlink"><?php echo JText::_( 'CCB_SHOW_LOGIN_LINK' ); ?>:</label>
	            </td>
	            <td><?php echo $this->showloginlink; ?></td>
	            <td class="ccbgeneraldesc"><?php echo JText::_( 'CCB_SHOW_LOGIN_LINK_DESCRIPTION' ); ?></td>
	        </tr>
		</table>
	<?php
		echo $this->pane->endPanel();
		echo $this->pane->startPanel( JText::_('CCB_GENERAL_SECURITY') , 'security' );
	?>
            <table class="admintable" style="width:100%;">
	        <tr>
				<td class="key" style="width:200px;">
	                <label for="showcaptcha"><?php echo JText::_( 'CCB_SHOW_CAPTCHA_FOR' ); ?>:</label>
	            </td>
	            <td class="ccbgeneralvalue">
	                <select id="showcaptcha" name="showcaptcha">
	                	<option value="0" <?php if( $elem->showcaptcha == '0' ) echo 'selected'; ?>>Disabled</option>
	                	<option value="1" <?php if( $elem->showcaptcha == '1' ) echo 'selected'; ?>>Guest Only</option>
	                	<option value="2" <?php if( $elem->showcaptcha == '2' ) echo 'selected'; ?>>Guest/Registered</option>
					</select>
	            </td>
	            <td class="ccbgeneraldesc"><?php echo JText::_( 'CCB_SHOW_CAPTCHA_FOR_DESCRIPTION' ); ?></td>
	        </tr>
	        <tr bgcolor="#F9F9FF">
	            <td class="key">
	                <label for="emailalert"><?php echo JText::_( 'CCB_SEND_EMAIL_TO' ); ?>:</label>
	            </td>
	            <td>
	                <select id="emailalert" name="emailalert">
	                	<option value="0" <?php if( $elem->emailalert == '0' ) echo 'selected'; ?>>Disabled</option>
	                	<option value="1" <?php if( $elem->emailalert == '1' ) echo 'selected'; ?>>Moderators Only</option>
	                	<option value="2" <?php if( $elem->emailalert == '2' ) echo 'selected'; ?>>Moderators/Administrators</option>
	                	<option value="3" <?php if( $elem->emailalert == '3' ) echo 'selected'; ?>>Administrators Only</option>
					</select>
	            </td>
	            <td class="ccbgeneraldesc"><?php echo JText::_( 'CCB_SEND_EMAIL_TO_DESCRIPTION' ); ?></td>
	        </tr>
	        <tr>
	            <td class="key">
	                <label for="logip"><?php echo JText::_( 'CCB_LOG_IP_ADDRESS' ); ?>:</label>
	            </td>
	            <td>
	                <?php echo $this->logipradio; ?>
	            </td>
	            <td class="ccbgeneraldesc"><?php echo JText::_( 'CCB_LOG_IP_ADDRESS_DESCRIPTION' ); ?></td>
	        </tr>
	        <tr bgcolor="#F9F9FF">
	            <td valign="top" class="key">
	                <label for="badwords"><?php echo JText::_( 'CCB_BAD_WORDS_TO_FILTER' ); ?>:</label>
	            </td>
	            <td>
	                <textarea id="badwords" class="text_area" name="badwords" rows="5" cols="40"><?php echo $elem->badwords; ?></textarea>
	                <br /><?php  echo JText::_('CCB_PLEASE_USE_COMMA'); ?>
				</td>
				<td class="ccbgeneraldesc"><?php echo JText::_( 'CCB_BAD_WORDS_TO_FILTER_DESCRIPTION' ); ?></td>
	        </tr>
	</table>
<?php
	echo $this->pane->endPanel();
	echo $this->pane->startPanel( JText::_('CCB_GENERAL_UPLOADS') , 'uploads' );
?>
    	<table class="admintable" style="width:100%;">
	        <tr>
	            <td class="key" style="width:200px;">
	                <label for="avatarupload"><?php echo JText::_( 'CCB_ALLOW_AVATAR_UPLOAD' ); ?>:</label>
	            </td>
	            <td class="ccbgeneralvalue">
	                <?php echo $this->avatarupload; ?>
	            </td>
	            <td class="ccbgeneraldesc"><?php echo JText::_( 'CCB_ALLOW_AVATAR_UPLOAD_DESCRIPTION' ); ?></td>
	        </tr>
	        <tr bgcolor="#F9F9FF">
	            <td class="key">
	                <label for="avataruploadsize"><?php echo JText::_( 'CCB_MAXIMUM_AVATAR_UPLOAD_SIZE' ); ?>:</label>
	            </td>
	            <td>
	                <input class="text_area" type="text" name="avataruploadsize" id="avataruploadsize" size="10" maxlength="8" value="<?php echo $elem->avataruploadsize; ?>" />
	            </td>
				<td class="ccbgeneraldesc"><?php echo JText::_('CCB_MAXIMUM_AVATAR_UPLOAD_SIZE_DESCRIPTION');  ?></td>
	        </tr>
	        <tr>
	            <td class="key">
	                <label for="attachments"><?php echo JText::_( 'CCB_ALLOW_ATTACHMENTS_FOR' ); ?>:</label>
	            </td>
	            <td>
	                <select id="attachments" name="attachments">
	                	<option value="0" <?php if( $elem->attachments == '0' ) echo 'selected'; ?>>Disabled</option>
	                	<option value="1" <?php if( $elem->attachments == '1' ) echo 'selected'; ?>>All (Including Guest)</option>
	                	<option value="2" <?php if( $elem->attachments == '2' ) echo 'selected'; ?>>Registered Only</option>
	                	<option value="3" <?php if( $elem->attachments == '3' ) echo 'selected'; ?>>Moderators/Administrators</option>
					</select>
	            </td>
	            <td class="ccbgeneraldesc"><?php echo JText::_( 'CCB_ALLOW_ATTACHMENT_FOR_DESCRIPTION' ); ?></td>
	        </tr>
	        <tr bgcolor="#F9F9FF">
	            <td class="key">
	                <label for="fileuploadsize"><?php echo JText::_( 'CCB_MAXIMUM_ATTACHMENT_UPLOAD_SIZE' ); ?>:</label>
	            </td>
	            <td>
	                <input class="text_area" type="text" name="fileuploadsize" id="fileuploadsize" size="10" maxlength="8" value="<?php echo $elem->fileuploadsize; ?>" />
	             </td>
	             <td class="ccbgeneraldesc"><?php echo JText::_('CCB_MAXIMUM_ATTACHMENT_UPLOAD_SIZE_DESCRIPTION');  ?></td>
	        </tr>
	        <tr>
	            <td class="key">
	                <label for="extensions"><?php echo JText::_( 'CCB_ALLOWED_ATTACHMENT_EXTENSIONS' ); ?>:</label>
	            </td>
	            <td>
	                <input class="text_area" type="text" name="extensions" id="extensions" size="30" maxlength="250" value="<?php echo $elem->extensions; ?>" />
	                <br /><?php  echo JText::_('CCB_PLEASE_USE_COMMA'); ?>
	            </td>
	            <td class="ccbgeneraldesc"><?php echo JText::_( 'CCB_ALLOWED_ATTACHMENT_EXTENSIONS_DESCRIPTION'); ?></td>
	        </tr>
		</table>
<?php
	echo $this->pane->endPanel();
	echo $this->pane->endPane();
?>
</div>
<input type="hidden" name="option" value="com_ccboard" />
<input type="hidden" name="task" value="" />
<?php echo JHTML::_( 'form.token' ); ?>
<?php echo CCBOARD_COPYRIGHT; ?>
</form>
