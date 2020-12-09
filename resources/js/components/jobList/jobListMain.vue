<template>
    <div>
        <side-header-component v-slot:default="slotProps" ref="header" :selectedMenuType="selectedMenuType">
            <span  class="menu-btn hide" @click="changeMenuType('normal')" :key="'toggle1'">Menu</span>
            <span  v-if="loginCheck && loginUser != null &&loginUser.user_type == 'A'" class="menu-btn hide setting" @click="changeMenuType('admin')" :key="'toggle2'">Setting</span>
            <section v-if="slotProps.drawerFlg" 　class="drawer-menu-wrapper" :class="{setting:loginCheck && loginUser != null && loginUser.user_type == 'A' && selectedMenuType == 'admin'}" :key="'toggle'">
                <span class="menu-btn" @click="changeMenuType('normal')">Menu</span>
                <span v-if="loginCheck && loginUser != null && loginUser.user_type == 'A'" class="menu-btn setting" @click="changeMenuType('admin')">Setting</span>
                <div v-if="loginCheck && loginUser != null && loginUser.user_type == 'A' && selectedMenuType == 'admin'">
                    <transition-group name="inMenu" class="a">
                        <div class="drawer-menu setting" :key="'admin'" >
                            <ul>
                                <li @click="switchMenu('setting/users')" v-bind:class="{selectedBgInSetting:selectedMenu == 'setting/users'}">Users</li>
                                <li @click="switchMenu('setting/category')" v-bind:class="{selectedBgInSetting:selectedMenu == 'setting/category'}">Category</li>
                                <li @click="switchMenu('setting/country')" v-bind:class="{selectedBgInSetting:selectedMenu == 'setting/country'}">Country</li>
                        </ul>
                        </div>
                    </transition-group>
                </div>
                    <div v-else>
                <transition-group name="inMenu" class="a">
                        <div class="menu-title user-info"  v-on:click="modalShow('login')" :key="'loginTitle'" v-if="!loginCheck">Login</div>
                        <div class="menu-title user-info login-menu" :key="'loginTitle'" @click="logoutInMenu = !logoutInMenu" v-else >{{loginUser.name == null ? loginUser.email : loginUser.name}}</div>
                        <div class="drawer-menu" v-if="loginCheck && logoutInMenu" :key="'loginMenus'">
                            <ul>
                                <li @click="switchMenu('profile')" v-bind:class="{selectedBg:selectedMenu == 'profile'}">Profile</li>
                                <li @click="logout">
                                    logout
                                </li>
                            </ul>
                        </div>        

                        <div class="menu-title top-menu-option" @click="switchMenu('top')"
                        :key="'top'" v-bind:class="{selectedBg:selectedMenu == 'top'}">Top</div>

                        <div class="menu-title" @click="countryInMenu = !countryInMenu;switchMenu('country')"
                        :key="'coutnryTitle'" v-bind:class="{selectedBg:countryInMenu && selectedMenu == 'country'}">Countries</div>
                        <div class="drawer-menu menu-country" v-if="countryInMenu" :key="'country'">
                            <ul>
                                <li v-for="country in countries" :key="country.id" 
                                @click="changeCountry(country.country_name)" 
                                v-bind:class="{selectedBg:selectedCountry == country.country_name}">{{country.country_name}}</li>
                            </ul>
                        </div>

                        <div class="menu-title" @click="categoryInMenu = !categoryInMenu;switchMenu('category')" 
                        :key="'categoryTitle'" v-bind:class="{selectedBg:categoryInMenu && selectedMenu == 'category'}">Categories</div>
                        <div class="drawer-menu menu-category" v-if="categoryInMenu" :key="'category'">
                            <ul>
                                <li v-for="category in categories" :key="category.id" 
                                @click="changeCategory(category.category_name)" 
                                v-bind:class="{selectedBg:selectedCategory == category.category_name}">{{category.category_name}}</li>
                            </ul>
                        </div>

                        <div v-if="loginCheck && loginUser != null &&loginUser.user_type == 'C'" :key="'companymenu'">
                            <div>
                                <div class="menu-title" @click="switchMenu('postJob')" 
                                :key="'jobTitle'" v-bind:class="{selectedBg:selectedMenu == 'postJob'}">Post jobs</div>
                        
                                <div class="menu-title" @click="switchMenu('posts')" 
                                :key="'postTitle'" v-bind:class="{selectedBg:selectedMenu == 'posts'}">Your post</div>

                                <div class="menu-title" @click="switchMenu('appliesList')" 
                                :key="'appliesList'" v-bind:class="{selectedBg:selectedMenu == 'appliesList'}">Applies
                                <transition name="badge">
                                    <span v-if="newMessageFlg" class="badge badge-danger new-message-badge">new message</span>
                                </transition>
                                </div>
                            </div>
                        </div>

                        <div v-else-if="loginCheck && loginUser != null &&loginUser.user_type == 'U'" :key="'userMenu'">
                            <div>
                                <div class="menu-title" @click="switchMenu('likes')" 
                                :key="'likes'" v-bind:class="{selectedBg:selectedMenu == 'likes'}">Likes</div>
                            </div>
                            <div>
                                <div class="menu-title" @click="switchMenu('applies')" 
                                :key="'applies'" v-bind:class="{selectedBg:selectedMenu == 'applies'}">Apply records
                                <transition name="badge">
                                    <span v-if="newMessageFlg" class="badge badge-danger new-message-badge">new message</span>
                                </transition>

                                </div>
                            </div>
                        </div>
                </transition-group>
                    </div>
            </section>
        </side-header-component>

        <transition name="main" appear appear-active-class="first-enter-active">
            <section :class="bgClass" class="overlay main-bg " :key="selectedContentsBg + selectedCountry + selectedCategory" :style="bgImage" v-cloak>
                <div class="container" @click="closeMenu">
                    <keep-alive>
                        <transition name="content" appear>
                            <router-view 
                            :searchInfo="searchInfo" 
                            @changeCategory="changeCategory"
                            @changeCountry="changeCountry"
                            @switchMenu="switchMenu"
                            @removeApplyLikeFlg="removeApplyLikeFlg"
                            :countries="countries"
                            ></router-view>
                        </transition>
                    </keep-alive>
                </div>
            </section>
        </transition>

        <modal ref="modal"></modal>

    </div>
