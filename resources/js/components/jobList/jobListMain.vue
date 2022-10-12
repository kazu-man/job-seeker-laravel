<template>
    <div>
        <transition name="initLoader">
            <div class="modal-mask" v-if="init">
                <div class="loading">
                    <vue-loading
                        type="spiningDubbles"
                        color="#333"
                        :size="{ width: '50px', height: '50px' }"
                    ></vue-loading>
                </div>
            </div>
        </transition>
        <div v-if="loading" class="spinner-background" @click="closeMenu">
            <spinner
                style="
                position:absolute;
                top:50%;
                left:50%;
            "
                size="40"
            ></spinner>
        </div>

        <mailAlert
            ref="mailAlert"
            :loginCheck="loginCheck"
            :loginUser="loginUser"
        ></mailAlert>

        <side-header-component
            v-slot:default="slotProps"
            ref="header"
            :selectedMenuType="selectedMenuType"
        >
            <span
                class="menu-btn hide"
                @click="changeMenuType('normal')"
                :key="'toggle1'"
                >Menu</span
            >
            <span
                v-if="
                    loginCheck &&
                        loginUser != null &&
                        loginUser.user_type == 'A'
                "
                class="menu-btn hide setting"
                @click="changeMenuType('admin')"
                :key="'toggle2'"
                >Setting</span
            >
            <section
                v-if="slotProps.drawerFlg"
                class="drawer-menu-wrapper"
                :class="{
                    setting:
                        loginCheck &&
                        loginUser != null &&
                        loginUser.user_type == 'A' &&
                        selectedMenuType == 'admin'
                }"
                :key="'toggle'"
            >
                <span class="menu-btn" @click="changeMenuType('normal')"
                    >Menu</span
                >
                <span
                    v-if="
                        loginCheck &&
                            loginUser != null &&
                            loginUser.user_type == 'A'
                    "
                    class="menu-btn setting"
                    @click="changeMenuType('admin')"
                    >Setting</span
                >
                <div
                    v-if="
                        loginCheck &&
                            loginUser != null &&
                            loginUser.user_type == 'A' &&
                            selectedMenuType == 'admin'
                    "
                >
                    <transition-group name="inMenu" class="a">
                        <div class="drawer-menu setting" :key="'admin'">
                            <ul>
                                <li
                                    @click="switchMenu('setting/users')"
                                    v-bind:class="{
                                        selectedBgInSetting:
                                            selectedMenu == 'setting/users'
                                    }"
                                >
                                    Users
                                </li>
                                <li
                                    @click="switchMenu('setting/category')"
                                    v-bind:class="{
                                        selectedBgInSetting:
                                            selectedMenu == 'setting/category'
                                    }"
                                >
                                    Category
                                </li>
                                <li
                                    @click="switchMenu('setting/country')"
                                    v-bind:class="{
                                        selectedBgInSetting:
                                            selectedMenu == 'setting/country'
                                    }"
                                >
                                    Country
                                </li>
                            </ul>
                        </div>
                    </transition-group>
                </div>
                <div v-else>
                    <transition-group name="inMenu" class="a">
                        <div
                            class="menu-title user-info"
                            v-on:click="modalShow('login')"
                            :key="'loginTitle'"
                            v-if="!loginCheck"
                        >
                            Login
                        </div>
                        <div
                            class="menu-title user-info login-menu"
                            :key="'loginTitle'"
                            @click="logoutInMenu = !logoutInMenu"
                            v-else
                        >
                            {{
                                loginUser.name == null
                                    ? loginUser.email
                                    : loginUser.name | omittedText
                            }}
                        </div>
                        <div
                            class="drawer-menu"
                            v-if="loginCheck && logoutInMenu"
                            :key="'loginMenus'"
                        >
                            <ul>
                                <li
                                    @click="switchMenu('profile')"
                                    v-bind:class="{
                                        selectedBg: selectedMenu == 'profile'
                                    }"
                                >
                                    Profile
                                </li>
                                <li @click="logout">
                                    logout
                                </li>
                            </ul>
                        </div>

                        <div
                            class="menu-title top-menu-option"
                            @click="switchMenu('top')"
                            :key="'top'"
                            v-bind:class="{ selectedBg: selectedMenu == 'top' }"
                        >
                            Top
                        </div>

                        <div
                            class="menu-title"
                            @click="
                                countryInMenu = !countryInMenu;
                                switchMenu('country');
                            "
                            :key="'coutnryTitle'"
                            v-bind:class="{
                                selectedBg:
                                    countryInMenu && selectedMenu == 'country'
                            }"
                        >
                            Countries
                        </div>
                        <div
                            class="drawer-menu menu-country"
                            v-if="countryInMenu"
                            :key="'country'"
                        >
                            <ul>
                                <li
                                    v-for="country in countries"
                                    :key="country.id"
                                    @click="changeCountry(country.country_name)"
                                    v-bind:class="{
                                        selectedBg:
                                            selectedCountry ==
                                            country.country_name
                                    }"
                                >
                                    {{ country.country_name }}
                                </li>
                            </ul>
                        </div>

                        <div
                            class="menu-title"
                            @click="
                                categoryInMenu = !categoryInMenu;
                                switchMenu('category');
                            "
                            :key="'categoryTitle'"
                            v-bind:class="{
                                selectedBg:
                                    categoryInMenu && selectedMenu == 'category'
                            }"
                        >
                            Categories
                        </div>
                        <div
                            class="drawer-menu menu-category"
                            v-if="categoryInMenu"
                            :key="'category'"
                        >
                            <ul>
                                <li
                                    v-for="category in categories"
                                    :key="category.id"
                                    @click="
                                        changeCategory(category.category_name)
                                    "
                                    v-bind:class="{
                                        selectedBg:
                                            selectedCategory ==
                                            category.category_name
                                    }"
                                >
                                    {{ category.category_name }}
                                </li>
                            </ul>
                        </div>

                        <div
                            v-if="
                                loginCheck &&
                                    loginUser != null &&
                                    loginUser.user_type == 'C'
                            "
                            :key="'companymenu'"
                        >
                            <div>
                                <div
                                    class="menu-title"
                                    @click="switchMenu('postJob')"
                                    :key="'jobTitle'"
                                    v-bind:class="{
                                        selectedBg: selectedMenu == 'postJob'
                                    }"
                                >
                                    Post jobs
                                </div>

                                <div
                                    class="menu-title"
                                    @click="switchMenu('posts')"
                                    :key="'postTitle'"
                                    v-bind:class="{
                                        selectedBg: selectedMenu == 'posts'
                                    }"
                                >
                                    Your post
                                </div>

                                <div
                                    class="menu-title"
                                    @click="switchMenu('appliesList')"
                                    :key="'appliesList'"
                                    v-bind:class="{
                                        selectedBg:
                                            selectedMenu == 'appliesList'
                                    }"
                                >
                                    Applies
                                    <transition name="badge">
                                        <span
                                            v-if="newMessageFlg"
                                            class="badge badge-danger new-message-badge"
                                            >new message</span
                                        >
                                    </transition>
                                </div>

                                <div
                                    class="menu-title"
                                    @click="switchMenu('sendScout')"
                                    :key="'sendScoutTitle'"
                                    v-bind:class="{
                                        selectedBg: selectedMenu == 'sendScout'
                                    }"
                                >
                                    Scouts
                                </div>
                            </div>
                        </div>

                        <div
                            v-else-if="
                                loginCheck &&
                                    loginUser != null &&
                                    loginUser.user_type == 'U'
                            "
                            :key="'userMenu'"
                        >
                            <div>
                                <div
                                    class="menu-title"
                                    @click="switchMenu('likes')"
                                    :key="'likes'"
                                    v-bind:class="{
                                        selectedBg: selectedMenu == 'likes'
                                    }"
                                >
                                    Likes
                                </div>
                            </div>
                            <div>
                                <div
                                    class="menu-title"
                                    @click="switchMenu('applies')"
                                    :key="'applies'"
                                    v-bind:class="{
                                        selectedBg: selectedMenu == 'applies'
                                    }"
                                >
                                    Apply records
                                    <transition name="badge">
                                        <span
                                            v-if="newMessageFlg"
                                            class="badge badge-danger new-message-badge"
                                            >new message</span
                                        >
                                    </transition>
                                </div>
                            </div>

                            <div
                                class="menu-title"
                                @click="switchMenu('scoutList')"
                                :key="'scoutListTitle'"
                                v-bind:class="{
                                    selectedBg: selectedMenu == 'scoutList'
                                }"
                            >
                                Scout List
                            </div>
                        </div>

                        <div class="about" :key="'about'">
                            <div
                                class="menu-title"
                                @click="switchMenu('about')"
                                :key="'about'"
                                v-bind:class="{
                                    selectedBg: selectedMenu == 'about'
                                }"
                            >
                                About this page
                            </div>
                        </div>
                    </transition-group>
                </div>
            </section>
        </side-header-component>

        <transition name="main" appear appear-active-class="first-enter-active">
            <section
                v-show="changeBgShow"
                v-cloak
                :class="bgClass"
                class="overlay main-bg "
                :style="countryBgImageStyle"
            >
                <div class="container" @click="closeMenu">
                    <keep-alive>
                        <transition name="content" appear>
                            <router-view
                                @changeCategory="changeCategory"
                                @changeCountry="changeCountry"
                                @switchMenu="switchMenu"
                                :countries="countries"
                                :placeData="placeData"
                                :categories="categories"
                                :jobTypes="jobTypes"
                                :initFlg="true"
                            ></router-view>
                        </transition>
                    </keep-alive>
                </div>
            </section>
        </transition>

        <modal
            ref="modal"
            :placeData="placeData"
            :category="categories"
            :jobTypes="jobTypes"
        ></modal>
        <scrollTop></scrollTop>
    </div>
