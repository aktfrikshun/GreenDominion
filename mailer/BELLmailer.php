<?php
/* 
	BELLonline PHP MAILER SCRIPT v1.5
	Copyright 2006 Gavin Bell 
	http://www.bellonline.co.uk 
	gavin@bellonline.co.uk

	Set up an email form on your website within minutes - see readme.txt for installation.
*/

extract($_POST);

if (!file_exists("config.php")) 
	{
$host = $_SERVER[HTTP_HOST ];
$path = pathinfo($_SERVER['PHP_SELF']);
$file_path = $path['dirname'];
print "<h1>BELLonline PHP mailer script</h1>
	<h2>There is a problem with your PHP mailer script installation</h2>
	<p>The config.php file seems to be missing!</p>
	<p>For this script to work, you need to upload the config.php file that came with the download of the BELLonline <a href=\"http://bellonline.co.uk/downloads/php-mailer-script/\">PHP mailer script</a>.</p>
	<p>The file must be in the following directory of your website:</p>
	<p>$host<span style=\"font-weight: bold; font-size: 150%;\">$file_path/</span></p>
	<p>If you need help installing the script, then feel free to email me at <a href=\"&#x6d;&#x61;&#105;&#108;&#116;&#x6f;&#58;&#x67;&#x61;&#118;&#x69;&#110;&#64;&#98;&#x65;&#x6c;&#x6c;&#x6f;&#110;&#x6c;&#105;&#110;&#101;&#46;&#x63;&#111;&#46;&#x75;&#x6b;\">&#x67;&#x61;&#118;&#x69;&#110;&#64;&#98;&#x65;&#x6c;&#x6c;&#x6f;&#110;&#x6c;&#105;&#110;&#101;&#46;&#x63;&#111;&#46;&#x75;&#x6b;</a></p>";
exit;
	}
include "config.php";
$error = 0;

if ($sendto_email == "changeme@example.com")
	{
print "<h1>BELLonline PHP mailer script</h1>
	<h2>Installation nearly complete!</h2>
	<p>Thank you for downloading the <a href=\"http://bellonline.co.uk/downloads/php-mailer-script/\" title=\"free PHP mailer script\">free PHP mailer script</a> from <a href=\"http://www.bellonline.co.uk\">BELLonline web services</a>. </p>
	<p>To start using the script, open config.php in a text editor and change the <b>&#36;sendto_email</b> variable to your email address.</p>
	<p>If you did not get a config.php file with this script, then go to the <a href=\"http://bellonline.co.uk/downloads/php-mailer-script/\">PHP mailer script page</a> and download the full script.</p>
	<p>If you need help installing the script, then feel free to email me at <a href=\"&#x6d;&#x61;&#105;&#108;&#116;&#x6f;&#58;&#x67;&#x61;&#118;&#x69;&#110;&#64;&#98;&#x65;&#x6c;&#x6c;&#x6f;&#110;&#x6c;&#105;&#110;&#101;&#46;&#x63;&#111;&#46;&#x75;&#x6b;\">&#x67;&#x61;&#118;&#x69;&#110;&#64;&#98;&#x65;&#x6c;&#x6c;&#x6f;&#110;&#x6c;&#105;&#110;&#101;&#46;&#x63;&#111;&#46;&#x75;&#x6b;</a></p>";
exit;
	}
	/*
if (empty ($company_name)) 
	{
	$error = "1";
	$info_error .= $lang_noname . "<br>"; 
	}
	*/
if (empty ($youremail)) 
	{
	$error = "1";
	$info_error .= $lang_noemail . "<br>";  
	}
if (empty ($mail_subject)) 
	{
	$error = "1";
	$info_error .= $lang_nosubject . "<br>";  
	}
if (empty ($mail_message))  
	{
	$error = "1";
	$info_error .= $lang_nomessage . "<br>";  
	}
