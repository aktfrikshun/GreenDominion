<?PHP

/*****************************************************************
 *
 * @version		v1.0.0
 * @package 	Contact Us Pro by CSW
 * @copyright	Copyright (c) 2010 C.S. Wilson and Associates, Inc. All rights reserved.
 * @website		www.c-s-wilson.com
 * @license		GNU/GPL, see http://www.c-s-wilson.com/license-agreement.html
 *
 * This program is NOT free software; you can NOT redistribute 
 * it, but do have the right to modify it under the terms of 
 * the GNU General Public License v2 as published by the Free 
 * Software Foundation;
 *
 * This program is distributed in the hope that it will be useful, 
 * but WITHOUT ANY WARRANTY; without even the implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the 
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public 
 * License along with this program; if not, write to:
 *
 *   C.S. Wilson and Associates
 *   7303 E Hampshire Ln,
 *   Nampa, ID 83687
 *   sales@c-s-wilson.com
 *   http://www.c-s-wilson.com/license-agreement.html
 *
 *****************************************************************/

// no direct access
defined('_JEXEC') or die('Restricted access');
global $mainframe;
$page_title = $mainframe->getPageTitle();
$params = &JComponentHelper::getParams('com_contactuspro');

$filename = 'SpryValidationTextarea.js';
$path = 'components/com_contactuspro/js/';
JHTML::script($filename, $path);
$filename = 'SpryValidationTextField.js';
$path = 'components/com_contactuspro/js/';
JHTML::script($filename, $path);
$filename = 'SpryValidationConfirm.js';
$path = 'components/com_contactuspro/js/';
JHTML::script($filename, $path);
$filename = 'stylesheet.css';
$path = 'components/com_contactuspro/css/';
JHTML::stylesheet($filename, $path);

if($params->get('enableReCaptcha') == "1"):
  require_once('components/com_contactuspro/recaptchalib.php');
  $resp = recaptcha_check_answer ($params->get('reCaptchaPrivateKey'),$_SERVER["REMOTE_ADDR"],$_POST["recaptcha_challenge_field"],$_POST["recaptcha_response_field"]); 
endif;

if(isset($_POST['submit'])):
	
	// How did you hear about us
	switch ($_POST['hdyhau']){
		case "Other":
			$hdyhau = 'Other: '.$_POST['hdyhau_other'];
			break;
		default:
			$hdyhau = $_POST['hdyhau'];
	}
	
	$to = $params->get('email');
	$subject = JText::_('LABEL EMAIL SUBJECT') . ' ' . $_POST['subject'];
	$message  = '<html><head><title>' . $subject.$_POST['subject'] . '</title></head><body>';
	if($_POST['message'] != ""){$message .= '<p>' . $_POST['message'] . '</p>';}
	$message .= '<p>';
	if($_POST['name'] != ""){$message .= $_POST['name'] . '<br />';}
	if($_POST['company'] != ""){$message .= $_POST['company'] . '<br />';}
	if($_POST['home_phone'] != ""){$message .= $_POST['home_phone'] . '<br />';}
	if($_POST['mobile_phone'] != ""){$message .= $_POST['mobile_phone'] . '<br />';}
	if($_POST['email'] != ""){$message .= $_POST['email'] . '<br />';}
	if($_POST['address'] != ""){$message .= $_POST['address'] . '<br />';}
	if($_POST['city'] != ""){$message .= $_POST['city'] . ', ' . $_POST['state'] . ' ' . $_POST['zip'] . '<br />';}
	if($_POST['country'] != ""){$message .= $_POST['country'] . '<br />';}
	if($hdyhau != ""){$message .= '<br />' . JText::_('LABEL HDYHAU') . ' ' . $hdyhau . '<br />';}
	$message .= '</p>';
	$message .= '<p>' . JText::_('LABEL USERS IP ADDRESS') . ' ' . $_SERVER['REMOTE_ADDR'] . '</p>';
	$message .= '</body></html>';
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: ' . $_POST['email'] . "\r\n";
	
	if ($params->get('enableReCaptcha') == "1"):
		if ($resp->is_valid):
			mail($to, $subject, $message, $headers);
			JFactory::getApplication()->enqueueMessage( JText::_('MESSAGE THANK YOU'), 'message' );
			unset($_POST);
		else:
			JFactory::getApplication()->enqueueMessage( JText::_('MESSAGE INCORRECT VALIDATION CODE'), 'error' );
  		endif;
	else:
		mail($to, $subject, $message, $headers);
		JFactory::getApplication()->enqueueMessage( JText::_('MESSAGE THANK YOU'), 'message' );
		unset($_POST);
	endif;
