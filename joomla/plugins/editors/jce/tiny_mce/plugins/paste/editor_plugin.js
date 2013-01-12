(function(){var each=tinymce.each;var styleProps=new Array('background','background-attachment','background-color','background-image','background-position','background-repeat','border','border-bottom','border-bottom-color','border-bottom-style','border-bottom-width','border-color','border-left','border-left-color','border-left-style','border-left-width','border-right','border-right-color','border-right-style','border-right-width','border-style','border-top','border-top-color','border-top-style','border-top-width','border-width','outline','outline-color','outline-style','outline-width','height','max-height','max-width','min-height','min-width','width','font','font-family','font-size','font-style','font-variant','font-weight','content','counter-increment','counter-reset','quotes','list-style','list-style-image','list-style-position','list-style-type','margin','margin-bottom','margin-left','margin-right','margin-top','padding','padding-bottom','padding-left','padding-right','padding-top','bottom','clear','clip','cursor','display','float','left','overflow','position','right','top','visibility','z-index','orphans','page-break-after','page-break-before','page-break-inside','widows','border-collapse','border-spacing','caption-side','empty-cells','table-layout','color','direction','letter-spacing','line-height','text-align','text-decoration','text-indent','text-shadow','text-transform','unicode-bidi','vertical-align','white-space','word-spacing');tinymce.create('tinymce.plugins.PastePlugin',{init:function(ed,url){var t=this,cb;t.editor=ed;t.url=url;t.onPreProcess=new tinymce.util.Dispatcher(t);t.onPostProcess=new tinymce.util.Dispatcher(t);t.onAfterPaste=new tinymce.util.Dispatcher(t);t.onPreProcess.add(t._preProcess);t.onPostProcess.add(t._postProcess);t.onPreProcess.add(function(pl,o){ed.execCallback('paste_preprocess',pl,o)});t.onPostProcess.add(function(pl,o){ed.execCallback('paste_postprocess',pl,o)});t.pasteText=ed.getParam('paste_text',1);t.pasteHtml=ed.getParam('paste_html',1);function process(o){var dom=ed.dom;t.plainText=t.command=='mcePasteText'||(t.pasteText&&!t.pasteHtml);t.onPreProcess.dispatch(t,o);o.node=dom.create('div',0,o.content);t.onPostProcess.dispatch(t,o);o.content=ed.serializer.serialize(o.node,{getInner:1});if(t.plainText){o.wordContent=false;t._insertPlainText(ed,dom,o.content)}else if(/<(p|h[1-6]|ul|ol)/.test(o.content)){t._insertBlockContent(ed,dom,o.content)}else{t._insert(o.content)}t.onAfterPaste.dispatch(t);t.command='mcePaste'};ed.addCommand('mceInsertClipboardContent',function(u,o){process(o)});ed.onInit.add(function(){if(ed.plugins.contextmenu){ed.plugins.contextmenu.onContextMenu.add(function(th,m,e){var c=ed.selection.isCollapsed();m.add({title:'advanced.cut_desc',icon:'cut',cmd:'Cut'}).setDisabled(c);m.add({title:'advanced.copy_desc',icon:'copy',cmd:'Copy'}).setDisabled(c);if(t.pasteHtml){m.add({title:'paste.paste_desc',icon:'paste',cmd:'mcePaste'})}if(t.pasteText){m.add({title:'paste.paste_text_desc',icon:'pastetext',cmd:'mcePasteText'})}})}});function grabContent(e){var n,or,rng,sel=ed.selection,dom=ed.dom,body=ed.getBody(),posY;if(ed.pasteAsPlainText&&(e.clipboardData||dom.doc.dataTransfer)){e.preventDefault();process({content:(e.clipboardData||dom.doc.dataTransfer).getData('Text')},true);return}if(dom.get('_mcePaste'))return;n=dom.add(body,'div',{id:'_mcePaste','class':'mcePaste'},'\uFEFF<br _mce_bogus="1">');if(body!=ed.getDoc().body)posY=dom.getPos(ed.selection.getStart(),body).y;else posY=body.scrollTop;dom.setStyles(n,{position:'absolute',left:-10000,top:posY,width:1,height:1,overflow:'hidden'});if(tinymce.isIE){rng=dom.doc.body.createTextRange();rng.moveToElementText(n);rng.execCommand('Paste');dom.remove(n);if(n.innerHTML==='\uFEFF'){ed.execCommand('mcePaste');e.preventDefault();return}process({content:n.innerHTML});return tinymce.dom.Event.cancel(e)}else{function block(e){e.preventDefault()};dom.bind(ed.getDoc(),'mousedown',block);dom.bind(ed.getDoc(),'keydown',block);or=ed.selection.getRng();n=n.firstChild;rng=ed.getDoc().createRange();rng.setStart(n,0);rng.setEnd(n,1);sel.setRng(rng);window.setTimeout(function(){var h='',nl=dom.select('div.mcePaste');each(nl,function(n){var child=n.firstChild;if(child&&child.nodeName=='DIV'&&child.style.marginTop&&child.style.backgroundColor){dom.remove(child,1)}each(dom.select('div.mcePaste',n),function(n){dom.remove(n,1)});each(dom.select('span.Apple-style-span',n),function(n){dom.remove(n,1)});each(dom.select('br[_mce_bogus]',n),function(n){dom.remove(n)});h+=n.innerHTML});each(nl,function(n){dom.remove(n)});if(or)sel.setRng(or);process({content:h});dom.unbind(ed.getDoc(),'mousedown',block);dom.unbind(ed.getDoc(),'keydown',block)},0)}};if(ed.getParam(ed,"paste_auto_cleanup_on_paste")){if(tinymce.isOpera||/Firefox\/2/.test(navigator.userAgent)){ed.onKeyDown.add(function(ed,e){if(((tinymce.isMac?e.metaKey:e.ctrlKey)&&e.keyCode==86)||(e.shiftKey&&e.keyCode==45)){return grabContent(e)}})}else{ed.onPaste.addToTop(function(ed,e){return grabContent(e)})}}if(ed.getParam('paste_block_drop')){ed.onInit.add(function(){ed.dom.bind(ed.getBody(),['dragend','dragover','draggesture','dragdrop','drop','drag'],function(e){e.preventDefault();e.stopPropagation();return false})})}each(['mcePasteText','mcePaste'],function(cmd){ed.addCommand(cmd,function(){t.command=cmd;if(ed.getParam('paste_use_dialog')){return t._openWin(cmd)}else{try{if(!ed.getDoc().queryCommandSupported('Paste')){return t._openWin(cmd)}else{return ed.getDoc().execCommand('Paste',false,null)}}catch(ex){return t._openWin(cmd)}}})});if(t.pasteHtml&&!t.pasteText){ed.addButton('paste',{title:'paste.paste_desc',cmd:'mcePaste',ui:true})}if(!t.pasteHtml&&t.pasteText){ed.addButton('paste',{title:'paste.paste_text_desc',cmd:'mcePasteText',ui:true,image:t.url+'/img/pastetext.gif'})}},createControl:function(n,cm){var t=this,ed=t.editor,doc=ed.getDoc();switch(n){case'paste':if(t.pasteHtml&&t.pasteText){var c=cm.createSplitButton('paste',{title:'paste.paste_desc',onclick:function(){ed.execCommand('mcePaste')}});c.onRenderMenu.add(function(c,m){m.add({title:'paste.paste_desc',icon:'paste',onclick:function(){ed.execCommand('mcePaste')}});m.add({title:'paste.paste_text_desc',icon:'pastetext',onclick:function(){ed.execCommand('mcePasteText')}})});return c}break}return null},getInfo:function(){return{longname:'Paste text/word',author:'Moxiecode Systems AB',authorurl:'http://tinymce.moxiecode.com',infourl:'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/paste',version:tinymce.majorVersion+"."+tinymce.minorVersion}},_openWin:function(cmd){var t=this,ed=this.editor;ed.windowManager.open({file:t.url+'/paste.htm',width:parseInt(ed.getParam("paste_dialog_width","450")),height:parseInt(ed.getParam("paste_dialog_height","400")),inline:1},{cmd:cmd})},_processRe:function(items,h){each(items,function(v){if(v.constructor==RegExp)h=h.replace(v,'');else h=h.replace(v[0],v[1])});return h},_preProcess:function(pl,o){var ed=this.editor,h=o.content;h=this._processRe([/^\s*(&nbsp;)+/g,/(&nbsp;|<br[^>]*>)+\s*$/g],h);if(/(content=\"OpenOffice.org[^\"]+\")/i.test(h)){o.wordContent=true;h=this._processRe([/[\s\S]+?<meta[^>]*>/,/<!--[\s\S]+?-->/gi,/<style[^>]*>[\s\S]+?<\/style>/gi],h)}if(/(class=\"?Mso|style=\"[^\"]*\bmso\-|w:WordDocument)/.test(h)){o.wordContent=true;h=this._processWordContent(h)}h=this._processRe([/<\/?(font|meta|link|style|title)[^>]*>/gi,[/&nbsp;/g,'\u00a0']],h);if(ed.getParam('paste_remove_spans')){h=this._processRe([/<\/?(span)[^>]*>/gi],h)}if(ed.getParam("force_p_newlines")){var r='',h=h.replace(/(\n\n|(<br[ \/]*>){2})/g,'__MCE_BREAK__');tinymce.each(h.split(/__MCE_BREAK__/g),function(v,k){r+='<p>'+v+'</p>'});h=r}o.content=h},_processWordContent:function(h){var ed=this.editor,stripClass;if(ed.getParam('paste_convert_middot_lists',true)&&!this.plainText){h=this._processRe([[/<!--\[if !supportLists\]-->/gi,'$&__MCE_ITEM__'],[/(<span[^>]+:\s*symbol[^>]+>)/gi,'$1__MCE_ITEM__'],[/(<span[^>]+mso-list:[^>]+>)/gi,'$1__MCE_ITEM__']],h)}h=this._processRe([/<!--[\s\S]+?-->/gi,/<(!|script[^>]*>.*?<\/script(?=[>\s])|\/?(\?xml(:\w+)?|meta|link|style|\w:\w+)(?=[\s\/>]))[^>]*>/gi,[/<(\/?)s>/gi,"<$1strike>"],[/&nbsp;/gi,"\u00a0"]],h);do{len=h.length;h=h.replace(/(<[a-z][^>]*\s)(?:id|language|type|on\w+|\w+:\w+)=(?:"[^"]*"|\w+)\s?/gi,"$1")}while(len!=h.length);if(!ed.getParam(ed,"paste_remove_styles")){h=this._processRe([[/<span\s+style\s*=\s*"\s*mso-spacerun\s*:\s*yes\s*;?\s*"\s*>([\s\u00a0]*)<\/span>/gi,function(str,spaces){return(spaces.length>0)?spaces.replace(/./," ").slice(Math.floor(spaces.length/2)).split("").join("\u00a0"):""}],[/(<[a-z][^>]*)\sstyle="([^"]*)"/gi,function(str,tag,style){var n=[],i=0,s=tinymce.explode(tinymce.trim(style).replace(/&quot;/gi,"'"),";");each(s,function(v){var name,value,parts=tinymce.explode(v,":");function ensureUnits(v){return v+((v!=="0")&&(/\d$/.test(v)))?"px":""}if(parts.length==2){name=parts[0].toLowerCase();value=parts[1].toLowerCase();switch(name){case"mso-padding-alt":case"mso-padding-top-alt":case"mso-padding-right-alt":case"mso-padding-bottom-alt":case"mso-padding-left-alt":case"mso-margin-alt":case"mso-margin-top-alt":case"mso-margin-right-alt":case"mso-margin-bottom-alt":case"mso-margin-left-alt":case"mso-table-layout-alt":case"mso-height":case"mso-width":case"mso-vertical-align-alt":n[i++]=name.replace(/^mso-|-alt$/g,"")+":"+ensureUnits(value);return;case"horiz-align":n[i++]="text-align:"+value;return;case"vert-align":n[i++]="vertical-align:"+value;return;case"font-color":case"mso-foreground":n[i++]="color:"+value;return;case"mso-background":case"mso-highlight":n[i++]="background:"+value;return;case"mso-default-height":n[i++]="min-height:"+ensureUnits(value);return;case"mso-default-width":n[i++]="min-width:"+ensureUnits(value);return;case"mso-padding-between-alt":n[i++]="border-collapse:separate;border-spacing:"+ensureUnits(value);return;case"text-line-through":if((value=="single")||(value=="double")){n[i++]="text-decoration:line-through"}return;case"mso-zero-height":if(value=="yes"){n[i++]="display:none"}return}if(/^(mso|column|font-emph|lang|layout|line-break|list-image|nav|panose|punct|row|ruby|sep|size|src|tab-|table-border|text-(?!align|decor|indent|trans)|top-bar|version|vnd|word-break)/.test(name)){return}n[i++]=name+":"+parts[1]}});if(i>0){return tag+' style="'+n.join(';')+'"'}else{return tag}}]],h)}if(ed.getParam(ed,"paste_convert_headers_to_strong")){h=this._processRe([[/<h[1-6][^>]*>/gi,"<p><strong>"],[/<\/h[1-6][^>]*>/gi,"</strong></p>"]],h)}stripClass=ed.getParam("paste_strip_class_attributes","all");if(stripClass!=="none"){function removeClasses(match,g1){if(stripClass==="all")return'';var cls=tinymce.grep(tinymce.explode(g1.replace(/^(["'])(.*)\1$/,"$2")," "),function(v){return(/^(?!mso)/i.test(v))});return cls.length?' class="'+cls.join(" ")+'"':''};h=h.replace(/ class="([^"]+)"/gi,removeClasses);h=h.replace(/ class=(\w+)/gi,removeClasses)}return h},_insertPlainText:function(ed,dom,h){var ed=this.editor,dom=ed.dom,i,len,pos,rpos,node,breakElms,before,after,w=ed.getWin(),d=ed.getDoc(),sel=ed.selection,entities=null;var rl=[[/\u2026/g,"..."],[/[\x93\x94\u201c\u201d]/g,'"'],[/[\x60\x91\x92\u2018\u2019]/g,"'"]];if((typeof(h)==="string")&&(h.length>0)){if(!entities)entities=("34,quot,38,amp,39,apos,60,lt,62,gt,"+ed.serializer.settings.entities).split(",");if(/<(?:p|br|h[1-6]|ul|ol|dl|table|t[rdh]|div|blockquote|fieldset|pre|address|center)[^>]*>/i.test(h)){h=h.replace(/[\n\r]+/g,'')}else{h=h.replace(/\r+/g,'')}h=this._processRe([[/<\/(?:p|h[1-6]|ul|ol|dl|table|div|blockquote|fieldset|pre|address|center)>/gi,"\n\n"],[/<br[^>]*>|<\/tr>/gi,"\n"],[/<\/t[dh]>\s*<t[dh][^>]*>/gi,"\t"],/<[a-z!\/?][^>]*>/gi,[/&nbsp;/gi," "],[/&(#\d+|[a-z0-9]{1,10});/gi,function(e,s){if(s.charAt(0)==="#"){return String.fromCharCode(s.slice(1))}else{return((e=tinymce.inArray(entities,s))>0)?String.fromCharCode(entities[e-1]):" "}}],[/(?:(?!\n)\s)*(\n+)(?:(?!\n)\s)*/gi,"$1"],[/\n{3,}/g,"\n\n"],/^\s+|\s+$/g],h);h=dom.encode(h);if(ed.getParam("force_p_newlines")){h=h.replace(/\n\n/g,'</p><p>').replace(/\n/g,'<br />')}else{h=h.replace(/\n/g,'<br />')}if(!sel.isCollapsed()){d.execCommand("Delete",false,null)}if(tinymce.is(rl,"array")||(tinymce.is(rl,"array"))){h=this._processRe(rl,h)}else if(tinymce.is(rl,"string")){h=this._processRe(new RegExp(rl,"gi"),h)}if((pos=h.indexOf("</p><p>"))!=-1){rpos=h.lastIndexOf("</p><p>");node=sel.getNode();breakElms=[];do{if(node.nodeType==1){if(node.nodeName=="TD"||node.nodeName=="BODY"){break}breakElms[breakElms.length]=node}}while(node=node.parentNode);if(breakElms.length>0){before=h.substring(0,pos);after="";for(i=0,len=breakElms.length;i<len;i++){before+="</"+breakElms[i].nodeName.toLowerCase()+">";after+="<"+breakElms[breakElms.length-i-1].nodeName.toLowerCase()+">"}if(pos==rpos){h=before+after+h.substring(pos+7)}else{h=before+h.substring(pos+4,rpos+4)+after+h.substring(rpos+7)}}}ed.execCommand("mceInsertRawHTML",false,h+'<span id="_plain_text_marker">&nbsp;</span>');window.setTimeout(function(){var marker=dom.get('_plain_text_marker'),elm,vp,y,elmHeight;sel.select(marker,false);d.execCommand("Delete",false,null);marker=null;elm=sel.getStart();vp=dom.getViewPort(w);y=dom.getPos(elm).y;elmHeight=elm.clientHeight;if((y<vp.y)||(y+elmHeight>vp.y+vp.h)){d.body.scrollTop=y<vp.y?y:y-vp.h+25}},0)}},_postProcess:function(pl,o){var t=this,ed=t.editor,dom=ed.dom;if(o.wordContent||!this.plainText){each(dom.select('a',o.node),function(a){if(!a.href||a.href.indexOf('#_Toc')!=-1)dom.remove(a,1)});if(ed.getParam('paste_remove_styles')||(ed.getParam("paste_remove_styles_if_webkit")&&tinymce.isWebKit)){each(dom.select('span',o.node),function(el){el.removeAttribute('style');el.removeAttribute('_mce_style')})}else{var s=ed.getParam('paste_retain_style_properties');if(s&&tinymce.is(s,'string')){styleProps=tinymce.explode(s)}if(t.editor.getParam('paste_convert_middot_lists',true)){t._convertLists(pl,o);styleProps.push('list-style-type')}each(dom.select('*',o.node),function(el){var ns={},x=0;var styles=ed.dom.parseStyle(el.style.cssText);each(styles,function(v,k){if(tinymce.inArray(styleProps,k)!=-1){ns[k]=v;x++}});dom.setAttrib(el,'style','');if(x>0){dom.setStyles(el,ns)}else{if(el.nodeName=='SPAN'&&!el.className){dom.remove(el,true)}}if(tinymce.isWebKit){el.removeAttribute('_mce_style')}})}each(dom.select('img',o.node),function(el){if(/file:\/\//.test(el.src)){dom.remove(el)}});each(dom.select('a[href*=#]',o.node),function(el){var href=el.href;dom.setAttrib(el,'href',href.substring(href.lastIndexOf('#')));if(el.name)dom.addClass(el,'mceItemAnchor')})}if(ed.getParam('paste_convert_urls',true)){var ex='([-!#$%&\'\*\+\\./0-9=?A-Z^_`a-z{|}~]+@[-!#$%&\'\*\+\\/0-9=?A-Z^_`a-z{|}~]+\.[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+)';var ux='((news|telnet|nttp|file|http|ftp|https)://[-!#$%&\'\*\+\\/0-9=?A-Z^_`a-z{|}~]+\.[-!#$%&\'\*\+\\./0-9=?A-Z^_`a-z{|}~]+)';function processRe(h){h=h.replace(new RegExp(ex,'g'),'<a href="mailto:$1">$1</a>');h=h.replace(new RegExp(ux,'g'),'<a href="$1">$1</a>');return h}each(dom.select('*:not(a)',o.node),function(el){if(!dom.getParent(el,'a')){each(el.childNodes,function(n){if(n&&n.nodeType==3){var s=n.innerText||n.textContent||n.data||null;if(s&&/(@|:\/\/)/.test(s)){if(s=processRe(s)){n.parentNode.innerHTML=s}}}})}})}if(ed.getParam('paste_remove_empty_paragraphs',true)){each(dom.select('p',o.node),function(el){if(el.innerHTML==''||el.innerHTML==' '||el.innerHTML=='&nbsp;'){if(ed.getParam('force_br_newlines')){dom.insertAfter(dom.create('br'),el)}dom.remove(el)}})}},_convertLists:function(pl,o){var dom=pl.editor.dom,listElm,li,lastMargin=-1,margin,levels=[],lastType;each(dom.select('p',o.node),function(p){var sib,val='',type,html,idx,parents,s,st;for(sib=p.firstChild;sib&&sib.nodeType==3;sib=sib.nextSibling)val+=sib.nodeValue;val=p.innerHTML.replace(/<\/?\w+[^>]*>/gi,'').replace(/&nbsp;/g,'\u00a0');if(/^(__MCE_ITEM__)+[\u2022\u00b7\u00a7\u00d8o]\s*\u00a0*/.test(val))type='ul';if(s=val.match(/^__MCE_ITEM__\s*\(*(\w+)\.*\)*\s*\u00a0{2,}/)){type='ol';s=tinymce.trim(s[1]);if(s){if(/0[1-9]/.test(s)){st='decimal-leading-zero'}if(/[a-z+?]/.test(s)){st='lower-alpha'}if(/[A-Z+?]/.test(s)){st='upper-alpha'}if(/[ivx+]/.test(s)){st='lower-roman'}if(/[IVX+]/.test(s)){st='upper-roman'}}}if(type){margin=parseFloat(p.style.marginLeft||0);if(margin>lastMargin)levels.push(margin);if(!listElm||type!=lastType){listElm=dom.create(type);dom.insertAfter(listElm,p)}else{if(margin>lastMargin){listElm=li.appendChild(dom.create(type))}else if(margin<lastMargin){idx=tinymce.inArray(levels,margin);parents=dom.getParents(listElm.parentNode,type);listElm=parents[parents.length-1-idx]||listElm}}each(dom.select('span',p),function(span){var html=span.innerHTML.replace(/<\/?\w+[^>]*>/gi,'');if(type=='ul'&&/^[\u2022\u00b7\u00a7\u00d8o]/.test(html))dom.remove(span);else if(/^[\s\S]*\w+\.(&nbsp;|\u00a0)*\s*/.test(html))dom.remove(span)});html=p.innerHTML;if(type=='ul')html=p.innerHTML.replace(/__MCE_ITEM__/g,'').replace(/^[\u2022\u00b7\u00a7\u00d8o]\s*(&nbsp;|\u00a0)+\s*/,'');else html=p.innerHTML.replace(/__MCE_ITEM__/g,'').replace(/^\s*\(*\w+\.*\)*(&nbsp;|\u00a0)+\s*/,'');li=listElm.appendChild(dom.create('li',0,html));dom.remove(p);if(st&&st!='undefined'){dom.setStyle(listElm,'list-style-type',st)}lastMargin=margin;lastType=type}else listElm=lastMargin=0});html=o.node.innerHTML;if(html.indexOf('__MCE_ITEM__')!=-1)o.node.innerHTML=html.replace(/__MCE_ITEM__/g,'')},_insertBlockContent:function(ed,dom,content){var parentBlock,marker,sel=ed.selection,last,elm,vp,y,elmHeight;function select(n){var r;if(tinymce.isIE){r=ed.getDoc().body.createTextRange();r.moveToElementText(n);r.collapse(false);r.select()}else{sel.select(n,1);sel.collapse(false)}};this._insert('<span id="_marker"></span>',1);marker=dom.get('_marker');parentBlock=dom.getParent(marker,'p,h1,h2,h3,h4,h5,h6,ul,ol,th,td');if(parentBlock&&!/TD|TH/.test(parentBlock.nodeName)){marker=dom.split(parentBlock,marker);each(dom.create('div',0,content).childNodes,function(n){last=marker.parentNode.insertBefore(n.cloneNode(true),marker)});select(last)}else{dom.setOuterHTML(marker,content);sel.select(ed.getBody(),1);sel.collapse(0)}dom.remove('_marker');elm=sel.getStart();vp=dom.getViewPort(ed.getWin());y=ed.dom.getPos(elm).y;elmHeight=elm.clientHeight;if(y<vp.y||y+elmHeight>vp.y+vp.h)ed.getDoc().body.scrollTop=y<vp.y?y:y-vp.h+25},_insert:function(h,skip_undo){var ed=this.editor;if(!ed.selection.isCollapsed())ed.getDoc().execCommand('Delete',false,null);ed.execCommand(tinymce.isGecko?'insertHTML':'mceInsertContent',false,h,{skip_undo:skip_undo})}});tinymce.PluginManager.add('paste',tinymce.plugins.PastePlugin)})();