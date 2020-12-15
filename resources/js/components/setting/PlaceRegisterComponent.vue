<template>
    <div class="place-register-area">  
        <div class="place-forms">
            <div class="single-form-title">Add country</div>
            <div class="form-group">
                <label>
                    Country Name: 
                </label>
                <input type="text" name="country" v-model="countryForm.countryName" class="form-control">
                <div style="width:100%;margin:auto;max-width:200px">
                <label for="countryBg">
                    <input type="file" name="countryBg" id="countryBg" class="p-3" style="display:none" @change="countryBgSelect">
                    <div style="display:inline-block;margin-left:10px;width:100%;">
                        <div>Background image :</div>
                    </div>
                    <img v-if="currentBgImage != null" :src="currentBgImage" class="m-auto p-auto image country-bg-image" alt="image">
                    <img v-else :src="defaultImage" class="m-auto p-auto image country-bg-image" alt="image">
                </label>
                </div>
                <div>
                    <button v-on:click='registerCountry' class="btn register-btn">Submit</button>
                </div>       
            </div>
            <div class="single-form-title">Add province</div>
            <div class="form-group">
                <label>
                    Country Name: 
                </label>
                <select class="country form-control" v-model="provinceForm.countryId">
                    <option value='' disabled selected style='display:none;'>選択してください</option>
                    <option v-for="(obj) in countries" :key="obj.id" :value="obj.id" >
                        {{obj.country_name}}
                    </option>
                </select>
                <label>
                    Region Name: 
                </label>
                <input type="text" name="province" v-model="provinceForm.provinceName" class="form-control">
                
                <div>
                    <button v-on:click='registerProvince' class="btn register-btn">Submit</button>
                </div>       
            </div>
            <div class="single-form-title">Add city</div>
            <div class="form-group">
                <label>
                    Country Name: 
                </label>
                <select class="country form-control" v-model="cityForm.countryId" @change="setProvince">
                    <option value='' disabled selected style='display:none;'>選択してください</option>
                    <option v-for="(obj) in countries" :key="obj.id" :value="obj.id" >
                        {{obj.country_name}}
                    </option>
                </select>
                <label style="position:relative;">
                    Region Name: 
                    <spinner v-if="loading" style="
                        position: absolute;
                        left: 60%;
                        top: 10%;
                    " size="20"
                    line-fg-color="#f00"></spinner>

                </label>
                <select class="province form-control" v-model="cityForm.provinceId">
                    <option value='' disabled selected style='display:none;'>選択してください</option>
                    <option v-for="(obj) in provinces" :key="obj.id" :value="obj.id" >
                        {{obj.province_name}}
                    </option>
                </select>
                <label>
                    City Name: 
                </label>
                <input type="text" name="city" v-model="cityForm.cityName" class="form-control">
                <div>
                    <button v-on:click='registerCity' class="btn register-btn">Submit</button>
                </div>       
            </div>
        </div>
    </div>
</template>


<script>
import methodMixIn from '../common/CommonMethodsMixIn.vue';
import { UNAUTHORIZED ,OK, UNPROCESSABLE_ENTITY} from '../../util';
import { VueGoodTable } from 'vue-good-table';

export default {
    data() {
        return {
            countries:[],
            provinces:[],
            city:'',
            currentBgImage:null,
            defaultImage: "/images/search.jpg",
            countryForm:{
                countryName:"",
                countryImage:null
            },
            provinceForm:{
                provinceName:"",
                countryId:""
            },
            cityForm:{
                cityName:"",
                provinceId:"",
                countryId:""
            },
            loading:false
        }
    },
    methods: {
        countryBgSelect: function(file){
            var target = file.target.files[0]
            this.countryForm.countryImage = target;
            const self = this; 
            this.readFileAsDataURL(target).then((value) => {
                self.currentBgImage = value;
            });
        },
        registerCountry: function(){
            this.countryForm.countryName = this.countryForm.countryName.trim();

            if(this.countryForm.countryName == ""){
                this.$store.dispatch('common/alertModalUp', {data:OK, successMessage:'国名を入力してください'});
                return;
            }else if(this.countryForm.countryImage == null){
                this.$store.dispatch('common/alertModalUp', {data:OK, successMessage:'背景を選択してください。'});
                return;
            }

            const formData = new FormData()

            formData.append("countryName", this.countryForm.countryName);
            formData.append("countryImage", this.countryForm.countryImage);

            axios.post('/api/registerCountry', formData).then(res => {
                if(res.status != OK){
                    this.$store.dispatch('common/alertModalUp', {data:OK, successMessage:res.data.message});
                    return false;
                }
                this.$store.dispatch('common/alertModalUp', {data:res.status, successMessage:'国を追加しました。'});
                this.getCountries();
                console.log(res.data);
            });
        },
        registerProvince:function(){
            this.provinceForm.provinceName = this.provinceForm.provinceName.trim();

            if(this.provinceForm.countryId == ""){
                this.$store.dispatch('common/alertModalUp', {data:OK, successMessage:'国を選択してください。'});
                return;
            }else if(this.provinceForm.provinceName == ""){
                this.$store.dispatch('common/alertModalUp', {data:OK, successMessage:'地域を入力してください'});
                return;
            }
            
            axios.post('/api/registerProvince', this.provinceForm).then(res => {
                this.$store.dispatch('common/alertModalUp', {data:res.status, successMessage:'地域を追加しました。'});
                this.loading = true;
                this.setProvince();
                console.log(res.data);
            });
        },
        registerCity:function(){
            this.cityForm.cityName = this.cityForm.cityName.trim();
            if(this.cityForm.countryId == ""){
                this.$store.dispatch('common/alertModalUp', {data:OK, successMessage:'国を入力してください'});
                return;

            }else if(this.cityForm.provinceId == ""){
                this.$store.dispatch('common/alertModalUp', {data:OK, successMessage:'地域を選択してください。'});
                return;

            }else if(this.cityForm.cityName == ""){
                this.$store.dispatch('common/alertModalUp', {data:OK, successMessage:'都市名を入力してください'});
                return;
            }
            var that = this;
            axios.post('/api/registerCity', this.cityForm).then(res => {
                this.$store.dispatch('common/alertModalUp', {data:res.status, successMessage:'都市を追加しました。'});
                console.log(res.data);
                that.$emit('update_data');
                this.$store.dispatch('auth/refreshCountries');
            });
        },
        getCountries:function(){
            axios.get('/api/getCountries').then(res => {
                this.countries = res.data;
            });
        },
        setProvince:function(){
            var that = this;
            this.loading = true;
            axios.get('/api/getTargetProvinces/' + this.cityForm.countryId).then(res => {
                this.provinces = res.data;
                that.loading = false;
            });
        }
    },
    mounted:function(){
        this.countries = this.$store.getters['auth/countries']
    },
    mixins: [methodMixIn],

}
</script>


<style scoped>


.form-control {
    width:50%;
    display:inline-block;
    min-width: 200px;
}
.register-btn {
    width:100px;
    padding:10px 5px;
    margin:20px 0;
}
.place-register-area {
    border:solid 2px lightgray;
    border-radius:10px;
    padding:20px;
    width:100%;
    padding:20px !important;
}

.form-title {
    font-size:24px;
}

.form-group {
    margin:10px auto;
    text-align:center;
    border: lightgray 2px solid;
    border-radius: 10px;
    margin-bottom:50px;
}

.country-bg-image {
    width:200px;
    height:200px;
}
label{
    width:100%;
    margin-top:15px;
}

.single-form-title{
    text-align:center;
    font-size:20px;
    
 }
</style>