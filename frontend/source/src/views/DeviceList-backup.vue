<template>
  <b-container>
    <b-row align-h="between">
      <b-col cols="1">
        <b-button
          v-ripple.400="'rgba(113, 102, 240, 0.15)'"
          variant="outline-primary"
          class="btn-icon">
          <feather-icon icon="ChevronLeftIcon" />
        </b-button>
      </b-col>
      <b-col cols="6">
        <h1 class="text-warning"><b>ห้องนอน</b></h1>
      </b-col>
      <b-col cols="4">
        <div class="text-right">
          <b-dropdown
          v-ripple.400="'rgba(113, 102, 240, 0.15)'"
          text="เปลี่ยนห้อง"
          variant="outline-warning">
            <b-dropdown-item>
              ห้องน้ำ
            </b-dropdown-item>
            <b-dropdown-item>
              ห้องครัว
            </b-dropdown-item>
          </b-dropdown>          
        </div>

      </b-col>

    </b-row>
      <hr>
    <b-row>
      <b-col cols="4">
        <img src="https://www.gadgetbuck.com/img/icons/room/room-white-02.png" height="100" width="100"/>
        </b-col>
        <b-col align-self="end" cols="4" >
          <h1 class="display-4 text-right text-light" >25°</h1>
          <h3 class="text-right text-light" >อุณหภูมิ</h3>
        </b-col>
        <b-col align-self="end" cols="4">
          <h1 class="display-4 text-left text-light" >55%</h1>
          <h3 class="text-left text-light" >ความชื้น</h3>
        </b-col>
        <b-col><hr></b-col>
    </b-row>
      <div class="padding-10"></div>
       <b-row align-h="center">
         <b-col cols="12">
          <div class="big">
            <b-button-toolbar justify>
              <b-button-group
                class="mr-2 mb-1">
                <b-button
                  @click="settab(1)"
                  v-ripple.400="'rgba(113, 102, 240, 0.15)'"
                  variant="outline-primary"
                >
                  <feather-icon icon="SunIcon" />Light
                </b-button>
                <b-button
                  @click="settab(2)"
                  v-ripple.400="'rgba(113, 102, 240, 0.15)'"
                  variant="outline-primary"
                >
                  <feather-icon icon="MonitorIcon" />Devices

                </b-button>
                <b-button
                  @click="settab(3)"
                  v-ripple.400="'rgba(113, 102, 240, 0.15)'"
                  variant="outline-primary"
                >
                  <feather-icon icon="ActivityIcon" />Usage
                </b-button>
                <b-button
                  @click="settab(4)"
                  v-ripple.400="'rgba(113, 102, 240, 0.15)'"
                  variant="outline-primary"
                >
                  <feather-icon icon="ThermometerIcon" />Temp

                </b-button>
              </b-button-group>
            </b-button-toolbar>
          </div>

      </b-col>
        </b-row>
        <b-col>
          <hr>
        </b-col>
      <b-row v-if="now_tab==1"> 
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
                <b-col cols="5">
                  <RockerSwitch :value="dev.switch_status" @change="isOn => light_update(dev.id,currentValue = isOn)"  borderColor="#283046" :size="1"/>
                </b-col>
              </b-row>
              </b-card>
            </b-col>
      </b-row>
      <b-row v-if="now_tab==2">
        <b-col cols="12"  v-for="(dev) in devicelist" :key="dev.id">
              <b-card
              border-variant="warning"
              :bg-variant="dev.bg_variant"
              class="shadow-none text-center">
              <b-row align-v="center">
                  <b-col cols="3" >
                    <img v-bind:src="dev.icon" height="60" width="60">
                  </b-col>
                  <b-col cols="5" class="text-left">
                    <h5 class="text-light">{{dev.sensor_name}}</h5>
                  </b-col>
                  <b-col cols="4">
                  <RockerSwitch :value="dev.switch_status" @change="isOn => device_update(dev.id,currentValue = isOn)"  borderColor="#283046" :size="0.9"/>
                </b-col>
                  </b-row>
              </b-card>
            </b-col>
      </b-row>
      <b-row v-if="now_tab==3">
        <b-col cols="6">
          <h1 class="text-warning">การใช้ไฟฟ้า</h1>          
        </b-col>
        <b-col cols="6">
          <div class="text-right">
            <b-button
                class="btn"
                variant="outline-primary">
              ดูการใช้งานย้อนหลัง
            </b-button>
          </div>
        </b-col>

        <b-col cols="12">
           <div class="padding-10"></div>
          <b-row>
            <b-col cols="12">
              <b-card
              border-variant="warning"
              bg-variant="transparent"
              class="shadow-none text-center">
                <div class="row">
                  <div class="col-6">
                    การใช้ไฟฟ้าในห้องตอนนี้ 
                  </div>
                  <div class="col-6">
                    3456 KWH
                  </div>
                </div>
                <div class="row padding-10">
                  <div class="col-6">
                    จ่ำนวนอุปกรณ์ที่เปิดอยู่
                  </div>
                  <div class="col-6">
                    <b-badge variant="warning">
                      69 อุปกรณ์
                    </b-badge>
                  </div>
                </div>
                <div class="row padding-10">
                  <div class="col-6 border-top border-bottom border-right d-flex align-items-between flex-column py-1">
                    <h1>78 ฿/ชั่วโมง</h1>
                    การใช้ไฟตอนนี้
                  </div>
                  <div class="col-6 border-top border-bottom d-flex align-items-between flex-column py-1">
                    <h1>5678 ฿</h1>
                    ค่าไฟในห้องวันนี้
                  </div>
                </div>

                <div class="row padding-10">
                  <div class="col-6">
                    วันที่ใช้ไฟเยอะที่สุดในเดือน
                  </div>
                  <div class="col-6">
                    ไม่รุ
                  </div>
                </div>
                <div class="row padding-10">
                  <div class="col-6">
                    ค่าไฟห้องเฉลี่ยวันละ
                  </div>
                  <div class="col-6">
                    3 บาท
                  </div>
                </div>
                <div class="row padding-10">
                  <div class="col-6">
                  อุปกรณ์ที่ใช้พลังงานเยอะที่สุด
                  </div>
                  <div class="col-6">
                    3 บาท
                  </div>
                </div>
                <div class="row padding-10">
                  <div class="col-6">
                  ค่าไฟอาทิตย์นี้
                  </div>
                  <div class="col-6">
                    3 บาท
                  </div>
                </div>
                <div class="row padding-10">
                  <div class="col-6">
                  ค่าไฟอาทิตย์ที่แล้ว
                  </div>
                  <div class="col-6">
                    3 บาท
                  </div>
                </div>
                <div class="row padding-10">
                  <div class="col-6">
                  ค่าไฟเดือนนี้
                  </div>
                  <div class="col-6">
                    3 บาท
                  </div>
                </div>
                <div class="row padding-10">
                  <div class="col-6">
                  ค่าไฟเดือนที่แล้ว
                  </div>
                  <div class="col-6">
                    3 บาท
                  </div>
                </div>
              </b-card>
            </b-col>
          </b-row>
        </b-col>
      </b-row>
      <b-row v-if="now_tab==4">
        <b-col>
          <h1>Tab 4</h1>
        </b-col>
      </b-row>
      
  </b-container>
</template>

<script>
import { BBadge,BDropdown,BDropdownItem,BButton,BTabs, BTab,BImg,BCard , BCol ,BCardBody, BCardText, BRow ,BButtonToolbar, BButtonGroup,BContainer} from 'bootstrap-vue'
// Import package
import RockerSwitch from "vue-rocker-switch";
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
    BDropdownItem
    
    // Lightbulb
  },
  directives: {
    Ripple,
  },
  data() {
    return {
      now_tab:3,
      currentValue: [],
      lightlist:[],
      devicelist:[],
      cardstyle:"transparent"
    };
  },
  methods:{
    light_update(id,value){
      this.axios.get('devicelist/change/'+id+'/'+value).then(response => (
      this.lightlist = response.data)
    )},
    device_update(id,value){
      this.axios.get('devicelist/change/'+id+'/'+value).then(response => (
      this.devicelist = response.data)
    )},
    settab(tab){
      this.now_tab = tab
    },
  },
  mounted() {
    this.axios.get('devicelist/room/4/light')
      .then(response => (
        this.lightlist = response.data))
    this.axios.get('devicelist/room/4/device')
      .then(response => (
        this.devicelist = response.data))
  }
};
</script>

<style>
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
</style>