endif;

if($params->get('enableReCaptcha') == "1"):
	echo '<script type="text/javascript">var RecaptchaOptions = {theme : \'' . $params->get('reCaptchaTheme','red') . '\'};</script>' . "\n";
endif;

$legend0				= JText::_('LEGEND STEP0');
$legend1				= JText::_('LEGEND STEP1');
$legend2				= JText::_('LEGEND STEP2');
$legend3				= JText::_('LEGEND STEP3');
$legend1NUM				= JText::_('LEGEND STEP1 NUM');
$legend2NUM				= JText::_('LEGEND STEP2 NUM');
$legend3NUM				= JText::_('LEGEND STEP3 NUM');

$your_company_name 		= $params->get('title');
$your_phone 			= '&nbsp;' . $params->get('phone1');
$your_toll_free_phone 	= '&nbsp;' . $params->get('phone2');
$your_fax 				= '&nbsp;' . $params->get('fax');
$your_address 			= $params->get('address');

$button_submit			= '<input type="submit" name="submit" value="' . JText::_('BUTTON SEND') . '" class="button" />';
$button_reset			= '<input type="reset" name="reset" value="' . JText::_('BUTTON RESET') . '" class="button" />';
$button_cancel			= '<input type="button" value="' . JText::_('BUTTON CANCEL') . '" onclick="history.go(-1)" class="button" />';

$customHTML				= $params->get('HTML');

// SETUP USERS NAME FIELD
if($params->get('disName')=="1"):
	if($params->get('disNameRequired')=="1"):
		$required = 'textfieldRequired';
	else:
		$required = '';
	endif;
	$users_name = '<input type="text" name="name" class="inputbox ' . $required . '" id="csw_name" value="' . $_POST['name'] . '" />' . "\n";
endif;

// SETUP USERS COMPANY FIELD
if($params->get('disCompany')=="1"):
	if($params->get('disCompanyRequired')=="1"):
		$required = 'textfieldRequired';
	else:
		$required = '';
	endif;
	$users_company = '<input type="text" name="company" class="inputbox ' . $required . '" id="csw_company" value="' . $_POST['company'] . '" />' . "\n";
endif;

// SETUP USERS HOME PHONE FIELD
if($params->get('disHomePhone')=="1"):
	if($params->get('disHomePhoneRequired')=="1"):
		$required = 'textfieldRequired';
	else:
		$required = '';
	endif;
	$users_home_phone = '<input type="text" name="home_phone" class="inputbox ' . $required . '" id="csw_home_phone" value="' . $_POST['phone'] . '" />' . "\n";
endif;

// SETUP USERS MOBILE FIELD
if($params->get('disMobilePhone')=="1"):
	if($params->get('disMobilePhoneRequired')=="1"):
		$required = 'textfieldRequired';
	else:
		$required = '';
	endif;
	$users_mobile_phone = '<input type="text" name="mobile_phone" class="inputbox ' . $required . '" id="csw_mobile_phone" value="' . $_POST['phone'] . '" />' . "\n";
endif;

// SETUP USERS EMAIL FIELD
if($params->get('disEmail')=="1"):
	if($params->get('disEmailRequired')=="1"):
		$required = 'textfieldRequired';
	else:
		$required = '';
	endif;
	$users_email = '<input type="text" name="email" class="inputbox ' . $required . '" id="csw_email" value="' . $_POST['email'] . '" />' . "\n";
endif;

