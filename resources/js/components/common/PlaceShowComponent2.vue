<template> 
        <select class="country form-control" v-model="selectedPlace" @change="selectPlace">
            <option value='' disabled selected style='display:none;'>場所で検索</option>
            <option value=''></option>
            <option v-for="(place) in cleanedData" v-bind:key="`${place.id}_${place.type}_${place.value}`" 
            :value="place">
                {{place.value}}
            </option>
        </select>
</template>

<script>

export default {
    data() {
        return{
        placeData:{},
        selectedPlace:"",
        cleanedData:{},

    }},
    created: function() {
        this.getTableData();
    },
    methods: {
            getTableData: function() {
            console.log("placeTable取りに行きます。");
            axios.get('/api/getPlaceData').then(res => {
                this.placeData = res.data;
                console.log("placeTableのデータ");
                console.log(res.data);
                this.computedPlace();
            });
        },
        selectPlace(){
            this.$emit('changeSelectedPlace',this.selectedPlace);
        },
        computedPlace: function() {
            var countries = this.placeData;
            var countryInShow = 0;
            var provinceInShow = 0;
            var result = [];
            var obj = null;

            countries.forEach((country) => {
                obj = {"value":country.country_name, "type":"country","id":country.id};
                result.push(obj);
                obj = null;

                country.provinces.forEach((province) => {

                    obj = {"value":"　-" + province.province_name, "type":"province","id":province.id};
                    result.push(obj);
                    obj = null;
                    
                    province.cities.forEach((city) => {

                        obj = {"value":"　　-" + city.city_name, "type":"city", "id": city.id};
                        result.push(obj);
                        obj = null;

                    });

                });
            });
            this.cleanedData = result;
        }
    }, 

    

}
</script>


<style scoped>
select {
    width:30%;
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

</style>