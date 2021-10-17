<template>
    <b-row>
        <b-col cols="2">
            <!-- <div class="demo-inline-spacing">
            <b-button variant="gradient-primary">Day</b-button>
            <b-button variant="gradient-primary">Days</b-button>
            <b-button variant="gradient-primary">Month</b-button>
            <b-button variant="gradient-primary">Year</b-button>
            </div> -->
            <b-button @click="$router.push({ name: 'mainpage'})" v-ripple.400="'rgba(113, 102, 240, 0.15)'" variant="outline-primary" class="btn-icon">
                <feather-icon icon="ChevronLeftIcon" />
            </b-button>
        </b-col>
        <b-col>
          <h1 v-if="url_params.type == 'home'" class="text-warning">My Home</h1>
        </b-col>
        <b-col cols="12">
          <hr>
          <h2 class="text-warning">Weather Detail</h2>
        </b-col>
        <b-col>
            <b-form-group>
                <b-row>
                    <b-col cols="9">
                    <flat-pickr placeholder="Select Date"  v-model="date_picker_single" @input="getGraphData()" class="form-control" :config="flat_config_single" />
                    
                    </b-col>
                    <!-- <b-col cols="3">
                        <b-button @click="getGraphData()" >GO</b-button>
                    </b-col> -->
                </b-row>
            </b-form-group>
            <apexchart :options="dateinfo.chartOptions" :series="dateinfo.series">
            </apexchart>
        </b-col>
        <b-col class="col-12">
            <b-card border-variant="warning" bg-variant="transparent" class="shadow-none text-center">
                <h1>Weather Detail</h1>
                <b-table responsive :items="items" thead-class="hidden_header" striped bordered />
            </b-card>
        </b-col>
        {{this.$route.params}}
    </b-row>
</template>
<script>
import { BFormRadioGroup, BTabs, BTab, BRow, BCol, BFormGroup, BButton, BTable, BCard } from 'bootstrap-vue'
import flatPickr from 'vue-flatpickr-component'
import Ripple from 'vue-ripple-directive'

export default {
    components: {
        BTabs,
        BTab,
        BRow,
        BCol,
        flatPickr,
        BFormGroup,
        BButton,
        BFormRadioGroup,
        BTable,
        BCard
    },
    directives: {
      Ripple,
    },
    data() {
      return {
        date_picker_single: new Date().toLocaleDateString(),
        date_picker_range: new Date().toLocaleDateString(),
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
        flat_config_range: { 
          dateFormat: 'd-m-Y',
          mode: 'range',
        },
        flat_config_single: { 
          dateFormat: 'd-m-Y',
          mode: 'single',
        },
        optionsRadio: [
          { text: 'Day', value: 'day' },
          { text: 'Days', value: 'days' },
          { text: 'Month', value: 'month' },
          { text: 'Year', value: 'year' },
        ],
        items: [
          {
            a: 'อุณหภูมิ',
            b: 'ไม่รู้',
          },
          {
            a: 'ค่าไฟห้องเฉลี่ยวันละ', 
            b: '3 บาท', 
          },
          {
            a: 'อุปกรณ์ที่ใช้พลังงานมากที่สุด', 
            b: '3 บาท', 
          },
          {
            a: 'ค่าไฟอาทิตย์นี้',
            b: '3 บาท'
          },
          {
            a: 'ค่าไฟอาทิตย์ที่แล้ว',
            b: '3 บาท',
          },
          {
            a: 'ค่าไฟเดือนนี้', 
            b: '3 บาท', 
          },
          {
            a: 'ค่าไฟเดือนที่แล้ว', 
            b: '3 บาท', 
          }
        ]
      }
    },
    methods: {
        getGraphData(){
          var tempstart = ''
          var tempend = ''
          var temp_date = ''
          if(this.selected_timeframe == 'days'){
            temp_date = this.date_picker_range
            temp_date = temp_date.toString().split(" to ");
            tempstart = temp_date[0]
            tempend = temp_date[1]
          }else{
            tempstart = this.date_picker_single
            tempend = tempstart
          }
         
          const params = {
              id: 16,
              startdate: tempstart,
              enddate: tempend,
              timeframe: this.selected_timeframe
          }

          this.axios.get("/sensor/single/statistic", { params }).then(response => (
              this.dateinfo = response.data,
              console.log(response.data)
          ))
          if (this.selected_timeframe == 'day' || this.selected_timeframe == 'month' || this.selected_timeframe == 'year') {
              this.datepickermode = 'single'
          } else {
              this.datepickermode = 'range'
          }

        },
        loginLine() {
            if(this.authenticated == null){
                this.loginLineaction().then((resp) => {
                if(resp.data.url){
                    window.location.href = resp.data.url
                }
                })
            }
            
        }
    },
    mounted(){
      this.getGraphData();
      this.loginLine()
    },

}
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-flatpicker.scss';
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