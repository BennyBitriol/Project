import axios from 'axios'

export default {
  namespaced: true,
  state: {
    token: null,
  },

  mutations: {
    SET_TOKEN(state,token){
      state.token = token
    }
  },

  getters:{
    authenticated(state){
      return state.token 
    }
  },
  actions: {
    loginLine (){
        return new Promise((resolve , reject) => {
            axios
              .get('https://www.gadgetbuck.com/login/line')
              .then((response) => {
                resolve(response)
              .catch((error) => {
                  reject(error)
              })
               })
        })
    },
    loginLineCallback ({dispatch}, payload){
      return new Promise((_ , reject) => {
          axios
            .get('https://www.gadgetbuck.com/login/line/callback',{
              params: payload
            })
            .then((response) => {
              if (response.data.token) {
                dispatch('attempt', response.data.token)
             }
             else {
              reject(response);
            }
          })
            .catch((error) => {
                reject(error)
            })
             })
    },
    attempt( { commit } , token){
      if (token){
        commit('SET_TOKEN', token)
      }


      
    }
  }
}

