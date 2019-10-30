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
	$userArray = array();
?>

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
						<td class="ccbheaderusername"><?php echo JText::_('WELCOME') . ' ' .  $this->userprofile['boardusername']; ?></td>
						<td class="ccbheaderavatar"><?php echo $this->userprofile['thumb']; ?></td>
					</tr>
				</table>
			</td>
			<td class="ccbrightbody"></td>
		</tr>
	    <?php foreach($this->sticky as $rec ) { ?>
		<tr>
			<td class="ccbleftbody"></td>
			<td class="ccbheaderrow">
				<table class="ccbnormaltable" cellpadding="0" cellspacing="0">
					<tr>
						<td class="ccbtopicicon"><img src="<?php echo CCBOARD_ASSETS_URL; ?>/sticky<?php echo $rec->topic_type; ?>.gif" class="ccbiconimg" /></td>
						<td class="ccblinetop">
							<a href="<?php echo JRoute::_('index.php?option=com_ccboard&view=postlist&forum=' . (int)$rec->forum_id . '&topic=' . (int)$rec->id . '&Itemid=' . $ccbConfig->itemid); ?>">
								<b><?php echo $this->escape($rec->post_subject); ?></b>
							</a>&nbsp;
						</td>
						<td class="ccbfrmlast">
							<?php
								if( $rec->post_time != 0 ) {
									if( $rec->post_user > 0 ) {
										if( isset( $userArray[$rec->post_user])) {
											$uname = $userArray[$rec->post_user];
										} else {
											$uname = ccboardHelperConfig::getUserName( $rec->post_user );
											$userArray[$rec->post_user] = $uname;
										}
                                        $uname = '<a href="'. JRoute::_('index.php?option=com_ccboard&view=myprofile&cid=' . $rec->post_user . '&Itemid=' . $ccbConfig->itemid ) . '">' . $uname . '</a>';
									} else {
										$uname = $rec->post_username;
									}
								}
								echo JText::_('POST_LIST_BY') . '&nbsp;<b>'. $uname . '</b>&nbsp;' . JText::_('POST_LIST_ON') . '&nbsp;' .
										JHTML::_('date', $rec->post_time + $ccbConfig->timeoffset, $ccbConfig->dateformat);
							?>
						</td>
					</tr>
				</table>
			</td>
			<td class="ccbrightbody"></td>
		</tr>
		<?php } ?>
		<tr class="ccbtablefooter">
 			 <td class="ccbbotleft"></td>
			 <td class="ccbbotmiddle"></td>
			 <td class="ccbbotright"></td>
 		</tr>
		<tr>
			<td colspan="3" class="ccbtpkmenucol">
				<a rel="nofollow" class="ovalbutton<?php echo $dispclr; ?>" href="<?php echo JRoute::_('index.php?option=com_ccboard&view=mylist&viewmode=myposts&Itemid='.$ccbConfig->itemid); ?>">
					<span><?php echo  $this->labels['myposts']; ?></span>
				</a>
				<a rel="nofollow" class="ovalbutton<?php echo $dispclr; ?>" href="<?php echo JRoute::_('index.php?option=com_ccboard&view=mylist&viewmode=mysubs&Itemid='.$ccbConfig->itemid); ?>">
					<span><?php echo  $this->labels['mysubs']; ?></span>
				</a>
				<a rel="nofollow" class="ovalbutton<?php echo $dispclr; ?>" href="<?php echo JRoute::_('index.php?option=com_ccboard&view=mylist&viewmode=myfavours&Itemid='.$ccbConfig->itemid); ?>">
					<span><?php echo  $this->labels['myfavours']; ?></span>
				</a>
			</td>
		</tr>
		<tr>
	 		 <td class="ccbtopleft<?php echo $dispclr; ?>"></td>
			 <td class="ccbtopmiddle<?php echo $dispclr; ?>">&nbsp;<?php echo $this->labels['pagetitle'];?> </td>
			 <td class="ccbtopright<?php echo $dispclr; ?>"></td>
	 	</tr>
		<tr>
			<td class="ccbleftbody"></td>
			<td class="ccbtpkdetail">
				<table class="ccbnormaltable" cellpadding="4" cellspacing="0">
					<tr>
						<td class="ccbfrmrowcol1"></td>
						<td class="ccbfrmrowcol6"></td>
						<td class="ccbfrmrowcol2"><b><?php echo $this->labels['subject']; ?></b></td>
						<td class="ccbfrmrowcol2"><b><?php echo $this->labels['forum']; ?></b></td>
						<td class="ccbfrmrowcol4"><b><?php echo $this->labels['date']; ?></b></td>
						<td class="ccbtpkcol3"><b><?php echo $this->labels['hits']; ?></b></td>
						<?php if( $this->viewmode <> 'myposts') { ?>
							<td class="ccbtpkcol3">&nbsp;</td>
						<?php } ?>
					</tr>
					<tr>
					<?php foreach ($this->items as $item) { ?>
						<tr>
			 				<td class="ccbfrmrowcol1"><img src="<?php echo CCBOARD_ASSETS_URL; ?>/24-topic-<?php echo $dispclr; if($item->locked == 1) echo '-locked'; ?>.png" /></td>
			 				<td class="ccblinetop" style="text-align: center;">
			 					<?php
									if( $item->topic_emoticon > 0) {
										echo '<img src="' .  CCBOARD_ASSETS_URL . '/topic_icon/' . $item->topic_emoticon . '.png" />';
									}
			 					?>
			 				</td>
			 				<td class="ccblinetop" align="left">
			 					<a href="<?php echo JRoute::_('index.php?option=com_ccboard&view=postlist&forum='. $item->forum_id .'&topic='. $item->topic_id .'&Itemid='.$ccbConfig->itemid); ?>">
									<?php echo $this->escape($item->post_subject); ?>
								</a>
							</td>
			 				<td class="ccblinetop" align="left"><?php echo $item->forum_name; ?></td>
			 				<td class="ccblinetop" align="center"><?php echo JHTML::_('date', $item->post_time+ $ccbConfig->timeoffset, $ccbConfig->dateformat); ?></td>
			 				<td class="ccblinetop" align="center"><?php echo (int)$item->hits; ?></td>
							<?php if( $this->viewmode <> 'myposts') { ?>
								<td>
								 	<a rel="nofollow" class="squarebutton" href="<?php echo 'index.php?option=com_ccboard&view=mylist&viewmode=' . $this->viewmode . '&task=delMyPosts&forum='. $item->forum_id .'&topic='. $item->topic_id . '&Itemid='.$ccbConfig->itemid; ?>">
										<span><?php echo  $this->labels['delete']; ?></span>
									</a>
								</td>
							<?php } ?>
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
		<tr>
			<td colspan="3" class="ccbtpkmenu">
				<table class="ccbnormaltable" cellpadding="0" cellspacing="0">
					<tr>
						<td class="ccbfrmrowcol5">
							<?php if(count($this->items) > 0 ) { echo JText::_('PAGE') . ' #&nbsp;'; echo $this->pagination->getLimitBox(); } ?>
						</td>
						<td class="ccbtpkpages">
							<?php if(count($this->items) > 0 ) echo $this->pagination->getPagesLinks(); ?>
						</td>
					</tr>
				</table>
			</td>
 		</tr>
	</table>
	<input type="hidden" name="option" value="com_ccboard" />
	<input type="hidden" name="viewmode" value="<?php echo $this->viewmode; ?>" />
	<input type="hidden" name="task" value="" />
</div>
<br/>
</form>
<?php echo CCBOARD_COPYRIGHT; ?>
<br/>
