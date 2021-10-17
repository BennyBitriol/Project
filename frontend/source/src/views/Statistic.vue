<template>
<b-container>
  <b-row v-align="center">
    <b-col cols="2">
      <b-button
        @click="$router.push({ name: 'viewroom'})"
        v-ripple.400="'rgba(113, 102, 240, 0.15)'"
        variant="outline-primary"
        class="btn-icon">
        <feather-icon icon="ChevronLeftIcon" />
      </b-button>
    </b-col>
    <b-col cols="10">
      <h3 class=" text-left text-light">Room Usage History</h3>
    </b-col>
  </b-row>
  <b-row>
        <b-col class="col-12">
          <hr>
          <carousel :perPage="1" paginationActiveColor="#ff9f43">
                <slide>
                    <b-form-group>
                      <b-form-radio-group id="btn-radios-1" v-model="selected_timeframe" @change="getGraphData()" button-variant="outline-primary" :options="optionsRadio" buttons name="radios-btn-default" />
                    </b-form-group>
                  
              
            <b-row>
                <b-col cols="12">
                    <flat-pickr placeholder="Select Date" v-show="selected_timeframe != 'days'" v-model="date_picker_single" @input="getGraphData()" class="form-control" :config="flat_config_single" />
                    <flat-pickr placeholder="Select Date" v-show="selected_timeframe == 'days'" v-model="date_picker_range" @input="getGraphData()" class="form-control" :config="flat_config_range" />
                </b-col>    
            </b-row>
         
        <div class="col-12">
          <apexchart
            :options="dateinfo.chartOptions"
            :series="dateinfo.series"
          >
          </apexchart>
        </div>
            </slide>
                <slide>
                <b-card
                  border-variant="warning"
                  bg-variant="transparent"
                  class="shadow-none text-center">
                  <h1 class="text-light">อุปกรณ์ในห้อง</h1>
                  <b-table responsive :items="device_list" thead-class="hidden_header"
                  striped
                  bordered/>
                </b-card>
                </slide>
                <slide>
                <b-card
                  border-variant="warning"
                  bg-variant="transparent"
                  class="shadow-none text-center">
                  <h1 class="text-light">สถิติการใช้งาน</h1>
                  <b-table responsive :items="statistic" thead-class="hidden_header"
                  striped
                  bordered/>
                </b-card>
                </slide>
              </carousel>
        </b-col>
        {{this.$route.params}}
  </b-row>
</b-container>
</template>

<script>
import { BFormRadioGroup , BTabs, BTab , BRow, BCol, BFormGroup , BButton , BTable,BCard } from 'bootstrap-vue'
import flatPickr from 'vue-flatpickr-component'
import Ripple from 'vue-ripple-directive'
import { Carousel, Slide } from 'vue-carousel';
export default {
    components:{
      BTabs,
      BTab,
      BRow,
      BCol,
      flatPickr,
      BFormGroup,
      BButton,
      BFormRadioGroup,
      BTable,
      BCard,
      Carousel, 
      Slide
    },
    directives: {
    Ripple,
  },
    data() {
    return {
      flat_config_range: {
        dateFormat: "d-m-Y",
        mode: "range",
        disableMobile: true
      }, 
      flat_config_single: {
        dateFormat: "d-m-Y",
        mode: "single",
        disableMobile: true
      },
       date_picker_single: new Date(),//'3/4/2021',//new Date().toLocaleDateString(),
      date_picker_range: [new Date(Date.now()-(86400000*7)).toLocaleDateString(['ban', 'id']).substring(0,10),new Date().toLocaleDateString(['ban', 'id'])],
        dateinfo: {
          chartOptions:{
            chart:{
              type:'area'
            }
          }
        },
        selected_timeframe: 'day',
        url_params:this.$route.params,
        datepickermode: 'single',
        selectedRadio: 'active',
        optionsRadio: [
          { text: 'Day', value: 'day' },
          // { text: 'Days', value: 'days' },
          { text: 'Month', value: 'month' },
          { text: 'Year', value: 'year' },
        ],
      device_list:[
        {
          a: 'อุปกรณ์',
          b: 'ค่าไฟ/บาท',
        },
        {
          a: 'แอร์',
          b: '222 บาท',
        },
        {
          a: 'พัดลม', 
          b: '333 บาท', 
        },
        {
          a: 'คอมพิวเตอร์', 
          b: '444 บาท', 
        },        
      ],
      statistic: [
        {
          a: 'วันที่ใช้ไฟมากที่สุด',
          b: 'ไม่รู้',
        },
        {
          a: 'ค่าไฟห้องเฉลี่ย', 
          b: '333 บาท', 
        },
        {
          a: 'ประมาณค่าไฟ', 
          b: '444 บาท', 
        },
        {
          a: 'การใช้ไฟคิดเป็นกี่ % ของทั้งหลัง', 
          b: '30 %', 
        },
        {
          a: 'อุปกรณ์ที่ใช้พลังงานมากที่สุด', 
          b: 'แอร์', 
        },
        {
          a: 'อุปกรณ์ที่ใช้งานมากที่สุด',
          b: 'คอมพิวเตอร์'
        },
        {
          a: 'อุณหภูมิสูงสุด',
          b: '35 C',
        },
        {
          a: 'อุณหภูมิต่ำสุด', 
          b: '30 C', 
        },
        {
          a: 'อุณหภูมิเฉลี่ย', 
          b: '33 C', 
        },
        {
          a: 'ความชื้นสูงสุด',
          b: '70 %',
        },
        {
          a: 'ความชื้นต่ำสุด', 
          b: '50 %', 
        },
        {
          a: 'ความชื้นเฉลี่ย', 
          b: '60 %', 
        },
        

      ]
    }
    },
    methods: {
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
        // id: this.$route.params.id, //id ส่งไปกราฟ   
        startdate: tempstart,
        enddate: tempend,
        timeframe: this.selected_timeframe,
        type: this.url_params.type,
      };
      // console.log(tempstart);
      if (this.selected_timeframe == "day" || this.selected_timeframe == "month" || this.selected_timeframe == "year") {
          this.datepickermode = "single";
      } 
      else {
          this.datepickermode = "range";
      }
      console.log({ params });
      this.axios.get("/room/usage/"+this.$route.params.id, { params }).then(response => (
          this.dateinfo = response.data,
          console.log(response.data)
      ))
       
    }
    },
    mounted(){
      this.getGraphData();
    },
}
</script>
<style lang="scss">
@import "@core/scss/vue/libs/vue-flatpicker.scss";
</style>
<style>
.hidden_header {
  display: none;
}

.dark-layout .table {
  background-color: transparent !important;
}

.table th,
.table td {
  padding: 0.5rem 0.1rem;
}
</style>