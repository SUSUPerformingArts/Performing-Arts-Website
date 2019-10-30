<?if( $sg == 'banner' ):?>	<?php if (JRequest::getVar('view') == 'frontpage'):?>	<!-- SIDE BEGIN --><!-- SIDE END -->	<?php endif?>
<?else:?>
 	<?php echo $mainframe->getCfg('sitename') ;?>, Powered by <a href="http://joomla.org/" class="sgfooter" target="_blank">Joomla!</a>	<?php if (JRequest::getVar('view') == 'frontpage'):?>	<!-- FOOTER BEGIN --><a href="http://www.siteground.com/joomla-hosting.htm" target="_blank">Expert hosting for Joomla by SiteGround</a><!-- FOOTER END -->	<?php endif?>
<?endif;?>
