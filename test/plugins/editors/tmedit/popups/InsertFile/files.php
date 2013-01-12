<?php
// $Id: files.php, v 1.2 2009/06/23 14:47:53 bpfeifer Exp $
/**
* TMEdit InsertFile
* @ package TMEdit
* @ Copyright © 2004-2009 Bernhard Pfeifer - www.xhtmlsuite.com
* @ All rights reserved
* @ Released under the GNU/GPL http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
* @version $Revision: 1.2 $
**/

	require('config.inc.php');

	$refresh_dirs = false;
	$err = false;

	if (isset($_REQUEST['refresh'])) {
		$refresh_dirs = true;
	}

	if (isset($_REQUEST['file'])) {
	 	$file = str_replace('\\', '/', $_REQUEST['file']);
		$file = str_replace('../', '', $file);
		$file = str_replace('./', '', $file);
		$file = str_replace('/', '', $file);
	} else {
		$file = '';
	}

	if (isset($_REQUEST['path'])) {
	 	$path = str_replace('\\', '/', $_REQUEST['path']);
		$path = str_replace('../', '', $path);
		$path = str_replace('./', '', $path);
		if ('/' != substr($path,0,1)) $path =  '/'.$path;
		if ('/' != substr($path,-1,1)) $path = $path.'/';
	} else {
		$path = '/';
	}

	$MY_FILE = $file;
	$MY_PATH = $path;

	$paths = explode('/', $MY_PATH);
	$MY_UP_PATH = '/';
	for($i=0; $i<count($paths)-2; $i++){
			$path = $paths[$i];
			if(strlen($path) > 0) $MY_UP_PATH .= $path.'/';
	}

	function dirs($dir,$abs_path) {
		$d = dir($dir);
		$dirs = array();
		while (false !== ($entry = $d->read())) {
			if(is_dir($dir.'/'.$entry) && substr($entry,0,1) != '.')  {
				$path['path'] = $dir.'/'.$entry;
				$path['name'] = $entry;
				$dirs[$entry] = $path;
			}
		}
		$d->close();
		ksort($dirs);
		for($i=0; $i<count($dirs); $i++) {
			$name = key($dirs);
			$current_dir = $abs_path.'/'.$dirs[$name]['name'];
			echo ", \"$current_dir/\"\n";
			dirs($dirs[$name]['path'],$current_dir);
			next($dirs);
		}
	}

	function parse_size($size) {
		if($size < 1024)
			return $size.' B';
		else if($size >= 1024 && $size < 1024*1024) {
			return sprintf('%01.2f',$size/1024.0).' KB';
		} else {
			return sprintf('%01.2f',$size/(1024.0*1024)).' MB';
		}
	}

	function parse_time($timestamp) {
		global $MY_DATETIME_FORMAT;
		return date($MY_DATETIME_FORMAT, $timestamp);
	}

	function parse_icon($ext) {
		switch (strtolower($ext)) {
			case 'doc': return 'doc_small.gif';
			case 'xls': return 'xls_small.gif';
			case 'ppt': return 'ppt_small.gif';
			case 'html': return 'html_small.gif';
			case 'pdf': return 'pdf_small.gif';
			case 'rar': return 'rar_small.gif';
			case 'zip': return 'zip_small.gif';
			case 'gz': return 'gz_small.gif';
			case 'mov': return 'mov_small.gif';
			case 'txt': return 'txt_small.gif';
			case 'png': return 'png_small.gif';
			case 'jpg': return 'jpg_small.gif';
			case 'gif': return 'gif_small.gif';
	  		default:
			    return 'def_small.gif';

		}
	}


	function draw_no_results() {
		global $MY_MESSAGES;
		echo '<table style="width:100%;height:150px;" border="0" cellpadding="0" cellspacing="0"><tr>
		    <td bgcolor="white"><div align="center" style="font-size:large;font-weight:bold;color:#CCCCCC;font-family: Helvetica, sans-serif;">';
		echo $MY_MESSAGES['nofiles'];
		echo '</div></td></tr></table>';
	}

	function draw_no_dir() {
		global $MY_MESSAGES;
		global $MY_DOCUMENT_ROOT;
		echo '<table style="width:100%;height:150px;" border="0" cellpadding="0" cellspacing="0"><tr>
	   	 <td><div align="center" style="font-size:small;font-weight:bold;color:#CC0000;font-family: Helvetica, sans-serif;">';
		echo $MY_MESSAGES['configproblem']." ".$MY_DOCUMENT_ROOT;
		echo '</div></td></tr></table>';
	}

	?>
	<html>
	<head>
	<title>File Browser</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<style type="text/css">
	<!--
	body {
		font-family:	Verdana, Helvetica, Arial, Sans-Serif;
		font:			Message-Box;
	}
	code {
		font-size:	1em;
	}
	a {
		color: black;
	}

	a:visited {
		color: black;
	}

	-->
	</style>
	<link type="text/css" rel="StyleSheet" href="css/sortabletable.css" />
	<script type="text/javascript" src="js/sortabletable.js"></script>
	<script language="JavaScript" type="text/JavaScript">
	<!--

	function MM_findObj(n, d) { //v4.01
	  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
	    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
	  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
	  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
	  if(!x && d.getElementById) x=d.getElementById(n); return x;
	}

	function MM_showHideLayers() { //v6.0
	  var i,p,v,obj,args=MM_showHideLayers.arguments;
	  for (i=0; i<(args.length-2); i+=3) if ((obj=MM_findObj(args[i],window.top.document))!=null) { v=args[i+2];
	    if (obj.style) { obj=obj.style; v=(v=='show')?'visible':(v=='hide')?'hidden':v; }
	    obj.visibility=v; }
	}

	function changeLoadingStatus(state) {
		var statusText = null;
		if(state == 'load') {
			statusText = '<?php echo $MY_MESSAGES['loading']; ?>';
		}
		if(statusText != null) {
			var obj = MM_findObj('loadingStatus', window.top.document);
			if (obj != null && obj.innerHTML != null)
				obj.innerHTML = statusText;
			MM_showHideLayers('loading','','show')
		}
	}

	function changeDir(Path) {
		location.href = "files.php?dialogname=<?php echo $MY_NAME; ?>&path="+Path;
	}

	function goUp() {
		location.href = "files.php?dialogname=<?php echo $MY_NAME.'&path='.$MY_UP_PATH; ?>";
	}

	function fileSelected(filename, caption, icon, size, date) {
		var topDoc = window.top.document.forms[0];
		topDoc.f_url.value = filename;
		topDoc.f_caption.value = caption;
		topDoc.f_icon.value = icon;
		topDoc.f_size.value = size;
		topDoc.f_date.value = date;
	}

	function updateDir() {
		var newPath = "<?php echo $MY_PATH; ?>";
		if(window.top.document.forms[0] != null) {
			var allPaths = window.top.document.forms[0].path.options;
			for(i=0; i<allPaths.length; i++)  {
				allPaths.item(i).selected = false;
				if((allPaths.item(i).value)==newPath) {
					allPaths.item(i).selected = true;
				}
			}
		}
	}

	<?php
	if ($refresh_dirs) { ?>
	function refreshDirs() {
		var isIE = (navigator.appName == "Microsoft Internet Explorer") ? true : false;
		var allPaths = window.top.document.forms[0].path.options;
		var fields = ["/" <?php dirs($MY_DOCUMENT_ROOT,'');?>];
		var newPath = "<?php echo $MY_PATH; ?>";
	 	for(i = allPaths.length0; i > 0; i--) {
				allPaths[i-1]=null;
			}

		for(i=0; i<fields.length; i++) {
			var newElem =	document.createElement("OPTION");
			var newValue = fields[i];
			newElem.text = newValue;
			newElem.value = newValue;
			if(newValue == newPath)
				newElem.selected = true;
			else
				newElem.selected = false;
			allPaths.add(newElem);
		}
	}
	refreshDirs();
	<?php }
	if ($err) {
		echo 'alert(\''.$err.'\');';
	}
	 ?>


	//-->
	</script>
	</head>
	<body onLoad="updateDir();">
	<?php

	$d = @dir($MY_DOCUMENT_ROOT.$MY_PATH);
	if($d) {
		$t_header = '<table class="sort-table" id="tableHeader" cellspacing="0" width="100%">
		<col />
		<col />
		<col style="text-align: right" />
		<col />
		<col />
		<thead>
			<tr>
				<td width="7%" id="sortmefirst" onclick="st2.sort(5)">'.$MY_MESSAGES['type'].'</td>
				<td width="50%" title="CaseInsensitiveString" onclick="st1.sort(1);st2.sort(1)">'.$MY_MESSAGES['name'].'</td>
				<td width="10%" align="right" onclick="st2.sort(6);">'.$MY_MESSAGES['size'].'</td>
				<td width="15%" align="right" onclick="st1.sort(6);st2.sort(7);">'.$MY_MESSAGES['datemodified'].'</td>
				<td width="8%" align="center"> &nbsp;</td>
			</tr>
		</thead>
		<tbody style="display: none;">
			<tr>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
		</tbody>
	</table>
	';
	$t_folders = '<table class="sort-table" id="tableFolders" cellspacing="0" width="100%">
		<col />
		<col />
		<col style="text-align: right" />
		<col style="text-align: right" />
		<col />
		<col />
		<col />
		<col />
		<thead style="display: none;">
			<tr>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
		</thead>
		<tbody>
	';
		$t_files='<table class="sort-table" id="tableFiles" cellspacing="0" width="100%">
		<col />
		<col />
		<col style="text-align: right" />
		<col style="text-align: right" />
		<col />
		<col />
		<col />
		<col />
		<thead style="display: none;">
			<tr>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
		</thead>
		<tbody>';


		$entries_cnt = 0;
		while (false !== ($entry = $d->read())) {
			if(substr($entry,0,1) != '.') {
				$relativePath = $MY_PATH.$entry;
				$absolutePath = $MY_DOCUMENT_ROOT.$relativePath;
				if (is_dir($absolutePath)) {
					$entries_cnt++;
					$time = filemtime($absolutePath);
					$t_folders .= '<tr >
					<td width="7%"><img src="images/ext/folder_small.gif" width="16" height="16" border=0 alt="'.$entry.'" /></td>
					<td width="50%"><div style="width:200px; overflow:hidden;"><a href="files.php?dialogname='.$MY_NAME.'&path='.$relativePath.'" onClick="changeLoadingStatus(\'load\')">'.$entry.'</a></div></td>
					<td width="10%" align="right">'.$MY_MESSAGES['folder'].'</td>
					<td width="15%" align="right">'.parse_time($time).'</td>
					<td width="8%" align="center">&nbsp;</td>
					<td width="0px" style="display: none;">'.$MY_MESSAGES['folder'].'</td>
					<td width="0px" style="display: none;">'.$time.'</td>
					</tr>';
				} else {
					$entries_cnt++;
					$ext = substr(strrchr($entry, '.'), 1);
					if (is_array($MY_LIST_EXTENSIONS)) {
							if (!in_array($ext, $MY_LIST_EXTENSIONS)) continue;
					}
					$size = filesize($absolutePath);
					$time = filemtime($absolutePath);
					$parsed_size = parse_size($size);
					$parsed_time = parse_time($time);
					$parsed_icon ='/plugins/editors/tmedit/popups/InsertFile/images/ext/'.parse_icon($ext);

					$t_files .= '<tr>
					<td width="7%"><img src="'.$parsed_icon.'" width="16" height="16" border=0 alt="'.$entry.'" /></td>
					<td width="50%"><div style="width:200px; overflow:hidden;"><a href="javascript:;" onClick="javascript:fileSelected(\''.$MY_BASE_URL.$relativePath.'\',\''.$entry.'\',\''.$parsed_icon.'\',\''.$parsed_size.'\',\''.$parsed_time.'\');">'.$entry.'</div></td>
					<td width="10%" align="right">'.$parsed_size.'</td>
					<td width="15%" align="right">'.$parsed_time.'</td>
					<td width="8%" align="center">&nbsp;</td>
					<td width="0px" style="display: none;">'.$ext.'</td>
					<td width="0px" style="display: none;">'.$size.'</td>
					<td width="0px" style="display: none;">'.$time.'</td>
					</tr>';
				}
			}
		}
		$d->close();


		$t_folders .= '</tbody> </table>';
		$t_files .= '</tbody> </table>';


		if ($entries_cnt) {
			echo $t_header."\n<div style=\"height:127px; overflow: auto;\">".$t_folders."\n".$t_files."</div>";
	?>
	<script type="text/javascript">
	var st = new SortableTable(document.getElementById("tableHeader"), ["CaseInsensitiveString", "CaseInsensitiveString", "Number", "Number", "None"]);
	var st1 = new SortableTable(document.getElementById("tableFolders"), ["None", "CaseInsensitiveString", "None", "None", "None", "CaseInsensitiveString", "Number", "Number"]);
	var st2 = new SortableTable(document.getElementById("tableFiles"), ["None", "CaseInsensitiveString", "None", "None", "None", "CaseInsensitiveString", "Number", "Number"]);
	//st2.sort(5);

	</script>

	<?php
		} else {
		draw_no_results();
		}
	}
	else
	{
	 draw_no_dir();
	}

	?>

	<script language="JavaScript" type="text/JavaScript">
	MM_showHideLayers('loading','','hide')
	</script>
	</body>
	</html>