<template>
  <b-container>
    <b-row>
      <b-col cols="2">

        <b-button
          @click="$router.push({ name: 'viewroom' , params:{roomid: info.location , id: info.location}})"
          v-ripple.400="'rgba(113, 102, 240, 0.15)'"
          variant="outline-primary"
          class="btn-icon"
        >
          <feather-icon icon="ChevronLeftIcon" />
        </b-button>
        
      </b-col>
      <b-col>
        <h2 class="text-warning">{{ info.sensor_name }}</h2>
      </b-col>
    </b-row>
    <b-row class="text-center">
      <b-col cols="12">
        <hr />
        <b-button-toolbar justify>
          <b-button-group class="mr-2 mb-1">
           <b-form-radio-group  v-model="value"  button-variant="outline-primary" :options="optionsRadio2" buttons />
          </b-button-group>
        </b-button-toolbar>
      </b-col>
    </b-row>
    <b-row v-if="value == 1">
      <b-col cols="12"><b-form-group>
        <b-form-radio-group id="btn-radios-1" v-model="selected_timeframe" @change="getGraphData()" button-variant="outline-primary" :options="optionsRadio" buttons name="radios-btn-default" />
      </b-form-group>
          <b-row align-v="center">
            <b-col>
              <b-form-group>
        <b-row>
            <b-col cols="9">
                <flat-pickr placeholder="Select Date" v-show="selected_timeframe != 'days'" v-model="date_picker_single" @input="getGraphData()" class="form-control" :config="flat_config_single" />
                <flat-pickr placeholder="Select Date" v-show="selected_timeframe == 'days'" v-model="date_picker_range" @input="getGraphData()" class="form-control" :config="flat_config_range" />
            </b-col>    
        </b-row>
      </b-form-group>
      <div class="col-12">
      <apexchart
        :options="dateinfo.chartOptions"
        :series="dateinfo.series"
      >
      </apexchart>
    </div>
    </b-col>
    <b-col class="col-12">
    <b-card
              border-variant="warning"
              bg-variant="transparent"
              class="shadow-none text-center">
      <h1>Usage</h1>
      <b-table responsive :items="items" thead-class="hidden_header"
      striped
      bordered/>
      </b-card>
      
            </b-col>
          </b-row>
      </b-col>
    </b-row>
    <b-row v-if="value == 2">
      <b-col cols="12">
          <b-row align-v="center">
            <b-col>
              <h1>tab2</h1>
            </b-col>
          </b-row>
      </b-col>
    </b-row>
    <b-row v-if="value == 3">
      <b-col cols="12">
          <b-row align-v="center">
            <b-col>
              <h1>tab3</h1>
            </b-col>
          </b-row>
      </b-col>
    </b-row>
  </b-container>
</template>
<script>
import {
  BCol,
  BRow,
  BButton,
  BCardText,
  BButtonGroup,
  BButtonToolbar,
  BContainer,
  BCard,
  BTable,
  BFormGroup,
  BFormRadioGroup
} from "bootstrap-vue";
import flatPickr from 'vue-flatpickr-component'
import Ripple from 'vue-ripple-directive'
export default {
  components: {
    BCol,
    BRow,
    BButton,
    BCardText,
    BButtonGroup,
    BButtonToolbar,
    BContainer,
    BCard,
    BTable,
    flatPickr,
    BFormGroup,
    BFormRadioGroup,
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
        chartOptions: {
          chart: {
            type: "area",
          },
        },
      },
      selected_timeframe: "day",
      url_params: this.$route.params,
      datepickermode: "single",
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
      value:1,
      optionsRadio2: [
        { text: 'Usage', value: 1},
        { text: 'Mode', value: 2},
        { text: 'Timer', value: 3},
      ],
      items: [],
      info:[],
    };
  },
  methods: {
    settab(tab) {
      this.now_tab = tab;
    },
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

          if (this.selected_timeframe == 'day' || this.selected_timeframe == 'month' || this.selected_timeframe == 'year') {
              this.datepickermode = 'single'
          } else {
              this.datepickermode = 'range'
          }
         
          const params = {
              id: this.$route.params.id, ///id ใน table devicelist
              startdate: tempstart,
              enddate: tempend,
              timeframe: this.selected_timeframe
          }

          this.axios.get("/energyreport/info", { params }).then(response => (
              this.info = response.data
          ))

          this.axios.get("/energyreport/chart", { params }).then(response => (
              this.dateinfo = response.data,
              console.log(response.data)
          ))

          this.axios.get("/energyreport/table", { params }).then((response) => (
              (this.items = response.data),
              console.log(response.data)
          )
        );
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
  mounted() {
      this.getGraphData();
      this.loginLine()
  },
};
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-flatpicker.scss';
</style>
<style >
.hidden_header {
  display: none;
}
.dark-layout .table {
    background-color: transparent!important;
}
.table th,.table td {
    padding: 0.5rem 0.1rem;
  }
</style>
