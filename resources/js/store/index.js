import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

export default new Vuex.Store({
  state: {
    events: []
  },
  mutations: {
    updateEvents(state, events) {
      console.log("state: ", state)
      console.log("events: ", events)

      state.events = events
    }
  },
  getters: {
    events: state => state.events
  }
})