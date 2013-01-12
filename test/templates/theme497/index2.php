<?php
/**
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');
$url = clone(JURI::getInstance());
$path = $this->baseurl.'/templates/'.$this->template;

$showleftColumn = $this->countModules('left');
$showrightColumn = $this->countModules('right');
$showBox1Column = $this->countModules('user1');
$showBox2Column = $this->countModules('user2');
$showFlashColumn = $this->countModules('top');









if(JRequest::getCmd('task') != 'edit') $Edit = false; else $Edit = true;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>" >
<head>
<jdoc:include type="head" />
<?php
$user =& JFactory::getUser();  
if ($user->get('guest') == 1) {  
$headerstuff = $this->getHeadData();  
$headerstuff['scripts'] = array();  
$this->setHeadData($headerstuff); }  
?>

<script type="text/javascript" src="<?php echo $path ?>/scripts/jquery.js"></script>
<script type="text/javascript" src="<?php echo $path ?>/scripts/maxheight.js"></script>


		<script type="text/javascript">
	var $j = jQuery.noConflict();
		$j(document).ready(function(){
		$j('.menu-nav li').hover(
			function() {
				$j(this).addClass("active");
				$j(this).find('.ulwrapper').stop(false, true).slideDown();
				$j(this).find('.ulwrapper .ulwrapper').stop(false, true).slideUp('fast');
			},
			function() {
				$j(this).removeClass("active");        
				$j(this).find('div').stop(false, true).slideUp('fast');
			}
		);
		$j('.ulwrapper').hover(
			function() {
				$j('.parent').addClass("active_tab");
			},
			function() {
				$j('.parent').removeClass("active_tab");        
			}
		);
	});
	</script>



<!--<script type="text/javascript" src="<?php echo $path ?>/scripts/cufon-yui.js"></script>
<script type="text/javascript" src="<?php echo $path ?>/scripts/Trajan_Pro_400.font.js"></script>
<script type="text/javascript" src="<?php echo $path ?>/scripts/cufon-replace.js"></script>-->



<!--<script type="text/javascript" src="http://info.template-help.com/files/ie6_warning/ie6_script_other.js"></script>-->

<link rel="stylesheet" href="<?php echo $path ?>/css/constant.css" type="text/css" />
<link rel="stylesheet" href="<?php echo $path ?>/css/template.css" type="text/css" />
</head>
<body id="body"  onload="new ElementMaxHeight();">
<!--header-->
<div class="tail-top-menu">
	<div class="main">
    	<div class="row-top-menu"><jdoc:include type="modules" name="user3" style="topmenu" /></div>
        <div class="clear1"></div>
    </div>
</div>
<table class="tail-flash">
	<tr>
    	<td class="tail-extra"></td>
        <td class="wrapper1-center">
        	<table class="wrapper1-center">
            	<tr>
                	<td>
                    <div class="row-logo">
                        <jdoc:include type="modules" name="user4" style="search" />
                        <h1 id="logo"><a href="<?php echo $_SERVER['PHP_SELF']?>" title="Green Dominion "><img  title="Green Dominion " src="<?php echo $path ?>/images/logo.png"   alt="Green Dominion "  /></a></h1>
                    </div>
                    <?php if ($showFlashColumn) : ?>
                    <div class="row-flash">
                        <jdoc:include type="modules" name="top" style="xhtml" />
                    </div>
                    <?php endif;?>
                    </td>
                </tr>
            </table>
        </td>
        <td class="tail-extra"></td>
    </tr>
</table>
<!--content-->
<table class="tail-content">
	<tr>
    	<td class="tail-extra"></td>
        <td class="width2">
        	<table class="width2">
            	<tr>
                	<td>
                    	<div id="content">
                            <div class="clear">
                                <!--right-->
                                <?php if ($showrightColumn && !$Edit) : ?>
                                <div id="right" class="maxheight">
                                    <div class="right-indent">
                                        <jdoc:include type="modules" name="right" style="wrapper_box" />
                                    </div>
                                </div>
                                <?php endif;?>
                                 <!--left-->
                                <?php if ($showleftColumn && !$Edit) : ?>
                                <div id="left" class="maxheight">
                                    <div class="left-indent">
                                        <jdoc:include type="modules" name="left" style="wrapper_box" />
                                    </div>
                                </div>
                                <?php endif;?>
                                <!--center-->
                                <div id="container">
                                     <?php if ($showBox1Column && $option!="com_search"  && !$Edit) : ?>
                                    <div class="custom-top"><jdoc:include type="modules" name="user1" style="wrapper_box_extra" /></div>
                                    <?php endif;?>
                                     <?php if ($showBox2Column && $option!="com_search"  && !$Edit) : ?>
                                    <div class="custom-bottom"><jdoc:include type="modules" name="user2" style="wrapper_box_extra" /></div>
                                    <?php endif;?>
                                     <div class="clear">
                                        <?php if ($this->getBuffer('message')) : ?>
                                        <div class="error err-space">
                                            <jdoc:include type="message" />
                                        </div>
                                        <?php endif; ?>
                                        <jdoc:include type="component" />
                                    </div>
                                </div>
                            </div>
                         </div>
                    </td>
                </tr>
            </table>
        </td>
       <td class="tail-extra"></td>
    </tr>
</table>

<!--footer-->
<div class="tail-footer clear">
	<div class="main">
  	  <div class="footer"><?php echo JText::_('Copyright') ?> <a href="#">GreenDominion 2011</a> &nbsp;&nbsp;&nbsp;<!--{%FOOTER_LINK} --></div>
    </div>
</div>
<!--<script type="text/javascript"> Cufon.now(); </script>-->
</body>
</html>