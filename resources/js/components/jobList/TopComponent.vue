<template>
    <div class="container">
        <div class="row align-items-center justify-content-center">
            <div class="col-md-12">
                <div class="mb-5 text-center">
                    <h1
                        class="text-white font-weight-bold"
                        v-if="initPage == 'country'"
                    >
                        Job's in {{ this.$route.params.country }}
                    </h1>
                    <h1
                        class="text-white font-weight-bold"
                        v-else-if="initPage == 'category'"
                    >
                        Job's in {{ this.$route.params.category }}
                    </h1>
                    <h1 class="text-white font-weight-bold" v-else>
                        JOB SEEKER
                    </h1>
                    <p class="text-white" style="opacity:0.8">
                        Find your dream jobs in our career website.
                    </p>
                </div>

                <div class="row mb-5 search-area">
                    <div class="col-12 mb-4" :class="fullSearchBar">
                        <input
                            type="text"
                            name="title"
                            class="form-control"
                            placeholder="Job title, keywords..."
                            v-model="searchKeys.keyWord"
                        />
                    </div>
                    <div
                        class="col-12 col-sm-6 col-md-6  mb-4 mb-lg-0"
                        v-if="initPage != 'country'"
                    >
                        <place-show-component2
                            @changeSelectedPlace="changeSelectedPlace"
                            v-model="searchKeys.city"
                            :placeData="placeData"
                        ></place-show-component2>
                    </div>
                    <div
                        class="col-12 col-sm-6 col-md-6  mb-4 mb-lg-0"
                        style="margin-bottom:1.5rem!important"
                    >
                        <select-box-component
                            @changeSelectedVal="changeSelectedType"
                            :target="'jobType'"
                            v-model="searchKeys.jobTypeId"
                            :baseData="jobTypes"
                        ></select-box-component>
                    </div>

                    <div
                        class="col-12 col-sm-6 col-md-6  mb-4 mb-lg-0"
                        v-if="initPage != 'category'"
                        style="margin-bottom:1.5rem!important"
                    >
                        <select-box-component
                            @changeSelectedVal="changeSelectedCategory"
                            :target="'category'"
                            v-model="searchKeys.jobTypeId"
                            :baseData="categories"
                        ></select-box-component>
                    </div>

                    <div class="form-group" style="width:90%;margin:auto">
                        <label style="color:white">
                            詳細検索
                        </label>
                        <select-tag-component
                            ref="selectTagComponent"
                        ></select-tag-component>
                    </div>

                    <div
                        class="col-12 col-sm-6 col-md-6 col-lg-6 my-4 mx-auto mb-lg-0"
                        style="margin-top: 5px !important;"
                    >
                        <button
                            @click="refreshList"
                            type="submit"
                            name="search"
                            class="btn btn-primary btn-lg btn-block text-white btn-search"
                        >
                            <span class="icon-search icon mr-2"></span>Search
                            Job
                        </button>
                    </div>
                </div>
                <form
                    action=""
                    method="POST"
                    class="text-center search-country-area"
                >
                    <h3 class="text-white">Search by Country ?</h3>
                    <a
                        href="#"
                        v-for="country in countries"
                        @click="searchByCountry(country.country_name)"
                        class="m-3 text-white seach-by-country"
                        :key="country.id"
                        >{{ country.country_name }}</a
                    >
                </form>
            </div>
        </div>

        <transition name="fade" appear>
            <postsListComponent
                :initFlg="false"
                :initPage="initPage"
                ref="postList"
            ></postsListComponent>
        </transition>
    </div>
</template>

<script>
import postsListComponent from "./PostsListComponent.vue";

export default {
    data() {
        return {
            searchKeys: {
                cityId: "",
                countryId: "",
                provinceId: "",
                categoryId: "",
                jobTypeId: "",
                keyWord: "",
                tagList: ""
            },
            mountedOK: false
        };
    },
    components: {
        postsListComponent
    },
    props: ["countries", "initPage", "placeData", "categories", "jobTypes"],
    methods: {
        changeSelectedCategory: function(val) {
            this.searchKeys.categoryId = val;
        },
        changeSelectedPlace: function(val) {
            this.searchKeys.cityId = "";
            this.searchKeys.provinceId = "";
            this.searchKeys.countryId = "";

            if (val.type == "city") {
                this.searchKeys.cityId = val.id;
            } else if (val.type == "province") {
                this.searchKeys.provinceId = val.id;
            } else if (val.type == "country") {
                this.searchKeys.countryId = val.id;
            }
        },
        changeSelectedType: function(val) {
            this.searchKeys.jobTypeId = val;
        },
        refreshList: function() {
            this.searchKeys.tagList = this.$refs.selectTagComponent.selectedTagList;
            this.$refs.postList.commitSearchKeys(this.searchKeys);
            // this.$refs.postList.getPostList();
        },
        searchByCountry: function(val) {
            this.$emit("changeCountry", val);
        },
        searchKeysClear: function() {
            Object.keys(this.searchKeys).forEach(val => {
                this.searchKeys[val] = "";
            });
        }
    },
    computed: {
        fullSearchBar: function() {
            return {
                "col-sm-6": this.isSearchAreaFull,
                "col-md-6": this.isSearchAreaFull
            };
        },
        isSearchAreaFull: function() {
            if (this.initPage != "category" && this.initPage != "country") {
                return true;
            } else {
                return false;
            }
        },
        route: function() {
            return this.$route.params;
        }
    },
    watch: {
        initPage: function() {
            this.searchKeysClear();
        },
        route: function() {
            this.searchKeysClear();
        }
    }
};
</script>

<style scoped>
.container {
    padding-top: 100px;
    z-index: 100;
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 200ms;
}

.fade-enter,
.fade-leave-to {
    opacity: 0;
}
.search-country-area {
    word-break: break-all;
    padding: 30px 0px 100px 0px;
}
.search-area {
    width: 80%;
    margin: 0 auto;
}
.seach-by-country {
    word-break: break-all;
}
</style>
