<?php
/**
 * @version		$Id: default.php 194 2009-09-26 08:12:37Z thomasv $
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
	<table class="ccbnormaltable" cellpadding=0 cellspacing=0>
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
							<a href="<?php echo JRoute::_($this->posturl .'&forum=' . (int)$rec->forum_id . '&topic=' . (int)$rec->id .'&Itemid=' . $ccbConfig->itemid ); ?>">
								<b><?php echo $this->escape($rec->post_subject); ?></b>
							</a>&nbsp;
						</td>
						<td class="ccbfrmlast">
							<?php
								if( $rec->post_time != 0 ) {
									if( $rec->post_user > 0 ) {
										if( isset( $userArray[$rec->post_user])) {
											$uname = $userArray[$rec->post_user][0];
										} else {
											$uobj = ccboardHelperConfig::getUserProfile( $rec->post_user );
											$userArray[$rec->post_user][0] = $uobj['username'];
                                            $userArray[$rec->post_user][1] = $uobj['thumb'];
                                            $uname = $uobj['username'];
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
			<td colspan="3" class="ccbtpkmenu">
				<table class="ccbnormaltable" cellpadding="0" cellspacing="0">
					<tr>
						<?php if( $this->postingAllowed) { ?>
							<td class="ccbtpkmenucol">
								<a rel="nofollow" class="ovalbutton<?php echo $dispclr; ?>" href="<?php echo $this->newtopic; ?>">
									<span><?php echo  $this->labels['newtopic']; ?></span>
								</a>
							</td>
						<?php }	?>
						<td class="ccbtpkpages">
							<?php if(count($this->items) > 0 ) echo $this->pagination->getPagesLinks(); ?>
						</td>
					</tr>
				</table>
			</td>
 		</tr>
		<tr>
	 		 <td class="ccbtopleft<?php echo $dispclr; ?>"></td>
			 <td class="ccbtopmiddle<?php echo $dispclr; ?>">&nbsp;<?php echo $this->escape($this->forum_name);?> </td>
			 <td class="ccbtopright<?php echo $dispclr; ?>"></td>
	 	</tr>
		<tr>
			<td class="ccbleftbody"></td>
			<td class="ccbtpkdetail">
				<table class="ccbnormaltable" cellpadding="4" cellspacing="0">
					<tr>
						<td class="ccbfrmrowcol1"></td>
						<td class="ccbfrmrowcol6"></td>
						<td class="ccbfrmrowcol2"><b><?php echo $this->labels['topics']; ?></b></td>
                        <td class="ccbtpkcol3"><b><?php echo $this->labels['replies']; ?></b></td>
						<td class="ccbtpkcol3"><b><?php echo $this->labels['views']; ?></b></td>
                        <?php if( $ccbConfig->showtopicavatar > 0) { ?>
                            <td class="ccbheaderavatar">&nbsp;</td>
                        <?php } ?>
						<td class="ccbfrmrowcol5"><b><?php echo $this->labels['lastpost']; ?></b></td>
					</tr>
					<?php
					$i=1;
					foreach ($this->items as $item) {
					?>
						<tr>
			 				<td class="ccbfrmrowcol1">
			 					<img src="<?php echo CCBOARD_ASSETS_URL; ?>/24-topic-<?php echo $dispclr; if($item->locked == 1) echo '-locked'; ?>.png" />
			 				</td>
			 				<td class="ccblinetop" style="text-align: center;">
			 					<?php
									if( $item->topic_emoticon) {
										echo '<img src="' .  CCBOARD_ASSETS_URL . '/topic_icon/' . $item->topic_emoticon . '.png" />';
									}
			 					?>
			 				</td>
			 				<td class="ccblinetop" align="left">
			 					<a href="<?php echo JRoute::_($this->posturl . '&forum='.(int)$item->forum_id.'&topic='.(int)$item->id.'&Itemid=' . $ccbConfig->itemid); ?>">
									<b><?php echo $this->escape($item->post_subject); ?></b>
								</a><br />
								<?php
									if( $item->post_user > 0 ) {
										if( isset( $userArray[$item->post_user])) {
											$uname = $userArray[$item->post_user][0];
										} else {
											$uobj = ccboardHelperConfig::getUserProfile( $item->post_user );
											$userArray[$item->post_user][0] = $uobj['username'];
                                            $userArray[$item->post_user][1] = $uobj['thumb'];
                                            $uname = $uobj['username'];
										}
                                        $uname = '<a rel="nofollow" href="'. JRoute::_('index.php?option=com_ccboard&view=myprofile&cid=' . $item->post_user . '&Itemid=' . $ccbConfig->itemid ) . '">' . $uname . '</a>';
									} else {
										$uname = $item->post_username;
									}
									echo JText::_('POST_LIST_BY') . '&nbsp;'. $uname . '&nbsp;' . JText::_('POST_LIST_ON') . '&nbsp;' .
										JHTML::_('date', $item->post_time + $ccbConfig->timeoffset, $ccbConfig->dateformat);
			 					?>
							</td>
			 				<td class="ccblinetop" align="center"><?php echo (int)$item->reply_count; ?></td>
			 				<td class="ccblinetop" align="center"><?php echo (int)$item->hits; ?></td>
    	 					<?php
                                $uthumb = '<span class="topicavatar"><img src="components/com_ccboard/assets/avatar/avatar1.png" style="height: ' . $ccbConfig->smallavatarheight . 'px; width: ' . $ccbConfig->smallavatarwidth . 'px;" /></span>';
                                if( $item->last_post_user > 0 ) {
                                    if( isset( $userArray[$item->last_post_user])) {
                                        $uname = $userArray[$item->last_post_user][0];
                                        $uthumb = $userArray[$item->last_post_user][1];
                                    } else {
                                        $uobj = ccboardHelperConfig::getUserProfile( $item->last_post_user);
                                        $userArray[$item->last_post_user][0] = $uobj['username'];
                                        $userArray[$item->last_post_user][1] = $uobj['thumb'];
                                        $uname = $uobj['username'];
                                        $uthumb = $uobj['thumb'];
                                    }
                                    $uname = '<a rel="nofollow" href="'. JRoute::_('index.php?option=com_ccboard&view=myprofile&cid=' . $item->last_post_user . '&Itemid=' . $ccbConfig->itemid ) . '">' . $uname . '</a>';
                                    $uthumb = '<a rel="nofollow" href="'. JRoute::_('index.php?option=com_ccboard&view=myprofile&cid=' . $item->last_post_user . '&Itemid=' . $ccbConfig->itemid ) . '">' . $uthumb . '</a>';
                                } else {
    								$uname = $item->last_post_username;
                                }
		 					?>
                            <?php if( $ccbConfig->showtopicavatar > 0) { ?>
                                <td class="ccblinetop" align="center">
                                    <?php echo $uthumb; ?>
                                </td>
                            <?php } ?>
                            <td class="ccblinetop" align="left">
                                <?php
                                    //echo '<a href="' . JRoute::_($this->posturl . '&forum='. $item->id .'&topic='. (int) $item->id . '&Itemid=' . $ccbConfig->itemid . ccboardHelperConfig::getPageNumber($item->id, $item->last_post_id)) . '">' . $this->escape($item->last_post_subject) . '<a/>&nbsp;';
                                    echo $this->escape($item->last_post_subject) .'&nbsp;';
                                    echo '<a href="' . JRoute::_($this->posturl . '&forum='. $item->id .'&topic='. (int) $item->id . '&Itemid=' . $ccbConfig->itemid . ccboardHelperConfig::getPageNumber($item->id, $item->last_post_id)) .  '">';
                                    echo '<img src="' . CCBOARD_ASSETS_URL . '/lastpost.gif"/>';
                                    echo '</a><br/>';
                                    echo JText::_('POST_LIST_BY') . '&nbsp;<b>'. $uname . '</b><br />';
                                    echo JText::_('POST_LIST_ON') . '&nbsp;' . JHTML::_('date', $item->last_post_time+ $ccbConfig->timeoffset, $ccbConfig->dateformat);
                                ?>
                            </td>
                        </tr>
		 		    <?php
					}
					?>
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
						<td class="ccbtpkmenucolbot">
							<?php if( $this->postingAllowed) { ?>
								<a rel="nofollow" class="ovalbutton<?php echo $dispclr; ?>" href="<?php echo $this->newtopic; ?>">
									<span><?php echo  $this->labels['newtopic']; ?></span>
								</a>
							<?php }	?>
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
	<?php
?>
<input type="hidden" name="option" value="com_ccboard" />
<input type="hidden" name="forum" value="<?php echo $this->forum_id; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="filter_order" value="<?php echo $this->filter_order; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filter_orderDir; ?>" />
</div>
<br/>
</form>
<?php echo CCBOARD_COPYRIGHT; ?>
<br/>
