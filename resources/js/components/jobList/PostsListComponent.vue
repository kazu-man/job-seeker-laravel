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
            <span @click="interviewModal()" class="calendar-span">
                <div>
                    <img src="/images/calendar.png" class="calendar" />
                    <div style="font-size: 15px;">interviews</div>
                </div>
            </span>
        </div>

        <div class="title" v-if="searchInfo.pageType == 'scout'" v-cloak>
            {{ count }} Scouts
        </div>
        <div class="title" v-else-if="searchInfo.pageType != 'applies'" v-cloak>
            {{ count }} Jobs found
        </div>

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
                @plusPostNumInDisplay="plusPostNumInDisplay"
                style="width:100%"
            ></posts-component>
        </transition-group>

        <transition>
            <spinner
                v-if="loading"
                style="
                z-index: 99999999;
            "
                size="40"
                line-fg-color="#f00"
            ></spinner>
        </transition>
    </section>
</template>

<script>
import anime from "animejs";

export default {
    data() {
        return {
            posts: {},
            userType: "user",
            count: 0,
            pageType: "",
            hidePost: [],
            loading: true,
            searchInfo: {
                pageType: "",
                searchedBy: "",
                companyId: "",
                countryName: "",
                categoryName: "",
                categoryId: "",
                likes: false,
                applies: false,
                cityId: "",
                countryId: "",
                provinceId: "",
                categoryId: "",
                jobTypeId: "",
                keyWord: "",
                postInitFlg: false
            },
            postsInDisplay: 1
        };
    },
    methods: {
        countUp: function(targetNum) {
            const obj = { n: 0 };
            anime({
                targets: obj,
                n: targetNum,
                round: 1,
                duration: 2000,
                easing: "easeInOutSine",
                update: () => {
                    this.count = obj.n;
                }
            });
        },
        getPostList: function(page = 1) {
            this.count = 0;
            //再建策の場合は初期化
            if (page == 1) {
                this.posts = {};
                this.postsInDisplay = 1;
            }
            this.loading = true;
            axios
                .post("/api/getPosts?page=" + page, this.searchInfo)
                .then(res => {
                    if (page > 1) {
                        this.posts = this.posts.concat(res.data.data);
                    } else {
                        this.posts = res.data.data;
                    }
                    if (this.searchInfo.pageType == "applies") {
                        this.posts.forEach(post => {
                            var newMessageFlg = false;
                            for (var message of post.apply_records[0]
                                .messages) {
                                if (
                                    message.checked == 0 &&
                                    message.sent_to == "U"
                                ) {
                                    newMessageFlg = true;
                                }
                            }
                            post["newMessageFlg"] = newMessageFlg;
                        });
                    }
                    this.countUp(res.data.total);
                    this.loading = false;
                })
                .catch(res => {
                    if (store.getters["auth/routePath"] == undefined) {
                        window.location.reload();
                    } else {
                        window.location.href = store.getters["auth/routePath"];
                    }
                });
        },
        init: function() {
            this.getPostList();
        },
        addHidePost: function(id) {
            this.hidePost.push(id);
        },
        minusCount: function() {
            this.count -= 1;
        },
        switchList: function() {
            if (this.initPage == "likes") {
                this.searchInfoClear();
                this.searchInfo.likes = true;
                this.searchInfo.pageType = "likes";
            } else if (this.initPage == "applies") {
                this.searchInfoClear();
                this.searchInfo.applies = true;
                this.searchInfo.pageType = "applies";
            } else if (this.initPage == "posts") {
                this.searchInfoClear();
                this.searchInfo.companyId = this.loginUser.company_id;
                this.searchInfo.pageType = "posts";
            } else if (this.initPage == "top") {
                this.searchInfoClear();
            } else if (this.initPage == "country") {
                this.searchInfoClear();
                this.searchInfo.pageType = "country";
                this.searchInfo.searchedBy = this.$route.params.country;
                this.searchInfo.countryName = this.$route.params.country;
            } else if (this.initPage == "category") {
                this.searchInfoClear();
                this.searchInfo.pageType = "category";
                this.searchInfo.searchedBy = this.$route.params.category;
                this.searchInfo.categoryName = this.$route.params.category;
            } else if (this.initPage == "scout") {
                this.searchInfoClear();
                this.searchInfo.pageType = "scout";
            }

            this.getPostList();
        },
        commitSearchKeys: function(searchKeys) {
            Object.entries(searchKeys).forEach(([infoKey]) => {
                this.searchInfo[infoKey] = searchKeys[infoKey];
            });

            this.getPostList();
        },
        searchInfoClear: function() {
            Object.keys(this.searchInfo).forEach(val => {
                this.searchInfo[val] = "";
            });
            this.searchInfo.likes = false;
            this.searchInfo.applies = false;
        },
        interviewModal: function() {
            this.$store.dispatch("common/setInterviewModal");
        },
        //100個づつ表示し、一番下までスクロールされたことを検知
        plusPostNumInDisplay() {
            this.postsInDisplay++;
            var lastFlg = this.postsInDisplay % 90;
            if (lastFlg == 0) {
                var nextPage = this.postsInDisplay / 90 + 1;
                this.getPostList(nextPage);
            }
        }
    },
    components: {
        anime // animeという名前でコンポーネント登録
    },
    mounted: function() {
        this.switchList();
    },
    update: {},
    props: ["initPage", "initFlg"],
    watch: {
        updatedPost: function() {
            if (this.updatedPost == null || this.updatedPost == undefined) {
                return;
            }

            for (let post of this.posts) {
                if (post.id == this.updatedPost.id) {
                    Object.entries(post).forEach(([key]) => {
                        post[key] = this.updatedPost[key];
                    });

                    break;
                }
            }
        },
        pagePath: function() {
            this.switchList();
        }
    },
    computed: {
        likeList: function() {
            return this.$store.state.auth.likeList;
        },
        applyList: function() {
            return this.$store.state.auth.applyList;
        },
        updatedPost: function() {
            return this.$store.state.common.updatedPost;
        },
        pagePath: function() {
            return this.$route.path;
        },
        loginUser: function() {
            return this.$store.state.auth.user;
        }
    }
};
</script>

<style scoped>
.title {
    padding-top: 20px;
    color: white;
    text-align: center;
    font-size: 20px;
}
/* .post-enter, */
.post-leave-to,
.post-leave {
    opacity: 0;
}
/* .post-enter-active, */
.post-leave-active {
    width: 100%;
    position: absolute;
}
.post-move {
    transition: transform 500ms;
}

.calendar {
    width: 30px;
}
.calendar-span {
    float: right;
    font-weight: normal;
    text-align: center;
    position: absolute;
    top: -3px;
    right: 0px;
    cursor: pointer;
}

[v-cloak] {
    display: none;
}
</style>
