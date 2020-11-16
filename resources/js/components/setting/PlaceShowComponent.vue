<template> 
        <div class="place-show-area">
                <div class="place-form">
                    <label>
                        Country:
                    </label>
                    <select class="country form-control" v-model="checkedCountry">
                        <option v-for="(country ,key) in countries" v-bind:key="key" v-bind:value="country.id">{{country.country_name}}</option>
                    </select>
                </div>
                <div class="place-form">

                    <label>
                        Region:
                    </label>
                    <select class="province form-control" v-model="checkedProvince">
                        <option v-for="(province ,key) in provinces" v-bind:key="key" v-bind:value="province.id">{{province.province_name}}</option>
                    </select>
                </div>
                <div class="place-form">
        
                    <label>
                        City:
                    </label>
                    <select class="city form-control" v-model="checkedCity">
                        <option v-for="(city ,key) in cities" v-bind:key="key" v-bind:value="city.id">{{city.city_name}}</option>
                    </select>

                </div>
        </div>         
</template>

<script>
var placeShowListComponent = Vue.component('placeShowListComponent', require('./PlaceShowList.vue').default);

export default {
    data() {
        return{
        countries:{},
        provinces:{},
        cities:{},
        checkedCountry: "",
        checkedProvince: "",
        checkedCity: "",
        placeData:{},
        countryInShow:"",
        provinceInShow:"",
    }},
    components: {
        placeShowListComponent
    },
    created: function() {
        this.updateCountry();
        this.getTableData();
    },
    methods: {
            updateCountry: function () {
                axios.get('/api/getCountries').then(res => {
                    console.log("country探しにいきます");
                    var $countries = res.data;
                    this.countries = $countries;
                    console.log($countries);

                    if(this.initVal != undefined && this.initVal.province != undefined && this.initVal.province != ""){
                        console.log("country 初期値あり");
                        this.checkedCountry = this.initVal.province.country_id;
                    }
                });
            },
            updateProvince: function($id) {
                if($id == ""){
                    return;
                }
                console.log("province探しにいきます" + $id);
                console.log("checkedCountry = " + this.checkedCountry);
                axios.post('/api/getProvinces' , {"id":$id}).then(res => {
                    var $provinces = res.data;
                    this.provinces = $provinces;
                    this.checkedCity = "";
                    console.log(res);
                    if(this.initVal != undefined && this.initVal.province != undefined && this.initVal.province != ""){
                        this.checkedProvince = this.initVal.province.id;
                    }
                });
            },
            updateCity: function($id){
                if($id == ""){
                    return;
                }
                console.log("city探しにいきます" + $id);
                axios.post('/api/getCities' , {"id":$id}).then(res => {
                    var $cities = res.data;
                    this.cities = $cities;
                    console.log(res);
                    if(this.initVal != undefined && this.initVal.province != undefined && this.initVal.province != ""){
                        this.checkedCity = this.initVal.id;
                    }
                });
            },
            updateAll: function() {
                this.updateCountry();
                this.updateProvince(this.checkedCountry);
                this.updateCity(this.checkedProvince);
            },

            getTableData: function() {
            console.log("placeTable取りに行きます。");
            axios.get('/api/getPlaceData').then(res => {
                this.placeData = res.data;
                console.log("placeTableのデータ");
                console.log(res.data);
            });
        }
    }, 
    watch: {
        checkedCountry: function($id) {this.updateProvince($id)},
        checkedProvince: function($id) {this.updateCity($id)},
        countryInShow : function($id) {console.log('country = ' + $id)},
        provinceInShow : function($id) {console.log('province = ' + $id)},
        checkedCity:function(val){console.log('province = ' + val);this.$emit('select-city',val);}

    },props:['initVal']
}
</script>


<style scoped>
.place-form {
    width: 100%;
    display: inline-block;
    margin: 0 1%;
}

.form-control {
    width:100%;
    display:inline-block;
}
.register-btn {
    width:100px;
    padding:10px 5px;
    margin-left:20px;
}
.place-show-area {
    border:solid 2px lightgray;
    border-radius:10px;
    padding:20px;
    width:100%;
}

.form-title {
    font-size:24px;
}

.form-group {
    margin:10px auto;
    text-align:center;
}
.countryRow {
    color:red;
}

.provinceRow {
    color:blue;
}
.form-controll{
    min-width: 200px;    
}

</style>