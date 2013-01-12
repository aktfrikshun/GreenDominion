// $Id: tmedit.js, v 1.2 2009/06/23 14:44:22 bpfeifer Exp $
/**
* TMEdit Core File
* @ package TMEdit
* @ Copyright © 2004-2009 Bernhard Pfeifer - www.xhtmlsuite.com
* @ All rights reserved
* @ Released under the GNU/GPL http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
* @version $Revision: 1.2 $
**/

if (typeof _editor_url == "string") {
	_editor_url = _editor_url.replace(/\x2f*$/, '/');
} else {
	alert("WARNING: _editor_url is not set!	You should set this variable to the editor files path; it should preferably be an absolute path, like in '/tmedit', but it can be relative if you prefer.	Further we will try to load the editor files correctly but we'll probably fail.");
	_editor_url = '';
}
_editor_lang = "en";
function TMEdit(textarea, config) {
	if (TMEdit.checkSupportedBrowser()) {
		if (typeof config == "undefined") {
			this.config = new TMEdit.Config();
		} else {
			this.config = config;
		}
		this._tmEdit = null;
		this._textArea = textarea;
		this._editMode = "wysiwyg";
		this.plugins = {};
		this._timerToolbar = null;
		this._timerUndo = null;
		this._undoQueue = [this.config.undoSteps];
		this._undoPos = -1;
		this._customUndo = true;
		this._mdoc = document;
		this.doctype = '';
	}
};

TMEdit._scripts = [];
TMEdit.RE_tagName = /(<\/|<)\s*([^ \t\n>]+)/ig;
TMEdit.RE_doctype = /(<!doctype((.|\n)*?)>)\n?/i;
TMEdit.RE_head = /<head>((.|\n)*?)<\/head>/i;
TMEdit.RE_body = /<body>((.|\n)*?)<\/body>/i;
TMEdit.Config = function() {
	this.version = "TMEdit 1.2";
	this.width = "auto";
	this.height = "auto";
	this.statusBar = true;
	this.undoSteps = 10;
	this.undoTimeout = 500;
	this.sizeIncludesToolbar = false;
	this.pageStyle = '';
	this.killWordOnPaste = false;
	this.baseURL = _editor_base_url;
	this.imgURL = "images/";
	this.popupURL = "popups/";
	this.toolbar = [];
	this.fontname = {
		"&mdash; Font &mdash;": '',
		"Arial": 'arial,helvetica,sans-serif',
		"Courier New": 'courier new,courier,monospace',
		"Impact": 'impact',
		"Georgia": 'georgia,times new roman,times,serif',
		"Tahoma": 'tahoma,arial,helvetica,sans-serif',
		"Times New Roman": 'times new roman,times,serif',
		"Trebuchet": 'trebuchet, trebuchet ms',
		"Verdana": 'verdana,arial,helvetica,sans-serif',
		"WingDings": 'wingdings'
	};

	this.fontsize = {
		"&mdash; Size &mdash;": "",
		"1 (8 pt)": "1",
		"2 (10 pt)": "2",
		"3 (12 pt)": "3",
		"4 (14 pt)": "4",
		"5 (18 pt)": "5",
		"6 (24 pt)": "6",
		"7 (36 pt)": "7"
	};

	this.formatblock = {
		"&mdash; Format &mdash;": "",
		"Heading 1": "h1",
		"Heading 2": "h2",
		"Heading 3": "h3",
		"Heading 4": "h4",
		"Heading 5": "h5",
		"Heading 6": "h6",
		"Normal": "p",
		"Address": "address",
		"Formatted": "pre"
	};

	this.customSelects = {};

	function cut_copy_paste(e, cmd, obj) {
		e.execCommand(cmd);
	};

	this.btnList = {
		bold: ["Bold", "ed_format_bold.gif", false,
		function(e) {
			e.execCommand("bold");
		}],
		italic: ["Italic", "ed_format_italic.gif", false,
		function(e) {
			e.execCommand("italic");
		}],
		underline: ["Underline", "ed_format_underline.gif", false,
		function(e) {
			e.execCommand("underline");
		}],
		strikethrough: ["Strikethrough", "ed_format_strike.gif", false,
		function(e) {
			e.execCommand("strikethrough");
		}],
		subscript: ["Subscript", "ed_format_sub.gif", false,
		function(e) {
			e.execCommand("subscript");
		}],
		superscript: ["Superscript", "ed_format_sup.gif", false,
		function(e) {
			e.execCommand("superscript");
		}],
		justifyleft: ["Justify Left", "ed_align_left.gif", false,
		function(e) {
			e.execCommand("justifyleft");
		}],
		justifycenter: ["Justify Center", "ed_align_center.gif", false,
		function(e) {
			e.execCommand("justifycenter");
		}],
		justifyright: ["Justify Right", "ed_align_right.gif", false,
		function(e) {
			e.execCommand("justifyright");
		}],
		justifyfull: ["Justify Full", "ed_align_justify.gif", false,
		function(e) {
			e.execCommand("justifyfull");
		}],
		insertorderedlist: ["Ordered List", "ed_list_num.gif", false,
		function(e) {
			e.execCommand("insertorderedlist");
		}],
		insertunorderedlist: ["Bulleted List", "ed_list_bullet.gif", false,
		function(e) {
			e.execCommand("insertunorderedlist");
		}],
		outdent: ["Decrease Indent", "ed_indent_less.gif", false,
		function(e) {
			e.execCommand("outdent");
		}],
		indent: ["Increase Indent", "ed_indent_more.gif", false,
		function(e) {
			e.execCommand("indent");
		}],
		forecolor: ["Font Color", "ed_color_fg.gif", false,
		function(e) {
			e.execCommand("forecolor");
		}],
		hilitecolor: ["Background Color", "ed_color_bg.gif", false,
		function(e) {
			e.execCommand("hilitecolor");
		}],
		inserthorizontalrule: ["Horizontal Rule", "ed_hr.gif", false,
		function(e) {
			e.execCommand("inserthorizontalrule");
		}],
		createlink: ["Insert/Edit Web Link", "ed_link.gif", false,
		function(e) {
			e.execCommand("createlink", true);
		}],
		insertimage: ["Insert Image", "ed_insert_image.gif", false,
		function(e) {
			e.execCommand("insertimage");
		}],
		insertcharacter: ["Insert special character", "ed_charmap.gif", false,
		function(e) {
			e.execCommand("insertcharacter");
		}],
		inserttable: ["Insert Table", "insert_table.gif", false,
		function(e) {
			e.execCommand("inserttable");
		}],
		toggleborders: ["Show/hide table borders when 0", "ed_show_0_border.gif", false,
		function(e) {
			e.execCommand("toggleborders");
		},
		"table"],
		insertfile: ["Insert File", "ed_linktofile.gif", false,
		function(e) {
			e.execCommand("insertfile");
		}],
		htmlmode: ["Toggle HTML Source", "ed_html.gif", true,
		function(e) {
			e.execCommand("htmlmode");
		}],
		showhelp: ["Help using editor", "ed_help.gif", true,
		function(e) {
			e.execCommand("showhelp");
		}],
		undo: ["Undoes your last action", "ed_undo.gif", false,
		function(e) {
			e.execCommand("undo");
		}],
		redo: ["Redoes your last action", "ed_redo.gif", false,
		function(e) {
			e.execCommand("redo");
		}],
		removeformat: ["Remove format", "ed_remove_format.gif", false,
		function(e) {
			e.execCommand("RemoveFormat");
		}],
		cut: ["Cut selection", "ed_cut.gif", false, cut_copy_paste],
		copy: ["Copy selection", "ed_copy.gif", false, cut_copy_paste],
		paste: ["Paste from clipboard", "ed_paste.gif", false, cut_copy_paste],
		preview: ["Preview", "ed_preview.gif", true,
		function(e) {
			e.execCommand("preview");
		}],
		lefttoright: ["Direction left to right", "ed_left_to_right.gif", false,
		function(e) {
			e.execCommand("lefttoright");
		}],
		righttoleft: ["Direction right to left", "ed_right_to_left.gif", false,
		function(e) {
			e.execCommand("righttoleft");
		}]
	};

	for (var i in this.btnList) {
		var btn = this.btnList[i];
		if (typeof btn != 'object') {
			continue;
		}
		btn[1] = _editor_url + this.imgURL + btn[1];

		if (typeof TMEdit.I18N.tooltips[i] != "undefined") {
			btn[0] = TMEdit.I18N.tooltips[i];
		}
	}
};

