<template>
<b-container>
        <b-row>
            <b-col cols="12">
            <apexchart
            :options="info.chartOptions"
            :series="info.series"
            >
            </apexchart>
            </b-col>
            </b-row>
            <hr/>
        <b-row>
        <div class="col-6 border-right text-center">
            <h4 class="text-light">{{usage.now_usage}} ฿</h4>
            <h5 class="text-light">ใช้งานไปแล้ว</h5>
        </div>
        <div class="col-6 text-center">
            <h4 class="text-light">{{usage.target}} ฿</h4>
            <h5 class="text-light">ค่าไฟที่เหมาะสม</h5>
        </div>
        <b-col cols="12">
        <hr>
        </b-col>
            <div class="col-12 text-center">
                <!-- button on both side -->
                <b-input-group>
                <b-form-input placeholder="Change Target" v-model="value" />
               
                <b-input-group-append>
                    <b-button 
                    @click="updatechallenge"
                    variant="outline-primary">
                    Set target
                    </b-button>
                </b-input-group-append>
                </b-input-group>
                </div>
        </b-row>
        
    
</b-container>

</template>
<script>
import {BRow,BContainer,BCol,BButtonToolbar, BButtonGroup, BButton,BInputGroup, BFormInput, BInputGroupAppend, BInputGroupPrepend} from 'bootstrap-vue'
import { Carousel, Slide } from 'vue-carousel';
import Ripple from 'vue-ripple-directive'
export default {
    components:{
        BRow,
        BContainer,
        BCol,
        Carousel,
        Slide,
        BButtonToolbar,
        BButtonGroup,
        BButton, 
        BFormInput, 
        BInputGroup,
        BInputGroupAppend,
        BInputGroupPrepend
    },
    directives:{
        Ripple,
    },
    data() {
        return {
            tokens:'',
            info: {
                    chartOptions:{
                chart:{
                  type:'radialBar'
                }
              }
            },
            usage:[],
            value:'',
        }
    },
    async beforeCreate() {
        await this.$liff.init({liffId: "1655594666-ME3mz5N5" })
        if (this.$liff.isLoggedIn()){
            this.auth()
            this.graphinfo()
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
        graphinfo(){
            this.tokens = this.$liff.getIDToken()
            const params = {
                token: this.tokens
            }
            this.axios.get("/challengeline/graph",{params}).then(response => (
            this.usage = response.data,
            this.info = response.data.data,
            this.value = response.data.challenge_value,
            console.log(response.data)
            ))
        },
        updatechallenge(){
            const params = {
                token: this.tokens
            }
            this.axios.get("/updatechallenge/"+this.value,{params}).then(response => (
                this.value = response.data,
                this.graphinfo()
            ))
        }
    },
    mounted() {
        
    }
}
</script>
<style>
.VueCarousel-dot-container{
  margin-top: 0px!important;
}
.VueCarousel-dot{
  margin-top: 0px!important;
}
.navbar-hidden .app-content {
    padding: 0rem 2rem 0 2rem!important;
}
</style>