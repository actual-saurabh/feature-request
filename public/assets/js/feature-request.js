/* ========================================================================
 * Bootstrap: transition.js v3.3.1
 * http://getbootstrap.com/javascript/#transitions
 * ========================================================================
 * Copyright 2011-2014 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */
+function(a){"use strict";
// CSS TRANSITION SUPPORT (Shoutout: http://www.modernizr.com/)
// ============================================================
function b(){var a=document.createElement("bootstrap"),b={WebkitTransition:"webkitTransitionEnd",MozTransition:"transitionend",OTransition:"oTransitionEnd otransitionend",transition:"transitionend"};for(var c in b)if(void 0!==a.style[c])return{end:b[c]};return!1}
// http://blog.alexmaccaw.com/css-transitions
a.fn.emulateTransitionEnd=function(b){var c=!1,d=this;a(this).one("bsTransitionEnd",function(){c=!0});var e=function(){c||a(d).trigger(a.support.transition.end)};return setTimeout(e,b),this},a(function(){a.support.transition=b(),a.support.transition&&(a.event.special.bsTransitionEnd={bindType:a.support.transition.end,delegateType:a.support.transition.end,handle:function(b){return a(b.target).is(this)?b.handleObj.handler.apply(this,arguments):void 0}})})}(jQuery),jQuery(document).ready(function(a){var b=parseInt(feature_request.startPage)+1,c=parseInt(feature_request.maxPages),d=feature_request.nextLink,e=feature_request.label,f=feature_request.label_loading;c>=b&&a(".avfr-wrap").append('<div class="avfr-layout-main clearfix avfr-layout-main-'+b+'"></div>').append('<p class="avfr-loadmore fix"><a class="avfr-button" href="#">'+e+"</a></p>"),a(".avfr-loadmore a").click(function(){
// Are there more posts to load?
// Show that we're working.
return c>=b&&(a(this).text(f),a(".avfr-layout-main-"+b).load(d+" .avfr-entry-wrap",function(){
// Update page number and nextLink.
b++,d=d.replace(/\/page\/[0-9]?/,"/page/"+b),a(".avfr-loadmore").before('<div class="avfr-layout-main clearfix avfr-layout-main-'+b+'"></div>'),c>=b?a(".avfr-loadmore a").text(e):a(".avfr-loadmore a").fadeOut()})),!1})}),jQuery(document).ready(function(a){
// entry handler
function b(b,c,d){var e=h;if(""===a.trim(a("#avfr-entryform-title").val())){e.find("#avfr-entryform-title").css("border-color","#d9534f");var f=!1}if(""===a.trim(a("#avfr-entryform-description").val())){e.find("textarea[name='avfr-description']").css("border-color","#d9534f");var f=!1}return f===!1?!1:(e.find(":submit").attr("disabled","disabled"),a("#avfr-entry-form-results").show(),a("#avfr-entry-form-results").css({display:"inline-block","background-color":"#afafaf"}),void a("#avfr-entry-form-results p").html("Sending data ..."))}function c(b,c,d,f){a(e).html(b.message),a("#avfr-entry-form-results").css({display:"inline-block","background-color":"#DD6D5A"}),"false"==b.success?a(".avfr-modal-footer input").removeAttr("disabled"):(a("#avfr-entry-form-results").css("background-color","#53d96f"),setTimeout(function(){window.location=window.location.pathname},1e3))}
//vars
var d=feature_request.ajaxurl,e=a("#avfr-entry-form-results p"),f=feature_request.thanks_voting,g=feature_request.already_voted,h=(feature_request.error_message,feature_request.already_flagged,a("#avfr-entry-form")),i=a("#imgCaptcha").attr("src"),j=feature_request.user_email,k=feature_request.reached_limit,l=feature_request.confirm_flag,m={target:"#avfr-entry-form-results p",success:c,beforeSubmit:b,url:d};h.ajaxForm(m),
// When user like / dislike / vote up 1
a(".avfr-wrap").on("click",".avfr-submit",function(b){b.preventDefault();var c=a(this),e={action:"avfr_vote",post_id:c.data("post-id"),cfg:c.data("current-group"),// cfg = Current-Feature's Group
votes:c.hasClass("avfr-set-vote-up")?"+1":"-1",nonce:feature_request.nonce};null===localStorage.getItem("email")||"undefined"==localStorage.getItem("email")?e.voter_email=c.parent().find(".voter-email").val():e.voter_email=""!=j?j:localStorage.email,a.post(d,e,function(b){"success"==b.response?(a("#avfr-"+e.post_id).find(".avfr-tooltip").css({"margin-top":"5px"}),a("#avfr-"+e.post_id).find(".avfr-like").hide(),a("#avfr-"+e.post_id).find(".avfr-tooltip .voting-buttons").html(f),a("#avfr-"+e.post_id).find(".avfr-tooltip span").html(b.remaining),a("#avfr-"+c.data("post-id")+" .avfr-totals-num").html(b.total_votes),localStorage.email=e.voter_email):"already-voted"==b.response?alert(g):"reached-limit"==b.response?alert(k):"email-warning"==b.response?alert(b.warning):alert("Your remainig votes are "+b.response)})}),
// When user vote multiple
a(".avfr-wrap").on("click",".avfr-votes-value",function(b){b.preventDefault();var c=a(this),e={action:"avfr_vote",post_id:c.data("post-id"),votes:c.data("vote"),cfg:c.data("current-group"),// cfg = Current-Feature's Group
nonce:feature_request.nonce};null===localStorage.getItem("email")||"undefined"==localStorage.getItem("email")?e.voter_email=c.parent().find(".voter-email").val():e.voter_email=""!=j?j:localStorage.email,a.post(d,e,function(b){"success"==b.response?(c.parent().parent().css({"margin-top":"-5px"}),c.parent().addClass("avfr-voted"),c.parent().nextAll(".small-text").find("span").html(b.remaining),c.parent().html(f),a("#avfr-"+c.data("post-id")+" .avfr-totals-num").html(b.total_votes),localStorage.email=e.voter_email,a("#avfr-"+c.data("post-id")+" .avfr-vote-calc").addClass("voted"),a(".voted").hide()):"remaining-limit"==b.response?alert(remaining_limit):"already-voted"==b.response?alert(g):"email-warning"==b.response?alert(b.warning):alert("Your remainig votes are "+b.response)})}),
// When status chenged from fornt-end
a(document).on("click",".avfr-change-status",function(b){b.preventDefault();var c=a(this),e={action:"process_change_status",post_id:c.data("post-id"),new_status:c.data("val"),nonce:feature_request.nonce};a.post(d,e,function(a){"success"==a&&alert("Status changed.")})}),
// Whene user submit status change
a(".change-status-select").click(function(b){b.preventDefault();var c=a(this);c.change(function(){c.parent().nextAll(".avfr-change-status").attr("data-val",c.find("option:selected").val())})}),
//calc remaining votes like/dislike/votes
a(".avfr-wrap").on("click",".avfr-vote-calc",function(b){b.preventDefault();var c=a(this);c.nextAll(".avfr-tooltip").show(),b.stopPropagation();var e=c.hasClass("avfr-vote-up")?"avfr-set-vote-up":"avfr-set-vote-down";c.nextAll(".avfr-tooltip").find(".avfr-submit").removeClass("avfr-set-vote-up avfr-set-vote-down"),c.nextAll(".avfr-tooltip").find(".avfr-submit").addClass(e);var f={action:"avfr_calc_remaining_votes",post_id:c.data("post-id"),cfg:c.data("current-group"),nonce:feature_request.nonce};null===localStorage.getItem("email")||"undefined"==localStorage.getItem("email")?f.voter_email=c.parent().find(".voter-email").val():f.voter_email=""!=j?j:localStorage.email,a.post(d,f,function(b){c.nextAll(".avfr-tooltip").find("span").html(b.response),null!=localStorage.getItem("email")&&"undefined"!=localStorage.getItem("email")&&a(".voting-buttons-title").hide()})}),a("#imgCaptcha").on("load",function(){a("#reload").removeClass("avfr-reload-animation")}),a("#reload").click(function(b){b.preventDefault(),a("#imgCaptcha").attr("src",i+"?"+Math.random()),a(this).addClass("avfr-reload-animation")}),
// When user report (flag)
a(".avfr-flag").click(function(b){var c=confirm(l);if(1==c){b.preventDefault();var e=a(this),f={action:"avfr_add_flag",post_id:e.data("post-id"),cfg:e.data("current-group"),// cfg = Current Feature Group
nonce:feature_request.nonce};a.post(d,f,function(a){"success"==a.response?(e.addClass("avfr-flagged"),e.html(a.message)):"already-flagged"==a.response&&alert(a.message)})}}),a(document).on("click",".avfr-tooltip",function(b){a(this).nextAll(".avfr-tooltip").show(),b.stopPropagation()}),a(document).click(function(b){b.stopPropagation(),a(".avfr-tooltip").hide()}),
//Filter buttons current link
a(function(){a(".avfr-filter-control-item a").each(function(){a(this).prop("href")==window.location.href&&a(this).addClass("current")})})}),/*!
 * jQuery Form Plugin
 * version: 3.51.0-2014.06.20
 * Requires jQuery v1.5 or later
 * Copyright (c) 2014 M. Alsup
 * Examples and documentation at: http://malsup.com/jquery/form/
 * Project repository: https://github.com/malsup/form
 * Dual licensed under the MIT and GPL licenses.
 * https://github.com/malsup/form#copyright-and-license
 */
