<template>
    <div id="post">
        <transition name="fade" appear>
            <section
                class="content post"
                :class="{
                    singlePostMargin: onSinglePost,
                    closedBackground: isClosedPost
                }"
                v-if="visible"
                style="position:relative"
            >
                <div
                    v-if="pageType == 'applies'"
                    style="position:absolute;top: -14px;right: -5px;"
                >
                    <transition name="badge">
                        <span
                            v-if="post.newMessageFlg && !newMessageClose"
                            class="badge new-message-badge"
                            >new message</span
                        >
                    </transition>
                </div>
                <div v-if="isClosedPost" class="close-badge-position">
                    <transition name="badge">
                        <span class="badge badge-danger closed-badge"
                            >Closed</span
                        >
                    </transition>
                </div>

                <transition-group name="content">
                    <div
                        class="post-header row"
                        @click="toggleSlide"
                        :key="'header'"
                    >
                        <div class="col-2 pc-view">
                            <div
                                class="p-2 d-inline-block mr-5 rounded-circle company-logo"
                                style="margin: 16% auto auto auto !important;"
                                :class="{ bgBlue: openFlg }"
                            >
                                <img
                                    v-if="
                                        post.company.company_image !=
                                            undefined &&
                                            post.company.company_image !=
                                                null &&
                                            post.company.company_image != ''
                                    "
                                    :src="post.company.company_image"
                                    class="m-auto p-auto image rounded-circle"
                                    alt="image"
                                    style="width:100px;height:100px"
                                />
                                <img
                                    v-else
                                    :src="defaultLogo"
                                    class="m-auto p-auto image rounded-circle"
                                    alt="image"
                                    style="width:100px;height:100px"
                                />
                            </div>
                        </div>

                        <div class="col-md-10 col-sm-10 row">
                            <div class="col-1 pt-1 pl-0">
                                <span
                                    class="badge px-2 py-1 mb-3"
                                    :class="{
                                        'badge-warning':
                                            post.job_type.job_type ==
                                            'FullTime',
                                        'badge-primary':
                                            post.job_type.job_type ==
                                            'Fleerance',
                                        'badge-success':
                                            post.job_type.job_type == 'PartTime'
                                    }"
                                    >{{ post.job_type.job_type }}</span
                                >
                            </div>

                            <div
                                class="col-7 pt-2 text-right post-country-show"
                            >
                                <span class="pc-view"
                                    >{{ post.city.province.province_name }} -
                                    {{ post.city.city_name }}<br
                                /></span>
                                <strong>{{
                                    post.city.province.country.country_name
                                }}</strong>
                            </div>

                            <div class="col-3 pt-2 text-right post-salary">
                                <strong class="text-black"
                                    >{{ post.annual_salary }}/Month</strong
                                >
                            </div>

                            <div class="col-12 post-title">
                                <!-- <span class="badge badge-primary px-2 py-1 mb-3">FREELANCE</span> -->
                                <h4>
                                    <a target="_blank">{{ post.job_title }}</a>
                                </h4>
                            </div>

                            <div class="col-12 post-info">
                                <p class="meta mb-0">
                                    Publisher:
                                    <strong>{{
                                        post.company.company_name
                                    }}</strong>
                                    In:
                                    <strong>{{
                                        post.category.category_name
                                    }}</strong>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div
                        v-if="openFlg"
                        class="toggle-down-area row"
                        :class="{
                            singlePostHeight: onSinglePost,
                            singlePostMargin: onSinglePost
                        }"
                        :key="'content'"
                    >
                        <div class="tagShowArea">
                            <tag-component
                                v-for="tagRelation in post.job_tag_relations"
                                :key="tagRelation.tag.id"
                                :value="tagRelation.tag.id"
                                :tag="tagRelation.tag"
                                :active="false"
                            >
                            </tag-component>
                        </div>
                        <div class="post-buttons">
                            <div v-if="pageType == 'applies'" class="buttons">
                                <div class="row">
                                    <div
                                        class="col-6 text-right"
                                        style="padding-right:0"
                                    ></div>
                                    <div class="col-6">
                                        <button
                                            @click.stop="showMessage"
                                            name="like"
                                            class="btn btn-block btn-info btn-md p-2"
                                        >
                                            <span
                                                class="icon-heart-o mr-2"
                                            ></span
                                            >Messages
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div
                                v-else-if="
                                    !this.$store.state.auth.loginCheck ||
                                        this.$store.state.auth.user.user_type ==
                                            'U'
                                "
                                class="buttons"
                            >
                                <div class="row">
                                    <div class="col-6">
                                        <button
                                            v-if="!likedOrNot"
                                            @click.stop="addLike(post.id)"
                                            name="like"
                                            class="btn btn-block btn-warning btn-md p-2 text-danger"
                                        >
                                            <span
                                                class="icon-heart-o mr-2 text-danger"
                                            ></span
                                            >Save Job
                                        </button>
                                        <button
                                            v-else
                                            @click.stop="removeLike(post.id)"
                                            name="like"
                                            class="btn btn-block btn-warning btn-md p-2 text-danger"
                                        >
                                            <span
                                                class="icon-heart-o mr-2 text-danger"
                                            ></span
                                            >Remove
                                        </button>
                                    </div>
                                    <div class="col-6">
                                        <button
                                            v-if="!appliedOrNot"
                                            type="button"
                                            @click.stop="apply()"
                                            class="btn btn-primary p-2 btn-block text-white"
                                            data-toggle="modal"
                                            data-target="#apply_modal"
                                        >
                                            Apply
                                        </button>
                                        <button
                                            v-else
                                            type="button"
                                            class="btn btn-primary p-2 btn-block text-white"
                                            data-toggle="modal"
                                            disabled
                                        >
                                            Applied
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div
                                v-else-if="this.pageType == 'posts'"
                                class="buttons"
                            >
                                <div class="row">
                                    <div class="col-6">
                                        <button
                                            @click.stop="setEditForm()"
                                            name="like"
                                            class="btn btn-block btn-success btn-md p-2"
                                        >
                                            <span
                                                class="icon-heart-o mr-2 text-danger"
                                            ></span
                                            >Edit
                                        </button>
                                    </div>
                                    <div class="col-6">
                                        <button
                                            v-if="isClosedPost"
                                            @click.stop="setPostCloseModal()"
                                            name="like"
                                            class="btn btn-block btn-danger btn-md p-2"
                                        >
                                            <span
                                                class="icon-heart-o mr-2 text-danger"
                                            ></span
                                            >Open
                                        </button>
                                        <button
                                            v-else
                                            @click.stop="setPostCloseModal()"
                                            name="like"
                                            class="btn btn-block btn-danger btn-md p-2"
                                        >
                                            <span
                                                class="icon-heart-o mr-2 text-danger"
                                            ></span
                                            >Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="post-main col-lg-8">
                            <div
                                class="video-area"
                                v-if="
                                    videoOptions.sources[0].src != undefined &&
                                        videoOptions.sources[0].src != ''
                                "
                            >
                                <div style="width:100%; padding: 0 1%;">
                                    <div
                                        v-if="videoReload"
                                        class="video-area video-area-loading"
                                    >
                                        <spinner
                                            style="
                                                position: absolute;
                                                left: 47%;
                                                top: 45%;
                                            "
                                            size="20"
                                            line-fg-color="#f00"
                                        ></spinner>
                                    </div>
                                    <video-player
                                        v-else
                                        :options="videoOptions"
                                    ></video-player>
                                </div>
                            </div>

                            <div class="mb-5">
                                <h3
                                    class="h5 d-flex align-items-center mb-4 text-primary"
                                >
                                    <span class="icon-align-left mr-3"></span>
                                    Description
                                </h3>
                                <p class="longText">
                                    {{ post.job_description.description }}
                                </p>
                            </div>

                            <div class="mb-5">
                                <h3
                                    class="h5 d-flex align-items-center mb-4 text-primary"
                                >
                                    <span class="icon-align-left mr-3"></span>
                                    Requirements
                                </h3>
                                <p class="longText">
                                    {{ post.job_description.requirement }}
                                </p>
                            </div>

                            <div class="mb-5">
                                <h3
                                    class="h5 d-flex align-items-center mb-4 text-primary"
                                >
                                    <span class="icon-align-left mr-3"></span>
                                    Experience
                                </h3>
                                <p class="longText">
                                    {{ post.job_description.experience }}
                                </p>
                            </div>

                            <div class="mb-5">
                                <h3
                                    class="h5 d-flex align-items-center mb-4 text-primary"
                                >
                                    <span class="icon-align-left mr-3"></span>
                                    Other Benefits
                                </h3>
                                <p class="longText">
                                    {{ post.job_description.benefit }}
                                </p>
                            </div>
                        </div>

                        <div
                            class="right-area col-lg-4 "
                            style="float:right;margin-top: 10px;"
                        >
                            <div class="basic-info bg-light">
                                <div class="text-primary h5 mb-3">
                                    Job Sammary
                                </div>
                                <div class="h6">
                                    Published On:
                                    <span class="font-gray"></span>
                                </div>
                                <div class="h6">
                                    Employment Status:
                                    <span class="font-gray">{{
                                        post.job_type.job_type
                                    }}</span>
                                </div>
                                <div class="h6">
                                    Job Location:
                                    <span class="font-gray"
                                        >{{
                                            post.city.province.province_name
                                        }}
                                        -
                                        {{
                                            post.city.province.country
                                                .country_name
                                        }}</span
                                    >
                                </div>
                                <div class="h6">
                                    Salary :
                                    <span class="font-gray"
                                        >{{ post.annual_salary }}/Month</span
                                    >
                                </div>
                            </div>
                            <div class="reviews"></div>
                        </div>

                        <div
                            v-if="openFlg && post.address != null"
                            style="margin-left:5%"
                            class="address-line"
                        >
                            {{
                                post.address.address_line_1 != null &&
                                post.address.address_line_1 != ""
                                    ? post.address.address_line_1
                                    : ""
                            }}
                            {{
                                post.address.address_line_2 != null &&
                                post.address.address_line_1 != ""
                                    ? ", " + post.address.address_line_2
                                    : ""
                            }}
                            {{
                                post.address.city != null &&
                                post.address.address_line_1 != ""
                                    ? ", " + post.address.city
                                    : ""
                            }}
                            {{
                                post.address.country != null &&
                                post.address.address_line_1 != ""
                                    ? ", " + post.address.country
                                    : ""
                            }}
                            {{
                                post.address.zip_code != null &&
                                post.address.address_line_1 != ""
                                    ? ", " + post.address.zip_code
                                    : ""
                            }}
                        </div>

                        <GmapMap
                            v-if="openFlg && post.address != null"
                            :center="{
                                lat: post.address.lat,
                                lng: post.address.lng
                            }"
                            :zoom="16"
                            map-type-id="roadmap"
                            style="width: 90%; height: 300px; margin:0 auto 30px auto;"
                        >
                            <GmapMarker
                                :key="index"
                                v-for="(m, index) in [
                                    {
                                        position: {
                                            lat: post.address.lat,
                                            lng: post.address.lng
                                        }
                                    }
                                ]"
                                :position="m.position"
                                :clickable="false"
                                :draggable="false"
                                @click="center = m.position"
                            />
                        </GmapMap>
                    </div>
                </transition-group>
            </section>
        </transition>
    </div>
