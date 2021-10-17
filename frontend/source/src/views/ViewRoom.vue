<template>
  <b-container class="px-0">
    <b-row align-v="center">
      <b-col cols="1">
        <b-button
          @click="$router.push({ name: 'mainpage'})"
          v-ripple.400="'rgba(113, 102, 240, 0.15)'"
          variant="outline-primary"
          class="btn-icon">
          <feather-icon icon="ChevronLeftIcon" />
        </b-button>
      </b-col>

      <b-col cols="6">
        <h3 class="text-warning text-center"><b>{{roomdetail.statistic}}</b></h3>

      </b-col>
      <b-col cols="5">
        <div class="text-right">
          <b-dropdown right
          v-ripple.400="'rgba(113, 102, 240, 0.15)'"
          text="เปลี่ยนห้อง"
          variant="outline-warning">
            <b-dropdown-item  v-for="room in roomlist" :key="room.id">
              <div @click="changeroom(room.id)">
              <img v-bind:src="room.icon" height="20" width="20">
              {{room.statistic}}
              </div>
            </b-dropdown-item>
          </b-dropdown>          
        </div>
      </b-col>
      <b-col cols="12">
        <hr>
      </b-col>
    </b-row>
      
    <b-row>
      <hr>
      <b-col cols="4">
        <img v-bind:src="roomdetail.icon" height="100" width="100"/>
        </b-col>
        <b-col align-self="end" cols="4" >
          <h1 class="display-4 text-right text-light" >{{roomtemphumid.indoortemp}}°</h1>
          <h3 class="text-right text-light" >อุณหภูมิ</h3>
        </b-col>
        <b-col align-self="end" cols="4">
          <h1 class="display-4 text-left text-light" >{{roomtemphumid.indoorhumi}}%</h1>
          <h3 class="text-left text-light" >ความชื้น</h3>
        </b-col>
        
    </b-row>
    <b-row>
      <b-col cols="12"><hr></b-col>
    </b-row>
    <b-row>
      <b-col cols="12">
        <b-card
       
        bg-variant="transparent"
        class="shadow-none">
        <b-card-text>
         
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
    </b-row>
    <b-row align-h="center">
        <b-button-toolbar class="t" justify>
          <b-button-group>
            <b-form-radio-group size="sm" id="btn-radios-1" v-model="value"  button-variant="outline-primary" :options="optionsRadio" buttons name="radios-btn-default" />
          </b-button-group>
        </b-button-toolbar>
    </b-row>
    <b-row>
      <b-col>
        <hr>
      </b-col>
    </b-row>

    <b-row v-if="value==1"> 
      <b-col cols="12"  v-for="(dev) in lightlist" :key="dev.id">
        <b-card
        border-variant="warning"
        :bg-variant="dev.bg_variant"
        class="shadow-none text-center">
          <b-row align-v="center">
            <b-col cols="7" >
              <b-row align-v="center">
              <b-col cols="4" >
                <img v-bind:src="dev.icon" height="60" width="60">
              </b-col>
              <b-col cols="8" class="text-left">
                <h5 class="text-light">{{dev.sensor_name}}</h5>
              </b-col>
              </b-row>
            </b-col>
            <b-col cols="5" class="text-right">
              <RockerSwitch :value="dev.switch_status" @change="isOn => light_update(dev.id,currentValue = isOn)"  borderColor="#283046" :size="1"/>
            </b-col>
          </b-row>
        </b-card>
      </b-col>
    </b-row>


    <b-row v-if="value==2">
      <b-col cols="12" >
        <!-- devicelist ทั้งหมดยกเว้น air -->
        <div  v-for="(dev) in devicelist" :key="dev.id"> 
        <b-card
          v-if="dev.devicetype != 'air'"
          border-variant="warning"
          :bg-variant="dev.bg_variant"
          class="shadow-none text-center"
          >
          <b-row align-v="center">
            <b-col cols="3" >
              <img v-bind:src="dev.icon" height="60" width="60">
            </b-col>
            <b-col cols="5" class="text-left">
              <h5 class="text-light">{{dev.sensor_name}}</h5>
            </b-col>
            <b-col cols="4" class="text-right">
              <RockerSwitch :value="dev.switch_status" @change="isOn => device_update(dev.id,currentValue = isOn)"  borderColor="#283046" :size="0.9"/>
            </b-col>
          </b-row>
          <b-row class="text-center mx-0 mt-2">
            <b-col
              cols="12"
              class="border-top d-flex align-items-between flex-column py-1">
            
                <b-button
                 @click="gotodevicestat($route.query.id,dev.id)"
                 size="sm"
                 class="btn"
                 :variant="dev.bt_variant">Details</b-button>
              
              <h3 class="font-weight-bolder mb-0">
            
              </h3>
            </b-col>
          </b-row>
        </b-card> 
        </div>

        <!-- devicelist ที่เป็น air -->
        <div v-for="(devi) in devicelistair" :key="devi.id">
        <b-card
          border-variant="warning"
          
          class="shadow-none text-center"
          >
          <b-row align-v="center">
            <b-col cols="3" >
              <img v-bind:src="devi.icon" height="60" width="60">
            </b-col>
            <b-col cols="5" class="text-left">
              <h5 class="text-light">{{devi.sensor_name}}</h5>
            </b-col>
            <b-col cols="4" class="text-right">
              <!-- <RockerSwitch :value="devi.switch_status" @change="isOn => device_update(devi.id,currentValue = isOn)"  borderColor="#283046" :size="0.9"/> -->
              <b-button
              :class="btncoloron"
              variant="outline-primary"
              @click="turnonair($route.query.id)" size="sm">On</b-button>
              <b-button 
              :class="btncoloroff"
              variant="outline-primary"
              @click="turnoffair($route.query.id)" size="sm">Off</b-button>
            </b-col>
          </b-row>
          <b-row class="text-center mx-0 mt-2">
            <b-col
              cols="12"
              class="border-top d-flex align-items-between flex-column py-1">
              
                <b-button
                 @click="gotodevicestat($route.query.id,devi.id)"
                 size="sm"
                 class="btn"
                 variant="outline-warning">Details</b-button>
              
              <h3 class="font-weight-bolder mb-0">
            
              </h3>
            </b-col>
          </b-row>
        </b-card>
        </div>
      </b-col>
    </b-row>
      <b-row v-if="value==3">
        <b-col cols="7" class="text-left">
          <b-card
            bg-variant="transparent"
            class="shadow-none mb-0">
            <h3 class="text-warning">Room Usage</h3>
          </b-card>       
        </b-col>
        <b-col cols="5">
          <div class="text-right">
            <b-button
                @click="gotoroomstat($route.query.id)"
                class="btn"
                variant="outline-primary">
              History Usage
            </b-button>
          </div>
        </b-col>
        <b-col cols="12">
          <b-row>
            <b-col cols="12">
              <br>
              <b-card
              border-variant="warning"
              bg-variant="transparent"
              class="shadow-none text-center">
                <div class="row">
                  <div class="col-8">
                    <div class="padding-10"></div>
                    <h5 class="text-light">Consumption</h5>
                  </div>
                  <div class="col-4">
                    <div class="padding-10"></div>
                    <h5 class="text-light">{{roomusage.watt}} W</h5>
                  </div>
                </div>
                <div class="row padding-10">
                  <div class="col-8">
                    <h5 class="text-light">Devices ON</h5>
                  </div>
                  <div class="col-4">
                    <b-badge variant="success" >
                      {{roomusage.on}} Device
                    </b-badge>
                  </div>
                </div>
                <div class="row padding-10">
                  <div class="col-6 border-top border-bottom border-right d-flex align-items-between flex-column py-1">
                    <h3 class="text-light">{{roomusage.cost_h}}฿/Hour</h3>
                    <h5 class="text-light">Now Usage</h5>
                  </div>
                  <div class="col-6 border-top border-bottom d-flex align-items-between flex-column py-1">
                    <h3 class="text-light">{{roomusage.cost_today}}฿</h3>
                    <h5 class="text-light">Today Usage</h5>
                  </div>
                </div>
                <div class="padding-10">
                <b-table responsive :items="roomusage.item" thead-class="hidden_header"
                striped
                bordered/>
                </div>
                <div class="padding-10"></div>
               
              </b-card>
            </b-col>
          </b-row>
        </b-col>
      </b-row>
      <!-- <b-row v-if="value==4">
        <b-col  class="col-12">
            <b-form-group >
                <b-row align-h="center">
                  
                 
                    <b-button-toolbar class="my-1" justify>
                      <b-button-group>
                        <b-form-radio-group size="sm" id="btn-radios-1" v-model="selected_timeframe" @change="getGraphData()" button-variant="outline-primary" :options="optionsRadio2" buttons name="radios-btn-default" /> 
                      </b-button-group>
                    </b-button-toolbar>
                  
                    <b-col cols="9">
                    <flat-pickr placeholder="Select Date" v-if="selected_timeframe != 'days'" v-model="date_picker_single" @input="getGraphData()" class="form-control"  :config="flat_config_single" />
                    </b-col>
                </b-row>
            </b-form-group>
            <apexchart :options="dateinfo.chartOptions" :series="dateinfo.series">
            </apexchart>
        </b-col>
      </b-row> -->
      
  </b-container>
