<?php
// $Id: tmedit.php, v 1.5.2 2009/06/23 18:34:21 bpfeifer Exp $
/**
* Handler for TMEdit
* @ package TMEdit
* @ Copyright © 2004-2009 Bernhard Pfeifer - www.xhtmlsuite.com
* @ All rights reserved
* @ Released under GNU/GPL http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
* @version $Revision: 1.5.2 $
**/

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.event.plugin');

class plgEditorTMEdit extends JPlugin {

	function plgEditorTMEdit(& $subject, $config) {
		parent::__construct($subject, $config);
	}

	function onInit( ) {

	}

	function onGetContent( $name ) {
		return "editor" . $name . ".getHTML();\n";
	}

	function onSetContent( $name, $html ) {
		return "editor" . $name . ".setHTML(" . $html . ");";
	}

	function onDisplay( $name, $content, $width, $height, $buttons=true) {
		global $mainframe;
		$url = $mainframe->isAdmin() ? $mainframe->getSiteURL() : JURI::base();

		$db = & JFactory::getDBO();

		if( $this->params->def( 'csspreview', '0' ) ) {
			$db->setQuery( "SELECT template FROM #__templates_menu WHERE client_id='0' AND menuid='0'" );
			$template = $db->loadResult();

			$file_path = $url .'templates/'. $template .'/css/';

			if (file_exists( JPATH_SITE .'/templates/'. $template .'/css/editor.css' )) {
				$content_css = $file_path . 'editor.css';
			} else {
				$content_css = $file_path . 'template.css';
			}
		}
		$buttons = $this->_displayXTDButtons($name, $buttons);
		?>
		<script type="text/javascript">
			_editor_url = "<?php echo $url; ?>plugins/editors/tmedit/";
			_editor_base_url = "<?php echo JURI::root(); ?>";

			function aLE(f) {
				var oldonload = window.onload;
				if (typeof window.onload != 'function') {
					window.onload = f;
				} else {
					window.onload = function() {
						oldonload();
						f();
					}
				}
			};
		</script>
		<script type="text/javascript" src="<?php echo $url; ?>plugins/editors/tmedit/tmedit.js" charset="utf-8"></script>
		<script type="text/javascript" src="<?php echo $url; ?>plugins/editors/tmedit/dialog.js" charset="utf-8"></script>
		<script type="text/javascript" src="<?php echo $url; ?>plugins/editors/tmedit/lang/en.js" charset="utf-8"></script>
		<style type="text/css">@import url(<?php echo $url; ?>plugins/editors/tmedit/tmedit.css)</style>
		<script type="text/javascript">
			<?php
			if( $this->params->def( 'contextmenu', '0' ) ) {
			?>
			TMEdit.loadPlugin("ContextMenu");
			<?php
			}
			if( $this->params->def( 'css', '0' ) ) {
			?>
			TMEdit.loadPlugin("CSS");
			<?php
			}
			if( $this->params->def( 'insertsmiley', '0' ) ) {
			?>
			TMEdit.loadPlugin("InsertSmiley");
			<?php
			}
			?>
			var editor<?php echo $name ?> = null;
		</script>
		<textarea name="<?php echo $name; ?>" id="<?php echo $name; ?>" style="font-size: 13px; color: black; width:<?php echo $width; ?>px; height:<?php echo $height; ?>px;"><?php echo $content; ?></textarea>
		<?php echo $buttons; ?>
		<script type="text/javascript" charset="utf-8">
			editor<?php echo $name ?> = new TMEdit("<?php echo $name ?>");
			var config<?php echo $name ?> = editor<?php echo $name ?>.config;
		<?php
		if( $this->params->def( 'csspreview', '0' ) && isset($template) && !empty($template) ) {
		?>
			config<?php echo $name ?>.pageStyle='@import url(<?php echo $content_css; ?>); html, body { margin: 4px; text-align: left;}';
		<?php
		} else {
		?>
			config<?php echo $name ?>.pageStyle='body {background: white; margin: 5px;} body, td, p {font-family: sans-serif; font-size: 12px;}';
		<?php
		}
		?>
			config<?php echo $name ?>.sizeIncludesToolbar = false;
			config<?php echo $name ?>.height = "500px";
			config<?php echo $name ?>.width = "625px";

			config<?php echo $name ?>.toolbar = [
				[ "fontname", "space", "fontsize", "space", "formatblock", "space", "separator", "space", "bold", "italic", "underline", "separator", "strikethrough", "subscript", "superscript", "separator", "createlink", "separator", "undo", "redo"],
				[ "justifyleft", "justifycenter", "justifyright", "justifyfull", "separator", "insertorderedlist", "insertunorderedlist", "outdent", "indent", "separator", "forecolor", "hilitecolor", "space", "textindicator", "space", "removeformat", "separator", "htmlmode", "separator", "preview"],
				[ "inserthorizontalrule", "insertcharacter", "insertimage", "insertfile", "separator", "inserttable", "toggleborders", "separator", "cut", "copy", "paste", "space", "separator", "showhelp"],
			];

		<?php
			if( $this->params->def( 'contextmenu', '0' ) ) {
		?>
			editor<?php echo $name ?>.registerPlugin(ContextMenu);
		<?php
			}
			if( $this->params->def( 'css', '0' ) ) {
		?>
			editor<?php echo $name ?>.registerPlugin(CSS, {
				combos : [ { label: "CSS Styles:",
					// 5 default Joomla! CSS template classes contained
					// add your own CSS classes like this (but leave &mdash; Default &mdash; for removal of classes)
					// "Class name to be shown in the drop down": "name of the class like typed in your CSS file",
					// Note: DO NOT put a comma to the last line (see "Message": "message")!
						options: { "&mdash; Default &mdash;": "",
							"Small": "small",
							"Small Dark": "smalldark",
							"Contentheading": "contentheading",
							"Componentheading": "componentheading",
		 					"Message": "message"
						}
					} ]
				}
			);
		<?php
			}
			if( $this->params->def( 'insertsmiley', '0' ) ) {
		?>
			editor<?php echo $name ?>.registerPlugin(InsertSmiley);
		<?php
			}
		?>
			aLE(
				function () {
					editor<?php echo $name ?>.generate('<?php echo $name ?>');
				}
			);
		</script>
		<?php
	}

