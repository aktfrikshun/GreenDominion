<?php
defined('_JEXEC') or die('Restricted access');

/**
 * This is a file to add template specific chrome to module rendering.  To use it you would
 * set the style attribute for the given module(s) include in your template to use the style
 * for each given modChrome function.
 *
 * eg.  To render a module mod_test in the sliders style, you would use the following include:
 * <jdoc:include type="module" name="test" style="slider" />
 *
 * This gives template designers ultimate control over how modules are rendered.
 *
 * NOTICE: All chrome wrapping methods should be named: modChrome_{STYLE} and take the same
 * three arguments.
 */


/**
 * Custom module chrome, echos the whole module in a <div> and the header in <h{x}>. The level of
 * the header can be configured through a 'headerLevel' attribute of the <jdoc:include /> tag.
 * Defaults to <h3> if none given
 */

function modChrome_wrapper_box ($module, &$params, &$attribs)
{
	$headerLevel = isset($attribs['headerLevel']) ? (int) $attribs['headerLevel'] : 3;
	if (!empty ($module->content)) : ?>
		<div class="wrapper-box module<?php echo $params->get('moduleclass_sfx'); ?>">
            <?php if ($module->showtitle) : ?>
                <div class="boxTitle">
                     <h<?php echo $headerLevel; ?> ><?php echo $module->title; ?></h<?php echo $headerLevel; ?>>
                 </div>
                <?php endif; ?>
                <div class="clear">
                     <div class="boxIndent">
                        <?php echo $module->content; ?>
                    </div>
               </div>
		</div>
	<?php endif; 
}

function modChrome_wrapper_box_extra ($module, &$params, &$attribs)
{
	$headerLevel = isset($attribs['headerLevel']) ? (int) $attribs['headerLevel'] : 3;
	if (!empty ($module->content)) : ?>
		<div class="wrapper-box-extra module<?php echo $params->get('moduleclass_sfx'); ?>">
            <?php if ($module->showtitle) : ?>
                <div class="boxTitle">
                     <h<?php echo $headerLevel; ?> ><?php echo $module->title; ?></h<?php echo $headerLevel; ?>>
                 </div>
                <?php endif; ?>
                <div class="clear">
                     <div class="boxIndent">
                        <?php echo $module->content; ?>
                    </div>
               </div>
		</div>
	<?php endif; 
}

function modChrome_topmenu ($module, &$params, &$attribs)
{	
	?>
    	<?php
		$headerLevel = isset($attribs['headerLevel']) ? (int) $attribs['headerLevel'] : 3;
		if (!empty ($module->content)) : ?>
	<?php echo $module->content; ?>
	<?php endif; ?>
    <?php
}


function modChrome_search ($module, &$params, &$attribs)
{
	$headerLevel = isset($attribs['headerLevel']) ? (int) $attribs['headerLevel'] : 3;
	if (!empty ($module->content)) : ?>
<div class="module-search<?php echo $params->get('moduleclass_sfx'); ?>">
    <?php if ($module->showtitle) : ?>    <h<?php echo $headerLevel; ?>><?php echo $module->title; ?></h<?php echo $headerLevel; ?>>

    <?php endif; ?>
    <?php echo $module->content; ?> </div>
<?php endif;
}