// SETUP USERS EMAIL CONFIRM FIELD
if($params->get('disEmailConfirmation')=="1"):
	if($params->get('disEmailRequired')=="1"):
		$required = 'textfieldRequired';
	else:
		$required = '';
	endif;
	if($_POST['confirm_email'] != ""):
		$email_post = $_POST['confirm_email'];
	else:
		$email_post = JText::_('LABEL EMAIL CONFIRM');
	endif;
	$users_confirm_email = '<input type="text" name="confirm_email" class="inputbox ' . $required . '" id="csw_confirm_email" value="' . $email_post . '" onfocus="if(this.value==\'' . $email_post . '\'){this.value=\'\'};" onblur="if(this.value==\'\'){this.value=\'' . $email_post . '\'};" />' . "\n";
endif;

// SETUP USERS ADDRESS FIELD
if($params->get('disAddress')=="1"):
	if($params->get('disAddressRequired')=="1"):
		$required = 'textfieldRequired';
	else:
		$required = '';
	endif;
	$users_address = '<input type="text" name="address" class="inputbox ' . $required . '" id="csw_address" value="' . $_POST['address'] . '" />' . "\n";
endif;

// SETUP USERS CITY FIELD
if($params->get('disCity')=="1"):
	if($params->get('disCityRequired')=="1"):
		$required = 'textfieldRequired';
	else:
		$required = '';
	endif;
	$users_city = '<input type="text" name="city" class="inputbox ' . $required . '" id="csw_city" value="' . $_POST['city'] . '" />' . "\n";
endif;

// SETUP USERS STATE FIELD
if($params->get('disState')=="1"):
	if($params->get('disStateRequired')=="1"):
		$required = 'textfieldRequired';
	else:
		$required = '';
	endif;
	$users_state = '<input type="text" name="state" class="inputbox ' . $required . '" id="csw_state" value="' . $_POST['state'] . '" />' . "\n";
endif;

// SETUP USERS ZIP FIELD
if($params->get('disZip')=="1"):
	if($params->get('disZipRequired')=="1"):
		$required = 'textfieldRequired';
	else:
		$required = '';
	endif;
	$users_zip = '<input type="text" name="zip" class="inputbox ' . $required . '" id="csw_zip" value="' . $_POST['zip'] . '" />' . "\n";
endif;

// SETUP USERS COUNTRY FIELD
if($params->get('disCountry')=="1"):
	if($params->get('disCountryRequired')=="1"):
		$required = 'textfieldRequired';
	else:
		$required = '';
	endif;
	$users_country = '<input type="text" name="country" class="inputbox ' . $required . '" id="csw_country" value="' . $_POST['country'] . '" />' . "\n";
endif;

// HOW DID YOU HEAR ABOUT US
if($params->get('disHdyhau')=="1"):
	$file = file("language/en-GB/en-GB.com_contactuspro.ini");
	$options  = '';
	$option_array = array();
	foreach($file as $line):
		$line_is = substr($line, 0, 13);
		if($line_is == "OPTION HDYHAU"):
			$line = explode('=',$line);
			$option_array[] = $line;
			if($_POST['hdyhau'] == $line[1]){$selected = ' style="display:block;"';}
			$line = str_replace("\r\n","",$line[1]);
			$options .= '<option value="' . $line . '"' . $selected . '>' . $line . '</option>' . "\n";
		endif;
	endforeach;
	if($_POST['hdyhau'] == ""){$selected = '  selected="selected"';}
	$users_hdyhau  = '<div class="hdyhau_label">' . JText::_( 'LABEL HDYHAU' ) . '</div>' . "\n";
	$users_hdyhau .= '<select name="hdyhau" id="hdyhau" class="inputbox" onchange="swap();">' . "\n";
	$users_hdyhau .= '<option' . $selected . ' disabled="disabled" value="">Select One</option>' . "\n";
	$users_hdyhau .= $options;
	$users_hdyhau .= '</select>' . "\n";
	foreach($option_array as $items):
		foreach($items as $item):
			if(trim(str_replace("OPTION HDYHAU","",$item)) == JText::_('HDYHAU OTHER')){
				if($_POST['hdyhau_other'] != ""){$value = $_POST['hdyhau_other'];}else{$value = JText::_('LABEL HDYHAU OTHER');}
				$users_hdyhau .= '<div id="hdyhauOtherWrapper"><input type="text" name="hdyhau_other" id="hdyhau_other" class="inputbox" value="' . $value . '" onfocus="if(this.value==\'' . $value . '\'){this.value=\'\'};" onblur="if(this.value==\'\'){this.value=\'' . $value . '\'};" /></div>' . "\n";
			}
		endforeach;
	endforeach;