</template>

<script>
import VideoPlayer from "../common/VideoPlayer.vue";

export default {
    data() {
        return {
            openFlg: false,
            defaultLogo: "/images/search.jpg",
            visible: true,
            top: "",
            screenHeight: "",
            initFlg: true,
            hidePost: false,
            editForm: {
                id: "",
                job_title: "",
                annual_salary: "",
                type: "",
                category: "",
                city: "",
                description: "",
                requirement: "",
                benefit: "",
                experience: ""
            },
            newMessageClose: false,
            videoOptions: {
                autoplay: false,
                controls: true,
                fluid: true,
                aspectRatio: "16:9",
                sources: [
                    {
                        src: "",
                        type: "application/x-mpegURL"
                    }
                ]
            },
            defaultAddressObj: {
                address_line_1: "",
                address_line_2: "",
                city: "",
                state: "",
                zip_code: "",
                country: ""
            },
            videoReload: false
        };
    },
    methods: {
        toggleSlide: function() {
            if (this.toggleDisable != null && this.toggleDisable == true) {
                return;
            }
            console.log("truenisuru");
            this.openFlg = !this.openFlg;
        },
        handleScroll() {
            if (this.visible && !this.initFlg) {
                return;
            }
            this.top = this.$el.getBoundingClientRect().top;
            this.screenHeight = window.innerHeight;
            this.visible = this.top < this.screenHeight;
        },
        init() {
            window.addEventListener("scroll", this.handleScroll);
            this.handleScroll();
            this.initFlg = false;
            if (this.toggleDisable != null && this.toggleDisable == true) {
                this.openFlg = true;
            }
            this.videoOptions.sources[0].src = this.post.video_url;
        },
        addLike(jobId) {
            if (!this.$store.state.auth.loginCheck) {
                this.$store.commit("common/setModalTarget", "login");
                return;
            }
            console.log(jobId);
            // this.toggleSlide();
            this.$store.commit("common/setLoadingFlg", true);
            axios.post("/api/addLike", { jobId: jobId }).then(res => {
                this.$store.commit("common/setLoadingFlg", false);
                this.$store.dispatch("common/alertModalUp", {
                    data: res.status,
                    successMessage: "likeリストに追加しました。"
                });
                this.$store.dispatch("auth/getLikeList");
            });
        },
        removeLike(jobId) {
            if (!this.$store.state.auth.loginCheck) {
                this.$store.commit("common/setModalTarget", "login");
                return;
            }

            console.log(jobId);
            // this.toggleSlide();
            this.$store.commit("common/setLoadingFlg", true);
            axios.post("/api/removeLike", { jobId: jobId }).then(res => {
                this.$store.commit("common/setLoadingFlg", false);
                this.$store.dispatch("common/alertModalUp", {
                    data: res.status,
                    successMessage: "likeリストから削除しました。"
                });
                this.$store.dispatch("auth/getLikeList");

                if (this.likePageOrNot) {
                    this.$emit("hidePost", this.post.id);
                    this.$emit("minusCount");
                }
            });
        },

        setEditForm: function() {
            this.editFlg = !this.editFlg;

            //オブジェクトをコピー
            this.editForm = JSON.parse(JSON.stringify(this.post));

            this.editForm.description = this.post.job_description.description;
            this.editForm.requirement = this.post.job_description.requirement;
            this.editForm.benefit = this.post.job_description.benefit;
            this.editForm.experience = this.post.job_description.experience;
            this.editForm.type = this.post.job_type.id;
            this.editForm.category = this.post.category.id;

            var tagList = [];
            for (var tagRelation of this.post.job_tag_relations) {
                tagList.push(tagRelation.tag);
            }
            this.editForm.tagList = tagList;

            this.editForm.addressObj =
                this.post.address != null
                    ? this.post.address
                    : this.defaultAddressObj;
            this.videoOptions.sources[0].src = this.post.video_url;
            this.$store.dispatch("common/setEditForm", this.editForm);
        },
        apply: function() {
            if (!this.$store.state.auth.loginCheck) {
                this.$store.commit("common/setModalTarget", "login");
                return;
            }

            this.$store.dispatch("common/setApplyModal", this.post);
        },
        showMessage: function() {
            this.newMessageClose = true;
            this.$store.commit("common/setLoadingFlg", true);

            axios
                .get(
                    "/api/getSingleApplyRecord/" + this.post.apply_records[0].id
                )
                .then(res => {
                    this.$store.dispatch("common/setMessageModal", res.data);
                });
        },
        setPostCloseModal: function() {
            this.$store.dispatch("common/setPostCloseModal", this.post);
        }
    },
    mounted() {
        this.init();
        console.log("mounted!!");
    },
    destroyed() {
        window.removeEventListener("scroll", this.handleScroll);
    },
    computed: {
        likedOrNot: function() {
            return this.likeList.includes(this.post.id);
        },
        appliedOrNot: function() {
            return this.applyList.includes(this.post.id);
        },
        isClosedPost: function() {
            if (this.post.job_status == "D") {
                return true;
            } else {
                return false;
            }
        },
        initVideo: function() {
            return this.post.video_url != "";
        }
    },
    watch: {
        post: {
            handler: function(val, old) {
                this.videoReload = true;
                // this.videoOptions.sources[0].src = "";
                setTimeout(() => {
                    this.videoOptions.sources[0].src = val.video_url;
                    this.videoReload = false;
                }, 8000);
            },
            deep: true
        },
        visible: function(val) {
            if (val) {
                this.$emit("plusPostNumInDisplay");
            }
        }
    },
    props: [
        "post",
        "likeList",
        "likePageOrNot",
        "pageType",
        "applyList",
        "toggleDisable",
        "onSinglePost",
        "newMessageFlg"
    ],
    components: {
        VideoPlayer
    }
};
</script>