!function(a){"use strict";"function"==typeof define&&define.amd?define(["jquery"],a):a("undefined"!=typeof jQuery?jQuery:window.Zepto)}(function(a){"use strict";function b(b){var c=b.data;b.isDefaultPrevented()||(b.preventDefault(),a(b.target).ajaxSubmit(c))}function c(b){var c=b.target,d=a(c);if(!d.is("[type=submit],[type=image]")){var e=d.closest("[type=submit]");if(0===e.length)return;c=e[0]}var f=this;if(f.clk=c,"image"==c.type)if(void 0!==b.offsetX)f.clk_x=b.offsetX,f.clk_y=b.offsetY;else if("function"==typeof a.fn.offset){var g=d.offset();f.clk_x=b.pageX-g.left,f.clk_y=b.pageY-g.top}else f.clk_x=b.pageX-c.offsetLeft,f.clk_y=b.pageY-c.offsetTop;setTimeout(function(){f.clk=f.clk_x=f.clk_y=null},100)}function d(){if(a.fn.ajaxSubmit.debug){var b="[jquery.form] "+Array.prototype.join.call(arguments,"");window.console&&window.console.log?window.console.log(b):window.opera&&window.opera.postError&&window.opera.postError(b)}}var e={};e.fileapi=void 0!==a("<input type='file'/>").get(0).files,e.formdata=void 0!==window.FormData;var f=!!a.fn.prop;a.fn.attr2=function(){if(!f)return this.attr.apply(this,arguments);var a=this.prop.apply(this,arguments);return a&&a.jquery||"string"==typeof a?a:this.attr.apply(this,arguments)},a.fn.ajaxSubmit=function(b){function c(c){var d,e,f=a.param(c,b.traditional).split("&"),g=f.length,h=[];for(d=0;g>d;d++)f[d]=f[d].replace(/\+/g," "),e=f[d].split("="),h.push([decodeURIComponent(e[0]),decodeURIComponent(e[1])]);return h}function g(d){for(var e=new FormData,f=0;f<d.length;f++)e.append(d[f].name,d[f].value);if(b.extraData){var g=c(b.extraData);for(f=0;f<g.length;f++)g[f]&&e.append(g[f][0],g[f][1])}b.data=null;var h=a.extend(!0,{},a.ajaxSettings,b,{contentType:!1,processData:!1,cache:!1,type:i||"POST"});b.uploadProgress&&(h.xhr=function(){var c=a.ajaxSettings.xhr();return c.upload&&c.upload.addEventListener("progress",function(a){var c=0,d=a.loaded||a.position,e=a.total;a.lengthComputable&&(c=Math.ceil(d/e*100)),b.uploadProgress(a,d,e,c)},!1),c}),h.data=null;var j=h.beforeSend;return h.beforeSend=function(a,c){c.data=b.formData?b.formData:e,j&&j.call(this,a,c)},a.ajax(h)}function h(c){function e(a){var b=null;try{a.contentWindow&&(b=a.contentWindow.document)}catch(c){d("cannot get iframe.contentWindow document: "+c)}if(b)return b;try{b=a.contentDocument?a.contentDocument:a.document}catch(c){d("cannot get iframe.contentDocument: "+c),b=a.document}return b}function g(){function b(){try{var a=e(r).readyState;d("state = "+a),a&&"uninitialized"==a.toLowerCase()&&setTimeout(b,50)}catch(c){d("Server abort: ",c," (",c.name,")"),h(A),w&&clearTimeout(w),w=void 0}}var c=l.attr2("target"),f=l.attr2("action"),g="multipart/form-data",j=l.attr("enctype")||l.attr("encoding")||g;x.setAttribute("target",o),(!i||/post/i.test(i))&&x.setAttribute("method","POST"),f!=m.url&&x.setAttribute("action",m.url),m.skipEncodingOverride||i&&!/post/i.test(i)||l.attr({encoding:"multipart/form-data",enctype:"multipart/form-data"}),m.timeout&&(w=setTimeout(function(){v=!0,h(z)},m.timeout));var k=[];try{if(m.extraData)for(var n in m.extraData)m.extraData.hasOwnProperty(n)&&k.push(a.isPlainObject(m.extraData[n])&&m.extraData[n].hasOwnProperty("name")&&m.extraData[n].hasOwnProperty("value")?a('<input type="hidden" name="'+m.extraData[n].name+'">').val(m.extraData[n].value).appendTo(x)[0]:a('<input type="hidden" name="'+n+'">').val(m.extraData[n]).appendTo(x)[0]);m.iframeTarget||q.appendTo("body"),r.attachEvent?r.attachEvent("onload",h):r.addEventListener("load",h,!1),setTimeout(b,15);try{x.submit()}catch(p){var s=document.createElement("form").submit;s.apply(x)}}finally{x.setAttribute("action",f),x.setAttribute("enctype",j),c?x.setAttribute("target",c):l.removeAttr("target"),a(k).remove()}}function h(b){if(!s.aborted&&!F){if(E=e(r),E||(d("cannot access response document"),b=A),b===z&&s)return s.abort("timeout"),void y.reject(s,"timeout");if(b==A&&s)return s.abort("server abort"),void y.reject(s,"error","server abort");if(E&&E.location.href!=m.iframeSrc||v){r.detachEvent?r.detachEvent("onload",h):r.removeEventListener("load",h,!1);var c,f="success";try{if(v)throw"timeout";var g="xml"==m.dataType||E.XMLDocument||a.isXMLDoc(E);if(d("isXml="+g),!g&&window.opera&&(null===E.body||!E.body.innerHTML)&&--G)return d("requeing onLoad callback, DOM not available"),void setTimeout(h,250);var i=E.body?E.body:E.documentElement;s.responseText=i?i.innerHTML:null,s.responseXML=E.XMLDocument?E.XMLDocument:E,g&&(m.dataType="xml"),s.getResponseHeader=function(a){var b={"content-type":m.dataType};return b[a.toLowerCase()]},i&&(s.status=Number(i.getAttribute("status"))||s.status,s.statusText=i.getAttribute("statusText")||s.statusText);var j=(m.dataType||"").toLowerCase(),k=/(json|script|text)/.test(j);if(k||m.textarea){var l=E.getElementsByTagName("textarea")[0];if(l)s.responseText=l.value,s.status=Number(l.getAttribute("status"))||s.status,s.statusText=l.getAttribute("statusText")||s.statusText;else if(k){var o=E.getElementsByTagName("pre")[0],p=E.getElementsByTagName("body")[0];o?s.responseText=o.textContent?o.textContent:o.innerText:p&&(s.responseText=p.textContent?p.textContent:p.innerText)}}else"xml"==j&&!s.responseXML&&s.responseText&&(s.responseXML=H(s.responseText));try{D=J(s,j,m)}catch(t){f="parsererror",s.error=c=t||f}}catch(t){d("error caught: ",t),f="error",s.error=c=t||f}s.aborted&&(d("upload aborted"),f=null),s.status&&(f=s.status>=200&&s.status<300||304===s.status?"success":"error"),"success"===f?(m.success&&m.success.call(m.context,D,"success",s),y.resolve(s.responseText,"success",s),n&&a.event.trigger("ajaxSuccess",[s,m])):f&&(void 0===c&&(c=s.statusText),m.error&&m.error.call(m.context,s,f,c),y.reject(s,"error",c),n&&a.event.trigger("ajaxError",[s,m,c])),n&&a.event.trigger("ajaxComplete",[s,m]),n&&!--a.active&&a.event.trigger("ajaxStop"),m.complete&&m.complete.call(m.context,s,f),F=!0,m.timeout&&clearTimeout(w),setTimeout(function(){m.iframeTarget?q.attr("src",m.iframeSrc):q.remove(),s.responseXML=null},100)}}}var j,k,m,n,o,q,r,s,t,u,v,w,x=l[0],y=a.Deferred();if(y.abort=function(a){s.abort(a)},c)for(k=0;k<p.length;k++)j=a(p[k]),f?j.prop("disabled",!1):j.removeAttr("disabled");if(m=a.extend(!0,{},a.ajaxSettings,b),m.context=m.context||m,o="jqFormIO"+(new Date).getTime(),m.iframeTarget?(q=a(m.iframeTarget),u=q.attr2("name"),u?o=u:q.attr2("name",o)):(q=a('<iframe name="'+o+'" src="'+m.iframeSrc+'" />'),q.css({position:"absolute",top:"-1000px",left:"-1000px"})),r=q[0],s={aborted:0,responseText:null,responseXML:null,status:0,statusText:"n/a",getAllResponseHeaders:function(){},getResponseHeader:function(){},setRequestHeader:function(){},abort:function(b){var c="timeout"===b?"timeout":"aborted";d("aborting upload... "+c),this.aborted=1;try{r.contentWindow.document.execCommand&&r.contentWindow.document.execCommand("Stop")}catch(e){}q.attr("src",m.iframeSrc),s.error=c,m.error&&m.error.call(m.context,s,c,b),n&&a.event.trigger("ajaxError",[s,m,c]),m.complete&&m.complete.call(m.context,s,c)}},n=m.global,n&&0===a.active++&&a.event.trigger("ajaxStart"),n&&a.event.trigger("ajaxSend",[s,m]),m.beforeSend&&m.beforeSend.call(m.context,s,m)===!1)return m.global&&a.active--,y.reject(),y;if(s.aborted)return y.reject(),y;t=x.clk,t&&(u=t.name,u&&!t.disabled&&(m.extraData=m.extraData||{},m.extraData[u]=t.value,"image"==t.type&&(m.extraData[u+".x"]=x.clk_x,m.extraData[u+".y"]=x.clk_y)));var z=1,A=2,B=a("meta[name=csrf-token]").attr("content"),C=a("meta[name=csrf-param]").attr("content");C&&B&&(m.extraData=m.extraData||{},m.extraData[C]=B),m.forceSync?g():setTimeout(g,10);var D,E,F,G=50,H=a.parseXML||function(a,b){return window.ActiveXObject?(b=new ActiveXObject("Microsoft.XMLDOM"),b.async="false",b.loadXML(a)):b=(new DOMParser).parseFromString(a,"text/xml"),b&&b.documentElement&&"parsererror"!=b.documentElement.nodeName?b:null},I=a.parseJSON||function(a){return window.eval("("+a+")")},J=function(b,c,d){var e=b.getResponseHeader("content-type")||"",f="xml"===c||!c&&e.indexOf("xml")>=0,g=f?b.responseXML:b.responseText;return f&&"parsererror"===g.documentElement.nodeName&&a.error&&a.error("parsererror"),d&&d.dataFilter&&(g=d.dataFilter(g,c)),"string"==typeof g&&("json"===c||!c&&e.indexOf("json")>=0?g=I(g):("script"===c||!c&&e.indexOf("javascript")>=0)&&a.globalEval(g)),g};return y}if(!this.length)return d("ajaxSubmit: skipping submit process - no element selected"),this;var i,j,k,l=this;"function"==typeof b?b={success:b}:void 0===b&&(b={}),i=b.type||this.attr2("method"),j=b.url||this.attr2("action"),k="string"==typeof j?a.trim(j):"",k=k||window.location.href||"",k&&(k=(k.match(/^([^#]+)/)||[])[1]),b=a.extend(!0,{url:k,success:a.ajaxSettings.success,type:i||a.ajaxSettings.type,iframeSrc:/^https/i.test(window.location.href||"")?"javascript:false":"about:blank"},b);var m={};if(this.trigger("form-pre-serialize",[this,b,m]),m.veto)return d("ajaxSubmit: submit vetoed via form-pre-serialize trigger"),this;if(b.beforeSerialize&&b.beforeSerialize(this,b)===!1)return d("ajaxSubmit: submit aborted via beforeSerialize callback"),this;var n=b.traditional;void 0===n&&(n=a.ajaxSettings.traditional);var o,p=[],q=this.formToArray(b.semantic,p);if(b.data&&(b.extraData=b.data,o=a.param(b.data,n)),b.beforeSubmit&&b.beforeSubmit(q,this,b)===!1)return d("ajaxSubmit: submit aborted via beforeSubmit callback"),this;if(this.trigger("form-submit-validate",[q,this,b,m]),m.veto)return d("ajaxSubmit: submit vetoed via form-submit-validate trigger"),this;var r=a.param(q,n);o&&(r=r?r+"&"+o:o),"GET"==b.type.toUpperCase()?(b.url+=(b.url.indexOf("?")>=0?"&":"?")+r,b.data=null):b.data=r;var s=[];if(b.resetForm&&s.push(function(){l.resetForm()}),b.clearForm&&s.push(function(){l.clearForm(b.includeHidden)}),!b.dataType&&b.target){var t=b.success||function(){};s.push(function(c){var d=b.replaceTarget?"replaceWith":"html";a(b.target)[d](c).each(t,arguments)})}else b.success&&s.push(b.success);if(b.success=function(a,c,d){for(var e=b.context||this,f=0,g=s.length;g>f;f++)s[f].apply(e,[a,c,d||l,l])},b.error){var u=b.error;b.error=function(a,c,d){var e=b.context||this;u.apply(e,[a,c,d,l])}}if(b.complete){var v=b.complete;b.complete=function(a,c){var d=b.context||this;v.apply(d,[a,c,l])}}var w=a("input[type=file]:enabled",this).filter(function(){return""!==a(this).val()}),x=w.length>0,y="multipart/form-data",z=l.attr("enctype")==y||l.attr("encoding")==y,A=e.fileapi&&e.formdata;d("fileAPI :"+A);var B,C=(x||z)&&!A;b.iframe!==!1&&(b.iframe||C)?b.closeKeepAlive?a.get(b.closeKeepAlive,function(){B=h(q)}):B=h(q):B=(x||z)&&A?g(q):a.ajax(b),l.removeData("jqxhr").data("jqxhr",B);for(var D=0;D<p.length;D++)p[D]=null;return this.trigger("form-submit-notify",[this,b]),this},a.fn.ajaxForm=function(e){if(e=e||{},e.delegation=e.delegation&&a.isFunction(a.fn.on),!e.delegation&&0===this.length){var f={s:this.selector,c:this.context};return!a.isReady&&f.s?(d("DOM not ready, queuing ajaxForm"),a(function(){a(f.s,f.c).ajaxForm(e)}),this):(d("terminating; zero elements found by selector"+(a.isReady?"":" (DOM not ready)")),this)}return e.delegation?(a(document).off("submit.form-plugin",this.selector,b).off("click.form-plugin",this.selector,c).on("submit.form-plugin",this.selector,e,b).on("click.form-plugin",this.selector,e,c),this):this.ajaxFormUnbind().bind("submit.form-plugin",e,b).bind("click.form-plugin",e,c)},a.fn.ajaxFormUnbind=function(){return this.unbind("submit.form-plugin click.form-plugin")},a.fn.formToArray=function(b,c){var d=[];if(0===this.length)return d;var f,g=this[0],h=this.attr("id"),i=b?g.getElementsByTagName("*"):g.elements;if(i&&!/MSIE [678]/.test(navigator.userAgent)&&(i=a(i).get()),h&&(f=a(':input[form="'+h+'"]').get(),f.length&&(i=(i||[]).concat(f))),!i||!i.length)return d;var j,k,l,m,n,o,p;for(j=0,o=i.length;o>j;j++)if(n=i[j],l=n.name,l&&!n.disabled)if(b&&g.clk&&"image"==n.type)g.clk==n&&(d.push({name:l,value:a(n).val(),type:n.type}),d.push({name:l+".x",value:g.clk_x},{name:l+".y",value:g.clk_y}));else if(m=a.fieldValue(n,!0),m&&m.constructor==Array)for(c&&c.push(n),k=0,p=m.length;p>k;k++)d.push({name:l,value:m[k]});else if(e.fileapi&&"file"==n.type){c&&c.push(n);var q=n.files;if(q.length)for(k=0;k<q.length;k++)d.push({name:l,value:q[k],type:n.type});else d.push({name:l,value:"",type:n.type})}else null!==m&&"undefined"!=typeof m&&(c&&c.push(n),d.push({name:l,value:m,type:n.type,required:n.required}));if(!b&&g.clk){var r=a(g.clk),s=r[0];l=s.name,l&&!s.disabled&&"image"==s.type&&(d.push({name:l,value:r.val()}),d.push({name:l+".x",value:g.clk_x},{name:l+".y",value:g.clk_y}))}return d},a.fn.formSerialize=function(b){return a.param(this.formToArray(b))},a.fn.fieldSerialize=function(b){var c=[];return this.each(function(){var d=this.name;if(d){var e=a.fieldValue(this,b);if(e&&e.constructor==Array)for(var f=0,g=e.length;g>f;f++)c.push({name:d,value:e[f]});else null!==e&&"undefined"!=typeof e&&c.push({name:this.name,value:e})}}),a.param(c)},a.fn.fieldValue=function(b){for(var c=[],d=0,e=this.length;e>d;d++){var f=this[d],g=a.fieldValue(f,b);null===g||"undefined"==typeof g||g.constructor==Array&&!g.length||(g.constructor==Array?a.merge(c,g):c.push(g))}return c},a.fieldValue=function(b,c){var d=b.name,e=b.type,f=b.tagName.toLowerCase();if(void 0===c&&(c=!0),c&&(!d||b.disabled||"reset"==e||"button"==e||("checkbox"==e||"radio"==e)&&!b.checked||("submit"==e||"image"==e)&&b.form&&b.form.clk!=b||"select"==f&&-1==b.selectedIndex))return null;if("select"==f){var g=b.selectedIndex;if(0>g)return null;for(var h=[],i=b.options,j="select-one"==e,k=j?g+1:i.length,l=j?g:0;k>l;l++){var m=i[l];if(m.selected){var n=m.value;if(n||(n=m.attributes&&m.attributes.value&&!m.attributes.value.specified?m.text:m.value),j)return n;h.push(n)}}return h}return a(b).val()},a.fn.clearForm=function(b){return this.each(function(){a("input,select,textarea",this).clearFields(b)})},a.fn.clearFields=a.fn.clearInputs=function(b){var c=/^(?:color|date|datetime|email|month|number|password|range|search|tel|text|time|url|week)$/i;return this.each(function(){var d=this.type,e=this.tagName.toLowerCase();c.test(d)||"textarea"==e?this.value="":"checkbox"==d||"radio"==d?this.checked=!1:"select"==e?this.selectedIndex=-1:"file"==d?/MSIE/.test(navigator.userAgent)?a(this).replaceWith(a(this).clone(!0)):a(this).val(""):b&&(b===!0&&/hidden/.test(d)||"string"==typeof b&&a(this).is(b))&&(this.value="")})},a.fn.resetForm=function(){return this.each(function(){("function"==typeof this.reset||"object"==typeof this.reset&&!this.reset.nodeType)&&this.reset()})},a.fn.enable=function(a){return void 0===a&&(a=!0),this.each(function(){this.disabled=!a})},a.fn.selected=function(b){return void 0===b&&(b=!0),this.each(function(){var c=this.type;if("checkbox"==c||"radio"==c)this.checked=b;else if("option"==this.tagName.toLowerCase()){var d=a(this).parent("select");b&&d[0]&&"select-one"==d[0].type&&d.find("option").selected(!1),this.selected=b}})},a.fn.ajaxSubmit.debug=!1}),/**
 * jQuery TextExt Plugin
 * http://textextjs.com
 *
 * @version 1.3.1
 * @copyright Copyright (C) 2011 Alex Gorbatchev. All rights reserved.
 * @license MIT License
 */
function(a,b){/**
	 * TextExt is the main core class which by itself doesn't provide any functionality
	 * that is user facing, however it has the underlying mechanics to bring all the
	 * plugins together under one roof and make them work with each other or on their
	 * own.
	 *
	 * @author agorbatchev
	 * @date 2011/08/19
	 * @id TextExt
	 */
function c(){}/**
	 * ItemManager is used to seamlessly convert between string that come from the user input to whatever 
	 * the format the item data is being passed around in. It's used by all plugins that in one way or 
	 * another operate with items, such as Tags, Filter, Autocomplete and Suggestions. Default implementation 
	 * works with `String` type. 
	 *
	 * Each instance of `TextExt` creates a new instance of default implementation of `ItemManager`
	 * unless `itemManager` option was set to another implementation.
	 *
	 * To satisfy requirements of managing items of type other than a `String`, different implementation
	 * if `ItemManager` should be supplied.
	 *
	 * If you wish to bring your own implementation, you need to create a new class and implement all the 
	 * methods that `ItemManager` has. After, you need to supply your pass via the `itemManager` option during
	 * initialization like so:
	 *
	 *     $('#input').textext({
	 *         itemManager : CustomItemManager
	 *     })
	 *
	 * @author agorbatchev
	 * @date 2011/08/19
	 * @id ItemManager
	 */
function d(){}/**
	 * TextExtPlugin is a base class for all plugins. It provides common methods which are reused
	 * by majority of plugins.
	 *
	 * All plugins must register themselves by calling the `$.fn.textext.addPlugin(name, constructor)`
	 * function while providing plugin name and constructor. The plugin name is the same name that user
	 * will identify the plugin in the `plugins` option when initializing TextExt component and constructor
	 * function will create a new instance of the plugin. *Without registering, the core won't
	 * be able to see the plugin.*
	 *
	 * <span class="new label version">new in 1.2.0</span> You can get instance of each plugin from the core 
	 * via associated function with the same name as the plugin. For example:
	 *
	 *     $('#input').textext()[0].tags()
	 *     $('#input').textext()[0].autocomplete()
	 *     ...
	 *
	 * @author agorbatchev
	 * @date 2011/08/19
	 * @id TextExtPlugin
	 */
function e(){}/**
	 * Returns object property by name where name is dot-separated and object is multiple levels deep.
	 * @param target Object Source object.
	 * @param name String Dot separated property name, ie `foo.bar.world`
	 * @id core.getProperty
	 */
function f(a,b){"string"==typeof b&&(b=b.split("."));var c,d=b.join(".").replace(/\.(\w)/g,function(a,b){return b.toUpperCase()}),e=b.shift();
// name.length here should be zero
return typeof(c=a[d])!=l?c=c:typeof(c=a[e])!=l&&b.length>0&&(c=f(c,b)),c}/**
	 * Hooks up specified events in the scope of the current object.
	 * @author agorbatchev
	 * @date 2011/08/09
	 */
function g(){function a(a,b){e.bind(a,function(){
// apply handler to our PLUGIN object, not the target
return b.apply(d,arguments)})}var b,c=k.apply(arguments),d=this,e=1===c.length?d:c.shift();c=c[0]||{};for(b in c)a(b,c[b])}function h(a,b){return{input:a,form:b}}var i,j=(JSON||{}).stringify,k=Array.prototype.slice,l="undefined",/**
		 * TextExt provides a way to pass in the options to configure the core as well as
		 * each plugin that is being currently used. The jQuery exposed plugin `$().textext()` 
		 * function takes a hash object with key/value set of options. For example:
		 *
		 *     $('textarea').textext({
		 *         enabled: true
		 *     })
		 *
		 * There are multiple ways of passing in the options:
		 *
		 * 1. Options could be nested multiple levels deep and accessed using all lowercased, dot
		 * separated style, eg `foo.bar.world`. The manual is using this style for clarity and
		 * consistency. For example:
		 *
		 *        {
		 *            item: {
		 *                manager: ...
		 *            },
		 *
		 *            html: {
		 *                wrap: ...
		 *            },
		 *
		 *            autocomplete: {
		 *                enabled: ...,
		 *                dropdown: {
		 *                   position: ...
		 *                }
		 *            }
		 *        }
		 *
		 * 2. Options could be specified using camel cased names in a flat key/value fashion like so:
		 *
		 *        {
		 *            itemManager: ...,
		 *            htmlWrap: ...,
		 *            autocompleteEnabled: ...,
		 *            autocompleteDropdownPosition: ...
		 *        }
		 *
		 * 3. Finally, options could be specified in mixed style. It's important to understand that
		 * for each dot separated name, its alternative in camel case is also checked for, eg for 
		 * `foo.bar.world` it's alternatives could be `fooBarWorld`, `foo.barWorld` or `fooBar.world`, 
		 * which translates to `{ foo: { bar: { world: ... } } }`, `{ fooBarWorld: ... }`, 
		 * `{ foo : { barWorld : ... } }` or `{ fooBar: { world: ... } }` respectively. For example:
		 *
		 *        {
		 *            itemManager : ...,
		 *            htmlWrap: ...,
		 *            autocomplete: {
		 *                enabled: ...,
		 *                dropdownPosition: ...
		 *            }
		 *        }
		 *
		 * Mixed case is used through out the code, wherever it seems appropriate. However in the code, all option
		 * names are specified in the dot notation because it works both ways where as camel case is not
		 * being converted to its alternative dot notation.
		 *
		 * @author agorbatchev
		 * @date 2011/08/17
		 * @id TextExt.options
		 */
/**
		 * Default instance of `ItemManager` which takes `String` type as default for tags.
		 *
		 * @name item.manager
		 * @default ItemManager
		 * @author agorbatchev
		 * @date 2011/08/19
		 * @id TextExt.options.item.manager
		 */
m="item.manager",/**
		 * List of plugins that should be used with the current instance of TextExt. The list could be
		 * specified as array of strings or as comma or space separated string.
		 *
		 * @name plugins
		 * @default []
		 * @author agorbatchev
		 * @date 2011/08/19
		 * @id TextExt.options.plugins
		 */
n="plugins",/**
		 * TextExt allows for overriding of virtually any method that the core or any of its plugins
		 * use. This could be accomplished through the use of the `ext` option.
		 *
		 * It's possible to specifically target the core or any plugin, as well as overwrite all the
		 * desired methods everywhere.
		 *
		 * 1. Targeting the core:
		 *
		 *        ext: {
		 *            core: {
		 *                trigger: function()
		 *                {
		 *                    console.log('TextExt.trigger', arguments);
		 *                    $.fn.textext.TextExt.prototype.trigger.apply(this, arguments);
		 *                }
		 *            }
		 *        }
		 *
		 * 2. Targeting individual plugins:
		 *
		 *        ext: {
		 *            tags: {
		 *                addTags: function(tags)
		 *                {
		 *                    console.log('TextExtTags.addTags', tags);
		 *                    $.fn.textext.TextExtTags.prototype.addTags.apply(this, arguments);
		 *                }
		 *            }
		 *        }
		 *
		 * 3. Targeting `ItemManager` instance:
		 *
		 *        ext: {
		 *            itemManager: {
		 *                stringToItem: function(str)
		 *                {
		 *                    console.log('ItemManager.stringToItem', str);
		 *                    return $.fn.textext.ItemManager.prototype.stringToItem.apply(this, arguments);
		 *                }
		 *            }
		 *        }
		 *
		 * 4. And finally, in edge cases you can extend everything at once:
		 *
		 *        ext: {
		 *            '*': {
		 *                fooBar: function() {}
		 *            }
		 *        }
		 *
		 * @name ext
		 * @default {}
		 * @author agorbatchev
		 * @date 2011/08/19
		 * @id TextExt.options.ext
		 */
o="ext",/**
		 * HTML source that is used to generate elements necessary for the core and all other
		 * plugins to function.
		 *
		 * @name html.wrap
		 * @default '<div class="text-core"><div class="text-wrap"/></div>'
		 * @author agorbatchev
		 * @date 2011/08/19
		 * @id TextExt.options.html.wrap
		 */
p="html.wrap",/**
		 * HTML source that is used to generate hidden input value of which will be submitted 
		 * with the HTML form.
		 *
		 * @name html.hidden
		 * @default '<input type="hidden" />'
		 * @author agorbatchev
		 * @date 2011/08/20
		 * @id TextExt.options.html.hidden
		 */
q="html.hidden",/**
		 * Hash table of key codes and key names for which special events will be created
		 * by the core. For each entry a `[name]KeyDown`, `[name]KeyUp` and `[name]KeyPress` events 
		 * will be triggered along side with `anyKeyUp` and `anyKeyDown` events for every 
		 * key stroke.
		 *
		 * Here's a list of default keys:
		 *
		 *     {
		 *         8   : 'backspace',
		 *         9   : 'tab',
		 *         13  : 'enter!',
		 *         27  : 'escape!',
		 *         37  : 'left',
		 *         38  : 'up!',
		 *         39  : 'right',
		 *         40  : 'down!',
		 *         46  : 'delete',
		 *         108 : 'numpadEnter'
		 *     }
		 *
		 * Please note the `!` at the end of some keys. This tells the core that by default
		 * this keypress will be trapped and not passed on to the text input.
		 *
		 * @name keys
		 * @default { ... }
		 * @author agorbatchev
		 * @date 2011/08/19
		 * @id TextExt.options.keys
		 */
r="keys",/**
		 * The core triggers or reacts to the following events.
		 *
		 * @author agorbatchev
		 * @date 2011/08/17
		 * @id TextExt.events
		 */
/**
		 * Core triggers `preInvalidate` event before the dimensions of padding on the text input
		 * are set.
		 *
		 * @name preInvalidate
		 * @author agorbatchev
		 * @date 2011/08/19
		 * @id TextExt.events.preInvalidate
		 */
s="preInvalidate",/**
		 * Core triggers `postInvalidate` event after the dimensions of padding on the text input
		 * are set.
		 *
		 * @name postInvalidate
		 * @author agorbatchev
		 * @date 2011/08/19
		 * @id TextExt.events.postInvalidate
		 */
t="postInvalidate",/**
		 * Core triggers `getFormData` on every key press to collect data that will be populated
		 * into the hidden input that will be submitted with the HTML form and data that will
		 * be displayed in the input field that user is currently interacting with.
		 *
		 * All plugins that wish to affect how the data is presented or sent must react to 
		 * `getFormData` and populate the data in the following format:
		 *
		 *     {
		 *         input : {String},
		 *         form  : {Object}
		 *     }
		 *
		 * The data key must be a numeric weight which will be used to determine which data
		 * ends up being used. Data with the highest numerical weight gets the priority. This
		 * allows plugins to set the final data regardless of their initialization order, which
		 * otherwise would be impossible.
		 *
		 * For example, the Tags and Autocomplete plugins have to work side by side and Tags
		 * plugin must get priority on setting the data. Therefore the Tags plugin sets data
		 * with the weight 200 where as the Autocomplete plugin sets data with the weight 100.
		 *
		 * Here's an example of a typical `getFormData` handler:
		 * 
		 *     TextExtPlugin.prototype.onGetFormData = function(e, data, keyCode)
		 *     {
		 *         data[100] = self.formDataObject('input value', 'form value');
		 *     };
		 *
		 * Core also reacts to the `getFormData` and updates hidden input with data which will be
		 * submitted with the HTML form.
		 *
		 * @name getFormData
		 * @author agorbatchev
		 * @date 2011/08/19
		 * @id TextExt.events.getFormData
		 */
u="getFormData",/**
		 * Core triggers and reacts to the `setFormData` event to update the actual value in the
		 * hidden input that will be submitted with the HTML form. Second argument can be value
		 * of any type and by default it will be JSON serialized with `TextExt.serializeData()`
		 * function.
		 *
		 * @name setFormData
		 * @author agorbatchev
		 * @date 2011/08/22
		 * @id TextExt.events.setFormData
		 */
v="setFormData",/**
		 * Core triggers and reacts to the `setInputData` event to update the actual value in the
		 * text input that user is interacting with. Second argument must be of a `String` type
		 * the value of which will be set into the text input.
		 *
		 * @name setInputData
		 * @author agorbatchev
		 * @date 2011/08/22
		 * @id TextExt.events.setInputData
		 */
w="setInputData",/**
		 * Core triggers `postInit` event to let plugins run code after all plugins have been 
		 * created and initialized. This is a good place to set some kind of global values before 
		 * somebody gets to use them. This is not the right place to expect all plugins to finish
		 * their initialization.
		 *
		 * @name postInit
		 * @author agorbatchev
		 * @date 2011/08/19
		 * @id TextExt.events.postInit
		 */
x="postInit",/**
		 * Core triggers `ready` event after all global configuration and prepearation has been
		 * done and the TextExt component is ready for use. Event handlers should expect all 
		 * values to be set and the plugins to be in the final state.
		 *
		 * @name ready
		 * @author agorbatchev
		 * @date 2011/08/19
		 * @id TextExt.events.ready
		 */
y="ready",/**
		 * Core triggers `anyKeyUp` event for every key up event triggered within the component.
		 *
		 * @name anyKeyUp
		 * @author agorbatchev
		 * @date 2011/08/19
		 * @id TextExt.events.anyKeyUp
		 */
/**
		 * Core triggers `anyKeyDown` event for every key down event triggered within the component.
		 *
		 * @name anyKeyDown
		 * @author agorbatchev
		 * @date 2011/08/19
		 * @id TextExt.events.anyKeyDown
		 */
/**
		 * Core triggers `[name]KeyUp` event for every key specifid in the `keys` option that is 
		 * triggered within the component.
		 *
		 * @name [name]KeyUp
		 * @author agorbatchev
		 * @date 2011/08/19
		 * @id TextExt.events.[name]KeyUp
		 */
/**
		 * Core triggers `[name]KeyDown` event for every key specified in the `keys` option that is 
		 * triggered within the component.
		 *
		 * @name [name]KeyDown
		 * @author agorbatchev
		 * @date 2011/08/19
		 * @id TextExt.events.[name]KeyDown
		 */
/**
		 * Core triggers `[name]KeyPress` event for every key specified in the `keys` option that is 
		 * triggered within the component.
		 *
		 * @name [name]KeyPress
		 * @author agorbatchev
		 * @date 2011/08/19
		 * @id TextExt.events.[name]KeyPress
		 */
z={itemManager:d,plugins:[],ext:{},html:{wrap:'<div class="text-core"><div class="text-wrap"/></div>',hidden:'<input type="hidden" />'},keys:{8:"backspace",9:"tab",13:"enter!",27:"escape!",37:"left",38:"up!",39:"right",40:"down!",46:"delete",108:"numpadEnter"}};
// Freak out if there's no JSON.stringify function found
if(!j)throw new Error("JSON.stringify() not found");i=d.prototype,i.init=function(a){},i.filter=function(a,b){var c,d,e=[];for(c=0;c<a.length;c++)d=a[c],this.itemContains(d,b)&&e.push(d);return e},i.itemContains=function(a,b){return 0==this.itemToString(a).toLowerCase().indexOf(b.toLowerCase())},i.stringToItem=function(a){return a},i.itemToString=function(a){return a},i.compareItems=function(a,b){return a==b},i=c.prototype,i.init=function(b,c){var d,e,f,g=this;g._defaults=a.extend({},z),g._opts=c||{},g._plugins={},g._itemManager=e=new(g.opts(m)),b=a(b),f=a(g.opts(p)),d=a(g.opts(q)),b.wrap(f).keydown(function(a){return g.onKeyDown(a)}).keyup(function(a){return g.onKeyUp(a)}).data("textext",g),a(g).data({hiddenInput:d,wrapElement:b.parents(".text-wrap").first(),input:b}),d.attr("name",b.attr("name")),b.attr("name",null),d.insertAfter(b),a.extend(!0,e,g.opts(o+".item.manager")),a.extend(!0,g,g.opts(o+".*"),g.opts(o+".core")),g.originalWidth=b.outerWidth(),g.invalidateBounds(),e.init(g),g.initPatches(),g.initPlugins(g.opts(n),a.fn.textext.plugins),g.on({setFormData:g.onSetFormData,getFormData:g.onGetFormData,setInputData:g.onSetInputData,anyKeyUp:g.onAnyKeyUp}),g.trigger(x),g.trigger(y),g.getFormData(0)},i.initPatches=function(){var b,c=[],d=a.fn.textext.patches;for(b in d)c.push(b);this.initPlugins(c,d)},i.initPlugins=function(b,c){var d,e,f,g=this,h=[];for("string"==typeof b&&(b=b.split(/\s*,\s*|\s+/g)),f=0;f<b.length;f++)d=b[f],e=c[d],e&&(g._plugins[d]=e=new e,g[d]=function(a){return function(){return a}}(e),h.push(e),a.extend(!0,e,g.opts(o+".*"),g.opts(o+"."+d)));for(h.sort(function(a,b){return a=a.initPriority(),b=b.initPriority(),a===b?0:b>a?1:-1}),f=0;f<h.length;f++)h[f].init(g)},i.hasPlugin=function(a){return!!this._plugins[a]},i.on=g,i.bind=function(a,b){this.input().bind(a,b)},i.trigger=function(){var a=arguments;this.input().trigger(a[0],k.call(a,1))},i.itemManager=function(){return this._itemManager},i.input=function(){return a(this).data("input")},i.opts=function(a){var b=f(this._opts,a);return"undefined"==typeof b?f(this._defaults,a):b},i.wrapElement=function(){return a(this).data("wrapElement")},i.invalidateBounds=function(){var a,b=this,c=b.input(),d=b.wrapElement(),e=d.parent(),f=b.originalWidth+"px";b.trigger(s),a=c.outerHeight()+"px",c.css({width:f}),d.css({width:f,height:a}),e.css({height:a}),b.trigger(t)},i.focusInput=function(){this.input()[0].focus()},i.serializeData=j,i.hiddenInput=function(b){return a(this).data("hiddenInput")},i.getWeightedEventResponse=function(a,b){var c=this,d={},e=0;c.trigger(a,d,b);for(var f in d)e=Math.max(e,f);return d[e]},i.getFormData=function(a){var b=this,c=b.getWeightedEventResponse(u,a||0);b.trigger(v,c.form),b.trigger(w,c.input)},i.onAnyKeyUp=function(a,b){this.getFormData(b)},i.onSetInputData=function(a,b){this.input().val(b)},i.onSetFormData=function(a,b){var c=this;c.hiddenInput().val(c.serializeData(b))},i.onGetFormData=function(a,b){var c=this.input().val();b[0]=h(c,c)},a(["Down","Up"]).each(function(){var a=this.toString();i["onKey"+a]=function(b){var c=this,d=c.opts(r)[b.keyCode],e=!0;return d&&(e="!"!=d.substr(-1),d=d.replace("!",""),c.trigger(d+"Key"+a),"Up"==a&&c._lastKeyDown==b.keyCode&&(c._lastKeyDown=null,c.trigger(d+"KeyPress")),"Down"==a&&(c._lastKeyDown=b.keyCode)),c.trigger("anyKey"+a,b.keyCode),e}}),i=e.prototype,i.on=g,i.formDataObject=h,i.init=function(a){throw new Error("Not implemented")},i.baseInit=function(b,c){var d=this;b._defaults=a.extend(!0,b._defaults,c),d._core=b,d._timers={}},i.startTimer=function(a,b,c){var d=this;d.stopTimer(a),d._timers[a]=setTimeout(function(){delete d._timers[a],c.apply(d)},1e3*b)},i.stopTimer=function(a){clearTimeout(this._timers[a])},i.core=function(){return this._core},i.opts=function(a){return this.core().opts(a)},i.itemManager=function(){return this.core().itemManager()},i.input=function(){return this.core().input()},i.val=function(a){var b=this.input();return typeof a===l?b.val():void b.val(a)},i.trigger=function(){var a=this.core();a.trigger.apply(a,arguments)},i.bind=function(a,b){this.core().bind(a,b)},i.initPriority=function(){return 0};
//--------------------------------------------------------------------------------
// jQuery Integration
/**
	 * TextExt integrates as a jQuery plugin available through the `$(selector).textext(opts)` call. If
	 * `opts` argument is passed, then a new instance of `TextExt` will be created for all the inputs
	 * that match the `selector`. If `opts` wasn't passed and TextExt was already intantiated for 
	 * inputs that match the `selector`, array of `TextExt` instances will be returned instead.
	 *
	 *     // will create a new instance of `TextExt` for all elements that match `.sample`
	 *     $('.sample').textext({ ... });
	 *
	 *     // will return array of all `TextExt` instances
	 *     var list = $('.sample').textext();
	 *
	 * The following properties are also exposed through the jQuery `$.fn.textext`:
	 *
	 * * `TextExt` -- `TextExt` class.
	 * * `TextExtPlugin` -- `TextExtPlugin` class.
	 * * `ItemManager` -- `ItemManager` class.
	 * * `plugins` -- Key/value table of all registered plugins.
	 * * `addPlugin(name, constructor)` -- All plugins should register themselves using this function.
	 *
	 * @author agorbatchev
	 * @date 2011/08/19
	 * @id TextExt.jquery
	 */
var A=!1,B=a.fn.textext=function(b){var d;return A||null==(d=a.fn.textext.css)||(a("head").append("<style>"+d+"</style>"),A=!0),this.map(function(){var d=a(this);if(null==b)return d.data("textext");var e=new c;return e.init(d,b),d.data("textext",e),e.input()[0]})};/**
	 * This static function registers a new plugin which makes it available through the `plugins` option
	 * to the end user. The name specified here is the name the end user would put in the `plugins` option
	 * to add this plugin to a new instance of TextExt.
	 * 
	 * @signature $.fn.textext.addPlugin(name, constructor)
	 *
	 * @param name {String} Name of the plugin.
	 * @param constructor {Function} Plugin constructor.
	 *
	 * @author agorbatchev
	 * @date 2011/10/11
	 * @id TextExt.addPlugin
	 */
B.addPlugin=function(a,b){B.plugins[a]=b,b.prototype=new B.TextExtPlugin},/**
	 * This static function registers a new patch which is added to each instance of TextExt. If you are
	 * adding a new patch, make sure to call this method.
	 * 
	 * @signature $.fn.textext.addPatch(name, constructor)
	 *
	 * @param name {String} Name of the patch.
	 * @param constructor {Function} Patch constructor.
	 *
	 * @author agorbatchev
	 * @date 2011/10/11
	 * @id TextExt.addPatch
	 */
B.addPatch=function(a,b){B.patches[a]=b,b.prototype=new B.TextExtPlugin},B.TextExt=c,B.TextExtPlugin=e,B.ItemManager=d,B.plugins={},B.patches={}}(jQuery),function(a){function b(){}a.fn.textext.TextExtIE9Patches=b,a.fn.textext.addPatch("ie9",b);var c=b.prototype;c.init=function(a){if(-1!=navigator.userAgent.indexOf("MSIE 9")){var b=this;a.on({postInvalidate:b.onPostInvalidate})}},c.onPostInvalidate=function(){var a=this,b=a.input(),c=b.val();
// agorbatchev :: IE9 doesn't seem to update the padding if box-sizing is on until the
// text box value changes, so forcing this change seems to do the trick of updating
// IE's padding visually.
b.val(Math.random()),b.val(c)}}(jQuery),/**
 * jQuery TextExt Plugin
 * http://textextjs.com
 *
 * @version 1.3.1
 * @copyright Copyright (C) 2011 Alex Gorbatchev. All rights reserved.
 * @license MIT License
 */
function(a){/**
	 * Autocomplete plugin brings the classic autocomplete functionality to the TextExt ecosystem.
	 * The gist of functionality is when user starts typing in, for example a term or a tag, a
	 * dropdown would be presented with possible suggestions to complete the input quicker.
	 *
	 * @author agorbatchev
	 * @date 2011/08/17
	 * @id TextExtAutocomplete
	 */
function b(){}a.fn.textext.TextExtAutocomplete=b,a.fn.textext.addPlugin("autocomplete",b);var c=b.prototype,d=".",e="text-selected",f=d+e,g="text-suggestion",h=d+g,i="text-label",j=d+i,/**
		 * Autocomplete plugin options are grouped under `autocomplete` when passed to the 
		 * `$().textext()` function. For example:
		 *
		 *     $('textarea').textext({
		 *         plugins: 'autocomplete',
		 *         autocomplete: {
		 *             dropdownPosition: 'above'
		 *         }
		 *     })
		 *
		 * @author agorbatchev
		 * @date 2011/08/17
		 * @id TextExtAutocomplete.options
		 */
/**
		 * This is a toggle switch to enable or disable the Autucomplete plugin. The value is checked
		 * each time at the top level which allows you to toggle this setting on the fly.
		 *
		 * @name autocomplete.enabled
		 * @default true
		 * @author agorbatchev
		 * @date 2011/08/17
		 * @id TextExtAutocomplete.options.autocomplete.enabled
		 */
k="autocomplete.enabled",/**
		 * This option allows to specify position of the dropdown. The two possible values
		 * are `above` and `below`.
		 *
		 * @name autocomplete.dropdown.position
		 * @default "below"
		 * @author agorbatchev
		 * @date 2011/08/17
		 * @id TextExtAutocomplete.options.autocomplete.dropdown.position
		 */
l="autocomplete.dropdown.position",/**
		 * This option allows to specify maximum height of the dropdown. Value is taken directly, so
		 * if desired height is 200 pixels, value must be `200px`.
		 *
		 * @name autocomplete.dropdown.maxHeight
		 * @default "100px"
		 * @author agorbatchev
		 * @date 2011/12/29
		 * @id TextExtAutocomplete.options.autocomplete.dropdown.maxHeight
		 * @version 1.1
		 */
m="autocomplete.dropdown.maxHeight",/**
		 * This option allows to override how a suggestion item is rendered. The value should be
		 * a function, the first argument of which is suggestion to be rendered and `this` context
		 * is the current instance of `TextExtAutocomplete`. 
		 *
		 * [Click here](/manual/examples/autocomplete-with-custom-render.html) to see a demo.
		 *
		 * For example:
		 *
		 *     $('textarea').textext({
		 *         plugins: 'autocomplete',
		 *         autocomplete: {
		 *             render: function(suggestion)
		 *             {
		 *                 return '<b>' + suggestion + '</b>';
		 *             }
		 *         }
		 *     })
		 *
		 * @name autocomplete.render
		 * @default null
		 * @author agorbatchev
		 * @date 2011/12/23
		 * @id TextExtAutocomplete.options.autocomplete.render
		 * @version 1.1
		 */
n="autocomplete.render",/**
		 * HTML source that is used to generate the dropdown.
		 *
		 * @name html.dropdown
		 * @default '<div class="text-dropdown"><div class="text-list"/></div>'
		 * @author agorbatchev
		 * @date 2011/08/17
		 * @id TextExtAutocomplete.options.html.dropdown
		 */
o="html.dropdown",/**
		 * HTML source that is used to generate each suggestion.
		 *
		 * @name html.suggestion
		 * @default '<div class="text-suggestion"><span class="text-label"/></div>'
		 * @author agorbatchev
		 * @date 2011/08/17
		 * @id TextExtAutocomplete.options.html.suggestion
		 */
p="html.suggestion",/**
		 * Autocomplete plugin triggers or reacts to the following events.
		 *
		 * @author agorbatchev
		 * @date 2011/08/17
		 * @id TextExtAutocomplete.events
		 */
/**
		 * Autocomplete plugin triggers and reacts to the `hideDropdown` to hide the dropdown if it's 
		 * already visible.
		 *
		 * @name hideDropdown
		 * @author agorbatchev
		 * @date 2011/08/17
		 * @id TextExtAutocomplete.events.hideDropdown
		 */
q="hideDropdown",/**
		 * Autocomplete plugin triggers and reacts to the `showDropdown` to show the dropdown if it's 
		 * not already visible.
		 *
		 * It's possible to pass a render callback function which will be called instead of the
		 * default `TextExtAutocomplete.renderSuggestions()`. 
		 *
		 * Here's how another plugin should trigger this event with the optional render callback:
		 *
		 *     this.trigger('showDropdown', function(autocomplete)
		 *     {
		 *         autocomplete.clearItems();
		 *         var node = autocomplete.addDropdownItem('<b>Item</b>');
		 *         node.addClass('new-look');
		 *     });
		 *
		 * @name showDropdown
		 * @author agorbatchev
		 * @date 2011/08/17
		 * @id TextExtAutocomplete.events.showDropdown
		 */
r="showDropdown",/**
		 * Autocomplete plugin reacts to the `setSuggestions` event triggered by other plugins which
		 * wish to populate the suggestion items. Suggestions should be passed as event argument in the 
		 * following format: `{ data : [ ... ] }`. 
		 *
		 * Here's how another plugin should trigger this event:
		 *
		 *     this.trigger('setSuggestions', { data : [ "item1", "item2" ] });
		 *
		 * @name setSuggestions
		 * @author agorbatchev
		 * @date 2011/08/17
		 * @id TextExtAutocomplete.events.setSuggestions
		 */
/**
		 * Autocomplete plugin triggers the `getSuggestions` event and expects to get results by listening for
		 * the `setSuggestions` event.
		 *
		 * @name getSuggestions
		 * @author agorbatchev
		 * @date 2011/08/17
		 * @id TextExtAutocomplete.events.getSuggestions
		 */
s="getSuggestions",t="above",u="below",v="mousedownOnAutocomplete",w={autocomplete:{enabled:!0,dropdown:{position:u,maxHeight:"100px"}},html:{dropdown:'<div class="text-dropdown"><div class="text-list"/></div>',suggestion:'<div class="text-suggestion"><span class="text-label"/></div>'}};/**
	 * Initialization method called by the core during plugin instantiation.
	 *
	 * @signature TextExtAutocomplete.init(core)
	 *
	 * @param core {TextExt} Instance of the TextExt core class.
	 *
	 * @author agorbatchev
	 * @date 2011/08/17
	 * @id TextExtAutocomplete.init
	 */
c.init=function(b){var c=this;c.baseInit(b,w);var d,e=c.input();c.opts(k)===!0&&(c.on({blur:c.onBlur,anyKeyUp:c.onAnyKeyUp,deleteKeyUp:c.onAnyKeyUp,backspaceKeyPress:c.onBackspaceKeyPress,enterKeyPress:c.onEnterKeyPress,escapeKeyPress:c.onEscapeKeyPress,setSuggestions:c.onSetSuggestions,showDropdown:c.onShowDropdown,hideDropdown:c.onHideDropdown,toggleDropdown:c.onToggleDropdown,postInvalidate:c.positionDropdown,getFormData:c.onGetFormData,
// using keyDown for up/down keys so that repeat events are
// captured and user can scroll up/down by holding the keys
downKeyDown:c.onDownKeyDown,upKeyDown:c.onUpKeyDown}),d=a(c.opts(o)),d.insertAfter(e),c.on(d,{mouseover:c.onMouseOver,mousedown:c.onMouseDown,click:c.onClick}),d.css("maxHeight",c.opts(m)).addClass("text-position-"+c.opts(l)),a(c).data("container",d),a(document.body).click(function(a){c.isDropdownVisible()&&!c.withinWrapElement(a.target)&&c.trigger(q)}),c.positionDropdown())},/**
	 * Returns top level dropdown container HTML element.
	 * 
	 * @signature TextExtAutocomplete.containerElement()
	 * 
	 * @author agorbatchev
	 * @date 2011/08/15
	 * @id TextExtAutocomplete.containerElement
	 */
c.containerElement=function(){return a(this).data("container")},
//--------------------------------------------------------------------------------
// User mouse/keyboard input
/**
	 * Reacts to the `mouseOver` event triggered by the TextExt core.
	 *
	 * @signature TextExtAutocomplete.onMouseOver(e)
	 *
	 * @param e {Object} jQuery event.
	 *
	 * @author agorbatchev
	 * @date 2011/08/17
	 * @id TextExtAutocomplete.onMouseOver
	 */
c.onMouseOver=function(b){var c=this,d=a(b.target);d.is(h)&&(c.clearSelected(),d.addClass(e))},/**
	 * Reacts to the `mouseDown` event triggered by the TextExt core.
	 *
	 * @signature TextExtAutocomplete.onMouseDown(e)
	 *
	 * @param e {Object} jQuery event.
	 *
	 * @author adamayres
	 * @date 2012/01/13
	 * @id TextExtAutocomplete.onMouseDown
	 */
c.onMouseDown=function(a){this.containerElement().data(v,!0)},/**
	 * Reacts to the `click` event triggered by the TextExt core.
	 *
	 * @signature TextExtAutocomplete.onClick(e)
	 *
	 * @param e {Object} jQuery event.
	 *
	 * @author agorbatchev
	 * @date 2011/08/17
	 * @id TextExtAutocomplete.onClick
	 */
c.onClick=function(b){var c=this,d=a(b.target);(d.is(h)||d.is(j))&&c.trigger("enterKeyPress"),c.core().hasPlugin("tags")&&c.val("")},/**
	 * Reacts to the `blur` event triggered by the TextExt core.
	 *
	 * @signature TextExtAutocomplete.onBlur(e)
	 *
	 * @param e {Object} jQuery event.
	 *
	 * @author agorbatchev
	 * @date 2011/08/17
	 * @id TextExtAutocomplete.onBlur
	 */
c.onBlur=function(a){var b=this,c=b.containerElement(),d=c.data(v)===!0;
// only trigger a close event if the blur event was 
// not triggered by a mousedown event on the autocomplete
// otherwise set focus back back on the input
b.isDropdownVisible()&&(d?b.core().focusInput():b.trigger(q)),c.removeData(v)},/**
	 * Reacts to the `backspaceKeyPress` event triggered by the TextExt core. 
	 *
	 * @signature TextExtAutocomplete.onBackspaceKeyPress(e)
	 *
	 * @param e {Object} jQuery event.
	 *
	 * @author agorbatchev
	 * @date 2011/08/17
	 * @id TextExtAutocomplete.onBackspaceKeyPress
	 */
c.onBackspaceKeyPress=function(a){var b=this,c=b.val().length>0;(c||b.isDropdownVisible())&&b.getSuggestions()},/**
	 * Reacts to the `anyKeyUp` event triggered by the TextExt core.
	 *
	 * @signature TextExtAutocomplete.onAnyKeyUp(e)
	 *
	 * @param e {Object} jQuery event.
	 *
	 * @author agorbatchev
	 * @date 2011/08/17
	 * @id TextExtAutocomplete.onAnyKeyUp
	 */
c.onAnyKeyUp=function(a,b){var c=this,d=null!=c.opts("keys."+b);c.val().length>0&&!d&&c.getSuggestions()},/**
	 * Reacts to the `downKeyDown` event triggered by the TextExt core.
	 *
	 * @signature TextExtAutocomplete.onDownKeyDown(e)
	 *
	 * @param e {Object} jQuery event.
	 *
	 * @author agorbatchev
	 * @date 2011/08/17
	 * @id TextExtAutocomplete.onDownKeyDown
	 */
c.onDownKeyDown=function(a){var b=this;b.isDropdownVisible()?b.toggleNextSuggestion():b.getSuggestions()},/**
	 * Reacts to the `upKeyDown` event triggered by the TextExt core.
	 *
	 * @signature TextExtAutocomplete.onUpKeyDown(e)
	 *
	 * @param e {Object} jQuery event.
	 *
	 * @author agorbatchev
	 * @date 2011/08/17
	 * @id TextExtAutocomplete.onUpKeyDown
	 */
c.onUpKeyDown=function(a){this.togglePreviousSuggestion()},/**
	 * Reacts to the `enterKeyPress` event triggered by the TextExt core.
	 *
	 * @signature TextExtAutocomplete.onEnterKeyPress(e)
	 *
	 * @param e {Object} jQuery event.
	 *
	 * @author agorbatchev
	 * @date 2011/08/17
	 * @id TextExtAutocomplete.onEnterKeyPress
	 */
c.onEnterKeyPress=function(a){var b=this;b.isDropdownVisible()&&b.selectFromDropdown()},/**
	 * Reacts to the `escapeKeyPress` event triggered by the TextExt core. Hides the dropdown
	 * if it's currently visible.
	 *
	 * @signature TextExtAutocomplete.onEscapeKeyPress(e)
	 *
	 * @param e {Object} jQuery event.
	 *
	 * @author agorbatchev
	 * @date 2011/08/17
	 * @id TextExtAutocomplete.onEscapeKeyPress
	 */
c.onEscapeKeyPress=function(a){var b=this;b.isDropdownVisible()&&b.trigger(q)},
//--------------------------------------------------------------------------------
// Core functionality
/**
	 * Positions dropdown either below or above the input based on the `autocomplete.dropdown.position`
	 * option specified, which could be either `above` or `below`.
	 *
	 * @signature TextExtAutocomplete.positionDropdown()
	 *
	 * @author agorbatchev
	 * @date 2011/08/15
	 * @id TextExtAutocomplete.positionDropdown
	 */
c.positionDropdown=function(){var a=this,b=a.containerElement(),c=a.opts(l),d=a.core().wrapElement().outerHeight(),e={};e[c===t?"bottom":"top"]=d+"px",b.css(e)},/**
	 * Returns list of all the suggestion HTML elements in the dropdown.
	 *
	 * @signature TextExtAutocomplete.suggestionElements()
	 *
	 * @author agorbatchev
	 * @date 2011/08/17
	 * @id TextExtAutocomplete.suggestionElements
	 */
c.suggestionElements=function(){return this.containerElement().find(h)},/**
	 * Highlights specified suggestion as selected in the dropdown.
	 *
	 * @signature TextExtAutocomplete.setSelectedSuggestion(suggestion)
	 *
	 * @param suggestion {Object} Suggestion object. With the default `ItemManager` this
	 * is expected to be a string, anything else with custom implementations.
	 *
	 * @author agorbatchev
	 * @date 2011/08/17
	 * @id TextExtAutocomplete.setSelectedSuggestion
	 */
c.setSelectedSuggestion=function(b){if(b){var c,d,f=this,h=f.suggestionElements(),i=h.first();for(f.clearSelected(),d=0;d<h.length;d++)if(c=a(h[d]),f.itemManager().compareItems(c.data(g),b)){i=c.addClass(e);break}i.addClass(e),f.scrollSuggestionIntoView(i)}},/**
	 * Returns the first suggestion HTML element from the dropdown that is highlighted as selected.
	 *
	 * @signature TextExtAutocomplete.selectedSuggestionElement()
	 *
	 * @author agorbatchev
	 * @date 2011/08/17
	 * @id TextExtAutocomplete.selectedSuggestionElement
	 */
c.selectedSuggestionElement=function(){return this.suggestionElements().filter(f).first()},/**
	 * Returns `true` if dropdown is currently visible, `false` otherwise.
	 *
	 * @signature TextExtAutocomplete.isDropdownVisible()
	 *
	 * @author agorbatchev
	 * @date 2011/08/17
	 * @id TextExtAutocomplete.isDropdownVisible
	 */
c.isDropdownVisible=function(){return this.containerElement().is(":visible")===!0},/**
	 * Reacts to the `getFormData` event triggered by the core. Returns data with the
	 * weight of 100 to be *less than the Tags plugin* data weight. The weights system is
	 * covered in greater detail in the [`getFormData`][1] event documentation.
	 *
	 * [1]: /manual/textext.html#getformdata
	 *
	 * @signature TextExtAutocomplete.onGetFormData(e, data, keyCode)
	 *
	 * @param e {Object} jQuery event.
	 * @param data {Object} Data object to be populated.
	 * @param keyCode {Number} Key code that triggered the original update request.
	 *
	 * @author agorbatchev
	 * @date 2011/08/22
	 * @id TextExtAutocomplete.onGetFormData
	 */
c.onGetFormData=function(a,b,c){var d=this,e=d.val(),f=e,g=e;b[100]=d.formDataObject(f,g)},/**
	 * Returns initialization priority of the Autocomplete plugin which is expected to be
	 * *greater than the Tags plugin* because of the dependencies. The value is 200.
	 *
	 * @signature TextExtAutocomplete.initPriority()
	 *
	 * @author agorbatchev
	 * @date 2011/08/22
	 * @id TextExtAutocomplete.initPriority
	 */
c.initPriority=function(){return 200},/**
	 * Reacts to the `hideDropdown` event and hides the dropdown if it's already visible.
	 *
	 * @signature TextExtAutocomplete.onHideDropdown(e)
	 *
	 * @param e {Object} jQuery event.
	 *
	 * @author agorbatchev
	 * @date 2011/08/17
	 * @id TextExtAutocomplete.onHideDropdown
	 */
c.onHideDropdown=function(a){this.hideDropdown()},/**
	 * Reacts to the 'toggleDropdown` event and shows or hides the dropdown depending if
	 * it's currently hidden or visible.
	 *
	 * @signature TextExtAutocomplete.onToggleDropdown(e)
	 *
	 * @param e {Object} jQuery event.
	 *
	 * @author agorbatchev
	 * @date 2011/12/27
	 * @id TextExtAutocomplete.onToggleDropdown
	 * @version 1.1.0
	 */
c.onToggleDropdown=function(a){var b=this;b.trigger(b.containerElement().is(":visible")?q:r)},/**
	 * Reacts to the `showDropdown` event and shows the dropdown if it's not already visible.
	 * It's possible to pass a render callback function which will be called instead of the
	 * default `TextExtAutocomplete.renderSuggestions()`.
	 *
	 * If no suggestion were previously loaded, it will fire `getSuggestions` event and exit.
	 *
	 * Here's how another plugin should trigger this event with the optional render callback:
	 *
	 *     this.trigger('showDropdown', function(autocomplete)
	 *     {
	 *         autocomplete.clearItems();
	 *         var node = autocomplete.addDropdownItem('<b>Item</b>');
	 *         node.addClass('new-look');
	 *     });
	 *
	 * @signature TextExtAutocomplete.onShowDropdown(e, renderCallback)
	 *
	 * @param e {Object} jQuery event.
	 * @param renderCallback {Function} Optional callback function which would be used to 
	 * render dropdown items. As a first argument, reference to the current instance of 
	 * Autocomplete plugin will be supplied. It's assumed, that if this callback is provided
	 * rendering will be handled completely manually.
	 *
	 * @author agorbatchev
	 * @date 2011/08/17
	 * @id TextExtAutocomplete.onShowDropdown
	 */
c.onShowDropdown=function(b,c){var d=this,e=d.selectedSuggestionElement().data(g),f=d._suggestions;return f?(a.isFunction(c)?c(d):(d.renderSuggestions(d._suggestions),d.toggleNextSuggestion()),d.showDropdown(d.containerElement()),void d.setSelectedSuggestion(e)):d.trigger(s)},/**
	 * Reacts to the `setSuggestions` event. Expects to recieve the payload as the second argument
	 * in the following structure:
	 *
	 *     {
	 *         result : [ "item1", "item2" ],
	 *         showHideDropdown : false
	 *     }
	 *
	 * Notice the optional `showHideDropdown` option. By default, ie without the `showHideDropdown` 
	 * value the method will trigger either `showDropdown` or `hideDropdown` depending if there are
	 * suggestions. If set to `false`, no event is triggered.
	 *
	 * @signature TextExtAutocomplete.onSetSuggestions(e, data)
	 *
	 * @param data {Object} Data payload.
	 *
	 * @author agorbatchev
	 * @date 2011/08/17
	 * @id TextExtAutocomplete.onSetSuggestions
	 */
c.onSetSuggestions=function(a,b){var c=this,d=c._suggestions=b.result;b.showHideDropdown!==!1&&c.trigger(null===d||0===d.length?q:r)},/**
	 * Prepears for and triggers the `getSuggestions` event with the `{ query : {String} }` as second
	 * argument.
	 *
	 * @signature TextExtAutocomplete.getSuggestions()
	 *
	 * @author agorbatchev
	 * @date 2011/08/17
	 * @id TextExtAutocomplete.getSuggestions
	 */
c.getSuggestions=function(){var a=this,b=a.val();a._previousInputValue!=b&&(
// if user clears input, then we want to select first suggestion
// instead of the last one
""==b&&(current=null),a._previousInputValue=b,a.trigger(s,{query:b}))},/**
	 * Removes all HTML suggestion items from the dropdown.
	 *
	 * @signature TextExtAutocomplete.clearItems()
	 *
	 * @author agorbatchev
	 * @date 2011/08/17
	 * @id TextExtAutocomplete.clearItems
	 */
c.clearItems=function(){this.containerElement().find(".text-list").children().remove()},/**
	 * Clears all and renders passed suggestions.
	 *
	 * @signature TextExtAutocomplete.renderSuggestions(suggestions)
	 *
	 * @param suggestions {Array} List of suggestions to render.
	 *
	 * @author agorbatchev
	 * @date 2011/08/17
	 * @id TextExtAutocomplete.renderSuggestions
	 */
c.renderSuggestions=function(b){var c=this;c.clearItems(),a.each(b||[],function(a,b){c.addSuggestion(b)})},/**
	 * Shows the dropdown.
	 *
	 * @signature TextExtAutocomplete.showDropdown()
	 *
	 * @author agorbatchev
	 * @date 2011/08/17
	 * @id TextExtAutocomplete.showDropdown
	 */
c.showDropdown=function(){this.containerElement().show()},/**
	 * Hides the dropdown.
	 *
	 * @signature TextExtAutocomplete.hideDropdown()
	 *
	 * @author agorbatchev
	 * @date 2011/08/17
	 * @id TextExtAutocomplete.hideDropdown
	 */
c.hideDropdown=function(){var a=this,b=a.containerElement();a._previousInputValue=null,b.hide()},/**
	 * Adds single suggestion to the bottom of the dropdown. Uses `ItemManager.itemToString()` to
	 * serialize provided suggestion to string.
	 *
	 * @signature TextExtAutocomplete.addSuggestion(suggestion)
	 *
	 * @param suggestion {Object} Suggestion item. By default expected to be a string.
	 *
	 * @author agorbatchev
	 * @date 2011/08/17
	 * @id TextExtAutocomplete.addSuggestion
	 */
c.addSuggestion=function(a){var b=this,c=b.opts(n),d=b.addDropdownItem(c?c.call(b,a):b.itemManager().itemToString(a));d.data(g,a)},/**
	 * Adds and returns HTML node to the bottom of the dropdown.
	 *
	 * @signature TextExtAutocomplete.addDropdownItem(html)
	 *
	 * @param html {String} HTML to be inserted into the item.
	 *
	 * @author agorbatchev
	 * @date 2011/08/17
	 * @id TextExtAutocomplete.addDropdownItem
	 */
c.addDropdownItem=function(b){var c=this,d=c.containerElement().find(".text-list"),e=a(c.opts(p));return e.find(".text-label").html(b),d.append(e),e},/**
	 * Removes selection highlight from all suggestion elements.
	 *
	 * @signature TextExtAutocomplete.clearSelected()
	 *
	 * @author agorbatchev
	 * @date 2011/08/02
	 * @id TextExtAutocomplete.clearSelected
	 */
c.clearSelected=function(){this.suggestionElements().removeClass(e)},/**
	 * Selects next suggestion relative to the current one. If there's no
	 * currently selected suggestion, it will select the first one. Selected
	 * suggestion will always be scrolled into view.
	 *
	 * @signature TextExtAutocomplete.toggleNextSuggestion()
	 *
	 * @author agorbatchev
	 * @date 2011/08/02
	 * @id TextExtAutocomplete.toggleNextSuggestion
	 */
c.toggleNextSuggestion=function(){var a,b=this,c=b.selectedSuggestionElement();c.length>0?(a=c.next(),a.length>0&&c.removeClass(e)):a=b.suggestionElements().first(),a.addClass(e),b.scrollSuggestionIntoView(a)},/**
	 * Selects previous suggestion relative to the current one. Selected
	 * suggestion will always be scrolled into view.
	 *
	 * @signature TextExtAutocomplete.togglePreviousSuggestion()
	 *
	 * @author agorbatchev
	 * @date 2011/08/02
	 * @id TextExtAutocomplete.togglePreviousSuggestion
	 */
c.togglePreviousSuggestion=function(){var a=this,b=a.selectedSuggestionElement(),c=b.prev();0!=c.length&&(a.clearSelected(),c.addClass(e),a.scrollSuggestionIntoView(c))},/**
	 * Scrolls specified HTML suggestion element into the view.
	 *
	 * @signature TextExtAutocomplete.scrollSuggestionIntoView(item)
	 *
	 * @param item {HTMLElement} jQuery HTML suggestion element which needs to
	 * scrolled into view.
	 *
	 * @author agorbatchev
	 * @date 2011/08/17
	 * @id TextExtAutocomplete.scrollSuggestionIntoView
	 */
c.scrollSuggestionIntoView=function(a){var b=a.outerHeight(),c=this.containerElement(),d=c.innerHeight(),e=c.scrollTop(),f=(a.position()||{}).top,g=null,h=parseInt(c.css("paddingTop"));null!=f&&(
// if scrolling down and item is below the bottom fold
f+b>d&&(g=f+e+b-d+h),
// if scrolling up and item is above the top fold
0>f&&(g=f+e-h),null!=g&&c.scrollTop(g))},/**
	 * Uses the value from the text input to finish autocomplete action. Currently selected
	 * suggestion from the dropdown will be used to complete the action. Triggers `hideDropdown`
	 * event.
	 *
	 * @signature TextExtAutocomplete.selectFromDropdown()
	 *
	 * @author agorbatchev
	 * @date 2011/08/17
	 * @id TextExtAutocomplete.selectFromDropdown
	 */
c.selectFromDropdown=function(){var a=this,b=a.selectedSuggestionElement().data(g);b&&(a.val(a.itemManager().itemToString(b)),a.core().getFormData()),a.trigger(q)},/**
	 * Determines if the specified HTML element is within the TextExt core wrap HTML element.
	 *
	 * @signature TextExtAutocomplete.withinWrapElement(element)
	 *
	 * @param element {HTMLElement} element to check if contained by wrap element
	 *
	 * @author adamayres
	 * @version 1.3.0
	 * @date 2012/01/15
	 * @id TextExtAutocomplete.withinWrapElement
	 */
c.withinWrapElement=function(a){return this.core().wrapElement().find(a).size()>0}}(jQuery),/**
 * jQuery TextExt Plugin
 * http://textextjs.com
 *
 * @version 1.3.1
 * @copyright Copyright (C) 2011 Alex Gorbatchev. All rights reserved.
 * @license MIT License
 */
function(a){/**
	 * Tags plugin brings in the traditional tag functionality where user can assemble and
	 * edit list of tags. Tags plugin works especially well together with Autocomplete, Filter,
	 * Suggestions and Ajax plugins to provide full spectrum of features. It can also work on
	 * its own and just do one thing -- tags.
	 *
	 * @author agorbatchev
	 * @date 2011/08/19
	 * @id TextExtTags
	 */
function b(){}a.fn.textext.TextExtTags=b,a.fn.textext.addPlugin("tags",b);var c=b.prototype,d=".",e="text-tags-on-top",f=d+e,g="text-tag",h=d+g,i="text-tags",j=d+i,k="text-label",l=d+k,m="text-remove",n=d+m,/**
		 * Tags plugin options are grouped under `tags` when passed to the
		 * `$().textext()` function. For example:
		 *
		 *     $('textarea').textext({
		 *         plugins: 'tags',
		 *         tags: {
		 *             items: [ "tag1", "tag2" ]
		 *         }
		 *     })
		 *
		 * @author agorbatchev
		 * @date 2011/08/19
		 * @id TextExtTags.options
		 */
/**
		 * This is a toggle switch to enable or disable the Tags plugin. The value is checked
		 * each time at the top level which allows you to toggle this setting on the fly.
		 *
		 * @name tags.enabled
		 * @default true
		 * @author agorbatchev
		 * @date 2011/08/19
		 * @id TextExtTags.options.tags.enabled
		 */
o="tags.enabled",/**
		 * Allows to specify tags which will be added to the input by default upon initialization.
		 * Each item in the array must be of the type that current `ItemManager` can understand.
		 * Default type is `String`.
		 *
		 * @name tags.items
		 * @default null
		 * @author agorbatchev
		 * @date 2011/08/19
		 * @id TextExtTags.options.tags.items
		 */
p="tags.items",/**
		 * HTML source that is used to generate a single tag.
		 *
		 * @name html.tag
		 * @default '<div class="text-tags"/>'
		 * @author agorbatchev
		 * @date 2011/08/19
		 * @id TextExtTags.options.html.tag
		 */
q="html.tag",/**
		 * HTML source that is used to generate container for the tags.
		 *
		 * @name html.tags
		 * @default '<div class="text-tag"><div class="text-button"><span class="text-label"/><a class="text-remove"/></div></div>'
		 * @author agorbatchev
		 * @date 2011/08/19
		 * @id TextExtTags.options.html.tags
		 */
r="html.tags",/**
		 * Tags plugin dispatches or reacts to the following events.
		 *
		 * @author agorbatchev
		 * @date 2011/08/17
		 * @id TextExtTags.events
		 */
/**
		 * Tags plugin triggers the `isTagAllowed` event before adding each tag to the tag list. Other plugins have
		 * an opportunity to interrupt this by setting `result` of the second argument to `false`. For example:
		 *
		 *     $('textarea').textext({...}).bind('isTagAllowed', function(e, data)
		 *     {
		 *         if(data.tag === 'foo')
		 *             data.result = false;
		 *     })
		 *
		 * The second argument `data` has the following format: `{ tag : {Object}, result : {Boolean} }`. `tag`
		 * property is in the format that the current `ItemManager` can understand.
		 *
		 * @name isTagAllowed
		 * @author agorbatchev
		 * @date 2011/08/19
		 * @id TextExtTags.events.isTagAllowed
		 */
s="isTagAllowed",/**
		 * Tags plugin triggers the `tagClick` event when user clicks on one of the tags. This allows to process
		 * the click and potentially change the value of the tag (for example in case of user feedback).
		 *
		 *     $('textarea').textext({...}).bind('tagClick', function(e, tag, value, callback)
		 *     {
		 *         var newValue = window.prompt('New value', value);

		 *         if(newValue)
		 *             callback(newValue, true);
		 *     })
		 *
		 *  Callback argument has the following signature:
		 *
		 *     function(newValue, refocus)
		 *     {
		 *         ...
		 *     }
		 *
		 * Please check out [example](/manual/examples/tags-changing.html).
		 *
		 * @name tagClick
		 * @version 1.3.0
		 * @author s.stok
		 * @date 2011/01/23
		 * @id TextExtTags.events.tagClick
		 */
t="tagClick",u={tags:{enabled:!0,items:null},html:{tags:'<div class="text-tags"/>',tag:'<div class="text-tag"><div class="text-button"><span class="text-label"/><a class="text-remove"/></div></div>'}};/**
	 * Initialization method called by the core during plugin instantiation.
	 *
	 * @signature TextExtTags.init(core)
	 *
	 * @param core {TextExt} Instance of the TextExt core class.
	 *
	 * @author agorbatchev
	 * @date 2011/08/19
	 * @id TextExtTags.init
	 */
c.init=function(b){this.baseInit(b,u);var c,d=this,e=d.input();d.opts(o)&&(c=a(d.opts(r)),e.after(c),a(d).data("container",c),d.on({enterKeyPress:d.onEnterKeyPress,backspaceKeyDown:d.onBackspaceKeyDown,preInvalidate:d.onPreInvalidate,postInit:d.onPostInit,getFormData:d.onGetFormData}),d.on(c,{click:d.onClick,mousemove:d.onContainerMouseMove}),d.on(e,{mousemove:d.onInputMouseMove})),d._originalPadding={left:parseInt(e.css("paddingLeft")||0),top:parseInt(e.css("paddingTop")||0)},d._paddingBox={left:0,top:0},d.updateFormCache()},/**
	 * Returns HTML element in which all tag HTML elements are residing.
	 *
	 * @signature TextExtTags.containerElement()
	 *
	 * @author agorbatchev
	 * @date 2011/08/15
	 * @id TextExtTags.containerElement
	 */
c.containerElement=function(){return a(this).data("container")},
//--------------------------------------------------------------------------------
// Event handlers
/**
	 * Reacts to the `postInit` event triggered by the core and sets default tags
	 * if any were specified.
	 *
	 * @signature TextExtTags.onPostInit(e)
	 *
	 * @param e {Object} jQuery event.
	 *
	 * @author agorbatchev
	 * @date 2011/08/09
	 * @id TextExtTags.onPostInit
	 */
c.onPostInit=function(a){var b=this;b.addTags(b.opts(p))},/**
	 * Reacts to the [`getFormData`][1] event triggered by the core. Returns data with the
	 * weight of 200 to be *greater than the Autocomplete plugin* data weight. The weights
	 * system is covered in greater detail in the [`getFormData`][1] event documentation.
	 *
	 * [1]: /manual/textext.html#getformdata
	 *
	 * @signature TextExtTags.onGetFormData(e, data, keyCode)
	 *
	 * @param e {Object} jQuery event.
	 * @param data {Object} Data object to be populated.
	 * @param keyCode {Number} Key code that triggered the original update request.
	 *
	 * @author agorbatchev
	 * @date 2011/08/22
	 * @id TextExtTags.onGetFormData
	 */
c.onGetFormData=function(a,b,c){var d=this,e=13===c?"":d.val(),f=d._formData;b[200]=d.formDataObject(e,f)},/**
	 * Returns initialization priority of the Tags plugin which is expected to be
	 * *less than the Autocomplete plugin* because of the dependencies. The value is
	 * 100.
	 *
	 * @signature TextExtTags.initPriority()
	 *
	 * @author agorbatchev
	 * @date 2011/08/22
	 * @id TextExtTags.initPriority
	 */
c.initPriority=function(){return 100},/**
	 * Reacts to user moving mouse over the text area when cursor is over the text
	 * and not over the tags. Whenever mouse cursor is over the area covered by
	 * tags, the tags container is flipped to be on top of the text area which
	 * makes all tags functional with the mouse.
	 *
	 * @signature TextExtTags.onInputMouseMove(e)
	 *
	 * @param e {Object} jQuery event.
	 *
	 * @author agorbatchev
	 * @date 2011/08/08
	 * @id TextExtTags.onInputMouseMove
	 */
c.onInputMouseMove=function(a){this.toggleZIndex(a)},/**
	 * Reacts to user moving mouse over the tags. Whenever the cursor moves out
	 * of the tags and back into where the text input is happening visually,
	 * the tags container is sent back under the text area which allows user
	 * to interact with the text using mouse cursor as expected.
	 *
	 * @signature TextExtTags.onContainerMouseMove(e)
	 *
	 * @param e {Object} jQuery event.
	 *
	 * @author agorbatchev
	 * @date 2011/08/08
	 * @id TextExtTags.onContainerMouseMove
	 */
c.onContainerMouseMove=function(a){this.toggleZIndex(a)},/**
	 * Reacts to the `backspaceKeyDown` event. When backspace key is pressed in an empty text field,
	 * deletes last tag from the list.
	 *
	 * @signature TextExtTags.onBackspaceKeyDown(e)
	 *
	 * @param e {Object} jQuery event.
	 *
	 * @author agorbatchev
	 * @date 2011/08/02
	 * @id TextExtTags.onBackspaceKeyDown
	 */
c.onBackspaceKeyDown=function(a){var b=this,c=b.tagElements().last();0==b.val().length&&b.removeTag(c)},/**
	 * Reacts to the `preInvalidate` event and updates the input box to look like the tags are
	 * positioned inside it.
	 *
	 * @signature TextExtTags.onPreInvalidate(e)
	 *
	 * @param e {Object} jQuery event.
	 *
	 * @author agorbatchev
	 * @date 2011/08/19
	 * @id TextExtTags.onPreInvalidate
	 */
c.onPreInvalidate=function(a){var b=this,c=b.tagElements().last(),d=c.position();c.length>0?d.left+=c.innerWidth():d=b._originalPadding,b._paddingBox=d,b.input().css({paddingLeft:d.left,paddingTop:d.top})},/**
	 * Reacts to the mouse `click` event.
	 *
	 * @signature TextExtTags.onClick(e)
	 *
	 * @param e {Object} jQuery event.
	 *
	 * @author agorbatchev
	 * @date 2011/08/19
	 * @id TextExtTags.onClick
	 */
c.onClick=function(b){function c(a,b){d.data(g,a),d.find(l).text(e.itemManager().itemToString(a)),e.updateFormCache(),f.getFormData(),f.invalidateBounds(),b&&f.focusInput()}var d,e=this,f=e.core(),i=a(b.target),k=0;i.is(j)?k=1:i.is(n)?(e.removeTag(i.parents(h+":first")),k=1):i.is(l)&&(d=i.parents(h+":first"),e.trigger(t,d,d.data(g),c)),k&&f.focusInput()},/**
	 * Reacts to the `enterKeyPress` event and adds whatever is currently in the text input
	 * as a new tag. Triggers `isTagAllowed` to check if the tag could be added first.
	 *
	 * @signature TextExtTags.onEnterKeyPress(e)
	 *
	 * @param e {Object} jQuery event.
	 *
	 * @author agorbatchev
	 * @date 2011/08/19
	 * @id TextExtTags.onEnterKeyPress
	 */
c.onEnterKeyPress=function(a){var b=this,c=b.val(),d=b.itemManager().stringToItem(c);b.isTagAllowed(d)&&(b.addTags([d]),
// refocus the textarea just in case it lost the focus
b.core().focusInput())},
//--------------------------------------------------------------------------------
// Core functionality
/**
	 * Creates a cache object with all the tags currently added which will be returned
	 * in the `onGetFormData` handler.
	 *
	 * @signature TextExtTags.updateFormCache()
	 *
	 * @author agorbatchev
	 * @date 2011/08/09
	 * @id TextExtTags.updateFormCache
	 */
c.updateFormCache=function(){var b=this,c=[];b.tagElements().each(function(){c.push(a(this).data(g))}),
// cache the results to be used in the onGetFormData
b._formData=c},/**
	 * Toggles tag container to be on top of the text area or under based on where
	 * the mouse cursor is located. When cursor is above the text input and out of
	 * any of the tags, the tags container is sent under the text area. If cursor
	 * is over any of the tags, the tag container is brought to be over the text
	 * area.
	 *
	 * @signature TextExtTags.toggleZIndex(e)
	 *
	 * @param e {Object} jQuery event.
	 *
	 * @author agorbatchev
	 * @date 2011/08/08
	 * @id TextExtTags.toggleZIndex
	 */
c.toggleZIndex=function(a){var b=this,c=b.input().offset(),d=a.clientX-c.left,g=a.clientY-c.top,h=b._paddingBox,i=b.containerElement(),j=i.is(f),k=d>h.left&&g>h.top;(!j&&!k||j&&k)&&i[(j?"remove":"add")+"Class"](e)},/**
	 * Returns all tag HTML elements.
	 *
	 * @signature TextExtTags.tagElements()
	 *
	 * @author agorbatchev
	 * @date 2011/08/19
	 * @id TextExtTags.tagElements
	 */
c.tagElements=function(){return this.containerElement().find(h)},/**
	 * Wrapper around the `isTagAllowed` event which triggers it and returns `true`
	 * if `result` property of the second argument remains `true`.
	 *
	 * @signature TextExtTags.isTagAllowed(tag)
	 *
	 * @param tag {Object} Tag object that the current `ItemManager` can understand.
	 * Default is `String`.
	 *
	 * @author agorbatchev
	 * @date 2011/08/19
	 * @id TextExtTags.isTagAllowed
	 */
c.isTagAllowed=function(a){var b={tag:a,result:!0};return this.trigger(s,b),b.result===!0},/**
	 * Adds specified tags to the tag list. Triggers `isTagAllowed` event for each tag
	 * to insure that it could be added. Calls `TextExt.getFormData()` to refresh the data.
	 *
	 * @signature TextExtTags.addTags(tags)
	 *
	 * @param tags {Array} List of tags that current `ItemManager` can understand. Default
	 * is `String`.
	 *
	 * @author agorbatchev
	 * @date 2011/08/19
	 * @id TextExtTags.addTags
	 */
c.addTags=function(a){if(a&&0!=a.length){var b,c,d=this,e=d.core(),f=d.containerElement();for(b=0;b<a.length;b++)c=a[b],c&&d.isTagAllowed(c)&&f.append(d.renderTag(c));d.updateFormCache(),e.getFormData(),e.invalidateBounds()}},/**
	 * Returns HTML element for the specified tag.
	 *
	 * @signature TextExtTags.getTagElement(tag)
	 *
	 * @param tag {Object} Tag object in the format that current `ItemManager` can understand.
	 * Default is `String`.

	 * @author agorbatchev
	 * @date 2011/08/19
	 * @id TextExtTags.getTagElement
	 */
c.getTagElement=function(b){var c,d,e=this,f=e.tagElements();for(c=0;c<f.length;c++)if(d=a(f[c]),e.itemManager().compareItems(d.data(g),b))return d;return null},/**
	 * Removes specified tag from the list. Calls `TextExt.getFormData()` to refresh the data.
	 *
	 * @signature TextExtTags.removeTag(tag)
	 *
	 * @param tag {Object} Tag object in the format that current `ItemManager` can understand.
	 * Default is `String`.
	 *
	 * @author agorbatchev
	 * @date 2011/08/19
	 * @id TextExtTags.removeTag
	 */
c.removeTag=function(b){var c,d=this,e=d.core();if(b instanceof a)c=b,b=b.data(g);else if(c=d.getTagElement(b),null===c)
//Tag does not exist
return;c.remove(),d.updateFormCache(),e.getFormData(),e.invalidateBounds()},/**
	 * Creates and returns new HTML element from the source code specified in the `html.tag` option.
	 *
	 * @signature TextExtTags.renderTag(tag)
	 *
	 * @param tag {Object} Tag object in the format that current `ItemManager` can understand.
	 * Default is `String`.
	 *
	 * @author agorbatchev
	 * @date 2011/08/19
	 * @id TextExtTags.renderTag
	 */
c.renderTag=function(b){var c=this,d=a(c.opts(q));return d.find(".text-label").text(c.itemManager().itemToString(b)),d.data(g,b),d}}(jQuery);