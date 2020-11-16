<template> 
        <div class="place-show-area">
            <div style="min-width:500px">
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
                >
                <template slot="table-row" slot-scope="props">
                    <span v-if="props.column.field == 'delBtn'" >
                        <button 
                            @mouseover="mouseOverAction(props.row, props.column.field)"
                            @mouseleave="mouseLeaveAction(props.row, props.column.field)"
                            class="btn delete-btn" 
                            v-bind:class="{hoveredBtn:hoverFlag && hoverIndex == props.row && hoverColumn == 'delBtn'}" 
                            @click="deleteCity(props.row.delBtn)">X
                        </button>

                    </span>
                    <span v-else-if="props.column.field == 'country'" >
                        {{props.formattedRow[props.column.field]}}    
                        <button 
                            @mouseover="mouseOverAction(props.row, props.column.field)"
                            @mouseleave="mouseLeaveAction(props.row, props.column.field)"
                            class="btn delete-btn" 
                            v-bind:class="{hoveredBtn:hoverFlag && hoverIndex == props.row && hoverColumn == 'country'}" 
                            @click="changeBg(props.row.countryData)">background image
                        </button>
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
    data() {
        return{
            countries:{},
            provinces:{},
            cities:{},
            checkedCountry: "",
            checkedProvince: "",
            checkedCity: "",

            hoverFlag:false,
            hoverIndex:null,
            hoverColumn:null,
            placeData:{},

            columns: [
                {
                label: 'country',
                field: 'country',
                },
                {
                label: 'province',
                field: 'province',
                },
                {
                label: 'city',
                field: 'city',
                },
                {
                label: '',
                field: 'delBtn',
                },
            ],
            rows: [],
        }
    },
    mounted: function() {
        this.getTableData();
    },
    methods: {
        getTableData: function() {
            console.log("placeTable取りに行きます。");
            axios.get('/api/getPlaceForShowList').then(res => {
                this.placeData = res.data;
                this.setTableData(res.data);
                console.log("placeTableのデータ");
                console.log(res.data);
            });
        },
        setTableData: function(placeData){
            var result = [];
            for(var target of placeData){
                var place = {
                        "country":target.country_name,
                        "province":target.province_name,
                        "city":target.city_name,
                        "delBtn":target.id,
                        "countryData":target
                    };
                result.push(place);
            }
            this.rows = result;
        },
        deleteCity: function(id){
            axios.get('/api/deleteCity/' + id).then(res => {
                
                if(res.status == 503){
                    this.$store.dispatch('common/alertModalUp', {data:res.status, successMessage:res.data.message});
                    return false;
                }
                this.$store.dispatch('common/alertModalUp', {data:res.status, successMessage:'削除しました。'});
                console.log(id  + "を削除しました");
                console.log(res.data);
                console.log("テーブルを更新");

                this.getTableData();
            });
        },
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
        updatePlaceTable(){
            this.getTableData();
        },
        changeBg(countryData){
            this.$store.dispatch('common/setBgChangeModal',countryData);
        }
        
    }, 
    components: {
        VueGoodTable,
    },
    computed:{
        tableReloadFlg:function(){
            return this.$store.state.common.settingCountryReloadFlg;

        }
    },
    watch:{
        tableReloadFlg:function(newVal,old){
            if(newVal != old){
                console.log('table更新')
                this.updatePlaceTable();
            }
        }
    }

}
</script>


<style scoped>

.form-control {
    width:15%;
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
    padding:0px !important;
    width:100%;
    overflow:scroll;
}

.form-title {
    font-size:24px;
}

.form-group {
    margin:10px auto;
    text-align:center;
}

.delete-btn {
    padding:5px 10px;
    opacity:0.5;
    font-size:10px;
    float:right;
}

table,
tr {
    text-align:center;
    width:100%;
}
td{
    width:25%;
}
.hoveredBtn{
    opacity:1;
}

/* 表示・非表示アニメーション中 */
.showList-enter-active, .showList-leave-active {
  transition: all 500ms;
}

/* 表示アニメーション開始時 ・ 非表示アニメーション後 */
.showList-enter, .showList-leave-to {
  opacity: 0;
  background:lightgray;
}
.showList-leave-to {
    background:red;
}

.showList-leave-active {
    width:100%;
    position:absolute; 
}
.showList-move {
  transition: transform 500ms;
}
</style>