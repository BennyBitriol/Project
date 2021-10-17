<template>
<b-container>
  <b-button
  @click="enterroom(4)">
    Enter Room
  </b-button>
  {{name}}
  {{tokens}}
  </b-container>
</template>
<script> 
import {BButton,BContainer} from 'bootstrap-vue'
export default {
  components:{
    BButton,BContainer
  },
  data() {
    return {
      name:[],
      tokens:''
    }
  },

  async beforeCreate() {
    await this.$liff.init({liffId: "1655594666-KD40Op9p" })
    if (this.$liff.isLoggedIn()){
        this.tokens = this.$liff.getIDToken();
        console.log(this.tokens) // print raw idToken object
    }
    else{
        this.$liff.login()
    }

  },

  methods: {
    // ,query:{id:room_id,token:tokens}
    enterroom(room_id){
      this.$router.push({ name: 'viewroom' , query: { token:this.tokens, clickfrom: 'mainpage', id: room_id } })
      console.log("ENTER ROOM");

    },
    getprofile(){
      this.$liff.getProfile()
      .then(profile => {
        this.name = profile
        console.log(this.name);
      })
      .catch((err) => {
        console.log('error', err);
      });
      }
      }
    }
</script>