</template>


<script>
import modal from '../common/ModalComponent.vue';

export default {
    
    data() {
        return{
            // companies:[],
            selectedCountry:"",
            selectedCategory:"",
            countryInMenu:false,
            categoryInMenu:false,
            jobsInMenu:false,
            selectedMenu:"",
            searchInfo:{
                pageType:"",
                searchedBy:"",
                companyId:"",
                countryName:"",
                categoryName:"",
                categoryId:"",
                likes:false,
                applies:false
            },
            logoutInMenu:false,
            selectedBg:"",
            selectedContentsBg:"",
            categories:null,
            countries:null,
            countryBgImage:null,
            selectedMenuType:""
    }},
    methods: { 
        changeCountry(countryName) {
            this.scrollTop();
            for(let country of this.countries){
                if(country.country_name == countryName){
                    this.countryBgImage = country.country_image;
                    break;
                }
            }

            if(countryName == this.selectedCountry){
                return;
            }

            this.searchInfoClear();
            this.menuClear();
            this.selectedCountry = countryName;
            this.selectedMenu = "country";
            this.selectedContentsBg = "country";

            this.searchInfo.pageType = "country";
            this.searchInfo.searchedBy = countryName;
            this.searchInfo.countryName = countryName;
            this.$router.push(`${this.routePath}/country/${this.selectedCountry}`).catch(()=>{});
        },
        changeCategory(categoryName) {
            this.scrollTop();

            if(categoryName == this.selectedCategory){
                return;
            }

            this.searchInfoClear();
            this.menuClear();
            this.selectedCategory = categoryName;
            this.selectedMenu = "category";
            this.selectedContentsBg = "category";

            this.searchInfo.pageType = "category";
            this.searchInfo.searchedBy = categoryName;
            this.searchInfo.categoryName = categoryName;
            this.$router.push(`${this.routePath}/category/${this.selectedCategory}`).catch(()=>{});
        },
        closeMenu() {
            if(this.$refs.header.drawerFlg){
                this.$refs.header.openDrawerMenu(); 
                this.selectedMenuType = "";
            }
        },
        async logout () {
            this.$store.dispatch('common/alertModalUp', {data:200, successMessage:'ログアウトしました。'});
            await this.$store.dispatch('auth/logout')
            this.switchMenu('top');
        },
        modalShow(val){
            this.$refs.modal.modalShow(val);
        },
        searchInfoClear:function(){
            // this.menuClear();
            this.searchInfo = {};
        },
        menuClear() {
            // this.selectedMenu = "";
            this.selectedContentsBg = "";
            this.selectedCountry="";
            this.selectedCategory="";
        },
        switchMenu(selectVal) {
            console.log(selectVal);

            if(selectVal == this.selectedMenu){
                return;
            }

            if(selectVal == "category" || selectVal == "country"){
                this.selectedMenu = selectVal;   
                return;             
            }

            this.selectedMenu = selectVal;   
            console.log(this.selectedMenu)
            console.log("this.selectedMenu")
            // this.selectedContentsBg = selectVal;
        },
        scrollTop: function(){
            window.scrollTo({
                top: 0,
            });
        },
        removeApplyLikeFlg: function(){
            this.searchInfo.applies = false;
            this.searchInfo.likes = false;
        },
        changeMenuType:function(val) {
            
            if(this.selectedMenuType == ""){
                this.$refs.header.openDrawerMenu(val);
                this.selectedMenuType = val;
            }else if(this.selectedMenuType == val){
                this.$refs.header.openDrawerMenu(val);
                this.selectedMenuType = "";
            }else{
                this.selectedMenuType = val;
            }

        },
        initData: function(){
            axios.get('/api/category').then(res => {
                this.categories = res.data;
                console.log(res.data);

                if(this.$route.params != null){
                    if (this.$route.params.category != null){ 
                        this.changeCategory(this.$route.params.category)
                    };
                }
            });
            axios.get('/api/getCountries').then(res => {
                console.log("country探しにいきます");
                var $countries = res.data;
                this.countries = $countries;
                console.log($countries);

                if(this.$route.params != null){
                    if (this.$route.params.country != null){ 
                    this.changeCountry(this.$route.params.country)
                    };
                }
            });
        },
    },
    watch:{
        selectedMenu:async function(){

            if(this.selectedMenu == "category" && this.selectedCategory != "" || this.selectedMenu == "country" && this.selectedCountry != ""){
                this.selectedContentsBg = this.selectedMenu;
                return;             
            }
            // this.searchInfoClear();
            var that = this;
            if(this.selectedMenu == 'postJob' || this.selectedMenu == 'posts' || this.selectedMenu == "appliesList"){
                this.searchInfo.companyId = this.loginUser.company_id;
                this.searchInfo.pageType = 'post';
                this.searchInfoClear();
                await this.$router.push(`${this.routePath}/${this.selectedMenu}/${this.searchInfo.companyId}`)
                .then(() => {
                    that.scrollTop();
                    that.menuClear();
                    that.selectedContentsBg = that.selectedMenu;
                })
                .catch(()=>{});

            }else if(this.selectedMenu == 'top'){
                this.searchInfoClear();
                await this.$router.push(`${this.routePath}/top`)
                .then(() => {
                    that.menuClear();
                    that.selectedContentsBg = that.selectedMenu;
                })
                .catch(()=>{
                });
            }else if(this.selectedMenu == 'profile'){
                await this.$router.push(`${this.routePath}/profile/${this.loginUser.id}`)
                .then(() => {
                    that.scrollTop();
                    that.searchInfoClear();
                    that.menuClear();
                    that.selectedContentsBg = that.selectedMenu;
                })
                .catch(()=>{});
            }else if(this.selectedMenu == 'likes'){
                this.searchInfoClear();
                this.searchInfo.likes = true;
                this.searchInfo.pageType = "likes";
                await this.$router.push(`${this.routePath}/likes/${this.loginUser.id}`)
                .then(() => {
                    that.scrollTop();
                    that.menuClear();
                    that.selectedContentsBg = that.selectedMenu;
                })
                .catch(()=>{});

            }else if(this.selectedMenu == 'applies'){
                this.searchInfoClear();
                this.searchInfo.applies = true;
                this.searchInfo.pageType = "applies";
                await this.$router.push(`${this.routePath}/applies/${this.loginUser.id}`)
                .then(() => {
                    that.scrollTop();
                    that.menuClear();
                    that.selectedContentsBg = that.selectedMenu;
                })
                .catch(()=>{});

            }else if(this.selectedMenu == 'setting/country'){
                await this.$router.push(`${this.routePath}/setting/country`)
                .then(() => {
                    that.scrollTop();
                    that.searchInfoClear();
                    that.menuClear();
                    that.selectedContentsBg = that.selectedMenu;
                })
                .catch(()=>{});

            }else if(this.selectedMenu == 'setting/category'){
                await this.$router.push(`${this.routePath}/setting/category`)
                .then(() => {
                    that.scrollTop();
                    that.searchInfoClear();
                    that.menuClear();
                    that.selectedContentsBg = that.selectedMenu;

                })
                .catch(()=>{});

            }else if(this.selectedMenu == 'setting/users'){

                await this.$router.push(`${this.routePath}/setting/users`)
                .then(() => {
                    that.scrollTop();
                    that.searchInfoClear();
                    that.menuClear();
                    that.selectedContentsBg = that.selectedMenu;
                })
                .catch(()=>{});
            }
            
        },
        modalTarget:function(){
            if(this.modalTarget == null){
                return;
            }
            this.modalShow(this.modalTarget);
            this.$store.commit('common/setModalTarget', null)
        },

        countryReloadFlg:function(newVal,old){
            if(newVal != old){
                this.initData();
            }
        }
    },
    computed: {
        routePath:function(){
            return this.$store.getters['auth/routePath'];
        },
        modalTarget:function(){
            return this.$store.state.common.modalTarget;
        },
        loginUser:function() {
            return this.$store.state.auth.user;
        },
        loginCheck:function() {
            return this.$store.state.auth.loginCheck;
        },
        newMessageFlg:function(){
            return this.$store.state.auth.newMessageExistFlg;
        },
        bgClass: function () {
            return {
                // searchBg:this.selectedMenu == 'postJob',
                categoryBg:this.selectedContentsBg == 'category' && this.selectedCategory != '',
                mainBg:this.selectedContentsBg == 'top' 
                    || this.selectedContentsBg == 'posts' 
                    || this.selectedContentsBg == 'postJob' 
                    || this.selectedContentsBg == '' 
                    || this.selectedContentsBg == 'profile' 
                    || this.selectedContentsBg == 'likes' 
                    || this.selectedContentsBg == 'applies' 
                    || this.selectedContentsBg == 'appliesList' 
                    || this.selectedContentsBg == 'setting/country'
                    || this.selectedContentsBg == 'setting/category'
                    || this.selectedContentsBg == 'setting/users'
                    || (this.selectedContentsBg == 'country' && this.selectedCountry == '') 
                    || (this.selectedContentsBg == 'category' && this.selectedCategory == ''),
            }
        },
        bgImage: function(){
            if(this.selectedCountry == ""){
                return {

                }
            }else{
                return {
                    backgroundImage:  "url('" + this.countryBgImage + "')", 
                }
            }
        },
        countryReloadFlg:function(){
            return this.$store.state.common.settingCountryReloadFlg;

        }
    },
    created : function() {

        this.initData();
        this.scrollTop();

        console.log("user");
        console.log(this.user);
        console.log(this.isLogin);

    },
 
    components: {
        modal
    }

}
</script>


