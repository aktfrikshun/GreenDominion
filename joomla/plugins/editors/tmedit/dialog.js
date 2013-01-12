// $Id: dialog.js, v 1.2 2009/06/23 14:44:22 bpfeifer Exp $
/**
* TMEdit Core File
* @ package TMEdit
* @ Copyright © 2004-2009 Bernhard Pfeifer - www.xhtmlsuite.com
* @ All rights reserved
* @ Released under the GNU/GPL http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
* @version $Revision: 1.2 $
**/

function Dialog(url, action, init) {
	if (typeof init == "undefined") {
		init = window;
	}
	Dialog._geckoOpenModal(url, action, init);
};

Dialog._parentEvent = function(ev) {
	if (Dialog._modal && !Dialog._modal.closed) {
		setTimeout(function(){
			try {
				Dialog._modal.focus();
				TMEdit._stopEvent(ev);
			} catch (ex) {}
		}, 50);
	}
};

Dialog._return = null;
Dialog._modal = null;
Dialog._arguments = null;

Dialog._geckoOpenModal = function(url, action, init) {
	var dlg = window.open(url, "tmeditdialog", "toolbar=no,menubar=no,personalbar=no,width=10,height=10," + "scrollbars=no,resizable=yes,dependent=yes");
	dlg.focus();
	Dialog._modal = dlg;
	Dialog._arguments = init;

	function capwin(w) {
		TMEdit._addEvent(w, "click", Dialog._parentEvent);
		TMEdit._addEvent(w, "mousedown", Dialog._parentEvent);
		TMEdit._addEvent(w, "focus", Dialog._parentEvent);
	};
	function relwin(w) {
		TMEdit._removeEvent(w, "click", Dialog._parentEvent);
		TMEdit._removeEvent(w, "mousedown", Dialog._parentEvent);
		TMEdit._removeEvent(w, "focus", Dialog._parentEvent);
	};
	capwin(window);
	for (var i = 0; i < window.frames.length; i++) {try { capwin(window.frames[i]); } catch(e) { } };
	Dialog._return = function (val) {
		if (val && action) {
			action(val);
		}
		relwin(window);
		for (var i = 0; i < window.frames.length; i++) { try { relwin(window.frames[i]); } catch(e) { } };
		Dialog._modal = null;
	};
};