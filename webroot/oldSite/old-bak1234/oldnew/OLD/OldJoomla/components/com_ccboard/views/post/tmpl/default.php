<?php
/**
 * @version		$Id: default.php 173 2009-09-21 14:43:37Z thomasv $
 * @Project		ccBoard - Joomla! Bulletin Board Extension/Component
 * @author 		Thomas Varghese
 * @package		ccBoard
 * @copyright	Copyright (C) 2008-2009 codeclassic.org. All rights reserved.
 * @license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

	defined( '_JEXEC' ) or die( 'Restricted Access' );
	global $ccbConfig;
	$dispclr = $this->theme;
?>

<script language="javascript" type="text/javascript">

_CCB_JS_EDITOR_PATH = '<?php echo JURI::root(); ?>components/com_ccboard/assets/ccbeditor';

function setgood() {
	// TODO: Put setGood back
	return true;
}

function submitbutton(pressbutton)
{
	var form = document.postForm;
	if (pressbutton == "cancelPost") {
		form.submit();
		return;
	}
	if (pressbutton == "previewpost") {
        form.task.value="previewpost";
		form.submit();
		return;
	}

    // do field validation
	var sub = form.post_subject.value;
	if (sub == '') {
		alert ( "<?php echo $this->labels['invalidsub']; ?>");
		return;
	}

	var text='';
	<?php
		if( $ccbConfig->ccbeditor == 'ccboard' )
		{
			echo 'text= document.getElementById("post_text").value;';

		} else {
			 echo 'text=' . $this->editor->getContent( 'post_text' );
			 echo $this->editor->save( 'post_text' );
		}
	?>

	if (text == '') {
		alert ( "<?php echo $this->labels['invalidtext']; ?>");
		return;
	}

	var guest = <?php echo $this->guest; ?>;
	var username = form.username.value;
	if( guest == 1 && username == '' ) {
		alert ( "<?php echo $this->labels['invalidusername']; ?>");
		return;
	}


	form.task.value="savePost";
	form.submit();
}

function viewPost()
{
	el = document.getElementById('dvPreview');
	if( el.style.display == "block" ) {
		el.style.display="none";
	} else {
		el.style.display="block";
		var text = <?php if( $ccbConfig->ccbeditor  == 'ccboard' ) {
							echo 'text= document.getElementById("post_text").value;';
						}	else {
							echo $this->editor->getContent( 'post_text' );
						}
					?>
		var el = document.getElementById('pvcontent');
		el.innerHTML = text;
	}
}



function uploadAttachment()
{
    <?php echo $this->editor->save( 'post_text' ); ?>
            
	var form = document.postForm;
	form.task.value="uploadAttachment";
	form.submit();

}

function deleteAttachment( aid )
{
    <?php echo $this->editor->save( 'post_text' ); ?>
            
	var form = document.postForm;
	form.attachid.value = aid;
	form.task.value="deleteAttachment";
	form.submit();
}

</script>

<?php
	if( $ccbConfig->ccbeditor  == 'ccboard' ) {
		echo '<script src="' . $this->path . 'components/com_ccboard/assets/ccbeditor/ed.js" language="javascript" type="text/javascript"></script>';
	}

 ?>

<form action="<?php echo $this->action; ?>" method="post" name="postForm" id="postForm"  enctype="multipart/form-data" onSubmit="setgood();">
<div class="ccbmaindiv">
	<table class="ccbnormaltable" cellpadding="0" cellspacing="0">
		<tr>
			<td class="ccbtopleft<?php echo $dispclr; ?>"></td>
			<td class="ccbtopmiddle<?php echo $dispclr; ?>">&nbsp;<?php echo $this->page_title; ?></td>
			<td class="ccbtopright<?php echo $dispclr; ?>"></td>
		<tr>
		<tr>
			<td class="ccbleftbody"></td>
			<td class="ccbtpkdetail">
				<table class="ccbnormaltable" cellpadding="0" cellspacing="0">
					<tr>
						<td class="ccbeditsubjlabel">&nbsp;</td>
						<td class="ccbeditsubj">&nbsp;</td>
					</tr>
					<?php if( $this->guest > 0 ) { ?>
						<tr>
							<td class="ccbeditsubjlabel"><b><?php echo $this->labels['username']; ?></b></td>
							<td class="ccbeditsubj">
								<input class="ccbeditsubj" type="text" name="username" id="username" size="40" maxlength="100" value="<?php echo $this->username; ?>" />
							</td>
						</tr>
					<?php } else {
						echo '<tr><td></td><td><input type="hidden" id="username" name="username" value="' . $this->username. '" /></td></tr>';
					} ?>
					<?php // if( $this->mode <> 'reply' && $this->mode <> 'quote' ) {
                          // Commented out for promoting existing thread to Announcement / Viceversa
							   if( $this->modeactions > 0) {
								   //if( $this->id == 0 || $this->topictype > 0 ) { ?>
                                        <tr>
                                            <td class="ccbeditsubjlabel"><b><?php echo $this->labels['topictype']; ?>:</b></td>
                                            <td class="ccbeditsubj"><?php echo $this->topiccombo; ?></td>
                                        </tr>
					 <?php          // }
                                }
                            //}
                            if( ($this->mode == 'post') || ($this->mode == 'edit' && $this->id == $this->startPost) ) { ?>
                                <tr>
                                    <td class="ccbeditsubjlabel"><b><?php echo $this->labels['topicicon']; ?>:</b></td>
                                    <td class="ccbeditsubj">
                                        &nbsp;
                                            <input type = "radio" id="topic_emoticon" name = "topic_emoticon" value = "0" <?php echo $this->topic_emoticon == 0 ? 'checked="checked"':''; ?> /><?php echo JText::_('NONE'); ?>
                                            <input type = "radio" id="topic_emoticon" name = "topic_emoticon" value = "1" <?php echo $this->topic_emoticon == 1 ? 'checked="checked"':''; ?> /><img src = "<?php echo CCBOARD_ASSETS_URL; ?>/topic_icon/1.png" alt= "" border = "0"/>
                                            <input type = "radio" id="topic_emoticon" name = "topic_emoticon" value = "2" <?php echo $this->topic_emoticon == 2 ? 'checked="checked"':''; ?> /><img src = "<?php echo CCBOARD_ASSETS_URL; ?>/topic_icon/2.png" alt = "" border = "0"/>
                                            <input type = "radio" id="topic_emoticon" name = "topic_emoticon" value = "3" <?php echo $this->topic_emoticon == 3 ? 'checked="checked"':''; ?> /><img src = "<?php echo CCBOARD_ASSETS_URL; ?>/topic_icon/3.png" alt = "" border = "0"/>
                                            <input type = "radio" id="topic_emoticon" name = "topic_emoticon" value = "4" <?php echo $this->topic_emoticon == 4 ? 'checked="checked"':''; ?> /><img src = "<?php echo CCBOARD_ASSETS_URL; ?>/topic_icon/4.png" alt = "" border = "0"/>
                                            <input type = "radio" id="topic_emoticon" name = "topic_emoticon" value = "5" <?php echo $this->topic_emoticon == 5 ? 'checked="checked"':''; ?> /><img src = "<?php echo CCBOARD_ASSETS_URL; ?>/topic_icon/5.png" alt = "" border = "0"/>
                                            <input type = "radio" id="topic_emoticon" name = "topic_emoticon" value = "6" <?php echo $this->topic_emoticon == 6 ? 'checked="checked"':''; ?> /><img src = "<?php echo CCBOARD_ASSETS_URL; ?>/topic_icon/6.png" alt = "" border = "0"/>
                                            <input type = "radio" id="topic_emoticon" name = "topic_emoticon" value = "7" <?php echo $this->topic_emoticon == 7 ? 'checked="checked"':''; ?> /><img src = "<?php echo CCBOARD_ASSETS_URL; ?>/topic_icon/7.png" alt = "" border = "0"/>
                                            <input type = "radio" id="topic_emoticon" name = "topic_emoticon" value = "8" <?php echo $this->topic_emoticon == 8 ? 'checked="checked"':''; ?> /><img src = "<?php echo CCBOARD_ASSETS_URL; ?>/topic_icon/8.png" alt = "" border = "0"/>
                                            <input type = "radio" id="topic_emoticon" name = "topic_emoticon" value = "9" <?php echo $this->topic_emoticon == 9 ? 'checked="checked"':''; ?> /><img src = "<?php echo CCBOARD_ASSETS_URL; ?>/topic_icon/9.png" alt = "" border = "0"/>
                                            <input type = "radio" id="topic_emoticon" name = "topic_emoticon" value = "10" <?php echo $this->topic_emoticon == 10 ? 'checked="checked"':''; ?> /><img src = "<?php echo CCBOARD_ASSETS_URL; ?>/topic_icon/10.png" alt = "" border = "0"/>
                                    </td>
                                </tr>
                    <?php   }
						  else {
							echo '<tr><td></td><td><input type="hidden" id="topic_emoticon" name="topic_emoticon" value="' . $this->topic_emoticon . '" /></td></tr>';
						  } ?>
					<tr>
						<td class="ccbeditsubjlabel"><b><?php echo $this->labels['subject']; ?>:</b></td>
						<td class="ccbeditsubj"><input class="ccbeditsubj" type="text" name="post_subject" id="post_subject" size="<?php echo $ccbConfig->subjwidth; ?>" maxlength="100" value="<?php echo $this->post_subject; ?>" /></td>
					</tr>
					<tr>
						<td colspan="2" class="ccbedittext">
							<?php
								if( $ccbConfig->ccbeditor  == 'ccboard' ) {
									echo '<script> edToolbar("post_text", 1); </script>';
									echo '<textarea id="post_text" name = "post_text" cols="'. $ccbConfig->editorwidth . '"  width="" rows="'. $ccbConfig->editorheight . '" height>'. $this->post_text . '</textarea>';
								} else {
									echo $this->editor->display('post_text', $this->post_text, $ccbConfig->editorwidth , $ccbConfig->editorheight, '70', '15', false);
								}
							?>
						</td>
					</tr>
					<tr>
						<td class="ccbeditsubjlabel">&nbsp;</td>
						<td class="ccbeditsubj">&nbsp;</td>
					</tr>
				<?php if( $this->guest < 1 && $ccbConfig->emailsub > 0 && $this->mode <> 'edit') { ?>
					<tr>
						<td class="ccbeditsubjlabel"><b><?php echo $this->labels['subscribe']; ?>:</b></td>
						<td class="ccbeditsubj"><?php echo $this->autosub; ?></td>
					</tr>
				<?php } else { ?>
                    <tr>
                        <td></td>
                        <td><input type="hidden" name="autosub" id="autosub" value="0" /></td>
                    </tr>
                <?php } ?>
				<?php if( $this->uploadPermission ) {
					foreach($this->attachments as $key => $value) { ?>
						<tr>
							<td class="ccbeditsubj" colspan="2">
								<img src="components/com_ccboard/assets/24-attach.png"/>
								<?php echo $value[1] . ' [' . (int) ($value[3]/1024) . ' KB]'; ?>;
								<a href="#" onclick="deleteAttachment('<?php echo $value[0]; ?>'); return false;">[<?php echo JText::_('DELETE'); ?>]</a>::<?php echo $value[5]; ?>
							</td>
						</tr>
					<?php } ?>
					<tr>
						<td class="ccbeditsubjlabel"><b><?php echo $this->labels['attachments']; ?>:</b></td>
						<td class="ccbeditsubj"><input size="37" type="file" id="file-upload" name="Filedata" /></td>
					</tr>
					<tr>
						<td class="ccbeditsubjlabel"><b><?php echo $this->labels['attachmentcomment'] ?>:</b></td>
						<td class="ccbeditsubj">
							<div class="ccbuploadbut"><input class="attachtext" type="text" maxlength="250" id="attachmentcomment" name="attachmentcomment" /></div>
							<div class="ccbuploadbut">
								<a rel="nofollow" class="ovalbutton<?php echo $dispclr; ?>" href="#" onclick="uploadAttachment(); return false;">
									<span><?php echo  JText::_('BUTTON_UPLOAD'); ?></span>
								</a>
							</div>
						<td>
					</tr>
				<?php } ?>
				<?php if( $this->mode == 'edit' ) { ?>
					<tr>
						<td class="ccbeditsubjlabel"><b><?php echo $this->labels['editreason']; ?>:</b></td>
						<td class="ccbeditsubj"><input class="ccbeditsubj" type="text" name="modified_reason" id="modified_reason" size="<?php echo $ccbConfig->subjwidth; ?>" maxlength="255" value="<?php echo $this->modified_reason; ?>" /></td>
					</tr>
					<tr><td colspan="2" class="ccbcolspacer"></td></tr>
				<?php } else {
						echo '<tr><td></td><td><input type="hidden" id="modified_reason" name="modified_reason" value="" /></td></tr>';
				} ?>
					<tr>
						<td colspan="2">
							<table class="ccbnormaltable" cellpadding="0" cellspacing="0">
								<tr>
									<?php if( $this->cap_path <> '' ) { ?>
										<td class="ccbeditsubjlabel"><b><?php echo $this->labels['captcha']; ?>:</b></td>
										<td class="ccbeditcap"><img id="imgcap" name="imgcap" src="<?php echo $this->cap_path;?>"  /></td>
										<td class="ccbeditcaptext"><input class="ccbeditcapinput" type="text" id="captcha" name="captcha" maxlength="5" autocomplete="off" /></td>
									<?php }
										else {
                                        	echo '<td><input type="hidden" id="captcha" name="captcha"/><td>';
										}
									?>
                                        <td>
	                                        <div class="buttonarea">
                                                <?php if( $ccbConfig->ccbeditor == 'joomla') { ?>
	                                                <a rel="nofollow" class="ovalbutton<?php echo $dispclr; ?>" href="#" onclick="viewPost(); return false;">
                                                        <span><?php echo $this->labels['preview']; ?></span>
                                                    </a>
                                                <?php } else { ?>
                                                    <a rel="nofollow" class="ovalbutton<?php echo $dispclr; ?>" href="#" onclick="submitbutton('previewpost'); return false;">
                                                        <span><?php echo $this->labels['preview']; ?></span>
                                                    </a>
                                                <?php } ?>
                                                <a rel="nofollow" class="ovalbutton<?php echo $dispclr; ?>" href="#" onclick="submitbutton('savePost'); return false;">
													<span><?php echo $this->labels['save']; ?></span>
												</a>
	                                                <a rel="nofollow" class="ovalbutton<?php echo $dispclr; ?>" href="#" onclick="submitbutton('cancelPost'); return false;">
													<span><?php echo $this->labels['cancel']; ?></span>
												</a>
											</div>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
						<tr><td colspan="2" class="ccbcolspacer"></td></tr>
				</table>
			</td>
			<td class="ccbrightbody"></td>
		</tr>
		<tr class="ccbtablefooter">
 			 <td class="ccbbotleft"></td>
			 <td class="ccbbotmiddle"></td>
			 <td class="ccbbotright"></td>
 		</tr>
 		<tr><td colspan="3" class="ccbcolspacer"></td></tr>
	</table>
</div>
<input type="hidden" name="option" value="com_ccboard" />
<input type="hidden" id="task" name="task" value="cancelPost" />
<input type="hidden" name="<?php echo JUtility::getToken(); ?>" value="1" />
<input type="hidden" name="id" value="<?php if ( ((int)$this->id) > 0 ){ echo $this->id; } ?>" />
<input type="hidden" name="forum_id" value="<?php echo (int)$this->forum_id; ?>" />
<input type="hidden" name="topic_id" value="<?php echo (int)$this->topic_id; ?>" />
<input type="hidden" name="mode" value="<?php echo $this->mode; ?>" />
<input type="hidden" name="attachid" value="" />
</form>


<div id="dvPreview" class="ccbeditpreview">
	<table class="ccbnormaltable" cellpadding="0" cellspacing="0">
		<tr>
			<td class="ccbtopleft<?php echo $dispclr; ?>"></td>
			<td class="ccbtopmiddle<?php echo $dispclr; ?>">&nbsp;<?php echo $this->page_title; ?></td>
			<td class="ccbtopright<?php echo $dispclr; ?>"></td>
		<tr>
		<tr>
			<td class="ccbleftbody"></td>
			<td>
				<table class="ccbnormaltable" cellpadding="0" cellspacing="6">
					<tr>
						<td class="ccbuserinfo">
							<table  class="ccbnormaltable" cellpadding="5" cellspacing="0">
								<tr><td class="ccbpstusername"><b>User</b></td></tr>
								<tr><td class="ccbpostcenter"><img src="components/com_ccboard/assets/avatar/avatar1.png" class="ccbpostavatar" /></td></tr>
								<tr><td></td></tr>
							</table>
						</td>
						<td id="pvcontent" class="ccbposttextcol">
                            <?php
                                if( $ccbConfig->ccbeditor == 'ccboard' ) {
                                	require_once ('components/com_ccboard/assets/ccbeditor/ccbeditor.php');
                                    $bbcode = new ccbEditor();
                                    echo $bbcode->parseContent($this->post_text);
                                }
                            ?>
						</td>
					</tr>
				</table>
			</td>
			<td class="ccbrightbody"></td>
		</tr>
		<tr class="ccbtablefooter">
 			 <td class="ccbbotleft"></td>
			 <td class="ccbbotmiddle"></td>
			 <td class="ccbbotright"></td>
 		</tr>
 		<tr><td colspan="3" class="ccbcolspacer"></td></tr>
	</table>
</div>
<script>
<?php
    if( $this->preview == 'previewpost') {
        echo 'elem = document.getElementById("dvPreview");';
        echo 'elem.style.display="block";';
    }
?>
</script>
<br/><br clear="left"/>
<?php echo CCBOARD_COPYRIGHT; ?>
<br/><br clear="left"/>
