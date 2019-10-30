<?php
/**
 * @version		$Id: default.php 189 2009-09-26 07:19:26Z thomasv $
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
		<?php foreach($this->sticky as $rec) { ?>
			<tr>
				<td class="ccbleftbody"></td>
				<td class="ccbheaderrow">
					<table class="ccbnormaltable" cellpadding="0" cellspacing="0">
						<tr>
							<td class="ccbtopicicon"><img src="<?php echo CCBOARD_ASSETS_URL; ?>/sticky<?php echo $rec->topic_type; ?>.gif" class="ccbiconimg" /></td>
							<td class="ccblinetop">
								<a href="<?php echo JRoute::_('index.php?option=com_ccboard&view=postlist&forum=' . (int)$rec->forum_id . '&topic=' . (int)$rec->id.'&Itemid=' . $ccbConfig->itemid ); ?>">
									<b><?php echo $this->escape($rec->post_subject); ?></b>
								</a>&nbsp;
							</td>
							<td class="ccbfrmlast" >
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
		<?php }	?>
		<tr class="ccbtablefooter">
 			 <td class="ccbbotleft"></td>
			 <td class="ccbbotmiddle"></td>
			 <td class="ccbbotright"></td>
 		</tr>
 		<tr><td colspan="3" class="ccbcolspacer"></td></tr>
		<tr>
			<td colspan="3" class="ccbtpkpages">
                <?php if(count($this->items) > 0 ) echo $this->pagination->getPagesLinks(); ?>
            </td>
 		</tr>
        <?php
       	$userArray = array();
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
										<?php if( $item->post_user < 1 ) { echo '<br/>'. JText::_('CCB_GUEST'); } ?>
									</td>
								</tr>
								<?php if ($ccbConfig->showrank  > 0 && $item->post_user > 0) { ?>
									<tr><td class="ccbpostcenter"><?php echo $postuser['rank_title']; ?></td></tr>
									<tr><td class="ccbpostcenter"><img src="<?php echo CCBOARD_ASSETS_URL . '/ranks/' . $postuser['rank_image'] ; ?>" /></td></tr>
								<?php } ?>
								<?php if($item->post_user > 0 ) { ?>
									<tr>
										<td class="ccbpostuserdetails">
											<b><?php echo $this->labels['joined']; ?></b>:&nbsp;<?php echo JHTML::_('date', $postuser['registerdate'], $ccbConfig->dateformat); ?><br/>
											<b><?php echo $this->labels['post']; ?></b>:&nbsp;<?php echo number_format($postuser['post_count']); ?><br/>
											<b><?php echo $this->labels['location']; ?></b>:&nbsp;<?php echo $postuser['location']; ?>
										</td>
									</tr>
								<?php } ?>
								<?php if ($ccbConfig->userprofile == 'ccboard' && $item->post_user > 0 ) { ?>
									<tr>
										<td class="ccbpostcenter">
											<span class="ccbposticons">
							                	<?php
							                		// if((int)$postuser['showemail'] 	== 1) echo '<img src="' . 'components/com_ccboard/assets/icon_email.gif" height="20" width="20" 	title="' . $postuser['email'] . '"/>&nbsp;';
													if($postuser['www']		<> '') echo '<img src="' . 'components/com_ccboard/assets/icon_www.gif" height="20" width="20" title="' . $postuser['www'] . '" />&nbsp;';
													if($postuser['icq'] 	<> '') echo '<img src="' . 'components/com_ccboard/assets/icon_icq.gif" height="20" width="20" title="' . $postuser['icq'] . '" />&nbsp;';
													if($postuser['aol'] 	<> '') echo '<img src="' . 'components/com_ccboard/assets/icon_aol.gif" height="20" width="20" title="' . $postuser['aol'] . '" />&nbsp;';
													if($postuser['msn'] 	<> '') echo '<img src="' . 'components/com_ccboard/assets/icon_msn.gif" height="20" width="20" title="' . $postuser['msn'] . '" />&nbsp;';
													if($postuser['yahoo'] 	<> '') echo '<img src="' . 'components/com_ccboard/assets/icon_yahoo.gif" height="20" width="20" title="' . $postuser['yahoo'] . '" />&nbsp;';
													if($postuser['jabber'] 	<> '') echo '<img src="' . 'components/com_ccboard/assets/icon_jabber.gif" height="20" width="20" title="' . $postuser['jabber'] . '" />&nbsp;';
													if($postuser['skype'] 	<> '') echo '<img src="' . 'components/com_ccboard/assets/icon_skype.gif" height="20" width="20" title="' . $postuser['skype'] . '" />';
						                		?>
							                </span>
							            </td>
									</tr>
								<?php } ?>
							</table>
						</td>
						<td class="ccbpostarea">
							<table class="ccbposttable"  cellpadding="0" cellspacing="0">
								<tr>
									<td>
										<div  class="ccbkarma">
											<ul class="ccbkarmapanel">
												<li class="ccbkarmapanelrpt"><a rel="nofollow" href="<?php echo JRoute::_($this->posturl . 'list&forum=' . $item->forum_id . '&topic=' . $item->topic_id . '&post=' . $item->id . '&postreport=1&Itemid=' . $ccbConfig->itemid); ?>" title="<?php echo JText::_('REPORT_ABUSE'); ?>" ><img src="images/blank.gif" height="14" width="14" border="0" /></a></li>
												<?php if( $ccbConfig->showkarma > 0  && $item->post_user > 0 ) { ?>
													<li class="ccbkarmapaneltotal"><?php echo $postuser['karma']; ?></li>
                                                    <li class="ccbkarmapaneldown"><a rel="nofollow" href="<?php echo JRoute::_($this->posturl . 'list&task=karmaDown&forum=' . $item->forum_id . '&topic=' . $item->topic_id . '&post=' . $item->id . '&post_user=' . $item->post_user); ?>" title="<?php echo JText::_('VOTE_DOWN'); ?>"> <img src="images/blank.gif" height="14" width="14" border="0" /> </a></li>
													<li class="ccbkarmapanelup"><a rel="nofollow" href="<?php echo JRoute::_($this->posturl . 'list&task=karmaUp&forum=' . $item->forum_id . '&topic=' . $item->topic_id . '&post=' . $item->id . '&post_user=' . $item->post_user); ?>" title="<?php echo JText::_('VOTE_UP'); ?>"><img src="images/blank.gif" height="14" width="14" border="0" /> </a></li>
												<?php } ?>
											</ul>
										</div>
									</td>
								</tr>
								<tr>
									<td class="ccbposttextcol">
										<?php echo $this->labels['forum']; ?>
										<a href="<?php echo JRoute::_('index.php?option=com_ccboard&view=topiclist&forum='. $item->forum_id . '&Itemid='.$ccbConfig->itemid); ?>">
											<u><b><i><?php echo $this->escape($item->forum_name); ?></i></b></u>
										</a><br/>
										<?php echo $this->labels['topic']; ?>
										<a href="<?php echo JRoute::_('index.php?option=com_ccboard&view=postlist&forum='.$item->forum_id .'&topic='.$item->topic_id.'&Itemid='. $ccbConfig->itemid . $this->postlistmodel->getPageNumber($item->topic_id, $item->id) ); ?>">
											<u><b><i><?php echo $this->escape($item->topic_subject); ?></i></b></u>
										</a><br/><br/>
										<?php
										if( strlen($item->post_subject) > 70 ) {
											echo  '<u><b>'. $this->labels['subject'] . '<i>'. $this->escape($item->post_subject) . '</i></u></b><br/><br/>';
										}
										if( $ccbConfig->ccbeditor == 'ccboard' ) {
												echo $bbcode->parseContent($item->post_text);
											} else {
												echo $item->post_text;
											}
											$attachments = $this->postlistmodel->getAttachments( $item->id);
											if( count($attachments) > 0 ) {
												echo '<br/><div class="attachbox">';
												echo '<strong>' . JText::_('ATTACHMENTS') . '</strong>' ;
												foreach($attachments as $key => $value)
												{
													echo '<br/><img src="' . CCBOARD_ASSETS_URL . '/24-attach.png" />&nbsp;';
													echo '<a href="' . CCBOARD_ASSETS_URL . '/uploads/' . $value[0] . '">'. $value[1] . ' [' . (int) ($value[3]/1024) . ' KB]</a> :: '. $value[5];
												}
												echo '</div>';
											}
										?>
									</td>
								</tr>
							<?php if( (isset($item->ip)?$item->ip:'') <> '' ){ ?>
								<tr>
									<td class="ccbpostlogip">
										<img src="<?php echo CCBOARD_ASSETS_URL; ?>/ip.gif" />
										<small><?php echo $this->labels['logip']; ?></small>
									</td>
								</tr>
							<?php } ?>
							<?php if( $ccbConfig->showeditmarkup > 0 && $item->modified_time > 0) { ?>
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
</div>
<input type="hidden" name="option" value="com_ccboard" />
</form>
<br/><br/>
<?php echo CCBOARD_COPYRIGHT; ?>
<br/><br/>
