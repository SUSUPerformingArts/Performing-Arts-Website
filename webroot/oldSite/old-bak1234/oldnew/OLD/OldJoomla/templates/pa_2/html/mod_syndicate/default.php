<?php
defined('_JEXEC') or die;

if ($GLOBALS['version']->RELEASE == '1.6') {
  ?><a href="<?php echo $link ?>" class="art-rss-tag-icon syndicate-module<?php echo $moduleclass_sfx; ?>" title="<?php echo $text; ?>"></a><?php
} else {
  ?><a href="<?php echo $link ?>"
    class="art-rss-tag-icon syndicate-module<?php echo htmlspecialchars($params->get('moduleclass_sfx')); ?>"
    title="<?php echo $params->get('text'); ?>"></a><?php
}