if (!eregi("^[A-Z0-9._%-]+@[A-Z0-9._%-]+\.[A-Z]{2,6}$", $youremail))
	{
	$error = "1";
	$info_error .= $lang_invalidemail . "<br>"; 
	}
if (empty ($security_code))  
	{
	$error = "1";
	$info_error .= $lang_nocode . "<br>";  
	}
elseif (strtoupper($security_code) != strtoupper($randomness))  
	{
	$error = "1";
	$info_error .= $lang_wrongcode . "<br>";  
	}
if ($showlink != "no")
	{
	$link = "<br><span style=\"font-size: 10px;\">Powered by <a href=\"http://bellonline.co.uk/downloads/php-mailer-script/\" title=\"free PHP mailer script\">BELLonline PHP mailer script</a></span>";
	}
if ($error == "1") 
	{
	$info_notice = "<span style=\"color: " . $error_colour . "; font-weight: bold;\">" . $lang_error . "</span><br>"; 
	
	if (empty ($submit)) 
		{
		$info_error = "";
		$info_notice = $lang_notice;
		}	

	function Random() 
		{
		$chars = "ABCDEFGHJKLMNPQRSTUVWZYZ23456789";
		srand((double)microtime()*1000000);
		$i = 0;
		$pass = '' ;
		while ($i <= 4) 
			{
			$num = rand() % 32;
			$tmp = substr($chars, $num, 1);
			$pass = $pass . $tmp;
			$i++; 
			} 
		return $pass; 
		}
	$random_code = Random();
	$mail_message = stripslashes($mail_message);

	print "<form name=\"BELLonline_email\" method=\"post\" style=\"margin: 0;\" action=\"\">
  <table  border=\"0\" cellspacing=\"2\" cellpadding=\"2\">
    <tr align=\"$title_align\" valign=\"top\">
      <td colspan=\"2\"><span style=\"$title_css\">$lang_title</span></td>
    </tr>
    <tr align=\"left\" valign=\"top\">
      <td colspan=\"2\">$info_notice$info_error</td>
    </tr>
    <tr valign=\"top\">
      <td align=\"right\">$lang_company_name</td>
      <td align=\"left\"><input name=\"company_name\" type=\"text\" class=\"mailform_input\" id=\"company_name\" style=\"width: $input_width;\" value=\"$company_name\" maxlength=\"32\"></td>
    </tr>
    <tr valign=\"top\">
      <td align=\"right\">$lang_addr_1</td>
      <td align=\"left\"><input name=\"address_1\" type=\"text\" class=\"mailform_input\" id=\"address_1\" style=\"width: $input_width;\" value=\"$address_1\" maxlength=\"32\"></td>
    </tr>
    <tr valign=\"top\">
      <td align=\"right\">$lang_addr_2</td>
      <td align=\"left\"><input name=\"address_2\" type=\"text\" class=\"mailform_input\" id=\"address_2\" style=\"width: $input_width;\" value=\"$address_2\" maxlength=\"32\"></td>
    </tr>
    <tr valign=\"top\">
      <td align=\"right\">$lang_city</td>
      <td align=\"left\"><input name=\"city\" type=\"text\" class=\"mailform_input\" id=\"city\" style=\"width: $input_width;\" value=\"$city\" maxlength=\"32\"></td>
    </tr>
     <tr valign=\"top\">
      <td align=\"right\">$lang_state</td>
      <td align=\"left\"><input name=\"state\" type=\"text\" class=\"mailform_input\" id=\"state\" style=\"width: $input_width;\" value=\"$state\" maxlength=\"3\"></td>
    </tr>
     <tr valign=\"top\">
      <td align=\"right\">$lang_zip</td>
      <td align=\"left\"><input name=\"zip\" type=\"text\" class=\"mailform_input\" id=\"zip\" style=\"width: $input_width;\" value=\"$zip\" maxlength=\"15\"></td>
    </tr>
    <tr valign=\"top\">
      <td align=\"right\">$lang_contact</td>
      <td align=\"left\"><input name=\"contact\" type=\"text\" class=\"mailform_input\" id=\"contact\" style=\"width: $input_width;\" value=\"$contact\" maxlength=\"32\"></td>
    </tr>
    <tr valign=\"top\">
      <td align=\"right\">$lang_phone</td>
      <td align=\"left\"><input name=\"phone\" type=\"text\" class=\"mailform_input\" id=\"phone\" style=\"width: $input_width;\" value=\"$phone\" maxlength=\"20\"></td>
      <td width=\"10\" align=\"right\">$lang_ext</td>
      <td align=\"left\"><input name=\"ext\" type=\"text\" class=\"mailform_input\" id=\"ext\" size=\"5\" value=\"$ext\" maxlength=\"10\"></td>
    </tr>
    <tr valign=\"top\">
      <td align=\"right\">$lang_fax</td>
      <td align=\"left\"><input name=\"fax\" type=\"text\" class=\"mailform_input\" id=\"fax\" style=\"width: $input_width;\" value=\"$fax\" maxlength=\"20\"></td>
    </tr>
    <tr valign=\"top\">
      <td align=\"right\">$lang_youremail</td>
      <td align=\"left\"><input name=\"youremail\" type=\"text\" class=\"mailform_input\" id=\"youremail\" style=\"width: $input_width;\" value=\"$youremail\" maxlength=\"32\"></td>
    </tr>
    <tr valign=\"top\">
      <td align=\"left\">$lang_website</td>
      <td align=\"left\"><input name=\"website\" type=\"text\" class=\"mailform_input\" id=\"website\" style=\"width: $input_width;\" value=\"$website\" maxlength=\"32\"></td>
    </tr>
<tr><td>&nbsp</td></tr>
    <tr valign=\"top\">
      <td align=\"left\" colspan=\"2\">$lang_pref</td>
    </tr>
    <tr >
    <td align=\"left\" colspan=\"2\">
    <input type=\"radio\" name=\"group1\" value=\"phone\"> Phone
    <input type=\"radio\" name=\"group1\" value=\"email\"> Email
    </td> 
    </tr>
<tr><td>&nbsp</td></tr>
    <tr valign=\"top\">
      <td width=\"100\" align=\"right\">$lang_subject</td>     
      <td align=\"left\"><input name=\"mail_subject\" type=\"text\" class=\"mailform_input\" id=\"mail_subject\" style=\"width: $input_width;\" value=\"$mail_subject\" maxlength=\"64\"></td>
    </tr>
    <tr valign=\"top\">
      <td width=\"100\" align=\"right\">$lang_message</td>
      <td align=\"left\"><textarea name=\"mail_message\" cols=\"36\" rows=\"5\" style=\"width: $input_width;\" class=\"mailform_input\">$mail_message</textarea></td>
    </tr>
    <tr align=\"left\" valign=\"top\">
      <td colspan=\"1\" >$lang_confirmation</td>
      <td><input name=\"security_code\" type=\"text\" class=\"mailform_input\" id=\"security_code\" size=\"5\"> 
        &nbsp;&nbsp;&nbsp;&nbsp;<b>$random_code</b></td>
    </tr>
    <tr valign=\"top\">
      <td colspan=\"2\" align=\"left\"><input name=\"Back\" type=\"button\" id=\"Back\" value=\"Back\" onClick=\"window.location='http://greendominion.com'\">
      <td colspan=\"2\" align=\"right\"><input name=\"randomness\" type=\"hidden\" id=\"randomness\" value=\"$random_code\">
      <input name=\"submit\" type=\"submit\" id=\"submit\" value=\"$lang_submit\"></td>
    </tr>
  </table>
</form>";
	}
