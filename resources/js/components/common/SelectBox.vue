<template> 
        <select class="country form-control" @change="selectVal" v-model="selectedVal">
            <option value='' v-if="target == 'category'" selected style='display:none;'>カテゴリーで検索</option>
            <option value='' else selected style='display:none;'>種別で検索</option>
            <option value=''></option>
            <option v-for="(obj) in baseData" :key="obj.id" :value="obj.id" >
                {{obj.category_name}}
                {{obj.job_type}}
            </option>
        </select>
</template>

<script>

export default {
    data() {
        return{
        data:{},
        selectedVal:""
    }},
    props:['target','initVal','baseData'],
    created: function() {
        this.getTableData();
    },
    methods: {
        getTableData: function() {
            // var url = this.target == "category" ? "category" : "getJobType";
            var tc = this;
            // axios.get('/api/' + url).then(res => {
            //     this.data = res.data;

                if(tc.initVal != undefined && tc.initVal != ""){
                    tc.selectedVal = tc.initVal;
                }
            // });
        },
        selectVal:function() {
            this.$emit('changeSelectedVal',this.selectedVal);
        }
    },
    computed:{
        route:function(){
          return this.$route.params;
        }
    },
    watch:{
        route:function(){
          this.selectedVal = "";
          this.selectVal();
        }
    }
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