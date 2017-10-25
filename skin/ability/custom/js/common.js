/*--------------------------------------------------------
TOOLTIPS 
--------------------------------------------------------*/

/**
 * Tooltip plugin
 * http://onehackoranother.com/projects/jquery/tipsy
 */
(function($){$.fn.tipsy=function(options){options=$.extend({},$.fn.tipsy.defaults,options);return this.each(function(){var opts=$.fn.tipsy.elementOptions(this,options);$(this).hover(function(){$.data(this,'cancel.tipsy',true);var tip=$.data(this,'active.tipsy');if(!tip){tip=$('<div class="tipsy"><div class="tipsy-inner"/></div>');tip.css({position:'absolute',zIndex:100000});$.data(this,'active.tipsy',tip)}if($(this).attr('title')||typeof($(this).attr('original-title'))!='string'){$(this).attr('original-title',$(this).attr('title')||'').removeAttr('title')}var title;if(typeof opts.title=='string'){title=$(this).attr(opts.title=='title'?'original-title':opts.title)}else if(typeof opts.title=='function'){title=opts.title.call(this)}tip.find('.tipsy-inner')[opts.html?'html':'text'](title||opts.fallback);var pos=$.extend({},$(this).offset(),{width:this.offsetWidth,height:this.offsetHeight});tip.get(0).className='tipsy';tip.remove().css({top:0,left:0,visibility:'hidden',display:'block'}).appendTo(document.body);var actualWidth=tip[0].offsetWidth,actualHeight=tip[0].offsetHeight;var gravity=(typeof opts.gravity=='function')?opts.gravity.call(this):opts.gravity;switch(gravity.charAt(0)){case'n':tip.css({top:pos.top+pos.height,left:pos.left+pos.width/2-actualWidth/2}).addClass('tipsy-north');break;case's':tip.css({top:pos.top-actualHeight,left:pos.left+pos.width/2-actualWidth/2}).addClass('tipsy-south');break;case'e':tip.css({top:pos.top+pos.height/2-actualHeight/2,left:pos.left-actualWidth}).addClass('tipsy-east');break;case'w':tip.css({top:pos.top+pos.height/2-actualHeight/2,left:pos.left+pos.width}).addClass('tipsy-west');break}if(opts.fade){tip.css({opacity:0,display:'block',visibility:'visible'}).animate({opacity:0.8})}else{tip.css({visibility:'visible'})}},function(){$.data(this,'cancel.tipsy',false);var self=this;setTimeout(function(){if($.data(this,'cancel.tipsy'))return;var tip=$.data(self,'active.tipsy');if(opts.fade){tip.stop().fadeOut(function(){$(this).remove()})}else{tip.remove()}},100)})})};$.fn.tipsy.elementOptions=function(ele,options){return $.metadata?$.extend({},options,$(ele).metadata()):options};$.fn.tipsy.defaults={fade:false,fallback:'',gravity:'n',html:false,title:'title'};$.fn.tipsy.autoNS=function(){return $(this).offset().top>($(document).scrollTop()+$(window).height()/2)?'s':'n'};$.fn.tipsy.autoWE=function(){return $(this).offset().left>($(document).scrollLeft()+$(window).width()/2)?'e':'w'}})(jQuery);

////////////////////////////////
// INITIALISE TOOLTIPS
////////////////////////////////

$(function() {
  $('.ttip_n').tipsy({ gravity: 'n', html: true, opacity: 0.8, title: 'title' });
  $('.ttip_e').tipsy({ gravity: 'e', html: true, opacity: 0.8, title: 'title' });
  $('.ttip_s').tipsy({ gravity: 's', html: true, opacity: 0.8, title: 'title' });
  $('.ttip_w').tipsy({ gravity: 'w', html: true, opacity: 0.8, title: 'title' });
  $('.ttip_pat').tipsy({ gravity: 's', html: true, opacity: 0.8, title: 'rel' });
});

/*--------------------------------------------------------
TWITTER
--------------------------------------------------------*/

// jquery.tweet.js - See http://tweet.seaofclouds.com/ or https://github.com/seaofclouds/tweet for more info
// Copyright (c) 2008-2011 Todd Matthews & Steve Purcell

