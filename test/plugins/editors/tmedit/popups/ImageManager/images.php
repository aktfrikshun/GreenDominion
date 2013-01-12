<?php
// $Id: images.php, v 1.4 2009/06/23 17:17:45 bpfeifer Exp $
/**
* TMEdit ImageManager
* @ package TMEdit
* @ Copyright © 2004-2009 Bernhard Pfeifer - www.xhtmlsuite.com
* @ All rights reserved
* @ Released under the GNU/GPL http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
* @version $Revision: 1.4 $
**/
	include 'config.inc.php';

	if(isset($_GET['dir'])) {
		$dirParam = $_GET['dir'];

		if(strlen($dirParam) > 0)
		{
			if(substr($dirParam,0,1)=='/')
				$IMG_ROOT .= $dirParam;
			else
				$IMG_ROOT = $dirParam;
		}
	}

	$refresh_dirs = false;

	if(strrpos($IMG_ROOT, '/')!= strlen($IMG_ROOT)-1)
		$IMG_ROOT .= '/';


	function num_files($dir)
	{
		$total = 0;

		if(is_dir($dir))
		{
			$d = @dir($dir);

			while (false !== ($entry = $d->read()))
			{
				//echo $entry."<br>";
				if(substr($entry,0,1) != '.') {
					$total++;
				}
			}
			$d->close();
		}
		return $total;
	}

	function dirs($dir,$abs_path)
	{
		$d = dir($dir);
			//echo "Handle: ".$d->handle."<br>\n";
			//echo "Path: ".$d->path."<br>\n";
			$dirs = array();
			while (false !== ($entry = $d->read())) {
				if(is_dir($dir.'/'.$entry) && substr($entry,0,1) != '.')
				{
					//dirs($dir.'/'.$entry, $prefix.$prefix);
					//echo $prefix.$entry."<br>\n";
					$path['path'] = $dir.'/'.$entry;
					$path['name'] = $entry;
					$dirs[$entry] = $path;
				}
			}
			$d->close();

			ksort($dirs);
			for($i=0; $i<count($dirs); $i++)
			{
				$name = key($dirs);
				$current_dir = $abs_path.'/'.$dirs[$name]['name'];
				echo ", \"$current_dir\"\n";
				dirs($dirs[$name]['path'],$current_dir);
				next($dirs);
			}
	}

	function parse_size($size)
	{
		if($size < 1024)
			return $size.' Bytes';
		else if($size >= 1024 && $size < 1024*1024)
		{
			return sprintf('%01.2f',$size/1024.0).' KB';
		}
		else
		{
			return sprintf('%01.2f',$size/(1024.0*1024)).' MB';
		}
	}

	function show_image($img, $file, $info, $size)
	{
		global $BASE_DIR, $BASE_URL, $newPath;

		$img_path = dir_name($img);
		$img_file = basename($img);

		$img_url = $BASE_URL.$img_path.'/'.$img_file;

		$filesize = parse_size($size);

	?>
	<td>
	<table width="102" border="0" cellpadding="0" cellspacing="2">
	  <tr>
	    <td align="center" class="imgBorder" onMouseOver="pviiClassNew(this,'imgBorderHover')" onMouseOut="pviiClassNew(this,'imgBorder')">
		<a href="javascript:;" onClick="javascript:imageSelected('<?php echo $img_url; ?>', <?php echo $info[0];?>, <?php echo $info[1]; ?>,'<?php echo $file; ?>');"><img src="<?php echo $img_url; ?>" height="80" alt="<?php echo $file; ?> - <?php echo $filesize; ?>" border="0"></a></td>
	  </tr>
	</table>
	</td>
	<?php
	}

	function show_dir($path, $dir)
	{
		global $newPath, $BASE_DIR, $BASE_URL;

		$num_files = num_files($BASE_DIR.$path);
	?>
	<td>
	<table width="102" border="0" cellpadding="0" cellspacing="2">
	  <tr>
	    <td align="center" class="imgBorder" onMouseOver="pviiClassNew(this,'imgBorderHover')" onMouseOut="pviiClassNew(this,'imgBorder')">
		  <a href="images.php?dir=<?php echo $path; ?>" onClick="changeLoadingStatus('load')">
		  <img src="folder.gif" width="75" height="75" border=0 alt="<?php echo $dir; ?>">
		  </a>
		</td>
	  </tr>
	  <tr>
	    <td><table width="100%" border="0" cellspacing="1" cellpadding="2">
	        <tr>
	          <td width="99%" class="info"><?php echo $dir; ?></td>
	        </tr>
	      </table></td>
	  </tr>
	</table>
	</td>
	<?
	}

	function draw_no_results()
	{
	?>
	<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
	  <tr>
	    <td><div align="center" style="font-size:large;font-weight:bold;color:#CCCCCC;font-family: Helvetica, sans-serif;">No files</div></td>
	  </tr>
	</table>
	<?
	}

	function draw_no_dir()
	{
		global $BASE_DIR, $BASE_ROOT;
	?>
	<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
	  <tr>
	    <td><div align="center" style="font-size:small;font-weight:bold;color:#CC0000;font-family: Helvetica, sans-serif;">Configuration problem: &quot;<?php echo $BASE_DIR.$BASE_ROOT; ?>&quot; does not exist.</div></td>
	  </tr>
	</table>
	<?
	}


	function draw_table_header()
	{
		echo '<table border="0" cellpadding="0" cellspacing="2">';
		echo '<tr>';
	}

	function draw_table_footer()
	{
		echo '</tr>';
		echo '</table>';
	}

	?>
	<html>
	<head>
	<title>Image Browser</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<style type="text/css">
	<!--
	.imgBorder {
		height: 96px;
		border: 1px solid threedface;
		vertical-align: middle;
	}
	.imgBorderHover {
		height: 96px;
		border: 1px solid threedshadow;
		vertical-align: middle;
		background: #EEE;
		cursor: hand;
	}

	.buttonHover {
		border: 1px solid;
		border-color: ButtonHighlight ButtonShadow ButtonShadow ButtonHighlight;
		cursor: hand;
		background: #EEE;
	}
	.buttonOut
	{
	 border: 1px solid;
	 border-color: white;
	}

	.imgCaption {
		font-size: 10pt;
		font-family: Arial, Helvetica, sans-serif;
		text-align: center;
	}
	.dirField {
		font-size: 10pt;
		font-family: Arial, Helvetica, sans-serif;
		width:110px;
	}

	.info {
		font-size: 8.5pt;
		font-family: Arial, Helvetica, sans-serif;
		text-align: center;
	}


	-->
	</style>
	<?php
		$dirPath = eregi_replace($BASE_ROOT,'',$IMG_ROOT);

		$paths = explode('/', $dirPath);
		$upDirPath = '/';
		for($i=0; $i<count($paths)-2; $i++)
		{
			$path = $paths[$i];
			if(strlen($path) > 0)
			{
				$upDirPath .= $path.'/';
			}
		}

		$slashIndex = strlen($dirPath);
		$newPath = $dirPath;
		if($slashIndex > 1 && substr($dirPath, $slashIndex-1, $slashIndex) == '/')
		{
			$newPath = substr($dirPath, 0,$slashIndex-1);
		}
	?>
	<script type="text/javascript" src="../popup.js"></script>
	<script type="text/javascript" src="../../dialog.js"></script>
	<script language="JavaScript" type="text/JavaScript">
	<!--
	function pviiClassNew(obj, new_style) { //v2.6 by PVII
	  obj.className=new_style;
	}

	function goUp()
	{
		location.href = "ImageManager/images.php?dir=<?php echo $upDirPath; ?>";
	}

	function changeDir(newDir)
	{
		location.href = "ImageManager/images.php?dir="+newDir;
	}

	function updateDir()
	{
		var newPath = "<?php echo $newPath; ?>";
		if(window.top.document.forms[0] != null) {

		var allPaths = window.top.document.forms[0].dirPath.options;
		for(i=0; i<allPaths.length; i++)
		{
			//alert(allPaths.item(i).value);
			allPaths.item(i).selected = false;
			if((allPaths.item(i).value)==newPath)
			{
				allPaths.item(i).selected = true;
			}
		}

		}

	}

	<?php if ($refresh_dirs) { ?>
	function refreshDirs()
	{
		var allPaths = window.top.document.forms[0].dirPath.options;
		var fields = ["/" <?php dirs($BASE_DIR.$BASE_ROOT,'');?>];

		var newPath = "<?php echo $newPath; ?>";

		while(allPaths.length > 0)
		{
			for(i=0; i<allPaths.length; i++)
			{
				allPaths[i]=null;
			}
		}

		for(i=0; i<fields.length; i++)
		{
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
	<?php } ?>

	function imageSelected(filename, width, height, alt)
	{
		var topDoc = window.top.document.forms[0];
		topDoc.f_url.value = filename;
		topDoc.f_width.value= width;
		topDoc.f_height.value = height;
		topDoc.f_alt.value = "";
		topDoc.orginal_width.value = width;
		topDoc.orginal_height.value = height;

	}

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

	function changeLoadingStatus(state)
	{
		var statusText = null;
		if(state == 'load') {
			statusText = 'Loading images...';
		}
		if(statusText != null) {
			var obj = MM_findObj('loadingStatus', window.top.document);
			//alert(obj.innerHTML);
			if (obj != null && obj.innerHTML != null)
				obj.innerHTML = statusText;
			MM_showHideLayers('loading','','show')
		}
	}

	//-->
	</script>
	</head>
	<body onLoad="updateDir();" bgcolor="#FFFFFF">

	<?php
	//var_dump($_GET);
	//echo $dirParam.':'.$upDirPath;
	//echo '<br>';
	$d = @dir($BASE_DIR.$IMG_ROOT);

	if($d)
	{
		//var_dump($d);
		$images = array();
		$folders = array();
		while (false !== ($entry = $d->read()))
		{
			$img_file = $IMG_ROOT.$entry;

			if(is_file($BASE_DIR.$img_file) && substr($entry,0,1) != '.')
			{
				$image_info = @getimagesize($BASE_DIR.$img_file);
				if(is_array($image_info))
				{
					$file_details['file'] = $img_file;
					$file_details['img_info'] = $image_info;
					$file_details['size'] = filesize($BASE_DIR.$img_file);
					$images[$entry] = $file_details;
					//show_image($img_file, $entry, $image_info);
				}
			}
			else if(is_dir($BASE_DIR.$img_file) && substr($entry,0,1) != '.')
			{
				$folders[$entry] = $img_file;
				//show_dir($img_file, $entry);
			}
		}
		$d->close();

		if(count($images) > 0 || count($folders) > 0)
		{
			//now sort the folders and images by name.
			ksort($images);
			ksort($folders);

			draw_table_header();

			for($i=0; $i<count($folders); $i++)
			{
				$folder_name = key($folders);
				show_dir($folders[$folder_name], $folder_name);
				next($folders);
			}
			for($i=0; $i<count($images); $i++)
			{
				$image_name = key($images);
				show_image($images[$image_name]['file'], $image_name, $images[$image_name]['img_info'], $images[$image_name]['size']);
				next($images);
			}
			draw_table_footer();
		}
		else
		{
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