</template>

<script>
import { BBadge,BDropdown,BDropdownItem,BButton,BTabs, BTab,BImg,BCard , BCol ,BCardBody, BCardText, BRow ,BButtonToolbar, BButtonGroup,BContainer,BTable,BFormRadioGroup,BFormGroup} from 'bootstrap-vue'
// Import package
import RockerSwitch from "vue-rocker-switch";
import flatPickr from "vue-flatpickr-component";
// Import styles
import "vue-rocker-switch/dist/vue-rocker-switch.css";
import Ripple from 'vue-ripple-directive'
export default {
  components: {
    BBadge,
    RockerSwitch,
    BButton,
    BCard,
    BImg,
    BCardText,
    BCardBody,
    BCol,
    BRow,
    BTabs,
    BTab,
    BButtonToolbar,
    BButtonGroup,
    BContainer,
    BDropdown,
    BDropdownItem,
    BTable,
    BFormRadioGroup,
    flatPickr,
    BFormGroup
  },
  directives: {
    Ripple,
  },
  data() {
    return {
      btncoloron:'btn-block',
      btncoloroff:'btn-block',
      airvalueon:'',
      airvalueoff:'',
      devicelistair:[],
      humidexsuggest:'',
      cost:[],
      tokens:'',
      // now_tab:1,
      dataSource:[],
      currentValue: [],
      lightlist:[],
      devicelist:[],
      roomlist:[],
      roomdetail:[],
      roomusage:[],
      roomtemphumid:[],
      flat_config_single: {
        dateFormat: "d-m-Y",
        mode: "single",
        disableMobile: true
      },
      date_picker_single: new Date(),//'3/4/2021',//new Date().toLocaleDateString(),
      date_picker_range: [new Date(Date.now()-(86400000*7)).toLocaleDateString(['ban', 'id']).substring(0,10),new Date().toLocaleDateString(['ban', 'id'])],
      dateinfo: {
        chartOptions: {
          chart: {
            type: "area",
          },
        },
      },
      selected_timeframe: "day",
      url_params: this.$route.params,
      datepickermode: "single",
      cardstyle:"transparent",
      value: 1,
      optionsRadio: [
        { text: "Light", value: 1 },
        { text: "Devices", value: 2 },
        { text: "Usage", value: 3 },
        // { text: "Temp", value: 4 },
      ],
      optionsRadio2: [
        { text: "Day", value: "day" },
        { text: "Month", value: "month" },
        { text: "Year", value: "year" },
      ],
    };
  },
  // async beforeCreate() {
  //   // await this.$liff.init({liffId: "1655594666-27kj0QOQ" })
  //   // if (this.$liff.isLoggedIn()){
  //   //     this.getuserroom()
  //   //     this.$liff.getUserProfile()
  //   //     this.xxx =this.$liff.liff.getOS()
  //   // }
  //   // else{
  //   //     this.$liff.login()
  //   // }

  // },
  methods:{
     getGraphData() {
      var tempstart = "";
      var tempend = "";
      var temp_date = "";
      if (this.selected_timeframe == "days") {
        temp_date = this.date_picker_range;
        temp_date = temp_date.toString().split(" to ");
        tempstart = temp_date[0];
        tempend = temp_date[1];
      } else {
        tempstart = this.date_picker_single;
        console.log(this.date_picker_single);
        tempend = tempstart;
      }

      const params = {
        // id: 1, //id ส่งไปกราฟ   
        startdate: tempstart,
        enddate: tempend,
        timeframe: this.selected_timeframe,
        // type: this.url_params.type,
      };
      // console.log(tempstart);
      if (this.selected_timeframe == "day" || this.selected_timeframe == "month" || this.selected_timeframe == "year") {
          this.datepickermode = "single";
      } 
      else {
          this.datepickermode = "range";
      }
      
      this.axios.get("/room/temp/"+this.$route.params.id, { params }).then(response => (
          this.dateinfo = response.data
      ))
       
    },
    light_update(id,value){
      this.axios.get('devicelist/change/'+id+'/'+value).then(response => (
      this.lightlist = response.data)
    )},
    device_update(id,value){
      this.axios.get('devicelist/change/'+id+'/'+value).then(response => (
      this.devicelist = response.data)
    )},
    turnonair(id){
        this.airvalueon = ''
        this.$bvModal.msgBoxConfirm('Are you sure?')
          .then(
              this.btncoloron = 'btn-block btn-primary',
              this.btncoloroff = 'btn-block',
              this.axios.get('/remoteon/'+id+'/true')
            )
    },
    turnoffair(id){
        this.$bvModal.msgBoxConfirm('Are you sure?')
          .then(
              this.btncoloroff = 'btn-block btn-primary',
              this.btncoloron = 'btn-block',
              this.axios.get('/remoteoff/'+id+'/true')
          )
    },
    changeroom(roomid){
      this.$router.push({ name: 'viewroom', query: {  clickfrom: 'viewroom', room_id:roomid ,  id: roomid ,token:this.$route.query.token  } })
      .then(location.reload()) 
    },
    gotoroomstat(roomid){
      this.$router.push({name: 'energyreport', params:{  clickfrom: 'viewroom', id:roomid , type:'room', token:this.$route.query.token   }})
      //this.$router.push({ name: 'statistic', params: {  clickfrom: 'viewroom', room_id: roomid ,  id: roomid , type: 'room'  } })
    },
    gotodevicestat(room_id,dev_id){
      this.$router.push({ name: 'energyreport', params: {  clickfrom: 'viewroom', id: room_id ,  dev_id: dev_id , type: 'sensor', token:this.$route.query.token  }})
    },
    axiosTest() {
      if(this.$route.query.clickfrom == 'mainpage'){
          // create a promise for the axios request
          const promise = this.axios.get('room/devicelist/'+this.$route.query.id)
          // using .then, create a new promise which extracts the data
          const dataPromise = promise.then((response) => this.roomtemphumid=response.data)

          // return it
          return dataPromise
      }
      else{
          // create a promise for the axios request
          const promise = this.axios.get('/page/dashboard/usage')
          // using .then, create a new promise which extracts the data
          const dataPromise = promise.then((response) => this.cost=response.data)

          // return it
          return dataPromise
      }
        
    },
    humidexsuggestion(){
        this.axiosTest()
        .then(data => {
            const params = {
            humi: data.indoorhumi,
            temp: data.indoortemp,
            };
            console.log(params);
            //humidex suggest
            this.axios.get('humidex/suggest', {params})
            .then(response => (
            this.humidexsuggest = response.data,
            console.log(this.humidexsuggest)
        ))
        })
    },

    loadpage(){

      ///ห้องทั้งหมดเพื่อทำเปลี่ยนห้อง
      ///comfort meter
      /// ไฟทั้งหมด light
      /// device ทั้งหมด
      /// ข้อมูลห้อง

      this.axios.get('/room/', { params: { token: this.$route.query.token } })
        .then(response => (
          this.roomlist = response.data))

      this.axios.get('comfort/predict/'+this.$route.query.id,{params:{token:this.$route.query.token}} ).then(response => (
          this.dataSource = response.data,
          console.log("dataSource="+this.dataSource)))

      this.axios.get('devicelist/room/'+this.$route.query.id+'/light/light')
          .then(response => (
            this.lightlist = response.data))

      this.axios.get('devicelist/room/'+this.$route.query.id+'/device/device')
          .then(response => (
            this.devicelist = response.data))

      this.axios.get('devicelist/room/'+this.$route.query.id+'/air/air')
          .then(response => (
            this.devicelistair = response.data))      

      this.axios.get('/room/'+this.$route.query.id )
          .then(response => (
            this.roomdetail = response.data))
      
      this.axios.get('room/devicelist/'+this.$route.query.id)
        .then(response => (
          this.roomtemphumid = response.data))
      
      this.axios.get('energyreport/roomusage/'+this.$route.query.id)
        .then(response => (
          this.roomusage = response.data))
      
      this.axiosTest()

      this.humidexsuggestion()
    }
  },
  mounted() {
    console.log(this.$route.query.id)
    console.log(this.$route.query.token)
    this.loadpage()
    
  },
  // beforeCreate() {
  //   this.$liff.init()
  // } 
}
</script>
<style lang="scss">
@import "@core/scss/vue/libs/vue-flatpicker.scss";
</style>
<style>
.hidden_header {
  display: none;
}
  .padding-10 {
    padding-top: 10px!important;
  }
  .big .feather{
    width:35px;
    height:35px;
  }
  .card-body {
    padding: 0.7rem;
  }
  .dark-layout .table {
    background-color: transparent!important;
  }
  .table th,.table td {
    padding: 0.5rem 0.1rem;
  }
  text{
  fill: #ffffff!important;
  }
  .navbar-hidden .app-content {
    padding: 2rem 1rem 0 1rem !important;
}
</style>