<style scoped>
.container {
    z-index:5;
    height: 100%;
    padding-top: 100px;
    padding-bottom: 100px;
    position:relative;
}
.company-show-area {
    background:white;
    border-radius:10px;

}
.menu-country,
.menu-category{
    overflow:scroll;
    height:150px;
}

.menu-country li:hover,
.menu-category li:hover{
    background: rgb(45, 51, 117);
}

.drawer-menu {
    padding-top:5px !important;
}
.drawer-menu li:hover{
    background: rgb(45, 51, 117);
}
.drawer-menu.setting li:hover{
  background-color: rgb(89 168 199);
}
.selectedBgInSetting{
  background-color: rgb(89 168 199);
}
.menu-title {
    padding-top:10px;
    padding-left:10px;
    font-weight:bold;
    cursor:pointer;
}
.menu-title:hover{
    background: rgb(45, 51, 117);
}
.overlay:before{
    z-index: 0;
    position: fixed;
    content: "";
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    height:100%; 
}

/* 表示・非表示アニメーション中 */
.inMenu-enter-active{
  transition: ease-in-out 500ms;
}
.inMenu-leave-active{
  transition: ease-in-out 100ms;
}

/* 表示アニメーション開始時 ・ 非表示アニメーション後 */
.inMenu-enter,
.inMenu-leave-to,
.inMenu-leave{
  opacity:0;
}

