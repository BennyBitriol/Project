import Vue from 'vue'
Vue.config.devtools = true
import { ToastPlugin, ModalPlugin } from 'bootstrap-vue'
import VueCompositionAPI from '@vue/composition-api'

// 3rd party plugins
import '@/libs/portal-vue'
import '@/libs/toastification'

import axios from 'axios'
import router from './router'
import store from './store'
import App from './App.vue'


// Global Components
import './global-components'

require('./store/subscriber')

//axios
if (process.env.NODE_ENV === 'development') {
  axios.defaults.baseURL = 'http://127.0.0.1:8000/api'
} else if (process.env.NODE_ENV === 'production') {
  axios.defaults.baseURL = 'https://www.gadgetbuck.com/api'
}

import Axios from 'axios'
Vue.prototype.axios = Axios;

Vue.prototype.$liff = window.liff


//apexchart
import VueApexCharts from 'vue-apexcharts'
Vue.use(VueApexCharts)
Vue.component('apexchart', VueApexCharts)


// BSV Plugin Registration
Vue.use(ToastPlugin)
Vue.use(ModalPlugin)

//Comfort Meter
import VueFusionCharts from 'vue-fusioncharts';
import FusionCharts from 'fusioncharts';
import Widgets from 'fusioncharts/fusioncharts.widgets';
import Charts from 'fusioncharts/fusioncharts.charts';
import Theme from 'fusioncharts/themes/fusioncharts.theme.fusion';

// Resolves charts dependency
Vue.use(VueFusionCharts, FusionCharts, Charts,Widgets,Theme);
//end Comfort Meter

// Composition API
Vue.use(VueCompositionAPI)

// import core styles
require('@core/scss/core.scss')

// import assets styles
require('@/assets/scss/style.scss')

Vue.config.productionTip = false

store.dispatch('auth/attempt', localStorage.getItem('token')).then(() => {
new Vue({
  router,
  store,
  render: h => h(App),
}).$mount('#app')
})
