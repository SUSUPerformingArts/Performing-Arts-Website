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
	require_once ('components/com_ccboard/assets/ccbeditor/ccbeditor.php');
	$dispclr = $this->theme;
	global $ccbConfig;
	$bbcode = new ccbEditor();
	$userArray = array();
?>

<script language="javascript" type="text/javascript">
function delconfirm( delpath ) {

	var answer = confirm ("<?php echo JText::_('POST_DELETE_CONFIRMATION'); ?>")
	if (answer) {
		window.location = 'index.php?option=com_ccboard&task=deletePost' + delpath + '&Itemid=<?php echo $ccbConfig->itemid; ?>';
	}
}
</script>

<form action="<?php echo $this->action; ?>" method="post" name="FrmPostlist" id="FrmPostlist">
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
		<?php
		foreach ($this->items as $item) {
				if( isset( $userArray[$item->post_user])) {
					$postuser = $userArray[$item->post_user];
				} else {
					$postuser = ccboardHelperConfig::getUserProfile($item->post_user);
					$userArray[$item->post_user] = $postuser;
				}
		?>
		<tr>
	 		 <td class="ccbtopleft<?php echo $dispclr; ?>"></td>
			 <td class="ccbtopmiddle<?php echo $dispclr; ?>">
			 	<span class="ccbdvsubject">&nbsp;<?php echo $this->labels['subject'] . substr($this->escape($item->post_subject),0,70).'..'; ?></span>
				<span class="ccbdvpostime"><?php echo JHTML::_('date', $item->post_time + $ccbConfig->timeoffset, $ccbConfig->dateformat); ?>&nbsp;</span>
			 </td>
			 <td class="ccbtopright<?php echo $dispclr; ?>"></td>
		</tr>
		<tr>
			<td class="ccbleftbody"></td>
			<td>
				<table class="ccbnormaltable" cellpadding="0" cellspacing="6">
					<tr>
						<td class="ccbuserinfo">
							<table class="ccbnormaltable" cellpadding="5" cellspacing="0">
								<tr><td class="ccbpstusername"><b><?php echo $item->post_user > 0 ? $postuser['username'] : $item->post_username; ?></b></td></tr>
								<tr>
									<td class="ccbpostcenter">
										<a rel="nofollow" onFocus="blur();" href="<?php echo $postuser['profilelink']; ?>" >
											<?php echo $postuser['avatar']; ?>
										</a>
									</td>
								</tr>
								<?php if ($ccbConfig->showrank  > 0) { ?>
									<tr><td class="ccbpostcenter"><?php echo $postuser['rank_title']; ?></td></tr>
									<tr><td class="ccbpostcenter"><img src="<?php echo CCBOARD_ASSETS_URL . '/ranks/' . $postuser['rank_image'] ; ?>" /></td></tr>
								<?php } ?>
								<tr>
									<td class="ccbpostuserdetails">
										<b><?php echo $this->labels['joined']; ?></b>:&nbsp;<?php echo JHTML::_('date', $postuser['registerdate'], $ccbConfig->dateformat); ?><br/>
										<b><?php echo $this->labels['post']; ?></b>:&nbsp;<?php echo number_format($postuser['post_count']); ?><br/>
										<b><?php echo $this->labels['location']; ?></b>:&nbsp;<?php echo $postuser['location']; ?>
									</td>
								</tr>
							</table>
						</td>
						<td class="ccbpostarea">
							<table class="ccbposttable"  cellpadding="0" cellspacing="0">
								<tr>
									<td class="ccbposttextcol">
										<br/>
										<?php
											echo '<b>'. $this->labels['forum'] . ':</b> ' . $item->forum_name. '<br/>';
											echo '<b>'. $this->labels['subject'] . ':</b> ' . $item->post_subject . '<br/><br/>';
										?>
									</td>
								</tr>
								<tr>
									<td class="ccbposttextcol">
										<?php
											if( strlen($item->post_subject) > 70 ) {
												echo  '<u><b>'. $this->labels['subject'] . '<i>'. $this->escape($item->post_subject) . '</i></u></b><br/><br/>';
											}
											if( $ccbConfig->ccbeditor == 'ccboard' ) {
												echo $bbcode->parseContent($item->post_text);
											} else {
												echo $item->post_text;
											}
											$attachments = $this->model->getAttachments( $item->id);
											if( count($attachments) > 0 ) {
												echo '<div class="attachbox">';
												echo '<strong>' . JText::_('ATTACHMENTS') . '</strong>' ;
												foreach($attachments as $key => $value)
												{
													echo '<br/><img src="' . CCBOARD_ASSETS_URL . '/24-attach.png" />&nbsp;';
													echo '<a rel="nofollow" href="' . CCBOARD_ASSETS_URL . '/uploads/' . $value[0] . '">'. $value[1] . ' [' . (int) ($value[3]/1024) . ' KB]</a> :: '. $value[5];
												}
												echo '</div>';
											}
										?>
									</td>
								</tr>
							<?php if( $ccbConfig->showeditmarkup && $item->modified_time > 0) { ?>
								<tr>
									<td class="ccbpostmod"><span class="ccbpostmodified">
										<?php
											if( $item->modified_by > 0 ) {
												if( isset( $userArray[$item->modified_by])) {
													$modifyuser = $userArray[$item->modified_by];
												} else {
													$modifyuser  = ccboardHelperConfig::getUserProfile($item->modified_by);
													$userArray[$item->modified_by] = $modifyuser;
												}
											} else {
												$modifyuser = $item->post_username;
											}
											echo $this->labels['lastedit'] . ': ' .
												JHTML::_('date', $item->modified_time + $ccbConfig->timeoffset, $ccbConfig->dateformat) . ' ' .
												$this->labels['lasteditby'] . ' ' . $modifyuser['username'] . ' '.
												$this->labels['lasteditreason'] . ' ' . $item->modified_reason ;
										?></span>
									</td>
								</tr>
							<?php } ?>
							<?php if( $postuser['signature'] <> '' ) { ?>
								<tr>
									<td class="ccbpostsignature">
										<?php echo $bbcode->parseContent($postuser['signature']);?>
									</td>
								</tr>
							<?php } ?>
								<tr>
									<td class="ccbmodmenu">
										<a rel="nofollow" class="squarebutton" href="#" onclick='delconfirm("<?php echo '&forum='.$item->forum_id.'&topic='.$item->topic_id.'&post='.$item->id; ?>"); return false;' ><span><?php echo  $this->labels['delete']; ?></span></a>
										<a rel="nofollow" class="squarebutton" href="<?php echo $this->approvelink . $item->id.'&Itemid=' . $ccbConfig->itemid; ?>" ><span><?php echo  $this->labels['approve']; ?></span></a>
									</td>
								</tr>
							</table>
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
<?php	}	?>
<?php if( count($this->items) > 0) { ?>
		<tr>
			<td colspan="3" class="ccbtpkpagecount">
				<?php
					echo JText::_('PAGE') . ' #&nbsp;'. $this->pagination->getLimitBox();
					echo $this->pagination->getPagesLinks();
				?>
			</td>
		</tr>
<?php	}	?>
	</table>
</div>
<input type="hidden" name="option" value="com_ccboard" />
</form>
<br/><br/>
<?php echo CCBOARD_COPYRIGHT; ?>
<br/><br/>
