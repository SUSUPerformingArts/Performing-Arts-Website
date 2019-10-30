<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>
<?php
foreach ($list as $item){
modRandArticlesHelper::renderItem($item, $params, $access);
}
?>