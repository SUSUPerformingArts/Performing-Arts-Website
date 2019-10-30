<?php
/**
 * @version		$Id: default.php 173 2009-09-21 14:43:37Z thomasv $
 * @Project		ccBoard - Joomla! Bulletin Board Extension/Component
 * @author 		Thomas Varghese
 * @package		ccBoard
 * @copyright	Copyright (C) 2008-2009 codeclassic.org. All rights reserved.
 * @license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

	defined('_JEXEC') or die('Restricted access');
	global $ccbConfig;
	$dispclr = $this->theme;

?>
<script language="javascript" type="text/javascript">

var _CCB_JS_EDITOR_PATH = "<?php echo JURI::root(); ?>components/com_ccboard/assets/ccbeditor";

<?php if( $this->editmode > 0) { ?>
	function submitbutton(pressbutton) {
	    if (pressbutton == 'cancelProfile') {
	        submitform( pressbutton );
	        return;
	    }
	    submitform( pressbutton );
	}

	function uploadAvatar()
	{
		var form = document.adminForm;
		form.task.value="uploadAvatar";
		form.submit();

	}
<?php } ?>

</script>
<?php echo '<script src="components/com_ccboard/assets/ccbeditor/ed.js" language="javascript" type="text/javascript"></script>'; ?>

<form action="<?php echo $this->action; ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
<div class="ccbmaindiv">
	<table class="ccbnormaltable" cellpadding="0" cellspacing="0">
		<tr>
	 		 <td class="ccbtopleft<?php echo $dispclr; ?>"></td>
			 <td class="ccbtopmiddle<?php echo $dispclr; ?>">&nbsp;<?php echo $ccbConfig->boardname;?> </td>
			 <td class="ccbtopright<?php echo $dispclr; ?>"></td>
	 	</tr>
		<tr>
			<td class="ccbleftbody"></td>
			<td class="ccbheaderrow">
				<table class="ccbnormaltable" cellpadding="5" cellspacing="5">
					<tr>
						<td class="ccbheaderlink"><?php echo $this->userprofile['home']; ?></td>
						<td class="ccbheaderlink"><?php echo $this->userprofile['latest']; ?></td>
						<td class="ccbheaderlink"><?php echo $this->userprofile['link']; ?></td>
						<td class="ccbheaderlink"><?php echo $this->userprofile['mylist']; ?></td>
						<td class="ccbheaderlink"><?php echo $this->userprofile['approval']; ?></td>
						<td class="ccbheaderusername"><?php echo JText::_('WELCOME') . ' ' . $this->userprofile['boardusername']; ?></td>
						<td class="ccbheaderavatar"><?php echo $this->userprofile['thumb']; ?></td>
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
		<tr>
	 		 <td class="ccbtopleft<?php echo $dispclr; ?>"></td>
			 <td class="ccbtopmiddle<?php echo $dispclr; ?>">&nbsp;<?php echo $this->labels['pagetitle']; ?> </td>
			 <td class="ccbtopright<?php echo $dispclr; ?>"></td>
	 	</tr>
		<tr>
			<td class="ccbleftbody"></td>
			<td>
			    <table class="ccbnormaltable" cellpadding="0" cellspacing="0">
			    	<?php if( $ccbConfig->showrealname > 0) { ?>
				        <tr>
				            <td class="ccbprolabel"><?php echo $this->labels['name']; ?>:</td>
				            <td class="ccbproval"><b><?php echo $this->item['name']; ?></b></td>
				        </tr>
				    <?php } ?>
			        <tr>
			            <td class="ccbprolabel"><?php echo $this->labels['username']; ?>:</td>
			            <td class="ccbproval"><b><?php echo $this->item['username']; ?></b></td>
			        </tr>
			        <tr>
			            <td class="ccbprolabel"><?php echo $this->labels['group']; ?>:</td>
			            <td class="ccbproval"><b><?php echo $this->item['usertype']; ?></b></td>
			        </tr>
			        <tr>
			        	<td class="ccbprolabel"><?php echo $this->labels['dob']; ?>:</td>
			        	<td class="ccbproval">
			        		<?php if($this->editmode > 0) { ?>
			        			<input class="text_area" type="text" name="dob" id="dob" size="20" maxlength="20" value="<?php echo JHTML::_('date', $this->item['dob'], $ccbConfig->dateformat); ?>" />
			        		<?php } else { echo JHTML::_('date', $this->item['dob'], $ccbConfig->dateformat); } ?>
			        	</td>
			        </tr>
			        <tr>
			            <td class="ccbprolabel"><?php echo $this->labels['gender']; ?>:</td>
			            <td class="ccbproval">
				            <?php if($this->editmode > 0) {
				            		echo $this->gendercombo;
				            	} else {
				            		echo $this->item['gender'];
				            	}
				           	?>
			            </td>
			        </tr>

			        <tr>
			            <td class="ccbprolabel"><?php echo $this->labels['avatar']; ?>:</td>
			            <td class="ccbproval"><?php echo $this->avatarcombo; ?></td>
					</tr>
			    	<?php if( $ccbConfig->avatarupload > 0 && $this->editmode > 0) { ?>
				        <tr>
				            <td class="ccbprolabel"><?php echo $this->labels['upload']; ?>:</td>
							<td class="ccbproval">
								<div class="ccbuploadbut"><input size="37" type="file" id="file-upload" name="Filedata" /></div>
								<div class="ccbuploadbut">
									<a class="ovalbutton<?php echo $dispclr; ?>" href="#" onclick="uploadAvatar(); return false;">
										<span><?php echo  JText::_('BUTTON_UPLOAD'); ?></span>
									</a>
								</div>
							</td>
				        </tr>
				    <?php } ?>
			        <tr>
			            <td class="ccbprolabel"><?php echo $this->labels['postcount']; ?>:</td>
						<td class="ccbproval"><?php echo $this->item['post_count']; ?></td>
			        </tr>
			    	<?php if( $ccbConfig->showrank > 0) { ?>
				        <tr>
				            <td class="ccbprolabel" valign="top"><?php echo $this->labels['rank_title']; ?>:</td>
							<td class="ccbproval"><?php echo $this->item['rank_title']; ?><br/><img src="<?php echo CCBOARD_ASSETS_URL . '/ranks/' . $this->item['rank_image']; ?>" /></td>
				        </tr>
				    <?php } ?>
			    	<?php if( $ccbConfig->showkarma > 0) { ?>
				        <tr>
				            <td class="ccbprolabel" valign="top"><?php echo $this->labels['karma']; ?>:</td>
							<td class="ccbproval"><?php echo $this->item['karma']; ?></td>
				        </tr>
				    <?php } ?>
			        <tr>
			            <td class="ccbprolabel"><?php echo $this->labels['location']; ?>:</td>
			            <td class="ccbproval">
					    	<?php if( $this->editmode > 0) { ?>
				            	<input class="text_area" type="text" name="location" id="location" size="45" maxlength="45" value="<?php echo $this->item['location']; ?>"/>
				            <?php } else { echo $this->item['location']; } ?>
			            </td>
			        </tr>
					<?php if( $this->editmode > 0) { ?>
				        <tr>
				            <td class="ccbprolabel" valign="top"><?php echo $this->labels['signature']; ?></td>
				            <td class="ccbproval">
				            	<div class="ccbusereditor">
									<?php echo '<script> edToolbar("signature", 1); </script>'; ?>
					                <textarea id="signature" class="text_area" name="signature" rows="5" cols="50"><?php echo $this->item['signature']; ?></textarea>
								</div>
				            </td>
				        </tr>
				    <?php } ?>
			        <tr>
			            <td class="ccbprolabel"><?php echo $this->labels['www']; ?>:</td>
						<td class="ccbproval">
							<?php if( $this->editmode > 0) { ?>
								<input class="text_area" type="text" name="www" id="www" size="45" maxlength="45" value="<?php echo $this->item['www']; ?>" />
				            <?php } else { echo $this->item['www']; } ?>
						</td>
					</tr>
			        <tr>
			            <td class="ccbprolabel"><?php echo $this->labels['icq']; ?>:</td>
						<td class="ccbproval">
							<?php if( $this->editmode > 0) { ?>
								<input class="text_area" type="text" name="icq" id="icq" size="45" maxlength="45" value="<?php echo $this->item['icq']; ?>" />
				            <?php } else { echo $this->item['icq']; } ?>
						</td>
					</tr>
			        <tr>
			            <td class="ccbprolabel"><?php echo $this->labels['aol']; ?>:</td>
						<td class="ccbproval">
							<?php if( $this->editmode > 0) { ?>
								<input class="text_area" type="text" name="aol" id="aol" size="45" maxlength="45" value="<?php echo $this->item['aol']; ?>" />
				            <?php } else { echo $this->item['aol']; } ?>
						</td>
					</tr>
			        <tr>
			            <td class="ccbprolabel"><?php echo $this->labels['msn']; ?>:</td>
						<td class="ccbproval">
							<?php if( $this->editmode > 0) { ?>
								<input class="text_area" type="text" name="msn" id="msn" size="45" maxlength="45" value="<?php echo $this->item['msn']; ?>" />
				            <?php } else { echo $this->item['msn']; } ?>
						</td>
					</tr>
			        <tr>
			            <td class="ccbprolabel"><?php echo $this->labels['yahoo']; ?>:</td>
						<td class="ccbproval">
							<?php if( $this->editmode > 0) { ?>
								<input class="text_area" type="text" name="yahoo" id="yahoo" size="45" maxlength="45" value="<?php echo $this->item['yahoo']; ?>" />
							<?php } else { echo $this->item['yahoo']; } ?>
						</td>
					</tr>
			        <tr>
			            <td class="ccbprolabel"><?php echo $this->labels['jabber']; ?>:</td>
						<td class="ccbproval">
							<?php if( $this->editmode > 0) { ?>
								<input class="text_area" type="text" name="jabber" id="jabber" size="45" maxlength="45" value="<?php echo $this->item['jabber']; ?>" />
							<?php } else { echo $this->item['jabber']; } ?>
						</td>
					</tr>
			        <tr>
			            <td class="ccbprolabel"><?php echo $this->labels['skype']; ?>:</td>
						<td class="ccbproval">
							<?php if( $this->editmode > 0) { ?>
								<input class="text_area" type="text" name="skype" id="skype" size="45" maxlength="45" value="<?php echo $this->item['skype']; ?>" />
							<?php } else { echo $this->item['skype']; } ?>
						</td>
					</tr>
			        <tr>
			            <td class="ccbprolabel"><?php echo $this->labels['view']; ?>:</td>
						<td class="ccbproval"><?php echo $this->item['hits']; ?></td>
			        </tr>
			        <tr>
			            <td class="ccbprolabel"><?php echo $this->labels['registereddate']; ?></td>
						<td class="ccbproval"><?php echo JHTML::_('date',$this->item['registerdate'], $ccbConfig->dateformat ); ?></td>
			        </tr>
			        <tr>
			            <td class="ccbprolabel"><?php echo $this->labels['lastvisitdate']; ?></td>
						<td class="ccbproval"><?php echo JHTML::_('date',$this->item['lastvisitdate'], $ccbConfig->dateformat ); ?></td>
			        </tr>
					<?php if( $this->editmode > 0) { ?>
				        <tr>
				            <td class="ccbprolabel"></td>
							<td>
								<div class="buttonarea">
									<a rel="nofollow" class="ovalbutton<?php echo $dispclr; ?>" href="#" onclick="submitbutton('saveProfile'); return false;">
										<span><?php echo $this->labels['save']; ?></span>
									</a>
									<a rel="nofollow" class="ovalbutton<?php echo $dispclr; ?>" href="#" onclick="submitbutton('cancelProfile'); return false;">
										<span><?php echo $this->labels['cancel']; ?></span>
									</a>
								</div>
							</td>
				        </tr>
				    <?php } ?>
			    </table>
			</td>
			<td class="ccbrightbody"></td>
		</tr>
		<tr class="ccbtablefooter">
 			 <td class="ccbbotleft"></td>
			 <td class="ccbbotmiddle"></td>
			 <td class="ccbbotright"></td>
 		</tr>
	</table>
</div>
<input type="hidden" name="option" id="option" value="com_ccboard" />
<input type="hidden" name="user_id" id="user_id" value="<?php echo $this->item['user_id']; ?>" />
<input type="hidden" name="<?php echo JUtility::getToken(); ?>" value="1" />
<input type="hidden" name="task" id="task" value="" />
</form>
<?php echo CCBOARD_COPYRIGHT; ?>
<br clear="left" />

