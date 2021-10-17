<template>
<b-container fluid>
  <b-row>
    <b-col cols="12">
      <b-card 
        border-variant="warning"
        bg-variant="transparent"
        class="shadow-none">
        <h2 class="text-center text-light"><b>SCEM House</b></h2>
        <h5 class="text-center text-light">Smart Cloud Energy Monitor House</h5>
      </b-card>
    </b-col>

    <b-col cols="12">
      <b-card
        border-variant="warning"
        bg-variant="transparent"
        class="shadow-none">
        <b-card-text>
          <div class="row justify-content-between">
            <div class="col-12"> 
            </div>
            <div class="col-7 text-center" >
              <h4 class="text-warning">Today Weather</h4>
              <h1 class="display-3 text-light" >{{weatherhl[0].tc}}°</h1>
              <p class="text-light">Min {{weatherdl[0].data.tc_min}}° | Max {{weatherdl[0].data.tc_max}}°</p>
              <h4 class="text-light">Indoor : {{cost.indoortemp}}°</h4>
              <h4 class="text-light">Humidity : {{cost.indoorhumi}}%</h4>
            </div>
            <div class="col-5 align-self-center" >
              <div class="text-center">
                <img v-bind:src="weatherhl[0].img" height="120" width="120"/>
                <br><br>
                <h3 class="text-light">{{ weatherhl[0].condition_en }}</h3>
              </div>
              <!-- <b-button
                class="btn-block"
                variant="outline-primary"
                @click="$router.push({name: 'weatherdetail', params:{  clickfrom: 'mainpage'}})"
              >Detail
              </b-button> -->
            </div>
          </div>
         
        </b-card-text>
      </b-card>
    </b-col>
    <b-col cols="12">
      <b-card
        border-variant="warning"
        bg-variant="transparent"
        class="shadow-none">
        <b-card-text>
          <b-row align-v="center">
            <b-col cols="12">
            <h3 class="text-warning">Humidex</h3>
            </b-col>
          </b-row>
          <hr>
          <!-- <h1 class="text-light text-center">Comfort Condition </h1> -->
          <b-row>
              <b-col cols="12">
                <h2 class="text-light text-center">{{dataSource.status}}</h2>
                <h5 class="text-light text-center">{{humidexsuggest}}</h5>
              </b-col>
          </b-row>
          <fusioncharts
            :type="dataSource.graph.type"
            width="100%"
            height="110"
            :dataFormat="dataSource.graph.dataFormat"
            :dataSource="dataSource.graph"
          ></fusioncharts> 
        </b-card-text>
      </b-card>
    </b-col>


    <b-col cols="12">
      <b-card
        border-variant="warning"
        bg-variant="transparent"
        class="shadow-none">
        <b-card-text>
          <b-row align-v="center">
            <b-col cols="5">
            <h4 class="text-warning">Weather Forecast</h4>
            </b-col>
            <b-col cols="7" class="text-right">
              <b-form-radio-group id="btn-radios-1" v-model="weeklyorhour"  button-variant="outline-primary" :options="options" buttons name="radios-btn-default" size="sm"/>
            </b-col>
          </b-row>
          <hr>
          <carousel :perPage="3" paginationActiveColor="#ff9f43" v-if="weeklyorhour == 'weekly'">
            <slide v-for="(data,i) in weatherdl" :key="i">
              <div class="text-center">
                <img v-bind:src="data.img" height="80" width="80"/>
                <br><br>
                <h6 class="text-light">{{ data.condition_en }}</h6>
                <p class="text-light">{{ data.tc_min}}° | {{ data.tc_max}}°</p>
                <h6 class="text-light">{{ data.day }}</h6>    
              </div>
            </slide>
          </carousel>
          <carousel :perPage="3" paginationActiveColor="#ff9f43" v-if="weeklyorhour == 'hourly'">
            <slide v-for="(data,i) in weatherhl" :key="i">
              <div class="text-center">
                <img v-bind:src="data.img" height="80" width="80"/>
                <br><br>
                <h6 class="text-light">{{ data.condition_en }}</h6>
                <p class="text-light">{{ data.tc}}° | {{ data.rh}}%</p>
                <h6 class="text-light">{{ data.day }}</h6>    
              </div>
            </slide>
          </carousel>
        </b-card-text>
      </b-card>
    </b-col>

    <b-col cols="12">
      <b-card 
        border-variant="warning"
        bg-variant="transparent"
        class="shadow-none">
        <b-card-text>
          <div class="row">
            <div class="col-7">
              <h4 class="text-warning"><feather-icon icon="ZapIcon" size="30"/> Energy Usage</h4>
            </div>
            <div class="col-5">
              <b-button
                @click="gotoenergyreportall()"
                class="btn-block"
                variant="outline-primary"
              >Detail
              </b-button>
            </div>
          </div>
          <hr>
          <div class="row mt-100">
            <div class="col-4 border-right text-center">
              <h4 class="text-light">{{Number(cost.hour).toLocaleString()}} ฿</h4>
              <h5 class="text-light">Usage/Hr</h5>
            </div>
            <div class="col-4 border-right text-center">
              <h4 class="text-light">{{Number(cost.day).toLocaleString()}} ฿</h4>
              <h5 class="text-light">Today Cost</h5>
            </div>
            <div class="col-4  text-center">
              <h4 class="text-light">{{Number(cost.month).toLocaleString()}} ฿</h4>
              <h5 class="text-light">This month cost</h5>
            </div>          
          </div>
        </b-card-text>
      </b-card>
    </b-col>

    <b-col cols="12">
      <b-card 
        border-variant="warning"
        bg-variant="transparent"
        class="shadow-none">
        <b-card-text>
          <div class="row">
            <div class="col-12">
              <h4 class="text-warning"><feather-icon icon="ZapIcon" size="30"/> Energy Meter</h4>
            </div>
          </div>
          <hr>
          <div class="row mt-100">
            <div class="col-6 border-right text-center">
              <h4 class="text-light">{{Number(cost.volt).toLocaleString()}}</h4>
              <h5 class="text-light">Voltage</h5>
            </div>
            <div class="col-6 text-center">
              <h4 class="text-light">{{Number(cost.amp).toLocaleString()}}</h4>
              <h5 class="text-light">Current</h5>
            </div>
            <div class="col-12">
              <hr>
            </div>
            <div class="col-6 border-right text-center">
              <h4 class="text-light">{{Number(cost.watt).toLocaleString()}}</h4>
              <h5 class="text-light">Power</h5>
            </div>
            <div class="col-6 text-center">
              <h4 class="text-light">{{Number(cost.unit).toLocaleString()}}</h4>
              <h5 class="text-light">Unit</h5>
            </div>         
          </div>
        </b-card-text>
      </b-card>
    </b-col>

    <b-col cols="12">
      <b-card 
        border-variant="warning"
        bg-variant="transparent"
        class="shadow-none">
        <b-card-text>
          <div class="row">
            <div class="col-12">
              <h4 class="text-warning"><feather-icon icon="ZapIcon" size="30"/> Energy Usage Prediction</h4>
            </div>
          </div>
          <hr>
          <div class="row mt-100">
            <div class="col-6 border-right text-center">
              <h4 class="text-light">{{pred.today}} ฿</h4>
              <h5 class="text-light">Today</h5>
            </div>
            <div class="col-6 text-center">
              <h4 class="text-light">{{pred.f10day}} ฿</h4>
              <h5 class="text-light">10 days</h5>
            </div>
          </div>
        </b-card-text>
      </b-card>
    </b-col>    
  </b-row>
  <b-row>
    <b-col cols="12">
      <b-row>
      <b-col cols="7" > 
      <h4 class="text-warning">Rooms</h4>                             
      </b-col>
      <b-col cols="5" class="text-right"> 
      <b-button
        size="sm"
        v-ripple.400="'rgba(113, 102, 240, 0.15)'"
        v-b-modal.modal-select2
        variant="outline-primary"
      >
        Add Room
      </b-button>
      <!-- <b-button
        @click="logout()"
        class="btn-block"
        variant="outline-primary"
      >Logout
      </b-button> -->
      </b-col>
      </b-row>
    <b-modal
      id="modal-select2"
      ok-title="Add room"
      cancel-variant="outline-secondary"
      @ok="addroom"
    >
      <b-form>
        <b-form-group
          label="Enter Room name"
          label-for="roomname"
        >
          <b-form-input
            id="roomname"
            placeholder="Enter Room name"
            v-model="roomname"
          />
        </b-form-group>
        <b-form-group
          label="Choose room type"
          label-for="vue-select"
        >
        <b-form-select v-model="room_selected" :options="option"></b-form-select>
        </b-form-group>
      </b-form>
    </b-modal>
      <hr>
      <carousel :perPage="1" paginationActiveColor="#ff9f43">
        <slide  v-for="(room,ii) in roomlist" :key="ii">
              <b-card
                border-variant="warning"
                bg-variant="transparent"
                class="shadow-none">
                <b-card-text> 
                  <b-row>
                    <b-col cols="5">
                      <img v-bind:src="room.icon" height="100" width="100"/>
                    </b-col>
                    <b-col cols="7">
                      <h3 class="text-light">{{ room.statistic }}</h3> 
                      <!-- {{lineid}} -->
                      <p class="text-light">{{room.statistictitle}}</p>
                      <div>
                        <b-button
                          class="btn-block"
                          variant="outline-primary"
                          @click="devicelist(room.id,room.statistic)"
                        >Enter
                        </b-button>
                      </div>
                    </b-col>
                  </b-row>
                </b-card-text>
              </b-card>
        </slide>
      </carousel>
    </b-col>
  </b-row>
