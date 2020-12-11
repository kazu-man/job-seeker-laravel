<template>
<div class="container">
        <div class="row align-items-center justify-content-center">
          <div class="col-md-12">
            <div class="mb-5 text-center">
              <h1 class="text-white font-weight-bold" v-if="searchInfo.pageType == 'country'">Job's in {{searchInfo.searchedBy}}</h1>
              <h1 class="text-white font-weight-bold" v-else-if="searchInfo.pageType == 'category'">Job's in {{searchInfo.searchedBy}}</h1>
              <h1 class="text-white font-weight-bold" v-else>JOB SEEKER</h1>
              <p class="text-white" style="opacity:0.8">Find your dream jobs in our career website.</p>
            </div>


<!-- form -->
              <div class="row mb-5">
<!-- title -->
                <div class="col-12 mb-4" :class="fullSearchBar">
                  <input type="text" name="title" class="form-control" placeholder="Job title, keywords..." v-model="searchInfo.keyWord">
                </div>
<!-- title -->
                <div class="col-12 col-sm-6 col-md-6  mb-4 mb-lg-0" v-if="searchInfo.pageType != 'country'">
<!-- place country, province-->
                  <place-show-component2 @changeSelectedPlace="changeSelectedPlace" v-model="searchInfo.city"></place-show-component2>
                </div>
<!-- type -->  
                <div class="col-12 col-sm-6 col-md-6  mb-4 mb-lg-0">
                  <select-box-component @changeSelectedVal="changeSelectedType" :target="'jobType'" v-model="searchInfo.jobTypeId"></select-box-component>
                </div>

<!-- categeory -->
                <div class="col-12 col-sm-6 col-md-6  mb-4 mb-lg-0" v-if="searchInfo.pageType != 'category'">
                  <select-box-component @changeSelectedVal="changeSelectedCategory" :target="'category'"></select-box-component>
                </div>
<!-- categeory -->

<!-- submit -->
                <div class="col-12 col-sm-6 col-md-6 col-lg-6 my-4 mx-auto mb-lg-0">
                  <button @click="refreshList" type="submit" name="search" class="btn btn-primary btn-lg btn-block text-white btn-search"><span class="icon-search icon mr-2"></span>Search Job</button>
                </div>
<!-- submit -->
              </div>
<!-- /form -->
<!-- /form -->
          <form action="" method="POST" class="text-center search-country-area" >
            <h3 class="text-white">Search by Country ?</h3>
            <a href="#" v-for="country in countries" @click="searchByCountry(country.country_name)" class="m-3 text-white" :key="country.id">{{country.country_name}}</a>
          </form>
<!-- /form -->
          </div>
        </div>

      <transition name="fade" appear>
        <postsListComponent 
          :searchInfo="searchInfo" 
          ref="postList"></postsListComponent>
      </transition>


      </div>
</template>

<script>
    import postsListComponent from "./PostsListComponent.vue";

    export default {
      data(){
        return {

        }
      },
      components:{
        postsListComponent
      },
      props:['searchInfo','countries'],
      methods: {
        changeSelectedCategory:function(val){
          console.log(val);
          this.searchInfo.categoryId = val;
        },
        changeSelectedPlace:function(val){
          console.log("val");
          console.log(val);
          this.searchInfo.cityId = "";
          this.searchInfo.provinceId = "";
          this.searchInfo.countryId = "";
          
          if(val.type == 'city'){
            this.searchInfo.cityId = val.id;
          }else if(val.type == 'province'){
            this.searchInfo.provinceId = val.id;
          }else if(val.type == 'country'){
            this.searchInfo.countryId = val.id;
          }
        },
        changeSelectedType:function(val){
          console.log(val);
          this.searchInfo.jobTypeId = val;
        },
        refreshList:function(){
          this.$refs.postList.getPostList();
        },
        searchByCountry:function(val) {
          this.$emit('changeCountry',val)
        },

      },
      computed:{
        fullSearchBar: function () {
          return {
            'col-sm-6': this.isSearchAreaFull,
            'col-md-6': this.isSearchAreaFull,
          }
        },
        isSearchAreaFull:function(){
          if(this.searchInfo.pageType != 'category' && this.searchInfo.pageType != 'country'){
            return true;
          }else{
            return false;
          }
        }
      }

    }
</script>

<style scoped>

    .container {
        padding-top:100px;
        z-index:100;
    }

    .fade-enter-active,
    .fade-leave-active {
      transition: opacity 200ms;
    }

    .fade-enter,
    .fade-leave-to {
      opacity: 0
    }
    .search-country-area {
      word-break:break-all;
      padding:30px 0px 100px 0px;
    }


</style>

