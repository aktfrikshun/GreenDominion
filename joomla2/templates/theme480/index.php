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

$showrightColumn = $this->countModules('right');
$showFlashColumn = $this->countModules('top');








if(JRequest::getCmd('task') != 'edit') $Edit = false; else $Edit = true;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>" >
<head>
<jdoc:include type="head" />
<script type="text/javascript" src="<?php echo $this->baseurl?>/media/system/js/mootools.js"></script>
<script type="text/javascript" src="<?php echo $path ?>/scripts/maxheight.js"></script><!--equal-->

<script type="text/javascript" src="<?php echo $path ?>/scripts/cufon-yui.js"></script>
<script type="text/javascript" src="<?php echo $path ?>/scripts/Arial_MT_400.font.js"></script>
<script type="text/javascript" src="<?php echo $path ?>/scripts/cufon-replace.js"></script>

<script type="text/javascript" src="http://info.template-help.com/files/ie6_warning/ie6_script_other.js"></script>

<script type="text/javascript" src="<?php echo $path ?>/scripts/imagepreloader.js"></script>
<script type="text/javascript">
	preloadImages([
		'../images/menu_button_left.png',
		'../images/menu_button_right.png'
		]);
</script>

<link rel="stylesheet" href="<?php echo $path ?>/css/constant.css" type="text/css" />
<link rel="stylesheet" href="<?php echo $path ?>/css/template.css" type="text/css" />
<!--[if IE 6]>
	<script type="text/javascript" src="<?php echo $path ?>/scripts/ie_png.js"></script>
    <script type="text/javascript">
       ie_png.fix('.png');
   </script>
<![endif]-->
</head>
<body id="body">
<div class="top-tail clear">
	<div class="main-wrapper-center clear">
    	<div class="main">
            <!--header-->
            <div class="clear row-header">
                <div class="fright">
                	<div class="row-search clear"><jdoc:include type="modules" name="user4" style="search" /></div>
                    <div class="row-top-menu clear"><jdoc:include type="modules" name="user3" style="topmenu" /></div>
                </div>
                <div class="fleft">
                    <h1 id="logo"><a href="<?php echo $_SERVER['PHP_SELF']?>" title="archicom "><img  title="archicom " src="<?php echo $path ?>/images/logo.png" class="png"   alt="archicom "  /></a></h1>
                </div>
            </div>
            <?php if ($showFlashColumn) : ?>
    		<div class="row-flash clear"><jdoc:include type="modules" name="top" style="xhtml" /></div>
            <?php endif;?>
             <!--content-->
             <div class="content-indent">
             	<div class="box">
                    <div class="border-top png"></div>
                    <div class="border-left png">
                        <div class="border-right png">
                            <div class="xcontent png">
                                <div class="wrapper">
                                    <div id="content">
                                        <div class="clear">
                                            <!--right-->
                                            <?php if ($showrightColumn && !$Edit) : ?>
                                            <div id="right" class="png equal">
                                                <div class="right-indent">
                                                    <jdoc:include type="modules" name="right" style="wrapper_box" />
                                                </div>
                                            </div>
                                            <?php endif;?>
                                            <!--center-->
                                            <div id="container" class="equal">
                                                 <div class="container-indent">
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
                                     </div>                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="border-bottom png"></div>
                </div>
             </div>
              <!--footer-->
   			 <div class="footer"><?php echo JText::_('Powered by') ?> <a href="#">Joomla!</a> &nbsp;&nbsp;&nbsp;<!--{%FOOTER_LINK} --></div>
        </div>
    </div>
</div>
<script type="text/javascript"> Cufon.now(); </script>

</body>
</html>

