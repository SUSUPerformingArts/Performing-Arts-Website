<?php
/**
* @version $Id: plgContentMultiAds.php 1.5
* @copyright Joe Guo
* @license GNU/GPLv2,
* @author Joe Guo - http://www.eboga.org
*/
defined( '_JEXEC' ) or  die('Restricted access');
jimport( 'joomla.event.plugin' );



class plgContentRandomContent extends JPlugin
{

	function plgContentRandomContent( &$subject, $params )
		{
		   parent::__construct( $subject, $params );	
		}	
	
	
 function onAfterDisplayContent(&$article, &$params){ 
           
         if(!$this->isArticleView()){
               return "";
         }  

         $catId=$article->catid; 
         if($this->exclude('Exclude_Category_Ids',$catId)){
            return "";     
         }

         $catIds=$this->param('Category_Ids');
         if($catIds){
            $ids = explode( ',', $catIds );
			   JArrayHelper::toInteger( $ids );
			   $catCondition = ' AND (a.catid=' . implode( ' OR a.catid=', $ids ) . ')';           
         }else{         
           $catCondition=' AND a.catid='.$catId;	
          }
         $user		=& JFactory::getUser();
         $aid		= $user->get('aid', 0);      	
         $db=& JFactory::getDBO();
		   $nullDate	= $db->getNullDate();
		   jimport('joomla.utilities.date');
		   $date = new JDate();
		   $now  = $date->toMySQL();         
              

        $query = 'SELECT a.sectionid,a.title,' .
			' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug,'.
			' CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug'.
			' FROM #__content AS a' .
			' LEFT JOIN #__content_frontpage AS f ON f.content_id = a.id' .
			' INNER JOIN #__categories AS cc ON cc.id = a.catid' .
			' INNER JOIN #__sections AS s ON s.id = a.sectionid' .
			' WHERE ( a.state = 1 AND s.id > 0 )' .
			' AND ( a.publish_up = '.$db->Quote($nullDate).' OR a.publish_up <= '.$db->Quote($now).' )' .
			' AND ( a.publish_down = '.$db->Quote($nullDate).' OR a.publish_down >= '.$db->Quote($now).' )'.
			' AND a.access <= ' .(int) $aid. ' AND cc.access <= ' .(int) $aid. ' AND s.access <= ' .(int) $aid .
			$catCondition.			
			' AND s.published = 1' .
			' AND cc.published = 1'.
			' ORDER BY RAND()';
     // return $query;
      $count=$this->param('count');      
       
		$db->setQuery($query, 0, $count);

		$rows = $db->loadObjectList();

    if(!$rows){
     return "";
     }
    
     $htmlResult1='<ul>';
     $htmlResult2='<ul>'; 
     $i=0;
		foreach ( $rows as $row )
		{
			$link = JRoute::_(ContentHelperRoute::getArticleRoute($row->slug, $row->catslug, $row->sectionid));
			$text = htmlspecialchars( $row->title );
		if($i==0){
         $htmlResult1.='<li>'.
                      '<a href="'.$link.'">'.
                            $text.'</a>'.
                       '</li>';	
         $i=1;
      }else{
            $htmlResult2.='<li>'.
                      '<a href="'.$link.'">'.
                            $text.'</a>'.
                       '</li>';	
          $i=0;       
       }		
       
		}
     $htmlResult1.='</ul>';
     $htmlResult2.='</ul>';
     $cssClass=$this->param('CssClass');
     if($cssClass){
        $cssClass=' class="'.$cssClass.'"';
     }
     $htmlResult='<table'.$cssClass.'>
     <tr><td colspan="2" align="center"><b>'.$this->param('tableHeader') .'</b></td></tr>
     <tr><td>'.$htmlResult1.'</td><td>'.$htmlResult2.'</td></tr>
     </table>';
     return $htmlResult;
 }
 
  
function isFrontPage(){
     if ((JRequest :: getVar('view')) == 'frontpage'){
         return true;
     }else{
         return false;
     }      
}

function isArticleView(){
     if ((JRequest :: getVar('view')) == 'article'){
         return true;
     }else{
         return false;
     } 

}
  
 function param($name){
      static $plugin,$pluginParams;
      if (!isset( $plugin )){ 
 	 	  $plugin =& JPluginHelper::getPlugin('content', 'RandomContent');
   	  $pluginParams = new JParameter( $plugin->params );
   	}
   	return trim($pluginParams->get($name));
 }

function exclude($paramName,$value){
     $excludeIds=$this->param($paramName);
     $excludeIdsArray=explode(',',$excludeIds);
     if(in_array($value,$excludeIdsArray,false)){
       return true;     
     } 
     return false;
 }
}
?>
