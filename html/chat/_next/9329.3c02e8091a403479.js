(self.webpackChunk_N_E=self.webpackChunk_N_E||[]).push([[9329,3299],{26640:function(e,t,a){"use strict";a.d(t,{UK:function(){return c},YZ:function(){return l},x6:function(){return i}});var n=a(86006),s=a(54963),r=a(1405);function l(){let e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:{},[t,a]=(0,s.lr)();(0,n.useEffect)(()=>{let n=!1;t.forEach((a,s)=>{"function"==typeof e[s]&&(e[s](a),t.delete(s),n=!0)}),n&&a(t)},[t,e])}let i=":";function c(){let e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:{};function t(e){return e.startsWith(i)?e.slice(1):e}return{match:function(a){let n=t(a),s="function"==typeof e[n];return{matched:s,invoke:()=>s&&e[n](a)}},search:function(a){let n=t(a),s=r.ZP.Chat.Commands;return Object.keys(e).filter(e=>e.startsWith(n)).map(e=>({title:s[e],content:i+e}))}}}},33403:function(e,t,a){"use strict";a.r(t),a.d(t,{NewChat:function(){return M}});var n,s,r,l,i=a(9268),c=a(86006),o=a(72908),h=a(48),m=a(16778),d=a(665),f=a.n(d);function u(){return(u=Object.assign?Object.assign.bind():function(e){for(var t=1;t<arguments.length;t++){var a=arguments[t];for(var n in a)Object.prototype.hasOwnProperty.call(a,n)&&(e[n]=a[n])}return e}).apply(this,arguments)}var _=function(e){return c.createElement("svg",u({xmlns:"http://www.w3.org/2000/svg",xmlnsXlink:"http://www.w3.org/1999/xlink",width:16,height:16,fill:"none"},e),n||(n=c.createElement("defs",null,c.createElement("path",{id:"left_svg__a",d:"M0 0h16v16H0z"}))),c.createElement("g",null,s||(s=c.createElement("mask",{id:"left_svg__b",fill:"#fff"},c.createElement("use",{xlinkHref:"#left_svg__a"}))),c.createElement("g",{mask:"url(#left_svg__b)"},c.createElement("path",{style:{stroke:"#333",strokeWidth:1.3333333333333333,strokeOpacity:1,strokeDasharray:"0 0"},d:"M4 8 0 4l4-4",transform:"translate(6.333 4)"}))))};function k(){return(k=Object.assign?Object.assign.bind():function(e){for(var t=1;t<arguments.length;t++){var a=arguments[t];for(var n in a)Object.prototype.hasOwnProperty.call(a,n)&&(e[n]=a[n])}return e}).apply(this,arguments)}var w=function(e){return c.createElement("svg",k({xmlns:"http://www.w3.org/2000/svg",xmlnsXlink:"http://www.w3.org/1999/xlink",width:16,height:16,fill:"none"},e),c.createElement("g",null,r||(r=c.createElement("mask",{id:"lightning_svg__b",fill:"#fff"},c.createElement("use",{xlinkHref:"#lightning_svg__a"}))),c.createElement("g",{mask:"url(#lightning_svg__b)"},c.createElement("path",{style:{fill:"#333",opacity:1},d:"M2.248 8.852a.665.665 0 0 1-.172-.828l3.67-7a.664.664 0 0 1 .59-.357h6a.662.662 0 0 1 .634.46.669.669 0 0 1-.11.618l-2.822 3.592h3.629a.661.661 0 0 1 .605.387.663.663 0 0 1-.116.732l-8 8.66a.664.664 0 0 1-.893.078.66.66 0 0 1-.257-.441.66.66 0 0 1 .016-.26L6.472 9H2.666a.657.657 0 0 1-.42-.148zm1.52-1.185h3.569a.665.665 0 0 1 .644.836l-.986 3.74 5.148-5.573H8.667a.665.665 0 0 1-.63-.885.662.662 0 0 1 .105-.194L10.965 2H6.74z"}))),l||(l=c.createElement("defs",null,c.createElement("path",{id:"lightning_svg__a",d:"M0 0h16v16H0z"}))))},v=a(83573),g=a(17005),p=a(22852),x=a(1405),j=a(79724),N=a(28029),y=a(26640),E=a(19319),C=a(91396);function b(e){return(0,i.jsxs)("div",{className:f().mask,onClick:e.onClick,children:[(0,i.jsx)(N.MaskAvatar,{avatar:e.mask.avatar,model:e.mask.modelConfig.model}),(0,i.jsx)("div",{className:f()["mask-name"]+" one-line",children:e.mask.name})]})}function M(){let e=(0,j.aK)(),t=(0,p.Zy)(),a=t.getAll(),n=function(e){let[t,a]=(0,c.useState)([]);return(0,c.useEffect)(()=>{let t=()=>{let t=document.getElementById(o.ym.AppBody);if(!t||0===e.length)return;let n=t.getBoundingClientRect(),s=n.width,r=.6*n.height,l=()=>e[Math.floor(Math.random()*e.length)],i=0,c=()=>e[i++%e.length],h=Math.ceil(s/120),m=Array(Math.ceil(r/50)).fill(0).map((e,t)=>Array(h).fill(0).map((e,t)=>t<1||t>h-2?l():c()));a(m)};return t(),window.addEventListener("resize",t),()=>window.removeEventListener("resize",t)},[]),t}(a),s=(0,g.s0)(),r=(0,j.MG)(),l=(0,c.useRef)(null),{state:d}=(0,g.TH)(),u=t=>{setTimeout(()=>{e.newSession(t),s(o.y$.Chat)},10)};return(0,y.YZ)({mask:e=>{try{var a;let n=null!==(a=t.get(e))&&void 0!==a?a:C.$n.get(e);u(null!=n?n:void 0)}catch(t){console.error("[New Chat] failed to create chat from mask id=",e)}}}),(0,c.useEffect)(()=>{l.current&&(l.current.scrollLeft=(l.current.scrollWidth-l.current.clientWidth)/2)},[n]),(0,i.jsxs)("div",{className:f()["new-chat"],children:[(0,i.jsxs)("div",{className:f()["mask-header"],children:[(0,i.jsx)(h.h,{icon:(0,i.jsx)(_,{}),text:x.ZP.NewChat.Return,onClick:()=>s(o.y$.Home)}),!(null==d?void 0:d.fromHome)&&(0,i.jsx)(h.h,{text:x.ZP.NewChat.NotShow,onClick:async()=>{await (0,E.i0)(x.ZP.NewChat.ConfirmNoShow)&&(u(),r.update(e=>e.dontShowMaskSplashScreen=!0))}})]}),(0,i.jsxs)("div",{className:f()["mask-cards"],children:[(0,i.jsx)("div",{className:f()["mask-card"],children:(0,i.jsx)(m.eL,{avatar:"1f606",size:24})}),(0,i.jsx)("div",{className:f()["mask-card"],children:(0,i.jsx)(m.eL,{avatar:"1f916",size:24})}),(0,i.jsx)("div",{className:f()["mask-card"],children:(0,i.jsx)(m.eL,{avatar:"1f479",size:24})})]}),(0,i.jsx)("div",{className:f().title,children:x.ZP.NewChat.Title}),(0,i.jsx)("div",{className:f()["sub-title"],children:x.ZP.NewChat.SubTitle}),(0,i.jsxs)("div",{className:f().actions,children:[(0,i.jsx)(h.h,{text:x.ZP.NewChat.More,onClick:()=>s(o.y$.Masks),icon:(0,i.jsx)(v.Z,{}),bordered:!0,shadow:!0}),(0,i.jsx)(h.h,{text:x.ZP.NewChat.Skip,onClick:()=>u(),icon:(0,i.jsx)(w,{}),type:"primary",shadow:!0,className:f().skip})]}),(0,i.jsx)("div",{className:f().masks,ref:l,children:n.map((e,t)=>(0,i.jsx)("div",{className:f()["mask-row"],children:e.map((e,t)=>(0,i.jsx)(b,{mask:e,onClick:()=>u(e)},t))},t))})]})}},665:function(e){e.exports={"new-chat":"new-chat_new-chat__63RF3","mask-header":"new-chat_mask-header__nBwht","slide-in-from-top":"new-chat_slide-in-from-top__kKaCc","mask-cards":"new-chat_mask-cards__W1FzL","slide-in":"new-chat_slide-in__VIaHY","mask-card":"new-chat_mask-card__EXvr1",title:"new-chat_title__lfHL6","sub-title":"new-chat_sub-title__qYtID",actions:"new-chat_actions__ntcag",skip:"new-chat_skip__js1_N",masks:"new-chat_masks__ArNS9","mask-row":"new-chat_mask-row__ZRTfV",mask:"new-chat_mask__P5aBk","mask-name":"new-chat_mask-name__AytPM"}}}]);