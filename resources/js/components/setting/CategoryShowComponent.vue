<template>
    <div>
        <div class="component-title" :key="'title'" >Category List</div>
        <div  class="category-show-area">
                <spinner v-if="loading" style="
                    position:absolute;
                    top:50%;
                    left:50%;
                " size="40"
                line-fg-color="#f00"
                ></spinner>

            <categoryRegisterComponent @refresh-list="refresh()"></categoryRegisterComponent>
            <div v-show="listSize > 0" class="list-count">{{listSize}} registered</div>
                <ul style="padding-left:0">
            <transition-group class="flex">
                    <li v-for="category in categories" :key="category.id + 'test' + category.created_at" @mouseover="mouseOverAction(category.id)" v-on:mouseleave="mouseLeaveAction(category.id)">
                        <span v-if="hoverFlag && hoverIndex == category.id" @click="deleteCategory(category.id)" class="del-btn btn">X</span>
                        <span v-else style="display:inline-block;width:20px;">{{category.id}}.</span> {{category.category_name}}
                    </li>
            </transition-group>
                </ul>
        </div>
        </div>
</template>


<script>
// var categoryRegisterComponent = Vue.component('categoryRegisterComponent', require('./CategoryRegisterComponent.vue').default);
import categoryRegisterComponent from './CategoryRegisterComponent.vue'

export default {
    data() {return{
        hoverFlag:false,
        hoverIndex:null
    }},
    props:[
        "categories",
        "loading"
    ],
    components:{
        categoryRegisterComponent,
    },
    methods:{
        refresh(){
            console.log('refresh');
            this.$emit('refresh-category-list');
        },
        mouseOverAction(i){
            this.hoverIndex = i;
            this.hoverFlag = true;
        },      
        mouseLeaveAction(i){
            this.hoverIndex = i;
            this.hoverFlag = false;
        },
        deleteCategory(id) {
            var data = {"id": id};

            axios.post('/api/category/delete', data).then(res => {
                if(res.status == 503){
                    this.$store.dispatch('common/alertModalUp', {data:res.status, successMessage:res.data.message});
                    return false;
                }
                this.$store.dispatch('common/alertModalUp', {data:res.status, successMessage:'削除しました。'});
                console.log(res.data);
                this.$emit('refresh-list');
            });
            this.$emit('refresh-category-list');
        }
    },
    computed:{
        listSize:function(){
            return this.categories.length;
        }
    }

}
</script>


<style scoped>
.category-show-area {
    background:white;
    border-radius:10px;
}
.category-show-title {
    font-size:24px;
    width:30%;
    display:inline-block;
}
ul {
    height:280px;
    margin:1rem 0;
}
.flex {
    display: flex;
    flex-flow: column wrap;
    height:280px;
    overflow:scroll;
    background:#c5b9b917;
    border-radius:10px;
    padding:10px;
    border:1px solid lightgray;
}
ul li {
   list-style:none;
   width:45%;
   float:left;
   min-width:300px;
}
.category-show-area {
    border:solid 2px lightgray;
    border-radius:10px;
    padding:20px;
    width:100%;
}
.del-btn {
    padding:0;
    margin:0;
    margin-right:3px;
    width:20px;
    height:20px;
    border-radius:5px;
    background:red;
    color:white;
}

/* 表示・非表示アニメーション中 */
.v-enter-active, .v-leave-active {
  transition: all 500ms;
}
/* 表示アニメーション開始時 ・ 非表示アニメーション後 */
.v-enter, .v-leave-to {
  opacity: 0;
}
.list-count{
    text-align: right;
    margin-top: 10px;
}
.component-title{
    color:white;
    font-size:25px;
    padding-left:5%;
    margin-bottom: 15px;
}
</style>