.inMenu-move {
    transition:ease-in-out 300ms;
}
.inMenu-leave-active {
    position: absolute;
}
.top-menu-option{
    margin-top:65px;
}

/* 表示・非表示アニメーション中 */
.content-enter-active {
  transition: ease-in-out 500ms;
}
.content-leave-active{
  transition: ease-in-out 10ms;
}

.content-enter,
.content-leave-to,
.content-leave{
  opacity:0;
}

.content-move {
    transition:ease-in-out 500ms;
}
.content-leave-active {
    position: absolute;
}

.main-enter-active {
  transition: all 1s;
}
.first-enter-active {
  transition: all 4s;
}

.main-enter,
.main-leave-to,
.main-leave {
  background-image: none;
  background-color:white;
  opacity:0;
}

.top-menu-option{
    margin-top:65px;
}

.drawer-menu{
    -ms-overflow-style: none;    /* IE, Edge 対応 */
    scrollbar-width: none;       /* Firefox 対応 */
}
.drawer-menu::-webkit-scrollbar {  /* Chrome, Safari 対応 */
    display:none;
}

.drawer-menu-wrapper {
  position: absolute;
  z-index: 1;
  top: 0;
  left: 0;
  width: 200px;
  height: 100%;
  background-color: rgb(36, 42, 121);
  padding-top:50px;
  color:rgb(222, 211, 247);
}
.drawer-menu-wrapper.setting{
    background-color:  #4894b3;
    color:white;
}
.drawer-menu {
  padding: 0px 0;
}
.drawer-menu li{
    cursor:pointer;
}
.selectedBg {
    background: rgb(45, 51, 117);
}
.selectedBg.setting{
  background-color: rgb(183, 221, 236);
}

.menu-btn {
    position: absolute;
    right: -50px;
    border-radius: 0px 20px 20px 0px;
    background-color:  rgb(36, 42, 121);
    padding: 16px 5px;
    color:rgb(222, 211, 247);
    cursor:pointer;
}
.menu-btn.setting{
    background-color:  #4894b3;
    top:110px;
    color:white;
    padding: 16px 3.5px;
    font-size: 14px;
}

.mainBg {
    background-image: url("/images/hero_1.jpg");    
    min-height:900px;   
    background-position: center;
}

.categoryBg{
    background-image: url("/images/sq_img_3.jpg");
    min-height:900px;
}

.main-bg {
    background-size: cover;
    background-repeat: no-repeat;
    background-attachment: fixed;
}

.login-menu:after {
    display: inline-block;
    margin-left: 0.255em;
    vertical-align: 0.255em;
    content: "";
    border-top: 0.3em solid;
    border-right: 0.3em solid transparent;
    border-bottom: 0;
    border-left: 0.3em solid transparent;
}

.new-message-badge {
    opacity: 1;
    font-size: 10px;
    vertical-align: middle;
    background-color: red !important;
}

</style>