<template>
     <b-row >
         <b-col cols="6" v-for="room in roomlist" :key="room.id" >
             <b-card
                border-variant="warning"
                bg-variant="transparent"
                class="shadow-none"
                @click="devicelist(room.id,room.statistic)">
                <b-card-text> 
                  <b-row>
                    <b-col cols="12" class="text-center" >
                      <img v-bind:src="room.icon" height="80" width="80"/>
                    </b-col>
                    <b-col cols="12" class="text-center" >
                      <h4 class="text-light">{{ room.statistic }}</h4>
                    </b-col>
                  </b-row>
                </b-card-text>
              </b-card>
         </b-col>
     </b-row>
</template>
<script>
import {BContainer , BRow , BCol , BCard ,BCardText , BButton} from 'bootstrap-vue'
export default {
    data() {
        return {
            roomlist:[],
            tokens:''
        }
    },
    components:{
        BContainer, 
        BRow, 
        BCol,
        BCard,
        BCardText,
        BButton
    },
    async beforeCreate() {
    await this.$liff.init({liffId: "1655594666-oJOza8E8" })
    if (this.$liff.isLoggedIn()){
        this.auth()
        this.getuserroom()
    }
    else{
        this.$liff.login()
    }
    },
    methods: {
        auth(){
          this.tokens = this.$liff.getIDToken()
          const params = {
            token: this.tokens
          };
          this.axios.get('/user/auth',{params}).then(response => (
            console.log('auth '+response.data)))
          },
        devicelist(roomid){
            this.tokens = this.$liff.getIDToken()
            this.$router.push({ name: 'viewroom' , query: { token:this.tokens, clickfrom: 'allroom', id: roomid } })
            //console.log("ENTER ROOM");
        },
        getuserroom(){
            this.tokens = this.$liff.getIDToken()
              const params = {
              token: this.tokens
            };
            this.axios.get('/room', {params})
            .then(response => (
            console.log(response.data),
            this.roomlist = response.data))
        }
    }
}
</script>
<style>
text{
  fill: #ffffff!important;
}
</style>