endif;


// SETUP MESSAGE SUBJECT FIELD
if($params->get('disSubject')=="1"):
	if($params->get('disSubjectRequired')=="1"):
		$required = 'textfieldRequired';
	else:
		$required = '';
	endif;
	$message_subject = '<input type="text" name="subject" class="inputbox ' . $required . '" id="csw_subject" value="' . $_POST['subject'] . '" />' . "\n";
endif;

// SETUP MESSAGE FIELD
if($params->get('disMessage')=="1"):
	if($params->get('disMessageRequired')=="1"):
		$required = 'textareaRequired';
	else:
		$required = '';
	endif;
	$message_message = '<textarea name="message" id="csw_message" class="textarea ' . $required . '">' . $_POST['message'] . '</textarea>' . "\n";
endif;

// SETUP RECAPTCHA FIELD
if($params->get('enableReCaptcha') == "1"):
	if ($params->get('reCaptchaPublicKey') == ''):
		JFactory::getApplication()->enqueueMessage( JText::_( 'MESSAGE RECAPTCHA WARNING' ), 'error' );
	else:
		$recaptcha_field = recaptcha_get_html($params->get('reCaptchaPublicKey'));
	endif;
else:
	$recaptcha_field = JText::_( 'MESSAGE RECAPTCHA NOTICE' );
endif;


echo '<div class="contactusWrapper">' . "\n";

if($params->get('displaytitle') == "1"):
	echo '<div class="componentheading">' . $page_title . '</div>' . "\n";
endif;

echo '<form action="" method="post" name="form1">' . "\n";

// Replace Custom HTML Tokens
$tokens = array("{LEGEND_STEP0}","{LEGEND_STEP1}","{LEGEND_STEP2}","{LEGEND_STEP3}","{LEGEND_STEP1_NUM}","{LEGEND_STEP2_NUM}","{LEGEND_STEP3_NUM}","{YOUR_COMPANY_NAME}","{YOUR_PHONE}","{YOUR_TOLL_FREE_PHONE}","{YOUR_FAX}","{YOUR_ADDRESS}","{USERS_NAME_FIELD}","{USERS_COMPANY_FIELD}","{USERS_EMAIL_FIELD}","{USERS_EMAIL_CONFIRM_FIELD}","{USERS_ADDRESS_FIELD}","{USERS_CITY_FIELD}","{USERS_STATE_FIELD}","{USERS_ZIP_FIELD}","{USERS_COUNTRY_FIELD}","{USERS_HDYHAU_SELECT}","{USERS_HOME_PHONE_FIELD}","{USERS_MOBILE_PHONE_FIELD}","{MESSAGE_SUBJECT_FIELD}","{MESSAGE_MESSAGE_FIELD}","{RECAPTCHA_FIELD}","{BUTTON_SUBMIT}","{BUTTON_RESET}","{BUTTON_CANCEL}");
$fields = array($legend0,$legend1,$legend2,$legend3,$legend1NUM,$legend2NUM,$legend3NUM,$your_company_name,$your_phone,$your_toll_free_phone,$your_fax,$your_address,$users_name,$users_company,$users_email,$users_confirm_email,$users_address,$users_city,$users_state,$users_zip,$users_country,$users_hdyhau,$users_home_phone,$users_mobile_phone,$message_subject,$message_message,$recaptcha_field,$button_submit,$button_reset,$button_cancel);
echo str_replace($tokens, $fields, $customHTML);

echo '</form></div>' . "\n";

echo '
<script type="text/javascript">
<!--' . "\n";

