/*****************************************/
// Name: Javascript Textarea BBCode Markup Editor
// Version: 1.3
// Author: Balakrishnan
// Last Modified Date: 25/jan/2009
// License: Free
// URL: http://www.corpocrat.com

// Modified by Thomas Date: 21/Feb/2009 for ccBoard http://codeclassic.org
/*****************************************************************************/

var textarea;
var content;
document.write("<link href=\""+_CCB_JS_EDITOR_PATH+"/styles.css\" rel=\"stylesheet\" type=\"text/css\">");


function edToolbar(obj, smileyOk) {
   
	document.write("<img class=\"button\" src=\""+_CCB_JS_EDITOR_PATH+"/images/bold.gif\" title=\"bold\" name=\"btnBold\" onClick=\"doAddTags('[b]','[/b]','" + obj + "')\">");
    document.write("<img class=\"button\" src=\""+_CCB_JS_EDITOR_PATH+"/images/italic.gif\" title=\"italic\" name=\"btnItalic\" onClick=\"doAddTags('[i]','[/i]','" + obj + "')\">");
	document.write("<img class=\"button\" src=\""+_CCB_JS_EDITOR_PATH+"/images/underline.gif\" title=\"underline\" name=\"btnUnderline\" onClick=\"doAddTags('[u]','[/u]','" + obj + "')\">");
	document.write("<img class=\"button\" src=\""+_CCB_JS_EDITOR_PATH+"/images/link.gif\" title=\"link\" name=\"btnLink\" onClick=\"doURL('" + obj + "')\">");
	document.write("<img class=\"button\" src=\""+_CCB_JS_EDITOR_PATH+"/images/picture.gif\" title=\"picture\" name=\"btnPicture\" onClick=\"doImage('" + obj + "')\">");
	document.write("<img class=\"button\" src=\""+_CCB_JS_EDITOR_PATH+"/images/ordered.gif\" title=\"ordered\" name=\"btnList\" onClick=\"doList('[LIST=1]','[/LIST]','" + obj + "')\">");
	document.write("<img class=\"button\" src=\""+_CCB_JS_EDITOR_PATH+"/images/unordered.gif\" title=\"unordered\" name=\"btnList\" onClick=\"doList('[LIST]','[/LIST]','" + obj + "')\">");
	document.write("<img class=\"button\" src=\""+_CCB_JS_EDITOR_PATH+"/images/quote.gif\" title=\"quote\" name=\"btnQuote\" onClick=\"doAddTags('[quote]','[/quote]','" + obj + "')\">"); 
  	document.write("<img class=\"button\" src=\""+_CCB_JS_EDITOR_PATH+"/images/code.gif\" title=\"code\" name=\"btnCode\" onClick=\"doAddTags('[code]','[/code]','" + obj + "')\">");

	document.write("<select class= \"button\" style=\"vertical-align:top;\" id=\"bbcolor\" name=\"bbcolor\" title=\"Color\" onChange = \"doAddTags('[color=' + this.form.bbcolor.options[this.form.bbcolor.selectedIndex].value + ']', '[/color]','" + obj +"'); \" >");
	document.write("<option style = \"color:black;   background-color: #FAFAFA\" value = \"\">Standard</option>");
	document.write("<option style = \"color:#FF0000; background-color: #FAFAFA\" value = \"#FF0000\">Red</option>");
	document.write("<option style = \"color:#800080; background-color: #FAFAFA\" value = \"#800080\">Purple</option>");
	document.write("<option style = \"color:#0000FF; background-color: #FAFAFA\" value = \"#0000FF\">Blue</option>");
	document.write("<option style = \"color:#008000; background-color: #FAFAFA\" value = \"#008000\">Green</option>");
	document.write("<option style = \"color:#FFFF00; background-color: #FAFAFA\" value = \"#FFFF00\">Yellow</option>");
	document.write("<option style = \"color:#FF6600; background-color: #FAFAFA\" value = \"#FF6600\">Orange</option>");
	document.write("<option style = \"color:#000080; background-color: #FAFAFA\" value = \"#000080\">Darkblue</option>");	
	document.write("<option style = \"color:#825900; background-color: #FAFAFA\" value = \"#825900\">Brown</option>");
	document.write("<option style = \"color:#9A9C02; background-color: #FAFAFA\" value = \"#9A9C02\">Gold</option>");
	document.write("<option style = \"color:#A7A7A7; background-color: #FAFAFA\" value = \"#A7A7A7\">Silver</option>");
	document.write("</select>");

	document.write("<select class= \"button\" style=\"vertical-align:top;\" name= \"bbsize\" id= \"bbsize\" onChange= \"doAddTags('[size=' + this.form.bbsize.options[this.form.bbsize.selectedIndex].value + ']', '[/size]','" + obj + "');  \" >");
	document.write("<option value=0>Tiny</option>");
	document.write("<option value=1>Very Small</option>");
	document.write("<option value=2>Small</option>");
	document.write("<option value=3 selected = \"selected\">Normal</option>");
	document.write("<option value=4>Medium Big</option>");
	document.write("<option value=5>Big</option>");
	document.write("<option value=6>Very Big</option>");
	document.write("<option value=7>Biggest</option>");
	document.write("</select>");

    document.write("<br>");
    if( smileyOk ) {
	    document.write("<img class=\"button\" src=\""+_CCB_JS_EDITOR_PATH+"/images/smileys/angry.gif\" 	title=\"angry\" name=\"btnCode\" onClick=\"doAddTags('>:(',' ','" + obj + "')\">");
	    document.write("<img class=\"button\" src=\""+_CCB_JS_EDITOR_PATH+"/images/smileys/anime.gif\" 	title=\"anime\" name=\"btnCode\" onClick=\"doAddTags('^_^',' ','" + obj + "')\">");
	    document.write("<img class=\"button\" src=\""+_CCB_JS_EDITOR_PATH+"/images/smileys/bigeyes.gif\" title=\"bigeyes\" name=\"btnCode\" onClick=\"doAddTags('8)',' ','" + obj + "')\">");
	    document.write("<img class=\"button\" src=\""+_CCB_JS_EDITOR_PATH+"/images/smileys/bigsmile.gif\" title=\"bigsmile\" name=\"btnCode\" onClick=\"doAddTags(':-D',' ','" + obj + "')\">");
	    document.write("<img class=\"button\" src=\""+_CCB_JS_EDITOR_PATH+"/images/smileys/bigwink.gif\" title=\"bigwink\" name=\"btnCode\" onClick=\"doAddTags(';D',' ','" + obj + "')\">");
	    document.write("<img class=\"button\" src=\""+_CCB_JS_EDITOR_PATH+"/images/smileys/blue.gif\" title=\"blue\" name=\"btnCode\" onClick=\"doAddTags(':blue:',' ','" + obj + "')\">");
	    document.write("<img class=\"button\" src=\""+_CCB_JS_EDITOR_PATH+"/images/smileys/boggle.gif\" title=\"boggle\" name=\"btnCode\" onClick=\"doAddTags('o.O',' ','" + obj + "')\">");
	    document.write("<img class=\"button\" src=\""+_CCB_JS_EDITOR_PATH+"/images/smileys/confuse.gif\" title=\"confuse\" name=\"btnCode\" onClick=\"doAddTags(':?',' ','" + obj + "')\">");
	    document.write("<img class=\"button\" src=\""+_CCB_JS_EDITOR_PATH+"/images/smileys/cool.gif\" title=\"cool\" name=\"btnCode\" onClick=\"doAddTags('B-)',' ','" + obj + "')\">");
	    document.write("<img class=\"button\" src=\""+_CCB_JS_EDITOR_PATH+"/images/smileys/evil.gif\" title=\"evil\" name=\"btnCode\" onClick=\"doAddTags('>:)',' ','" + obj + "')\">");
	    document.write("<img class=\"button\" src=\""+_CCB_JS_EDITOR_PATH+"/images/smileys/frown.gif\" title=\"frown\" name=\"btnCode\" onClick=\"doAddTags(':(',' ','" + obj + "')\">");
	    document.write("<img class=\"button\" src=\""+_CCB_JS_EDITOR_PATH+"/images/smileys/heart.gif\" title=\"heart\" name=\"btnCode\" onClick=\"doAddTags('<3',' ','" + obj + "')\">");
	    document.write("<img class=\"button\" src=\""+_CCB_JS_EDITOR_PATH+"/images/smileys/irritated.gif\" title=\"irritated\" name=\"btnCode\" onClick=\"doAddTags(':-/',' ','" + obj + "')\">");
		document.write("<img class=\"button\" src=\""+_CCB_JS_EDITOR_PATH+"/images/smileys/laugh.gif\" title=\"laugh\" name=\"btnCode\" onClick=\"doAddTags('X-D',' ','" + obj + "')\">");
		document.write("<img class=\"button\" src=\""+_CCB_JS_EDITOR_PATH+"/images/smileys/neutral.gif\" title=\"neutral\" name=\"btnCode\" onClick=\"doAddTags(':|',' ','" + obj + "')\">");
		document.write("<img class=\"button\" src=\""+_CCB_JS_EDITOR_PATH+"/images/smileys/saint.gif\" title=\"saint\" name=\"btnCode\" onClick=\"doAddTags('O:)',' ','" + obj + "')\">");
		document.write("<img class=\"button\" src=\""+_CCB_JS_EDITOR_PATH+"/images/smileys/sleepy.gif\" title=\"sleepy\" name=\"btnCode\" onClick=\"doAddTags(':zzz:',' ','" + obj + "')\">");
		document.write("<img class=\"button\" src=\""+_CCB_JS_EDITOR_PATH+"/images/smileys/smile.gif\" title=\"smile\" name=\"btnCode\" onClick=\"doAddTags(':)',' ','" + obj + "')\">");
		document.write("<img class=\"button\" src=\""+_CCB_JS_EDITOR_PATH+"/images/smileys/smile3.gif\" title=\"smile3\" name=\"btnCode\" onClick=\"doAddTags(':-3',' ','" + obj + "')\">");
		document.write("<img class=\"button\" src=\""+_CCB_JS_EDITOR_PATH+"/images/smileys/sneaky.gif\" title=\"sneaky\" name=\"btnCode\" onClick=\"doAddTags('>;)',' ','" + obj + "')\">");
		document.write("<img class=\"button\" src=\""+_CCB_JS_EDITOR_PATH+"/images/smileys/star.gif\" title=\"star\" name=\"btnCode\" onClick=\"doAddTags(':star:',' ','" + obj + "')\">");
		document.write("<img class=\"button\" src=\""+_CCB_JS_EDITOR_PATH+"/images/smileys/surprise.gif\" title=\"surprise\" name=\"btnCode\" onClick=\"doAddTags(':O',' ','" + obj + "')\">");
		document.write("<img class=\"button\" src=\""+_CCB_JS_EDITOR_PATH+"/images/smileys/teeth.gif\" title=\"teeth\" name=\"btnCode\" onClick=\"doAddTags('<g>',' ','" + obj + "')\">");
		document.write("<img class=\"button\" src=\""+_CCB_JS_EDITOR_PATH+"/images/smileys/tongue.gif\" title=\"tongue\" name=\"btnCode\" onClick=\"doAddTags(':P',' ','" + obj + "')\">");
		document.write("<img class=\"button\" src=\""+_CCB_JS_EDITOR_PATH+"/images/smileys/wink.gif\" title=\"wink\" name=\"btnCode\" onClick=\"doAddTags(';)',' ','" + obj + "')\">");
		document.write("<img class=\"button\" src=\""+_CCB_JS_EDITOR_PATH+"/images/smileys/worry.gif\" title=\"worry\" name=\"btnCode\" onClick=\"doAddTags(':-S',' ','" + obj + "')\">");
	    document.write("<br>");
	}
}

