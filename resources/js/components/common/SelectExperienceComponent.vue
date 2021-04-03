<template> 
    <div class="form-group" style="position:relative; margin-bottom: 30px;">
        <label for="" style="display:block">Experience</label>
        <div v-for="(experience, index) in experiences" :key="index" >
            <div style="width:45%;display:inline-block">
                <select-box-component @changeSelectedVal="changeSelectedCategory($event,experience,index)" :target="'category'" :baseData="categories" :initVal="experience.category_id"></select-box-component>
            </div>
            <div style="width:45%;display:inline-block">
                <input type="number" class="form-control" v-model="experience.experience_years" style="display: inline-block;width: 50%;margin-right: 10px;margin-bottom:5px;">
                <span min="1">years</span>
            </div>
        </div>

        <span class="add-btn" @click="addExForm">+</span>    

    </div>
</template>

<script>

export default {
    data() {
        return{}
    },
    props:["experiences"],
    methods: {
        changeSelectedCategory:function(val,experience,index){

          this.$emit("selectExperience",val,experience,index);

        },
        addExForm:function(){
            
          this.$emit("addExForm");

        }
    },
    computed:{
        categories:function(){
            return this.$store.getters['auth/categories'];
        },
    }
}
</script>


<style scoped>
    .add-btn{
        border-radius: 100%;
        border: 1px solid gray;
        padding: 0px;
        cursor: pointer;
        font-size: 15px;
        width: 23px;
        height: 23px;
        display: inline-block;
        text-align: center;
        position: absolute;
        left: 0px;
        color:gray;
    }
</style>