<style scoped>
.row {
    margin-left: 0px;
}

.btnGray {
    background: gray !important;
}

.post {
    margin: 20px 0;
    border-radius: 10px;
    width: 100%;
    color: black;
    height: 100%;
    background: white;
    opacity: 0.9;
}
.buttons {
    margin-top: 20px;
}
.button-area {
    padding-bottom: 40px;
}

.toggle-down-area {
    margin-top: 20px;
    padding: 30px;
    height: min-content;
    /* height:auto; */
    overflow: hidden;
    width: 98%;
    border-top: 2px lightgray solid;
    margin-right: auto !important;
    margin-left: auto !important;
}
.post-salary {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    padding-left: 5px;
    padding-right: 0;
}
.post-country-show {
    padding-right: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    padding-left: 48px;
}

.basic-info {
    padding: 20px;
    border-radius: 10px;
    opacity: 0.9;
}
.post-header {
    padding: 10px 10px 10px 0;
}
.post-header:hover {
    cursor: pointer;
}
.post-header .col-2 {
    padding-left: 10px !important;
    text-align: center;
}
.post-main {
    word-break: break-all;
}

/* 表示・非表示アニメーション中 */
.content-leave-active,
.content-enter-active {
    /* transition: transform 500ms; */
    transition: all 200ms ease;
}
.content-leave,
.content-enter-to {
    height: 600px;
}
.content-leave-to,
.content-enter {
    /* transform: translateY(-1000px); */
    height: 0;
    margin-top: 0;
    margin-bottom: 0;
    padding-top: 0;
    padding-bottom: 0;
}