(function($){$.fn.tweet=function(o){var s=$.extend({username:null,list:null,favorites:false,query:null,avatar_size:null,count:3,fetch:null,page:1,retweets:true,intro_text:null,outro_text:null,join_text:null,auto_join_text_default:"i said,",auto_join_text_ed:"i",auto_join_text_ing:"i am",auto_join_text_reply:"i replied to",auto_join_text_url:"i was looking at",loading_text:null,refresh_interval:null,twitter_url:"twitter.com",twitter_api_url:"api.twitter.com",twitter_search_url:"search.twitter.com",template:"{avatar}{time}{join}{text}",comparator:function(tweet1,tweet2){return tweet2["tweet_time"]-tweet1["tweet_time"]},filter:function(tweet){return true}},o);var url_regexp=/\b((?:[a-z][\w-]+:(?:\/{1,3}|[a-z0-9%])|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:'".,<>?������]))/gi;function t(template,info){if(typeof template==="string"){var result=template;for(var key in info){var val=info[key];result=result.replace(new RegExp('{'+key+'}','g'),val===null?'':val)}return result}else return template(info)}$.extend({tweet:{t:t}});function replacer(regex,replacement){return function(){var returning=[];this.each(function(){returning.push(this.replace(regex,replacement))});return $(returning)}}function escapeHTML(s){return s.replace(/</g,"&lt;").replace(/>/g,"^&gt;")}$.fn.extend({linkUser:replacer(/(^|[\W])@(\w+)/gi,"$1@<a href=\"http://"+s.twitter_url+"/$2\">$2</a>"),linkHash:replacer(/(?:^| )[\#]+([\w\u00c0-\u00d6\u00d8-\u00f6\u00f8-\u00ff\u0600-\u06ff]+)/gi,' <a href="http://'+s.twitter_search_url+'/search?q=&tag=$1&lang=all'+((s.username&&s.username.length==1&&!s.list)?'&from='+s.username.join("%2BOR%2B"):'')+'">#$1</a>'),capAwesome:replacer(/\b(awesome)\b/gi,'<span class="awesome">$1</span>'),capEpic:replacer(/\b(epic)\b/gi,'<span class="epic">$1</span>'),makeHeart:replacer(/(&lt;)+[3]/gi,"<tt class='heart'>&#x2665;</tt>")});function linkURLs(text,entities){return text.replace(url_regexp,function(match){var url=(/^[a-z]+:/i).test(match)?match:"http://"+match;var text=match;for(var i=0;i<entities.length;++i){var entity=entities[i];if(entity.url==url&&entity.expanded_url){url=entity.expanded_url;text=entity.display_url;break}}return"<a href=\""+escapeHTML(url)+"\">"+escapeHTML(text)+"</a>"})}function parse_date(date_str){return Date.parse(date_str.replace(/^([a-z]{3})( [a-z]{3} \d\d?)(.*)( \d{4})$/i,'$1,$2$4$3'))}function relative_time(date){var relative_to=(arguments.length>1)?arguments[1]:new Date();var delta=parseInt((relative_to.getTime()-date)/1000,10);var r='';if(delta<60){r=delta+' seconds ago'}else if(delta<120){r='a minute ago'}else if(delta<(45*60)){r=(parseInt(delta/60,10)).toString()+' minutes ago'}else if(delta<(2*60*60)){r='an hour ago'}else if(delta<(24*60*60)){r=''+(parseInt(delta/3600,10)).toString()+' hours ago'}else if(delta<(48*60*60)){r='a day ago'}else{r=(parseInt(delta/86400,10)).toString()+' days ago'}return'about '+r}function build_auto_join_text(text){if(text.match(/^(@([A-Za-z0-9-_]+)) .*/i)){return s.auto_join_text_reply}else if(text.match(url_regexp)){return s.auto_join_text_url}else if(text.match(/^((\w+ed)|just) .*/im)){return s.auto_join_text_ed}else if(text.match(/^(\w*ing) .*/i)){return s.auto_join_text_ing}else{return s.auto_join_text_default}}function build_api_url(){var proto=('https:'==document.location.protocol?'https:':'http:');var count=(s.fetch===null)?s.count:s.fetch;var common_params='&include_entities=1&callback=?';if(s.list){return proto+"//"+s.twitter_api_url+"/1/"+s.username[0]+"/lists/"+s.list+"/statuses.json?page="+s.page+"&per_page="+count+common_params}else if(s.favorites){return proto+"//"+s.twitter_api_url+"/favorites/"+s.username[0]+".json?page="+s.page+"&count="+count+common_params}else if(s.query===null&&s.username.length==1){return proto+'//'+s.twitter_api_url+'/1/statuses/user_timeline.json?screen_name='+s.username[0]+'&count='+count+(s.retweets?'&include_rts=1':'')+'&page='+s.page+common_params}else{var query=(s.query||'from:'+s.username.join(' OR from:'));return proto+'//'+s.twitter_search_url+'/search.json?&q='+encodeURIComponent(query)+'&rpp='+count+'&page='+s.page+common_params}}function extract_avatar_url(item,secure){if(secure){return('user'in item)?item.user.profile_image_url_https:extract_avatar_url(item,false)}else{return item.profile_image_url||item.user.profile_image_url}}function extract_template_data(item){var o={};o.item=item;o.source=item.source;o.screen_name=item.from_user||item.user.screen_name;o.avatar_size=s.avatar_size;o.avatar_url=extract_avatar_url(item,(document.location.protocol==='https:'));o.retweet=typeof(item.retweeted_status)!='undefined';o.tweet_time=parse_date(item.created_at);o.join_text=s.join_text=="auto"?build_auto_join_text(item.text):s.join_text;o.tweet_id=item.id_str;o.twitter_base="http://"+s.twitter_url+"/";o.user_url=o.twitter_base+o.screen_name;o.tweet_url=o.user_url+"/status/"+o.tweet_id;o.reply_url=o.twitter_base+"intent/tweet?in_reply_to="+o.tweet_id;o.retweet_url=o.twitter_base+"intent/retweet?tweet_id="+o.tweet_id;o.favorite_url=o.twitter_base+"intent/favorite?tweet_id="+o.tweet_id;o.retweeted_screen_name=o.retweet&&item.retweeted_status.user.screen_name;o.tweet_relative_time=relative_time(o.tweet_time);o.entities=item.entities?(item.entities.urls||[]).concat(item.entities.media||[]):[];o.tweet_raw_text=o.retweet?('RT @'+o.retweeted_screen_name+' '+item.retweeted_status.text):item.text;o.tweet_text=$([linkURLs(o.tweet_raw_text,o.entities)]).linkUser().linkHash()[0];o.tweet_text_fancy=$([o.tweet_text]).makeHeart().capAwesome().capEpic()[0];o.user=t('<a class="tweet_user" href="{user_url}">{screen_name}</a>',o);o.join=s.join_text?t(' <span class="tweet_join">{join_text}</span> ',o):' ';o.avatar=o.avatar_size?t('<a class="tweet_avatar" href="{user_url}"><img src="{avatar_url}" height="{avatar_size}" width="{avatar_size}" alt="{screen_name}\'s avatar" title="{screen_name}\'s avatar" border="0"/></a>',o):'';o.time=t('<span class="tweet_time"><a href="{tweet_url}" title="view tweet on twitter">{tweet_relative_time}</a></span>',o);o.text=t('<span class="tweet_text">{tweet_text_fancy}</span>',o);o.reply_action=t('<a class="tweet_action tweet_reply" href="{reply_url}">reply</a>',o);o.retweet_action=t('<a class="tweet_action tweet_retweet" href="{retweet_url}">retweet</a>',o);o.favorite_action=t('<a class="tweet_action tweet_favorite" href="{favorite_url}">favorite</a>',o);return o}return this.each(function(i,widget){var list=$('<ul class="tweet_list">');var intro='<p class="tweet_intro">'+s.intro_text+'</p>';var outro='<p class="tweet_outro">'+s.outro_text+'</p>';var loading=$('<p class="loading">'+s.loading_text+'</p>');if(s.username&&typeof(s.username)=="string"){s.username=[s.username]}$(widget).bind("tweet:load",function(){if(s.loading_text)$(widget).empty().append(loading);$.getJSON(build_api_url(),function(data){$(widget).empty().append(list);if(s.intro_text)list.before(intro);list.empty();var tweets=$.map(data.results||data,extract_template_data);tweets=$.grep(tweets,s.filter).sort(s.comparator).slice(0,s.count);list.append($.map(tweets,function(o){return"<li>"+t(s.template,o)+"</li>"}).join('')).children('li:first').addClass('tweet_first').end().children('li:odd').addClass('tweet_even').end().children('li:even').addClass('tweet_odd');if(s.outro_text)list.after(outro);$(widget).trigger("loaded").trigger((tweets.length===0?"empty":"full"));if(s.refresh_interval){window.setTimeout(function(){$(widget).trigger("tweet:load")},1000*s.refresh_interval)}})}).trigger("tweet:load")})}})(jQuery);

/*--------------------------------------------------------
EASING
--------------------------------------------------------*/

/*
 * Easing Plugin
 */

jQuery.easing['jswing']=jQuery.easing['swing'];jQuery.extend(jQuery.easing,{def:'easeOutQuad',swing:function(x,t,b,c,d){return jQuery.easing[jQuery.easing.def](x,t,b,c,d)},easeInQuad:function(x,t,b,c,d){return c*(t/=d)*t+b},easeOutQuad:function(x,t,b,c,d){return-c*(t/=d)*(t-2)+b},easeInOutQuad:function(x,t,b,c,d){if((t/=d/2)<1)return c/2*t*t+b;return-c/2*((--t)*(t-2)-1)+b},easeInCubic:function(x,t,b,c,d){return c*(t/=d)*t*t+b},easeOutCubic:function(x,t,b,c,d){return c*((t=t/d-1)*t*t+1)+b},easeInOutCubic:function(x,t,b,c,d){if((t/=d/2)<1)return c/2*t*t*t+b;return c/2*((t-=2)*t*t+2)+b},easeInQuart:function(x,t,b,c,d){return c*(t/=d)*t*t*t+b},easeOutQuart:function(x,t,b,c,d){return-c*((t=t/d-1)*t*t*t-1)+b},easeInOutQuart:function(x,t,b,c,d){if((t/=d/2)<1)return c/2*t*t*t*t+b;return-c/2*((t-=2)*t*t*t-2)+b},easeInQuint:function(x,t,b,c,d){return c*(t/=d)*t*t*t*t+b},easeOutQuint:function(x,t,b,c,d){return c*((t=t/d-1)*t*t*t*t+1)+b},easeInOutQuint:function(x,t,b,c,d){if((t/=d/2)<1)return c/2*t*t*t*t*t+b;return c/2*((t-=2)*t*t*t*t+2)+b},easeInSine:function(x,t,b,c,d){return-c*Math.cos(t/d*(Math.PI/2))+c+b},easeOutSine:function(x,t,b,c,d){return c*Math.sin(t/d*(Math.PI/2))+b},easeInOutSine:function(x,t,b,c,d){return-c/2*(Math.cos(Math.PI*t/d)-1)+b},easeInExpo:function(x,t,b,c,d){return(t==0)?b:c*Math.pow(2,10*(t/d-1))+b},easeOutExpo:function(x,t,b,c,d){return(t==d)?b+c:c*(-Math.pow(2,-10*t/d)+1)+b},easeInOutExpo:function(x,t,b,c,d){if(t==0)return b;if(t==d)return b+c;if((t/=d/2)<1)return c/2*Math.pow(2,10*(t-1))+b;return c/2*(-Math.pow(2,-10*--t)+2)+b},easeInCirc:function(x,t,b,c,d){return-c*(Math.sqrt(1-(t/=d)*t)-1)+b},easeOutCirc:function(x,t,b,c,d){return c*Math.sqrt(1-(t=t/d-1)*t)+b},easeInOutCirc:function(x,t,b,c,d){if((t/=d/2)<1)return-c/2*(Math.sqrt(1-t*t)-1)+b;return c/2*(Math.sqrt(1-(t-=2)*t)+1)+b},easeInElastic:function(x,t,b,c,d){var s=1.70158;var p=0;var a=c;if(t==0)return b;if((t/=d)==1)return b+c;if(!p)p=d*.3;if(a<Math.abs(c)){a=c;var s=p/4}else var s=p/(2*Math.PI)*Math.asin(c/a);return-(a*Math.pow(2,10*(t-=1))*Math.sin((t*d-s)*(2*Math.PI)/p))+b},easeOutElastic:function(x,t,b,c,d){var s=1.70158;var p=0;var a=c;if(t==0)return b;if((t/=d)==1)return b+c;if(!p)p=d*.3;if(a<Math.abs(c)){a=c;var s=p/4}else var s=p/(2*Math.PI)*Math.asin(c/a);return a*Math.pow(2,-10*t)*Math.sin((t*d-s)*(2*Math.PI)/p)+c+b},easeInOutElastic:function(x,t,b,c,d){var s=1.70158;var p=0;var a=c;if(t==0)return b;if((t/=d/2)==2)return b+c;if(!p)p=d*(.3*1.5);if(a<Math.abs(c)){a=c;var s=p/4}else var s=p/(2*Math.PI)*Math.asin(c/a);if(t<1)return-.5*(a*Math.pow(2,10*(t-=1))*Math.sin((t*d-s)*(2*Math.PI)/p))+b;return a*Math.pow(2,-10*(t-=1))*Math.sin((t*d-s)*(2*Math.PI)/p)*.5+c+b},easeInBack:function(x,t,b,c,d,s){if(s==undefined)s=1.70158;return c*(t/=d)*t*((s+1)*t-s)+b},easeOutBack:function(x,t,b,c,d,s){if(s==undefined)s=1.70158;return c*((t=t/d-1)*t*((s+1)*t+s)+1)+b},easeInOutBack:function(x,t,b,c,d,s){if(s==undefined)s=1.70158;if((t/=d/2)<1)return c/2*(t*t*(((s*=(1.525))+1)*t-s))+b;return c/2*((t-=2)*t*(((s*=(1.525))+1)*t+s)+2)+b},easeInBounce:function(x,t,b,c,d){return c-jQuery.easing.easeOutBounce(x,d-t,0,c,d)+b},easeOutBounce:function(x,t,b,c,d){if((t/=d)<(1/2.75)){return c*(7.5625*t*t)+b}else if(t<(2/2.75)){return c*(7.5625*(t-=(1.5/2.75))*t+.75)+b}else if(t<(2.5/2.75)){return c*(7.5625*(t-=(2.25/2.75))*t+.9375)+b}else{return c*(7.5625*(t-=(2.625/2.75))*t+.984375)+b}},easeInOutBounce:function(x,t,b,c,d){if(t<d/2)return jQuery.easing.easeInBounce(x,t*2,0,c,d)*.5+b;return jQuery.easing.easeOutBounce(x,t*2-d,0,c,d)*.5+c*.5+b}});

/*--------------------------------------------------------
MOUSE WHEEL
--------------------------------------------------------*/

/*! Copyright (c) 2011 Brandon Aaron (http://brandonaaron.net)
 * Licensed under the MIT License (LICENSE.txt).
 *
 * Thanks to: http://adomas.org/javascript-mouse-wheel/ for some pointers.
 * Thanks to: Mathias Bank(http://www.mathias-bank.de) for a scope bug fix.
 * Thanks to: Seamus Leahy for adding deltaX and deltaY
 *
 * Version: 3.0.6
 * 
 * Requires: 1.2.2+
 */

(function($){var types=['DOMMouseScroll','mousewheel'];if($.event.fixHooks){for(var i=types.length;i;){$.event.fixHooks[types[--i]]=$.event.mouseHooks}}$.event.special.mousewheel={setup:function(){if(this.addEventListener){for(var i=types.length;i;){this.addEventListener(types[--i],handler,false)}}else{this.onmousewheel=handler}},teardown:function(){if(this.removeEventListener){for(var i=types.length;i;){this.removeEventListener(types[--i],handler,false)}}else{this.onmousewheel=null}}};$.fn.extend({mousewheel:function(fn){return fn?this.bind("mousewheel",fn):this.trigger("mousewheel")},unmousewheel:function(fn){return this.unbind("mousewheel",fn)}});function handler(event){var orgEvent=event||window.event,args=[].slice.call(arguments,1),delta=0,returnValue=true,deltaX=0,deltaY=0;event=$.event.fix(orgEvent);event.type="mousewheel";if(orgEvent.wheelDelta){delta=orgEvent.wheelDelta/120}if(orgEvent.detail){delta=-orgEvent.detail/3}deltaY=delta;if(orgEvent.axis!==undefined&&orgEvent.axis===orgEvent.HORIZONTAL_AXIS){deltaY=0;deltaX=-1*delta}if(orgEvent.wheelDeltaY!==undefined){deltaY=orgEvent.wheelDeltaY/120}if(orgEvent.wheelDeltaX!==undefined){deltaX=-1*orgEvent.wheelDeltaX/120}args.unshift(event,delta,deltaX,deltaY);return($.event.dispatch||$.event.handle).apply(this,args)}})(jQuery);

/*--------------------------------------------------------
MOUSE WHEEL INTENT
--------------------------------------------------------*/

/**
 * @author trixta
 * @version 1.2
 */
(function($){var mwheelI={pos:[-260,-260]},minDif=3,doc=document,root=doc.documentElement,body=doc.body,longDelay,shortDelay;function unsetPos(){if(this===mwheelI.elem){mwheelI.pos=[-260,-260];mwheelI.elem=false;minDif=3}}$.event.special.mwheelIntent={setup:function(){var jElm=$(this).bind('mousewheel',$.event.special.mwheelIntent.handler);if(this!==doc&&this!==root&&this!==body){jElm.bind('mouseleave',unsetPos)}jElm=null;return true},teardown:function(){$(this).unbind('mousewheel',$.event.special.mwheelIntent.handler).unbind('mouseleave',unsetPos);return true},handler:function(e,d){var pos=[e.clientX,e.clientY];if(this===mwheelI.elem||Math.abs(mwheelI.pos[0]-pos[0])>minDif||Math.abs(mwheelI.pos[1]-pos[1])>minDif){mwheelI.elem=this;mwheelI.pos=pos;minDif=250;clearTimeout(shortDelay);shortDelay=setTimeout(function(){minDif=10},200);clearTimeout(longDelay);longDelay=setTimeout(function(){minDif=3},1500);e=$.extend({},e,{type:'mwheelIntent'});return $.event.handle.apply(this,arguments)}}};$.fn.extend({mwheelIntent:function(fn){return fn?this.bind("mwheelIntent",fn):this.trigger("mwheelIntent")},unmwheelIntent:function(fn){return this.unbind("mwheelIntent",fn)}});$(function(){body=doc.body;$(doc).bind('mwheelIntent.mwheelIntentDefault',$.noop)})})(jQuery);

/*--------------------------------------------------------
CAROUSELS
--------------------------------------------------------*/

/*	
 *	jQuery carouFredSel 4.5.1
 *	Demo's and documentation:
 *	caroufredsel.frebsite.nl
 *	
 *	Copyright (c) 2011 Fred Heusschen
 *	www.frebsite.nl
 *
 *	Dual licensed under the MIT and GPL licenses.
 *	http://en.wikipedia.org/wiki/MIT_License
 *	http://en.wikipedia.org/wiki/GNU_General_Public_License
 */

(function($){if($.fn.carouFredSel)return;$.fn.carouFredSel=function(options,configs){if(this.length==0){debug(true,'No element found for "'+this.selector+'".');return this}if(this.length>1){return this.each(function(){$(this).carouFredSel(options,configs)})}var $cfs=this,$tt0=this[0];if($cfs.data('cfs_isCarousel')){var starting_position=$cfs.triggerHandler('_cfs_currentPosition');$cfs.trigger('_cfs_destroy',true)}else{var starting_position=false}$cfs._cfs_init=function(o,setOrig,start){o=go_getObject($tt0,o);if(o.debug){conf.debug=o.debug;debug(conf,'The "debug" option should be moved to the second configuration-object.')}var obs=['items','scroll','auto','prev','next','pagination'];for(var a=0,l=obs.length;a<l;a++){o[obs[a]]=go_getObject($tt0,o[obs[a]])}if(typeof o.scroll=='number'){if(o.scroll<=50)o.scroll={'items':o.scroll};else o.scroll={'duration':o.scroll}}else{if(typeof o.scroll=='string')o.scroll={'easing':o.scroll}}if(typeof o.items=='number')o.items={'visible':o.items};else if(o.items=='variable')o.items={'visible':o.items,'width':o.items,'height':o.items};if(typeof o.items!='object')o.items={};if(setOrig)opts_orig=$.extend(true,{},$.fn.carouFredSel.defaults,o);opts=$.extend(true,{},$.fn.carouFredSel.defaults,o);if(typeof opts.items.visibleConf!='object')opts.items.visibleConf={};if(opts.items.start==0&&typeof start=='number'){opts.items.start=start}crsl.direction=(opts.direction=='up'||opts.direction=='left')?'next':'prev';var dims=[['width','innerWidth','outerWidth','height','innerHeight','outerHeight','left','top','marginRight',0,1,2,3],['height','innerHeight','outerHeight','width','innerWidth','outerWidth','top','left','marginBottom',3,2,1,0]];var dn=dims[0].length,dx=(opts.direction=='right'||opts.direction=='left')?0:1;opts.d={};for(var d=0;d<dn;d++){opts.d[dims[0][d]]=dims[dx][d]}var all_itm=$cfs.children();if(typeof opts.items.filter=='undefined'){opts.items.filter=(all_itm.filter(':hidden').length>0)?':visible':'*'}if(opts[opts.d['width']]=='auto'){var lrgst=ms_getTrueLargestSize(all_itm,opts,'outerWidth');opts[opts.d['width']]=lrgst}if(opts[opts.d['height']]=='auto'){var lrgst=ms_getTrueLargestSize(all_itm,opts,'outerHeight');opts[opts.d['height']]=lrgst}if(!opts.items[opts.d['width']]){opts.items[opts.d['width']]=(ms_hasVariableSizes(all_itm,opts,'outerWidth'))?'variable':all_itm[opts.d['outerWidth']](true)}if(!opts.items[opts.d['height']]){opts.items[opts.d['height']]=(ms_hasVariableSizes(all_itm,opts,'outerHeight'))?'variable':all_itm[opts.d['outerHeight']](true)}if(!opts[opts.d['height']]){opts[opts.d['height']]=opts.items[opts.d['height']]}if(typeof opts.items.visible=='object'){opts.items.visibleConf.min=opts.items.visible.min;opts.items.visibleConf.max=opts.items.visible.max;opts.items.visible=false}if(typeof opts.items.visible=='string'||typeof opts.items.visible=='function'){opts.items.visibleConf.adjust=opts.items.visible;opts.items.visible=false}if(!opts.items.visible){if(opts.items[opts.d['width']]=='variable'){opts.items.visibleConf.variable=true}if(!opts.items.visibleConf.variable){if(typeof opts[opts.d['width']]=='number'){opts.items.visible=Math.floor(opts[opts.d['width']]/opts.items[opts.d['width']])}else{var maxS=ms_getTrueInnerSize($wrp.parent(),opts,'innerWidth');opts.items.visible=Math.floor(maxS/opts.items[opts.d['width']]);opts[opts.d['width']]=opts.items.visible*opts.items[opts.d['width']];if(!opts.items.visibleConf.adjust)opts.align=false}if(opts.items.visible=='Infinity'||opts.items.visible<1){debug(true,'Not a valid number of visible items: Set to "variable".');opts.items.visibleConf.variable=true}}}if(!opts[opts.d['width']]){if(opts.items.filter!='*'){opts[opts.d['width']]='variable'}else if(!opts.items.visibleConf.variable&&opts.items[opts.d['width']]!='variable'){opts[opts.d['width']]=opts.items.visible*opts.items[opts.d['width']];opts.align=false}else{opts[opts.d['width']]='variable'}}if(opts.items.visibleConf.variable){opts.maxDimention=(opts[opts.d['width']]=='variable')?ms_getTrueInnerSize($wrp.parent(),opts,'innerWidth'):opts[opts.d['width']];if(opts.align===false){opts[opts.d['width']]='variable'}opts.items.visible=gn_getVisibleItemsNext(all_itm,opts,0)}else if(opts.items.filter!='*'){opts.items.visibleConf.org=opts.items.visible;opts.items.visible=gn_getVisibleItemsNextFilter(all_itm,opts,0)}if(typeof opts.padding=='undefined'){opts.padding=0}if(typeof opts.align=='undefined'){opts.align=(opts[opts.d['width']]=='variable')?false:'center'}opts.items.visible=cf_getItemsAdjust(opts.items.visible,opts,opts.items.visibleConf.adjust,$tt0);opts.items.visibleConf.old=opts.items.visible;opts.usePadding=false;opts.padding=cf_getPadding(opts.padding);if(opts.align=='top')opts.align='left';if(opts.align=='bottom')opts.align='right';switch(opts.align){case'center':case'left':case'right':if(opts[opts.d['width']]!='variable'){var p=cf_getAlignPadding(gi_getCurrentItems(all_itm,opts),opts);opts.usePadding=true;opts.padding[opts.d[1]]=p[1];opts.padding[opts.d[3]]=p[0]}break;default:opts.align=false;opts.usePadding=(opts.padding[0]==0&&opts.padding[1]==0&&opts.padding[2]==0&&opts.padding[3]==0)?false:true;break}if(typeof opts.cookie=='boolean'&&opts.cookie)opts.cookie='caroufredsel_cookie_'+$cfs.attr('id');if(typeof opts.items.minimum!='number')opts.items.minimum=opts.items.visible;if(typeof opts.scroll.duration!='number')opts.scroll.duration=500;if(typeof opts.scroll.items=='undefined')opts.scroll.items=(opts.items.visibleConf.variable||opts.items.filter!='*')?'visible':opts.items.visible;opts.auto=go_getNaviObject($tt0,opts.auto,'auto');opts.prev=go_getNaviObject($tt0,opts.prev);opts.next=go_getNaviObject($tt0,opts.next);opts.pagination=go_getNaviObject($tt0,opts.pagination,'pagination');opts.auto=$.extend(true,{},opts.scroll,opts.auto);opts.prev=$.extend(true,{},opts.scroll,opts.prev);opts.next=$.extend(true,{},opts.scroll,opts.next);opts.pagination=$.extend(true,{},opts.scroll,opts.pagination);if(typeof opts.pagination.keys!='boolean')opts.pagination.keys=false;if(typeof opts.pagination.anchorBuilder!='function'&&opts.pagination.anchorBuilder!==false)opts.pagination.anchorBuilder=$.fn.carouFredSel.pageAnchorBuilder;if(typeof opts.auto.play!='boolean')opts.auto.play=true;if(typeof opts.auto.delay!='number')opts.auto.delay=0;if(typeof opts.auto.pauseDuration!='number')opts.auto.pauseDuration=(opts.auto.duration<10)?2500:opts.auto.duration*5;if(opts.synchronise){opts.synchronise=cf_getSynchArr(opts.synchronise)}if(conf.debug){debug(conf,'Carousel width: '+opts.width);debug(conf,'Carousel height: '+opts.height);if(opts.maxDimention)debug(conf,'Available '+opts.d['width']+': '+opts.maxDimention);debug(conf,'Item widths: '+opts.items.width);debug(conf,'Item heights: '+opts.items.height);debug(conf,'Number of items visible: '+opts.items.visible);if(opts.auto.play)debug(conf,'Number of items scrolled automatically: '+opts.auto.items);if(opts.prev.button)debug(conf,'Number of items scrolled backward: '+opts.prev.items);if(opts.next.button)debug(conf,'Number of items scrolled forward: '+opts.next.items)}};$cfs._cfs_build=function(){$cfs.data('cfs_isCarousel',true);var orgCSS={'textAlign':$cfs.css('textAlign'),'float':$cfs.css('float'),'position':$cfs.css('position'),'top':$cfs.css('top'),'right':$cfs.css('right'),'bottom':$cfs.css('bottom'),'left':$cfs.css('left'),'width':$cfs.css('width'),'height':$cfs.css('height'),'marginTop':$cfs.css('marginTop'),'marginRight':$cfs.css('marginRight'),'marginBottom':$cfs.css('marginBottom'),'marginLeft':$cfs.css('marginLeft')};switch(orgCSS.position){case'absolute':var newPosition='absolute';break;case'fixed':var newPosition='fixed';break;default:var newPosition='relative'}$wrp.css(orgCSS).css({'overflow':'hidden','position':newPosition});$cfs.data('cfs_origCss',orgCSS).css({'textAlign':'left','float':'none','position':'absolute','top':0,'left':0,'marginTop':0,'marginRight':0,'marginBottom':0,'marginLeft':0});if(opts.usePadding){$cfs.children().each(function(){var m=parseInt($(this).css(opts.d['marginRight']));if(isNaN(m))m=0;$(this).data('cfs_origCssMargin',m)})}};$cfs._cfs_bind_events=function(){$cfs._cfs_unbind_events();$cfs.bind(cf_e('stop',conf),function(e,imm){e.stopPropagation();if(!crsl.isStopped){if(opts.auto.button){opts.auto.button.addClass(cf_c('stopped',conf))}}crsl.isStopped=true;if(opts.auto.play){opts.auto.play=false;$cfs.trigger(cf_e('pause',conf),imm)}return true});$cfs.bind(cf_e('finish',conf),function(e){e.stopPropagation();if(crsl.isScrolling){sc_stopScroll(scrl)}return true});$cfs.bind(cf_e('pause',conf),function(e,imm,res){e.stopPropagation();tmrs=sc_clearTimers(tmrs);if(imm&&crsl.isScrolling){scrl.isStopped=true;var nst=getTime()-scrl.startTime;scrl.duration-=nst;if(scrl.pre)scrl.pre.duration-=nst;if(scrl.post)scrl.post.duration-=nst;sc_stopScroll(scrl,false)}if(!crsl.isPaused&&!crsl.isScrolling){if(res)tmrs.timePassed+=getTime()-tmrs.startTime}if(!crsl.isPaused){if(opts.auto.button){opts.auto.button.addClass(cf_c('paused',conf))}}crsl.isPaused=true;if(opts.auto.onPausePause){var dur1=opts.auto.pauseDuration-tmrs.timePassed,perc=100-Math.ceil(dur1*100/opts.auto.pauseDuration);opts.auto.onPausePause.call($tt0,perc,dur1)}return true});$cfs.bind(cf_e('play',conf),function(e,dir,del,res){e.stopPropagation();tmrs=sc_clearTimers(tmrs);var v=[dir,del,res],t=['string','number','boolean'],a=cf_sortParams(v,t);var dir=a[0],del=a[1],res=a[2];if(dir!='prev'&&dir!='next')dir=crsl.direction;if(typeof del!='number')del=0;if(typeof res!='boolean')res=false;if(res){crsl.isStopped=false;opts.auto.play=true}if(!opts.auto.play){e.stopImmediatePropagation();return debug(conf,'Carousel stopped: Not scrolling.')}if(crsl.isPaused){if(opts.auto.button){opts.auto.button.removeClass(cf_c('stopped',conf));opts.auto.button.removeClass(cf_c('paused',conf))}}crsl.isPaused=false;tmrs.startTime=getTime();var dur1=opts.auto.pauseDuration+del;dur2=dur1-tmrs.timePassed;perc=100-Math.ceil(dur2*100/dur1);tmrs.auto=setTimeout(function(){if(opts.auto.onPauseEnd){opts.auto.onPauseEnd.call($tt0,perc,dur2)}if(crsl.isScrolling){$cfs.trigger(cf_e('play',conf),dir)}else{$cfs.trigger(cf_e(dir,conf),opts.auto)}},dur2);if(opts.auto.onPauseStart){opts.auto.onPauseStart.call($tt0,perc,dur2)}return true});$cfs.bind(cf_e('resume',conf),function(e){e.stopPropagation();if(scrl.isStopped){scrl.isStopped=false;crsl.isPaused=false;crsl.isScrolling=true;scrl.startTime=getTime();sc_startScroll(scrl)}else{$cfs.trigger(cf_e('play',conf))}return true});$cfs.bind(cf_e('prev',conf)+' '+cf_e('next',conf),function(e,obj,num,clb){e.stopPropagation();if(crsl.isStopped||$cfs.is(':hidden')){e.stopImmediatePropagation();return debug(conf,'Carousel stopped or hidden: Not scrolling.')}if(opts.items.minimum>=itms.total){e.stopImmediatePropagation();return debug(conf,'Not enough items ('+itms.total+', '+opts.items.minimum+' needed): Not scrolling.')}var v=[obj,num,clb],t=['object','number/string','function'],a=cf_sortParams(v,t);var obj=a[0],num=a[1],clb=a[2];var eType=e.type.substr(conf.events.prefix.length);if(typeof obj!='object'||obj==null)obj=opts[eType];if(typeof clb=='function')obj.onAfter=clb;if(typeof num!='number'){if(opts.items.filter!='*'){num='visible'}else{var arr=[num,obj.items,opts[eType].items];for(var a=0,l=arr.length;a<l;a++){if(typeof arr[a]=='number'||arr[a]=='page'||arr[a]=='visible'){num=arr[a];break}}}switch(num){case'page':e.stopImmediatePropagation();return $cfs.triggerHandler(eType+'Page',[obj,clb]);break;case'visible':if(!opts.items.visibleConf.variable&&opts.items.filter=='*'){num=opts.items.visible}break}}if(scrl.isStopped){$cfs.trigger(cf_e('resume',conf));$cfs.trigger(cf_e('queue',conf),[eType,[obj,num,clb]]);e.stopImmediatePropagation();return debug(conf,'Carousel resumed scrolling.')}if(obj.duration>0){if(crsl.isScrolling){if(obj.queue)$cfs.trigger(cf_e('queue',conf),[eType,[obj,num,clb]]);e.stopImmediatePropagation();return debug(conf,'Carousel currently scrolling.')}}if(obj.conditions&&!obj.conditions.call($tt0)){e.stopImmediatePropagation();return debug(conf,'Callback "conditions" returned false.')}tmrs.timePassed=0;$cfs.trigger('_cfs_slide_'+eType,[obj,num]);if(opts.synchronise){var s=opts.synchronise,c=[obj,num];for(var j=0,l=s.length;j<l;j++){var d=eType;if(!s[j][1])c[0]=s[j][0].triggerHandler('_cfs_configuration',eType);if(!s[j][2])d=(d=='prev')?'next':'prev';c[1]=num+s[j][3];s[j][0].trigger('_cfs_slide_'+d,c)}}return true});$cfs.bind(cf_e('_cfs_slide_prev',conf,false),function(e,sO,nI){e.stopPropagation();var a_itm=$cfs.children();if(!opts.circular){if(itms.first==0){if(opts.infinite){$cfs.trigger(cf_e('next',conf),itms.total-1)}return e.stopImmediatePropagation()}}if(opts.usePadding)sz_resetMargin(a_itm,opts);if(typeof nI!='number'){if(opts.items.visibleConf.variable){nI=gn_getVisibleItemsPrev(a_itm,opts,itms.total-1)}else if(opts.items.filter!='*'){var xI=(typeof sO.items=='number')?sO.items:gn_getVisibleOrg($cfs,opts);nI=gn_getScrollItemsPrevFilter(a_itm,opts,itms.total-1,xI)}else{nI=opts.items.visible}nI=cf_getAdjust(nI,opts,sO.items,$tt0)}if(!opts.circular){if(itms.total-nI<itms.first){nI=itms.total-itms.first}}if(opts.items.visibleConf.variable){var vI=gn_getVisibleItemsNext(a_itm,opts,itms.total-nI);if(opts.items.visible+nI<=vI&&nI<itms.total){nI++;vI=gn_getVisibleItemsNext(a_itm,opts,itms.total-nI)}opts.items.visibleConf.old=opts.items.visible;opts.items.visible=cf_getItemsAdjust(vI,opts,opts.items.visibleConf.adjust,$tt0)}else if(opts.items.filter!='*'){var vI=gn_getVisibleItemsNextFilter(a_itm,opts,itms.total-nI);opts.items.visibleConf.old=opts.items.visible;opts.items.visible=cf_getItemsAdjust(vI,opts,opts.items.visibleConf.adjust,$tt0)}if(opts.usePadding)sz_resetMargin(a_itm,opts,true);if(nI==0){e.stopImmediatePropagation();return debug(conf,'0 items to scroll: Not scrolling.')}debug(conf,'Scrolling '+nI+' items backward.');itms.first+=nI;while(itms.first>=itms.total){itms.first-=itms.total}if(!opts.circular){if(itms.first==0&&sO.onEnd)sO.onEnd.call($tt0);if(!opts.infinite)nv_enableNavi(opts,itms.first,conf)}$cfs.children().slice(itms.total-nI,itms.total).prependTo($cfs);if(itms.total<opts.items.visible+nI){$cfs.children().slice(0,(opts.items.visible+nI)-itms.total).clone(true).appendTo($cfs)}var a_itm=$cfs.children(),c_old=gi_getOldItemsPrev(a_itm,opts,nI),c_new=gi_getNewItemsPrev(a_itm,opts),l_cur=a_itm.eq(nI-1),l_old=c_old.last(),l_new=c_new.last();if(opts.usePadding)sz_resetMargin(a_itm,opts);if(opts.align){var p=cf_getAlignPadding(c_new,opts),pL=p[0],pR=p[1]}else{var pL=0,pR=0}var oL=(pL<0)?opts.padding[opts.d[3]]:0;if(sO.fx=='directscroll'&&opts.items.visible<nI){var hiddenitems=a_itm.slice(opts.items.visibleConf.old,nI),orgW=opts.items[opts.d['width']];hiddenitems.each(function(){var hi=$(this);hi.data('isHidden',hi.is(':hidden')).hide()});opts.items[opts.d['width']]='variable'}else{var hiddenitems=false}var i_siz=ms_getTotalSize(a_itm.slice(0,nI),opts,'width'),w_siz=cf_mapWrapperSizes(ms_getSizes(c_new,opts,true),opts,!opts.usePadding);if(hiddenitems)opts.items[opts.d['width']]=orgW;if(opts.usePadding){sz_resetMargin(a_itm,opts,true);if(pR>=0){sz_resetMargin(l_old,opts,opts.padding[opts.d[1]])}sz_resetMargin(l_cur,opts,opts.padding[opts.d[3]])}if(opts.align){opts.padding[opts.d[1]]=pR;opts.padding[opts.d[3]]=pL}var a_cfs={},a_dur=sO.duration;if(sO.fx=='none')a_dur=0;else if(a_dur=='auto')a_dur=opts.scroll.duration/opts.scroll.items*nI;else if(a_dur<=0)a_dur=0;else if(a_dur<10)a_dur=i_siz/a_dur;scrl=sc_setScroll(a_dur,sO.easing);if(opts[opts.d['width']]=='variable'||opts[opts.d['height']]=='variable'){scrl.anims.push([$wrp,w_siz])}if(opts.usePadding){var new_m=opts.padding[opts.d[3]];if(l_new.not(l_cur).length){var a_cur={};a_cur[opts.d['marginRight']]=l_cur.data('cfs_origCssMargin');if(pL<0)l_cur.css(a_cur);else scrl.anims.push([l_cur,a_cur])}if(l_new.not(l_old).length){var a_old={};a_old[opts.d['marginRight']]=l_old.data('cfs_origCssMargin');scrl.anims.push([l_old,a_old])}if(pR>=0){var a_new={};a_new[opts.d['marginRight']]=l_new.data('cfs_origCssMargin')+opts.padding[opts.d[1]];scrl.anims.push([l_new,a_new])}}else{var new_m=0}a_cfs[opts.d['left']]=new_m;var args=[c_old,c_new,w_siz,a_dur];if(sO.onBefore)sO.onBefore.apply($tt0,args);clbk.onBefore=sc_callCallbacks(clbk.onBefore,$tt0,args);switch(sO.fx){case'fade':case'crossfade':case'cover':case'uncover':scrl.pre=sc_setScroll(scrl.duration,scrl.easing);scrl.post=sc_setScroll(scrl.duration,scrl.easing);scrl.duration=0;break}switch(sO.fx){case'crossfade':case'cover':case'uncover':var $cf2=$cfs.clone().appendTo($wrp);break}switch(sO.fx){case'uncover':$cf2.children().slice(0,nI).remove();case'crossfade':case'cover':$cf2.children().slice(opts.items.visible).remove();break}switch(sO.fx){case'fade':scrl.pre.anims.push([$cfs,{'opacity':0}]);break;case'crossfade':$cf2.css({'opacity':0});scrl.pre.anims.push([$cfs,{'width':'+=0'},function(){$cf2.remove()}]);scrl.post.anims.push([$cf2,{'opacity':1}]);break;case'cover':scrl=fx_cover(scrl,$cfs,$cf2,opts,true);break;case'uncover':scrl=fx_uncover(scrl,$cfs,$cf2,opts,true,nI);break}var a_complete=function(){var overFill=opts.items.visible+nI-itms.total;if(overFill>0){$cfs.children().slice(itms.total).remove();c_old=$cfs.children().slice(itms.total-(nI-overFill)).get().concat($cfs.children().slice(0,overFill).get())}if(hiddenitems){hiddenitems.each(function(){var hi=$(this);if(!hi.data('isHidden'))hi.show()})}if(opts.usePadding){var l_itm=$cfs.children().eq(opts.items.visible+nI-1);l_itm.css(opts.d['marginRight'],l_itm.data('cfs_origCssMargin'))}scrl.anims=[];if(scrl.pre)scrl.pre=sc_setScroll(scrl.orgDuration,scrl.easing);var fn=function(){switch(sO.fx){case'fade':case'crossfade':$cfs.css('filter','');break}scrl.post=sc_setScroll(0,null);crsl.isScrolling=false;var args=[c_old,c_new,w_siz];if(sO.onAfter)sO.onAfter.apply($tt0,args);clbk.onAfter=sc_callCallbacks(clbk.onAfter,$tt0,args);if(queu.length){$cfs.trigger(cf_e(queu[0][0],conf),queu[0][1]);queu.shift()}if(!crsl.isPaused)$cfs.trigger(cf_e('play',conf))};switch(sO.fx){case'fade':scrl.pre.anims.push([$cfs,{'opacity':1},fn]);sc_startScroll(scrl.pre);break;case'uncover':scrl.pre.anims.push([$cfs,{'width':'+=0'},fn]);sc_startScroll(scrl.pre);break;default:fn();break}};scrl.anims.push([$cfs,a_cfs,a_complete]);crsl.isScrolling=true;$cfs.css(opts.d['left'],-(i_siz-oL));tmrs=sc_clearTimers(tmrs);sc_startScroll(scrl);cf_setCookie(opts.cookie,$cfs.triggerHandler(cf_e('currentPosition',conf)));$cfs.trigger(cf_e('updatePageStatus',conf),[false,w_siz]);return true});$cfs.bind(cf_e('_cfs_slide_next',conf,false),function(e,sO,nI){e.stopPropagation();var a_itm=$cfs.children();if(!opts.circular){if(itms.first==opts.items.visible){if(opts.infinite){$cfs.trigger(cf_e('prev',conf),itms.total-1)}return e.stopImmediatePropagation()}}if(opts.usePadding)sz_resetMargin(a_itm,opts);if(typeof nI!='number'){if(opts.items.filter!='*'){var xI=(typeof sO.items=='number')?sO.items:gn_getVisibleOrg($cfs,opts);nI=gn_getScrollItemsNextFilter(a_itm,opts,0,xI)}else{nI=opts.items.visible}nI=cf_getAdjust(nI,opts,sO.items,$tt0)}var lastItemNr=(itms.first==0)?itms.total:itms.first;if(!opts.circular){if(opts.items.visibleConf.variable){var vI=gn_getVisibleItemsNext(a_itm,opts,nI),xI=gn_getVisibleItemsPrev(a_itm,opts,lastItemNr-1)}else{var vI=opts.items.visible,xI=opts.items.visible}if(nI+vI>lastItemNr){nI=lastItemNr-xI}}if(opts.items.visibleConf.variable){var vI=gn_getVisibleItemsNextTestCircular(a_itm,opts,nI,lastItemNr);while(opts.items.visible-nI>=vI&&nI<itms.total){nI++;vI=gn_getVisibleItemsNextTestCircular(a_itm,opts,nI,lastItemNr)}opts.items.visibleConf.old=opts.items.visible;opts.items.visible=cf_getItemsAdjust(vI,opts,opts.items.visibleConf.adjust,$tt0)}else if(opts.items.filter!='*'){var vI=gn_getVisibleItemsNextFilter(a_itm,opts,nI);opts.items.visibleConf.old=opts.items.visible;opts.items.visible=cf_getItemsAdjust(vI,opts,opts.items.visibleConf.adjust,$tt0)}if(opts.usePadding)sz_resetMargin(a_itm,opts,true);if(nI==0){e.stopImmediatePropagation();return debug(conf,'0 items to scroll: Not scrolling.')}debug(conf,'Scrolling '+nI+' items forward.');itms.first-=nI;while(itms.first<0){itms.first+=itms.total}if(!opts.circular){if(itms.first==opts.items.visible&&sO.onEnd)sO.onEnd.call($tt0);if(!opts.infinite)nv_enableNavi(opts,itms.first,conf)}if(itms.total<opts.items.visible+nI){$cfs.children().slice(0,(opts.items.visible+nI)-itms.total).clone(true).appendTo($cfs)}var a_itm=$cfs.children(),c_old=gi_getOldItemsNext(a_itm,opts),c_new=gi_getNewItemsNext(a_itm,opts,nI),l_cur=a_itm.eq(nI-1),l_old=c_old.last(),l_new=c_new.last();if(opts.usePadding)sz_resetMargin(a_itm,opts);if(opts.align){var p=cf_getAlignPadding(c_new,opts),pL=p[0],pR=p[1]}else{var pL=0,pR=0}if(sO.fx=='directscroll'&&opts.items.visibleConf.old<nI){var hiddenitems=a_itm.slice(opts.items.visibleConf.old,nI),orgW=opts.items[opts.d['width']];hiddenitems.each(function(){var hi=$(this);hi.data('isHidden',hi.is(':hidden')).hide()});opts.items[opts.d['width']]='variable'}else{var hiddenitems=false}var i_siz=ms_getTotalSize(a_itm.slice(0,nI),opts,'width'),w_siz=cf_mapWrapperSizes(ms_getSizes(c_new,opts,true),opts,!opts.usePadding);if(hiddenitems)opts.items[opts.d['width']]=orgW;if(opts.align){if(opts.padding[opts.d[1]]<0){opts.padding[opts.d[1]]=0}}if(opts.usePadding){sz_resetMargin(a_itm,opts,true);sz_resetMargin(l_old,opts,opts.padding[opts.d[1]])}if(opts.align){opts.padding[opts.d[1]]=pR;opts.padding[opts.d[3]]=pL}var a_cfs={},a_dur=sO.duration;if(sO.fx=='none')a_dur=0;else if(a_dur=='auto')a_dur=opts.scroll.duration/opts.scroll.items*nI;else if(a_dur<=0)a_dur=0;else if(a_dur<10)a_dur=i_siz/a_dur;scrl=sc_setScroll(a_dur,sO.easing);if(opts[opts.d['width']]=='variable'||opts[opts.d['height']]=='variable'){scrl.anims.push([$wrp,w_siz])}if(opts.usePadding){var l_new_m=l_new.data('cfs_origCssMargin');if(pR>=0){l_new_m+=opts.padding[opts.d[1]]}l_new.css(opts.d['marginRight'],l_new_m);if(l_cur.not(l_old).length){var a_old={};a_old[opts.d['marginRight']]=l_old.data('cfs_origCssMargin');scrl.anims.push([l_old,a_old])}var c_new_m=l_cur.data('cfs_origCssMargin');if(pL>=0){c_new_m+=opts.padding[opts.d[3]]}var a_cur={};a_cur[opts.d['marginRight']]=c_new_m;scrl.anims.push([l_cur,a_cur])}a_cfs[opts.d['left']]=-i_siz;if(pL<0){a_cfs[opts.d['left']]+=pL}var args=[c_old,c_new,w_siz,a_dur];if(sO.onBefore)sO.onBefore.apply($tt0,args);clbk.onBefore=sc_callCallbacks(clbk.onBefore,$tt0,args);switch(sO.fx){case'fade':case'crossfade':case'cover':case'uncover':scrl.pre=sc_setScroll(scrl.duration,scrl.easing);scrl.post=sc_setScroll(scrl.duration,scrl.easing);scrl.duration=0;break}switch(sO.fx){case'crossfade':case'cover':case'uncover':var $cf2=$cfs.clone().appendTo($wrp);break}switch(sO.fx){case'uncover':$cf2.children().slice(opts.items.visibleConf.old).remove();break;case'crossfade':case'cover':$cf2.children().slice(0,nI).remove();$cf2.children().slice(opts.items.visible).remove();break}switch(sO.fx){case'fade':scrl.pre.anims.push([$cfs,{'opacity':0}]);break;case'crossfade':$cf2.css({'opacity':0});scrl.pre.anims.push([$cfs,{'width':'+=0'},function(){$cf2.remove()}]);scrl.post.anims.push([$cf2,{'opacity':1}]);break;case'cover':scrl=fx_cover(scrl,$cfs,$cf2,opts,false);break;case'uncover':scrl=fx_uncover(scrl,$cfs,$cf2,opts,false,nI);break}var a_complete=function(){var overFill=opts.items.visible+nI-itms.total,new_m=(opts.usePadding)?opts.padding[opts.d[3]]:0;$cfs.css(opts.d['left'],new_m);if(overFill>0){$cfs.children().slice(itms.total).remove()}var l_itm=$cfs.children().slice(0,nI).appendTo($cfs).last();if(overFill>0){c_new=gi_getCurrentItems(a_itm,opts)}if(hiddenitems){hiddenitems.each(function(){var hi=$(this);if(!hi.data('isHidden'))hi.show()})}if(opts.usePadding){if(itms.total<opts.items.visible+nI){var l_cur=$cfs.children().eq(opts.items.visible-1);l_cur.css(opts.d['marginRight'],l_cur.data('cfs_origCssMargin')+opts.padding[opts.d[3]])}l_itm.css(opts.d['marginRight'],l_itm.data('cfs_origCssMargin'))}scrl.anims=[];if(scrl.pre)scrl.pre=sc_setScroll(scrl.orgDuration,scrl.easing);var fn=function(){switch(sO.fx){case'fade':case'crossfade':$cfs.css('filter','');break}scrl.post=sc_setScroll(0,null);crsl.isScrolling=false;var args=[c_old,c_new,w_siz];if(sO.onAfter)sO.onAfter.apply($tt0,args);clbk.onAfter=sc_callCallbacks(clbk.onAfter,$tt0,args);if(queu.length){$cfs.trigger(cf_e(queu[0][0],conf),queu[0][1]);queu.shift()}if(!crsl.isPaused)$cfs.trigger(cf_e('play',conf))};switch(sO.fx){case'fade':scrl.pre.anims.push([$cfs,{'opacity':1},fn]);sc_startScroll(scrl.pre);break;case'uncover':scrl.pre.anims.push([$cfs,{'width':'+=0'},fn]);sc_startScroll(scrl.pre);break;default:fn();break}};scrl.anims.push([$cfs,a_cfs,a_complete]);crsl.isScrolling=true;tmrs=sc_clearTimers(tmrs);sc_startScroll(scrl);cf_setCookie(opts.cookie,$cfs.triggerHandler(cf_e('currentPosition',conf)));$cfs.trigger(cf_e('updatePageStatus',conf),[false,w_siz]);return true});$cfs.bind(cf_e('slideTo',conf),function(e,num,dev,org,obj,dir,clb){e.stopPropagation();var v=[num,dev,org,obj,dir,clb],t=['string/number/object','number','boolean','object','string','function'],a=cf_sortParams(v,t);var obj=a[3],dir=a[4],clb=a[5];num=gn_getItemIndex(a[0],a[1],a[2],itms,$cfs);if(num==0)return;if(typeof obj!='object')obj=false;if(crsl.isScrolling){if(typeof obj!='object'||obj.duration>0)return false}if(dir!='prev'&&dir!='next'){if(opts.circular){if(num<=itms.total/2)dir='next';else dir='prev'}else{if(itms.first==0||itms.first>num)dir='next';else dir='prev'}}if(dir=='prev')num=itms.total-num;$cfs.trigger(cf_e(dir,conf),[obj,num,clb]);return true});$cfs.bind(cf_e('prevPage',conf),function(e,obj,clb){e.stopPropagation();var cur=$cfs.triggerHandler(cf_e('currentPage',conf));return $cfs.triggerHandler(cf_e('slideToPage',conf),[cur-1,obj,'prev',clb])});$cfs.bind(cf_e('nextPage',conf),function(e,obj,clb){e.stopPropagation();var cur=$cfs.triggerHandler(cf_e('currentPage',conf));return $cfs.triggerHandler(cf_e('slideToPage',conf),[cur+1,obj,'next',clb])});$cfs.bind(cf_e('slideToPage',conf),function(e,pag,obj,dir,clb){e.stopPropagation();if(typeof pag!='number')pag=$cfs.triggerHandler(cf_e('currentPage',conf));var ipp=opts.pagination.items||opts.items.visible,max=Math.floor(itms.total/ipp)-1;if(pag<0)pag=max;if(pag>max)pag=0;return $cfs.triggerHandler(cf_e('slideTo',conf),[pag*ipp,0,true,obj,dir,clb])});$cfs.bind(cf_e('jumpToStart',conf),function(e,s){e.stopPropagation();if(s)s=gn_getItemIndex(s,0,true,itms,$cfs);else s=0;s+=itms.first;if(s!=0){while(s>itms.total)s-=itms.total;$cfs.prepend($cfs.children().slice(s,itms.total))}return true});$cfs.bind(cf_e('synchronise',conf),function(e,s){e.stopPropagation();if(s)s=cf_getSynchArr(s);else if(opts.synchronise)s=opts.synchronise;else return debug(conf,'No carousel to synchronise.');var n=$cfs.triggerHandler(cf_e('currentPosition',conf)),x=true;for(var j=0,l=s.length;j<l;j++){if(!s[j][0].triggerHandler(cf_e('slideTo',conf),[n,s[j][3],true])){x=false}}return x});$cfs.bind(cf_e('queue',conf),function(e,dir,opt){e.stopPropagation();if(typeof dir=='function'){dir.call($tt0,queu)}else if(is_array(dir)){queu=dir}else if(typeof dir!='undefined'){queu.push([dir,opt])}return queu});$cfs.bind(cf_e('insertItem',conf),function(e,itm,num,org,dev){e.stopPropagation();var v=[itm,num,org,dev],t=['string/object','string/number/object','boolean','number'],a=cf_sortParams(v,t);var itm=a[0],num=a[1],org=a[2],dev=a[3];if(typeof itm=='object'&&typeof itm.jquery=='undefined')itm=$(itm);if(typeof itm=='string')itm=$(itm);if(typeof itm!='object'||typeof itm.jquery=='undefined'||itm.length==0)return debug(conf,'Not a valid object.');if(typeof num=='undefined')num='end';if(opts.usePadding){itm.each(function(){var m=parseInt($(this).css(opts.d['marginRight']));if(isNaN(m))m=0;$(this).data('cfs_origCssMargin',m)})}var orgNum=num,before='before';if(num=='end'){if(org){if(itms.first==0){num=itms.total-1;before='after'}else{num=itms.first;itms.first+=itm.length}if(num<0)num=0}else{num=itms.total-1;before='after'}}else{num=gn_getItemIndex(num,dev,org,itms,$cfs)}if(orgNum!='end'&&!org){if(num<itms.first)itms.first+=itm.length}if(itms.first>=itms.total)itms.first-=itms.total;var $cit=$cfs.children().eq(num);if($cit.length){$cit[before](itm)}else{$cfs.append(itm)}itms.total=$cfs.children().length;var sz=$cfs.triggerHandler('updateSizes');nv_showNavi(opts,itms.total,conf);nv_enableNavi(opts,itms.first,conf);$cfs.trigger(cf_e('linkAnchors',conf));$cfs.trigger(cf_e('updatePageStatus',conf),[true,sz]);return true});$cfs.bind(cf_e('removeItem',conf),function(e,num,org,dev){e.stopPropagation();var v=[num,org,dev],t=['string/number/object','boolean','number'],a=cf_sortParams(v,t);var num=a[0],org=a[1],dev=a[2];if(typeof num=='undefined'||num=='end'){$cfs.children().last().remove()}else{num=gn_getItemIndex(num,dev,org,itms,$cfs);var $cit=$cfs.children().eq(num);if($cit.length){if(num<itms.first)itms.first-=$cit.length;$cit.remove()}}itms.total=$cfs.children().length;var sz=$cfs.triggerHandler('updateSizes');nv_showNavi(opts,itms.total,conf);nv_enableNavi(opts,itms.first,conf);$cfs.trigger(cf_e('updatePageStatus',conf),[true,sz]);return true});$cfs.bind(cf_e('onBefore',conf)+' '+cf_e('onAfter',conf),function(e,fn){e.stopPropagation();var eType=e.type.substr(conf.events.prefix.length);if(is_array(fn))clbk[eType]=fn;if(typeof fn=='function')clbk[eType].push(fn);return clbk[eType]});$cfs.bind(cf_e('_cfs_currentPosition',conf,false),function(e,fn){e.stopPropagation();return $cfs.triggerHandler(cf_e('currentPosition',conf),fn)});$cfs.bind(cf_e('currentPosition',conf),function(e,fn){e.stopPropagation();if(itms.first==0)var val=0;else var val=itms.total-itms.first;if(typeof fn=='function')fn.call($tt0,val);return val});$cfs.bind(cf_e('currentPage',conf),function(e,fn){e.stopPropagation();var ipp=opts.pagination.items||opts.items.visible;var max=Math.ceil(itms.total/ipp-1);if(itms.first==0)var nr=0;else if(itms.first<itms.total%ipp)var nr=0;else if(itms.first==ipp&&!opts.circular)var nr=max;else var nr=Math.round((itms.total-itms.first)/ipp);if(nr<0)nr=0;if(nr>max)nr=max;if(typeof fn=='function')fn.call($tt0,nr);return nr});$cfs.bind(cf_e('currentVisible',conf),function(e,fn){e.stopPropagation();$i=gi_getCurrentItems($cfs.children(),opts);if(typeof fn=='function')fn.call($tt0,$i);return $i});$cfs.bind(cf_e('slice',conf),function(e,f,l,fn){e.stopPropagation();var v=[f,l,fn],t=['number','number','function'],a=cf_sortParams(v,t);f=(typeof a[0]=='number')?a[0]:0,l=(typeof a[1]=='number')?a[1]:itms.total,fn=a[2];f+=itms.first;l+=itms.first;while(f>itms.total){f-=itms.total}while(l>itms.total){l-=itms.total}while(f<0){f+=itms.total}while(l<0){l+=itms.total}var $iA=$cfs.children();if(l>f){var $i=$iA.slice(f,l)}else{var $i=$iA.slice(f,itms.total).get().concat($iA.slice(0,l).get())}if(typeof fn=='function')fn.call($tt0,$i);return $i});$cfs.bind(cf_e('isPaused',conf)+' '+cf_e('isStopped',conf)+' '+cf_e('isScrolling',conf),function(e,fn){e.stopPropagation();var eType=e.type.substr(conf.events.prefix.length);if(typeof fn=='function')fn.call($tt0,crsl[eType]);return crsl[eType]});$cfs.bind(cf_e('_cfs_configuration',conf,false),function(e,a,b,c){e.stopPropagation();return $cfs.triggerHandler(cf_e('configuration',conf),[a,b,c])});$cfs.bind(cf_e('configuration',conf),function(e,a,b,c){e.stopPropagation();var reInit=false;if(typeof a=='function'){a.call($tt0,opts)}else if(typeof a=='object'){opts_orig=$.extend(true,{},opts_orig,a);if(b!==false)reInit=true;else opts=$.extend(true,{},opts,a)}else if(typeof a!='undefined'){if(typeof b=='function'){var val=eval('opts.'+a);if(typeof val=='undefined')val='';b.call($tt0,val)}else if(typeof b!='undefined'){if(typeof c!=='boolean')c=true;eval('opts_orig.'+a+' = b');if(c!==false)reInit=true;else eval('opts.'+a+' = b')}else{return eval('opts.'+a)}}if(reInit){sz_resetMargin($cfs.children(),opts);$cfs._cfs_init(opts_orig);$cfs._cfs_bind_buttons();var siz=sz_setSizes($cfs,opts);$cfs.trigger(cf_e('updatePageStatus',conf),[true,siz])}return opts});$cfs.bind(cf_e('linkAnchors',conf),function(e,$con,sel){e.stopPropagation();if(typeof $con=='undefined'||$con.length==0)$con=$('body');else if(typeof $con=='string')$con=$($con);if(typeof $con!='object')return debug(conf,'Not a valid object.');if(typeof sel!='string'||sel.length==0)sel='a.caroufredsel';$con.find(sel).each(function(){var h=this.hash||'';if(h.length>0&&$cfs.children().index($(h))!=-1){$(this).unbind('click').click(function(e){e.preventDefault();$cfs.trigger(cf_e('slideTo',conf),h)})}});return true});$cfs.bind(cf_e('updatePageStatus',conf),function(e,build,sizes){e.stopPropagation();if(!opts.pagination.container)return;if(build){var ipp=opts.pagination.items||opts.items.visible,l=Math.ceil(itms.total/ipp);if(opts.pagination.anchorBuilder){opts.pagination.container.children().remove();opts.pagination.container.each(function(){for(var a=0;a<l;a++){var i=$cfs.children().eq(gn_getItemIndex(a*ipp,0,true,itms,$cfs));$(this).append(opts.pagination.anchorBuilder(a+1,i))}})}opts.pagination.container.each(function(){$(this).children().unbind(opts.pagination.event).each(function(a){$(this).bind(opts.pagination.event,function(e){e.preventDefault();$cfs.trigger(cf_e('slideTo',conf),[a*ipp,0,true,opts.pagination])})})})}opts.pagination.container.each(function(){$(this).children().removeClass(cf_c('selected',conf)).eq($cfs.triggerHandler(cf_e('currentPage',conf))).addClass(cf_c('selected',conf))});return true});$cfs.bind(cf_e('updateSizes',conf),function(e){var a_itm=$cfs.children(),vI=opts.items.visible;if(opts.items.visibleConf.variable)vI=gn_getVisibleItemsNext(a_itm,opts,0);else if(opts.items.filter!='*')vI=gn_getVisibleItemsNextFilter(a_itm,opts,0);if(!opts.circular&&itms.first!=0&&vI>itms.first){if(opts.items.visibleConf.variable){var nI=gn_getVisibleItemsPrev(a_itm,opts,itms.first)-itms.first}else if(opts.items.filter!='*'){var nI=gn_getVisibleItemsPrevFilter(a_itm,opts,itms.first)-itms.first}else{nI=opts.items.visible-itms.first}debug(conf,'Preventing non-circular: sliding '+nI+' items backward.');$cfs.trigger('prev',nI)}opts.items.visible=cf_getItemsAdjust(vI,opts,opts.items.visibleConf.adjust,$tt0);return sz_setSizes($cfs,opts)});$cfs.bind(cf_e('_cfs_destroy',conf,false),function(e,orgOrder){e.stopPropagation();$cfs.trigger(cf_e('destroy',conf),orgOrder);return true});$cfs.bind(cf_e('destroy',conf),function(e,orgOrder){e.stopPropagation();tmrs=sc_clearTimers(tmrs);$cfs.data('cfs_isCarousel',false);$cfs.trigger(cf_e('finish',conf));if(orgOrder){$cfs.trigger(cf_e('jumpToStart',conf))}if(opts.usePadding){sz_resetMargin($cfs.children(),opts)}$cfs.css($cfs.data('cfs_origCss'));$cfs._cfs_unbind_events();$cfs._cfs_unbind_buttons();$wrp.replaceWith($cfs);return true})};$cfs._cfs_unbind_events=function(){$cfs.unbind(cf_e('',conf,false))};$cfs._cfs_bind_buttons=function(){$cfs._cfs_unbind_buttons();nv_showNavi(opts,itms.total,conf);nv_enableNavi(opts,itms.first,conf);if(opts.auto.pauseOnHover){var pC=bt_pauseOnHoverConfig(opts.auto.pauseOnHover);$wrp.bind(cf_e('mouseenter',conf,false),function(){$cfs.trigger(cf_e('pause',conf),pC)}).bind(cf_e('mouseleave',conf,false),function(){$cfs.trigger(cf_e('resume',conf))})}if(opts.auto.button){opts.auto.button.bind(cf_e(opts.auto.event,conf,false),function(e){e.preventDefault();if(crsl.isPaused){var ev='play',pC=null}else{var ev='pause',pC=bt_pauseOnHoverConfig(opts.auto.pauseOnClick)}$cfs.trigger(cf_e(ev,conf),pC)})}if(opts.prev.button){opts.prev.button.bind(cf_e(opts.prev.event,conf,false),function(e){e.preventDefault();$cfs.trigger(cf_e('prev',conf))});if(opts.prev.pauseOnHover){var pC=bt_pauseOnHoverConfig(opts.prev.pauseOnHover);opts.prev.button.bind(cf_e('mouseenter',conf,false),function(){$cfs.trigger(cf_e('pause',conf),pC)}).bind(cf_e('mouseleave',conf,false),function(){$cfs.trigger(cf_e('resume',conf))})}}if(opts.next.button){opts.next.button.bind(cf_e(opts.next.event,conf,false),function(e){e.preventDefault();$cfs.trigger(cf_e('next',conf))});if(opts.next.pauseOnHover){var pC=bt_pauseOnHoverConfig(opts.next.pauseOnHover);opts.next.button.bind(cf_e('mouseenter',conf,false),function(){$cfs.trigger(cf_e('pause',conf),pC)}).bind(cf_e('mouseleave',conf,false),function(){$cfs.trigger(cf_e('resume',conf))})}}if($.fn.mousewheel){if(opts.prev.mousewheel){if(!crsl.mousewheelPrev){crsl.mousewheelPrev=true;$wrp.mousewheel(function(e,delta){if(delta>0){e.preventDefault();var num=bt_mousesheelNumber(opts.prev.mousewheel);$cfs.trigger(cf_e('prev',conf),num)}})}}if(opts.next.mousewheel){if(!crsl.mousewheelNext){crsl.mousewheelNext=true;$wrp.mousewheel(function(e,delta){if(delta<0){e.preventDefault();var num=bt_mousesheelNumber(opts.next.mousewheel);$cfs.trigger(cf_e('next',conf),num)}})}}}if($.fn.touchwipe){var wP=(opts.prev.wipe)?function(){$cfs.trigger(cf_e('prev',conf))}:null,wN=(opts.next.wipe)?function(){$cfs.trigger(cf_e('next',conf))}:null;if(wN||wN){if(!crsl.touchwipe){crsl.touchwipe=true;var twOps={'min_move_x':30,'min_move_y':30,'preventDefaultEvents':true};switch(opts.direction){case'up':case'down':twOps.wipeUp=wN;twOps.wipeDown=wP;break;default:twOps.wipeLeft=wN;twOps.wipeRight=wP}$wrp.touchwipe(twOps)}}}if(opts.pagination.container){if(opts.pagination.pauseOnHover){var pC=bt_pauseOnHoverConfig(opts.pagination.pauseOnHover);opts.pagination.container.bind(cf_e('mouseenter',conf,false),function(){$cfs.trigger(cf_e('pause',conf),pC)}).bind(cf_e('mouseleave',conf,false),function(){$cfs.trigger(cf_e('resume',conf))})}}if(opts.prev.key||opts.next.key){$(document).bind(cf_e('keyup',conf,false),function(e){var k=e.keyCode;if(k==opts.next.key){e.preventDefault();$cfs.trigger(cf_e('next',conf))}if(k==opts.prev.key){e.preventDefault();$cfs.trigger(cf_e('prev',conf))}})}if(opts.pagination.keys){$(document).bind(cf_e('keyup',conf,false),function(e){var k=e.keyCode;if(k>=49&&k<58){k=(k-49)*opts.items.visible;if(k<=itms.total){e.preventDefault();$cfs.trigger(cf_e('slideTo',conf),[k,0,true,opts.pagination])}}})}if(opts.auto.play){$cfs.trigger(cf_e('play',conf),opts.auto.delay)}};$cfs._cfs_unbind_buttons=function(){var ns=cf_e('',conf,false);$(document).unbind(ns);$wrp.unbind(ns);if(opts.auto.button)opts.auto.button.unbind(ns);if(opts.prev.button)opts.prev.button.unbind(ns);if(opts.next.button)opts.next.button.unbind(ns);if(opts.pagination.container){opts.pagination.container.unbind(ns);if(opts.pagination.anchorBuilder){opts.pagination.container.children().remove()}}nv_showNavi(opts,'hide',conf);nv_enableNavi(opts,'removeClass',conf)};var crsl={'direction':'next','isPaused':true,'isScrolling':false,'isStopped':false,'mousewheelNext':false,'mousewheelPrev':false,'touchwipe':false},itms={'total':$cfs.children().length,'first':0},tmrs={'timer':null,'auto':null,'queue':null,'startTime':getTime(),'timePassed':0},scrl={'isStopped':false,'duration':0,'startTime':0,'easing':'','anims':[]},clbk={'onBefore':[],'onAfter':[]},queu=[],conf=$.extend(true,{},$.fn.carouFredSel.configs,configs),opts={},opts_orig=options,$wrp=$cfs.wrap('<'+conf.wrapper.element+' class="'+conf.wrapper.classname+'" />').parent();conf.selector=$cfs.selector;$cfs._cfs_init(opts_orig,true,starting_position);$cfs._cfs_build();$cfs._cfs_bind_events();$cfs._cfs_bind_buttons();if(is_array(opts.items.start)){var start_arr=opts.items.start}else{var start_arr=[];if(opts.items.start!=0){start_arr.push(opts.items.start)}}if(opts.cookie){start_arr.unshift(cf_readCookie(opts.cookie))}if(start_arr.length>0){for(var a=0,l=start_arr.length;a<l;a++){var s=start_arr[a];if(s==0){continue}if(s===true){s=window.location.hash;if(s.length<1){continue}}else if(s==='random'){s=Math.floor(Math.random()*itms.total)}if($cfs.triggerHandler(cf_e('slideTo',conf),[s,0,true,{fx:'none'}])){break}}}var siz=sz_setSizes($cfs,opts,false),itm=gi_getCurrentItems($cfs.children(),opts);if(opts.onCreate){opts.onCreate.call($tt0,itm,siz)}$cfs.trigger(cf_e('updatePageStatus',conf),[true,siz]);$cfs.trigger(cf_e('linkAnchors',conf));return $cfs};$.fn.carouFredSel.defaults={'synchronise':false,'infinite':true,'circular':true,'direction':'left','items':{'start':0},'scroll':{'easing':'swing','duration':500,'pauseOnHover':false,'mousewheel':false,'wipe':false,'event':'click','queue':false}};$.fn.carouFredSel.configs={'debug':false,'events':{'prefix':'','namespace':'cfs'},'wrapper':{'element':'div','classname':'caroufredsel_wrapper'},'classnames':{}};$.fn.carouFredSel.pageAnchorBuilder=function(nr,itm){return'<a href="#"><span>'+nr+'</span></a>'};function sc_setScroll(d,e){return{anims:[],duration:d,orgDuration:d,easing:e,startTime:getTime()}}function sc_startScroll(s){if(typeof s.pre=='object'){sc_startScroll(s.pre)}for(var a=0,l=s.anims.length;a<l;a++){var b=s.anims[a];if(!b)continue;if(b[3])b[0].stop();b[0].animate(b[1],{complete:b[2],duration:s.duration,easing:s.easing})}if(typeof s.post=='object'){sc_startScroll(s.post)}}function sc_stopScroll(s,finish){if(typeof finish!='boolean')finish=true;if(typeof s.pre=='object'){sc_stopScroll(s.pre,finish)}for(var a=0,l=s.anims.length;a<l;a++){var b=s.anims[a];b[0].stop(true);if(finish){b[0].css(b[1]);if(typeof b[2]=='function')b[2]()}}if(typeof s.post=='object'){sc_stopScroll(s.post,finish)}}function sc_clearTimers(t){if(t.auto)clearTimeout(t.auto);return t}function sc_callCallbacks(cbs,t,args){if(cbs.length){for(var a=0,l=cbs.length;a<l;a++){cbs[a].apply(t,args)}}return[]}function fx_fade(sO,c,x,d,f){var o={'duration':d,'easing':sO.easing};if(typeof f=='function')o.complete=f;c.animate({opacity:x},o)}function fx_cover(sc,c1,c2,o,prev){var old_w=ms_getSizes(gi_getOldItemsNext(c1.children(),o),o,true)[0],new_w=ms_getSizes(c2.children(),o,true)[0],cur_l=(prev)?-new_w:old_w,css_o={},ani_o={};css_o[o.d['width']]=new_w;css_o[o.d['left']]=cur_l;ani_o[o.d['left']]=0;sc.pre.anims.push([c1,{'opacity':1}]);sc.post.anims.push([c2,ani_o,function(){$(this).remove()}]);c2.css(css_o);return sc}function fx_uncover(sc,c1,c2,o,prev,n){var new_w=ms_getSizes(gi_getNewItemsNext(c1.children(),o,n),o,true)[0],old_w=ms_getSizes(c2.children(),o,true)[0],cur_l=(prev)?-old_w:new_w,css_o={},ani_o={};css_o[o.d['width']]=old_w;css_o[o.d['left']]=0;ani_o[o.d['left']]=cur_l;sc.post.anims.push([c2,ani_o,function(){$(this).remove()}]);c2.css(css_o);return sc}function nv_showNavi(o,t,c){if(t=='show'||t=='hide'){var f=t}else if(o.items.minimum>=t){debug(c,'Not enough items: hiding navigation ('+t+' items, '+o.items.minimum+' needed).');var f='hide'}else{var f='show'}var s=(f=='show')?'removeClass':'addClass',h=cf_c('hidden',c);if(o.auto.button)o.auto.button[f]()[s](h);if(o.prev.button)o.prev.button[f]()[s](h);if(o.next.button)o.next.button[f]()[s](h);if(o.pagination.container)o.pagination.container[f]()[s](h)}function nv_enableNavi(o,f,c){if(o.circular||o.infinite)return;var fx=(f=='removeClass'||f=='addClass')?f:false,di=cf_c('disabled',c);if(o.auto.button&&fx){o.auto.button[fx](di)}if(o.prev.button){var fn=fx||(f==0)?'addClass':'removeClass';o.prev.button[fn](di)}if(o.next.button){var fn=fx||(f==o.items.visible)?'addClass':'removeClass';o.next.button[fn](di)}}function go_getObject($tt,obj){if(typeof obj=='function')obj=obj.call($tt);if(typeof obj=='undefined')obj={};return obj}function go_getNaviObject($tt,obj,type){if(typeof type!='string')type='';obj=go_getObject($tt,obj);if(typeof obj=='string'){var temp=cf_getKeyCode(obj);if(temp==-1)obj=$(obj);else obj=temp}if(type=='pagination'){if(typeof obj=='boolean')obj={'keys':obj};if(typeof obj.jquery!='undefined')obj={'container':obj};if(typeof obj.container=='function')obj.container=obj.container.call($tt);if(typeof obj.container=='string')obj.container=$(obj.container);if(typeof obj.items!='number')obj.items=false}else if(type=='auto'){if(typeof obj.jquery!='undefined')obj={'button':obj};if(typeof obj=='boolean')obj={'play':obj};if(typeof obj=='number')obj={'pauseDuration':obj};if(typeof obj.button=='function')obj.button=obj.button.call($tt);if(typeof obj.button=='string')obj.button=$(obj.button)}else{if(typeof obj.jquery!='undefined')obj={'button':obj};if(typeof obj=='number')obj={'key':obj};if(typeof obj.button=='function')obj.button=obj.button.call($tt);if(typeof obj.button=='string')obj.button=$(obj.button);if(typeof obj.key=='string')obj.key=cf_getKeyCode(obj.key)}return obj}function gn_getItemIndex(num,dev,org,items,$cfs){if(typeof num=='string'){if(isNaN(num))num=$(num);else num=parseInt(num)}if(typeof num=='object'){if(typeof num.jquery=='undefined')num=$(num);num=$cfs.children().index(num);if(num==-1)num=0;if(typeof org!='boolean')org=false}else{if(typeof org!='boolean')org=true}if(isNaN(num))num=0;else num=parseInt(num);if(isNaN(dev))dev=0;else dev=parseInt(dev);if(org){num+=items.first}num+=dev;if(items.total>0){while(num>=items.total){num-=items.total}while(num<0){num+=items.total}}return num}function gn_getVisibleItemsPrev(i,o,s){var t=0,x=0;for(var a=s;a>=0;a--){var j=i.eq(a);t+=(j.is(':visible'))?j[o.d['outerWidth']](true):0;if(t>o.maxDimention)return x;if(a==0)a=i.length;x++}}function gn_getVisibleItemsPrevFilter(i,o,s){return gn_getItemsPrevFilter(i,o.items.filter,o.items.visibleConf.org,s)}function gn_getScrollItemsPrevFilter(i,o,s,m){return gn_getItemsPrevFilter(i,o.items.filter,m,s)}function gn_getItemsPrevFilter(i,f,m,s){var t=0,x=0;for(var a=s,l=i.length-1;a>=0;a--){x++;if(x==l)return x;var j=i.eq(a);if(j.is(f)){t++;if(t==m)return x}if(a==0)a=i.length}}function gn_getVisibleOrg($c,o){return o.items.visibleConf.org||$c.children().slice(0,o.items.visible).filter(o.items.filter).length}function gn_getVisibleItemsNext(i,o,s){var t=0,x=0;for(var a=s,l=i.length-1;a<=l;a++){var j=i.eq(a);t+=(j.is(':visible'))?j[o.d['outerWidth']](true):0;if(t>o.maxDimention)return x;x++;if(x==l)return x;if(a==l)a=-1}}function gn_getVisibleItemsNextTestCircular(i,o,s,l){var v=gn_getVisibleItemsNext(i,o,s);if(!o.circular){if(s+v>l)v=l-s}return v}function gn_getVisibleItemsNextFilter(i,o,s){return gn_getItemsNextFilter(i,o.items.filter,o.items.visibleConf.org,s)}function gn_getScrollItemsNextFilter(i,o,s,m){return gn_getItemsNextFilter(i,o.items.filter,m+1,s)-1}function gn_getItemsNextFilter(i,f,m,s){var t=0,x=0;for(var a=s,l=i.length-1;a<=l;a++){x++;if(x==l)return x;var j=i.eq(a);if(j.is(f)){t++;if(t==m)return x}if(a==l)a=-1}}function gi_getCurrentItems(i,o){return i.slice(0,o.items.visible)}function gi_getOldItemsPrev(i,o,n){return i.slice(n,o.items.visibleConf.old+n)}function gi_getNewItemsPrev(i,o){return i.slice(0,o.items.visible)}function gi_getOldItemsNext(i,o){return i.slice(0,o.items.visibleConf.old)}function gi_getNewItemsNext(i,o,n){return i.slice(n,o.items.visible+n)}function sz_resetMargin(i,o,m){var x=(typeof m=='boolean')?m:false;if(typeof m!='number')m=0;i.each(function(){var j=$(this);var t=parseInt(j.css(o.d['marginRight']));if(isNaN(t))t=0;j.data('cfs_tempCssMargin',t);j.css(o.d['marginRight'],((x)?j.data('cfs_tempCssMargin'):m+j.data('cfs_origCssMargin')))})}function sz_setSizes($c,o,p){var $w=$c.parent(),$i=$c.children(),$v=gi_getCurrentItems($i,o),sz=cf_mapWrapperSizes(ms_getSizes($v,o,true),o,p);$w.css(sz);if(o.usePadding){var p=o.padding,r=p[o.d[1]];if(o.align){if(r<0)r=0}var $l=$v.last();$l.css(o.d['marginRight'],$l.data('cfs_origCssMargin')+r);$c.css(o.d['top'],p[o.d[0]]);$c.css(o.d['left'],p[o.d[3]])}$c.css(o.d['width'],sz[o.d['width']]+(ms_getTotalSize($i,o,'width')*2));$c.css(o.d['height'],ms_getLargestSize($i,o,'height'));return sz}function ms_getSizes(i,o,wrapper){var s1=ms_getTotalSize(i,o,'width',wrapper),s2=ms_getLargestSize(i,o,'height',wrapper);return[s1,s2]}function ms_getLargestSize(i,o,dim,wrapper){if(typeof wrapper!='boolean')wrapper=false;if(typeof o[o.d[dim]]=='number'&&wrapper)return o[o.d[dim]];if(typeof o.items[o.d[dim]]=='number')return o.items[o.d[dim]];var di2=(dim.toLowerCase().indexOf('width')>-1)?'outerWidth':'outerHeight';return ms_getTrueLargestSize(i,o,di2)}function ms_getTrueLargestSize(i,o,dim){var s=0;for(var a=0,l=i.length;a<l;a++){var j=i.eq(a);var m=(j.is(':visible'))?j[o.d[dim]](true):0;if(s<m)s=m}return s}function ms_getTrueInnerSize($el,o,dim){if(!$el.is(':visible'))return 0;var siz=$el[o.d[dim]](),arr=(o.d[dim].toLowerCase().indexOf('width')>-1)?['paddingLeft','paddingRight']:['paddingTop','paddingBottom'];for(var a=0,l=arr.length;a<l;a++){var m=parseInt($el.css(arr[a]));siz-=(isNaN(m))?0:m}return siz}function ms_getTotalSize(i,o,dim,wrapper){if(typeof wrapper!='boolean')wrapper=false;if(typeof o[o.d[dim]]=='number'&&wrapper)return o[o.d[dim]];if(typeof o.items[o.d[dim]]=='number')return o.items[o.d[dim]]*i.length;var d=(dim.toLowerCase().indexOf('width')>-1)?'outerWidth':'outerHeight',s=0;for(var a=0,l=i.length;a<l;a++){var j=i.eq(a);s+=(j.is(':visible'))?j[o.d[d]](true):0}return s}function ms_hasVariableSizes(i,o,dim){var s=false,v=false;for(var a=0,l=i.length;a<l;a++){var j=i.eq(a);var c=(j.is(':visible'))?j[o.d[dim]](true):0;if(s===false)s=c;else if(s!=c)v=true;if(s==0)v=true}return v}function cf_e(n,c,pf,ns){if(typeof pf!='boolean')pf=true;if(typeof ns!='boolean')ns=true;if(pf)n=c.events.prefix+n;if(ns)n=n+'.'+c.events.namespace;return n}function cf_c(n,c){return(typeof c.classnames[n]=='string')?c.classnames[n]:n}function cf_mapWrapperSizes(ws,o,p){if(typeof p!='boolean')p=true;var pad=(o.usePadding&&p)?o.padding:[0,0,0,0];var wra={};wra[o.d['width']]=ws[0]+pad[1]+pad[3];wra[o.d['height']]=ws[1]+pad[0]+pad[2];return wra}function cf_sortParams(vals,typs){var arr=[];for(var a=0,l1=vals.length;a<l1;a++){for(var b=0,l2=typs.length;b<l2;b++){if(typs[b].indexOf(typeof vals[a])>-1&&typeof arr[b]=='undefined'){arr[b]=vals[a];break}}}return arr}function cf_getPadding(p){if(typeof p=='undefined')return[0,0,0,0];if(typeof p=='number')return[p,p,p,p];else if(typeof p=='string')p=p.split('px').join('').split('em').join('').split(' ');if(!is_array(p)){return[0,0,0,0]}for(var i=0;i<4;i++){p[i]=parseInt(p[i])}switch(p.length){case 0:return[0,0,0,0];case 1:return[p[0],p[0],p[0],p[0]];case 2:return[p[0],p[1],p[0],p[1]];case 3:return[p[0],p[1],p[2],p[1]];default:return[p[0],p[1],p[2],p[3]]}}function cf_getAlignPadding(itm,o){var x=(typeof o[o.d['width']]=='number')?Math.ceil(o[o.d['width']]-ms_getTotalSize(itm,o,'width')):0;switch(o.align){case'left':return[0,x];case'right':return[x,0];case'center':default:return[Math.ceil(x/2),Math.floor(x/2)]}}function cf_getAdjust(x,o,a,$t){var v=x;if(typeof a=='function'){v=a.call($t,v)}else if(typeof a=='string'){var p=a.split('+'),m=a.split('-');if(m.length>p.length){var neg=true,sta=m[0],adj=m[1]}else{var neg=false,sta=p[0],adj=p[1]}switch(sta){case'even':v=(x%2==1)?x-1:x;break;case'odd':v=(x%2==0)?x-1:x;break;default:v=x;break}adj=parseInt(adj);if(!isNaN(adj)){if(neg)adj=-adj;v+=adj}}if(typeof v!='number')v=1;if(v<1)v=1;return v}function cf_getItemsAdjust(x,o,a,$t){var v=cf_getAdjust(x,o,a,$t),i=o.items.visibleConf;if(typeof i.min=='number'&&v<i.min)v=i.min;if(typeof i.max=='number'&&v>i.max)v=i.max;if(v<1)v=1;return v}function cf_getSynchArr(s){if(!is_array(s))s=[[s]];if(!is_array(s[0]))s=[s];for(var j=0,l=s.length;j<l;j++){if(typeof s[j][0]=='string')s[j][0]=$(s[j][0]);if(typeof s[j][1]!='boolean')s[j][1]=true;if(typeof s[j][2]!='boolean')s[j][2]=true;if(typeof s[j][3]!='number')s[j][3]=0}return s}function cf_getKeyCode(k){if(k=='right')return 39;if(k=='left')return 37;if(k=='up')return 38;if(k=='down')return 40;return-1}function cf_setCookie(n,v){if(n)document.cookie=n+'='+v+'; path=/'}function cf_readCookie(n){n+='=';var ca=document.cookie.split(';');for(var a=0,l=ca.length;a<l;a++){var c=ca[a];while(c.charAt(0)==' '){c=c.substring(1,c.length)}if(c.indexOf(n)==0){return c.substring(n.length,c.length)}}return 0}function bt_pauseOnHoverConfig(p){if(p&&typeof p=='string'){var i=(p.indexOf('immediate')>-1)?true:false,r=(p.indexOf('resume')>-1)?true:false}else{var i=r=false}return[i,r]}function bt_mousesheelNumber(mw){return(typeof mw=='number')?mw:null}function is_array(a){return typeof(a)=='object'&&(a instanceof Array)}function getTime(){return new Date().getTime()}function debug(d,m){if(typeof d=='object'){var s=' ('+d.selector+')';d=d.debug}else{var s=''}if(!d)return false;if(typeof m=='string')m='carouFredSel'+s+': '+m;else m=['carouFredSel'+s+':',m];if(window.console&&window.console.log)window.console.log(m);return false}$.fn.caroufredsel=function(o){return this.carouFredSel(o)};$.extend($.easing,{'quadratic':function(t){var t2=t*t;return t*(-t2*t+4*t2-6*t+4)},'cubic':function(t){return t*(4*t*t-9*t+6)},'elastic':function(t){var t2=t*t;return t*(33*t2*t2-106*t2*t+126*t2-67*t+15)}})})(jQuery);

/*--------------------------------------------------------
SCROLL PANES
--------------------------------------------------------*/

/*
 * jScrollPane - v2.0.0beta11 - 2011-06-11
 * http://jscrollpane.kelvinluck.com/
 *
 * Copyright (c) 2010 Kelvin Luck
 * Dual licensed under the MIT and GPL licenses.
 */
(function(b,a,c){b.fn.jScrollPane=function(e){function d(D,O){var az,Q=this,Y,ak,v,am,T,Z,y,q,aA,aF,av,i,I,h,j,aa,U,aq,X,t,A,ar,af,an,G,l,au,ay,x,aw,aI,f,L,aj=true,P=true,aH=false,k=false,ap=D.clone(false,false).empty(),ac=b.fn.mwheelIntent?"mwheelIntent.jsp":"mousewheel.jsp";aI=D.css("paddingTop")+" "+D.css("paddingRight")+" "+D.css("paddingBottom")+" "+D.css("paddingLeft");f=(parseInt(D.css("paddingLeft"),10)||0)+(parseInt(D.css("paddingRight"),10)||0);function at(aR){var aM,aO,aN,aK,aJ,aQ,aP=false,aL=false;az=aR;if(Y===c){aJ=D.scrollTop();aQ=D.scrollLeft();D.css({overflow:"hidden",padding:0});ak=D.innerWidth()+f;v=D.innerHeight();D.width(ak);Y=b('<div class="jspPane" />').css("padding",aI).append(D.children());am=b('<div class="jspContainer" />').css({width:ak+"px",height:v+"px"}).append(Y).appendTo(D)}else{D.css("width","");aP=az.stickToBottom&&K();aL=az.stickToRight&&B();aK=D.innerWidth()+f!=ak||D.outerHeight()!=v;if(aK){ak=D.innerWidth()+f;v=D.innerHeight();am.css({width:ak+"px",height:v+"px"})}if(!aK&&L==T&&Y.outerHeight()==Z){D.width(ak);return}L=T;Y.css("width","");D.width(ak);am.find(">.jspVerticalBar,>.jspHorizontalBar").remove().end()}Y.css("overflow","auto");if(aR.contentWidth){T=aR.contentWidth}else{T=Y[0].scrollWidth}Z=Y[0].scrollHeight;Y.css("overflow","");y=T/ak;q=Z/v;aA=q>1;aF=y>1;if(!(aF||aA)){D.removeClass("jspScrollable");Y.css({top:0,width:am.width()-f});n();E();R();w();ai()}else{D.addClass("jspScrollable");aM=az.maintainPosition&&(I||aa);if(aM){aO=aD();aN=aB()}aG();z();F();if(aM){N(aL?(T-ak):aO,false);M(aP?(Z-v):aN,false)}J();ag();ao();if(az.enableKeyboardNavigation){S()}if(az.clickOnTrack){p()}C();if(az.hijackInternalLinks){m()}}if(az.autoReinitialise&&!aw){aw=setInterval(function(){at(az)},az.autoReinitialiseDelay)}else{if(!az.autoReinitialise&&aw){clearInterval(aw)}}aJ&&D.scrollTop(0)&&M(aJ,false);aQ&&D.scrollLeft(0)&&N(aQ,false);D.trigger("jsp-initialised",[aF||aA])}function aG(){if(aA){am.append(b('<div class="jspVerticalBar" />').append(b('<div class="jspCap jspCapTop" />'),b('<div class="jspTrack" />').append(b('<div class="jspDrag" />').append(b('<div class="jspDragTop" />'),b('<div class="jspDragBottom" />'))),b('<div class="jspCap jspCapBottom" />')));U=am.find(">.jspVerticalBar");aq=U.find(">.jspTrack");av=aq.find(">.jspDrag");if(az.showArrows){ar=b('<a class="jspArrow jspArrowUp" />').bind("mousedown.jsp",aE(0,-1)).bind("click.jsp",aC);af=b('<a class="jspArrow jspArrowDown" />').bind("mousedown.jsp",aE(0,1)).bind("click.jsp",aC);if(az.arrowScrollOnHover){ar.bind("mouseover.jsp",aE(0,-1,ar));af.bind("mouseover.jsp",aE(0,1,af))}al(aq,az.verticalArrowPositions,ar,af)}t=v;am.find(">.jspVerticalBar>.jspCap:visible,>.jspVerticalBar>.jspArrow").each(function(){t-=b(this).outerHeight()});av.hover(function(){av.addClass("jspHover")},function(){av.removeClass("jspHover")}).bind("mousedown.jsp",function(aJ){b("html").bind("dragstart.jsp selectstart.jsp",aC);av.addClass("jspActive");var s=aJ.pageY-av.position().top;b("html").bind("mousemove.jsp",function(aK){V(aK.pageY-s,false)}).bind("mouseup.jsp mouseleave.jsp",ax);return false});o()}}function o(){aq.height(t+"px");I=0;X=az.verticalGutter+aq.outerWidth();Y.width(ak-X-f);try{if(U.position().left===0){Y.css("margin-left",X+"px")}}catch(s){}}function z(){if(aF){am.append(b('<div class="jspHorizontalBar" />').append(b('<div class="jspCap jspCapLeft" />'),b('<div class="jspTrack" />').append(b('<div class="jspDrag" />').append(b('<div class="jspDragLeft" />'),b('<div class="jspDragRight" />'))),b('<div class="jspCap jspCapRight" />')));an=am.find(">.jspHorizontalBar");G=an.find(">.jspTrack");h=G.find(">.jspDrag");if(az.showArrows){ay=b('<a class="jspArrow jspArrowLeft" />').bind("mousedown.jsp",aE(-1,0)).bind("click.jsp",aC);x=b('<a class="jspArrow jspArrowRight" />').bind("mousedown.jsp",aE(1,0)).bind("click.jsp",aC);
if(az.arrowScrollOnHover){ay.bind("mouseover.jsp",aE(-1,0,ay));x.bind("mouseover.jsp",aE(1,0,x))}al(G,az.horizontalArrowPositions,ay,x)}h.hover(function(){h.addClass("jspHover")},function(){h.removeClass("jspHover")}).bind("mousedown.jsp",function(aJ){b("html").bind("dragstart.jsp selectstart.jsp",aC);h.addClass("jspActive");var s=aJ.pageX-h.position().left;b("html").bind("mousemove.jsp",function(aK){W(aK.pageX-s,false)}).bind("mouseup.jsp mouseleave.jsp",ax);return false});l=am.innerWidth();ah()}}function ah(){am.find(">.jspHorizontalBar>.jspCap:visible,>.jspHorizontalBar>.jspArrow").each(function(){l-=b(this).outerWidth()});G.width(l+"px");aa=0}function F(){if(aF&&aA){var aJ=G.outerHeight(),s=aq.outerWidth();t-=aJ;b(an).find(">.jspCap:visible,>.jspArrow").each(function(){l+=b(this).outerWidth()});l-=s;v-=s;ak-=aJ;G.parent().append(b('<div class="jspCorner" />').css("width",aJ+"px"));o();ah()}if(aF){Y.width((am.outerWidth()-f)+"px")}Z=Y.outerHeight();q=Z/v;if(aF){au=Math.ceil(1/y*l);if(au>az.horizontalDragMaxWidth){au=az.horizontalDragMaxWidth}else{if(au<az.horizontalDragMinWidth){au=az.horizontalDragMinWidth}}h.width(au+"px");j=l-au;ae(aa)}if(aA){A=Math.ceil(1/q*t);if(A>az.verticalDragMaxHeight){A=az.verticalDragMaxHeight}else{if(A<az.verticalDragMinHeight){A=az.verticalDragMinHeight}}av.height(A+"px");i=t-A;ad(I)}}function al(aK,aM,aJ,s){var aO="before",aL="after",aN;if(aM=="os"){aM=/Mac/.test(navigator.platform)?"after":"split"}if(aM==aO){aL=aM}else{if(aM==aL){aO=aM;aN=aJ;aJ=s;s=aN}}aK[aO](aJ)[aL](s)}function aE(aJ,s,aK){return function(){H(aJ,s,this,aK);this.blur();return false}}function H(aM,aL,aP,aO){aP=b(aP).addClass("jspActive");var aN,aK,aJ=true,s=function(){if(aM!==0){Q.scrollByX(aM*az.arrowButtonSpeed)}if(aL!==0){Q.scrollByY(aL*az.arrowButtonSpeed)}aK=setTimeout(s,aJ?az.initialDelay:az.arrowRepeatFreq);aJ=false};s();aN=aO?"mouseout.jsp":"mouseup.jsp";aO=aO||b("html");aO.bind(aN,function(){aP.removeClass("jspActive");aK&&clearTimeout(aK);aK=null;aO.unbind(aN)})}function p(){w();if(aA){aq.bind("mousedown.jsp",function(aO){if(aO.originalTarget===c||aO.originalTarget==aO.currentTarget){var aM=b(this),aP=aM.offset(),aN=aO.pageY-aP.top-I,aK,aJ=true,s=function(){var aS=aM.offset(),aT=aO.pageY-aS.top-A/2,aQ=v*az.scrollPagePercent,aR=i*aQ/(Z-v);if(aN<0){if(I-aR>aT){Q.scrollByY(-aQ)}else{V(aT)}}else{if(aN>0){if(I+aR<aT){Q.scrollByY(aQ)}else{V(aT)}}else{aL();return}}aK=setTimeout(s,aJ?az.initialDelay:az.trackClickRepeatFreq);aJ=false},aL=function(){aK&&clearTimeout(aK);aK=null;b(document).unbind("mouseup.jsp",aL)};s();b(document).bind("mouseup.jsp",aL);return false}})}if(aF){G.bind("mousedown.jsp",function(aO){if(aO.originalTarget===c||aO.originalTarget==aO.currentTarget){var aM=b(this),aP=aM.offset(),aN=aO.pageX-aP.left-aa,aK,aJ=true,s=function(){var aS=aM.offset(),aT=aO.pageX-aS.left-au/2,aQ=ak*az.scrollPagePercent,aR=j*aQ/(T-ak);if(aN<0){if(aa-aR>aT){Q.scrollByX(-aQ)}else{W(aT)}}else{if(aN>0){if(aa+aR<aT){Q.scrollByX(aQ)}else{W(aT)}}else{aL();return}}aK=setTimeout(s,aJ?az.initialDelay:az.trackClickRepeatFreq);aJ=false},aL=function(){aK&&clearTimeout(aK);aK=null;b(document).unbind("mouseup.jsp",aL)};s();b(document).bind("mouseup.jsp",aL);return false}})}}function w(){if(G){G.unbind("mousedown.jsp")}if(aq){aq.unbind("mousedown.jsp")}}function ax(){b("html").unbind("dragstart.jsp selectstart.jsp mousemove.jsp mouseup.jsp mouseleave.jsp");if(av){av.removeClass("jspActive")}if(h){h.removeClass("jspActive")}}function V(s,aJ){if(!aA){return}if(s<0){s=0}else{if(s>i){s=i}}if(aJ===c){aJ=az.animateScroll}if(aJ){Q.animate(av,"top",s,ad)}else{av.css("top",s);ad(s)}}function ad(aJ){if(aJ===c){aJ=av.position().top}am.scrollTop(0);I=aJ;var aM=I===0,aK=I==i,aL=aJ/i,s=-aL*(Z-v);if(aj!=aM||aH!=aK){aj=aM;aH=aK;D.trigger("jsp-arrow-change",[aj,aH,P,k])}u(aM,aK);Y.css("top",s);D.trigger("jsp-scroll-y",[-s,aM,aK]).trigger("scroll")}function W(aJ,s){if(!aF){return}if(aJ<0){aJ=0}else{if(aJ>j){aJ=j}}if(s===c){s=az.animateScroll}if(s){Q.animate(h,"left",aJ,ae)
}else{h.css("left",aJ);ae(aJ)}}function ae(aJ){if(aJ===c){aJ=h.position().left}am.scrollTop(0);aa=aJ;var aM=aa===0,aL=aa==j,aK=aJ/j,s=-aK*(T-ak);if(P!=aM||k!=aL){P=aM;k=aL;D.trigger("jsp-arrow-change",[aj,aH,P,k])}r(aM,aL);Y.css("left",s);D.trigger("jsp-scroll-x",[-s,aM,aL]).trigger("scroll")}function u(aJ,s){if(az.showArrows){ar[aJ?"addClass":"removeClass"]("jspDisabled");af[s?"addClass":"removeClass"]("jspDisabled")}}function r(aJ,s){if(az.showArrows){ay[aJ?"addClass":"removeClass"]("jspDisabled");x[s?"addClass":"removeClass"]("jspDisabled")}}function M(s,aJ){var aK=s/(Z-v);V(aK*i,aJ)}function N(aJ,s){var aK=aJ/(T-ak);W(aK*j,s)}function ab(aW,aR,aK){var aO,aL,aM,s=0,aV=0,aJ,aQ,aP,aT,aS,aU;try{aO=b(aW)}catch(aN){return}aL=aO.outerHeight();aM=aO.outerWidth();am.scrollTop(0);am.scrollLeft(0);while(!aO.is(".jspPane")){s+=aO.position().top;aV+=aO.position().left;aO=aO.offsetParent();if(/^body|html$/i.test(aO[0].nodeName)){return}}aJ=aB();aP=aJ+v;if(s<aJ||aR){aS=s-az.verticalGutter}else{if(s+aL>aP){aS=s-v+aL+az.verticalGutter}}if(aS){M(aS,aK)}aQ=aD();aT=aQ+ak;if(aV<aQ||aR){aU=aV-az.horizontalGutter}else{if(aV+aM>aT){aU=aV-ak+aM+az.horizontalGutter}}if(aU){N(aU,aK)}}function aD(){return -Y.position().left}function aB(){return -Y.position().top}function K(){var s=Z-v;return(s>20)&&(s-aB()<10)}function B(){var s=T-ak;return(s>20)&&(s-aD()<10)}function ag(){am.unbind(ac).bind(ac,function(aM,aN,aL,aJ){var aK=aa,s=I;Q.scrollBy(aL*az.mouseWheelSpeed,-aJ*az.mouseWheelSpeed,false);return aK==aa&&s==I})}function n(){am.unbind(ac)}function aC(){return false}function J(){Y.find(":input,a").unbind("focus.jsp").bind("focus.jsp",function(s){ab(s.target,false)})}function E(){Y.find(":input,a").unbind("focus.jsp")}function S(){var s,aJ,aL=[];aF&&aL.push(an[0]);aA&&aL.push(U[0]);Y.focus(function(){D.focus()});D.attr("tabindex",0).unbind("keydown.jsp keypress.jsp").bind("keydown.jsp",function(aO){if(aO.target!==this&&!(aL.length&&b(aO.target).closest(aL).length)){return}var aN=aa,aM=I;switch(aO.keyCode){case 40:case 38:case 34:case 32:case 33:case 39:case 37:s=aO.keyCode;aK();break;case 35:M(Z-v);s=null;break;case 36:M(0);s=null;break}aJ=aO.keyCode==s&&aN!=aa||aM!=I;return !aJ}).bind("keypress.jsp",function(aM){if(aM.keyCode==s){aK()}return !aJ});if(az.hideFocus){D.css("outline","none");if("hideFocus" in am[0]){D.attr("hideFocus",true)}}else{D.css("outline","");if("hideFocus" in am[0]){D.attr("hideFocus",false)}}function aK(){var aN=aa,aM=I;switch(s){case 40:Q.scrollByY(az.keyboardSpeed,false);break;case 38:Q.scrollByY(-az.keyboardSpeed,false);break;case 34:case 32:Q.scrollByY(v*az.scrollPagePercent,false);break;case 33:Q.scrollByY(-v*az.scrollPagePercent,false);break;case 39:Q.scrollByX(az.keyboardSpeed,false);break;case 37:Q.scrollByX(-az.keyboardSpeed,false);break}aJ=aN!=aa||aM!=I;return aJ}}function R(){D.attr("tabindex","-1").removeAttr("tabindex").unbind("keydown.jsp keypress.jsp")}function C(){if(location.hash&&location.hash.length>1){var aL,aJ,aK=escape(location.hash);try{aL=b(aK)}catch(s){return}if(aL.length&&Y.find(aK)){if(am.scrollTop()===0){aJ=setInterval(function(){if(am.scrollTop()>0){ab(aK,true);b(document).scrollTop(am.position().top);clearInterval(aJ)}},50)}else{ab(aK,true);b(document).scrollTop(am.position().top)}}}}function ai(){b("a.jspHijack").unbind("click.jsp-hijack").removeClass("jspHijack")}function m(){ai();b("a[href^=#]").addClass("jspHijack").bind("click.jsp-hijack",function(){var s=this.href.split("#"),aJ;if(s.length>1){aJ=s[1];if(aJ.length>0&&Y.find("#"+aJ).length>0){ab("#"+aJ,true);return false}}})}function ao(){var aK,aJ,aM,aL,aN,s=false;am.unbind("touchstart.jsp touchmove.jsp touchend.jsp click.jsp-touchclick").bind("touchstart.jsp",function(aO){var aP=aO.originalEvent.touches[0];aK=aD();aJ=aB();aM=aP.pageX;aL=aP.pageY;aN=false;s=true}).bind("touchmove.jsp",function(aR){if(!s){return}var aQ=aR.originalEvent.touches[0],aP=aa,aO=I;Q.scrollTo(aK+aM-aQ.pageX,aJ+aL-aQ.pageY);aN=aN||Math.abs(aM-aQ.pageX)>5||Math.abs(aL-aQ.pageY)>5;
return aP==aa&&aO==I}).bind("touchend.jsp",function(aO){s=false}).bind("click.jsp-touchclick",function(aO){if(aN){aN=false;return false}})}function g(){var s=aB(),aJ=aD();D.removeClass("jspScrollable").unbind(".jsp");D.replaceWith(ap.append(Y.children()));ap.scrollTop(s);ap.scrollLeft(aJ)}b.extend(Q,{reinitialise:function(aJ){aJ=b.extend({},az,aJ);at(aJ)},scrollToElement:function(aK,aJ,s){ab(aK,aJ,s)},scrollTo:function(aK,s,aJ){N(aK,aJ);M(s,aJ)},scrollToX:function(aJ,s){N(aJ,s)},scrollToY:function(s,aJ){M(s,aJ)},scrollToPercentX:function(aJ,s){N(aJ*(T-ak),s)},scrollToPercentY:function(aJ,s){M(aJ*(Z-v),s)},scrollBy:function(aJ,s,aK){Q.scrollByX(aJ,aK);Q.scrollByY(s,aK)},scrollByX:function(s,aK){var aJ=aD()+Math[s<0?"floor":"ceil"](s),aL=aJ/(T-ak);W(aL*j,aK)},scrollByY:function(s,aK){var aJ=aB()+Math[s<0?"floor":"ceil"](s),aL=aJ/(Z-v);V(aL*i,aK)},positionDragX:function(s,aJ){W(s,aJ)},positionDragY:function(aJ,s){V(aJ,s)},animate:function(aJ,aM,s,aL){var aK={};aK[aM]=s;aJ.animate(aK,{duration:az.animateDuration,ease:az.animateEase,queue:false,step:aL})},getContentPositionX:function(){return aD()},getContentPositionY:function(){return aB()},getContentWidth:function(){return T},getContentHeight:function(){return Z},getPercentScrolledX:function(){return aD()/(T-ak)},getPercentScrolledY:function(){return aB()/(Z-v)},getIsScrollableH:function(){return aF},getIsScrollableV:function(){return aA},getContentPane:function(){return Y},scrollToBottom:function(s){V(i,s)},hijackInternalLinks:function(){m()},destroy:function(){g()}});at(O)}e=b.extend({},b.fn.jScrollPane.defaults,e);b.each(["mouseWheelSpeed","arrowButtonSpeed","trackClickSpeed","keyboardSpeed"],function(){e[this]=e[this]||e.speed});return this.each(function(){var f=b(this),g=f.data("jsp");if(g){g.reinitialise(e)}else{g=new d(f,e);f.data("jsp",g)}})};b.fn.jScrollPane.defaults={showArrows:false,maintainPosition:true,stickToBottom:false,stickToRight:false,clickOnTrack:true,autoReinitialise:false,autoReinitialiseDelay:500,verticalDragMinHeight:0,verticalDragMaxHeight:99999,horizontalDragMinWidth:0,horizontalDragMaxWidth:99999,contentWidth:c,animateScroll:false,animateDuration:300,animateEase:"linear",hijackInternalLinks:false,verticalGutter:4,horizontalGutter:4,mouseWheelSpeed:0,arrowButtonSpeed:0,arrowRepeatFreq:50,arrowScrollOnHover:false,trackClickSpeed:0,trackClickRepeatFreq:70,verticalArrowPositions:"split",horizontalArrowPositions:"split",enableKeyboardNavigation:true,hideFocus:false,keyboardSpeed:0,initialDelay:300,speed:30,scrollPagePercent:0.8}})(jQuery,this);

////////////////////////////////
// Initialise Scroll Panes
////////////////////////////////

$(function() {$('.scroll-pane-50,.scroll-pane-80,.scroll-pane-90,.scroll-pane-100,.scroll-pane-125,.scroll-pane-150,.scroll-pane-175,.scroll-pane-200,.scroll-pane-250,.scroll-pane-300,.scroll-pane-350,.scroll-pane-400,.scroll-pane-450,.scroll-pane-500').jScrollPane({
        showArrows: false,
        autoReinitialise: true
    });
});

/*--------------------------------------------------------
THUMBNAIL HOVER
--------------------------------------------------------*/

$(document).ready(function() {
	$('.preview img').animate({'opacity' : 1}).hover(function() {
		$(this).animate({'opacity' : .3});
	}, function() {
		$(this).animate({'opacity' : 1});
	});
});

/*--------------------------------------------------------
SMART COLUMNS
--------------------------------------------------------*/

$(document).ready(function(){function smartColumns(){$("ul.smart_columns").css({'width':"100%"});var colWrap=$("ul.smart_columns").width();var colNum=Math.floor(colWrap/200);var colFixed=Math.floor(colWrap/colNum);$("ul.smart_columns").css({'width':colWrap});$("ul.smart_columns li").css({'width':colFixed})}smartColumns();$(window).resize(function(){smartColumns()})});

/*--------------------------------------------------------
ROTATING FOOTER TESTIMONIALS
--------------------------------------------------------*/

(function($){$.fn.quovolver=function(speed,delay){if(!speed)speed=500;if(!delay)delay=6000;var quaSpd=(speed*4);if(quaSpd>(delay))delay=quaSpd;var quote=$(this),firstQuo=$(this).filter(':first'),lastQuo=$(this).filter(':last'),wrapElem='<div id="quote_wrapper"></div>';$(this).wrapAll(wrapElem);$(this).hide();$(firstQuo).show();$(this).parent().css({height:$(firstQuo).height()});setInterval(function(){if($(lastQuo).is(':visible')){var nextElem=$(firstQuo);var wrapHeight=$(nextElem).height()}else{var nextElem=$(quote).filter(':visible').next();var wrapHeight=$(nextElem).height()}$(quote).filter(':visible').fadeOut(speed);setTimeout(function(){$(quote).parent().animate({height:wrapHeight},speed)},speed);if($(lastQuo).is(':visible')){setTimeout(function(){$(firstQuo).fadeIn(speed*2)},speed*2)}else{setTimeout(function(){$(nextElem).fadeIn(speed)},speed*2)}},delay)}})(jQuery);

/*--------------------------------------------------------
SELECT DROPDOWN SUBMIT FORM
--------------------------------------------------------*/

function select_go(targ,selObj,restore){
eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
if (restore) selObj.selectedIndex=0;
}

function select_sort_go(targ,selObj,restore){
eval("window.location='"+targ+"/"+selObj.options[selObj.selectedIndex].value+"'");
if (restore) selObj.selectedIndex=0;
}

/*--------------------------------------------------------
PAGE PEEL / CORNER CURL EFFECT
--------------------------------------------------------*/

$(document).ready(function(){$("#corner_curl").hover(function(){$("#corner_curl img , .corner_curl_reveal, .corner_curl_message").stop().animate({width:'300px',height:'300px'},{duration:150,easing:'easeInQuad'})},function(){$("#corner_curl img").stop().animate({width:'80px',height:'80px'},{duration:250,easing:'easeOutQuad'});$(".corner_curl_reveal").stop().animate({width:'80px',height:'80px'},{duration:250,easing:'easeOutQuad'});$(".corner_curl_message").stop().animate({width:'0px',height:'0px'},{duration:250,easing:'easeOutQuad'})})});

/*--------------------------------------------------------
SCROLL BACK TO TOP
--------------------------------------------------------*/

$(document).ready(function(){var pxShow=300;var fadeInTime=1000;var fadeOutTime=1000;var scrollSpeed=1000;$(window).scroll(function(){if($(window).scrollTop()>=pxShow){$("#back_to_top").fadeIn(fadeInTime)}else{$("#back_to_top").fadeOut(fadeOutTime)}});$('#back_to_top a').click(function(){$('html, body').animate({scrollTop:0},scrollSpeed);return false})});

/*--------------------------------------------------------
FOOTER NEWS SUBSCRIBE
--------------------------------------------------------*/

$(document).ready(function(){$('.subscribe_form').hide();$('.subscribe_form_reveal').click(function(){$('.subscribe_form').fadeIn('slow');return false});$("#subscribe_hide").click(function(){$(".subscribe_form").fadeOut('fast');return false})});

/*--------------------------------------------------------
HOMEPAGE TABS
--------------------------------------------------------*/

$(document).ready(function(){$(".abi_htab_content").hide();if($.cookie('active_htab')==null){$("ul.abi_htabs li:first-child").addClass("active").show();$(".abi_htab_content:first-child").show()}$("ul.abi_htabs li").click(function(){$("ul.abi_htabs li").removeClass("active");$(this).addClass("active");$(".abi_htab_content").hide();var active_htab=$(this).find('a').attr('rel');$(active_htab).fadeIn();return false});$(function(){$('div.abi_htabs').hide();if($.cookie('active_htab')==null){$('div.abi_htabs:first-child').show();$('ul.abi_htabs li:first-child').addClass('active').show()}else{$($.cookie('active_htab')).show();$('ul.abi_htabs a[rel='+$.cookie('active_htab')+']').parent().addClass('active').show()}$('ul.abi_htabs li').click(function(){$('ul.abi_htabs li').removeClass('active');$(this).addClass('active');$($(this).find('a').attr('rel')).show();$.cookie('active_htab',$(this).find('a').attr('rel'),{expires:30});return false})})});

/*--------------------------------------------------------
PRODUCT TABS
--------------------------------------------------------*/

$(document).ready(function(){$(".abi_ptab_content").hide();if($.cookie('active_ptab')==null){$("ul.abi_ptabs li:first-child").addClass("active").show();$(".abi_ptab_content:first-child").show()}$("ul.abi_ptabs li").click(function(){$("ul.abi_ptabs li").removeClass("active");$(this).addClass("active");$(".abi_ptab_content").hide();var active_ptab=$(this).find('a').attr('rel');$(active_ptab).fadeIn();return false});$(function(){$('div.abi_ptabs').hide();if($.cookie('active_ptab')==null){$('div.abi_ptabs:first-child').show();$('ul.abi_ptabs li:first-child').addClass('active').show()}else{$($.cookie('active_ptab')).show();$('ul.abi_ptabs a[rel='+$.cookie('active_ptab')+']').parent().addClass('active').show()}$('ul.abi_ptabs li').click(function(){$('ul.abi_ptabs li').removeClass('active');$(this).addClass('active');$($(this).find('a').attr('rel')).show();$.cookie('active_ptab',$(this).find('a').attr('rel'),{expires:30});return false})})});

/*--------------------------------------------------------
SPARE TABS
--------------------------------------------------------*/

$(document).ready(function(){$(".abi_stab_content").hide();if($.cookie('active_stab')==null){$("ul.abi_stabs li:first-child").addClass("active").show();$(".abi_stab_content:first-child").show()}$("ul.abi_stabs li").click(function(){$("ul.abi_stabs li").removeClass("active");$(this).addClass("active");$(".abi_stab_content").hide();var active_stab=$(this).find('a').attr('rel');$(active_stab).fadeIn();return false});$(function(){$('div.abi_stabs').hide();if($.cookie('active_stab')==null){$('div.abi_stabs:first-child').show();$('ul.abi_stabs li:first-child').addClass('active').show()}else{$($.cookie('active_stab')).show();$('ul.abi_stabs a[rel='+$.cookie('active_stab')+']').parent().addClass('active').show()}$('ul.abi_stabs li').click(function(){$('ul.abi_stabs li').removeClass('active');$(this).addClass('active');$($(this).find('a').attr('rel')).show();$.cookie('active_stab',$(this).find('a').attr('rel'),{expires:30});return false})})});

/*--------------------------------------------------------
DYNAMIC SEARCH
--------------------------------------------------------*/

function sack(file) {
	this.xmlhttp = null;

	this.resetData = function() {
		this.method = "POST";
  		this.queryStringSeparator = "?";
		this.argumentSeparator = "&";
		this.URLString = "";
		this.encodeURIString = true;
  		this.execute = false;
  		this.element = null;
		this.elementObj = null;
		this.requestFile = file;
		this.vars = new Object();
		this.responseStatus = new Array(2);
  	};

	this.resetFunctions = function() {
  		this.onLoading = function() { };
  		this.onLoaded = function() { };
  		this.onInteractive = function() { };
  		this.onCompletion = function() { };
  		this.onError = function() { };
		this.onFail = function() { };
	};

	this.reset = function() {
		this.resetFunctions();
		this.resetData();
	};

	this.createAJAX = function() {
		try {
			this.xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e1) {
			try {
				this.xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e2) {
				this.xmlhttp = null;
			}
		}

		if (! this.xmlhttp) {
			if (typeof XMLHttpRequest != "undefined") {
				this.xmlhttp = new XMLHttpRequest();
			} else {
				this.failed = true;
			}
		}
	};

	this.setVar = function(name, value){
		this.vars[name] = Array(value, false);
	};

	this.encVar = function(name, value, returnvars) {
		if (true == returnvars) {
			return Array(encodeURIComponent(name), encodeURIComponent(value));
		} else {
			this.vars[encodeURIComponent(name)] = Array(encodeURIComponent(value), true);
		}
	}

	this.processURLString = function(string, encode) {
		encoded = encodeURIComponent(this.argumentSeparator);
		regexp = new RegExp(this.argumentSeparator + "|" + encoded);
		varArray = string.split(regexp);
		for (i = 0; i < varArray.length; i++){
			urlVars = varArray[i].split("=");
			if (true == encode){
				this.encVar(urlVars[0], urlVars[1]);
			} else {
				this.setVar(urlVars[0], urlVars[1]);
			}
		}
	}

	this.createURLString = function(urlstring) {
		if (this.encodeURIString && this.URLString.length) {
			this.processURLString(this.URLString, true);
		}

		if (urlstring) {
			if (this.URLString.length) {
				this.URLString += this.argumentSeparator + urlstring;
			} else {
				this.URLString = urlstring;
			}
		}

		// prevents caching of URLString
		this.setVar("rndval", new Date().getTime());

		urlstringtemp = new Array();
		for (key in this.vars) {
			if (false == this.vars[key][1] && true == this.encodeURIString) {
				encoded = this.encVar(key, this.vars[key][0], true);
				delete this.vars[key];
				this.vars[encoded[0]] = Array(encoded[1], true);
				key = encoded[0];
			}

			urlstringtemp[urlstringtemp.length] = key + "=" + this.vars[key][0];
		}
		if (urlstring){
			this.URLString += this.argumentSeparator + urlstringtemp.join(this.argumentSeparator);
		} else {
			this.URLString += urlstringtemp.join(this.argumentSeparator);
		}
	}

	this.runResponse = function() {
		eval(this.response);
	}

	this.runAJAX = function(urlstring) {
		if (this.failed) {
			this.onFail();
		} else {
			this.createURLString(urlstring);
			if (this.element) {
				this.elementObj = document.getElementById(this.element);
			}
			if (this.xmlhttp) {
				var self = this;
				if (this.method == "GET") {
					totalurlstring = this.requestFile + this.queryStringSeparator + this.URLString;
					this.xmlhttp.open(this.method, totalurlstring, true);
				} else {
					this.xmlhttp.open(this.method, this.requestFile, true);
					try {
						this.xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded")
					} catch (e) { }
				}

				this.xmlhttp.onreadystatechange = function() {
					switch (self.xmlhttp.readyState) {
						case 1:
							self.onLoading();
							break;
						case 2:
							self.onLoaded();
							break;
						case 3:
							self.onInteractive();
							break;
						case 4:
							self.response = self.xmlhttp.responseText;
							self.responseXML = self.xmlhttp.responseXML;
							self.responseStatus[0] = self.xmlhttp.status;
							self.responseStatus[1] = self.xmlhttp.statusText;

							if (self.execute) {
								self.runResponse();
							}

							if (self.elementObj) {
								elemNodeName = self.elementObj.nodeName;
								elemNodeName.toLowerCase();
								if (elemNodeName == "input"
								|| elemNodeName == "select"
								|| elemNodeName == "option"
								|| elemNodeName == "textarea") {
									self.elementObj.value = self.response;
								} else {
									self.elementObj.innerHTML = self.response;
								}
							}
							if (self.responseStatus[0] == "200") {
								self.onCompletion();
							} else {
								self.onError();
							}

							self.URLString = "";
							break;
					}
				};

				this.xmlhttp.send(this.URLString);
			}
		}
	};

	this.reset();
	this.createAJAX();
}
// Autocomplete off in jQuery
$(document).ready(function() {
	$("#abi_search_input").attr("autocomplete", "off");
});

/////////////////////////////

var ajaxBox_offsetX = 0;
var ajaxBox_offsetY = 0;
var ajax_list_externalFile = 'ability_dynamic_search.php';	// Path to external file
var minimumLettersBeforeLookup = 3;	// Number of letters entered before a lookup is performed.

var ajax_list_objects = new Array();
var ajax_list_cachedLists = new Array();
var ajax_list_activeInput = false;
var ajax_list_activeItem;
var ajax_list_optionDivFirstItem = false;
var ajax_list_currentLetters = new Array();
var ajax_optionDiv = false;
var ajax_optionDiv_iframe = false;

var ajax_list_MSIE = false;
if(navigator.userAgent.indexOf('MSIE')>=0 && navigator.userAgent.indexOf('Opera')<0)ajax_list_MSIE=true;

function ajax_getTopPos(inputObj)
{
	
  var returnValue = inputObj.offsetTop;
  while((inputObj = inputObj.offsetParent) != null){
	returnValue += inputObj.offsetTop;
  }
  return returnValue;
}
function ajax_list_cancelEvent()
{
	return false;
}

function ajax_getLeftPos(inputObj)
{
  var returnValue = inputObj.offsetLeft;
  while((inputObj = inputObj.offsetParent) != null)returnValue += inputObj.offsetLeft;
  
  return returnValue;
}

function ajax_option_setValue(e,inputObj)
{
	if(!inputObj)inputObj=this;
	var tmpValue = inputObj.innerHTML;
	if(ajax_list_MSIE)tmpValue = inputObj.innerText;else tmpValue = inputObj.textContent;
	if(!tmpValue)tmpValue = inputObj.innerHTML;
	ajax_list_activeInput.value = tmpValue;
	if(document.getElementById(ajax_list_activeInput.name + '_hidden'))document.getElementById(ajax_list_activeInput.name + '_hidden').value = inputObj.id; 
	ajax_options_hide();
}

function ajax_options_hide()
{
	ajax_optionDiv.style.display='none';	
	if(ajax_optionDiv_iframe)ajax_optionDiv_iframe.style.display='none';
}

function ajax_options_rollOverActiveItem(item,fromKeyBoard)
{
	if(ajax_list_activeItem)ajax_list_activeItem.className='optionDiv';
	item.className='optionDivSelected';
	ajax_list_activeItem = item;
	
	if(fromKeyBoard){
		if(ajax_list_activeItem.offsetTop>ajax_optionDiv.offsetHeight){
			ajax_optionDiv.scrollTop = ajax_list_activeItem.offsetTop - ajax_optionDiv.offsetHeight + ajax_list_activeItem.offsetHeight + 2 ;
		}
		if(ajax_list_activeItem.offsetTop<ajax_optionDiv.scrollTop)
		{
			ajax_optionDiv.scrollTop = 0;	
		}
	}
}

function ajax_option_list_buildList(letters,paramToExternalFile)
{
	
	ajax_optionDiv.innerHTML = '';
	ajax_list_activeItem = false;
	if(ajax_list_cachedLists[paramToExternalFile][letters.toLowerCase()].length<=1){
		ajax_options_hide();
		return;			
	}
	
	
	
	ajax_list_optionDivFirstItem = false;
	var optionsAdded = false;
	for(var no=0;no<ajax_list_cachedLists[paramToExternalFile][letters.toLowerCase()].length;no++){
		if(ajax_list_cachedLists[paramToExternalFile][letters.toLowerCase()][no].length==0)continue;
		optionsAdded = true;
		var div = document.createElement('DIV');
		var items = ajax_list_cachedLists[paramToExternalFile][letters.toLowerCase()][no].split(/###/gi);
		
		if(ajax_list_cachedLists[paramToExternalFile][letters.toLowerCase()].length==1 && ajax_list_activeInput.value == items[0]){
			ajax_options_hide();
			return;						
		}
		
		
		div.innerHTML = items[items.length-1];
		div.id = items[0];
		div.className='optionDiv';
		div.onmouseover = function(){ ajax_options_rollOverActiveItem(this,false) }

		div.onclick = function(){document.location.href='product.php?productid='+this.id;} ;

		if(!ajax_list_optionDivFirstItem)ajax_list_optionDivFirstItem = div;
		ajax_optionDiv.appendChild(div);
	}	
	if(optionsAdded){
		ajax_optionDiv.style.display='block';
		if(ajax_optionDiv_iframe)ajax_optionDiv_iframe.style.display='';
	}

	if(optionsAdded){
		ajax_optionDiv.style.display='block';
		if(ajax_optionDiv_iframe)ajax_optionDiv_iframe.style.display='';
		ajax_options_rollOverActiveItem(ajax_list_optionDivFirstItem,true);
	}

}

function ajax_option_list_showContent(ajaxIndex,inputObj,paramToExternalFile)
{
	var letters = inputObj.value;
	var content = ajax_list_objects[ajaxIndex].response;
	var elements = content.split('|');
	ajax_list_cachedLists[paramToExternalFile][letters.toLowerCase()] = elements;
	ajax_option_list_buildList(letters,paramToExternalFile);
	
}

function ajax_option_resize(inputObj)
{
	ajax_optionDiv.style.top = (ajax_getTopPos(inputObj) + inputObj.offsetHeight + ajaxBox_offsetY) + 'px';
	ajax_optionDiv.style.left = (ajax_getLeftPos(inputObj) + ajaxBox_offsetX) + 'px';
	if(ajax_optionDiv_iframe){
		ajax_optionDiv_iframe.style.left = ajax_optionDiv.style.left;
		ajax_optionDiv_iframe.style.top = ajax_optionDiv.style.top;			
	}		
	
}

function ajax_showOptions(inputObj,paramToExternalFile,e)
{
	if(e.keyCode==13 || e.keyCode==9)return;
	if(ajax_list_currentLetters[inputObj.name]==inputObj.value)return;
	if(!ajax_list_cachedLists[paramToExternalFile])ajax_list_cachedLists[paramToExternalFile] = new Array();
	ajax_list_currentLetters[inputObj.name] = inputObj.value;
	if(!ajax_optionDiv){
		ajax_optionDiv = document.createElement('DIV');
		ajax_optionDiv.id = 'ajax_listOfOptions';	
		document.body.appendChild(ajax_optionDiv);
		
		if(ajax_list_MSIE){
			ajax_optionDiv_iframe = document.createElement('IFRAME');
			ajax_optionDiv_iframe.border='0';
			ajax_optionDiv_iframe.style.width = ajax_optionDiv.clientWidth + 'px';
			ajax_optionDiv_iframe.style.height = ajax_optionDiv.clientHeight + 'px';
			ajax_optionDiv_iframe.id = 'ajax_listOfOptions_iframe';
			
			document.body.appendChild(ajax_optionDiv_iframe);
		}
		
		var allInputs = document.getElementsByTagName('INPUT');
		for(var no=0;no<allInputs.length;no++){
			if(!allInputs[no].onkeyup)allInputs[no].onfocus = ajax_options_hide;
		}			
		var allSelects = document.getElementsByTagName('SELECT');
		for(var no=0;no<allSelects.length;no++){
			allSelects[no].onfocus = ajax_options_hide;
		}

		var oldonkeydown=document.body.onkeydown;
		if(typeof oldonkeydown!='function'){
			document.body.onkeydown=ajax_option_keyNavigation;
		}else{
			document.body.onkeydown=function(){
				oldonkeydown();
			ajax_option_keyNavigation() ;}
		}
		var oldonresize=document.body.onresize;
		if(typeof oldonresize!='function'){
			document.body.onresize=function() {ajax_option_resize(inputObj); };
		}else{
			document.body.onresize=function(){oldonresize();
			ajax_option_resize(inputObj) ;}
		}
			
	}
	
	if(inputObj.value.length<minimumLettersBeforeLookup){
		ajax_options_hide();
		return;
	}

	ajax_optionDiv.style.top = (ajax_getTopPos(inputObj) + inputObj.offsetHeight + ajaxBox_offsetY) + 'px';
	ajax_optionDiv.style.left = (ajax_getLeftPos(inputObj) + ajaxBox_offsetX) + 'px';
	if(ajax_optionDiv_iframe){
		ajax_optionDiv_iframe.style.left = ajax_optionDiv.style.left;
		ajax_optionDiv_iframe.style.top = ajax_optionDiv.style.top;			
	}
	
	ajax_list_activeInput = inputObj;
	ajax_optionDiv.onselectstart =  ajax_list_cancelEvent;
	
	if(ajax_list_cachedLists[paramToExternalFile][inputObj.value.toLowerCase()]){
		ajax_option_list_buildList(inputObj.value,paramToExternalFile);			
	}else{
		ajax_optionDiv.innerHTML = '';
		var ajaxIndex = ajax_list_objects.length;
		ajax_list_objects[ajaxIndex] = new sack();
		var url = ajax_list_externalFile + '?' + paramToExternalFile + '=1&letters=' + inputObj.value.replace(" ","+");
		ajax_list_objects[ajaxIndex].requestFile = url;	// Specifying which file to get
		ajax_list_objects[ajaxIndex].onCompletion = function(){ ajax_option_list_showContent(ajaxIndex,inputObj,paramToExternalFile); };	// Specify function that will be executed after file has been found
		ajax_list_objects[ajaxIndex].runAJAX();		// Execute AJAX function		
	}
	
		
}

function ajax_option_keyNavigation(e)
{
	if(document.all)e = event;
	
	if(!ajax_optionDiv)return;
	if(ajax_optionDiv.style.display=='none')return;
	
	if(e.keyCode==38){	// Up arrow
		if(!ajax_list_activeItem)return;
		if(ajax_list_activeItem && !ajax_list_activeItem.previousSibling)return;
		ajax_options_rollOverActiveItem(ajax_list_activeItem.previousSibling,true);
	}
	
	if(e.keyCode==40){	// Down arrow
		if(!ajax_list_activeItem){
			ajax_options_rollOverActiveItem(ajax_list_optionDivFirstItem,true);
		}else{
			if(!ajax_list_activeItem.nextSibling)return;
			ajax_options_rollOverActiveItem(ajax_list_activeItem.nextSibling,true);
		}
	}
	
	if(e.keyCode==13 || e.keyCode==9){	// Enter key or tab key
		if(ajax_list_activeItem && ajax_list_activeItem.className=='optionDivSelected')ajax_option_setValue(false,ajax_list_activeItem);
		if(e.keyCode==13)return false; else return true;
	}
	if(e.keyCode==27){	// Escape key
		ajax_options_hide();			
	}
}

// Fade Out Search Dropdown on click elsewhere
$(document).ready(function() {
	$("body:not(#ajax_listOfOptions)").click(function(){
		$("#ajax_listOfOptions_iframe,#ajax_listOfOptions").fadeOut('fast');
	});
});

/*--------------------------------------------------------
ADVANCED MINICART
--------------------------------------------------------*/

function minicart_update(){mc=$("#minicart-content");$.ajax({url:"./ability_minicart.php",cache:false,success:function(html){mc.text('');mc.append(html)}});$('html, body').animate({scrollTop:0},'slow');$('.cart_contents').fadeIn('slow');$('.cart_reveal').removeClass('cart_reveal').addClass('cart_hide');minicart_icon()}function minicart_delete(cartid){$.ajax({type:"GET",url:"./cart.php",data:"mode=delete&productindex="+cartid,success:function(){minicart_update()}})}function minicart_amount_minus(cartid){amount=document.getElementById("minicart-amount-"+cartid).innerHTML-1;if(amount==0){minicart_delete(cartid)}else{$.ajax({type:"GET",url:"./cart.php",data:"action=update&productindexes["+cartid+"]="+amount,success:function(){minicart_update_amount(cartid)}})}}function minicart_amount_plus(cartid){amount=document.getElementById("minicart-amount-"+cartid).innerHTML-0+1;$.ajax({type:"GET",url:"./cart.php",data:"action=update&productindexes["+cartid+"]="+amount,success:function(){minicart_update_amount(cartid)}})}function minicart_update_amount(cartid){ma=$("#minicart-amount-"+cartid);tc=$("#minicart-subtotal-cost");$.ajax({url:"./ability_minicart.php",type:"POST",data:"mode=update_amount&cartid="+cartid,success:function(html){eval(html);ma.text('');ma.append(amount);tc.text('');tc.append(subtotal_cost)}})}function minicart_clear(){$.ajax({type:"GET",url:"./cart.php",data:"mode=clear_cart",success:function(){minicart_update();$('.cart_contents').fadeOut('fast');$('.cart_hide').removeClass('cart_hide').addClass('cart_reveal');$('.mc_full').removeClass('mc_full').addClass('mc_empty')}})}function minicart_hide(){$('.cart_contents').fadeOut('fast');$('.cart_hide').removeClass('cart_hide').addClass('cart_reveal')}function minicart_icon(){tc=$("#minicart-subtotal-cost");if(tc||tc>0){$('.mc_empty').removeClass('mc_empty').addClass('mc_full')}}

/*--------------------------------------------------------
HEADER MINICART DROPDOWN
--------------------------------------------------------*/

$(document).ready(function(){$('.cart_contents').hide();$('.cart_reveal').click(function(){$('.cart_reveal').removeClass('cart_reveal').addClass('cart_hide');$('.cart_contents').fadeIn('slow')});$('.cart_hide').click(function(){$('.cart_hide').removeClass('cart_hide').addClass('cart_reveal');$('.cart_contents').fadeOut('fast')});$('#abi_menu_container,#abi_breadcrumbs_container,#abi_main_container,#abi_footer_container:not(.cart_contents,.cart_reveal,.cart_hide)').click(function(){$('.cart_hide').removeClass('cart_hide').addClass('cart_reveal');$('.cart_contents').fadeOut('fast')})});

/*--------------------------------------------------------
SHUFFLER
--------------------------------------------------------*/

/*
 * jQuery shuffle
 *
 * Copyright (c) 2008 Ca-Phun Ung <caphun at yelotofu dot com>
 * Dual licensed under the MIT (MIT-LICENSE.txt)
 * and GPL (GPL-LICENSE.txt) licenses.
 *
 * http://yelotofu.com/labs/jquery/snippets/shuffle/
 *
 * Shuffles an array or the children of a element container.
 * This uses the Fisher-Yates shuffle algorithm <http://jsfromhell.com/array/shuffle [v1.0]>
 */
 
(function($){

	$.fn.shuffle = function() {
		return this.each(function(){
			var items = $(this).children().clone(true);
			return (items.length) ? $(this).html($.shuffle(items)) : this;
		});
	}
	
	$.shuffle = function(arr) {
		for(var j, x, i = arr.length; i; j = parseInt(Math.random() * i), x = arr[--i], arr[i] = arr[j], arr[j] = x);
		return arr;
	}
	
})(jQuery);

