<template>
<div class="job-content">
    <div class="form-title">New Job Offer</div>

    <div class="job-register-area">  
        <div class="form-group title-form">
            <label>
                Job Title: 
            </label>
            <input type="text" name="title" v-model="title" class="form-control">
        </div>
        <div class="form-group salary-form">

            <label>
                Anual Salary: 
            </label>
            <input type="text" name="salary" v-model="salary" class="form-control">
        </div>

        <div class="form-group place-form">

            <place-show-component @select-city='selectCity' ref="placeShowComponent"></place-show-component>
        </div>


        <div class="form-group type-form">
            <label>
                Job type
            </label>
            <select-box-component :baseData="jobTypes" @changeSelectedVal="selectType"></select-box-component>
        </div>

        <div class="form-group type-form">
            <label>
                Tags
            </label>
            <select-tag-component ref="selectTagComponent"></select-tag-component>
        </div>

        <div class="form-group category-form">
            <label>
                Category
            </label>
            <select-box-component :baseData="categories" @changeSelectedVal="selectCategory"></select-box-component>
        </div>

        <div class="form-group description-form">

            <label>
                Description: 
            </label>
            <textarea name="description" v-model="description" class="form-control" rows="8"></textarea>
        </div>
        <div class="form-group requirement-form">

            <label>
                Requirement: 
            </label>
            <textarea name="requirement" v-model="requirement" class="form-control" rows="8"></textarea>

        </div>
        <div class="form-group benefit-form">

            <label>
                Benefit: 
            </label>
            <textarea name="benefit" v-model="benefit" class="form-control" rows="8"></textarea>
        </div>
        <div class="form-group experience-fom">

            <label>
                Experience: 
            </label>
            <textarea name="experience" v-model="experience" class="form-control" rows="8"></textarea>

        </div>

        <label>
            Video: 
        </label>
        <video-upload-component ref="videoUploadComponent" v-if="test"></video-upload-component>                      
        <div @click="test = !test">aiueo</div>

        <div class="form-group submit-form">
            <button v-on:click='postData' class="btn submit-btn">Submit</button>       
        </div>
    </div>
</div>

</template>

<script>
export default {
    data() {
        return {
            title:'',
            salary:'',
            type:'',
            category:'',
            city:'',
            description:'',
            requirement:'',
            benefit:'',
            experience:'',
            companyId:this.$route.params.id,
            test:false
        }
    },

    methods: {
        postData() {
            var data = {
                            title:this.title,
                            salary:this.salary,
                            type:this.type,
                            city:this.city,
                            description:this.description,
                            requirement:this.requirement,
                            benefit:this.benefit,
                            experience:this.experience,
                            category:this.category,
                            company:this.companyId,
                            tag:this.$refs.selectTagComponent.selectedTagList,
                            addressObj:this.$refs.placeShowComponent.addressObj,
                            latLng:this.$refs.placeShowComponent.lastMarkerPosition,
                            mapFlg:this.$refs.placeShowComponent.mapShow,
                            videoFile:this.$refs.videoUploadComponent.uploadVideoUrl
                        };
            console.log(data);

            axios.post('/api/registerJob', data).then(res => {
                // テストのため返り値をコンソールに表示
                console.log(res.data);

                this.$store.dispatch('common/alertModalUp', {data:res.status, successMessage:'登録しました。'});


            });


        },
        selectType: function(val){
            this.type = val
        },
        selectCategory: function(val) {
            this.category = val;
        },
        selectCity: function(val) {
            this.city = val;
        },
        selectTag: function(){
            console.log("tag");
        }
    },
    props:["categories","jobTypes"]
}
</script>


<style scoped>
.form-group {
    margin:10px auto;
    text-align:left;
}
.title-form,
.salary-form,
.address-form {
    display: inline-block;
    width: 49%;
    padding-left: 2%;
    padding-right: 2%;
}
.container {
    z-index:100;
    height: 100%;
}

.job-content {
    width:100%;
    margin:auto;
}
.form-control {
    width:15%;
    display:inline-block;
    width:100%;
}
label {
    text-align:left;
    padding-top:15px;
}
.submit-form {
    width:100px;
    text-align:center;
}
.submit-btn {
    padding:20px 20px;
    border-radius:10px;
}
.job-register-area {  
    border:solid 2px lightgray;
    border-radius:10px;
    padding:20px;
    width:100%;
    color:white;
    height:100%;

    background:#d3d3d3b0;
    opacity:1;
}

.form-title {
    font-size:24px;
    color:white;
    font-weight:bold;
}


</style>