// NAME VALIDATION
if($params->get('disName')=="1"):
	$disName  = 'var csw_name = new Spry.Widget.ValidationTextField("csw_name", "none", {';
	if($params->get('disNameRequired')=="1"){$disName .= 'isRequired:true';}else{$disName .= 'isRequired:false';}
	$disName .= ', hint:"' . JText::_('LABEL NAME') . '"});';
	echo $disName . "\n";
endif;

// COMPANY VALIDATION
if($params->get('disCompany')=="1"):
	$disCompany  = 'var csw_company = new Spry.Widget.ValidationTextField("csw_company", "none", {';
	if($params->get('disCompanyRequired')=="1"){$disCompany .= 'isRequired:true';}else{$disCompany .= 'isRequired:false';}
	$disCompany .= ', hint:"' . JText::_('LABEL COMPANY') . '"});';
	echo $disCompany . "\n";
endif;

// EMAIL VALIDATION
if($params->get('disEmail')=="1"):
	$disEmail  = 'var csw_email = new Spry.Widget.ValidationTextField("csw_email", "'.$params->get('disEmailEnforcePattern') . '", {';
	if($params->get('disEmailRequired')=="1"){$disEmail .= 'isRequired:true';}else{$disEmail .= 'isRequired:false';}
	$disEmail .= ', hint:"' . JText::_('LABEL EMAIL') . '"});';
	echo $disEmail . "\n";
endif;

// HOME PHONE VALIDATION
if($params->get('disHomePhone')=="1"):
	$disHomePhone  = 'var csw_home_phone = new Spry.Widget.ValidationTextField("csw_home_phone", "'.$params->get('disHomePhoneEnforcePattern') . '", {';
	if($params->get('disHomePhoneRequired')=="1"){$disHomePhone .= 'isRequired:true, ';}else{$disHomePhone .= 'isRequired:false, ';}
	if($params->get('disHomePhoneEnforcePattern')=="phone_number"){$disHomePhone .= 'useCharacterMasking:true, format:"phone_custom", pattern:"000-000-0000", ';}
	$disHomePhone .= 'hint:"' . JText::_('LABEL HOME PHONE') . '"});';
	echo $disHomePhone . "\n";
endif;

// MOBILE PHONE VALIDATION
if($params->get('disMobilePhone')=="1"):
	$disMobilePhone  = 'var csw_mobile_phone = new Spry.Widget.ValidationTextField("csw_mobile_phone", "'.$params->get('disMobilePhoneEnforcePattern') . '", {';
	if($params->get('disMobilePhoneRequired')=="1"){$disMobilePhone .= 'isRequired:true, ';}else{$disMobilePhone .= 'isRequired:false, ';}
	if($params->get('disHomePhoneEnforcePattern')=="phone_number"){$disMobilePhone .= 'useCharacterMasking:true, format:"phone_custom", pattern:"000-000-0000", ';}
	$disMobilePhone .= 'hint:"' . JText::_('LABEL MOBILE PHONE') . '"});';
	echo $disMobilePhone . "\n";
endif;

// ADDRESS VALIDATION
if($params->get('disAddress')=="1"):
	$disAddress  = 'var csw_address = new Spry.Widget.ValidationTextField("csw_address", "none", {';
	if($params->get('disAddressRequired')=="1"){$disAddress .= 'isRequired:true';}else{$disAddress .= 'isRequired:false';}
	$disAddress .= ', hint:"' . JText::_('LABEL ADDRESS') . '"});';
	echo $disAddress . "\n";
endif;

// CITY VALIDATION
if($params->get('disCity')=="1"):
	$disCity  = 'var csw_city = new Spry.Widget.ValidationTextField("csw_city", "none", {';
	if($params->get('disCityRequired')=="1"){$disCity .= 'isRequired:true';}else{$disCity .= 'isRequired:false';}
	$disCity .= ', hint:"' . JText::_('LABEL CITY') . '"});';
	echo $disCity . "\n";
endif;

// STATE VALIDATION
if($params->get('disState')=="1"):
	$disState  = 'var csw_state = new Spry.Widget.ValidationTextField("csw_state", "none", {';
	if($params->get('disStateRequired')=="1"){$disState .= 'isRequired:true';}else{$disState .= 'isRequired:false';}
	$disState .= ', hint:"' . JText::_('LABEL STATE') . '"});';
	echo $disState . "\n";
