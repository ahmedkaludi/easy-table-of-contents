/*!
 * jQuery Smooth Scroll - v1.5.5 - 2015-02-19
 * https://github.com/kswedberg/jquery-smooth-scroll
 * Copyright (c) 2015 Karl Swedberg
 * Licensed MIT (https://github.com/kswedberg/jquery-smooth-scroll/blob/master/LICENSE-MIT)
 */
(function(a){if(typeof define==="function"&&define.amd){define(["jquery"],a);
}else{if(typeof module==="object"&&module.exports){a(require("jquery"));}else{a(jQuery);}}}(function(d){var b="1.5.5",f={},e={exclude:[],excludeWithin:[],offset:0,direction:"top",scrollElement:null,scrollTarget:null,beforeScroll:function(){},afterScroll:function(){},easing:"swing",speed:400,autoCoefficient:2,preventDefault:true},a=function(i){var j=[],h=false,g=i.dir&&i.dir==="left"?"scrollLeft":"scrollTop";
this.each(function(){if(this===document||this===window){return;}var k=d(this);if(k[g]()>0){j.push(this);}else{k[g](1);h=k[g]()>0;if(h){j.push(this);}k[g](0);
}});if(!j.length){this.each(function(){if(this.nodeName==="BODY"){j=[this];}});}if(i.el==="first"&&j.length>1){j=[j[0]];}return j;};d.fn.extend({scrollable:function(g){var h=a.call(this,{dir:g});
return this.pushStack(h);},firstScrollable:function(g){var h=a.call(this,{el:"first",dir:g});return this.pushStack(h);},smoothScroll:function(h,g){h=h||{};
if(h==="options"){if(!g){return this.first().data("ssOpts");}return this.each(function(){var l=d(this),k=d.extend(l.data("ssOpts")||{},g);d(this).data("ssOpts",k);
});}var i=d.extend({},d.fn.smoothScroll.defaults,h),j=d.smoothScroll.filterPath(location.pathname);this.unbind("click.smoothscroll").bind("click.smoothscroll",function(m){var u=this,t=d(this),o=d.extend({},i,t.data("ssOpts")||{}),n=i.exclude,r=o.excludeWithin,v=0,q=0,l=true,w={},p=((location.hostname===u.hostname)||!u.hostname),k=o.scrollTarget||(d.smoothScroll.filterPath(u.pathname)===j),s=c(u.hash);
if(!o.scrollTarget&&(!p||!k||!s)){l=false;}else{while(l&&v<n.length){if(t.is(c(n[v++]))){l=false;}}while(l&&q<r.length){if(t.closest(r[q++]).length){l=false;
}}}if(l){if(o.preventDefault){m.preventDefault();}d.extend(w,o,{scrollTarget:o.scrollTarget||s,link:u});d.smoothScroll(w);}});return this;}});d.smoothScroll=function(r,m){if(r==="options"&&typeof m==="object"){return d.extend(f,m);
}var g,h,q,i,n,p=0,j="offset",l="scrollTop",o={},k={};if(typeof r==="number"){g=d.extend({link:null},d.fn.smoothScroll.defaults,f);q=r;}else{g=d.extend({link:null},d.fn.smoothScroll.defaults,r||{},f);
if(g.scrollElement){j="position";if(g.scrollElement.css("position")==="static"){g.scrollElement.css("position","relative");}}}l=g.direction==="left"?"scrollLeft":l;
if(g.scrollElement){h=g.scrollElement;if(!(/^(?:HTML|BODY)$/).test(h[0].nodeName)){p=h[l]();}}else{h=d("html, body").firstScrollable(g.direction);}g.beforeScroll.call(h,g);
q=(typeof r==="number")?r:m||(d(g.scrollTarget)[j]()&&d(g.scrollTarget)[j]()[g.direction])||0;o[l]=q+p+g.offset;i=g.speed;if(i==="auto"){n=o[l]-h.scrollTop();
if(n<0){n*=-1;}i=n/g.autoCoefficient;}k={duration:i,easing:g.easing,complete:function(){g.afterScroll.call(g.link,g);}};if(g.step){k.step=g.step;}if(h.length){h.stop().animate(o,k);
}else{g.afterScroll.call(g.link,g);}};d.smoothScroll.version=b;d.smoothScroll.filterPath=function(g){g=g||"";return g.replace(/^\//,"").replace(/(?:index|default).[a-zA-Z]{3,4}$/,"").replace(/\/$/,"");
};d.fn.smoothScroll.defaults=e;function c(g){return g.replace(/(:|\.|\/)/g,"\\$1");}}));