</template>

<script>
import scrollTop from "../common/GoTopComponent.vue";
import modal from "../common/ModalComponent.vue";
import mailAlert from "../common/MailAlertComponent.vue";
import { VueLoading } from "vue-loading-template";

export default {
    data() {
        return {
            selectedCountry: "",
            selectedCategory: "",
            countryInMenu: false,
            categoryInMenu: false,
            jobsInMenu: false,
            selectedMenu: "",
            logoutInMenu: false,
            selectedBg: "",
            selectedContentsBg: "",
            countryBgImage: null,
            selectedMenuType: "",
            loading: false,
            init: true,
            changeBgShow: true,
            test: false
        };
    },
    methods: {
        changeCountry(countryName) {
            this.scrollTop();
            for (let country of this.countries) {
                if (country.country_name == countryName) {
                    this.countryBgImage = country.country_image;
                    break;
                }
            }

            if (countryName == this.selectedCountry) {
                return;
            }

            this.menuClear();
            this.selectedCountry = countryName;
            this.selectedMenu = "country";
            this.selectedContentsBg = "country";
            this.$router
                .push(`${this.routePath}/country/${this.selectedCountry}`)
                .catch(() => {});
        },
        changeCategory(categoryName) {
            this.scrollTop();

            if (categoryName == this.selectedCategory) {
                return;
            }
            this.menuClear();
            this.selectedCategory = categoryName;
            this.selectedMenu = "category";
            this.selectedContentsBg = "category";
            this.$router
                .push(`${this.routePath}/category/${this.selectedCategory}`)
                .catch(() => {});
        },
        closeMenu() {
            if (this.$refs.header.drawerFlg) {
                this.$refs.header.openDrawerMenu();
                this.selectedMenuType = "";
            }
        },
        async logout() {
            this.$store.dispatch("common/alertModalUp", {
                data: 200,
                successMessage: "ログアウトしました。"
            });
            await this.$store.dispatch("auth/logout");
            this.switchMenu("top");
        },
        modalShow(val) {
            this.$refs.modal.modalShow(val);
        },
        menuClear() {
            // this.selectedContentsBg = "";
            this.selectedCountry = "";
            this.selectedCategory = "";
        },
        async switchMenu(selectVal) {
            if (selectVal == this.selectedMenu) {
                return;
            }
            if (selectVal == "category" || selectVal == "country") {
                this.selectedMenu = selectVal;
                return;
            }
            this.selectedMenu = selectVal;
            this.loading = true;

            if (selectVal == "likes") {
                this.loading = true;
                await this.$router
                    .push(`${this.routePath}/likes/${this.loginUser.id}`)
                    .then(() => {
                        this.menuClear();
                        this.scrollTop();
                        this.selectedContentsBg = this.selectedMenu;
                        this.loading = false;
                    })
                    .catch(error => {
                    });
            } else if (selectVal == "applies") {
                this.loading = true;
                await this.$router
                    .push(`${this.routePath}/applies/${this.loginUser.id}`)
                    .then(() => {
                        this.menuClear();
                        this.scrollTop();
                        this.selectedContentsBg = this.selectedMenu;
                        this.loading = false;
                    })
                    .catch(error => {
                    });
            }

            if (
                (this.selectedMenu == "category" &&
                    this.selectedCategory != "") ||
                (this.selectedMenu == "country" && this.selectedCountry != "")
            ) {
                this.selectedContentsBg = this.selectedMenu;
                return;
            }

            if (
                this.selectedMenu == "postJob" ||
                this.selectedMenu == "posts" ||
                this.selectedMenu == "appliesList"
            ) {
                await this.$router
                    .push(
                        `${this.routePath}/${this.selectedMenu}/${this.loginUser.company_id}`
                    )
                    .then(() => {
                        this.scrollTop();
                        this.menuClear();
                        this.selectedContentsBg = this.selectedMenu;
                        this.loading = false;
                    })
                    .catch(() => {});
            } else if (this.selectedMenu == "sendScout") {
                await this.$router
                    .push(`${this.routePath}/${this.selectedMenu}`)
                    .then(() => {
                        this.scrollTop();
                        this.menuClear();
                        this.selectedContentsBg = this.selectedMenu;
                        this.loading = false;
                    })
                    .catch(() => {});
            } else if (this.selectedMenu == "scoutList") {
                await this.$router
                    .push(
                        `${this.routePath}/${this.selectedMenu}/${this.loginUser.id}`
                    )
                    .then(() => {
                        this.scrollTop();
                        this.menuClear();
                        this.selectedContentsBg = this.selectedMenu;
                        this.loading = false;
                    })
                    .catch(() => {});
            } else if (this.selectedMenu == "top") {
                await this.$router
                    .push(`${this.routePath}/top`)
                    .then(() => {
                        this.menuClear();
                        this.scrollTop();
                        this.selectedContentsBg = this.selectedMenu;
                        this.loading = false;
                    })
                    .catch(() => {});
            } else if (this.selectedMenu == "profile") {
                await this.$router
                    .push(`${this.routePath}/profile/${this.loginUser.id}`)
                    .then(() => {
                        this.menuClear();
                        this.scrollTop();
                        this.selectedContentsBg = this.selectedMenu;
                        this.loading = false;
                    })
                    .catch(() => {});
            } else if (this.selectedMenu == "setting/country") {
                await this.$router
                    .push(`${this.routePath}/setting/country`)
                    .then(() => {
                        this.scrollTop();
                        this.menuClear();
                        this.selectedContentsBg = this.selectedMenu;
                        this.loading = false;
                    })
                    .catch(() => {});
            } else if (this.selectedMenu == "setting/category") {
                await this.$router
                    .push(`${this.routePath}/setting/category`)
                    .then(() => {
                        this.scrollTop();
                        this.menuClear();
                        this.selectedContentsBg = this.selectedMenu;
                        this.loading = false;
                    })
                    .catch(() => {});
            } else if (this.selectedMenu == "setting/users") {
                await this.$router
                    .push(`${this.routePath}/setting/users`)
                    .then(() => {
                        this.scrollTop();
                        this.menuClear();
                        this.selectedContentsBg = this.selectedMenu;
                        this.loading = false;
                    })
                    .catch(() => {});
            } else if (selectVal == "about") {
                await this.$router
                    .push(`${this.routePath}/about`)
                    .then(() => {
                        this.menuClear();
                        this.scrollTop();
                        this.selectedContentsBg = this.selectedMenu;
                        this.loading = false;
                    })
                    .catch(() => {});
            }

            if (this.loading) {
                this.loading = false;
            }
        },
        scrollTop: function() {
            window.scrollTo({
                top: 0
            });
        },
        changeMenuType: function(val) {
            if (this.selectedMenuType == "") {
                this.$refs.header.openDrawerMenu(val);
                this.selectedMenuType = val;
            } else if (this.selectedMenuType == val) {
                this.$refs.header.openDrawerMenu(val);
                this.selectedMenuType = "";
            } else {
                this.selectedMenuType = val;
            }
        },
        initData: async function() {
            var ready = await this.$store.dispatch("auth/initData");

            //初期の背景を設定
            if (this.$route.params.country != null) {
                for (let country of this.countries) {
                    if (country.country_name == this.$route.params.country) {
                        this.countryBgImage = country.country_image;
                        break;
                    }
                }
                this.selectedContentsBg = "country";
                this.selectedCountry = this.$route.params.country;
            } else if (this.$route.params.category != null) {
                this.selectedContentsBg = "category";
                this.selectedCategory = this.$route.params.category;
            }

            //初期ローダーを非表示
            if (this.init && ready) {
                this.init = false;
            }
        },
        changeBg: function() {
            this.changeBgShow = !this.changeBgShow;
        },
        //チャットの通信を開始
        startListen: function() {
            Echo.channel("chat").listen("MessageSent", e => {
                this.$store.commit("common/setLiveMessage", e);
            });
        }
    },
    watch: {
        modalTarget: function() {
            if (this.modalTarget == null) {
                return;
            }
            this.modalShow(this.modalTarget);
            this.$store.commit("common/setModalTarget", null);
        },

        countryReloadFlg: function(newVal, old) {
            if (newVal != old) {
                this.initData();
            }
        },
        bgClass: {
            handler: function(val, old) {
                this.changeBg();
                if (!this.changeBgFlg) {
                    var that = this;
                    setTimeout(function() {
                        that.changeBg();
                    }, 10);
                }
            },
            deep: true
        },
        loadingFlg: function(newVal, old) {
            this.loading = newVal;
        },
        liveMessage: function(val, old) {
            if (
                this.loginCheck &&
                this.loginUser != undefined &&
                this.loginUser.id == val.message.toId
            ) {
                this.$store.dispatch("auth/getNewMessageExistFlg");
            }
        }
    },
    computed: {
        routePath: function() {
            return this.$store.getters["auth/routePath"];
        },
        loadingFlg: function() {
            return this.$store.getters["common/loadingFlg"];
        },
        modalTarget: function() {
            return this.$store.state.common.modalTarget;
        },
        loginUser: function() {
            return this.$store.state.auth.user;
        },
        loginCheck: function() {
            return this.$store.state.auth.loginCheck;
        },
        newMessageFlg: function() {
            return this.$store.state.auth.newMessageExistFlg;
        },
        bgClass: function() {
            return {
                searchBg: this.selectedContentsBg == "postJob",
                categoryBg:
                    this.selectedContentsBg == "category" &&
                    this.selectedCategory != "",
                mainBg:
                    this.selectedContentsBg == "top" ||
                    this.selectedContentsBg == "posts" ||
                    this.selectedContentsBg == "postJob" ||
                    this.selectedContentsBg == "" ||
                    this.selectedContentsBg == null || //初期状態
                    this.selectedContentsBg == "profile" ||
                    this.selectedContentsBg == "likes" ||
                    this.selectedContentsBg == "applies" ||
                    this.selectedContentsBg == "appliesList" ||
                    this.selectedContentsBg == "setting/country" ||
                    this.selectedContentsBg == "setting/category" ||
                    this.selectedContentsBg == "setting/users" ||
                    this.selectedContentsBg == "scoutList" ||
                    this.selectedContentsBg == "sendScout" ||
                    this.selectedContentsBg == "about",
                countryBg:
                    this.selectedContentsBg == "country" &&
                    this.selectedCountry != ""
            };
        },
        countryBgImageStyle: function() {
            if (this.bgClass.countryBg) {
                return {
                    backgroundImage: "url('" + this.countryBgImage + "')"
                };
            }
        },
        countryReloadFlg: function() {
            return this.$store.state.common.settingCountryReloadFlg;
        },
        countries: function() {
            return this.$store.getters["auth/countries"];
        },
        categories: function() {
            return this.$store.getters["auth/categories"];
        },
        jobTypes: function() {
            return this.$store.getters["auth/jobTypes"];
        },
        placeData: function() {
            return this.$store.getters["auth/placeData"];
        },
        liveMessage: function() {
            return this.$store.state.common.liveMessage;
        }
    },
    created: function() {
        this.initData();
        this.scrollTop();
        this.startListen();
    },
    components: {
        modal,
        VueLoading,
        mailAlert,
        scrollTop
    },
    filters: {
        omittedText(text) {
            return text.length > 15 ? text.slice(0, 15) + "…" : text;
        }
    }
};
</script>

