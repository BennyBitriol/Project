<template>
  <div>
  <button @click="bobo()">bobo</button>
  <button @click="loginLine()">auth Github</button>
  <button @click="bypass()">Bypass</button>
  {{authenticated}}
  </div>
</template>

<script>
import {mapActions, mapGetters} from 'vuex'
export default {
  methods: {
    ...mapActions({
      loginLineaction: 'auth/loginLine',
      bypassaction: 'auth/bypass'
    }),
    
    bobo(){
      let token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczpcL1wvZ2FkZ2V0YnVjay5jb21cL2xvZ2luXC9saW5lXC9jYWxsYmFjayIsImlhdCI6MTYxNjY1MTA2MiwiZXhwIjoxNjE2NjU0NjYyLCJuYmYiOjE2MTY2NTEwNjIsImp0aSI6IlkzbjZubUtzQTFDUUFrYU0iLCJzdWIiOjEwLCJwcnYiOiJmNjRkNDhhNmNlYzdiZGZhN2ZiZjg5OTQ1NGI0ODhiM2U0NjI1MjBhIn0.1Fxt3hR18z1n_MdCzzN3b4hcI0YZHGAu4csdkd52LGU'
      this.axios.get('https://gadgetbuck.com/login/line/me', {
      headers: {
        Authorization: 'Bearer ' + token 
      }
      })
      .then((response)=>{
        console.log(response.data);
      })
      .catch((error)=>{
        console.error(error)
      })
  },
    loginLine() {
        this.loginLineaction().then((resp) => {
          if(resp.data.url){
            window.location.href = resp.data.url
          }
        })
    },
    bypass(){
      this.$store.commit('auth/SET_TOKEN', '88888')
    },
    tokenornot(){
      if(this.authenticated != null){
        alert('yes')
      }
      else{
        this.loginLine()
      }
    }
},
  created() {
    // this.tokenornot()
  },
  computed:{
    ...mapGetters({
      authenticated: 'auth/authenticated'
    }),
  }
};
</script>