TMEdit.Config.prototype.registerButton = function(id, tooltip, image, textMode, action, context) {
	var the_id;

	if (typeof id == "string") {
		the_id = id;
	} else if (typeof id == "object") {
		the_id = id.id;
	} else {
		return false;
	}

	switch (typeof id) {
	case "string":
		this.btnList[id] = [
		tooltip, image, textMode, action, context];

		break;

	case "object":
		this.btnList[id.id] = [
		id.tooltip, id.image, id.textMode, id.action, id.context];

		break;
	}
};

TMEdit.Config.prototype.registerDropdown = function(object) {
	this.customSelects[object.id] = object;
};

TMEdit.Config.prototype.hideSomeButtons = function(remove) {
	var toolbar = this.toolbar;

	for (var i in toolbar) {
		var line = toolbar[i];
		for (var j = line.length; --j >= 0;) {
			if (remove.indexOf(" " + line[j] + " ") >= 0) {
				var len = 1;

				if (/separator|space/.test(line[j + 1])) {
					len = 2;
				}
				line.splice(j, len);
			}
		}
	}
};

TMEdit.replaceAll = function(config) {
	var tas = document.getElementsByTagName("textarea");

	for (var i = tas.length; i > 0; (new TMEdit(tas[--i], config)).generate());
};

TMEdit.replace = function(id, config) {
	var ta = TMEdit.getElementById("textarea", id);
	return ta ? (new TMEdit(ta, config)).generate() : null;
};

TMEdit.prototype._createToolbar = function() {
	var editor = this;
	var toolbar = document.createElement("div");
	this._toolbar = toolbar;
	toolbar.className = "toolbar";
	toolbar.unselectable = "1";
	var tb_row = null;
	var tb_objects = new Object();
	this._toolbarObjects = tb_objects;
	function newLine() {
		var table = document.createElement("table");
		table.border = "0px";
		table.cellSpacing = "0px";
		table.cellPadding = "0px";
		table.style.padding = "0px";
		table.className = "tmediteditor";
		toolbar.appendChild(table);
		var tb_body = document.createElement("tbody");
		tb_body.className = "tmediteditor";
		table.appendChild(tb_body);
		tb_row = document.createElement("tr");
		tb_row.className = "tmediteditor";
		tb_row.style.padding = "0px";
		tb_body.appendChild(tb_row);
	};
	newLine();
	function setButtonStatus(id, newval) {
		var oldval = this[id];
		var el = this.element;

		if (oldval != newval) {
			switch (id) {
			case "enabled":
				if (newval) {
					TMEdit._removeClass(el, "buttonDisabled");
					el.disabled = false;
				} else {
					TMEdit._addClass(el, "buttonDisabled");
					el.disabled = true;
				}
				break;

			case "active":
				if (newval) {
					TMEdit._addClass(el, "buttonPressed");
				} else {
					TMEdit._removeClass(el, "buttonPressed");
				}
				break;
			}
			this[id] = newval;
		}
	};
	function createSelect(txt) {
		var options = null;
		var el = null;
		var cmd = null;
		var customSelects = editor.config.customSelects;
		var context = null;
		var tooltip = "";

		switch (txt) {
		case "fontsize":
		case "fontname":
		case "formatblock":
			options = editor.config[txt];
			cmd = txt;
			break;

		default:
			cmd = txt;
			var dropdown = customSelects[cmd];

			if (typeof dropdown != "undefined") {
				options = dropdown.options;
				context = dropdown.context;

				if (typeof dropdown.tooltip != "undefined") {
					tooltip = dropdown.tooltip;
				}
			}
			break;
		}

		if (options) {
			el = document.createElement("select");
			el.title = tooltip;
			var obj = {
				name: txt,
				element: el,
				enabled: true,
				text: false,
				cmd: cmd,
				state: setButtonStatus,
				context: context
			};

			tb_objects[txt] = obj;

			for (var i in options) {
				if (typeof(options[i]) != 'string') {
					continue;
				}
				var op = document.createElement("option");
				op.innerHTML = i;
				op.value = options[i];
				el.appendChild(op);
			}
			TMEdit._addEvent(el, "change",
			function() {
				editor._comboSelected(el, txt);
			});
		}
		return el;
	};
	function createButton(txt) {
		var el = null;
		var btn = null;

		switch (txt) {
		case "separator":
			el = document.createElement("div");
			el.className = "separator";
			break;

		case "space":
			el = document.createElement("div");
			el.className = "space";
			break;

		case "linebreak":
			newLine();
			return false;

		case "textindicator":
			el = document.createElement("div");
			el.appendChild(document.createTextNode("A"));
			el.className = "indicator";
			el.title = TMEdit.I18N.tooltips.textindicator;
			var obj = {
				name: txt,
				element: el,
				enabled: true,
				active: false,
				text: false,
				cmd: "textindicator",
				state: setButtonStatus
			};

			tb_objects[txt] = obj;
			break;

		default:
			btn = editor.config.btnList[txt];
		}

		if (!el && btn) {
			el = document.createElement("div");
			el.title = btn[0];
			el.className = "button";
			var obj = {
				name: txt,
				element: el,
				enabled: true,
				active: false,
				text: btn[2],
				cmd: btn[3],
				state: setButtonStatus,
				context: btn[4] || null
			};

			tb_objects[txt] = obj;
			TMEdit._addEvent(el, "mouseover",
			function() {
				if (obj.enabled) {
					TMEdit._addClass(el, "buttonHover");
				}
			});
			TMEdit._addEvent(el, "mouseout",
			function() {
				if (obj.enabled) with(TMEdit) {
					_removeClass(el, "buttonHover");
					_removeClass(el, "buttonActive"); (obj.active) && _addClass(el, "buttonPressed");
				}
			});
			TMEdit._addEvent(el, "mousedown",
			function(ev) {
				if (obj.enabled) with(TMEdit) {
					_addClass(el, "buttonActive");
					_removeClass(el, "buttonPressed");
					_stopEvent(is_ie ? window.event: ev);
				}
			});
			TMEdit._addEvent(el, "click",
			function(ev) {
				if (obj.enabled) with(TMEdit) {
					_removeClass(el, "buttonActive");
					_removeClass(el, "buttonHover");
					obj.cmd(editor, obj.name, obj);
					_stopEvent(is_ie ? window.event: ev);
				}
			});
			var img = document.createElement("img");
			img.src = btn[1];
			img.style.width = "18px";
			img.style.height = "18px";
			el.appendChild(img);
		} else if (!el) {
			el = createSelect(txt);
		}

		if (el) {
			var tb_cell = document.createElement("td");
			tb_row.appendChild(tb_cell);
			tb_cell.appendChild(el);
		}
		return el;
	};
	var first = true;

	for (var i = 0; i < this.config.toolbar.length; ++i) {
		if (typeof this.config.toolbar[i] != 'string') {
			//	continue;
		}
		if (!first) {
			createButton("linebreak");
		} else {
			first = false;
		}
		var group = this.config.toolbar[i];

		for (var j in group) {
			if (typeof group[j] != 'string') {
				continue;
			}
			var code = group[j];

			if (/^([IT])\[(.*?)\]/.test(code)) {
				var l7ed = RegExp.$1 == "I";
				var label = RegExp.$2;

				if (l7ed) {
					label = TMEdit.I18N.custom[label];
				}
				var tb_cell = document.createElement("td");
				tb_row.appendChild(tb_cell);
				tb_cell.className = "label";
				tb_cell.innerHTML = label;
			} else {
				createButton(code);
			}
		}
	}
	this._tmEdit.appendChild(toolbar);
};

