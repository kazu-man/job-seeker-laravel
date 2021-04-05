<template> 
<div>
    <div style="font-size:20px;font-weight:bold;color:white">Find Wokers</div>
        <div class="search-user-main">

            <div class="search-user-from">

                <select-experience-component 
                　@selectExperience="changeSelectedCategory" 
                　@addExForm="addExForm"
                　:experiences="experiences"></select-experience-component>

                <div class="key-word-area" >
                    <label>Key Words</label>
                    <input type="text" class="form-control" v-for="(keyWord, index) in keyWords" :key="index" :obj="keyWord" v-model="keyWord.value">
                </div>

                <div class="search-btn-area">
                    <button v-on:click="getScout" class="btn">
                        <span class="icon-heart-o mr-2">Search</span>
                    </button>
                </div>

            </div>
        </div>

        <div class="job-content user-table">
            <div class="list-scout-btn" @click="getMyScout" :class="{selectedBtn:onlyMyScoutFlg}">List Your Scouts</div>
            <div style="min-width:500px;">

                <spinner v-if="loading" style="
                    position:absolute;
                    top:45%;
                    left:50%;
                    z-index: 99999999;
                " size="40"
                line-fg-color="#f00"></spinner>

                <vue-good-table
                    :columns="columns"
                    :rows="rows"
                    :fixed-header="true"
                    :search-options="{
                        enabled: true,
                        trigger: 'enter',
                        skipDiacritics: true,
                        placeholder: 'Search this table',
                    }"
                    :sort-options="{
                        enabled: true,
                    }"
                    :pagination-options="{
                        enabled: true,
                        mode: 'pages',
                        perPageDropdown: [10, 20, 50],
                        dropdownAllowAll: true,
                        nextLabel: 'next',
                        prevLabel: 'prev',
                        rowsPerPageLabel: 'Rows per page',
                        ofLabel: 'of',
                        pageLabel: 'page', 
                        allLabel: 'All',
                    }"
                    styleClass="vgt-table"
                    @on-cell-click="onCellClick">
                    <template slot="table-row" slot-scope="props">
                        <span v-if="props.column.field == 'resume'" style="cursor:pointer;white-space:pre-line;overflow-wrap: break-word;color:blue">{{props.formattedRow[props.column.field] == "" ? "" : "Resume"}}</span>
                        <span v-else-if="props.column.field == 'scout'" style="cursor:pointer;white-space:pre-line;overflow-wrap: break-word;color:blue"><button @click="setScoutModal(props.row)" class="btn btn-info message-btn" :class="{scouted:props.row.scoutFlg || checkScoutOrNot(props.row.user_id)}" style="padding:7px">{{props.row.scoutFlg || checkScoutOrNot(props.row.user_id)? "scouted" : "scout"}}</button></span>
                        <span v-else style="cursor:pointer;white-space:pre-line;overflow-wrap: break-word;">{{props.formattedRow[props.column.field]}}</span>
                    </template>
                </vue-good-table>
                
            </div>
        </div>
</div>

</template>

<script>
import 'vue-good-table/dist/vue-good-table.css'
import { VueGoodTable } from 'vue-good-table';

import { UNAUTHORIZED ,OK, UNPROCESSABLE_ENTITY} from '../../util';
import methodMixIn from '../common/CommonMethodsMixIn.vue';

export default {
    data() {
        return{
        experiences:[
                { 
                        id : "", category_id : "", experience_years : "" 
                }
        ],
        keyWords:[
            {
                value:""
            },
        ],
        users:[],
        columns: [
                {
                label: 'User Name',
                field: 'user_name',
                },
                {
                label: 'Experiences',
                field: 'experiences',
                },
                {
                label: 'Resume',
                field: 'resume',
                },
                {
                label: '',
                field: 'scout',
                },

        ],
        rows:[],
        loading:false,
        onlyMyScoutFlg:false
    }},
    methods: {

        changeSelectedCategory:function(val,experience,index){
          console.log(val);
          console.log(experience);
          this.experiences[index].category_id = val;
        },
        addExForm:function(){
            var length = this.experiences.length;
            this.experiences.push(
                { 
                        id : "", category_id : "", experience_years : "" 
                }
            );
        },
        addKeyWord:function(){
            this.keyWords.push(
                {value:""
                }
            );
        },
        seachSubmit:function(){

            var formData = new FormData();
            formData.append("experiences", JSON.stringify(this.experiences));
            formData.append("keyWords", JSON.stringify(this.keyWords));
            formData.append("onlyMyScoutFlg", this.onlyMyScoutFlg);
            this.loading = true;

            axios.post('/api/searchUser', formData).then(res => {

                this.rows = res.data;
                this.loading = false;
            });
        },
        getMyScout:function(){
            this.onlyMyScoutFlg = !this.onlyMyScoutFlg;
            this.seachSubmit();
        },
        getScout:function(){
            this.onlyMyScoutFlg = false;
            this.seachSubmit();

        },
        onCellClick(props){
            
            if(props.column.field == 'jobTitle'){
                this.seePost(props.row.jobId);
            }else if(props.column.field == 'user_name'){
                this.seeProfile(props.row.profile_id);
            }else if(props.column.field == "resume"){
                if(props.row.resume != null && props.row.resume != ""){
                    this.resumeDownLoad(props.row.resume)
                }
            }
        },
        resumeDownLoad(resume){
            var data = {
                "resumeFilePath":resume,
                "resumeFile":resume.substr(resume.lastIndexOf('/') + 1)
            }
            this.download(data);
        },
        seeProfile(profileId){
            this.$store.dispatch('common/setApplicantProfileModal', profileId)
        },
        setScoutModal(record){

            this.$store.dispatch('common/setScoutModal', record)

        },

    },
    watch:{
        keyWords:{
            handler: function (val, old) {
                var lastKey = val.slice(-1)[0];

                if(lastKey.value != ""){
                    this.addKeyWord();
                }
            },
            deep:true
        }
    },    
    components: {
        VueGoodTable,
    },
    computed:{
        checkScoutOrNot:function () {

            return function (target) {
                return this.$store.getters['common/scoutedIds'].includes(target);

            };
        }
        
    },
    mixins: [methodMixIn],


}
</script>


<style scoped>
.search-user-main{
    width: 100%;
    background: white;
    margin: auto;
    padding: 5px;
    border-radius: 10px;
}

.search-user-from{
    width:90%;
    margin:30px auto;
}
.key-word-area label{
    width:100%;
    display:block;
    margin: 50px 0 5px 0;
}
.key-word-area input{
    width:48%;
    display:inline-block;
    margin:5px auto;
}

.key-word-area input:nth-child(even){
    margin-right:1%;
}

.search-btn-area{
    text-align: center;
    margin-top: 20px;
}

.search-btn-area button{
    margin:auto;
}

.user-table{
    position:relative;
    overflow:scroll;
    padding-top:40px;
}

.scouted{
    background:#33b5e56b !important;
}

.list-scout-btn{
    position:absolute;
    padding: 5px 6px;
    margin-right: 8px;
    margin-left: 1px;
    font-size: 75%;
    color: white;
    border-radius: 6px;
    box-shadow: 0 0 3px #ddd;
    white-space: nowrap;
    background-color: #58ACFA;
    right: -8px;
    top: 10px;
    z-index: 1;
    cursor: pointer;
}

.selectedBtn{
    background-color: #a6d0f7 ;
}

</style>