var LinkrBookmarks={msg:[],setMsg:function(m){if($type(m)=='string'&&m.length>0){this.msg[this.msg.length]=m}else if($type(m)=='array'&&m.length>0){this.msg.merge(m)}},display:function(c,e){var h=new Element('div');this.setMsg(Linkr.getAllErrors());Linkr.insertMsgDiv(this.msg,h);this.msg=[];Linkr.htmlContent(h,c,false);if(['element','string'].contains($type(e))){h.injectInside($(e).setHTML(''))}else{Linkr.htmlLayout(this._('BOOKMARKS'),h)}},landing:function(){Linkr.delayIdleDiv('layout',this.home.bind(this))},home:function(a){Linkr.setReturnPage(['LinkrBookmarks','home']);if(this.isPluginInstalled===false){return this.display('',this._('NOTICE_INSTALL'))}if(this.loadedByDefault===true){this.setMsg(this._('NOTICE_BOOKMARKS'))}var c=new Element('div');var ls=[];ls.include(['tbc',this._('BM_CONFIG_TITLE')]);ls.include([false,this._('GET_BMS'),[this,'insert']]);Linkr.htmlTBLinks(ls).injectInside(c);Linkr.htmlConfig('',[['bmSize',this._('BM_CONFIG_SIZE'),[['text',this._('SIZE_TEXT')],['small',this._('SIZE_SMALL')],['large',this._('SIZE_LARGE')],['button',this._('SIZE_BTN')],['custom',this._('SIZE_CSTM')]]],['bmText',this._('BM_CONFIG_TXT'),[['yl',this._('TEXT_LEFT')],['yr',this._('TEXT_RIGHT')],['nn',this._('NO')]]],['bmPre',this._('BM_PRE_TXT')],['bmPost',this._('BM_POST_TXT')],['bmSep',this._('BM_CONFIG_SEP')]],this._('BM_CONFIG_TITLE')).injectInside(c);new Element('div',{id:'bookmarks'}).injectInside(c);this.display(c);Linkr.createToggleLink('settings','tbc');this.loadBookmarks(true,a)},loadBookmarks:function(bms,anchor){if(bms===true){Linkr.idleDiv('bookmarks',true);var u=Linkr.createRequest('bm');a=this.parseAnchor(anchor);if(a){u+='&s[size]='+(a.size||'text');u+='&s[text]='+(a.text||'nn');u+='&s[separator]='+encodeURIComponent((a.separator||' '));u+='&s[pre]='+encodeURIComponent((a.pre||''));u+='&s[post]='+encodeURIComponent((a.post||''));u+='&s[badges]='+(a.badges||'')}return Linkr.json(u,this.loadBookmarks.bind(this))}if($type(bms)!='object')return this.display('An error occured');if(Linkr.isError(bms))return this.display('Error: '+bms.msg);if($type(bms.bookmarks)=='string')return this.display(bms.bookmarks);if($type(bms.bookmarks)!='array')return this.display('An error occured');var cnt=[];$('bmSize').value=bms.size;$('bmText').value=bms.text;$('bmSep').value=Linkr.UTF8.Decode(Linkr.urldecode(bms.separator));$('bmPre').value=Linkr.UTF8.Decode(Linkr.urldecode(bms.pre));$('bmPost').value=Linkr.UTF8.Decode(Linkr.urldecode(bms.post));var list=new Element('div',{styles:{margin:'0 15px'}});var chk=new Element('input',{type:'checkbox','class':'bm-check',styles:{border:'none'},events:{click:function(e){$('badges').value=''}}});var badges=bms.badges;var blist=badges.split(',');var all=bms.badges=='*';var pop=bms.badges=='p';bms.bookmarks.each(function(b){var name=Linkr.UTF8.Decode(b.name);var div=new Element('div',{styles:{margin:2,width:130,height:20,'float':'left'}}).injectInside(list);var cBox=chk.clone().injectInside(div);cBox.setProperties({'id':b.id,'alt':(b.popular==1?'p':''),'checked':(all||(pop&&b.popular==1)||blist.contains(b.id))});var icon=new Element('span',{styles:{'margin-left':3}}).injectInside(div);var link=new Element('a',{href:'javascript:void(0);',events:{click:function(e){$('badges').value='';$(b.id).checked=!$(b.id).checked}}}).setHTML(name).injectInside(icon);if(b.icon.contains(Linkr.siteRoot)){new Element('img',{src:b.icon}).injectBefore(link);link.setStyle('margin-left',3)}});new Element('div',{styles:{clear:'both'}}).injectInside(list);cnt.include(list);var buttons=new Element('div',{styles:{padding:'10px 0 0 0','text-align':'center'}});new Element('input',{type:'button',value:this._('BM_CONFIG_SELALL'),styles:{'margin-right':5},events:{click:function(e){$('badges').value='*';$$('.bm-check').each(function(el){el.checked=true})}}}).injectInside(buttons);new Element('input',{type:'button',value:this._('BM_CONFIG_SELPOP'),styles:{'margin-right':5},events:{click:function(e){$('badges').value='p';$$('.bm-check').each(function(el){el.checked=el.getProperty('alt')=='p'})}}}).injectInside(buttons);new Element('input',{type:'button',value:this._('CLEAR'),events:{click:function(e){$('badges').value='';$$('.bm-check').each(function(el){el.checked=false})}}}).injectInside(buttons);cnt.include(buttons);cnt.include(new Element('input',{id:'badges',type:'hidden',value:(['*','p'].contains(bms.badges)?bms.badges:'')}));this.display(cnt,'bookmarks')},insert:function(){var badges=$('badges').value;switch(badges){case'*':case'p':break;default:var bs=[];$$('.bm-check').each(function(el){if(el.checked==true){bs.include(el.id)}});if(bs.length>0)badges=bs.join(',');else badges=''}if(badges.length<1){return false}var a='{linkr:bookmarks;';a+='size:'+$('bmSize').value+';';a+='text:'+$('bmText').value+';';var sep=$('bmSep').value;if(sep.length>0){sep=Linkr.urlencode(Linkr.UTF8.Encode(sep));a+='separator:'+sep+';'}var pre=$('bmPre').value;if(pre.length>0){pre=Linkr.urlencode(Linkr.UTF8.Encode(pre));a+='pre:'+pre+';'}var post=$('bmPost').value;if(post.length>0){post=Linkr.urlencode(Linkr.UTF8.Encode(post));a+='post:'+post+';'}a+='badges:'+badges+'}';rep=(Linkr.linkrAnchor==true);return rep?Linkr.insert(a):Linkr.insertAtEnd(a)},parseAnchor:function(anchor){if($type(anchor)!='string'||!anchor.contains('{linkr:bookmarks;')){return false}var a={};anchor=anchor.substr(anchor.indexOf('{')+1);anchor=anchor.substr(0,anchor.lastIndexOf('}'));pieces=anchor.split(';');pieces.each(function(kv){var kvp=kv.indexOf(':');if(kvp>0){var k=kv.substr(0,kvp);var v=kv.substr(kvp+1);v=Linkr.UTF8.Decode(Linkr.urldecode(v))}else{var k=kv;var v=true}a[k]=v});a.anchor=anchor;return a},_:function(text,args){return Linkr.getL18N(text,args)},dump:function(a){var dump=Linkr.dump(a,false,true);dump=dump.replace(/\n/g,'<br/>');dump=dump.replace(/\s/g,'&nbsp;');this.display(dump);return false}};