TMEdit.prototype._createStatusBar = function() {
	var statusbar = document.createElement("div");
	statusbar.className = "statusBar";
	this._tmEdit.appendChild(statusbar);
	this._statusBar = statusbar;
	div = document.createElement("span");
	div.className = "statusBarTree";
	div.innerHTML = TMEdit.I18N.msg["Path"] + ": ";
	this._statusBarTree = div;
	this._statusBar.appendChild(div);

	if (!this.config.statusBar) {
		statusbar.style.display = "none";
	}
};

TMEdit.prototype.generate = function() {
	var editor = this;
	var textarea = this._textArea;

	if (typeof textarea == "string") {
		this._textArea = textarea = TMEdit.getElementById("textarea", textarea);
	}
	this._ta_size = {
		w: textarea.offsetWidth,
		h: textarea.offsetHeight
	};

	textarea.style.display = "none";
	var tmedit = document.createElement("div");
	tmedit.className = "tmedit";
	this._tmEdit = tmedit;
	textarea.parentNode.insertBefore(tmedit, textarea);

	if (textarea.form) {
		var f = textarea.form;
		f.onsubmit = function() {
			editor._textArea.value = editor.getHTML();
		};
	}
	this._createToolbar();
	var iframe = document.createElement("iframe");
	iframe.frameBorder = '0';
	tmedit.appendChild(iframe);
	this._iframe = iframe;
	this._createStatusBar();

	if (!TMEdit.is_ie) {
		iframe.style.borderWidth = "1px";
	}
	var height = (this.config.height == "auto" ? (this._ta_size.h + "px") : this.config.height);
	height = parseInt(height);
	var width = (this.config.width == "auto" ? (this._ta_size.w + "px") : this.config.width);
	width = parseInt(width);

	iframe.style.width = width + "px";

	if (this.config.sizeIncludesToolbar) {
		height -= this._toolbar.offsetHeight;
		height -= this._statusBar.offsetHeight;
	}

	if (height < 0) {
		height = 0;
	}
	iframe.style.height = height + "px";
	textarea.style.width = '633px';
	textarea.style.height = iframe.style.height;
	function initIframe() {
		var doc = editor._iframe.contentWindow.document;

		if (!doc) {
			if (TMEdit.is_gecko) {
				setTimeout(initIframe, 100);
				return false;
			} else {
				alert("ERROR: IFRAME can't be initialized.");
			}
		}

		if (TMEdit.is_gecko) {
			doc.designMode = "on";
		}
		editor._doc = doc;

		doc.open();
		var html = "<html>\n";
		html += "<head>\n";
		html += '<base href="' + _editor_base_url + '" />\n';
		html += "<style>" + editor.config.pageStyle + " html,body { border: 0px; margin: 4px} </style>\n";
		html += "<style title=\"table borders\">" + ".htmtableborders, .htmtableborders td, .htmtableborders th {border : 1px dashed lightgrey ! important;} \n" + "</style>\n";
		html += "</head>\n";
		html += "<body>\n";
		html += editor._textArea.value;
		html += "</body>\n";
		html += "</html>";
		doc.write(html);
		doc.close();

		if (TMEdit.is_ie) {
			doc.body.contentEditable = true;
		}
		editor.focusEditor();
		TMEdit._addEvents(doc, ["keydown", "keypress", "mousedown", "mouseup", "drag"],
		function(event) {
			return editor._editorEvent(TMEdit.is_ie ? editor._iframe.contentWindow.event: event);
		});

		for (var i in editor.plugins) {
			var plugin = editor.plugins[i].instance;

			if (typeof plugin.onGenerate == "function") plugin.onGenerate();
		}
		setTimeout(function() {
			editor.updateToolbar();
		},
		250);

		if (typeof editor.onGenerate == "function") editor.onGenerate();
	};
	setTimeout(initIframe, 100);
	editor.focusEditor();
};

TMEdit.prototype.setMode = function(mode) {
	if (typeof mode == "undefined") {
		mode = ((this._editMode == "textmode") ? "wysiwyg": "textmode");
	}

	switch (mode) {
	case "textmode":
		this._textArea.value = this.getHTML();
		this._iframe.style.display = "none";
		this._textArea.style.display = "";

		if (this.config.statusBar) {
			this._statusBar.innerHTML = TMEdit.I18N.msg["TEXT_MODE"];
		}
		break;

	case "wysiwyg":
		if (TMEdit.is_gecko) {
			try {
				this._doc.designMode = "off";
			}
			catch(e) {};
		}

		this._doc.body.innerHTML = this.getHTML();
		this._iframe.style.display = "";
		this._textArea.style.display = "none";

		if (TMEdit.is_gecko) {
			try {
				this._doc.designMode = "on";
			}
			catch(e) {};
		}

		if (this.config.statusBar) {
			this._statusBar.innerHTML = '';
			this._statusBar.appendChild(this._statusBarTree);
		}
		break;

	default:
		alert("Mode <" + mode + "> not defined!");
		return false;
	}
	this._editMode = mode;
	this.focusEditor();
};

TMEdit.prototype.registerPlugin2 = function(plugin, args) {
	if (typeof plugin == "string") plugin = eval(plugin);
	var obj = new plugin(this, args);

	if (obj) {
		var clone = {};

		var info = plugin._pluginInfo;

		for (var i in info) {
			if (typeof info[i] != 'string') {
				continue;
			}
			clone[i] = info[i];
		}
		clone.instance = obj;
		clone.args = args;
		this.plugins[plugin._pluginInfo.name] = clone;
	}
};

TMEdit.prototype.registerPlugin = function() {
	var plugin = arguments[0];
	var args = [];

	for (var i = 1; i < arguments.length; ++i)
	args.push(arguments[i]);
	this.registerPlugin2(plugin, args);
};

TMEdit.loadPlugin = function(pluginName) {
	var dir = _editor_url + "plugins/" + pluginName;
	var plugin = pluginName.replace(/([a-z])([A-Z])([a-z])/g,
	function(str, l1, l2, l3) {
		return l1 + "-" + l2.toLowerCase() + l3;
	}).toLowerCase() + ".js";
	var plugin_file = dir + "/" + plugin;
	var plugin_lang = dir + "/lang/" + TMEdit.I18N.lang + ".js";
	TMEdit._scripts.push(plugin_file, plugin_lang);
	document.write("<script type='text/javascript' src='" + plugin_file + "'></script>");
	document.write("<script type='text/javascript' src='" + plugin_lang + "'></script>");
};

TMEdit.loadStyle = function(style, plugin) {
	var url = _editor_url || '';

	if (typeof plugin != "undefined") {
		url += "plugins/" + plugin + "/";
	}
	url += style;
	document.write("<style type='text/css'>@import url(" + url + ");</style>");
};