function doImage(obj)
{
textarea = document.getElementById(obj);
var url = prompt('Enter the Image URL:','http://');
var scrollTop = textarea.scrollTop;
var scrollLeft = textarea.scrollLeft;

	if (document.selection) 
			{
				textarea.focus();
				var sel = document.selection.createRange();
				sel.text = '[img]' + url + '[/img]';
			}
   else 
    {
		var len = textarea.value.length;
	    var start = textarea.selectionStart;
		var end = textarea.selectionEnd;
		
        var sel = textarea.value.substring(start, end);
	    //alert(sel);
		var rep = '[img]' + url + '[/img]';
        textarea.value =  textarea.value.substring(0,start) + rep + textarea.value.substring(end,len);
		
			
		textarea.scrollTop = scrollTop;
		textarea.scrollLeft = scrollLeft;
	}

}

function doURL(obj)
{
textarea = document.getElementById(obj);
var url = prompt('Enter the URL:','http://');
var scrollTop = textarea.scrollTop;
var scrollLeft = textarea.scrollLeft;

	if (document.selection) 
			{
				textarea.focus();
				var sel = document.selection.createRange();
				
			if(sel.text==""){
					sel.text = '[url]'  + url + '[/url]';
					} else {
					sel.text = '[url=' + url + ']' + sel.text + '[/url]';
					}			

				//alert(sel.text);
				
			}
   else 
    {
		var len = textarea.value.length;
	    var start = textarea.selectionStart;
		var end = textarea.selectionEnd;
		
        var sel = textarea.value.substring(start, end);
		
		if(sel==""){
				var rep = '[url]' + url + '[/url]';
				} else
				{
				var rep = '[url=' + url + ']' + sel + '[/url]';
				}
	    //alert(sel);
		
        textarea.value =  textarea.value.substring(0,start) + rep + textarea.value.substring(end,len);
		
			
		textarea.scrollTop = scrollTop;
		textarea.scrollLeft = scrollLeft;
	}
}

