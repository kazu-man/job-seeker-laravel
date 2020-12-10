<template>
            <div>
                <keep-alive>
                    <div style="position:relative">
                    <transition-group>
                        <div class="component" :key="'company'">
                            <router-view 
                            :users="users"
                            @refresh="refreshList"
                            :categories="categories"
                            @refresh-category-list="refreshList"
                            :loading="loading"
                            ></router-view>
                        </div>
                    </transition-group>
                    </div>
                </keep-alive>
            </div>
</template>


<script>
export default {
    data() {return{
            users:[],
            categories:[],
            loading : false
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
            this.loading = true;
            axios.get('/api/getSettingData').then(res => {
                var $userList = res.data.users;
                var $cateList = res.data.categories;

                this.users = $userList;
                this.categories = $cateList;
                this.loading = false;
            });
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
        },
        refreshDataFlg:function(){
            return this.$store.state.auth.refreshAdminDataFlg;
        },
    },
    watch:{
        refreshDataFlg:function(next){
            console.log(next);
            console.log("asdfsafsdfsdfsadfsafsdafsafdsafluhsdiuhvalisudhfliashduflausdifhliuasdhlviuasdhluifnext");
            if(next){
                this.init();
                this.$store.dispatch('auth/refreshAdminData', false);
            }
        }
    }

}
</script>


<style scoped>
.container .component {
    margin:20px auto;
    width:100%;
}
.component div {
    /* padding:50px; */
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

</style>

