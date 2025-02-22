(self.webpackChunk_N_E = self.webpackChunk_N_E || []).push([[3185], {
    92096: function (e, t, n) {
        "use strict";
        Object.defineProperty(t, "__esModule", {value: !0});
        let r = n(9268), o = n(86006);
        t.default = function ({html: e, height: t = null, width: n = null, children: a, dataNtpc: i = ""}) {
            return (0, o.useEffect)(() => {
                i && performance.mark("mark_feature_usage", {detail: {feature: `next-third-parties-${i}`}})
            }, [i]), (0, r.jsxs)(r.Fragment, {
                children: [a, e ? (0, r.jsx)("div", {
                    style: {
                        height: null != t ? `${t}px` : "auto",
                        width: null != n ? `${n}px` : "auto"
                    }, "data-ntpc": i, dangerouslySetInnerHTML: {__html: e}
                }) : null]
            })
        }
    }, 64654: function (e, t, n) {
        "use strict";
        let r;
        var o = this && this.__importDefault || function (e) {
            return e && e.__esModule ? e : {default: e}
        };
        Object.defineProperty(t, "__esModule", {value: !0}), t.sendGAEvent = t.GoogleAnalytics = void 0;
        let a = n(9268), i = n(86006), l = o(n(96341));
         t.sendGAEvent = (...e) => {
            if (void 0 === r) {
                console.warn("@next/third-parties: GA has not been initialized");
                return
            }
            window[r] ? window[r].push(...e) : console.warn(`@next/third-parties: GA dataLayer ${r} does not exist`)
        }
    }, 37499: function (e, t, n) {
        "use strict";
        let r;
        var o = this && this.__importDefault || function (e) {
            return e && e.__esModule ? e : {default: e}
        };
        Object.defineProperty(t, "__esModule", {value: !0}), t.sendGTMEvent = t.GoogleTagManager = void 0;
        let a = n(9268), i = n(86006), l = o(n(96341));
        t.GoogleTagManager = function (e) {
            let {gtmId: t, dataLayerName: n = "dataLayer", auth: o, preview: u, dataLayer: s} = e;
            void 0 === r && (r = n);
            let d = "dataLayer" !== n ? `$l=${n}` : "", c = o ? `&gtm_auth=${o}` : "",
                f = u ? `&gtm_preview=${u}&gtm_cookies_win=x` : "";
            return (0, i.useEffect)(() => {
                performance.mark("mark_feature_usage", {detail: {feature: "next-third-parties-gtm"}})
            }, []), (0, a.jsxs)(a.Fragment, {
                children: [(0, a.jsx)(l.default, {
                    id: "_next-gtm-init", dangerouslySetInnerHTML: {
                        __html: `
      (function(w,l){
        w[l]=w[l]||[];
        w[l].push({'gtm.start': new Date().getTime(),event:'gtm.js'});
        ${s ? `w[l].push(${JSON.stringify(s)})` : ""}
      })(window,'${n}');`
                    }
                }), (0, a.jsx)(l.default, {
                    id: "_next-gtm",
                    "data-ntpc": "GTM",
                    src: ``
                })]
            })
        }, t.sendGTMEvent = e => {
            if (void 0 === r) {
                console.warn("@next/third-parties: GTM has not been initialized");
                return
            }
            window[r] ? window[r].push(e) : console.warn(`@next/third-parties: GTM dataLayer ${r} does not exist`)
        }
    }, 20646: function (e, t, n) {
        Promise.resolve().then(n.bind(n, 89869)), Promise.resolve().then(n.bind(n, 92096)), Promise.resolve().then(n.t.bind(n, 67676, 23)), Promise.resolve().then(n.t.bind(n, 37499, 23)), Promise.resolve().then(n.t.bind(n, 64654, 23)), Promise.resolve().then(n.t.bind(n, 87428, 23)), Promise.resolve().then(n.t.bind(n, 27983, 23)), Promise.resolve().then(n.t.bind(n, 89432, 23))
    }, 71522: function (e, t) {
        "use strict";
        let n;
        Object.defineProperty(t, "__esModule", {value: !0}), function (e, t) {
            for (var n in t) Object.defineProperty(e, n, {enumerable: !0, get: t[n]})
        }(t, {
            DOMAttributeNames: function () {
                return r
            }, isEqualNode: function () {
                return a
            }, default: function () {
                return i
            }
        });
        let r = {
            acceptCharset: "accept-charset",
            className: "class",
            htmlFor: "for",
            httpEquiv: "http-equiv",
            noModule: "noModule"
        };

        function o(e) {
            let {type: t, props: n} = e, o = document.createElement(t);
            for (let e in n) {
                if (!n.hasOwnProperty(e) || "children" === e || "dangerouslySetInnerHTML" === e || void 0 === n[e]) continue;
                let a = r[e] || e.toLowerCase();
                "script" === t && ("async" === a || "defer" === a || "noModule" === a) ? o[a] = !!n[e] : o.setAttribute(a, n[e])
            }
            let {children: a, dangerouslySetInnerHTML: i} = n;
            return i ? o.innerHTML = i.__html || "" : a && (o.textContent = "string" == typeof a ? a : Array.isArray(a) ? a.join("") : ""), o
        }

        function a(e, t) {
            if (e instanceof HTMLElement && t instanceof HTMLElement) {
                let n = t.getAttribute("nonce");
                if (n && !e.getAttribute("nonce")) {
                    let r = t.cloneNode(!0);
                    return r.setAttribute("nonce", ""), r.nonce = n, n === e.nonce && e.isEqualNode(r)
                }
            }
            return e.isEqualNode(t)
        }

        function i() {
            return {
                mountedInstances: new Set, updateHead: e => {
                    let t = {};
                    e.forEach(e => {
                        if ("link" === e.type && e.props["data-optimized-fonts"]) {
                            if (document.querySelector('style[data-href="' + e.props["data-href"] + '"]')) return;
                            e.props.href = e.props["data-href"], e.props["data-href"] = void 0
                        }
                        let n = t[e.type] || [];
                        n.push(e), t[e.type] = n
                    });
                    let r = t.title ? t.title[0] : null, o = "";
                    if (r) {
                        let {children: e} = r.props;
                        o = "string" == typeof e ? e : Array.isArray(e) ? e.join("") : ""
                    }
                    o !== document.title && (document.title = o), ["meta", "base", "link", "style", "script"].forEach(e => {
                        n(e, t[e] || [])
                    })
                }
            }
        }

        n = (e, t) => {
            let n = document.getElementsByTagName("head")[0], r = n.querySelector("meta[name=next-head-count]"),
                i = Number(r.content), l = [];
            for (let t = 0, n = r.previousElementSibling; t < i; t++, n = (null == n ? void 0 : n.previousElementSibling) || null) {
                var u;
                (null == n ? void 0 : null == (u = n.tagName) ? void 0 : u.toLowerCase()) === e && l.push(n)
            }
            let s = t.map(o).filter(e => {
                for (let t = 0, n = l.length; t < n; t++) {
                    let n = l[t];
                    if (a(n, e)) return l.splice(t, 1), !1
                }
                return !0
            });
            l.forEach(e => {
                var t;
                return null == (t = e.parentNode) ? void 0 : t.removeChild(e)
            }), s.forEach(e => n.insertBefore(e, r)), r.content = (i - l.length + s.length).toString()
        }, ("function" == typeof t.default || "object" == typeof t.default && null !== t.default) && void 0 === t.default.__esModule && (Object.defineProperty(t.default, "__esModule", {value: !0}), Object.assign(t.default, t), e.exports = t.default)
    }, 19830: function (e, t) {
        "use strict";
        Object.defineProperty(t, "__esModule", {value: !0}), function (e, t) {
            for (var n in t) Object.defineProperty(e, n, {enumerable: !0, get: t[n]})
        }(t, {
            requestIdleCallback: function () {
                return n
            }, cancelIdleCallback: function () {
                return r
            }
        });
        let n = "undefined" != typeof self && self.requestIdleCallback && self.requestIdleCallback.bind(window) || function (e) {
                let t = Date.now();
                return self.setTimeout(function () {
                    e({
                        didTimeout: !1, timeRemaining: function () {
                            return Math.max(0, 50 - (Date.now() - t))
                        }
                    })
                }, 1)
            },
            r = "undefined" != typeof self && self.cancelIdleCallback && self.cancelIdleCallback.bind(window) || function (e) {
                return clearTimeout(e)
            };
        ("function" == typeof t.default || "object" == typeof t.default && null !== t.default) && void 0 === t.default.__esModule && (Object.defineProperty(t.default, "__esModule", {value: !0}), Object.assign(t.default, t), e.exports = t.default)
    }, 67676: function (e, t, n) {
        "use strict";
        Object.defineProperty(t, "__esModule", {value: !0}), function (e, t) {
            for (var n in t) Object.defineProperty(e, n, {enumerable: !0, get: t[n]})
        }(t, {
            handleClientScriptLoad: function () {
                return m
            }, initScriptLoader: function () {
                return g
            }, default: function () {
                return _
            }
        });
        let r = n(26927), o = n(25909), a = r._(n(8431)), i = o._(n(86006)), l = n(27268), u = n(71522), s = n(19830),
            d = new Map, c = new Set,
            f = ["onLoad", "onReady", "dangerouslySetInnerHTML", "children", "onError", "strategy"], p = e => {
                let {
                    src: t,
                    id: n,
                    onLoad: r = () => {
                    },
                    onReady: o = null,
                    dangerouslySetInnerHTML: a,
                    children: i = "",
                    strategy: l = "afterInteractive",
                    onError: s
                } = e, p = n || t;
                if (p && c.has(p)) return;
                if (d.has(t)) {
                    c.add(p), d.get(t).then(r, s);
                    return
                }
                let m = () => {
                    o && o(), c.add(p)
                }, g = document.createElement("script"), h = new Promise((e, t) => {
                    g.addEventListener("load", function (t) {
                        e(), r && r.call(this, t), m()
                    }), g.addEventListener("error", function (e) {
                        t(e)
                    })
                }).catch(function (e) {
                    s && s(e)
                });
                for (let [n, r] of (a ? (g.innerHTML = a.__html || "", m()) : i ? (g.textContent = "string" == typeof i ? i : Array.isArray(i) ? i.join("") : "", m()) : t && (g.src = t, d.set(t, h)), Object.entries(e))) {
                    if (void 0 === r || f.includes(n)) continue;
                    let e = u.DOMAttributeNames[n] || n.toLowerCase();
                    g.setAttribute(e, r)
                }
                "worker" === l && g.setAttribute("type", "text/partytown"), g.setAttribute("data-nscript", l), document.body.appendChild(g)
            };

        function m(e) {
            let {strategy: t = "afterInteractive"} = e;
            "lazyOnload" === t ? window.addEventListener("load", () => {
                (0, s.requestIdleCallback)(() => p(e))
            }) : p(e)
        }

        function g(e) {
            e.forEach(m), function () {
                let e = [...document.querySelectorAll('[data-nscript="beforeInteractive"]'), ...document.querySelectorAll('[data-nscript="beforePageRender"]')];
                e.forEach(e => {
                    let t = e.id || e.getAttribute("src");
                    c.add(t)
                })
            }()
        }

        function h(e) {
            let {
                id: t, src: n = "", onLoad: r = () => {
                }, onReady: o = null, strategy: u = "afterInteractive", onError: d, ...f
            } = e, {
                updateScripts: m,
                scripts: g,
                getIsSsr: h,
                appDir: _,
                nonce: y
            } = (0, i.useContext)(l.HeadManagerContext), v = (0, i.useRef)(!1);
            (0, i.useEffect)(() => {
                let e = t || n;
                v.current || (o && e && c.has(e) && o(), v.current = !0)
            }, [o, t, n]);
            let w = (0, i.useRef)(!1);
            if ((0, i.useEffect)(() => {
                !w.current && ("afterInteractive" === u ? p(e) : "lazyOnload" === u && ("complete" === document.readyState ? (0, s.requestIdleCallback)(() => p(e)) : window.addEventListener("load", () => {
                    (0, s.requestIdleCallback)(() => p(e))
                })), w.current = !0)
            }, [e, u]), ("beforeInteractive" === u || "worker" === u) && (m ? (g[u] = (g[u] || []).concat([{
                id: t,
                src: n,
                onLoad: r,
                onReady: o,
                onError: d, ...f
            }]), m(g)) : h && h() ? c.add(t || n) : h && !h() && p(e)), _) {
                if ("beforeInteractive" === u) return n ? (a.default.preload(n, f.integrity ? {
                    as: "script",
                    integrity: f.integrity
                } : {as: "script"}), i.default.createElement("script", {
                    nonce: y,
                    dangerouslySetInnerHTML: {__html: "(self.__next_s=self.__next_s||[]).push(" + JSON.stringify([n]) + ")"}
                })) : (f.dangerouslySetInnerHTML && (f.children = f.dangerouslySetInnerHTML.__html, delete f.dangerouslySetInnerHTML), i.default.createElement("script", {
                    nonce: y,
                    dangerouslySetInnerHTML: {__html: "(self.__next_s=self.__next_s||[]).push(" + JSON.stringify([0, {...f}]) + ")"}
                }));
                "afterInteractive" === u && n && a.default.preload(n, f.integrity ? {
                    as: "script",
                    integrity: f.integrity
                } : {as: "script"})
            }
            return null
        }

        Object.defineProperty(h, "__nextScript", {value: !0});
        let _ = h;
        ("function" == typeof t.default || "object" == typeof t.default && null !== t.default) && void 0 === t.default.__esModule && (Object.defineProperty(t.default, "__esModule", {value: !0}), Object.assign(t.default, t), e.exports = t.default)
    }, 87428: function () {
    }, 89432: function () {
    }, 27983: function () {
    }, 83177: function (e, t, n) {
        "use strict";
        /**
         * @license React
         * react-jsx-runtime.production.min.js
         *
         * Copyright (c) Meta Platforms, Inc. and affiliates.
         *
         * This source code is licensed under the MIT license found in the
         * LICENSE file in the root directory of this source tree.
         */var r = n(86006), o = Symbol.for("react.element"), a = Symbol.for("react.fragment"),
            i = Object.prototype.hasOwnProperty,
            l = r.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED.ReactCurrentOwner,
            u = {key: !0, ref: !0, __self: !0, __source: !0};

        function s(e, t, n) {
            var r, a = {}, s = null, d = null;
            for (r in void 0 !== n && (s = "" + n), void 0 !== t.key && (s = "" + t.key), void 0 !== t.ref && (d = t.ref), t) i.call(t, r) && !u.hasOwnProperty(r) && (a[r] = t[r]);
            if (e && e.defaultProps) for (r in t = e.defaultProps) void 0 === a[r] && (a[r] = t[r]);
            return {$$typeof: o, type: e, key: s, ref: d, props: a, _owner: l.current}
        }

        t.Fragment = a, t.jsx = s, t.jsxs = s
    }, 9268: function (e, t, n) {
        "use strict";
        e.exports = n(83177)
    }, 56008: function (e, t, n) {
        e.exports = n(4e3)
    }, 96341: function (e, t, n) {
        e.exports = n(67676)
    }, 89869: function (e, t, n) {
        "use strict";
        n.r(t), n.d(t, {
            SpeedInsights: function () {
                return p
            }
        });
        var r = n(86006), o = n(56008), a = () => {
            window.si || (window.si = function (...e) {
                (window.siq = window.siq || []).push(e)
            })
        };

        function i() {
            return false
        }

        var l = "", u = `${l}/script.js`, s = `${l}/script.debug.js`;

        function d(e) {
            let t = (0, r.useRef)(null);
            return (0, r.useEffect)(() => {
                if (t.current) e.route && t.current(e.route); else {
                    let n = function (e) {
                        var t;
                        if (!("undefined" != typeof window) || null === e.route) return null;
                        a();
                        let n = !!e.dsn, r = e.scriptSrc || (n ? u : "_vercel/speed-insights/script.js");
                        if (document.head.querySelector(`script[src*="${r}"]`)) return null;
                        e.beforeSend && (null == (t = window.si) || t.call(window, "beforeSend", e.beforeSend));
                        let o = document.createElement("script");
                        return o.src = r, o.defer = !0, o.dataset.sdkn = "@vercel/speed-insights" + (e.framework ? `/${e.framework}` : ""), o.dataset.sdkv = "1.0.2", e.sampleRate && (o.dataset.sampleRate = e.sampleRate.toString()), e.route && (o.dataset.route = e.route), e.endpoint && (o.dataset.endpoint = e.endpoint), e.dsn && (o.dataset.dsn = e.dsn), o.onerror = () => {
                            console.log(`[Vercel Speed Insights] Failed to load script from ${r}. Please check if any content blockers are enabled and try again.`)
                        }, document.head.appendChild(o), {
                            setRoute: e => {
                                o.dataset.route = e ?? void 0
                            }
                        }
                    }({framework: e.framework || "react", ...e});
                    n && (t.current = n.setRoute)
                }
            }, [e.route]), null
        }

        var c = () => {
            let e = (0, o.useParams)(), t = (0, o.useSearchParams)(), n = (0, o.usePathname)(),
                a = (0, r.useMemo)(() => e ? 0 !== Object.keys(e).length ? e : {...Object.fromEntries(t.entries())} : null, [e, t]);
            return function (e, t) {
                if (!e || !t) return e;
                let n = e;
                try {
                    for (let [e, r] of Object.entries(t)) {
                        let t = Array.isArray(r), o = t ? r.join("/") : r, a = t ? `...${e}` : e,
                            i = RegExp(`/${o.replace(/[.*+?^${}()|[\]\\]/g, "\\$&")}(?=[/?#]|$)`);
                        i.test(n) && (n = n.replace(i, `/[${a}]`))
                    }
                    return n
                } catch (t) {
                    return e
                }
            }(n, a)
        };

        function f(e) {
            let t = c();
            return r.createElement(d, {route: t, ...e, framework: "next"})
        }

        function p(e) {
            return r.createElement(r.Suspense, {fallback: null}, r.createElement(f, {...e}))
        }
    }
}, function (e) {
    e.O(0, [9253, 7698, 1744], function () {
        return e(e.s = 20646)
    }), _N_E = e.O()
}]);