	function onGetInsertMethod($name) {
		$doc = & JFactory::getDocument();

		$js= "function jInsertEditorText( text ) {
			editor" . $name . ".insertHTML(text);
		}";
		$doc->addScriptDeclaration($js);
		return true;
	}

	function _displayXTDButtons($name, $buttons) {
		JHTML::_('behavior.modal', 'a.modal-button');

		$args['name'] = $name;
		$args['event'] = 'onGetInsertMethod';

		$return = '';
		$results[] = $this->update($args);
		foreach ($results as $result) {
			if (is_string($result) && trim($result)) {
				$return .= $result;
			}
		}

		if(!empty($buttons)) {
			$results = $this->_subject->getButtons($name, $buttons);
			$return .= "\n<div id=\"editor-xtd-buttons\">\n";
			foreach ($results as $button) {
				if ($button->get('name')) {
					$modal = ($button->get('modal')) ? 'class="modal-button"' : null;
					$href = ($button->get('link')) ? 'href="'.JURI::base().$button->get('link').'"' : null;
					$onclick = ($button->get('onclick')) ? 'onclick="'.$button->get('onclick').'"' : null;
					$return .= "<div class=\"button2-left\"><div class=\"".$button->get('name')."\"><a ".$modal." title=\"".$button->get('text')."\" ".$href." ".$onclick." rel=\"".$button->get('options')."\">".$button->get('text')."</a></div></div>\n";
				}
			}
			$return .= "</div>\n";
		}
		return $return;
	}
}