<style scoped>
.container {
    z-index: 5;
    height: 100%;
    padding-top: 100px;
    padding-bottom: 100px;
    position: relative;
}
.company-show-area {
    background: white;
    border-radius: 10px;
}
.menu-country,
.menu-category {
    overflow: scroll;
    height: 150px;
}

.menu-country li:hover,
.menu-category li:hover {
    background: rgb(45, 51, 117);
}

.drawer-menu {
    padding-top: 5px !important;
}
.drawer-menu li:hover {
    background: rgb(45, 51, 117);
}
.drawer-menu.setting li:hover {
    background-color: rgb(89 168 199);
}
.selectedBgInSetting {
    background-color: rgb(89 168 199);
}
.menu-title {
    padding: 5px 0px;
    padding-left: 10px;
    font-weight: bold;
    cursor: pointer;
    font-size: 18px;
}
.menu-title:hover {
    background: rgb(45, 51, 117);
}
.overlay:before {
    z-index: 0;
    position: fixed;
    content: "";
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    height: 100%;
}

/* 表示・非表示アニメーション中 */
.inMenu-enter-active {
    transition: ease-in-out 500ms;
}
.inMenu-leave-active {
    transition: ease-in-out 100ms;
}

/* 表示アニメーション開始時 ・ 非表示アニメーション後 */
.inMenu-enter,
.inMenu-leave-to,
.inMenu-leave {
    opacity: 0;
}

