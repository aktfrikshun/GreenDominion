<?php
/**
* @version		$Id: Enbed pdf v1.7 2010-11-08 17:57:35Z $
* @package		Joomla 1.5
* @copyright	Copyright (C) 2005 - 2010 Maik Heinelt. All rights reserved.
* @author		Maik Heinelt (www.heinelt.info)
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
 
 
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.plugin.plugin');



class plgContentenbed_pdf extends JPlugin
{
	function plgContentenbed_pdf( &$subject, $params ) 
	{
		parent::__construct( $subject, $params );
	}




	function onAfterRender()
    	{
    		require_once('plugins/content/browser.php');
    		
        	$output = JResponse::getBody();
        	$pattern = "#{pdf}(.*?){/pdf}#s";
			$found = preg_match_all($pattern, $output, $matches);
			
			
			$plugin = & JPluginHelper::getPlugin('content', 'enbed_pdf');

			// [[[ Load plugin params info
			$pluginParams = new JParameter($plugin->params);
			$mode = $pluginParams->def('mode', 1);
			$proxy = $pluginParams->def('proxy', 0);
			$height = $pluginParams->def('dheight', 300);
			$width =  $pluginParams->def('dwidth', 400);
			$alt_link = $pluginParams->def('alt_link', 0);
			$link_comment = $pluginParams->def('link_comment', 0);
			
			// !! Adobe Reader default settings !!
			$ar_toolbar = $pluginParams->def('ar_toolbar', 1);
			$ar_navpanes = $pluginParams->def('ar_navpanes', 1);
			$ar_scrollbar = $pluginParams->def('ar_scrollbar', 1);
			$ar_searchbar = $pluginParams->def('ar_searchbar', 0);
			// ]]] Load plugin params info


			// [[[ Adobe Reader default settings (Toolbar, Navpanel, Scrollbar) 2010.11.10
			$ar_param = null;
			$o_tb = $ar_toolbar;
			$o_np = $ar_navpanes;
			$o_scb = $ar_scrollbar;
			$o_sb = $ar_searchbar;
			function arparams($var1, $var2, $var3, $var4)
			{
				$ar_search = null;
				if ($var4 == 1)
				{
					$ar_search = '&search="foo"';
				}
				$ar_param = "#toolbar=$var1&navpanes=$var2&scrollbar=$var3$ar_search";
				
				return $ar_param;
			}
			// ]]] Adobe Reader default settings (Toolbar, Navpanel, Scrollbar) 2010.11.10


			// [[[ Browser identification (2010-11-08 Browser class changed, cause of similar class names in other Joomla extensions)
			$isIE = 0;
			$Enbedbrowser = new EnbedBrowser();
			if ( $Enbedbrowser->getBrowser() == EnbedBrowser::BROWSER_IE ) 
			{
				$isIE = 1;
			}
			// ]]] Browser identification (2010-11-08 Browser class changed, cause of similar class names in other Joomla extensions)
	

	
			// [[[ 2010-10-29 Use the proxy.php to avoid plocking the google page in IE8 >.
			$googlepath = "http://docs.google.com/viewer";
			if ($proxy == 1 && $isIE == 1)
			{
				$value = $_SERVER['SERVER_ADDR'];
				setcookie ("TBqP25b5nGuuFTph", $value,time()+100); 	
				$googlepath = "plugins/content/proxy.php";
			}
			// ]]] 2010-10-29 Use the proxy.php to avoid plocking the google page in IE8 >.

			$mcount = 0;
			$alink = null;
				
			if ($found)
			{
				
				foreach ( $matches[0] as $value ) 
				{
				
				$enpdf = $value;
				$find = '|';
				
				
				if(strstr($enpdf, $find))
				{
					$arr = explode('|',$enpdf);
					$enpdf = ltrim($arr[0], "{pdf}");
				
					// [[[ 2010-11-05 pdf link entry under each pdf file, in case it will not be displayed
					if ($alt_link)
					{
						$alink = "<br/>$link_comment :<a href=\"$enpdf\" target=\"_blank\">$enpdf</a><br/>";
					}
					// ]]] 2010-11-05 pdf link entry under each pdf file, in case it will not be displayed
					
					foreach ($arr as $phrase)
						{
							// Parse for PDF-height
							if (strstr(strtolower($phrase), 'height:'))	
							{
								$tpm1 = explode(':',$phrase);
								$height = trim($tpm1[1], '"');
							}
								
							// Parse for PDF-width
							if (strstr(strtolower($phrase), 'width:'))
							{
								$tpm1 = explode(':',$phrase);
								$width = trim($tpm1[1], '"');
							} 
							
							// Parse for adobe reader toolbar 2010-11-10
							if (strstr(strtolower($phrase), 'toolbar:'))
							{
								$tpm1 = explode(':',$phrase);
								$ar_toolbar = trim($tpm1[1], '"');
							}
							
							// Parse for adobe reader navpanel 2010-11-10
							if (strstr(strtolower($phrase), 'nav:'))
							{
								$tpm1 = explode(':',$phrase);
								$ar_navpanes = trim($tpm1[1], '"');
							}
							
							// Parse for adobe reader scrollbar 2010-11-10
							if (strstr(strtolower($phrase), 'scroll:'))
							{
								$tpm1 = explode(':',$phrase);
								$ar_scrollbar = trim($tpm1[1], '"');
							}
							
							// Parse for adobe reader searchpanel 2010-11-10
							if (strstr(strtolower($phrase), 'search:'))
							{
								$tpm1 = explode(':',$phrase);
								$ar_searchbar = trim($tpm1[1], '"');
							}
							
							// [[[ Parse for the PDF-app
							if (strstr(strtolower($phrase), 'app:'))
							{
								$tpm1 = explode(':',$phrase);
								$tpm1[1] = rtrim($tpm1[1], "{/pdf}");
								$app = trim(strtolower($tpm1[1]), '"');
								
								
								if ($app == "acrobat" || $app == "adobe")
								{
									// 2010-10-10 Fix for disabled acrobat plugin in IE
									if ($isIE == 1) 
									{
										$replacement[$mcount] = "<object classid=\"clsid:CA8A9780-280D-11CF-A24D-444553540000\" width=\"$width\" height=\"$height\"><param name=\"src\" value=\"$enpdf$ar_param\" />PDF plugin is deactivated! Please click the link</br>
										<a href=\"$enpdf\" target=\"_blank\">$enpdf</a> 
										</object>";	
									}
									else
									{
										$replacement[$mcount] = '<embed width="'.$width.'" height="'.$height.'" href="'.$enpdf.'" src="'.$enpdf.$ar_param.'" hidden="false">';
									}
								}
									
								if ($app == "google")
								{
									$enpdf_new = str_replace('/', '%2F', $enpdf);
									$enpdf = str_replace(':', '%3A', $enpdf_new);
									$replacement[$mcount] = '<iframe src="'.$googlepath.'?url='.$enpdf.'&embedded=true" width="'.$width.'" height="'.$height.'" style="border: none;"></iframe>'.$alink;	
								}
							}
							// ]]] Parse for the PDF-app
							else
							{
								$ar_param = arparams($ar_toolbar, $ar_navpanes, $ar_scrollbar, $ar_searchbar); // Let's check for Acrobat Reader options !
								
								if ($mode == 1) // If app|google
								{
									$enpdf_new = str_replace('/', '%2F', $enpdf);
									$genpdf = str_replace(':', '%3A', $enpdf_new);
									$replacement[$mcount] = '<iframe src="'.$googlepath.'?url='.$genpdf.'&embedded=true" width="'.$width.'"  height="'.$height.'" style="border: none;"></iframe>'.$alink;
								}
								else
								{
									// [[[ 2010-10-10 Fix for disabled acrobat plugin in IE
									if ($isIE == 1) 
									{
										$replacement[$mcount] = "<object classid=\"clsid:CA8A9780-280D-11CF-A24D-444553540000\" width=\"$width\" height=\"$height\"><param name=\"src\" value=\"$enpdf$ar_param\" />PDF plugin is deactivated! Please click the link</br>
										<a href=\"$enpdf\" target=\"_blank\">$enpdf</a> 
										</object>";	
									}
									else
									{
										$replacement[$mcount] = '<embed width="'.$width.'" height="'.$height.'" href="'.$enpdf.'" src="'.$enpdf.$ar_param.'">';	
									}
									// ]]] 2010-10-10 Fix for disabled acrobat plugin in IE
								}	
							}
						}
				}
				else // If there are no settings at the string in article, this code will be used.
					{
						$enpdf1 = ltrim($enpdf, "{pdf}");
						$enpdf1 = rtrim($enpdf1, '/pdf}');
						$enpdf = rtrim($enpdf1, '{');
						
						// [[[ 2010-11-05 pdf link entry under each pdf file, in case it will not be displayed
						if ($alt_link)
						{
							$alink = "<br/>$link_comment :<a href=\"$enpdf\" target=\"_blank\">$enpdf</a><br/>";
						}
						// ]]] 2010-11-05 pdf link entry under each pdf file, in case it will not be displayed
						
						
						if ($mode == 1) // If app|google
						{
							$enpdf_new = str_replace('/', '%2F', $enpdf);
							$enpdf = str_replace(':', '%3A', $enpdf_new);
							$replacement[$mcount] = '<iframe src="'.$googlepath.'?url='.$enpdf_new.'&embedded=true" width="'.$width.'" height="'.$height.'" style="border: none;"></iframe>'.$alink;
						}
						else
						{
							$ar_param = arparams($ar_toolbar, $ar_navpanes, $ar_scrollbar, $ar_searchbar); // Let's check for Acrobat Reader options !
							
							// 2010-10-10 Fix for disabled acrobat plugin in IE
							if ($isIE == 1) 
							{
								$replacement[$mcount] = "<object classid=\"clsid:CA8A9780-280D-11CF-A24D-444553540000\" width=\"$width\" height=\"$height\"><param name=\"src\" value=\"$enpdf$ar_param\" />PDF plugin is deactivated! Please click the link</br>
								<a href=\"$enpdf\" target=\"_blank\">$enpdf</a> 
								</object>".$alink;	
							}
							else
							{
								$replacement[$mcount] = '<embed width="'.$width.'" height="'.$height.'" href="'.$enpdf.'" src="'.$enpdf.$ar_param.'" hidden="false">'.$alink;
							}
						}
					}
					
					// Re-reset of Adobe Reader default settings (Toolbar, Navpanel, Scrollbar) 2010.11.10
					$o_tb = $ar_toolbar = $o_tb;
					$ar_navpanes = $o_np;
					$ar_scrollbar = $o_scb;
					$ar_searchbar = $o_sb;
					// Re-reset of Adobe Reader default settings (Toolbar, Navpanel, Scrollbar) 2010.11.10
					
			    	$mcount = $mcount + 1;
				}
				
				
				
				// [[[ Replace the original content with the added pdf content of article.
				for($i = 0; $i < count($replacement); $i++)
				{
				    $output = preg_replace($pattern, $replacement[$i], $output, 1);
				}
					
	        	JResponse::setBody($output);
	        	// ]]] Replace the original content with the added pdf content of article.
			}
				
				// Delete cookie what we needed to execute proxy.php
				return true;
				setcookie ("TBqP25b5nGuuFTph", "", time() - 100);
    	}
}
?>