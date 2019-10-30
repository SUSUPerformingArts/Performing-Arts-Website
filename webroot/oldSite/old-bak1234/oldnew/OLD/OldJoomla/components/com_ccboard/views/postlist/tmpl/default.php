<?php
/**
 * @version		$Id: default.php 174 2009-09-21 16:48:57Z thomasv $
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
		window.location= delpath;
	}
}

function submitbutton()
{
	var form = document.FrmPostlist;

	var text='';
    text= document.getElementById("post_text").value;
	if (text == '') {
		alert ( "<?php echo $this->labels['invalidtext']; ?>");
		return;
	}
	form.task.value="quickReply";
	form.submit();
}
</script>

<form action="<?php echo $this->action; ?>" method="post" name="FrmPostlist" id="FrmPostlist">
<div class="ccbmaindiv">
	<table class="ccbnormaltable" cellpadding="0" cellspacing="0">
		<tr>
	 		 <td class="ccbtopleft<?php echo $dispclr; ?>"></td>
			 <td class="ccbtopmiddle<?php echo $dispclr; ?>">&nbsp;<?php echo $ccbConfig->boardname . ' :: ' . $this->postlistmodel->getForumName();?> </td>
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
		<tr>
			<td colspan="3" class="ccbtpkmenu">
				<table class="ccbnormaltable" cellpadding="0" cellspacing="0">
					<tr>
						<?php if( count($this->items) > 0 ) { ?>
							<td class="ccbtpkmenucol">
							<?php if( $this->modactions || $this->allowposting ) { ?>
									<a rel="nofollow" class="ovalbutton<?php echo $dispclr; ?>" href="<?php echo JRoute::_($this->posturl . '&forum=' . ((int)$this->items[0]->forum_id) . '&topic=' . ((int)$this->items[0]->topic_id) . '&post=0&mode=reply&Itemid=' . $ccbConfig->itemid); ?>">
										<span><?php echo  $this->labels['postreply']; ?></span>
									</a>
							<?php }
								if( $ccbConfig->emailsub > 0 && $this->userprofile['user_id'] > 0 ) { ?>
									<a rel="nofollow" class="ovalbutton<?php echo $dispclr; ?>" href="<?php echo JRoute::_($this->posturl . 'list&task=emailsub&forum=' . ((int)$this->items[0]->forum_id) . '&topic=' . ((int)$this->items[0]->topic_id)); ?>">
										<span><?php echo $this->emailsub > 0 ? JText::_('EMAIL_UNSUBSCRIBE') : JText::_('EMAIL_SUBSCRIBE'); ?></span>
									</a>
							<?php }
								if( $ccbConfig->showfavourites > 0 && $this->userprofile['user_id'] > 0) { ?>
									<a rel="nofollow" class="ovalbutton<?php echo $dispclr; ?>" href="<?php echo JRoute::_($this->posturl . 'list&task=favourite&forum=' . ((int)$this->items[0]->forum_id) . '&topic=' . ((int)$this->items[0]->topic_id)); ?>">
										<span><?php echo ($this->favourite > 0 ? JText::_('CCB_UNFAVOURITE') : JText::_('CCB_FAVOURITE')); ?></span>
									</a>
							<?php }
							if( $this->modactions ) {
								if( $this->postLock > 0 ) { ?>
									<a rel="nofollow" class="ovalbutton<?php echo $dispclr; ?>" href="<?php echo JRoute::_($this->posturl . 'list&task=postunlock&forum=' . ((int)$this->items[0]->forum_id) . '&topic=' . ((int)$this->items[0]->topic_id)); ?>"><span><?php echo  $this->labels['postunlock']; ?></span></a>
								<?php } else { ?>
									<a rel="nofollow" class="ovalbutton<?php echo $dispclr; ?>" href="<?php echo JRoute::_($this->posturl . 'list&task=postlock&forum=' . ((int)$this->items[0]->forum_id) . '&topic=' . ((int)$this->items[0]->topic_id)); ?>"><span><?php echo  $this->labels['postlock']; ?></span></a>
								<?php } ?>
								<a rel="nofollow" class="ovalbutton<?php echo $dispclr; ?>" href="<?php echo JRoute::_($this->posturl . 'list&forum=' . ((int)$this->items[0]->forum_id) . '&topic=' . ((int)$this->items[0]->topic_id). '&postmove=1' ); ?>"><span><?php echo  $this->labels['postmove']; ?></span></a>
							<?php } ?>
							</td>
							<td class="ccbtpkpages"><?php echo $this->pagination->getPagesLinks(); ?></td>
						<?php } ?>
					</tr>
				</table>
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
            <td colspan="3"><a rel="nofollow" name="ccbp<?php echo $item->id; ?>"></a></td>
        </tr>
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
								<?php if ($ccbConfig->showrank  > 0 && $item->post_user > 0 ) { ?>
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
												echo '<br/><br/><div class="attachbox">';
												echo '<strong>' . JText::_('ATTACHMENTS') . '</strong>' ;
												foreach($attachments as $key => $value)
												{
													echo '<br/><img src="' . CCBOARD_ASSETS_URL . '/24-attach.png" />&nbsp;';
													echo '<a href="' . CCBOARD_ASSETS_URL . '/uploads/' . $value[0] . '">'. $value[1] . ' [' . (int) ($value[3]/1024) . ' KB]</a>';
                                                    if( $value[5] <> '' ) echo ' :: '.$value[5];
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
										<small><?php
												if( $this->modactions ) {
													echo $item->ip;
												}
												else {
													echo $this->labels['logip'];
												}
												?>
										</small>
									</td>
								</tr>
							<?php } ?>
							<?php if( $ccbConfig->showeditmarkup > 0 && $item->modified_time > 0) { ?>
								<tr>
									<td class="ccbpostmod">
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
											echo '<span class="ccbpostmodified">'.$this->labels['lastedit'] . ': ' .
												JHTML::_('date', $item->modified_time + $ccbConfig->timeoffset, $ccbConfig->dateformat) . ' ' .
												$this->labels['lasteditby'] . ' ' . $modifyuser['username'] . ' '.
												$this->labels['lasteditreason'] . ' ' . $item->modified_reason . '</span>' ;
										?>
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
									<?php
										$canedit = false;
										$tdiff = time() - $item->post_time;
										$locktime = $ccbConfig->editgracetime > 0 ? $ccbConfig->editgracetime  : $tdiff + 1;
										if( $this->userprofile['user_id'] == $item->post_user && $tdiff <= $locktime && $this->allowEditing > 0 ) {
											$canedit = true;
										}
									if( $this->modactions || $this->allowposting ) { ?>
										<a rel="nofollow" class="squarebutton" href="<?php echo JRoute::_($this->posturl . '&forum=' . $item->forum_id . '&topic=' . $item->topic_id . '&post=' . $item->id . '&mode=quote&Itemid=' . $ccbConfig->itemid ); ?>"><span><?php echo  $this->labels['postquote']; ?></span></a>
									<?php }
									if( $this->modactions || $canedit ) { ?>
										<a rel="nofollow" class="squarebutton" href="<?php echo JRoute::_($this->posturl . '&forum=' . $item->forum_id . '&topic=' . $item->topic_id . '&post=' . $item->id . '&mode=edit&Itemid='. $ccbConfig->itemid); ?>"><span><?php echo  $this->labels['postedit']; ?></span></a>
									<?php }
									if( $this->modactions) { ?>
										<a rel="nofollow" class="squarebutton" href="#" onclick='delconfirm( "<?php echo JRoute::_($this->posturl . 'list&task=deletePost&forum=' . $item->forum_id . '&topic=' . $item->topic_id . '&post=' . $item->id); ?>"); return false;' ><span><?php echo  $this->labels['postdelete']; ?></span></a>
										<?php if( $item->id != $this->startPost ) { ?>
											<a rel="nofollow" class="squarebutton" href="<?php echo JRoute::_($this->posturl . 'list&task=postsplit&forum=' . $item->forum_id . '&topic=' . $item->topic_id . '&post=' . $item->id); ?>"><span><?php echo  $this->labels['postsplit']; ?></span></a>
										<?php }
									}
									if( ($ccbConfig->showquickreply > 0) && ($this->modactions || $this->allowposting ) ) { ?>
										<a rel="nofollow" class="squarebutton" href="#ccbpquickreply"><span><?php echo  $this->labels['quickreply']; ?></span></a>
									<?php } ?>
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
		<tr>
			<td colspan="3" class="ccbtpkmenu">
				<table class="ccbnormaltable" cellpadding="0" cellspacing="0">
					<?php if( count($this->items) > 0 ) { ?>
						<tr>
							<td class="ccbtpkmenucolbot">
							<?php if( $this->modactions || $this->allowposting ) { ?>
									<a rel="nofollow" class="ovalbutton<?php echo $dispclr; ?>" href="<?php echo JRoute::_($this->posturl . '&forum=' . ((int)$this->items[0]->forum_id) . '&topic=' . ((int)$this->items[0]->topic_id) . '&post=0&mode=reply&Itemid=' . $ccbConfig->itemid); ?>">
										<span><?php echo  $this->labels['postreply']; ?></span>
									</a>
							<?php } ?>
							<?php if( $ccbConfig->emailsub > 0 && $this->userprofile['user_id'] > 0 ) { ?>
								<a rel="nofollow" class="ovalbutton<?php echo $dispclr; ?>" href="<?php echo JRoute::_($this->posturl . 'list&task=emailsub&forum=' . ((int)$this->items[0]->forum_id) . '&topic=' . ((int)$this->items[0]->topic_id)); ?>">
									<span><?php echo $this->emailsub > 0 ? JText::_('EMAIL_UNSUBSCRIBE') : JText::_('EMAIL_SUBSCRIBE'); ?></span>
								</a>
							<?php } ?>
							<?php if( $ccbConfig->showfavourites > 0 && $this->userprofile['user_id'] > 0) { ?>
								<a rel="nofollow" class="ovalbutton<?php echo $dispclr; ?>" href="<?php echo JRoute::_($this->posturl . 'list&task=favourite&forum=' . ((int)$this->items[0]->forum_id) . '&topic=' . ((int)$this->items[0]->topic_id)); ?>">
									<span><?php echo ($this->favourite > 0 ? JText::_('CCB_UNFAVOURITE') : JText::_('CCB_FAVOURITE')); ?></span>
								</a>
							<?php }
							if( $this->modactions ) {
								if( $this->postLock > 0 ) { ?>
									<a rel="nofollow" class="ovalbutton<?php echo $dispclr; ?>" href="<?php echo JRoute::_($this->posturl . 'list&task=postunlock&forum=' . ((int)$this->items[0]->forum_id) . '&topic=' . ((int)$this->items[0]->topic_id)); ?>"><span><?php echo  $this->labels['postunlock']; ?></span></a>
								<?php } else { ?>
									<a class="ovalbutton<?php echo $dispclr; ?>" href="<?php echo JRoute::_($this->posturl . 'list&task=postlock&forum=' . ((int)$this->items[0]->forum_id) . '&topic=' . ((int)$this->items[0]->topic_id)); ?>"><span><?php echo  $this->labels['postlock']; ?></span></a>
								<?php } ?>
								<a rel="nofollow" class="ovalbutton<?php echo $dispclr; ?>" href="<?php echo JRoute::_($this->posturl . 'list&forum=' . ((int)$this->items[0]->forum_id) . '&topic=' . ((int)$this->items[0]->topic_id) .'&postmove=1'); ?>"><span><?php echo  $this->labels['postmove']; ?></span></a>
							<?php } ?>
							</td>
							<td class="ccbtpkpages"><?php echo $this->pagination->getPagesLinks(); ?></td>
						</tr>
						<tr>
							<td></td>
							<td class="ccbtpkpagecount"><?php echo JText::_('PAGE') . ' #&nbsp;'. $this->pagination->getLimitBox(); ?></td>
						</tr>
					<?php } ?>
				</table>
			</td>
		</tr>
        <?php if( ($ccbConfig->showquickreply > 0) && ($this->modactions || $this->allowposting )) { ?>
            <tr><td colspan="3" class="ccbcolspacer"><a rel="nofollow" name="ccbpquickreply"></a></td></tr>
            <tr>
                 <td class="ccbtopleft<?php echo $dispclr; ?>"></td>
                 <td class="ccbtopmiddle<?php echo $dispclr; ?>">&nbsp;<?php echo $this->labels['quickreply']; ?></td>
                 <td class="ccbtopright<?php echo $dispclr; ?>"></td>
            </tr>
            <tr>
    			<td class="ccbleftbody"></td>
                <td class="ccbquickreply">
                    <textarea id="post_text" name = "post_text" style="width:100%; height: 150px;" ></textarea>
                    <br clear="left" />
                    <table class="ccbnormaltable" cellpadding="0" cellspacing="0">
                        <tr>
                            <?php if( $this->cap_path <> '' ) { ?>
                                <td class="ccbeditcaplabel"><strong><?php echo $this->labels['captcha']; ?>:</strong></td>
                                <td class="ccbeditcap"><img id="imgcap" name="imgcap" src="<?php echo $this->cap_path;?>"  /></td>
                                <td class="ccbeditcaptext"><input class="ccbeditcapinput" type="text" id="captcha" name="captcha" maxlength="5" autocomplete="off"/></td>
                            <?php } else {
                                echo '<td colspan="3"><input type="hidden" id="captcha" name="captcha" />&nbsp;</td>';
                            }
                            ?>
                            <td>
                                <div class="buttonarea">
                                    <a rel="nofollow" class="ovalbutton<?php echo $dispclr; ?>" href="#" onclick="submitbutton(); return false;">
                                        <span><?php echo $this->labels['save']; ?></span>
                                    </a>
                                </div>
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
        <?php } ?>
	</table>
</div>
<input type="hidden" id="option" name="option" value="com_ccboard" />
<input type="hidden" id="task" name="task" value="" />
<input type="hidden" id="id" name="id" value="0" />
<input type="hidden" id="forum_id" name="forum_id" value="<?php echo ((int)$this->items[0]->forum_id) ; ?>" />
<input type="hidden" id="topic_id" name="topic_id" value="<?php echo ((int)$this->items[0]->topic_id); ?>" />
<input type="hidden" id="mode" name="mode" value="reply" />
<input type="hidden" id="attachid" name="attachid" value="" />
<input type="hidden" id="username" name="username" value="<?php echo $this->userprofile['username']; ?>" />
<input type="hidden" id="topic_type" name="topic_type" value="0" />
<input type="hidden" id="topic_emoticon" name="topic_emoticon" value="0" />
<input type="hidden" id="post_subject" name="post_subject" value="<?php echo JText::_('POST_REPLY_PREFIX') . $this->items[0]->post_subject; ?>" />
<input type="hidden" id="autosub" name="autosub" value="<?php echo $ccbConfig->autosub; ?>" />
<input type="hidden" id="modified_reason" name="modified_reason" value="" />
<input type="hidden" name="<?php echo JUtility::getToken(); ?>" value="1" />
</form>
<br/><br/>
<?php echo CCBOARD_COPYRIGHT; ?>
<br/><br/>
