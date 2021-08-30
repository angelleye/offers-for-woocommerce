!function(e){"use strict";function t(e,t,a){if(void 0===e.selectionStart){e.focus();var n=e.createTextRange();n.collapse(!0),n.moveEnd("character",a),n.moveStart("character",t),n.select()}else e.selectionStart=t,e.selectionEnd=a}function a(e,t){"string"==typeof e[t]&&(e[t]*=1)}function n(t,n){!function(t,a){e.each(a,function(e,n){"function"==typeof n?a[e]=n(t,a,e):"function"==typeof t.autoNumerics[n]&&(a[e]=t.autoNumerics[n](t,a,e))})}(t,n),n.oEvent=null,n.tagList=["b","caption","cite","code","dd","del","div","dfn","dt","em","h1","h2","h3","h4","h5","h6","ins","kdb","label","li","output","p","q","s","sample","span","strong","td","th","u","var"];var i=n.vMax.toString().split("."),r=n.vMin||0===n.vMin?n.vMin.toString().split("."):[];if(a(n,"vMax"),a(n,"vMin"),a(n,"mDec"),n.mDec="CHF"===n.mRound?"2":n.mDec,n.allowLeading=!0,n.aNeg=n.vMin<0?"-":"",i[0]=i[0].replace("-",""),r[0]=r[0].replace("-",""),n.mInt=Math.max(i[0].length,r[0].length,1),null===n.mDec){var s=0,o=0;i[1]&&(s=i[1].length),r[1]&&(o=r[1].length),n.mDec=Math.max(s,o)}null===n.altDec&&n.mDec>0&&("."===n.aDec&&","!==n.aSep?n.altDec=",":","===n.aDec&&"."!==n.aSep&&(n.altDec="."));var u=n.aNeg?"([-\\"+n.aNeg+"]?)":"(-?)";n.aNegRegAutoStrip=u,n.skipFirstAutoStrip=new RegExp(u+"[^-"+(n.aNeg?"\\"+n.aNeg:"")+"\\"+n.aDec+"\\d].*?(\\d|\\"+n.aDec+"\\d)"),n.skipLastAutoStrip=new RegExp("(\\d\\"+n.aDec+"?)[^\\"+n.aDec+"\\d]\\D*$");var l="-"+n.aNum+"\\"+n.aDec;return n.allowedAutoStrip=new RegExp("[^"+l+"]","gi"),n.numRegAutoStrip=new RegExp(u+"(?:\\"+n.aDec+"?(\\d+\\"+n.aDec+"\\d+)|(\\d*(?:\\"+n.aDec+"\\d*)?))"),n}function i(e,t,a){if(t.aSign)for(;e.indexOf(t.aSign)>-1;)e=e.replace(t.aSign,"");e=(e=(e=e.replace(t.skipFirstAutoStrip,"$1$2")).replace(t.skipLastAutoStrip,"$1")).replace(t.allowedAutoStrip,""),t.altDec&&(e=e.replace(t.altDec,t.aDec));var n=e.match(t.numRegAutoStrip);if(e=n?[n[1],n[2],n[3]].join(""):"",("allow"===t.lZero||"keep"===t.lZero)&&"strip"!==a){var i=[],r="";-1!==(i=e.split(t.aDec))[0].indexOf("-")&&(r="-",i[0]=i[0].replace("-","")),i[0].length>t.mInt&&"0"===i[0].charAt(0)&&(i[0]=i[0].slice(1)),e=r+i.join(t.aDec)}if(a&&"deny"===t.lZero||a&&"allow"===t.lZero&&!1===t.allowLeading){var s="^"+t.aNegRegAutoStrip+"0*(\\d"+("leading"===a?")":"|$)");s=new RegExp(s),e=e.replace(s,"$1$2")}return e}function r(e,t,a){return t=t.split(","),"set"===a||"focusout"===a?(e=e.replace("-",""),e=t[0]+e+t[1]):"get"!==a&&"focusin"!==a&&"pageLoad"!==a||e.charAt(0)!==t[0]||(e=(e=e.replace(t[0],"-")).replace(t[1],"")),e}function s(e,t,a){if(t&&a){var n=e.split(t);n[1]&&n[1].length>a&&(a>0?(n[1]=n[1].substring(0,a),e=n.join(t)):e=n[0])}return e}function o(e,t,a){return t&&"."!==t&&(e=e.replace(t,".")),a&&"-"!==a&&(e=e.replace(a,"-")),e.match(/\d/)||(e+="0"),e}function u(e,t){if(e){var a=+e;if(a<1e-6&&a>-1)(e=+e)<1e-6&&e>0&&(e=(e=(e+10).toString()).substring(1)),e<0&&e>-1&&(e="-"+(e=(e-10).toString()).substring(2)),e=e.toString();else{var n=e.split(".");void 0!==n[1]&&(0==+n[1]?e=n[0]:(n[1]=n[1].replace(/0*$/,""),e=n.join(".")))}}return"keep"===t.lZero?e:e.replace(/^0*(\d)/,"$1")}function l(e,t,a){return a&&"-"!==a&&(e=e.replace("-",a)),t&&"."!==t&&(e=e.replace(".",t)),e}function c(t,a){var n=+(t=o(t=s(t=i(t,a),a.aDec,a.mDec),a.aDec,a.aNeg));return"set"===a.oEvent&&(n<a.vMin||n>a.vMax)&&e.error("The value ("+n+") from the 'set' method falls outside of the vMin / vMax range"),n>=a.vMin&&n<=a.vMax}function h(e,t,a){return""===e||e===t.aNeg?"zero"===t.wEmpty?e+"0":"sign"===t.wEmpty||a?e+t.aSign:e:null}function p(e,t){var a=(e=i(e,t)).replace(",","."),n=h(e,t,!0);if(null!==n)return n;var s="";s=2===t.dGroup?/(\d)((\d)(\d{2}?)+)$/:4===t.dGroup?/(\d)((\d{4}?)+)$/:/(\d)((\d{3}?)+)$/;var o=e.split(t.aDec);t.altDec&&1===o.length&&(o=e.split(t.altDec));var u=o[0];if(t.aSep)for(;s.test(u);)u=u.replace(s,"$1"+t.aSep+"$2");if(0!==t.mDec&&o.length>1?(o[1].length>t.mDec&&(o[1]=o[1].substring(0,t.mDec)),e=u+t.aDec+o[1]):e=u,t.aSign){var l=-1!==e.indexOf(t.aNeg);e=e.replace(t.aNeg,""),e="p"===t.pSign?t.aSign+e:e+t.aSign,l&&(e=t.aNeg+e)}return"set"===t.oEvent&&a<0&&null!==t.nBracket&&(e=r(e,t.nBracket,t.oEvent)),e}function g(e,t){e=""===e?"0":e.toString(),a(t,"mDec"),"CHF"===t.mRound&&(e=(Math.round(20*e)/20).toString());var n="",i=0,r="",s="boolean"==typeof t.aPad||null===t.aPad?t.aPad?t.mDec:0:+t.aPad,o=function(e){var t=0===s?/(\.(?:\d*[1-9])?)0*$/:1===s?/(\.\d(?:\d*[1-9])?)0*$/:new RegExp("(\\.\\d{"+s+"}(?:\\d*[1-9])?)0*$");return e=e.replace(t,"$1"),0===s&&(e=e.replace(/\.$/,"")),e};"-"===e.charAt(0)&&(r="-",e=e.replace("-","")),e.match(/^\d/)||(e="0"+e),"-"===r&&0==+e&&(r=""),(+e>0&&"keep"!==t.lZero||e.length>0&&"allow"===t.lZero)&&(e=e.replace(/^0*(\d)/,"$1"));var u=e.lastIndexOf("."),l=-1===u?e.length-1:u,c=e.length-1-l;if(c<=t.mDec){if(n=e,c<s){-1===u&&(n+=".");for(var h="000000";c<s;)n+=h=h.substring(0,s-c),c+=h.length}else c>s?n=o(n):0===c&&0===s&&(n=n.replace(/\.$/,""));if("CHF"!==t.mRound)return 0==+n?n:r+n;"CHF"===t.mRound&&(u=n.lastIndexOf("."),e=n)}var p=u+t.mDec,g=+e.charAt(p+1),d=e.substring(0,p+1).split(""),f="."===e.charAt(p)?e.charAt(p-1)%2:e.charAt(p)%2,m=!0;if(f=0===f&&e.substring(p+2,e.length)>0?1:0,g>4&&"S"===t.mRound||g>4&&"A"===t.mRound&&""===r||g>5&&"A"===t.mRound&&"-"===r||g>5&&"s"===t.mRound||g>5&&"a"===t.mRound&&""===r||g>4&&"a"===t.mRound&&"-"===r||g>5&&"B"===t.mRound||5===g&&"B"===t.mRound&&1===f||g>0&&"C"===t.mRound&&""===r||g>0&&"F"===t.mRound&&"-"===r||g>0&&"U"===t.mRound||"CHF"===t.mRound)for(i=d.length-1;i>=0;i-=1)if("."!==d[i]){if("CHF"===t.mRound&&d[i]<=2&&m){d[i]=0,m=!1;break}if("CHF"===t.mRound&&d[i]<=7&&m){d[i]=5,m=!1;break}if("CHF"===t.mRound&&m?(d[i]=10,m=!1):d[i]=+d[i]+1,d[i]<10)break;i>0&&(d[i]="0")}return 0==+(n=o((d=d.slice(0,p+1)).join("")))?n:r+n}function d(t,a){this.settings=a,this.that=t,this.$that=e(t),this.formatted=!1,this.settingsClone=n(this.$that,this.settings),this.value=t.value}function f(t){return"string"==typeof t&&(t="#"+(t=t.replace(/\[/g,"\\[").replace(/\]/g,"\\]")).replace(/(:|\.)/g,"\\$1")),e(t)}function m(e,t,a){var n=e.data("autoNumerics");n||(n={},e.data("autoNumerics",n));var i=n.holder;return(void 0===i&&t||a)&&(i=new d(e.get(0),t),n.holder=i),i}d.prototype={init:function(e){this.value=this.that.value,this.settingsClone=n(this.$that,this.settings),this.ctrlKey=e.ctrlKey,this.cmdKey=e.metaKey,this.shiftKey=e.shiftKey,this.selection=function(e){var t={};if(void 0===e.selectionStart){e.focus();var a=document.selection.createRange();t.length=a.text.length,a.moveStart("character",-e.value.length),t.end=a.text.length,t.start=t.end-t.length}else t.start=e.selectionStart,t.end=e.selectionEnd,t.length=t.end-t.start;return t}(this.that),"keydown"!==e.type&&"keyup"!==e.type||(this.kdCode=e.keyCode),this.which=e.which,this.processed=!1,this.formatted=!1},setSelection:function(e,a,n){e=Math.max(e,0),a=Math.min(a,this.that.value.length),this.selection={start:e,end:a,length:a-e},(void 0===n||n)&&t(this.that,e,a)},setPosition:function(e,t){this.setSelection(e,e,t)},getBeforeAfter:function(){var e=this.value;return[e.substring(0,this.selection.start),e.substring(this.selection.end,e.length)]},getBeforeAfterStriped:function(){var e=this.getBeforeAfter();return e[0]=i(e[0],this.settingsClone),e[1]=i(e[1],this.settingsClone),e},normalizeParts:function(e,t){var a=this.settingsClone;t=i(t,a),""!==(e=i(e,a,!!t.match(/^\d/)||"leading"))&&e!==a.aNeg||"deny"!==a.lZero||t>""&&(t=t.replace(/^0*(\d)/,"$1"));var n=e+t;if(a.aDec){var r=n.match(new RegExp("^"+a.aNegRegAutoStrip+"\\"+a.aDec));r&&(n=(e=e.replace(r[1],r[1]+"0"))+t)}return"zero"!==a.wEmpty||n!==a.aNeg&&""!==n||(e+="0"),[e,t]},setValueParts:function(e,t){var a=this.settingsClone,n=this.normalizeParts(e,t),i=n.join(""),r=n[0].length;return!!c(i,a)&&(r>(i=s(i,a.aDec,a.mDec)).length&&(r=i.length),this.value=i,this.setPosition(r,!1),!0)},signPosition:function(){var e=this.settingsClone,t=e.aSign,a=this.that;if(t){var n=t.length;if("p"===e.pSign)return e.aNeg&&a.value&&a.value.charAt(0)===e.aNeg?[1,n+1]:[0,n];var i=a.value.length;return[i-n,i]}return[1e3,-1]},expandSelectionOnSign:function(e){var t=this.signPosition(),a=this.selection;a.start<t[1]&&a.end>t[0]&&((a.start<t[0]||a.end>t[1])&&this.value.substring(Math.max(a.start,t[0]),Math.min(a.end,t[1])).match(/^\s*$/)?a.start<t[0]?this.setSelection(a.start,t[0],e):this.setSelection(t[1],a.end,e):this.setSelection(Math.min(a.start,t[0]),Math.max(a.end,t[1]),e))},checkPaste:function(){if(void 0!==this.valuePartsBeforePaste){var e=this.getBeforeAfter(),t=this.valuePartsBeforePaste;delete this.valuePartsBeforePaste,e[0]=e[0].substr(0,t[0].length)+i(e[0].substr(t[0].length),this.settingsClone),this.setValueParts(e[0],e[1])||(this.value=t.join(""),this.setPosition(t[0].length,!1))}},skipAllways:function(e){var t=this.kdCode,a=this.which,n=this.ctrlKey,i=this.cmdKey,r=this.shiftKey;if((n||i)&&"keyup"===e.type&&void 0!==this.valuePartsBeforePaste||r&&45===t)return this.checkPaste(),!1;if(t>=112&&t<=123||t>=91&&t<=93||t>=9&&t<=31||t<8&&(0===a||a===t)||144===t||145===t||45===t)return!0;if((n||i)&&65===t)return!0;if((n||i)&&(67===t||86===t||88===t))return"keydown"===e.type&&this.expandSelectionOnSign(),86!==t&&45!==t||("keydown"===e.type||"keypress"===e.type?void 0===this.valuePartsBeforePaste&&(this.valuePartsBeforePaste=this.getBeforeAfter()):this.checkPaste()),"keydown"===e.type||"keypress"===e.type||67===t;if(n||i)return!0;if(37===t||39===t){var s=this.settingsClone.aSep,o=this.selection.start,u=this.that.value;return"keydown"===e.type&&s&&!this.shiftKey&&(37===t&&u.charAt(o-2)===s?this.setPosition(o-1):39===t&&u.charAt(o+1)===s&&this.setPosition(o+1)),!0}return t>=34&&t<=40},processAllways:function(){var e;return(8===this.kdCode||46===this.kdCode)&&(this.selection.length?(this.expandSelectionOnSign(!1),e=this.getBeforeAfterStriped(),this.setValueParts(e[0],e[1])):(e=this.getBeforeAfterStriped(),8===this.kdCode?e[0]=e[0].substring(0,e[0].length-1):e[1]=e[1].substring(1,e[1].length),this.setValueParts(e[0],e[1])),!0)},processKeypress:function(){var e=this.settingsClone,t=String.fromCharCode(this.which),a=this.getBeforeAfterStriped(),n=a[0],i=a[1];return t===e.aDec||e.altDec&&t===e.altDec||("."===t||","===t)&&110===this.kdCode?!e.mDec||!e.aDec||(!!(e.aNeg&&i.indexOf(e.aNeg)>-1)||(n.indexOf(e.aDec)>-1||(i.indexOf(e.aDec)>0||(0===i.indexOf(e.aDec)&&(i=i.substr(1)),this.setValueParts(n+e.aDec,i),!0)))):"-"===t||"+"===t?!e.aNeg||(""===n&&i.indexOf(e.aNeg)>-1&&(n=e.aNeg,i=i.substring(1,i.length)),n=n.charAt(0)===e.aNeg?n.substring(1,n.length):"-"===t?e.aNeg+n:n,this.setValueParts(n,i),!0):!(t>="0"&&t<="9")||(e.aNeg&&""===n&&i.indexOf(e.aNeg)>-1&&(n=e.aNeg,i=i.substring(1,i.length)),e.vMax<=0&&e.vMin<e.vMax&&-1===this.value.indexOf(e.aNeg)&&"0"!==t&&(n=e.aNeg+n),this.setValueParts(n+t,i),!0)},formatQuick:function(){var e=this.settingsClone,t=this.getBeforeAfterStriped(),a=this.value;if((""===e.aSep||""!==e.aSep&&-1===a.indexOf(e.aSep))&&(""===e.aSign||""!==e.aSign&&-1===a.indexOf(e.aSign))){var n=[],i="";(n=a.split(e.aDec))[0].indexOf("-")>-1&&(i="-",n[0]=n[0].replace("-",""),t[0]=t[0].replace("-","")),n[0].length>e.mInt&&"0"===t[0].charAt(0)&&(t[0]=t[0].slice(1)),t[0]=i+t[0]}var r=p(this.value,this.settingsClone),s=r.length;if(r){for(var o=t[0].split(""),u=0;u<o.length;u+=1)o[u].match("\\d")||(o[u]="\\"+o[u]);var l=new RegExp("^.*?"+o.join(".*?")),c=r.match(l);c?(0===(s=c[0].length)&&r.charAt(0)!==e.aNeg||1===s&&r.charAt(0)===e.aNeg)&&e.aSign&&"p"===e.pSign&&(s=this.settingsClone.aSign.length+("-"===r.charAt(0)?1:0)):e.aSign&&"s"===e.pSign&&(s-=e.aSign.length)}this.that.value=r,this.setPosition(s),this.formatted=!0}};var v={init:function(a){return this.each(function(){var n=e(this),s=n.data("autoNumerics"),u=n.data();if("object"==typeof s)return this;if((s=e.extend({},{aNum:"0123456789",aSep:",",dGroup:"3",aDec:".",altDec:null,aSign:"",pSign:"p",vMax:"9999999999999.99",vMin:"0.00",mDec:null,mRound:"S",aPad:!0,nBracket:null,wEmpty:"empty",lZero:"allow",aForm:!0,onSomeEvent:function(){}},u,a)).aDec===s.aSep)return e.error("autoNumerics will not function properly when the decimal character aDec: '"+s.aDec+"' and thousand separator aSep: '"+s.aSep+"' are the same character"),this;n.data("autoNumerics",s),s.runOnce=!1;var d=m(n,s);if(-1===e.inArray(n.prop("tagName").toLowerCase(),s.tagList)&&"input"!==n.prop("tagName").toLowerCase())return e.error("The <"+n.prop("tagName").toLowerCase()+"> is not supported by autoNumerics()"),this;if(!1===s.runOnce&&s.aForm){if(n.is("input[type=text], input[type=hidden], input[type=tel], input:not([type])")){var f=!0;""===n[0].value&&"empty"===s.wEmpty&&(n[0].value="",f=!1),""===n[0].value&&"sign"===s.wEmpty&&(n[0].value=s.aSign,f=!1),f&&n.autoNumerics("set",n.val())}-1!==e.inArray(n.prop("tagName").toLowerCase(),s.tagList)&&""!==n.text()&&n.autoNumerics("set",n.text())}s.runOnce=!0,n.is("input[type=text], input[type=hidden], input[type=tel], input:not([type])")&&(n.on("keydown.autoNumerics",function(t){return(d=m(n)).settings.aDec===d.settings.aSep?(e.error("autoNumerics will not function properly when the decimal character aDec: '"+d.settings.aDec+"' and thousand separator aSep: '"+d.settings.aSep+"' are the same character"),this):d.that.readOnly?(d.processed=!0,!0):(d.init(t),d.settings.oEvent="keydown",d.skipAllways(t)?(d.processed=!0,!0):d.processAllways()?(d.processed=!0,d.formatQuick(),t.preventDefault(),!1):(d.formatted=!1,!0))}),n.on("keypress.autoNumerics",function(e){var t=m(n),a=t.processed;return t.init(e),t.settings.oEvent="keypress",!!t.skipAllways(e)||(a?(e.preventDefault(),!1):t.processAllways()||t.processKeypress()?(t.formatQuick(),e.preventDefault(),!1):void(t.formatted=!1))}),n.on("keyup.autoNumerics",function(e){var a=m(n);a.init(e),a.settings.oEvent="keyup";var i=a.skipAllways(e);return a.kdCode=0,delete a.valuePartsBeforePaste,n[0].value===a.settings.aSign&&("s"===a.settings.pSign?t(this,0,0):t(this,a.settings.aSign.length,a.settings.aSign.length)),!!i||(""===this.value||void(a.formatted||a.formatQuick()))}),n.on("focusin.autoNumerics",function(){var e=m(n);if(e.settingsClone.oEvent="focusin",null!==e.settingsClone.nBracket){var a=n.val();n.val(r(a,e.settingsClone.nBracket,e.settingsClone.oEvent))}e.inVal=n.val();var i=h(e.inVal,e.settingsClone,!0);null!==i&&(n.val(i),"s"===e.settings.pSign?t(this,0,0):t(this,e.settings.aSign.length,e.settings.aSign.length))}),n.on("focusout.autoNumerics",function(){var e=m(n),t=e.settingsClone,a=n.val(),s=a;e.settingsClone.oEvent="focusout";var u="";"allow"===t.lZero&&(t.allowLeading=!1,u="leading"),""!==a&&(a=null===h(a=i(a,t,u),t)&&c(a,t,n[0])?l(a=g(a=o(a,t.aDec,t.aNeg),t),t.aDec,t.aNeg):"");var d=h(a,t,!1);null===d&&(d=p(a,t)),d!==s&&n.val(d),d!==e.inVal&&(n.change(),delete e.inVal),null!==t.nBracket&&n.autoNumerics("get")<0&&(e.settingsClone.oEvent="focusout",n.val(r(n.val(),t.nBracket,t.oEvent)))}))})},destroy:function(){return e(this).each(function(){var t=e(this);t.off(".autoNumerics"),t.removeData("autoNumerics")})},update:function(t){return e(this).each(function(){var a=f(e(this)),n=a.data("autoNumerics");if("object"!=typeof n)return e.error("You must initialize autoNumerics('init', {options}) prior to calling the 'update' method"),this;var i=a.autoNumerics("get");return m(a,n=e.extend(n,t),!0),n.aDec===n.aSep?(e.error("autoNumerics will not function properly when the decimal character aDec: '"+n.aDec+"' and thousand separator aSep: '"+n.aSep+"' are the same character"),this):(a.data("autoNumerics",n),""!==a.val()||""!==a.text()?a.autoNumerics("set",i):void 0)})},set:function(t){return e(this).each(function(){var a=f(e(this)),n=a.data("autoNumerics"),s=t.toString(),o=t.toString();return"object"!=typeof n?(e.error("You must initialize autoNumerics('init', {options}) prior to calling the 'set' method"),this):(o!==a.attr("value")&&"input"===a.prop("tagName").toLowerCase()&&!1===n.runOnce&&(s=i(s=null!==n.nBracket?r(a.val(),n.nBracket,"pageLoad"):s,n)),o!==a.attr("value")&&o!==a.text()||!1!==n.runOnce||(s=s.replace(",",".")),e.isNumeric(+s)?(s=u(s,n),n.oEvent="set",s.toString(),""!==s&&(s=g(s,n)),c(s=l(s,n.aDec,n.aNeg),n)||(s=g("",n)),s=p(s,n),a.is("input[type=text], input[type=hidden], input[type=tel], input:not([type])")?a.val(s):-1!==e.inArray(a.prop("tagName").toLowerCase(),n.tagList)?a.text(s):(e.error("The <"+a.prop("tagName").toLowerCase()+"> is not supported by autoNumerics()"),!1)):"")})},get:function(){var t=f(e(this)),a=t.data("autoNumerics");if("object"!=typeof a)return e.error("You must initialize autoNumerics('init', {options}) prior to calling the 'get' method"),this;a.oEvent="get";var n="";if(t.is("input[type=text], input[type=hidden], input[type=tel], input:not([type])"))n=t.eq(0).val();else{if(-1===e.inArray(t.prop("tagName").toLowerCase(),a.tagList))return e.error("The <"+t.prop("tagName").toLowerCase()+"> is not supported by autoNumerics()"),!1;n=t.eq(0).text()}return""===n&&"empty"===a.wEmpty||n===a.aSign&&("sign"===a.wEmpty||"empty"===a.wEmpty)?"":(null!==a.nBracket&&""!==n&&(n=r(n,a.nBracket,a.oEvent)),(a.runOnce||!1===a.aForm)&&(n=i(n,a)),0==+(n=o(n,a.aDec,a.aNeg))&&"keep"!==a.lZero&&(n="0"),"keep"===a.lZero?n:n=u(n,a))},getString:function(){for(var t=!1,a=f(e(this)).serialize(),n=a.split("&"),i=0;i<n.length;i+=1){var r=n[i].split("=");"object"==typeof e('*[name="'+decodeURIComponent(r[0])+'"]').data("autoNumerics")&&null!==r[1]&&void 0!==e('*[name="'+decodeURIComponent(r[0])+'"]').data("autoNumerics")&&(r[1]=e('input[name="'+decodeURIComponent(r[0])+'"]').autoNumerics("get"),n[i]=r.join("="),t=!0)}return!0===t?n.join("&"):a},getArray:function(){var t=!1,a=f(e(this)).serializeArray();return e.each(a,function(a,n){"object"==typeof e('*[name="'+decodeURIComponent(n.name)+'"]').data("autoNumerics")&&(""!==n.value&&void 0!==e('*[name="'+decodeURIComponent(n.name)+'"]').data("autoNumerics")&&(n.value=e('input[name="'+decodeURIComponent(n.name)+'"]').autoNumerics("get").toString()),t=!0)}),!0===t?a:(e.error("You must initialize autoNumerics('init', {options}) prior to calling the 'getArray' method"),this)},getSettings:function(){return f(e(this)).eq(0).data("autoNumerics")}};e.fn.autoNumerics=function(t){return v[t]?v[t].apply(this,Array.prototype.slice.call(arguments,1)):"object"!=typeof t&&t?void e.error('Method "'+t+'" is not supported by autoNumerics()'):v.init.apply(this,arguments)}}(jQuery);