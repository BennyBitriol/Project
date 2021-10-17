import Vue from 'vue'
import Vuex from 'vuex'
import auth from './auth'

// Modules
import app from './app'
import appConfig from './app-config'
import verticalMenu from './vertical-menu'

Vue.use(Vuex)
Vue.config.devtools = true
export default new Vuex.Store({
  modules: {
    app,
    appConfig,
    verticalMenu,
    auth,
  },
  strict: process.env.DEV,
})
