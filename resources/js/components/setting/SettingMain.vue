<template>
            <div>
                <keep-alive>
                    <div style="position:relative">
                    <transition-group v-if="selectPage === routePath + '/setting/users'">
                        <div class="component-title" :key="'title'">Users List</div>
                        <div v-on:click="adminModalUp()" class="btn btn-success admin-add" :key="'addBtn'">Add New Admin</div>
                        <div class="component" :key="'company'">
                            <user-show-component 
                            :users="users"
                            @refresh="refreshList"></user-show-component>
                        </div>
                    </transition-group>
                    </div>
                </keep-alive>

                <keep-alive>
                    <transition-group v-if="selectPage === routePath + '/setting/category'">
                        <div class="component-title" :key="'title'">Register Category</div>
                        <div class="component" :key="'category'">
                            <category-show-component 
                            ref="company-show-com"
                            :categories="categories"
                            @refresh-category-list="refreshList"></category-show-component>
                        </div>
                    </transition-group>
                </keep-alive>

                <keep-alive>
                    <transition-group v-if="selectPage === routePath + '/setting/country'">
                        <div class="component-title" :key="'title'">Register Places</div>
                        <div class="component" :key="'placeRegister'">
                            <place-register-component  @update_data="updateData"></place-register-component>
                        </div>
                        <div class="component-title" :key="'title2'">place List</div>
                        <div class="component" :key="'placeList'">
                            <placeShowListComponent ref="placeCom"></placeShowListComponent>
                        </div>

                    </transition-group>
                </keep-alive>
            </div>
</template>


<script>
export default {
    data() {return{
            users:[],
            categories:[],
    }},
    methods: { 
        categoryUpdate(newVal) {
            this.categories = newVal;
        },
        updateData(){
            this.$refs.placeCom.updatePlaceTable();

        },
        refreshList(){
           this.init();        
        },
        changeComponent(target){
            console.log("component change");
            this.selectedComponent = target;
        },
        goToMainPage(){
            this.$router.push(`${this.routePath}/top`).catch(()=>{});
        },
        init(){
            axios.get('/api/getSettingData').then(res => {
                var $userList = res.data.users;
                var $cateList = res.data.categories;

                this.users = $userList;
                this.categories = $cateList;
            });
        },
        adminModalUp(){
            this.$store.commit('common/setModalTarget', "registerAdmin")
        },
    },
    created : function() {
        this.init();
    },
    computed:{
        routePath:function(){
            return this.$store.getters['auth/routePath'];
        },
        selectPage:function(){
            return this.$route.path;
        }
    },

}
</script>


<style scoped>
.container .component {
    margin:20px auto;
    width:100%;
}
.component div {
    /* padding:50px; */
    background:white;
    opacity:0.95;
    max-width:800px;
    margin:0 auto 50px auto;
}
/* 表示・非表示アニメーション中 */
.v-enter-active{
  transition: ease-in-out 500ms;
}

/* 表示アニメーション開始時 ・ 非表示アニメーション後 */
.v-enter, .v-leave-to {
  opacity: 0;
}
.component-title{
    color:white;
    font-size:25px;
    padding-left:10%;
}
.admin-add {
    position: absolute;
    top: 0;
    right:8%;
    padding: 10px;
}

</style>