TMEdit.loadStyle("tmedit.css");
TMEdit.prototype._wordClean = function() {
	var D = this.getInnerHTML();

	if (D.indexOf('class=Mso') >= 0) {
		D = D.replace(/\r\n/g, ' ').replace(/\n/g, ' ').replace(/\r/g, ' ').replace(/\&nbsp\;/g, ' ');
		D = D.replace(/ class=[^\s|>]*/gi, '').replace(/ style=\"[^>]*\"/gi, '').replace(/ align=[^\s|>]*/gi, '');
		D = D.replace(/<b [^>]*>/gi, '<b>').replace(/<i [^>]*>/gi, '<i>').replace(/<li [^>]*>/gi, '<li>').replace(/<ul [^>]*>/gi, '<ul>');
		D = D.replace(/<b>/gi, '<strong>').replace(/<\/b>/gi, '</strong>');
		D = D.replace(/<em>/gi, '<i>').replace(/<\/em>/gi, '</i>');
		D = D.replace(/<\?xml:[^>]*>/g, '').replace(/<\/?st1:[^>]*>/g, '').replace(/<\/?[a-z]\:[^>]*>/g, '').replace(/<\/?font[^>]*>/gi, '').replace(/<\/?span[^>]*>/gi, ' ').replace(/<\/?div[^>]*>/gi, ' ').replace(/<\/?pre[^>]*>/gi, ' ').replace(/<\/?h[1-6][^>]*>/gi, ' ');
		oldlen = D.length + 1;

		while (oldlen > D.length) {
			oldlen = D.length;
			D = D.replace(/<([a-z][a-z]*)> *<\/\1>/gi, ' ').replace(/<([a-z][a-z]*)> *<([a-z][^>]*)> *<\/\1>/gi, '<$2>');
		}
		D = D.replace(/<([a-z][a-z]*)><\1>/gi, '<$1>').replace(/<\/([a-z][a-z]*)><\/\1>/gi, '<\/$1>');
		D = D.replace(/	*/gi, ' ');
		this.setHTML(D);
		this.updateToolbar();
	}
};

TMEdit.prototype.forceRedraw = function() {
	this._doc.body.style.visibility = "hidden";
	this._doc.body.style.visibility = "visible";
};

TMEdit.prototype.focusEditor = function() {
	switch (this._editMode) {
	case "wysiwyg":
		this._iframe.contentWindow.focus();
		break;

	case "textmode":
		this._textArea.focus();
		break;

	default:
		alert("ERROR: mode " + this._editMode + " is not defined");
	}
	return this._doc;
};

TMEdit.prototype._undoTakeSnapshot = function() {
	++this._undoPos;
	if (this._undoPos >= this.config.undoSteps) {
		this._undoQueue.shift();
		--this._undoPos;
	}
	var take = true;
	var txt = this.getInnerHTML();

	if (this._undoPos > 0) take = (this._undoQueue[this._undoPos - 1] != txt);

	if (take) {
		this._undoQueue[this._undoPos] = txt;
	} else {
		this._undoPos--;
	}
};

TMEdit.prototype.undo = function() {
	if (this._undoPos > 0) {
		var txt = this._undoQueue[--this._undoPos];

		if (txt) this.setHTML(txt);
		else++this._undoPos;
	}
};

TMEdit.prototype.redo = function() {
	if (this._undoPos < this._undoQueue.length - 1) {
		var txt = this._undoQueue[++this._undoPos];

		if (txt) this.setHTML(txt);
		else--this._undoPos;
	}
};

TMEdit.prototype.updateToolbar = function(noStatus) {
	var doc = this._doc;
	var text = (this._editMode == "textmode");
	var ancestors = null;

	if (!text) {
		ancestors = this.getAllAncestors();

		if (this.config.statusBar && !noStatus) {
			this._statusBarTree.innerHTML = TMEdit.I18N.msg["Path"] + ": ";

			for (var i = ancestors.length; --i >= 0;) {
				var el = ancestors[i];

				if (!el) {
					continue;
				}
				var a = document.createElement("a");
				a.href = "#";
				a.el = el;
				a.editor = this;
				a.onclick = function() {
					this.blur();
					this.editor.selectNodeContents(this.el);
					this.editor.updateToolbar(true);
					return false;
				};
				a.oncontextmenu = function() {
					this.blur();
					var info = "Inline Style:\n\n";
					info += this.el.style.cssText.split(/;\s*/).join(";\n");
					alert(info);
					return false;
				};
				var txt = el.tagName.toLowerCase();
				a.title = el.style.cssText;

				if (el.id) {
					txt += "#" + el.id;
				}

				if (el.className) {
					txt += "." + el.className;
				}
				a.appendChild(document.createTextNode(txt));
				this._statusBarTree.appendChild(a);

				if (i != 0) {
					this._statusBarTree.appendChild(document.createTextNode(String.fromCharCode(0xbb)));
				}
			}
		}
	}

	for (var i in this._toolbarObjects) {
		var btn = this._toolbarObjects[i];
		if (typeof(btn.state) != 'function') {
			continue;
		}
		var cmd = i;
		var inContext = true;

		if (btn.context && !text) {
			inContext = false;
			var context = btn.context;
			var attrs = [];

			if (/(.*)\[(.*?)\]/.test(context)) {
				context = RegExp.$1;
				attrs = RegExp.$2.split(",");
			}
			context = context.toLowerCase();
			var match = (context == "*");

			for (var k = 0; k < ancestors.length; ++k) {
				if (!ancestors[k]) {
					continue;
				}

				if (match || (ancestors[k].tagName.toLowerCase() == context)) {
					inContext = true;

					for (var ka = 0; ka < attrs.length; ++ka) {
						if (!eval("ancestors[k]." + attrs[ka])) {
							inContext = false;
							break;
						}
					}

					if (inContext) {
						break;
					}
				}
			}
		}
		btn.state("enabled", (!text || btn.text) && inContext);

		if (typeof cmd == "function") {
			continue;
		}
		var dropdown = this.config.customSelects[cmd];

		if ((!text || btn.text) && (typeof dropdown != "undefined")) {
			dropdown.refresh(this);
			continue;
		}

		switch (cmd) {
		case "fontname":
		case "fontsize":
		case "formatblock":
			if (!text) try {
				var value = ("" + doc.queryCommandValue(cmd)).toLowerCase();

				if (!value) {
					btn.element.selectedIndex = 0;
					break;
				}
				var options = this.config[cmd];
				var k = 0;

				for (var j in options) {
					if ((j.toLowerCase() == value) || (options[j].substr(0, value.length).toLowerCase() == value)) {
						btn.element.selectedIndex = k;
						throw "ok";
					}++k;
				}
				btn.element.selectedIndex = 0;
			}
			catch(e) {};
			break;

		case "textindicator":
			if (!text) {
				try {
					with(btn.element.style) {
						backgroundColor = TMEdit._makeColor(doc.queryCommandValue(TMEdit.is_ie ? "backcolor": "hilitecolor"));

						if (/transparent/i.test(backgroundColor)) {
							backgroundColor = TMEdit._makeColor(doc.queryCommandValue("backcolor"));
						}
						color = TMEdit._makeColor(doc.queryCommandValue("forecolor"));
						fontFamily = doc.queryCommandValue("fontname");
						fontWeight = doc.queryCommandState("bold") ? "bold": "normal";
						fontStyle = doc.queryCommandState("italic") ? "italic": "normal";
					}
				}
				catch(e) {}
			}
			break;

		case "htmlmode":
			btn.state("active", text);
			break;

		case "lefttoright":
		case "righttoleft":
			var el = this.getParentElement();

			while (el && !TMEdit.isBlockElement(el))
			el = el.parentNode;

			if (el) btn.state("active", (el.style.direction == ((cmd == "righttoleft") ? "rtl": "ltr")));
			break;

		default:
			try {
				btn.state("active", (!text && doc.queryCommandState(cmd)));
			}
			catch(e) {}
		}
	}

	if (this._customUndo && !this._timerUndo) {
		this._undoTakeSnapshot();
		var editor = this;
		this._timerUndo = setTimeout(function() {
			editor._timerUndo = null;
		},
		this.config.undoTimeout);
	}

	for (var i in this.plugins) {
		var plugin = this.plugins[i].instance;

		if (typeof plugin.onUpdateToolbar == "function") plugin.onUpdateToolbar();
	}
};

TMEdit.prototype.insertNodeAtSelection = function(toBeInserted) {
	if (!TMEdit.is_ie) {
		var sel = this._getSelection();
		var range = this._createRange(sel);
		sel.removeAllRanges();
		range.deleteContents();
		var node = range.startContainer;
		var pos = range.startOffset;

		switch (node.nodeType) {
		case 3:
			if (toBeInserted.nodeType == 3) {
				node.insertData(pos, toBeInserted.data);
				range = this._createRange();
				range.setEnd(node, pos + toBeInserted.length);
				range.setStart(node, pos + toBeInserted.length);
				sel.addRange(range);
			} else {
				node = node.splitText(pos);
				var selnode = toBeInserted;

				if (toBeInserted.nodeType == 11) {
					selnode = selnode.firstChild;
				}
				node.parentNode.insertBefore(toBeInserted, node);
				this.selectNodeContents(selnode);
				this.updateToolbar();
			}
			break;

		case 1:
			var selnode = toBeInserted;

			if (toBeInserted.nodeType == 11) {
				selnode = selnode.firstChild;
			}
			node.insertBefore(toBeInserted, node.childNodes[pos]);
			this.selectNodeContents(selnode);
			this.updateToolbar();
			break;
		}
	} else {
		return null;
	}
};

TMEdit.prototype.getParentElement = function() {
	var sel = this._getSelection();
	var range = this._createRange(sel);

	if (TMEdit.is_ie) {
		switch (sel.type) {
		case "Text":
		case "None":
			return range.parentElement();

		case "Control":
			return range.item(0);

		default:
			return this._doc.body;
		}
	} else try {
		var p = range.commonAncestorContainer;

		if (!range.collapsed && range.startContainer == range.endContainer && range.startOffset - range.endOffset <= 1 && range.startContainer.hasChildNodes()) p = range.startContainer.childNodes[range.startOffset];

		while (p.nodeType == 3) {
			p = p.parentNode;
		}
		return p;
	}
	catch(e) {
		return null;
	}
};

TMEdit.prototype.getAllAncestors = function() {
	var p = this.getParentElement();
	var a = [];

	while (p && (p.nodeType == 1) && (p.tagName.toLowerCase() != 'body')) {
		a.push(p);
		p = p.parentNode;
	}
	a.push(this._doc.body);
	return a;
};

TMEdit.prototype.selectNodeContents = function(node, pos) {
	this.focusEditor();
	this.forceRedraw();
	var range;
	var collapsed = (typeof pos != "undefined");

	if (TMEdit.is_ie) {
		range = this._doc.body.createTextRange();
		range.moveToElementText(node); (collapsed) && range.collapse(pos);
		range.select();
	} else {
		var sel = this._getSelection();
		range = this._doc.createRange();
		range.selectNodeContents(node); (collapsed) && range.collapse(pos);
		sel.removeAllRanges();
		sel.addRange(range);
	}
};

TMEdit.prototype.insertHTML = function(html) {
	var sel = this._getSelection();
	var range = this._createRange(sel);

	if (TMEdit.is_ie) {
		range.pasteHTML(html);
	} else {
		var fragment = this._doc.createDocumentFragment();
		var div = this._doc.createElement("div");
		div.innerHTML = html;

		while (div.firstChild) {
			fragment.appendChild(div.firstChild);
		}
		var node = this.insertNodeAtSelection(fragment);
	}
};

TMEdit.prototype.surroundHTML = function(startTag, endTag) {
	var html = this.getSelectedHTML();
	this.insertHTML(startTag + html + endTag);
};

TMEdit.prototype.getSelectedHTML = function() {
	var sel = this._getSelection();
	var range = this._createRange(sel);
	var existing = null;

	if (TMEdit.is_ie) {
		existing = range.htmlText;
	} else {
		existing = TMEdit.getHTML(range.cloneContents(), false, this);
	}
	return existing;
};

TMEdit.prototype.hasSelectedText = function() {
	return this.getSelectedHTML() != '';
};

TMEdit.prototype._insertCharacter = function() {
	var editor = this;
	this._popupDialog("select_character.html",
	function(entity) {
		if (!entity) {
			return false;
		}
		editor.insertHTML(entity);
	},
	null);
};

TMEdit.prototype._insertImage = function(image) {
	var editor = this;
	var outparam = null;

	if (typeof image == "undefined") {
		image = this.getParentElement();

		if (image && !/^img$/i.test(image.tagName)) image = null;
	}

	if (image) outparam = {
		f_url: TMEdit.is_ie ? editor.stripBaseURL(image.src) : image.getAttribute("src"),
		f_alt: image.alt,
		f_border: image.border,
		f_align: image.align,
		f_vert: image.vspace,
		f_horiz: image.hspace,
		f_width: image.width,
		f_height: image.height
	};

	this._popupDialog("insert_image.php",
	function(param) {
		if (!param) {
			return false;
		}
		var img = image;

		if (!img) {
			var sel = editor._getSelection();
			var range = editor._createRange(sel);
			editor._doc.execCommand("insertimage", false, param.f_url);

			if (TMEdit.is_ie) {
				img = range.parentElement();

				if (img.tagName.toLowerCase() != "img") {
					img = img.previousSibling;
				}
			} else {
				img = range.startContainer.previousSibling;
			}
		} else {
			img.src = param.f_url;
		}

		for (fields in param) {
			var value = param[fields];

			switch (fields) {
			case "f_alt":
				img.alt = value;
				break;

			case "f_border":
				img.border = parseInt(value || "0");
				break;

			case "f_align":
				img.align = value;
				break;

			case "f_vert":
				img.vspace = parseInt(value || "0");
				break;

			case "f_horiz":
				img.hspace = parseInt(value || "0");
				break;

			case "f_width":
				img.width = parseInt(value || "0");
				break;

			case "f_height":
				img.height = parseInt(value || "0");
				break;
			}
		}
	},
	outparam);
};

TMEdit.prototype._createLink = function(link) {
	var editor = this;
	var outparam = null;

	if (typeof link == "undefined") {
		link = this.getParentElement();

		if (link && /^img$/i.test(link.tagName)) link = link.parentNode;

		if (link && !/^a$/i.test(link.tagName)) link = null;
	}

	if (link) outparam = {
		i_href: TMEdit.is_ie ? editor.stripBaseURL(link.href) : link.getAttribute("href"),
		i_title: link.title,
		i_target: link.target
	};

	this._popupDialog("insert_link.php",
	function(param) {
		if (!param) return false;

		var a = link;

		if (!a) {
			editor._doc.execCommand("createlink", false, param.i_href);
			a = editor.getParentElement();
			var sel = editor._getSelection();
			var range = editor._createRange(sel);

			if (!TMEdit.is_ie) {
				a = range.startContainer;

				if (a && !/^a$/i.test(a.tagName)) {
					a = a.nextSibling;

					if (a == null) {
						a = range.startContainer.parentNode;
					}
				}
			}
		} else {
			var href = param.i_href.trim();
			editor.selectNodeContents(a);

			if (href == "") {
				editor._doc.execCommand("unlink", false, null);
				editor.updateToolbar();
				return false;
			} else {
				a.href = href;
			}
		}

		if (/^img$/i.test(a.tagName)) a = a.parentNode;

		if (a && !/^a$/i.test(a.tagName)) return false;
		a.target = param.i_target.trim();
		a.title = param.i_title.trim();
		editor.selectNodeContents(a);
		editor.updateToolbar();
	},
	outparam);
};

TMEdit.prototype._insertTable = function() {
	var sel = this._getSelection();
	var range = this._createRange(sel);
	var editor = this;
	this._popupDialog("insert_table.html",
	function(param) {
		if (!param) {
			return false;
		}
		var doc = editor._doc;
		var table = doc.createElement("table");

		for (var field in param) {
			var value = param[field];

			if (!value) {
				continue;
			}

			switch (field) {
			case "f_width":
				table.style.width = value + param["f_unit"];
				break;

			case "f_align":
				table.align = value;
				break;

			case "f_border":
				table.border = parseInt(value);
				break;

			case "f_spacing":
				table.cellspacing = parseInt(value);
				break;

			case "f_padding":
				table.cellpadding = parseInt(value);
				break;
			}
		}
		var tbody = doc.createElement("tbody");
		table.appendChild(tbody);

		for (var i = 0; i < param["f_rows"]; ++i) {
			var tr = doc.createElement("tr");
			tbody.appendChild(tr);

			for (var j = 0; j < param["f_cols"]; ++j) {
				var td = doc.createElement("td");
				tr.appendChild(td); (TMEdit.is_gecko) && td.appendChild(doc.createElement("br"));
			}
		}

		if (TMEdit.is_ie) {
			range.pasteHTML(table.outerHTML);
		} else {
			editor.insertNodeAtSelection(table);
		}
		return true;
	},
	null);
};

TMEdit.prototype._toggleBorders = function() {
	tables = this._doc.getElementsByTagName('TABLE');

	if (tables.length != 0) {
		if (!this.borders) {
			name = "bordered";
			this.borders = true;
		} else {
			name = "";
			this.borders = false;
		}

		for (var ix = 0; ix < tables.length; ix++) {
			if (this.borders) {
				this._addClasses(tables[ix], 'htmtableborders');
			} else {
				this._removeClasses(tables[ix], 'htmtableborders');
			}
		}
	}
	return true;
};

TMEdit.prototype._removeClasses = function(el, classes) {
	if (el != null) {
		var thiers = el.className.trim().split(' ');
		var new_thiers = [];
		var ours = classes.split(' ');

		for (var x = 0; x < thiers.length; x++) {
			var exists = false;

			for (var i = 0; exists == false && i < ours.length; i++) {
				if (ours == thiers[x]) {
					exists = true;
				}
			}

			if (exists == false) {
				new_thiers[new_thiers.length] = thiers[x];
			}
		}

		if (new_thiers.length == 0 && el._stylist_usedToBe && el._stylist_usedToBe.length > 0 && el._stylist_usedToBe[el._stylist_usedToBe.length - 1].className != null) {
			var last_el = el._stylist_usedToBe[el._stylist_usedToBe.length - 1];
			var last_classes = TMEdit.arrayFilter(last_el.className.trim().split(' '),
			function(c) {
				if (c == null || c.trim() == '') {
					return false;
				}
				return true;
			});

			if ((new_thiers.length == 0) || (TMEdit.arrayContainsArray(new_thiers, last_classes) && TMEdit.arrayContainsArray(last_classes, new_thiers))) {
				el = this.switchElementTag(el, last_el.tagName);
				new_thiers = last_classes;
			} else {
				el._stylist_usedToBe = [];
			}
		}

		if (new_thiers.length > 0 || el.tagName.toLowerCase() != 'span' || (el.id && el.id != '')) {
			el.className = new_thiers.join(' ').trim();
		} else {
			var prnt = el.parentNode;
			var childs = el.childNodes;

			for (var x = 0; x < childs.length; x++) {
				prnt.insertBefore(childs[x], el);
			}
			prnt.removeChild(el);
		}
	}
};

TMEdit.prototype._addClasses = function(el, classes) {
	if (el != null) {
		var thiers = el.className.trim().split(' ');
		var ours = classes.split(' ');

		for (var x = 0; x < ours.length; x++) {
			var exists = false;

			for (var i = 0; exists == false && i < thiers.length; i++) {
				if (thiers == ours[x]) {
					exists = true;
				}
			}

			if (exists == false) {
				thiers[thiers.length] = ours[x];
			}
		}
		el.className = thiers.join(' ').trim();
	}
};

TMEdit.prototype._preview = function() {
	myWindow = window.open('', 'preview', 'width=640,height=480,menubar=0,status=1,location=0,toolbar=1,scrollbars=1,resizable=1"');
	myWindow.document.open();
	myWindow.document.write('<html>\n<body onload=\"self.focus();\">\n'); + myWindow.document.write('<head>\n<title>' + TMEdit.I18N.preview["Preview"] + '</title>\n'); + myWindow.document.write('<style type=\"text/css\">' + this.config.pageStyle + '</style>\n</head>\n'); + myWindow.document.write(this.getInnerHTML()); + myWindow.document.write('\n<br />\n<br />\n<div align=\"center\" style=\"margin-top: 20px; border-top: 1px solid #ccc;\"><a href=\"javascript:self.close();\">' + TMEdit.I18N.preview["Close this window"] + '</a></div><br />\n'); + myWindow.document.write('</body></html>');
	myWindow.document.close();
};

TMEdit.prototype._insertFile = function() {
	var sel = this._getSelection();
	var range = this._createRange(sel);
	var editor = this;
	this._popupDialog("InsertFile/insert_file.php",
	function(param) {
		if (!param) {
			return false;
		}
		var alink = editor._doc.createElement("a");
		var caption = "";

		if (param["f_addicon"]) {
			var img = editor._doc.createElement("img");
			img.src = param["f_icon"];
			img.alt = "icon";
			alink.appendChild(img);
			caption = '<img src="' + param["f_icon"] + '" alt="icon" border="0" align="bottom">&nbsp;';
		}
		caption = caption + param["f_caption"];

		if (param["f_addsize"] || param["f_adddate"]) caption = caption + ' <span class="smalldark">(';

		if (param["f_addsize"]) caption = caption + param["f_size"];

		if (param["f_adddate"]) caption = caption + ' ' + param["f_date"];

		if (param["f_addsize"] || param["f_adddate"]) caption = caption + ')</span> ';
		alink.href = param["f_url"];
		alink.innerHTML = caption;

		if (TMEdit.is_ie) {
			range.pasteHTML(alink.outerHTML);
		} else {
			editor.insertNodeAtSelection(alink);
		}
		return true;
	},
	null);
};

TMEdit.prototype._comboSelected = function(el, txt) {
	this.focusEditor();
	var value = el.options[el.selectedIndex].value;

	switch (txt) {
	case "fontname":
	case "fontsize":
		this.execCommand(txt, false, value);
		break;

	case "formatblock":
		(TMEdit.is_ie) && (value = "<" + value + ">");
		this.execCommand(txt, false, value);
		break;

	default:
		var dropdown = this.config.customSelects[txt];

		if (typeof dropdown != "undefined") {
			dropdown.action(this);
		}
	}
};

TMEdit.prototype.execCommand = function(cmdID, UI, param) {
	var editor = this;
	this.focusEditor();
	cmdID = cmdID.toLowerCase();

	switch (cmdID) {
	case "htmlmode":
		this.setMode();
		break;

	case "hilitecolor":
		(TMEdit.is_ie) && (cmdID = "backcolor");

	case "forecolor":
		this._popupDialog("select_color.html",
		function(color) {
			if (color) {
				editor._doc.execCommand(cmdID, false, "#" + color);
			}
		},
		TMEdit._colorToRgb(this._doc.queryCommandValue(cmdID)));
		break;

	case "createlink":
		this._createLink();
		break;

	case "undo":
	case "redo":
		if (this._customUndo) this[cmdID]();
		else this._doc.execCommand(cmdID, UI, param);
		break;

	case "inserttable":
		this._insertTable();
		break;

	case "toggleborders":
		this._toggleBorders();
		break;

	case "insertimage":
		this._insertImage();
		break;

	case "insertfile":
		this._insertFile();
		break;

	case "preview":
		this._preview();
		break;

	case "insertcharacter":
		this._insertCharacter();
		break;

	case "showhelp":
		window.open(this.popupURL("help.html"), "ha_help", "toolbar=no,menubar=no,personalbar=no,width=500,height=450," + "scrollbars=yes,resizable=no");
		break;

	case "cut":
	case "copy":
	case "paste":
		try {
			if (this.config.killWordOnPaste) this._wordClean();
			this._doc.execCommand(cmdID, UI, param);
		}
		catch(e) {
			if (TMEdit.is_gecko) {
				if (confirm(TMEdit.I18N.msg["Moz-Clipboard"])) {
					window.open(this.popupURL("mozilla_prefs.html"), "ha_moz_prefs", "toolbar=no,menubar=no,personalbar=no,width=700,height=250," + "scrollbars=no,resizable=no");
				} else {}
			}
		}
		break;

	case "lefttoright":
	case "righttoleft":
		var dir = (cmdID == "righttoleft") ? "rtl": "ltr";
		var el = this.getParentElement();

		while (el && !TMEdit.isBlockElement(el))
		el = el.parentNode;

		if (el) {
			if (el.style.direction == dir) el.style.direction = "";
			else el.style.direction = dir;
		}
		break;

	default:
		this._doc.execCommand(cmdID, UI, param);
	}
	this.updateToolbar();
	return false;
};

TMEdit.prototype._editorEvent = function(ev) {
	var editor = this;
	var keyEvent = (TMEdit.is_ie && ev.type == "keydown") || (ev.type == "keypress");

	if (keyEvent) {
		for (var i in editor.plugins) {
			var plugin = editor.plugins[i].instance;

			if (typeof plugin.onKeyPress == "function") plugin.onKeyPress(ev);
		}
	}

	if (keyEvent && ev.ctrlKey) {
		var sel = null;
		var range = null;
		var key = String.fromCharCode(TMEdit.is_ie ? ev.keyCode: ev.charCode).toLowerCase();
		var cmd = null;
		var value = null;

		switch (key) {
		case 'a':
			if (!TMEdit.is_ie) {
				sel = this._getSelection();
				sel.removeAllRanges();
				range = this._createRange();
				range.selectNodeContents(this._doc.body);
				sel.addRange(range);
				TMEdit._stopEvent(ev);
			}
			break;

		case 'b':
			cmd = "bold";
			break;

		case 'i':
			cmd = "italic";
			break;

		case 'u':
			cmd = "underline";
			break;

		case 's':
			cmd = "strikethrough";
			break;

		case 'l':
			cmd = "justifyleft";
			break;

		case 'e':
			cmd = "justifycenter";
			break;

		case 'r':
			cmd = "justifyright";
			break;

		case 'j':
			cmd = "justifyfull";
			break;

		case 'z':
			cmd = "undo";
			break;

		case 'y':
			cmd = "redo";
			break;

		case 'v':
			cmd = "paste";
			break;

		case '0':
			cmd = "killword";
			break;

		case '1':
		case '2':
		case '3':
		case '4':
		case '5':
		case '6':
			cmd = "formatblock";
			value = "h" + key;

			if (TMEdit.is_ie) {
				value = "<" + value + ">";
			}
			break;
		}

		if (cmd) {
			this.execCommand(cmd, false, value);
			TMEdit._stopEvent(ev);
		}
	}

	if (editor._timerToolbar) {
		clearTimeout(editor._timerToolbar);
	}
	editor._timerToolbar = setTimeout(function() {
		editor.updateToolbar();
		editor._timerToolbar = null;
	},
	50);
};

TMEdit.prototype.getHTML = function() {
	switch (this._editMode) {
	case "wysiwyg":
		return TMEdit.getHTML(this._doc.body, false, this);
	case "textmode":
		return this._textArea.value;

	default:
		alert("Mode <" + mode + "> not defined!");
	}
	return false;
};

TMEdit.prototype.getInnerHTML = function() {
	switch (this._editMode) {
	case "wysiwyg":
		try {
			return this._doc.body.innerHTML;
		}
		catch(e) {};

	case "textmode":
		return this._textArea.value;

	default:
		alert("Mode <" + mode + "> not defined!");
	}
	return false;
};

TMEdit.prototype.setHTML = function(html) {
	switch (this._editMode) {
	case "wysiwyg":
		this._doc.body.innerHTML = html;
		break;

	case "textmode":
		this._textArea.value = html;
		break;

	default:
		alert("Mode <" + mode + "> not defined!");
	}
	return false;
};

TMEdit.prototype.setDoctype = function(doctype) {
	this.doctype = doctype;
};

TMEdit.agt = navigator.userAgent.toLowerCase();
TMEdit.is_ie = ((TMEdit.agt.indexOf("msie") != -1) && (TMEdit.agt.indexOf("opera") == -1));
TMEdit.is_opera = (TMEdit.agt.indexOf("opera") != -1);
TMEdit.opera_version = navigator.appVersion.substring(0, navigator.appVersion.indexOf(' ')) * 1;
TMEdit.is_safari = (TMEdit.agt.indexOf('safari') != -1);
TMEdit.safari_version = parseInt(navigator.appVersion.replace(/.*?AppleWebKit\/([\d]).*?/, '$1'), 10);
TMEdit.is_mac = (TMEdit.agt.indexOf("mac") != -1);
TMEdit.is_mac_ie = (TMEdit.is_ie && TMEdit.is_mac);
TMEdit.is_win_ie = (TMEdit.is_ie && !TMEdit.is_mac);
TMEdit.is_gecko = (navigator.product == "Gecko");
TMEdit.is_designMode = (typeof document.designMode != 'undefined' && !TMEdit.is_ie && !TMEdit.is_safari);

TMEdit._object = null;
TMEdit.cloneObject = function(obj) {
	var newObj = new Object;

	if (obj.constructor.toString().indexOf("function Array(") == 1) {
		newObj = obj.constructor();
	}

	if (obj.constructor.toString().indexOf("function Function(") == 1) {
		newObj = obj;
	} else for (var n in obj) {
		var node = obj[n];

		if (typeof node == 'object') {
			newObj[n] = TMEdit.cloneObject(node);
		} else {
			newObj[n] = node;
		}
	}
	return newObj;
};

TMEdit.checkSupportedBrowser = function() {
	var supported = true;
	if (TMEdit.is_gecko && !TMEdit.is_safari && !TMEdit.is_opera) {
		if (navigator.productSub < 20030210) {
			supported = false;
		}
	}

	if (TMEdit.is_safari && TMEdit.safari_version < 522) {
		alert('Sorry, Safari prior version 3.0 is not supported by TMEdit.');
		supported = false;
	}
	if (TMEdit.is_opera && TMEdit.opera_version < 9.1) {
		alert('Sorry, Opera prior version 9.5 is not supported by TMEdit.');
		supported = false;
	}
	if (!supported) {
		try {
			var elt = document.getElementById('xs_dialog_container_' + textareaID);
			var elt2 = document.getElementById('xs_container_fulltext');
			if (typeof elt !== null && typeof elt != 'undefined') {
				elt.style.display = 'none';
			}
			if (typeof elt2 !== null && typeof elt2 != 'undefined') {
				elt2.style.display = '';
			}
		} catch(ex) {}
		return;
	} else {
		return TMEdit.is_ie || TMEdit.is_designMode || TMEdit.is_safari;
	}
};

TMEdit.prototype._getSelection = function() {
	if (TMEdit.is_ie) {
		return this._doc.selection;
	} else {
		return this._iframe.contentWindow.getSelection();
	}
};

TMEdit.prototype._createRange = function(sel) {
	if (TMEdit.is_ie) {
		return sel.createRange();
	} else {
		this.focusEditor();

		if (typeof sel != "undefined") {
			try {
				return sel.getRangeAt(0);
			}
			catch(e) {
				return this._doc.createRange();
			}
		} else {
			return this._doc.createRange();
		}
	}
};

TMEdit._addEvent = function(el, evname, func) {
	if (TMEdit.is_ie) {
		el.attachEvent("on" + evname, func);
	} else {
		el.addEventListener(evname, func, true);
	}
};

TMEdit._addEvents = function(el, evs, func) {
	for (var i in evs) {
		TMEdit._addEvent(el, evs[i], func);
	}
};

TMEdit._removeEvent = function(el, evname, func) {
	if (TMEdit.is_ie) {
		el.detachEvent("on" + evname, func);
	} else {
		el.removeEventListener(evname, func, true);
	}
};

TMEdit._removeEvents = function(el, evs, func) {
	for (var i in evs) {
		TMEdit._removeEvent(el, evs[i], func);
	}
};

TMEdit._stopEvent = function(ev) {
	if (TMEdit.is_ie) {
		ev.cancelBubble = true;
		ev.returnValue = false;
	} else {
		ev.preventDefault();
		ev.stopPropagation();
	}
};

TMEdit._removeClass = function(el, className) {
	if (! (el && el.className)) {
		return;
	}
	var cls = el.className.split(" ");
	var ar = new Array();

	for (var i = cls.length; i > 0;) {
		if (cls[--i] != className) {
			ar[ar.length] = cls[i];
		}
	}
	el.className = ar.join(" ");
};

TMEdit._addClass = function(el, className) {
	TMEdit._removeClass(el, className);
	el.className += " " + className;
};

TMEdit._hasClass = function(el, className) {
	if (! (el && el.className)) {
		return false;
	}
	var cls = el.className.split(" ");

	for (var i = cls.length; i > 0;) {
		if (cls[--i] == className) {
			return true;
		}
	}
	return false;
};

TMEdit.isBlockElement = function(el) {
	var blockTags = " body form textarea fieldset ul ol dl li div " + "p h1 h2 h3 h4 h5 h6 quote pre table thead " + "tbody tfoot tr td iframe address ";
	return (blockTags.indexOf(" " + el.tagName.toLowerCase() + " ") != -1);
};

TMEdit.needsClosingTag = function(el) {
	var closingTags = " head script style div span tr td tbody table em strong font a title ";
	return (closingTags.indexOf(" " + el.tagName.toLowerCase() + " ") != -1);
};

TMEdit.htmlEncode = function(str) {
	str = str.replace(/&/ig, "&amp;");
	str = str.replace(/</ig, "&lt;");
	str = str.replace(/>/ig, "&gt;");
	str = str.replace(/\xA0/g, "&nbsp;");
	str = str.replace(/\x22/ig, "&quot;");
	return str;
};

TMEdit.getHTML = function(root, outputRoot, editor) {
	var html = "";

	switch (root.nodeType) {
	case 1:
	case 11:
		var closed;
		var i;
		var root_tag = (root.nodeType == 1) ? root.tagName.toLowerCase() : '';

		if (TMEdit.is_ie && root_tag == "head") {
			if (outputRoot) html += "<head>";
			var save_multiline = RegExp.multiline;
			RegExp.multiline = true;
			var txt = root.innerHTML.replace(TMEdit.RE_tagName,
			function(str, p1, p2) {
				return p1 + p2.toLowerCase();
			});
			RegExp.multiline = save_multiline;
			html += txt;

			if (outputRoot) html += "</head>";
			break;
		} else if (outputRoot) {
			closed = (!(root.hasChildNodes() || TMEdit.needsClosingTag(root)));
			html = "<" + root.tagName.toLowerCase();
			var attrs = root.attributes;

			for (i = 0; i < attrs.length; ++i) {
				var a = attrs.item(i);

				if (!a.specified) {
					continue;
				}
				var name = a.nodeName.toLowerCase();

				if (/_moz|contenteditable|_msh/.test(name)) {
					continue;
				}
				var value;

				if (name != "style") {
					if (typeof root[a.nodeName] != "undefined" && name != "href" && name != "src" && name != "onclick" && name != "onmouseover" && name != "onmouseout") {
						value = root[a.nodeName];
					} else {
						value = a.nodeValue;

						if (TMEdit.is_ie && (name == "href" || name == "src")) {
							value = editor.stripBaseURL(value);
						}
					}
				} else {
					value = root.style.cssText;
				}

				if (/(_moz|^$)/.test(value)) {
					continue;
				}
				html += " " + name + '="' + value + '"';
			}
			html += closed ? " />": ">";
		}

		for (i = root.firstChild; i; i = i.nextSibling) {
			html += TMEdit.getHTML(i, true, editor);
		}

		if (outputRoot && !closed) {
			html += "</" + root.tagName.toLowerCase() + ">";
		}
		break;

	case 3:
		if (!root.previousSibling && !root.nextSibling && root.data.match(/^\s*$/i)) html = '&nbsp;';
		else html = TMEdit.htmlEncode(root.data);
		break;

	case 8:
		html = "<!--" + root.data + "-->";
		break;
	}
	return html;
};

TMEdit.prototype.stripBaseURL = function(string) {
	var baseurl = this.config.baseURL;
	baseurl = baseurl.replace(/[^\/]+$/, '');
	var basere = new RegExp(baseurl);
	string = string.replace(basere, "");
	baseurl = baseurl.replace(/^(https?:\/\/[^\/]+)(.*)$/, '$1');
	basere = new RegExp(baseurl);
	return string.replace(basere, "");
};
String.prototype.trim = function() {
	a = this.replace(/^\s+/, '');
	return a.replace(/\s+$/, '');
};

TMEdit._makeColor = function(v) {
	if (typeof v != "number") {
		return v;
	}
	var r = v & 0xFF;
	var g = (v >> 8) & 0xFF;
	var b = (v >> 16) & 0xFF;
	return "rgb(" + r + "," + g + "," + b + ")";
};

TMEdit._colorToRgb = function(v) {
	if (!v) return '';

	function hex(d) {
		return (d < 16) ? ("0" + d.toString(16)) : d.toString(16);
	};

	if (typeof v == "number") {
		var r = v & 0xFF;
		var g = (v >> 8) & 0xFF;
		var b = (v >> 16) & 0xFF;
		return "#" + hex(r) + hex(g) + hex(b);
	}

	if (v.substr(0, 3) == "rgb") {
		var re = /rgb\s*\(\s*([0-9]+)\s*,\s*([0-9]+)\s*,\s*([0-9]+)\s*\)/;

		if (v.match(re)) {
			var r = parseInt(RegExp.$1);
			var g = parseInt(RegExp.$2);
			var b = parseInt(RegExp.$3);
			return "#" + hex(r) + hex(g) + hex(b);
		}
		return null;
	}

	if (v.substr(0, 1) == "#") {
		return v;
	}
	return null;
};

TMEdit.prototype._popupDialog = function(url, action, init) {
	Dialog(this.popupURL(url), action, init);
};

TMEdit.prototype.imgURL = function(file, plugin) {
	if (typeof plugin == "undefined") return _editor_url + file;
	else return _editor_url + "plugins/" + plugin + "/img/" + file;
};

TMEdit.prototype.popupURL = function(file) {
	var url = "";

	if (file.match(/^plugin:\/\/(.*?)\/(.*)/)) {
		var plugin = RegExp.$1;
		var popup = RegExp.$2;

		if (!/\.html$/.test(popup)) popup += ".html";
		url = _editor_url + "plugins/" + plugin + "/popups/" + popup;
	} else url = _editor_url + this.config.popupURL + file;
	return url;
};

TMEdit.getElementById = function(tag, id) {
	var el, i, objs = document.getElementsByTagName(tag);

	for (i = objs.length; --i >= 0 && (el = objs[i]);)
	if (el.id == id) return el;

	return null;
};