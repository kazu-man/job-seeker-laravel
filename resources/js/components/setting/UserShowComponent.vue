<template>
        <div class="user-show-area">
        <div class="user-show-table-style">
            <vue-good-table
                :columns="columns"
                :rows="users"
                :fixed-header="true"
                :search-options="{
                    enabled: true,
                    trigger: 'enter',
                    skipDiacritics: true,
                    placeholder: 'Search this table',
                }"
                styleClass="vgt-table"
                :row-style-class="rowStyleClassFn"
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
                    pageLabel: 'page', // for 'pages' mode
                    allLabel: 'All',
                }"
                >
                <template slot="table-row" slot-scope="props" >
                    <span v-if="props.column.field == 'delBtn'">
                        {{props.formattedRow[props.column.field]}}    
                        <button 
                            v-if="props.row.user_status != 'D'"
                            @mouseover="mouseOverAction(props.row, props.column.field)"
                            @mouseleave="mouseLeaveAction(props.row, props.column.field)"
                            class="btn delete-btn btn-danger" 
                            v-bind:class="{hoveredBtn:hoverFlag && hoverIndex == props.row}" 
                            @click="deleteUser(props.row)">Close
                        </button>
                        <button 
                            v-else
                            @mouseover="mouseOverAction(props.row, props.column.field)"
                            @mouseleave="mouseLeaveAction(props.row, props.column.field)"
                            class="btn delete-btn btn-success" 
                            v-bind:class="{hoveredBtn:hoverFlag && hoverIndex == props.row}" 
                            @click="deleteUser(props.row)">Open
                        </button>
                    </span>
                    <span v-else-if="props.column.field == 'user_firstname'">
                        {{cleanedFullName(props.formattedRow[props.column.field],props.row.user_lastname)}}
                    </span>
                    <span v-else>
                        {{props.formattedRow[props.column.field]}}
                    </span>
                </template>
            </vue-good-table>
        </div>  
        </div>  
 
</template>


<script>
import { VueGoodTable } from 'vue-good-table';

export default {
    data() {return{
        hoverFlag:false,
        hoverIndex:null,
        hoverColumn:null,

        columns: [
            {
            label: 'user name',
            field: 'user_firstname',
            },
            {
            label: 'E-mail',
            field: 'email',
            },
            {
            label: 'user type',
            field: 'user_type',
            },
            {
            label: '',
            field: 'delBtn',
            sortable:false
            },
        ],
        

    }},
    props: {
        users:{} 
    },
    methods: {  
        mouseOverAction(i,hoverColumn){
            this.hoverIndex = i;
            this.hoverFlag = true;
            this.hoverColumn = hoverColumn;
        },      
        mouseLeaveAction(i,hoverColumn){
            this.hoverIndex = i;
            this.hoverFlag = false;
            this.hoverColumn = hoverColumn;
        },   
        cleanedFullName(firstName,lastName){
            if(lastName == null){
                lastName = "";
            }
            if(firstName == null){
                firstName = "";
            }
            return firstName + " " + lastName.replace(/"/g, '');
        },
        deleteUser(user){
            console.log(user)
            this.$store.dispatch('common/setUserDeleteModal', user)
        },
        rowStyleClassFn(row) {
            return row.user_status == "D" ? 'closed' : '';
        },
    },
    computed:{
        lastDeletedUser:function(){
            return this.$store.state.auth.lastDeletedUser;
        }
    },
    watch:{
        lastDeletedUser:function(){ 
            console.log("refresh desu")
            this.$emit('refresh');
        }
    },
    components: {
        VueGoodTable,
    }


}
</script>


<style scoped>

.table-title {
    font-size:24px;
}

.user-show-area {
    border:solid 2px lightgray;
    border-radius:10px;
    width:100%;
    position:relative;
    overflow-x:scroll;
}
.delete-btn {
    width:67px;
    padding:10px;
}
/* 表示・非表示アニメーション中 */
.v-enter-active, .v-leave-active {
  transition: all 500ms;
}

/* 表示アニメーション開始時 ・ 非表示アニメーション後 */
.v-enter, .v-leave-to {
  opacity: 0;
  background:lightgray;
}
tr.v-leave-to {
    background:red;
}

</style>
<style>
.closed{
    background:#80808057;
}
.user-show-table-style{
    min-width:500px;
}
</style>