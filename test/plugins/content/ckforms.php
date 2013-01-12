<?php
/**
 * @version		$Id: ckforms.php 10714 2009-07-24 09:00:00Z pierrerevest$
 * @package		CKForms
 * @subpackage	Content
 * @copyright	Copyright (C) 2008 - 2009 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * CKForms! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

/**
 * CKforms Content Plugin
 *
 * @package		CKforms
 * @subpackage	Content
 * @since 		1.3.4
 */
class plgContentCkForms extends JPlugin
{

	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @param object $subject The object to observe
	 * @param object $params  The object that holds the plugin parameters
	 * @since 1.3.1
	 */
	function plgContentCkForms( &$subject, $params )
	{
		parent::__construct( $subject, $params );
		JPlugin::loadLanguage( 'plg_content_ckforms' );
		
	}

	/**
	 * CKForms prepare content method
	 *
	 * Method is called by the view
	 *
	 * @param 	object		The article object.  Note $article->text is also available
	 * @param 	object		The article params
	 * @param 	int			The 'page' number
	 */
	 
	function onPrepareContent( &$article, &$params, $limitstart )
    {
        $regex = '/{ckform\s*(.*?)}/i';
		
        // find all instances of plugin and put in $matches
        preg_match_all( $regex, $article->text, $matches );

        // Number of plugins
        $count = count( $matches[0] );

		$j = 0;
        if ($count > 0) {
            foreach ($matches[1] as $i => $textFound) 
			{
            	// Load Selected form
                $form = $this->loadCkForm($textFound);
				if ($form == null)
				{
				   $html = "<span style='color: #FF0000'>CKForms Error Message : <br />Form  '" . $textFound . "' not found !!!</span>";
				   $article->text = str_replace($matches[0][$j], $html, $article->text);
				   
				   return;
				}
                            
                $fields = $this->loadCkFormFields($form->id);

                $_SESSION['ck_send_once'.$form->id] = "1";

                $this->formLink = "index.php?option=com_ckforms&view=ckforms&task=send&id=".$form->id;

                $html = $this->generateCkFormHTML( $form, $fields);

                $article->text = str_replace($matches[0][$i], $html, $article->text);
				$j++;
            }
        }
	}

	/**
	 * CKForms load form data method
	 *
	 * Method is called by onPrepareContent method
	 *
	 * @param 	object		The form name
	 * @return	object
	 */
	function loadCkForm($name)
	{
		$db =& JFactory::getDBO();
		$query = "SELECT * FROM #__ckforms where name='".$name."'";
						
		$db->setQuery( $query );
		$data = $db->loadObject();

		return $data;
	}

