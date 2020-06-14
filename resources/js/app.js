
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap')

window.Vue = require('vue')

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

// TEMPLATE
Vue.component('eventos-view', require('./components/eventos/Eventos.vue'))
Vue.component('eventos-edit', require('./components/eventos/EditEvento.vue'))
import VueGoogleCharts from 'vue-google-charts'

Vue.use(VueGoogleCharts)
const VueGoogleMaps = require('vue2-google-maps')

Vue.use(VueGoogleMaps, {
  load: {
    key: 'AIzaSyBiVhB1Qw5qw_DghX4W5bjZOr5jmqB-zcE',
    v: '3.38',
    libraries: 'places,geocoder,drawing,visualization',
    language: "pt-br",
    autobindAllEvents: true
  }
})

import GmapCluster from 'vue2-google-maps/dist/components/cluster' // replace src with dist if you have Babel issues
Vue.component('GmapCluster', GmapCluster)

import ElementUI from 'element-ui'
import 'element-ui/lib/theme-chalk/index.css'
Vue.use(ElementUI)

import lang from 'element-ui/lib/locale/lang/pt-br'
import locale from 'element-ui/lib/locale'

locale.use(lang)
import { DataTables, DataTablesServer } from 'vue-data-tables'
Vue.use(DataTables)
Vue.use(DataTablesServer)

import eventBus from './event/'
Vue.use(eventBus)

import Vuex from 'vuex'
Vue.use(Vuex)
import store from './store/index'

const moment = require('moment')
require('moment/locale/pt-br')

Vue.use(require('vue-moment'), {
    moment
})
window.moment = moment


const app = new Vue({
    el: '#app',
    eventBus,
    store,
    data: () => ({

    }),
    mounted () {

    }
})