</b-container>
</template>

<script>
import { Swiper, SwiperSlide } from "vue-awesome-swiper";
import { ZapIcon } from 'vue-feather-icons'
import { BImg, BCol, BRow, BCard, BCardText , BCardTitle , BTable , BButton ,BFormCheckbox,BFormRadioGroup,BModal, VBModal, BForm, BFormInput, BFormGroup,BFormSelect} from "bootstrap-vue";
import StatisticCardVertical from "@core/components/statistics-cards/StatisticCardVertical.vue";
import { Carousel, Slide } from 'vue-carousel';
import "swiper/css/swiper.css";
import Ripple from 'vue-ripple-directive'

// Import package
import RockerSwitch from "vue-rocker-switch";
// Import styles
import "vue-rocker-switch/dist/vue-rocker-switch.css";

// var vConsole = new VConsole();

export default {
  components: {
    Swiper,
    SwiperSlide,
    BImg,
    BRow,
    BCol,
    BCard,
    BCardText , 
    BCardTitle,
    StatisticCardVertical,
    ZapIcon,
    BTable,
    BButton,
    BFormCheckbox,
    RockerSwitch,
    Carousel,
    Slide,
    BFormRadioGroup,
    BModal, 
    VBModal, 
    BForm, 
    BFormInput, 
    BFormGroup,
    BFormSelect

  },
  directives:{
    'b-modal': VBModal,
    Ripple,
  },
  data() {
    return {
      lineid:'',
      humidexsuggest:'',
      tokens:'',
      uuuu:'',
      url:'https://997b3020bdad.ngrok.io/',
      roomname:'',
      room_selected: '',
      option: ['Living room', 'Bed Room', 'Kitchen', 'Bathroom'],
      dataSource:[],
      weeklyorhour: "weekly",
      options: [
          { text: 'Weekly', value: 'weekly' },
          { text: 'Hourly', value: 'hourly' },
        ],
      weatherdl:[],
      weatherhl:'',
      block: {},
      roomlist: [],
      pred:[],
      cost:[],
      count:0,
      swiperOptions: {
        effect: "coverflow",
        grabCursor: true,
        centeredSlides: true,
        slidesPerView: "auto",
        coverflowEffect: {
          rotate: 50,
          stretch: 0,
          depth: 100,
          modifier: 1,
          slideShadows: true,
        },
        pagination: {
          el: ".swiper-pagination",
        },
      },
    };
  },

  async beforeCreate() {
    await this.$liff.init({liffId: "1655594666-m92r6qKq " })  //real mainpage 1655594666-m92r6qKq   //benny mainpage 1655594666-ROYLq6m6
    if (this.$liff.isLoggedIn()){
        this.auth()
        this.getuserroom()
//         this.$liff.getProfile()
//         .then(profile => {
//           this.lineid = profile.userId
// })
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

      //this.$router.push('https://liff.line.me/1655594666-7GgbaQ3Q',params:{xxx:567,yyy:888});
      this.$router.push({ name: 'viewroom' , query: { token:this.tokens, clickfrom: 'mainpage', id: roomid } })
      //console.log("ENTER ROOM");
      //this.$router.push({ name: 'viewroom', params: {  clickfrom: 'mainpage', room_id: roomid ,  id: roomid  } })
    },
    gotoenergyreportall(){
      this.tokens = this.$liff.getIDToken()
      this.$router.push({name: 'energyreport', params:{  clickfrom: 'mainpage', id: 1 , type: 'home', token: this.tokens  }})
    },
    bobo(){
      this.axios.get('/room')
      .then((response)=>{
        console.log(response.data);
      })
      .catch((error)=>{
        console.error(error)
      })
    },
    logout(){
      if (this.$liff.isLoggedIn()) {
        this.$liff.logout();
      }
    },
    addroom(){
      this.tokens = this.$liff.getIDToken()
      const params = {
        roomname: this.roomname,
        roomtype: this.room_selected,
        token: this.tokens
      };
     this.axios.get('/addroom', {params})
      .then(response => (
        console.log(response.data),
        this.roomlist = response.data))
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
    },
    axiosTest() {
        // create a promise for the axios request
        const promise = this.axios.get('/page/dashboard/usage')

        // using .then, create a new promise which extracts the data
        const dataPromise = promise.then((response) => this.cost=response.data)

        // return it
        return dataPromise
    },
    humidexsuggestion(){
        this.axiosTest()
        .then(data => {
            const params = {
            humi: data.indoorhumi,
            temp: data.indoortemp,
            };
            //humidex suggest
            this.axios.get('humidex/suggest', {params})
            .then(response => (
            this.humidexsuggest = response.data,
            console.log(this.humidexsuggest)
        ))
        })
    }
  },
  async mounted() {
    // เรียกห้องทั้งห้อง (liff)
    /// พยากรอากาศ
    
    await this.axios.get('/weather/daily')
    .then(response=>(
      this.weatherdl = response.data,
      console.log(this.weatherdl)
    )),
   /// อากาศตอนนี้
    await this.axios.get('/weather/hourly')
    .then(response=>(
      this.weatherhl = response.data,
      console.log(this.weatherhl)
    )),
    // ค่าไฟต่อชมตอนนี้
    this.axiosTest()
    // this.axios.get('/page/dashboard/usage')
    // .then(response=>(
    //   this.cost = response.data
    // )),
    
    //ทำนาย
    this.axios.post('/page/dashboard/predict',this.weatherdl).then(response=>(
        this.pred = response.data,
        console.log(this.pred)
    )),

    //Comfort
    this.axios.get('comfort/comfortgraph').then(response=>(
        this.dataSource = response.data,
        console.log(this.dataSource)
    )),

    //humidexsuggest
    this.humidexsuggestion()
    
  }
};
</script>
<style>
.raphael-group-77-label-group text{
  fill: #ffffff!important;
}
.raphael-group-157-label-group text{
  fill: #ffffff!important;
}

.hidden_header {
  display: none;
}
.VueCarousel-dot-container{
  margin-top: 0px!important;
}
.VueCarousel-dot{
  margin-top: 0px!important;
}
hr{
  margin-bottom: 25px;
}
text{
  fill: #ffffff!important;
}
.raphael-group-50-dataset-top-label{
  display: none;
}
.navbar-hidden .app-content {
    padding: 2rem 1rem 0 1rem !important;
}
</style>