	/**
	 * CKForms load fields data method
	 *
	 * Method is called by onPrepareContent method
	 *
	 * @param 	int		The form id
	 * @return	object
	 */
	function loadCkFormFields($id)
	{
		$db =& JFactory::getDBO();
		$query = ' SELECT * FROM #__ckfields where fid='.$id." and published=1 order by ordering asc" ;
		$db->setQuery($query);
		$fields = $db->loadObjectList();
		
		$n=count($fields);
		for ($i=0; $i < $n; $i++)
		{ 
			$opt = explode("[--]", $fields[$i]->defaultvalue);
			
			switch ($fields[$i]->typefield)
			{
				case 'text':
					if (count($opt) > 0) {
						$key1 = explode("===", $opt[0]);
						$fields[$i]->t_initvalueT = $key1[1];
					} else {
						$fields[$i]->t_initvalueT = '';
					}
					if (count($opt) > 1) {
						$key2 = explode("===", $opt[1]);
						$fields[$i]->t_maxchar = $key2[1];
					} else {
						$fields[$i]->t_maxchar = '';
					}
					if (count($opt) > 2) {
						$key3 = explode("===", $opt[2]);
						$fields[$i]->t_texttype = $key3[1];	
					} else {
						$fields[$i]->t_texttype = '';
					}
					if (count($opt) > 3) {
						$key4 = explode("===", $opt[3]);
						$fields[$i]->t_minchar = $key4[1];		
					} else {
						$fields[$i]->t_minchar = '';
					}
					if (count($opt) > 4) {
						$key5 = explode("===", $opt[4]);
						$fields[$i]->d_format = $key5[1];		
					} else {
						$fields[$i]->d_format = '';
					}
					if (count($opt) > 5) {
						$key6 = explode("===", $opt[5]);
						$fields[$i]->d_daydate = $key6[1];		
					} else {
						$fields[$i]->d_daydate = '';
					}
					
					if (strcmp($fields[$i]->t_initvalueT,'') == 0 && strcmp($fields[$i]->t_texttype,'date') == 0 &&  strcmp($fields[$i]->d_daydate,'1') == 0) 
					{
						$fields[$i]->t_initvalueT = date('d/m/Y');
					}
					
					if (strcmp($fields[$i]->fillwith,'inival') != 0) 
					{
						$user = & JFactory::getUser(); 
						if (strcmp($fields[$i]->t_texttype,'text') == 0 && strcmp($fields[$i]->fillwith,'usrname') == 0) 
						{
							$fields[$i]->t_initvalueT = $user->name;
						} 
						else if (strcmp($fields[$i]->t_texttype,'text') == 0 && strcmp($fields[$i]->fillwith,'usrusername') == 0) 
						{
							$fields[$i]->t_initvalueT = $user->username;
						}
						else if (strcmp($fields[$i]->t_texttype,'email') == 0 && strcmp($fields[$i]->fillwith,'usremail') == 0) 
						{
							$fields[$i]->t_initvalueT = $user->email;
						}
					}
					
					break;
	
				case 'hidden':
					if (count($opt) > 0) {
						$key1 = explode("===", $opt[0]);
						$fields[$i]->t_initvalueH = $key1[1];
					} else {
						$fields[$i]->t_initvalueH = '';
					}
					if (count($opt) > 1) {
						$key2 = explode("===", $opt[1]);
						$fields[$i]->t_filluid = $key2[1];
					} else {
						$fields[$i]->t_filluid = '';
					}
						
					break;
					
				case 'textarea':
					if (count($opt) > 0) {
						$key1 = explode("===", $opt[0]);
						$fields[$i]->t_initvalueTA = $key1[1];
					} else {
						$fields[$i]->t_initvalueTA = '';
					}
					if (count($opt) > 1) {
						$key2 = explode("===", $opt[1]);
						$fields[$i]->t_HTMLEditor = $key2[1];
					} else {
						$fields[$i]->t_HTMLEditor = '';
					}
					if (count($opt) > 2) {
						$key3 = explode("===", $opt[2]);
						$fields[$i]->t_columns = $key3[1];
					} else {
						$fields[$i]->t_columns = '';
					}
					if (count($opt) > 3) {
						$key4 = explode("===", $opt[3]);
						$fields[$i]->t_rows = $key4[1];
					} else {
						$fields[$i]->t_rows = '';
					}
					if (count($opt) > 4) {
						$key5 = explode("===", $opt[4]);
						$fields[$i]->t_wrap = $key5[1];					
					} else {
						$fields[$i]->t_wrap = '';
					}
					if (count($opt) > 5) {
						$key6 = explode("===", $opt[5]);
						$fields[$i]->t_maxchar = $key6[1];
					} else {
						$fields[$i]->t_maxchar = '';
					}
					if (count($opt) > 6) {
						$key7 = explode("===", $opt[6]);
						$fields[$i]->t_minchar = $key7[1];							
					} else {
						$fields[$i]->t_minchar = '';
					}

					break;
	
				case 'checkbox':
					if (count($opt) > 0) {
						$key1 = explode("===", $opt[0]);
						$fields[$i]->t_initvalueCB = $key1[1];
					} else {
						$fields[$i]->t_initvalueCB = '';
					}
					if (count($opt) > 0) {
						$key2 = explode("===", $opt[1]);
						$fields[$i]->t_checkedCB = $key2[1];										
					} else {
						$fields[$i]->t_checkedCB = '';
					}
					break;
					
				case 'radiobutton':
					if (count($opt) > 0) {
						$key1 = explode("===", $opt[0]);
						$fields[$i]->t_listHRB = $key1[1];
					} else {
						$fields[$i]->t_listHRB = '';
					}
					if (count($opt) > 1) {
						$key2 = explode("===", $opt[1]);
						$fields[$i]->t_displayRB = $key2[1];
					} else {
						$fields[$i]->t_displayRB = '';
					}
					break;

				case 'select':
					if (count($opt) > 0) {
						$key1 = explode("===", $opt[0]);
						$fields[$i]->t_multipleS = $key1[1];
					} else {
						$fields[$i]->t_multipleS = '';
					}
					if (count($opt) > 1) {
						$key2 = explode("===", $opt[1]);
						$fields[$i]->t_heightS = $key2[1];
					} else {
						$fields[$i]->t_heightS = '';
					}
					if (count($opt) > 2) {
						$key3 = explode("===", $opt[2]);
						$fields[$i]->t_listHS = $key3[1];					
					} else {
						$fields[$i]->t_listHS = '';
					}
					break;

				case 'button':
					if (count($opt) > 0) {
						$key1 = explode("===", $opt[0]);
						$fields[$i]->t_typeBT = $key1[1];
					} else {
						$fields[$i]->t_typeBT = '';
					}
					break;

				case 'fieldsep':
					$fields[$i]->t_noborderFS = '0';
					if (count($opt) > 0) {
						$key1 = explode("===", $opt[0]);
						if (count($key1) > 1)
						{
							$fields[$i]->t_noborderFS = $key1[1];
						}
					}
					break;
			}					
		
		}				
		
		return $fields;
	}

