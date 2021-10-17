<template>
<b-container fluid>
  <b-row>
    <b-col class="col-2">
      <b-button
            @click="$router.go(-1)"
            v-ripple.400="'rgba(113, 102, 240, 0.15)'"
            variant="outline-primary"
            class="btn-icon"
          ><feather-icon icon="ChevronLeftIcon" />
      </b-button> 
    </b-col>
    <b-col class="col-10">
      <h2 class="text-warning text-center">{{ table_items[1].name }}</h2>
    </b-col>
  </b-row>
  <b-row>
    <b-col class="col-12">
      <hr>
    </b-col>
  </b-row>
  
  <b-row  align-v="center">
    <b-col class="col-12">
      <flat-pickr size="sm" placeholder="Select Date" v-if="selected_timeframe != 'days'" v-model="date_picker_single" @input="getGraphData()" class="form-control"  :config="flat_config_single" />
      <flat-pickr placeholder="Select Date" v-if="selected_timeframe == 'days'" v-model="date_picker_range" @input="getGraphData()" class="form-control" :config="flat_config_range" />
    </b-col>
  </b-row>
  <b-row class="pt-1 pb-0" align-v="center">
    <b-col class="col-12  text-center" >
      <b-form-radio-group id="btn-radios-1" inline="true" size="sm" v-model="selected_timeframe" @change="getGraphData()" button-variant="outline-primary" :options="optionsRadio" buttons name="radios-btn-default" /> 
    </b-col>
  </b-row>
  <b-row>
    <b-col class="col-12">
      <b-overlay variant="dark" blur="2px" :show="show_chart" rounded="sm">
        <apexchart :options="dateinfo.chartOptions" :series="dateinfo.series"></apexchart>
      </b-overlay>
    </b-col>
  </b-row>
  <b-row class="mb-0" align-v="center">
    <b-col>
      <h1 class="text-warning">Energy Report</h1>
    </b-col>
    <b-col class="col-12"><hr></b-col>
  </b-row>
  <b-row class="">
    <b-col>
      <div class="text-center">
        <b-overlay variant="dark" blur="2px" :show="show" rounded="sm">
          <b-table responsive :items="table_items[0]" thead-class="hidden_header" striped bordered />
        </b-overlay>
      </div>
    </b-col>
    <!-- <b-col cols="12">
      <h1>{{this.$route.params.dev_id}}</h1>
    </b-col> -->
  </b-row>

</b-container>
</template>
<script>
import {
  BButtonToolbar,
  BButtonGroup,
  BFormRadioGroup,
  BTabs,
  BTab,
  BRow,
  BCol,
  BFormGroup,
  BButton,
  BTable,
  BCard,
  BOverlay,
  BContainer,
} from "bootstrap-vue";
import { Carousel, Slide } from "vue-carousel";
import flatPickr from "vue-flatpickr-component";
import Ripple from "vue-ripple-directive";

export default {
  components: {
    BButtonToolbar,
    BButtonGroup,
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
    Slide,
    BOverlay,
    BContainer,
  },
  directives: {
    Ripple,
  },
  data() {
    return {
      tokens: '',
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
      name_label:"",

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
      selectedRadio: "active",
      show: false,
      show_chart:false,
      optionsRadio: [
        { text: "Day", value: "day" },
        { text: "Days", value: "days" },
        { text: "Month", value: "month" },
        { text: "Year", value: "year" },
      ],
      table_items: [],
    };
  },
  async beforeCreate() {
    if(this.$route.params.clickfrom != 'mainpage' && this.$route.params.clickfrom != 'viewroom'){
    await this.$liff.init({liffId: "1655594666-dvnpqxLx" })   
    if (this.$liff.isLoggedIn()){
        this.getGraphData();
    }
    else{
        this.$liff.login()
    }
    }
    },
  methods: {

    getGraphData() {
      this.show = true
      this.show_chart = true
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

      if(this.$route.params.clickfrom == 'mainpage' || this.$route.params.clickfrom == 'viewroom' ){
        this.tokens = this.$route.params.token
      }
      else{
        this.tokens = this.$liff.getIDToken()
      }
      const params = {
        id: this.url_params.id, //id ส่งไปกราฟ   
        startdate: tempstart,
        enddate: tempend,
        timeframe: this.selected_timeframe,
        type: this.url_params.type,
        dev_id : this.$route.params.dev_id,
        token: this.tokens
        
      };
      // console.log(tempstart);
      if (this.selected_timeframe == "day" || this.selected_timeframe == "month" || this.selected_timeframe == "year") {
          this.datepickermode = "single";
      } 
      else {
          this.datepickermode = "range";
      }
      this.axios.get("/energyreport/table", { params })
        .then(
          response => {
            this.table_items = response.data,
            console.log(this.table_items);
            // this.name_label = response.data,
            this.show = false
          },

        );
      // this.axios.get("/sensor/single/statistic", { params }).then(response => (
      this.axios.get("/energyreport/chart", { params }).then(response => (
          this.dateinfo = response.data,
          this.show_chart = false
      ))
    },
    back(){
          if(this.$route.params.clickfrom != ''){
              this.$router.push({name: this.$route.params.clickfrom})
          }
    }
  },
  mounted() {
    this.getGraphData();
    console.log(this.date_picker_single);
  },
};
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
.navbar-hidden .app-content {
padding: 2rem 1rem 0 1rem !important;
}
.container-fluid{
  padding-left: 0px !important;
  padding-right: 0px !important;
}
</style>
