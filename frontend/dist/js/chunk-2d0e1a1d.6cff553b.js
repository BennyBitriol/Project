(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-2d0e1a1d"],{"7aeb":function(t,s,e){"use strict";e.r(s);var n=function(){var t=this,s=t.$createElement,e=t._self._c||s;return e("div",[e("div",{staticClass:"row"},[e("div",{staticClass:"col-6"},[e("apexchart",{attrs:{type:"area",options:t.block.chartOptions,series:t.block.series}})],1),e("div",{staticClass:"col-6"})])])},a=[],i=e("bc3a"),c=e.n(i),o={data:function(){return{block:{}}},methods:{fetchEventsList:function(){var t=this;c.a.get("http://127.0.0.1:8000/api/sensor/graph1").then((function(s){return t.block=s.data})),console.log(this.block)}},mounted:function(){this.fetchEventsList(),this.timer=setInterval(this.fetchEventsList,3e4)}},r=o,l=e("2877"),h=Object(l["a"])(r,n,a,!1,null,null,null);s["default"]=h.exports}}]);
//# sourceMappingURL=chunk-2d0e1a1d.6cff553b.js.map