else
	{
	
	
	
	if ($checkdomain == "yes") 
		{
		$sender_domain = substr($senders_email, (strpos($senders_email, '@')) +1);
		$recipient_domain = substr($sendto_email, (strpos($sendto_email, '@')) +1);
		if ($sender_domain == $recipient_domain)
			{
			print "Sorry, you cannot send messages from this domain ($sender_domain)";
			exit;
			}		
		}
		
		
	$info_notice = $lang_sent;
	$mail_message = stripslashes($mail_message);
	$youremail = preg_replace("/[^a-zA-Z0-9s.@-_]/", "-", $youremail);
	$contact = preg_replace("/[^a-zA-Z0-9s]/", " ", $contact);
	$headers = "From: $contact <$youremail> \r\n";
	$headers .= "X-Mailer: BELLonline.co.uk PHP mailer \r\n";
	$headers .= "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";


$email_status = 'unchecked';
$phone_status = 'unchecked';

if (isset($_POST['group1'])) {

$selected_radio = $_POST['group1'];


if ($selected_radio == 'email') {
$email_status = 'checked';
	}
else if ($selected_radio == 'phone') {
$phone_status = 'checked';
	}
}

$entire_email = 
"<html>
<head>
<style type=\"text/css\">
html {height:100%;}
body {height:100%; margin:0; padding:0;}
p,a,h1 {color: green;
	font-family: \"Apple Chancery\";
	font-style: oblique;
	font-variant: normal;
	font-weight: bold;
	font-size: x-large;
	line-height: 100%;
	word-spacing: normal;
	letter-spacing: normal;
	text-decoration: none;
	text-transform: none;
	text-align: left;
	text-indent: 0ex;}
#bg {position:fixed; top:0; left:0; width:100%; height:100%;}
#content {position:absolute; z-index:1; width:800px; top:50px; left:50px;}
#mail {position:absolute; z-index:1; top:500px; left:800px;}
</style>
<!--[if gt IE 5]>
<style type=\"text/css\">
html {overflow-y:hidden;}
body {overflow-y:auto;}
p,a,h1 {color: green;
	font-family: \"Apple Chancery\";
	font-style: oblique;
	font-variant: normal;
	font-weight: normal;
	font-size: x-large;
	line-height: 100%;
	word-spacing: normal;
	letter-spacing: normal;
	text-decoration: none;
	text-transform: none;
	text-align: left;
	text-indent: 0ex;}
#bg {position:absolute; z-index:-1;}
#content {position:absolute; top:50px; left:50px;}
#mail {position:absolute; left:800px; top:400px;}
</style>
<![endif]-->
</head>
<body>
<form name=\"BELLonline_email\" method=\"post\" style=\"margin: 0;\" action=\"\">
  <table  border=\"0\" cellspacing=\"2\" cellpadding=\"2\">
    <tr align=\"$title_align\" valign=\"top\">
      <td colspan=\"2\"><span style=\"$title_css\">$lang_title</span></td>
    </tr>
    <tr align=\"left\" valign=\"top\">
      <td colspan=\"2\">$info_notice$info_error</td>
    </tr>
    <tr valign=\"top\">
      <td align=\"right\">$lang_company_name</td>
      <td align=\"left\"><input name=\"company_name\" type=\"text\" class=\"mailform_input\" id=\"company_name\" style=\"width: $input_width;\" value=\"$company_name\" maxlength=\"32\"  readonly=\"readonly\"></td>
    </tr>
    <tr valign=\"top\">
      <td align=\"right\">$lang_addr_1</td>
      <td align=\"left\"><input name=\"address_1\" type=\"text\" class=\"mailform_input\" id=\"address_1\" style=\"width: $input_width;\" value=\"$address_1\" maxlength=\"32\" readonly=\"readonly\"></td>
    </tr>
    <tr valign=\"top\">
      <td align=\"right\">$lang_addr_2</td>
      <td align=\"left\"><input name=\"address_2\" type=\"text\" class=\"mailform_input\" id=\"address_2\" style=\"width: $input_width;\" value=\"$address_2\" maxlength=\"32\" readonly=\"readonly\"></td>
    </tr>
    <tr valign=\"top\">
      <td align=\"right\">$lang_city</td>
      <td align=\"left\"><input name=\"city\" type=\"text\" class=\"mailform_input\" id=\"city\" style=\"width: $input_width;\" value=\"$city\" maxlength=\"32\" readonly=\"readonly\"></td>
    </tr>
     <tr valign=\"top\">
      <td align=\"right\">$lang_state</td>
      <td align=\"left\"><input name=\"state\" type=\"text\" class=\"mailform_input\" id=\"state\" style=\"width: $input_width;\" value=\"$state\" maxlength=\"3\" readonly=\"readonly\"></td>
    </tr>
     <tr valign=\"top\">
      <td align=\"right\">$lang_zip</td>
      <td align=\"left\"><input name=\"zip\" type=\"text\" class=\"mailform_input\" id=\"zip\" style=\"width: $input_width;\" value=\"$zip\" maxlength=\"15\" readonly=\"readonly\"></td>
    </tr>
    <tr valign=\"top\">
      <td align=\"right\">$lang_contact</td>
      <td align=\"left\"><input name=\"contact\" type=\"text\" class=\"mailform_input\" id=\"contact\" style=\"width: $input_width;\" value=\"$contact\" maxlength=\"32\" readonly=\"readonly\"></td>
    </tr>
    <tr valign=\"top\">
      <td align=\"right\">$lang_phone</td>
      <td align=\"left\"><input name=\"phone\" type=\"text\" class=\"mailform_input\" id=\"phone\" style=\"width: $input_width;\" value=\"$phone\" maxlength=\"20\" readonly=\"readonly\"></td>
      <td width=\"10\" align=\"right\">$lang_ext</td>
      <td align=\"left\"><input name=\"ext\" type=\"text\" class=\"mailform_input\" id=\"ext\" size=\"5\" value=\"$ext\" maxlength=\"10\" readonly=\"readonly\"></td>
    </tr>
    <tr valign=\"top\">
      <td align=\"right\">$lang_fax</td>
      <td align=\"left\"><input name=\"fax\" type=\"text\" class=\"mailform_input\" id=\"fax\" style=\"width: $input_width;\" value=\"$fax\" maxlength=\"20\" readonly=\"readonly\"></td>
    </tr>
    <tr valign=\"top\">
      <td align=\"right\">$lang_youremail</td>
      <td align=\"left\"><input name=\"youremail\" type=\"text\" class=\"mailform_input\" id=\"youremail\" style=\"width: $input_width;\" value=\"$youremail\" maxlength=\"32\"readonly=\"readonly\" ></td>
    </tr>
    <tr valign=\"top\">
      <td align=\"left\">$lang_website</td>
      <td align=\"left\"><input name=\"website\" type=\"text\" class=\"mailform_input\" id=\"website\" style=\"width: $input_width;\" value=\"$website\" maxlength=\"32\" readonly=\"readonly\"></td>
    </tr>

    <tr valign=\"top\">
      <td align=\"left\" colspan=\"2\">$lang_pref</td>
    </tr>
    <tr >
    <td align=\"left\" colspan=\"2\">
    <input type=\"radio\" name=\"group1\" value=\"phone\" $phone_status \"disabled\"> Phone 
    <input type=\"radio\" name=\"group1\" value=\"email\" $email_status \"disabled\"> Email 
    </td> 
    </tr>

    <tr valign=\"top\">
      <td width=\"100\" align=\"right\">$lang_subject</td>     
      <td align=\"left\"><input name=\"mail_subject\" type=\"text\" class=\"mailform_input\" id=\"mail_subject\" style=\"width: $input_width;\" value=\"$mail_subject\" maxlength=\"64\" readonly=\"readonly\"></td>
    </tr>
    <tr valign=\"top\">
      <td width=\"100\" align=\"right\">$lang_message</td>
      <td align=\"left\"><textarea name=\"mail_message\" cols=\"36\" rows=\"5\" style=\"width: $input_width;\" class=\"mailform_input\" readonly=\"readonly\">$mail_message</textarea></td>
    </tr>
    <tr valign=\"top\">
      <td colspan=\"2\" align=\"left\"><input name=\"Back\" type=\"button\" id=\"Back\" value=\"Back\" onClick=\"window.location='http://greendominion.com'\">
    </tr>
  </table>
</form>
</body>
</html>";


$entire_email_nobutt = 
"<html>
<head>
<style type=\"text/css\">
html {height:100%;}
body {height:100%; margin:0; padding:0;}
p,a,h1 {color: green;
	font-family: \"Apple Chancery\";
	font-style: oblique;
	font-variant: normal;
	font-weight: bold;
	font-size: x-large;
	line-height: 100%;
	word-spacing: normal;
	letter-spacing: normal;
	text-decoration: none;
	text-transform: none;
	text-align: left;
	text-indent: 0ex;}
#bg {position:fixed; top:0; left:0; width:100%; height:100%;}
#content {position:absolute; z-index:1; width:800px; top:50px; left:50px;}
#mail {position:absolute; z-index:1; top:500px; left:800px;}
</style>
<!--[if gt IE 5]>
<style type=\"text/css\">
html {overflow-y:hidden;}
body {overflow-y:auto;}
p,a,h1 {color: green;
	font-family: \"Apple Chancery\";
	font-style: oblique;
	font-variant: normal;
	font-weight: normal;
	font-size: x-large;
	line-height: 100%;
	word-spacing: normal;
	letter-spacing: normal;
	text-decoration: none;
	text-transform: none;
	text-align: left;
	text-indent: 0ex;}
#bg {position:absolute; z-index:-1;}
#content {position:absolute; top:50px; left:50px;}
#mail {position:absolute; left:800px; top:400px;}
</style>
<![endif]-->
</head>
<body>
<form name=\"BELLonline_email\" method=\"post\" style=\"margin: 0;\" action=\"\">
  <table  border=\"0\" cellspacing=\"2\" cellpadding=\"2\">
    <tr align=\"$title_align\" valign=\"top\">
      <td colspan=\"2\"><span style=\"$title_css\">$lang_title</span></td>
    </tr>
    <tr align=\"left\" valign=\"top\">
      <td colspan=\"2\">$info_notice$info_error</td>
    </tr>
    <tr valign=\"top\">
      <td align=\"right\">$lang_company_name</td>
      <td align=\"left\"><input name=\"company_name\" type=\"text\" class=\"mailform_input\" id=\"company_name\" style=\"width: $input_width;\" value=\"$company_name\" maxlength=\"32\"  readonly=\"readonly\"></td>
    </tr>
    <tr valign=\"top\">
      <td align=\"right\">$lang_addr_1</td>
      <td align=\"left\"><input name=\"address_1\" type=\"text\" class=\"mailform_input\" id=\"address_1\" style=\"width: $input_width;\" value=\"$address_1\" maxlength=\"32\" readonly=\"readonly\"></td>
    </tr>
    <tr valign=\"top\">
      <td align=\"right\">$lang_addr_2</td>
      <td align=\"left\"><input name=\"address_2\" type=\"text\" class=\"mailform_input\" id=\"address_2\" style=\"width: $input_width;\" value=\"$address_2\" maxlength=\"32\" readonly=\"readonly\"></td>
    </tr>
    <tr valign=\"top\">
      <td align=\"right\">$lang_city</td>
      <td align=\"left\"><input name=\"city\" type=\"text\" class=\"mailform_input\" id=\"city\" style=\"width: $input_width;\" value=\"$city\" maxlength=\"32\" readonly=\"readonly\"></td>
    </tr>
     <tr valign=\"top\">
      <td align=\"right\">$lang_state</td>
      <td align=\"left\"><input name=\"state\" type=\"text\" class=\"mailform_input\" id=\"state\" style=\"width: $input_width;\" value=\"$state\" maxlength=\"3\" readonly=\"readonly\"></td>
    </tr>
     <tr valign=\"top\">
      <td align=\"right\">$lang_zip</td>
      <td align=\"left\"><input name=\"zip\" type=\"text\" class=\"mailform_input\" id=\"zip\" style=\"width: $input_width;\" value=\"$zip\" maxlength=\"15\" readonly=\"readonly\"></td>
    </tr>
    <tr valign=\"top\">
      <td align=\"right\">$lang_contact</td>
      <td align=\"left\"><input name=\"contact\" type=\"text\" class=\"mailform_input\" id=\"contact\" style=\"width: $input_width;\" value=\"$contact\" maxlength=\"32\" readonly=\"readonly\"></td>
    </tr>
    <tr valign=\"top\">
      <td align=\"right\">$lang_phone</td>
      <td align=\"left\"><input name=\"phone\" type=\"text\" class=\"mailform_input\" id=\"phone\" style=\"width: $input_width;\" value=\"$phone\" maxlength=\"20\" readonly=\"readonly\"></td>
      <td width=\"10\" align=\"right\">$lang_ext</td>
      <td align=\"left\"><input name=\"ext\" type=\"text\" class=\"mailform_input\" id=\"ext\" size=\"5\" value=\"$ext\" maxlength=\"10\" readonly=\"readonly\"></td>
    </tr>
    <tr valign=\"top\">
      <td align=\"right\">$lang_fax</td>
      <td align=\"left\"><input name=\"fax\" type=\"text\" class=\"mailform_input\" id=\"fax\" style=\"width: $input_width;\" value=\"$fax\" maxlength=\"20\" readonly=\"readonly\"></td>
    </tr>
    <tr valign=\"top\">
      <td align=\"right\">$lang_youremail</td>
      <td align=\"left\"><input name=\"youremail\" type=\"text\" class=\"mailform_input\" id=\"youremail\" style=\"width: $input_width;\" value=\"$youremail\" maxlength=\"32\"readonly=\"readonly\" ></td>
    </tr>
    <tr valign=\"top\">
      <td align=\"left\">$lang_website</td>
      <td align=\"left\"><input name=\"website\" type=\"text\" class=\"mailform_input\" id=\"website\" style=\"width: $input_width;\" value=\"$website\" maxlength=\"32\" readonly=\"readonly\"></td>
    </tr>

    <tr valign=\"top\">
      <td align=\"left\" colspan=\"2\">$lang_pref</td>
    </tr>
    <tr >
    <td align=\"left\" colspan=\"2\">
    <input type=\"radio\" name=\"group1\" value=\"phone\" $phone_status \"disabled\"> Phone 
    <input type=\"radio\" name=\"group1\" value=\"email\" $email_status \"disabled\"> Email 
    </td> 
    </tr>

    <tr valign=\"top\">
      <td width=\"100\" align=\"right\">$lang_subject</td>     
      <td align=\"left\"><input name=\"mail_subject\" type=\"text\" class=\"mailform_input\" id=\"mail_subject\" style=\"width: $input_width;\" value=\"$mail_subject\" maxlength=\"64\" readonly=\"readonly\"></td>
    </tr>
    <tr valign=\"top\">
      <td width=\"100\" align=\"right\">$lang_message</td>
      <td align=\"left\"><textarea name=\"mail_message\" cols=\"36\" rows=\"5\" style=\"width: $input_width;\" class=\"mailform_input\" readonly=\"readonly\">$mail_message</textarea></td>
    </tr>
  </table>
</form>
</body>
</html>";
  mail($sendto_email, $mail_subject, $entire_email_nobutt, $headers);
  print $entire_email;
	}

?>