endif;

// ZIP CODE VALIDATION
if($params->get('disZip')=="1"):
	$disZip  = 'var csw_zip = new Spry.Widget.ValidationTextField("csw_zip", "' . $params->get('disZipEnforcePattern') . '", {';
	if($params->get('disZipRequired')=="1"){$disZip .= 'isRequired:true, ';}else{$disZip .= 'isRequired:false, ';}
	if($params->get('disZipValidationFormat')=="zip_us5"){$disZip .= 'useCharacterMasking:true, format:"zip_us5", ';}
	if($params->get('disZipValidationFormat')=="zip_us9"){$disZip .= 'useCharacterMasking:true, format:"zip_us9", ';}
	if($params->get('disZipValidationFormat')=="zip_uk"){$disZip .= 'useCharacterMasking:true, format:"zip_uk", ';}
	if($params->get('disZipValidationFormat')=="zip_canada"){$disZip .= 'useCharacterMasking:true, format:"zip_canada", ';}
	$disZip .= 'hint:"' . JText::_('LABEL ZIP') . '"});';
	echo $disZip . "\n";
endif;

// COUNTRY VALIDATION
if($params->get('disCountry')=="1"):
	$disCountry  = 'var csw_country = new Spry.Widget.ValidationTextField("csw_country", "none", {';
	if($params->get('disCountryRequired')=="1"){$disCountry .= 'isRequired:true';}else{$disCountry .= 'isRequired:false';}
	$disCountry .= ', hint:"' . JText::_('LABEL COUNTRY') . '"});';
	echo $disCountry . "\n";
endif;

// HOW DID YOU HEAR ABOUT US VALIDATION
if($params->get('disHdyhau')=="1"):
	$disHdyhau  = 'var csw_hdyhau = new Spry.Widget.ValidationTextField("csw_hdyhau", "none", {';
	if($params->get('disHdyhauRequired')=="1"){$disHdyhau .= 'isRequired:true';}else{$disHdyhau .= 'isRequired:false';}
	$disHdyhau .= ', hint:"' . JText::_('LABEL HDYHAU') . '"});';
	echo $disHdyhau . "\n";
endif;

// SUBJECT VALIDATION
if($params->get('disSubject')=="1"):
	$disSubject  = 'var csw_subject = new Spry.Widget.ValidationTextField("csw_subject", "none", {';
	if($params->get('disSubjectRequired')=="1"){$disSubject .= 'isRequired:true';}else{$disSubject .= 'isRequired:false';}
	$disSubject .= ', hint:"' . JText::_('LABEL SUBJECT') . '"});';
	echo $disSubject . "\n";
endif;

// MESSAGE VALIDATION
if($params->get('disMessage')=="1"):
	$disMessage  = 'var csw_message = new Spry.Widget.ValidationTextarea("csw_message", {';
	if($params->get('disMessageRequired')=="1"){$disMessage .= 'isRequired:true';}else{$disMessage .= 'isRequired:false';}
	$disMessage .= ', hint:"' . JText::_('LABEL MESSAGE') . '"});';
	echo $disMessage . "\n";
endif;

// CONFIRM EMAIL VALIDATION
if($params->get('disEmailConfirmation')=="1"){echo 'var csw_confirm_email = new Spry.Widget.ValidationConfirm("csw_confirm_email", "csw_email");';}

if($params->get('disHdyhau')=="1"):
echo "
function swap() {
	if(document.getElementById('hdyhau').value == 'Other'){
		document.getElementById('hdyhauOtherWrapper').style.display = 'block';
	} else {
		document.getElementById('hdyhauOtherWrapper').style.display = 'none';
	}
	/*if(document.getElementById('hdyhau').value == 'Other'){
		document.getElementById('hdyhau_icon').style.display = 'block';
	} else {
		document.getElementById('hdyhau_icon').style.display = 'none';
	}*/
}";
endif;

echo "
//-->
</script>" . "\n";

?>
