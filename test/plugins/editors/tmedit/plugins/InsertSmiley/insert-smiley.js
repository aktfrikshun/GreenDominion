// Based on htmlArea v3.0 - Copyright (c) 2002-2004 interactivetools.com, inc., dynarch.com
// TMEdit - © Copyright 2004-2009 XHTMLSuite.com
// Released under the GNU/GPL http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt

function InsertSmiley(editor) {
	this.editor = editor;
	var cfg = editor.config;
	var tt = InsertSmiley.I18N;
	var bl = InsertSmiley.btnList;
	var self = this;
	var toolbar = ["separator"];
	for (var i = 0; i < bl.length; ++i) {
		var btn = bl[i];
		if (!btn) {
			toolbar.push("separator");
		} else {
			var id = "IS-" + btn[0];
			cfg.registerButton(id, tt[id], editor.imgURL(btn[0] + ".gif", "InsertSmiley"), false,
				function(editor, id) {
				   self.buttonPress(editor, id);
				}, btn[1]);
			toolbar.push(id);
		}
	}
	for (var i in toolbar) {
		cfg.toolbar[0].push(toolbar[i]);
	}
};

InsertSmiley._pluginInfo = {
	name          : "InsertSmiley",
	version       : "v1.0",
	developer     : "Bernhard Pfeifer",
	developer_url : "http://www.xhtmlsuite.com/",
	c_owner       : "Bernhard Pfeifer",
	sponsor       : "none",
	sponsor_url   : "none",
	license       : "Released under the GNU/GPL http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt"
};

InsertSmiley.btnList = [
	//null, // separator
	["insert-smiley"]
	];

InsertSmiley.prototype.buttonPress = function(editor, id) {
	this.editor = editor;
	InsertSmiley.editor = editor;
	InsertSmiley.init = true;
	var sel = editor._getSelection();
	var range = editor._createRange(sel);
	editor._popupDialog("plugin://InsertSmiley/insert_smiley", function(param) {
		if(!param) {
			return false;
		}
		editor.focusEditor();
		editor.insertHTML('<img src="'+param.f_url+'" alt="'+param.f_alt+'" width="'+param.f_width+'" height="'+param.f_height+'" border="0" />');
	}, null);
};