	/**
	 * CKForms generate form HTML method
	 *
	 * Method is called by onPrepareContent method
	 *
	 * @param 	object				The form data
	 * @param 	array of objects	The fields data
	 * @return	String
	 */
	function generateCkFormHTML( $form, $fields)
	{
		global $mainframe;

		if ($form->published != '1') return '';
		
		$plugin	=& JPluginHelper::getPlugin('content', 'ckforms');
		$pluginParams = new JParameter( $plugin->params );
		$displaytitle = $pluginParams->get( 'displaytitle', '1');
		$pageclass_sfx = $pluginParams->get( 'pageclass_sfx', '');

		$html = '<link rel="stylesheet" href="'.JURI::root(true).'/components/com_ckforms/css/calendar.css" type="text/css" />'."\n";
		$html = $html.'<link rel="stylesheet" href="'.JURI::root(true).'/components/com_ckforms/css/ckforms.css" type="text/css" />'."\n";
		$html = $html.'<link rel="stylesheet" href="'.JURI::root(true).'/components/com_ckforms/css/tips.css" type="text/css" />'."\n";
		$html = $html.'<link rel="stylesheet" href="'.JURI::root(true).'/components/com_ckforms/js/theme/classic/formcheck.css" type="text/css" />'."\n";

		$html = $html.'<script type="text/javascript" src="'.JURI::root(true).'/media/system/js/mootools.js"></script>'."\n";
		$html = $html.'<script type="text/javascript" src="'.JURI::root(true).'/components/com_ckforms/js/calendar.js"></script>'."\n";
		$html = $html.'<script type="text/javascript" src="'.JURI::root(true).'/components/com_ckforms/js/formcheck.js"></script>'."\n";
		
		$nbFields=count($fields);

		if (strcmp ($displaytitle , "1" ) == 0) 
		{
			$html = $html.'<div class="componentheading'.$pageclass_sfx.'">'.$form->title.'</div>'."\n";
		}
		
		$mandatory = false;
		$upload = false;
		$custominfo = false;
		$textareaRequired = false;
		for ($i=0;$i < $nbFields; $i++)
		{ 
			$field = $fields[$i];
			if ($field->mandatory == "1") $mandatory = true;
			if ($field->typefield == "fileupload") $upload = true;
			if ($field->custominfo != "") $custominfo = true;
			if ($field->typefield == 'textarea' && $field->mandatory == '1' && $field->t_HTMLEditor == '1') $textareaRequired = true;
		}
		

		$html = $html.'<script type="text/javascript">'."\n";

		$html = $html.'window.addEvent(\'domready\', function(){'."\n";
		$html = $html.'var myTips = new Tips(\'.ckform_tooltip'.$form->id.'\', {'."\n";
		$html = $html.'initialize:function(){'."\n";
		$html = $html.'	this.fx = new Fx.Style(this.toolTip, \'opacity\', {duration: 250, wait: false}).set(0);'."\n";
		$html = $html.'},'."\n";
		$html = $html.'onShow: function(toolTip) {'."\n";
		$html = $html.'	this.fx.start(1);'."\n";
		$html = $html.'},'."\n";
		$html = $html.'onHide: function(toolTip) {'."\n";
		$html = $html.'	this.fx.start(0);'."\n";
		$html = $html.'}'."\n";
		$html = $html.'});'."\n";


		if ($textareaRequired == true)
		{
	

		$html = $html.'$(\'ckform'.$form->id.'\').onsubmit = function(event){'."\n";
	
		for ($i=0;$i < $nbFields; $i++)
		{ 
			$field = $fields[$i];
			if ($field->typefield == 'textarea' && $field->mandatory == 1 && $field->t_HTMLEditor == 1) 
			{

			$html = $html.'if ($chk($(\''.$field->name.'\')) && $chk($(\''.$field->name.'Cont\'))) {'."\n";
			$html = $html.'$(\''.$field->name.'Cont\').setProperty(\'value\', tinyMCE.get(\''.$field->name.'\').getContent());'."\n";
			$html = $html.'}'."\n";

			}
		}
		
		$html = $html.'};'."\n";

		}

		$html = $html.'var myForm = new FormCheck(\'ckform'.$form->id.'\', {'."\n";
		$html = $html.'fieldErrorClass : \'error\','."\n";
		$html = $html.'validateDisabled : true,'."\n";
		$html = $html.'display : {'."\n";
		$html = $html.'	showErrors : 1,'."\n";
		$html = $html.'	errorsLocation : 3,'."\n";
		$html = $html.'	indicateErrors : 2,'."\n";
		$html = $html.'	tipsPosition : \'right\', '."\n";
		$html = $html.'	addClassErrorToField : true,'."\n";
		$html = $html.'	scrollToFirst : true'."\n";
		$html = $html.'},'."\n";
		$html = $html.'alerts : {'."\n";
		$html = $html.'	required:\''.addslashes(JText::_( 'This field is required.' )).'\','."\n";
		$html = $html.'	number:\''.addslashes(JText::_( 'Please enter a valid number.' )).'\','."\n";
		$html = $html.'	email:\''.addslashes(JText::_( 'Please enter a valid email: <br /><span>E.g. yourname&#64;domain.com</span>' )).'\','."\n";
		$html = $html.'	url:\''.addslashes(JText::_( 'Please enter a valid url: <br /><span>E.g. http://www.domain.com</span>' )).'\','."\n";
		$html = $html.'	confirm:\''.addslashes(JText::_( 'This field is different from %0' )).'\','."\n";
		$html = $html.'	length_str:\''.addslashes(JText::_( 'The length is incorrect, it must be between %0 and %1' )).'\','."\n";
		$html = $html.'	lengthmax:\''.addslashes(JText::_( 'The length is incorrect, it must be at max %0' )).'\','."\n";
		$html = $html.'	lengthmin:\''.addslashes(JText::_( 'The length is incorrect, it must be at least %0' )).'\','."\n";
		$html = $html.'	checkbox:\''.addslashes(JText::_( 'Please check the box' )).'\','."\n";
		$html = $html.'	radios:\''.addslashes(JText::_( 'Please select a radio' )).'\','."\n";
		$html = $html.'	select:\''.addslashes(JText::_( 'Please choose a value' )).'\''."\n";
		$html = $html.'}'."\n";

		$html = $html.'})'."\n";

		$html = $html.'$(document.body).getElements(\'.captcharefresh\').addEvents({'."\n";
		$html = $html.'\'click\': function(){'."\n";
		$html = $html.'	if($chk($(\'captchacode\'))) { '."\n";
		$html = $html.'		$(\'captchacode\').setProperty(\'src\', \'index.php?option=com_ckforms&task=captcha&sid=\' + Math.random());'."\n";
		$html = $html.'	}'."\n";
		$html = $html.'}'."\n";
		$html = $html.'});'."\n";

		for ($i=0;$i < $nbFields; $i++)
		{ 
			$field = $fields[$i];
			if ($field->typefield == 'text' && $field->t_texttype == 'date') {
			
			// Chercher le format de la locale du l'utilisateur
				$dformat = "d/m/Y";
				if ($field->d_format != '0')
				{
					if ($field->d_format =='EUR')
					{
						$dformat = "d/m/Y";
					} else {
						$dformat = "m/d/Y";
					}
				}

            $html = $html.'var myCal'.$field->id.' = new Calendar('."\n";
            $html = $html.'	{'.$field->name.': \''.$dformat.'\'},'."\n";
            $html = $html.'	{ '."\n";
            $html = $html.'			days: [\''.addslashes(JText::_( 'Sunday' )).'\',\''.addslashes(JText::_( 'Monday' )).'\',\''.addslashes(JText::_( 'Tuesday' )).'\',\''.addslashes(JText::_( 'Wednesday' )).'\',\''.addslashes(JText::_( 'Thursday' )).'\',\''.addslashes(JText::_( 'Friday' )).'\',\''.addslashes(JText::_( 'Saturday' )).'\'], '."\n";
            $html = $html.'			months: [\''.addslashes(JText::_( 'January' )).'\',\''.addslashes(JText::_( 'February' )).'\',\''.addslashes(JText::_( 'March' )).'\',\''.addslashes(JText::_( 'April' )).'\',\''.addslashes(JText::_( 'May' )).'\',\''.addslashes(JText::_( 'June' )).'\',\''.addslashes(JText::_( 'July' )).'\',\''.addslashes(JText::_( 'August' )).'\',\''.addslashes(JText::_( 'September' )).'\',\''.addslashes(JText::_( 'October' )).'\',\''.addslashes(JText::_( 'November' )).'\',\''.addslashes(JText::_( 'December' )).'\'],'."\n";
            $html = $html.'			direction: 0,'."\n";
            $html = $html.'			navigation: 2,'."\n";
            $html = $html.'			tweak: {x: 6, y: 0}'."\n";
            $html = $html.'		}'."\n";
            $html = $html.'	);'."\n";

		}
	}
	
	$html = $html.'});'."\n";

	$html = $html.'</script>'."\n";		
		
	$html = $html.'<table class="contentpaneopen '.$pageclass_sfx.'" id="ckformcontainer">'."\n";	
	$html = $html.'<tr><td>'."\n";	

	if (strcmp ( $form->description , "" ) != 0) {
		$html = $html.'<p>'.$form->description.'</p>'."\n";	
	}

	if ($mandatory == true)
	{
		$html = $html.'<p class="ck_mandatory">'.JText::_( 'Required' ).' *</p>'."\n";	
	}

	$html = $html.'<form action="'.$this->formLink.'" method="post" name="ckform" id="ckform'.$form->id.'" class="ckform '.$form->formCSSclass.'"';
	if($upload == true) 
	{ 		
		$html = $html.' enctype="multipart/form-data"';
	} 
	$html = $html.'>'."\n";	
    
   	$html = $html.'<input name="id" id="id" type="hidden" value="'.$form->id.'" />'."\n";
	$html = $html.'<input name="articleid" id="articleid" type="hidden" value="'.JRequest::getVar('id').'" />'."\n";	

	if($upload == true) 
	{
		$html = $html.'<input type="hidden" name="MAX_FILE_SIZE" value="'.$form->maxfilesize.'" />'."\n";	
	} 
 
	for ($i=0;$i < $nbFields; $i++)
	{ 
		$field = $fields[$i];
		if ($field->typefield == "hidden")
		{
        	$html = $html.'<input name="'.$field->name.'" id="'.$field->name.'" type="hidden" value="';
			if ($field->t_filluid == "1") 
			{
				$html = $html.uniqid($field->t_initvalueH,true);
			} else {
				$html = $html.$field->t_initvalueH;
			}
			$html = $html.'" />'."\n";	
		}
	}
		
	for ($i=0;$i < $nbFields; $i++)
	{ 
		$field = $fields[$i];
		
		if ($field->typefield != "hidden" && $field->typefield != "button" && $field->typefield != "fieldsep")
		{
	
			$validationclass = "validate[";
									 
			if ($field->mandatory == "1") {
				$validationclass = $validationclass."'required',";
			}
			if ($field->typefield == 'text' || $field->typefield == 'textarea')
			{
				$min = "0";
				if ($field->t_minchar != '')
				{
					$min = $field->t_minchar;
				}
				$max = "-1";
				if ($field->t_maxchar != '')
				{
					$max = $field->t_maxchar;
				}
				if ($min != '0' || $max != '-1')
				{				
					if ($field->typefield == 'text' && $field->t_texttype == 'number') 
					{
						$validationclass = $validationclass."'digit[".$min.",".$max."]',";
					} else {
						$validationclass = $validationclass."'length[".$min.",".$max."]',";
					}					
				}
			}

			if ($field->typefield == 'text' && $field->t_texttype == 'email') {
				$validationclass = $validationclass."'email',";
			} 
 
			else if ($field->typefield == 'text' && $field->t_texttype == 'url') {
				$validationclass = $validationclass."'url',";
			}

			$validationclass = rtrim($validationclass,',')."]";						 
									       
			$html = $html.'<label class="ckCSSlabel';
			if ($field->custominfo != "" && $field->typefield == "textarea")
			{
				$html = $html.' ckCSSbot5 ';
			}
			$html = $html.$field->labelCSSclass.'" id="'.$field->name.'lbl" for="'.$field->name.'"> '.$field->label."\n";	
 
			if ($field->mandatory == '1') 
			{ 
		    	$html = $html.'&nbsp;<span class="ck_mandatory">*</span>'."\n";	
			}
			
			if ($field->custominfo != "" && $field->typefield == "textarea") 
    		{
				$html = $html.'<img class="ckform_tooltip'.$form->id.' ckform_tooltipcss" src="'.JURI::root(true).'/components/com_ckforms/'.'img/info.png" />'."\n";	
			}
			$html = $html.'</label>'."\n";	

			switch ($field->typefield)
			{
				case 'text':    		
		
				if ($field->t_texttype == 'text' || $field->t_texttype == 'number' || $field->t_texttype == 'email' || $field->t_texttype == 'url')
				{
					$html = $html.'<input type="text" name="'.$field->name.'" value="';
					if (empty($this->post) ==false) 
					{
						$html = $html.$this->post[$field->name];
					} else {
						$html = $html.$field->t_initvalueT;
					}
					$html = $html.'" class="'.$validationclass.' inputbox ckCSSinput ';
					if ($field->custominfo != "") 
					{
						$html = $html.'ckform_tooltip'.$form->id.' ckCSSTip ';
					}
					$html = $html.$field->fieldCSSclass.'" ';
					if ($field->readonly == "1") 
					{
						$html = $html.' readonly="true"';
					}
					$html = $html.' title="'.$field->custominfo.'" />'."\n";	
				}
				
				else if ($field->t_texttype == 'password' )
				{	
        			$html = $html.'<input type="password" name="'.$field->name.'" value="';
					if (empty($this->post) ==false) 
					{
						$html = $html.$this->post[$field->name];
					} else {
						$html = $html.$field->t_initvalueT;
					}
					$html = $html.'" class="'.$validationclass.' inputbox ckCSSinput ';
					if ($field->custominfo != "") 
					{
						$html = $html.'ckCSSTip';
					} else {
						$html = $html.'ckCSSnoTip';
					}
					$html = $html.$field->fieldCSSclass.'" ';
					if ($field->readonly == "1") 
					{
						$html = $html.' readonly="true"';
					}
					$html = $html.' />'."\n";	
				}
				
				else if ($field->t_texttype == 'date' )
				{
					$html = $html.'<input type="text" name="'.$field->name.'" id="'.$field->name.'" value="';
					if (empty($this->post) == false) 
					{
						$html = $html.$this->post[$field->name];
					} else {
						$html = $html.$field->t_initvalueT;
					}
					$html = $html.'" class="'.$validationclass.' inputbox ckCSSinput '.$field->fieldCSSclass.'" maxlength="10" ';
					if ($field->readonly == "1") 
					{
						$html = $html.' readonly="true"';
					}
					$html = $html.' />'."\n";	
				}
				break; 		

				case 'fileupload':

					$html = $html.'<input name="'.$field->name.'" type="file" class="'.$validationclass.' ckCSSinput ';
					if ($field->custominfo != "") 
					{
						$html = $html.'ckCSSTip';
					} else {
						$html = $html.'ckCSSnoTip';
					}
					$html = $html.$field->fieldCSSclass.'" ';
					if ($field->readonly == "1") 
					{
						$html = $html.' readonly="true"';
					}
					$html = $html.' />'."\n";	
				break; 	
	
				case 'textarea':
					if ($field->t_HTMLEditor == 1 &&  $field->readonly != "1") 
					{			
						$html = $html.'<div class="ckCSSclear ckCSSbot10">'."\n";
        				$html = $html.'<input style="float: right; margin-right: 20px; height: 1px; visibility:hidden;" type="text" class="'.$validationclass.'" name="'.$field->name.'Cont" id="'.$field->name.'Cont" value="" />'."\n";

						$INIThtml = $field->t_initvalueTA;
						if (empty($this->post) ==false) 
						{
							$INIThtml = $this->post[$field->name];
						}				
		
						$editorDesc = JFactory::getEditor();
						$editor_param['smilies'] = '0';
						$editor_param['layer'] = '0';
						$html = $html.$editorDesc->display($field->name, $INIThtml, '97%', 200, $field->t_columns, $field->t_rows,true,$editor_param);
				
       					$html = $html.'</div>'."\n";

					} else {
				
       					$html = $html.'<textarea class="'.$validationclass.' ckCSSinput '.$field->fieldCSSclass.'" name="'.$field->name.'" cols="'.$field->t_columns.'" rows="'.$field->t_rows.'" wrap="'.$field->t_wrap.'" ';
						if ($field->readonly == "1") 
						{
							$html = $html.' readonly="true"';
						}
						$html = $html.'>';
						if (empty($this->post) ==false) 
						{
							$html = $html.$this->post[$field->name];
						} else {
							$html = $html.$field->t_initvalueTA;
						}
						$html = $html.'</textarea>'."\n";
		            }
				break;
			
				case 'checkbox':
				
					if (empty($this->post) ==false && isset($this->post[$field->name])) 
					{
						$field->t_checkedCB = '1';
					}				

					$html = $html.'<input class="'.$validationclass.' ckCSStop10 '.$field->fieldCSSclass.'" name="'.$field->name.'" type="checkbox" value="'.$field->t_initvalueCB.'" ';
					if ($field->t_checkedCB == '1') 
					{
						$html = $html.' checked ';
					}
					if ($field->readonly == "1") 
					{
						$html = $html.' readonly="true"';
					}
					$html = $html.' />'."\n";
				break; 					
		
				case 'radiobutton':
		
					if ($field->t_displayRB == '' || $field->t_displayRB == 'INL')
					{
						$opt = explode("[-]", $field->t_listHRB);
						$k=count($opt);
						
						for ($j=0; $j < $k; $j++)
						{	
							$checked = "";
							$val = explode("==", $opt[$j]);
							$key = explode("||", $val[1]);
							$ipos = strpos ($key[1],' [default]');
							
							if (empty($this->post) == false && isset($this->post[$field->name])
								 && $this->post[$field->name] == $key[0]) 
							{
								$checked = "checked";
							} 
							else if ($ipos != false && (empty($this->post) == true || isset($this->post[$field->name]) == false)) 
							{
								$checked = "checked";
								$key[1] = substr($key[1],0,$ipos);
							}
							
							$html = $html.'<input class="'.$validationclass.' ckCSStop10 '.$field->fieldCSSclass.'" name="'.$field->name.'" type="radio" value="'.$key[0].'" '.$checked.' ';
							if ($field->readonly == "1") 
							{
								$html = $html.' readonly="true"';
							}
							$html = $html.' />&nbsp;'.$key[1].'&nbsp'."\n";
						} 
					
					}
					else 
					{

						$opt = explode("[-]", $field->t_listHRB);
						$k=count($opt);
						echo '<div class="ckCSSinput '.$field->fieldCSSclass.'">';
						for ($j=0; $j < $k; $j++)
						{	
							$checked = "";
							$val = explode("==", $opt[$j]);
							$key = explode("||", $val[1]);
							$ipos = strpos ($key[1],' [default]');
							
							if (empty($this->post) == false && isset($this->post[$field->name])
								 && $this->post[$field->name] == $key[0]) 
							{
								$checked = "checked";
							} 
							else if ($ipos != false && (empty($this->post) == true || isset($this->post[$field->name]) == false)) 
							{
								$checked = "checked";
								$key[1] = substr($key[1],0,$ipos);
							}					
		
							if($j!=0){
								$html = $html.'<br />'."\n";
							}

							$html = $html.'<input class="'.$validationclass.'" name="'.$field->name.'" type="radio" value="'.$key[0].'" '.$checked.' ';
							if ($field->readonly == "1") 
							{
								$html = $html.' readonly="true"';
							}
							$html = $html.' />	&nbsp;'.$key[1]."\n";
				
						} 
						$html = $html.'</div>';
		
					}					
				break;
			
				case 'select':

					$html = $html.'<select class="'.$validationclass.' ckCSSinput '.$field->fieldCSSclass.'" name="'.$field->name.'[]" size="'.$field->t_heightS.'" ';
					if ($field->t_multipleS == '1') 
					{
						$html = $html.' multiple';
					}
					if ($field->readonly == "1") 
					{
						$html = $html.' readonly="true"';
					}
					$html = $html.'>'."\n";
					if (($field->t_multipleS != '1' && ($field->t_heightS == '' || $field->t_heightS <= 1)) && strpos($field->t_listHS,' [default]') == false) 
					{
						$html = $html.'<option value="-1">'.strpos($field->t_listHS,' [default]').'</option>'."\n";
					}  		
					
					$opt = explode("[-]", $field->t_listHS);
					$k=count($opt);
					for ($j=0;$j < $k; $j++)
					{	
						$checked = "";
						$val = explode("==", $opt[$j]);
						$key = explode("||", $val[1]);
						$ipos = strpos ($key[1],' [default]');
						
						if (empty($this->post) == false && isset($this->post[$field->name])
							 && in_array($key[0], $this->post[$field->name]) ) 
						{
							$checked = 'selected="selected"';
						} 
						else if ($ipos != false && (empty($this->post) == true || isset($this->post[$field->name]) == false)) 
						{
							$checked = 'selected="selected"';
							$key[1] = substr($key[1],0,$ipos);
						}		
						
						$html = $html.'<option value="'.$key[0].'" '.$checked.' >'.$key[1].'&nbsp;</option>'."\n";
					}
					$html = $html.'</select>'."\n";
				break;
			}	

			if ($field->mandatory == "1" || ($field->typefield == 'text' && ($field->t_texttype == 'email' || $field->t_texttype == 'number' || $field->t_minchar != ''))) 
			{
				$idError = $field->name;
				if ($field->typefield == 'textarea' && $field->t_HTMLEditor == 1 &&  $field->readonly != "1")
				{
					$idError = $field->name.'Cont';
				}
				
				if ($field->customerror != "") 
				{
					$html = $html.'<div class="error" id="error'.$idError.'">'."\n";
					$html = $html.$field->customerror.'</div>'."\n";
				}
			}
		
			$html = $html.'<p class="ckCSSclear" />'."\n";
		
		}  

		else if ($field->typefield == "fieldsep")
		{
			$html = $html.'<hr ';
			if ($field->t_noborderFS == "1") 
			{
				$html = $html.' class="ckNoBorder"';
			}
			$html = $html.' />'."\n";
		}
			
		if ($field->customtext != '')
		{
			$html = $html.'<div class="ckCustomText '.$field->customtextCSSclass.'">'.$field->customtext.'</div>'."\n";
		}	
			
	}

	if ($form->captcha == 1)
	{	
		$html = $html.'<div class="captchaCont">'."\n";
        $html = $html.'<img id="captchacode" class="captchacode" src="index.php?option=com_ckforms&task=captcha&sid=c4ce9d9bffcf8ba3357da92fd49c2457" align="absmiddle"> &nbsp;'."\n";
        $html = $html.'<img alt="'.JText::_( 'Refresh Captcha' ).'" class="captcharefresh" src="'.JURI::root(true).'/components/com_ckforms/'.'captcha/images/refresh.gif" align="absmiddle"> &nbsp;'."\n";
        $html = $html.'<input class="validate[\'required\']" type="text" id="ck_captcha_code" name="ck_captcha_code" />'."\n";
        	
        if ($form->captchacustominfo != "") 
        {
            $html = $html.'<img class="ckform_tooltip'.$form->id.' ckform_tooltipcss" src="'.JURI::root(true).'/components/com_ckforms/'.'img/info.png" title="'.$form->captchacustominfo.'" />'."\n";
        }
        $html = $html.'<div class="error" id="errorck_captcha_code">'."\n";
        if ($form->captchacustomerror != "") 
        {
            $html = $html.$form->captchacustomerror;
        }
        $html = $html.'</div>'."\n";
    	$html = $html.'</div>'."\n";
	} 
    
	$html = $html.'<div class="ckBtnCon">'."\n";

	for ($i=0;$i < $nbFields; $i++)
	{ 
		$field = $fields[$i];
		if ($field->typefield == "button")
		{
			$jsbutton = "";
			if ($field->t_typeBT == "submit") 
			{
    			$html = $html.'<input name="submit_bt" id="submit_bt" type="submit" value="'.$field->label.'" '.$jsbutton.' />'."\n";
   				$html = $html.'&nbsp;'."\n";
			} else if ($field->t_typeBT == "reset") 
			{
	    		$html = $html.'<input name="reset_bt" id="reset_bt" type="reset" value="'.$field->label.'" />&nbsp;'."\n";
 			}
		}
	}

	$html = $html.'</div>'."\n";
    
	$html = $html.'</form>'."\n";

	if ($form->poweredby == '1') 
	{
		$html = $html.'<div id="ckpoweredby"><a href="http://ckforms.cookex.eu" target="_blank">'.JText::_( 'Powered by CK Forms' ).'</a></div>'."\n";
	}

	$html = $html.'</td></tr>'."\n";
	$html = $html.'</table>'."\n";

	return $html;
	
	}

}