.inMenu-move {
    transition: ease-in-out 300ms;
}
.inMenu-leave-active {
    position: absolute;
}
.top-menu-option {
    margin-top: 65px;
}

/* 表示・非表示アニメーション中 */
.content-enter-active {
    transition: ease-in-out 500ms;
}
.content-leave-active {
    transition: ease-in-out 10ms;
}

.content-enter,
.content-leave-to,
.content-leave {
    opacity: 0;
}

.content-move {
    transition: ease-in-out 500ms;
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
    background-color: white;
    opacity: 0;
}

.initLoader-enter-active,
.initLoader-leave-active {
    transition: opacity 1s;
}

.initLoader-enter,
.initLoader-leave-to {
    opacity: 0;
}

.top-menu-option {
    margin-top: 65px;
}

.drawer-menu {
    -ms-overflow-style: none; /* IE, Edge 対応 */
    scrollbar-width: none; /* Firefox 対応 */
}
.drawer-menu::-webkit-scrollbar {
    /* Chrome, Safari 対応 */
    display: none;
}

.drawer-menu-wrapper {
    position: absolute;
    z-index: 1;
    top: 0;
    left: 0;
    width: 200px;
    height: 100%;
    background-color: rgb(36, 42, 121);
    padding-top: 50px;
    color: rgb(222, 211, 247);
}
.drawer-menu-wrapper.setting {
    background-color: #4894b3;
    color: white;
}
.drawer-menu {
    padding: 0px 0;
}
.drawer-menu li {
    cursor: pointer;
    font-size: 18px;
}
.selectedBg {
    background: rgb(45, 51, 117);
}
.selectedBg.setting {
    background-color: rgb(183, 221, 236);
}

.menu-btn {
    position: absolute;
    right: -50px;
    border-radius: 0px 20px 20px 0px;
    background-color: rgb(36, 42, 121);
    padding: 16px 5px;
    color: rgb(222, 211, 247);
    cursor: pointer;
}
.menu-btn.setting {
    background-color: #4894b3;
    top: 110px;
    color: white;
    padding: 16px 3.5px;
    font-size: 14px;
}

.mainBg {
    background-image: url("/images/hero_1.jpg") !important;
    min-height: 900px;
    background-position: center;
}

.categoryBg {
    background-image: url("/images/sq_img_3.jpg") !important;
    min-height: 900px;
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

.spinner-background {
    height: 100%;
    width: 100%;
    background: white;
    opacity: 0.5;
    z-index: 9999999;
    position: fixed;
}

.about {
    position: absolute;
    bottom: 0;
    width: 100%;
}

.about .menu-title {
    font-size: 13px;
}
</style>
<style>
.spinner-background {
    height: 100%;
    width: 100%;
    background: white;
    opacity: 0.5;
    z-index: 999999999;
    position: fixed;
}
.modal-mask {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    /* background-color:rgba(0,0,0,0.5); */
    background: white;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 999999999999;
}
</style>
