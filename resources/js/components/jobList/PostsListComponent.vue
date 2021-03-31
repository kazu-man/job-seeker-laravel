<template>
    <section style="min-height:500px;width:100%;position:relative">
        <div class="title" v-if="searchInfo.pageType == 'posts'">
            Your Post's
        </div>
        <div class="title" v-else-if="searchInfo.pageType == 'likes'">
            Liked Post's
        </div>
        <div class="title" v-else-if="searchInfo.pageType == 'applies'">
            Your applies
        </div>

        <div class="title" v-if="searchInfo.pageType != 'applies'" v-cloak>
            {{count}} Jobs found
        </div>
        <spinner v-if="loading" style="
            position:absolute;
            top:35%;
            left:48%;
            z-index: 99999999;
        " size="40"
        line-fg-color="#f00"></spinner>

        <transition-group name="post">
            <posts-component 
            v-for="post in posts" 
            v-show="!hidePost.includes(post.id)"
            :post="post" 
            :likeList="likeList" 
            :applyList="applyList" 
            :key="post.id"
            :likePageOrNot="searchInfo.likes"
            :pageType="searchInfo.pageType"
            @hidePost="addHidePost"
            @minusCount="minusCount"
            style="width:100%"
            ></posts-component>
        </transition-group>

    </section>
</template>


<script>
import anime from 'animejs' ;

export default {
    data() {return{
        posts:{},
        userType:"user",
        count:0,
        pageType:"",
        hidePost:[],
        loading:true,
        searchInfo:{
            pageType:"",
            searchedBy:"",
            companyId:"",
            countryName:"",
            categoryName:"",
            categoryId:"",
            likes:false,
            applies:false,
            cityId:"",
            countryId:"",
            provinceId:"",
            categoryId:"",
            jobTypeId:"",
            keyWord:"",
            postInitFlg:false
        },

    }},
    methods: { 
        countUp: function(targetNum){
            const obj = { n: 0 };
            anime({
                targets: obj,
                n: targetNum,
                round: 1,
                duration: 2000,
                easing: 'easeInOutSine',
                update: () => {
                    this.count = obj.n;
                }
            })
        },
        getPostList:async function(){

            console.log("function getPostList");
            console.log(this.searchInfo);
            this.count = 0;
            this.posts = {};
            this.loading = true;
            await axios.post('/api/getPosts', this.searchInfo).then(res => {
                this.posts = res.data;
                if(this.searchInfo.pageType == 'applies'){
                    this.posts.forEach(post => {
                        var newMessageFlg = false;
                        console.log("post")
                        for(var message of post.apply_records[0].messages){
                            if(message.checked == 0 && message.sent_to == "U"){
                                newMessageFlg = true; 
                            }
                        }
                        post["newMessageFlg"] = newMessageFlg;
                    });
                }
                console.log('getPost');
                console.log(res.data);
                this.countUp(this.posts.length);
                this.loading = false;
            })
            .catch(res => {
                  window.location.href = store.getters['auth/routePath'];
            });
        },
        init:function(){
            this.getPostList();
        },
        addHidePost:function(id){
            this.hidePost.push(id);
        },
        minusCount:function(){
            this.count -= 1;
        },
        switchList:function(){

            if(this.initPage == 'likes' ){
                this.searchInfoClear();
                this.searchInfo.likes = true;
                this.searchInfo.pageType = "likes";

            }else if(this.initPage == 'applies'){
                this.searchInfoClear();
                this.searchInfo.applies = true;
                this.searchInfo.pageType = "applies";

            }else if(this.initPage == 'posts'){
                this.searchInfoClear();
                this.searchInfo.companyId = this.loginUser.company_id;
                this.searchInfo.pageType = 'posts';

            }else if(this.initPage == 'top'){
                this.searchInfoClear();
            }else if(this.initPage == 'country'){
                this.searchInfoClear();
                this.searchInfo.pageType = "country";
                this.searchInfo.searchedBy = this.$route.params.country;
                this.searchInfo.countryName = this.$route.params.country;
            }else if(this.initPage == 'category'){
                this.searchInfoClear();
                this.searchInfo.pageType = "category";
                this.searchInfo.searchedBy = this.$route.params.category;
                this.searchInfo.categoryName = this.$route.params.category;

            }
            this.getPostList();
        },
        commitSearchKeys:function(searchKeys){
                this.searchInfo.cityId = searchKeys.cityId
                this.searchInfo.countryId = searchKeys.countryId
                this.searchInfo.provinceId = searchKeys.provinceId
                this.searchInfo.categoryId = searchKeys.categoryId
                this.searchInfo.jobTypeId = searchKeys.jobTypeId
                this.searchInfo.keyWord = searchKeys.keyWord
                this.getPostList();
        },
        searchInfoClear:function(){
                this.searchInfo.pageType = ""
                this.searchInfo.searchedBy = ""
                this.searchInfo.companyId = ""
                this.searchInfo.countryName = ""
                this.searchInfo.categoryName = ""
                this.searchInfo.categoryId = ""
                this.searchInfo.likes = false
                this.searchInfo.applies = false
                this.searchInfo.cityId = ""
                this.searchInfo.countryId = ""
                this.searchInfo.provinceId = ""
                this.searchInfo.categoryId = ""
                this.searchInfo.jobTypeId = ""
                this.searchInfo.keyWord = ""
        },
        
    },
    components: {
        anime // animeという名前でコンポーネント登録
    },
    mounted: function(){
        this.switchList();
    },
    update:{

    },
    props:["initPage","initFlg"],
    watch:{
        updatedPost:function(){
            if(this.updatedPost == null || this.updatedPost == undefined){
                return;
            }

            for(let post of this.posts){

                if(post.id == this.updatedPost.id){

                    post.id = this.updatedPost.id;
                    post.job_title = this.updatedPost.job_title;
                    post.annual_salary = this.updatedPost.annual_salary;
                    post.job_description.description = this.updatedPost.job_description.description;
                    post.job_description.requirement = this.updatedPost.job_description.requirement;
                    post.job_description.benefit = this.updatedPost.job_description.benefit;
                    post.job_description.experience = this.updatedPost.job_description.experience;
                    post.city = this.updatedPost.city;
                    post.category = this.updatedPost.category;
                    post.job_type = this.updatedPost.job_type;
                    post.job_status = this.updatedPost.job_status;
                    post.job_tag_relations = this.updatedPost.job_tag_relations;

                    break;
                }
            }
        },
        pagePath:function(){
            this.switchList();
        }
    },
    computed:{
        likeList:function(){
            return this.$store.state.auth.likeList;
        },
        applyList:function(){
            return this.$store.state.auth.applyList;
        },
        updatedPost:function(){
            return this.$store.state.common.updatedPost;
        },
        pagePath:function(){
            return this.$route.path;
        },
        loginUser:function() {
            return this.$store.state.auth.user;
        },
    }

}
</script>


<style scoped>
.title {
    padding-top:20px;
    color:white;
    text-align:center;
    font-size:20px;
}
/* .post-enter, */
.post-leave-to,
.post-leave {
  opacity: 0
}
/* .post-enter-active, */
.post-leave-active {
    width:100%;
    position:absolute; 
}
.post-move {
  transition: transform 500ms;
}

[v-cloak] {
  display: none;
}
</style>