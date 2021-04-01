<template> 
        <div class="place-show-area">
                <div class="place-form">
                    <label>
                        Country:
                    </label>
                    <select class="country form-control" v-model="checkedCountry">
                        <option v-for="(country ,key) in countries" v-bind:key="key" v-bind:value="country">{{country.country_name}}</option>
                    </select>
                </div>
                <div class="place-form">

                    <label>
                        Region:
                    </label>
                    <select class="province form-control" v-model="checkedProvince">
                        <option v-for="(province ,key) in provinces" v-bind:key="key" v-bind:value="province">{{province.province_name}}</option>
                    </select>
                </div>
                <div class="place-form">
        
                    <label>
                        City:
                    </label>
                    <select class="city form-control" v-model="checkedCity">
                        <option v-for="(city ,key) in cities" v-bind:key="key" v-bind:value="city">{{city.city_name}}</option>
                    </select>

                </div>

                <div>
                    <div class="mapButtonArea">
                        <p v-if="!mapShow" @click="mapAreaShow"><span class="add-btn">+</span>　地図を表示する</p>    
                        <p v-else @click="mapAreaShow"><span class="add-btn">-</span>　地図を表示しない</p> 

                        
                    </div>
                    <transition name="content">
                        <div class="mapListArea" v-show="mapShow">

                            <div class="mapTypeArea" >
                                <div style="padding:0 0 3px 0"><span style="width:16%; display: inline-block;">国：</span>{{checkedCountry != "" && checkedCountry != null ? checkedCountry.country_name : ""}}</div>
                                <div style="padding:3px 0"><span style="width:16%; display: inline-block;">都市：</span>{{checkedProvince != "" && checkedProvince != null ? checkedProvince.province_name : ""}}</div>
                            </div>
                            <div>
                            <label style="width:16%;">
                                住所1: 
                            </label>
                            <input type="text" style="width:78%;display:inline-block" class="form-control" v-model="addressObj.address_line_1">
                            </div>
                            <label style="width:16%;">
                                住所2: 
                            </label>
                            <input type="text" style="width:78%;display:inline-block" class="form-control" v-model="addressObj.address_line_2">
                            <label style="width:16%;">
                                郵便番号: 
                            </label>
                            <input type="text" style="width:40%;display:inline-block" class="form-control" v-model="addressObj.zip_code">
                            

                            <GmapMap
                                :center="{ lat: currentLat, lng:currentLng  }"
                                :zoom="mapZoom"
                                map-type-id="roadmap"
                                style="width: 90%; height: 300px; margin:30px auto;"
                            >
                                <GmapMarker
                                    :key="index"
                                    v-for="(m, index) in markers"
                                    :position="m.position"
                                    :clickable="true"
                                    :draggable="true"
                                    @click="center=m.position"
                                    @drag="drag"
                                />
                            </GmapMap>
                            
                        </div>
                    </transition>
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
        mapShow:false,
        addressObj:{
            address_line_1: '',
            address_line_2: '',
            city: '',
            state: '',
            zip_code:'',
            country: '',
        },
        currentLat:35.4122,
        currentLng:139.4130,
        lastMarkerPosition:{lat:'',lng:''},
        mapZoom:4,
        markers: [{ position: { lat: 35.4122, lng: 139.4130 } }],
        updatedMarker:false
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
                    var $countries = res.data;
                    this.countries = $countries;

                    if(this.initVal != undefined && this.initVal.province != undefined && this.initVal.province != ""){
                        console.log("country 初期値あり");
                        console.log(res.data);
                        for(var country of res.data){
                            if(this.initVal.province.country.country_name == country.country_name){
                                this.checkedCountry = country;
                                break;
                            }
                        }
                    }
                });
            },
            updateProvince: function($id) {
                if($id == ""){
                    return;
                }
                axios.post('/api/getProvinces' , {"id":$id}).then(res => {
                    var $provinces = res.data;
                    this.provinces = $provinces;
                    this.checkedCity = "";
                    this.checkedProvince = "";
                    console.log(res);
                    if(this.initVal != undefined && this.initVal.province != undefined && this.initVal.province != ""){
                        for(var province of res.data){
                            if(this.initVal.province.province_name == province.province_name){
                                this.checkedProvince = province;
                                break;
                            }
                        }

                    }
                });
            },
            updateCity: function($id){
                if($id == ""){
                    return;
                }
                axios.post('/api/getCities' , {"id":$id}).then(res => {
                    var $cities = res.data;
                    this.cities = $cities;
                    console.log(res);
                    if(this.initVal != undefined && this.initVal != "" && this.initVal.province != undefined &&  this.initVal.province != ""){
                        for(var city of res.data){
                            if(this.initVal.city_name == city.city_name){
                                this.checkedCity = city;
                                this.addressObj = this.initAddressObj;
                                break;
                            }
                        }
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
        },
        mapAreaShow:function() {
            this.mapShow = !this.mapShow;
        },
        updateMap:function(){
            Vue.$geocoder.send(this.addressObj, response => { 
                console.log("map検索結果");
                console.log(response);
                if(response.status == "ZERO_RESULTS"){
                    return;
                }
                var newLat = response.results[0].geometry.location.lat;
                var newLng = response.results[0].geometry.location.lng;

                // 保存用
                this.lastMarkerPosition.lat = newLat;
                this.lastMarkerPosition.lng = newLng;
                // 地図表示用
                this.currentLat = newLat;
                this.currentLng = newLng;
                // マーカー表示用
                if(!this.updatedMarker && this.initMarkers != undefined){

                    this.markers[0].position.lat = this.initMarkers.lat;
                    this.markers[0].position.lng = this.initMarkers.lng;

                }else{

                    this.markers[0].position.lat = newLat;
                    this.markers[0].position.lng = newLng;

                }
            });
        },
        drag:function(e){
            console.log(e.latLng.lat());
            console.log(e.latLng.lng());
            this.lastMarkerPosition.lat = e.latLng.lat();
            this.lastMarkerPosition.lng = e.latLng.lng();
        }
    }, 
    watch: {
        checkedCountry: function($el) {
            this.updateProvince($el.id);
            this.addressObj.city = "";
            this.addressObj.address_line_1 = "";
            this.mapZoom = 4;
            this.addressObj.country = $el != undefined && $el != "" ? $el.country_name : "";
        },
        checkedProvince: function($el) {

            if($el != "" && $el != undefined){
                this.updateCity($el != undefined && $el != "" ? $el.id : "");
                this.addressObj.address_line_1 = "";
                this.mapZoom = 8;
                this.addressObj.city = $el != undefined && $el != "" ? $el.province_name : "";
            }
        },
        checkedCity:function($el){
            
            if($el != "" && $el != undefined){
                this.$emit('select-city',$el.id);
                this.mapZoom = 16;
                this.addressObj.address_line_1 = $el.city_name;
            }
        },
        countryInShow : function($el) {console.log('country = ' + $el.id)},
        provinceInShow : function($el) {console.log('province = ' + $el.id)},
        
        addressObj:{
            handler: function (val, old) {
                this.updateMap();
            },
            deep:true
        }
    

    },props:['initVal','initAddressObj','initMarkers']
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

.mapButtonArea {
    width: 100%;
    min-height: 30px;
    border-radius: 5px;
    padding-left: 20px;
    position: relative;
    margin-top: 10px;
}
.mapButtonArea p{
    font-size:13px;
    display:inline;
    cursor:pointer;
}
.mapListArea {
    width: 100%;
    height:550px;
    border-radius: 5px;
    background: white;
    margin-top:20px;
    padding: 15px 5% 15px 5%;
    overflow:hidden;
}

.mapListArea label{
    color:black;
}
.mapTypeArea{
    color:black;
}

.mapName{
    font-size: 18px;
    /* border-bottom: solid 2px darkgray; */
    margin: 0 auto;
    padding: 0px 20px;
    margin: 20px 0px;
    font-weight:500;
}
.mapName:after{
    background-color: darkgray;
    border-radius: 5px;
    content: "";
    display: block;
    height: 6px;
    width: 103%;
    margin-left: -3%;
}

.add-btn{
    border-radius: 100%;
    border: 1px solid white;
    padding: 0px;
    cursor: pointer;
    font-size: 15px;
    width: 23px;
    height: 23px;
    display: inline-block;
    text-align: center;
    position: absolute;
    left: 0px;

}

@media (max-width: 415px) {

    .mapListArea,.mapListArea input{
        font-size:10px !important;
    }

    .mapListArea {
        height:500px ;
    }
}

@media (max-width: 376px) {

    .mapListArea label,.mapListArea span{
        width:20% !important;
    }
}

/* 表示・非表示アニメーション中 */
.content-enter-active{
  transition: ease-in-out 500ms !important;
}
.content-leave-active{
  transition: ease-in-out 500ms !important;
}

/* 表示アニメーション開始時 ・ 非表示アニメーション後 */
.content-enter,
.content-leave-to,
.content-leave{
  height:0;
  margin:0;
  padding-top:0;
  padding-bottom:0;
}

.content-move {
    transition:ease-in-out 300ms !important;
}

</style>