function doAddTags(tag1,tag2,obj)
{
textarea = document.getElementById(obj);
	// Code for IE
		if (document.selection) 
			{
				textarea.focus();
				var sel = document.selection.createRange();
				//alert(sel.text);
				sel.text = tag1 + sel.text + tag2;
			}
   else 
    {  // Code for Mozilla Firefox
		var len = textarea.value.length;
	    var start = textarea.selectionStart;
		var end = textarea.selectionEnd;
		
		
		var scrollTop = textarea.scrollTop;
		var scrollLeft = textarea.scrollLeft;

		
        var sel = textarea.value.substring(start, end);
	    //alert(sel);
		var rep = tag1 + sel + tag2;
        textarea.value =  textarea.value.substring(0,start) + rep + textarea.value.substring(end,len);
		
		textarea.scrollTop = scrollTop;
		textarea.scrollLeft = scrollLeft;
		
		
	}
}

function doList(tag1,tag2,obj){
textarea = document.getElementById(obj);
// Code for IE
		if (document.selection) 
			{
				textarea.focus();
				var sel = document.selection.createRange();
				var list = sel.text.split('\n');
		
				for(i=0;i<list.length;i++) 
				{
				list[i] = '[*]' + list[i];
				}
				//alert(list.join("\n"));
				sel.text = tag1 + '\n' + list.join("\n") + '\n' + tag2;
			} else
			// Code for Firefox
			{

		var len = textarea.value.length;
	    var start = textarea.selectionStart;
		var end = textarea.selectionEnd;
		var i;
		
		var scrollTop = textarea.scrollTop;
		var scrollLeft = textarea.scrollLeft;

		
        var sel = textarea.value.substring(start, end);
	    //alert(sel);
		
		var list = sel.split('\n');
		
		for(i=0;i<list.length;i++) 
		{
		list[i] = '[*]' + list[i];
		}
		//alert(list.join("<br>"));
        
		
		var rep = tag1 + '\n' + list.join("\n") + '\n' +tag2;
		textarea.value =  textarea.value.substring(0,start) + rep + textarea.value.substring(end,len);
		
		textarea.scrollTop = scrollTop;
		textarea.scrollLeft = scrollLeft;
 }
 
 
}
