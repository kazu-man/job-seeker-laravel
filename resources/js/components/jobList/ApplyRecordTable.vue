<template>
    <div class="job-content apply-table">
        <div class="form-title">Applies</div>
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
                    <span v-if="props.column.field == 'message'">
                        <transition name="badge">
                            <span v-if="props.row.newMessageFlg" class="badge badge-danger" style="position:absolute">new</span>
                        </transition>
                        <button @click="setMessageModal(props.row)" class="btn btn-info message-btn">Messages</button>
                    </span>
                    <span v-else style="cursor:pointer">
                        {{props.formattedRow[props.column.field]}}
                    </span>
                </template>
            </vue-good-table>
            
    </div>
    </div>

</template>

<script>

import 'vue-good-table/dist/vue-good-table.css'
import { VueGoodTable } from 'vue-good-table';

export default {
    data(){
        return {            
            columns: [
                {
                label: 'Job title',
                field: 'jobTitle',
                },
                {
                label: 'Applicant Name',
                field: 'applicantName',
                },
                {
                label: 'Applied On',
                field: 'applyDate',
                type: 'date',
                dateInputFormat: 'yyyy-MM-dd',
                dateOutputFormat: 'MM/dd/yyyy',
                },
                {
                label: '',
                field: 'message',
                type: 'message',
                sortable: false,
                },
            ],
            loading:true,
            rows: [],
        }
    },
    methods:{
        getApplyRecords(){
            this.loading = true;
            axios.get('/api/getApplyRecords').then(res => {
                this.rows = res.data;
                console.log('getApplyRecords');
                console.log(res.data);
                this.loading = false;
            });
        },
        onCellClick(props){
            
            console.log("props");
            console.log(props);
            if(props.column.field == 'jobTitle'){
                this.seePost(props.row.jobId);
            }else if(props.column.field == 'applicantName'){
                this.seeProfile(props.row.profileId);
            }
        },
        seePost(jobId){
            this.$store.dispatch('common/setSinglePostModal', jobId)
        },
        seeProfile(profileId){
            this.$store.dispatch('common/setApplicantProfileModal', profileId)
        },
        setMessageModal(record){
            record.newMessageFlg = false;
            this.$store.dispatch('common/setMessageModal', record)
        },
    },
    mounted:function() {
        this.getApplyRecords();
    },
    components: {
        VueGoodTable,
    }
}
</script>









<style scoped>
.form-group {
    margin:10px auto;
    text-align:left;
}
.title-form,
.salary-form {
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

    background:gray;
    opacity:0.9;
}

.form-title {
    font-size:24px;
    color:white;
    font-weight:bold;
    position: sticky;
    top: 0;
    left: 0;
}

/* 表示・非表示アニメーション中 */
.badge-enter-active {
  transition: ease-in-out 500ms;
}
.badge-leave-active{
  transition: ease-in-out 500ms;
}

.badge-enter,
.badge-leave-to,
.badge-leave{
  opacity:0;
}
.apply-table{
    overflow:scroll;
}
.message-btn {
    width:80px;
    padding:6px 0px !important;
}
@media (max-width:414px){
    .message-btn{
        width: 60px;
        padding: 7px 0px !important;
        font-size: 10px;
    }
}

</style>
<style >
@media (max-width:414px){
    .show-on-pc{
        display: none;
    }
}
</style>