.bgBlue {
    background-color: rgb(187, 228, 243);
}

.company-logo {
    transition-property: background-color;
    transition-duration: 1s;
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 1s;
}

.fade-enter,
.fade-leave-to,
.fade-leave {
    opacity: 0;
}

.fade-move {
    transition: ease-in-out 300ms;
}
.fade-leave-active {
    position: absolute;
}

.font-gray {
    color: gray;
}

.text-right {
    text-align: right;
}

.hidePost {
    display: none;
}
.singlePostHeight {
    height: 453px !important;
    overflow: scroll;
}
.singlePostMargin {
    margin-top: 0 !important;
    margin-bottom: 0 !important;
}
.post-buttons {
    width: 100%;
    margin-bottom: 20px;
    height: 40px;
}

.post-buttons div {
    float: right;
    width: 100%;
}
.new-message-badge {
    animation: blink 1s infinite alternate;
}

.longText {
    white-space: pre-line;
    overflow-wrap: break-word;
}
@keyframes blink {
    0% {
        background: red;
    }
    100% {
        background: rgb(238, 98, 98);
    }
}

.closedBackground {
    background: #d3d3d3d6;
}

.closed-badge {
    padding: 10px;
    font-size: 15px;
}
.close-badge-position {
    position: absolute;
    top: 0px;
    z-index: 10;
}

.post-title,
.post-info {
    width: 100%;
}
.post-buttons .buttons {
    width: 36%;
    padding-left: 5px;
    padding-right: 5px;
}

.video-area {
    margin-bottom: 10%;
}
.video-area .video {
    width: 95%;
    display: block;
    margin: auto;
}
.video-area-loading {
    padding-bottom: 26%;
    padding-top: 26%;
    text-align: center;
    background: black;
    position: relative;
}

@media (max-width: 991px) {
    .address-line {
        margin-top: 20px;
        font-size: 12px;
    }
}
@media (max-width: 767px) {
    .pc-view {
        display: none;
    }
    .post-buttons .buttons {
        width: 100%;
        margin-top: 10px;
    }
    .post-buttons .buttons div {
        float: unset;
    }
    .post-main p {
        font-size: 14px;
    }
    .toggle-down-area {
        padding: 5px;
    }
    .close-badge-position {
        top: -18px;
    }
    .tagShowArea {
        width: 100%;
        min-height: 30px;
        border-radius: 5px;
        padding-left: 20px;
        position: relative;
    }
}
</style>
