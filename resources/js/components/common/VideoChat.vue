<template>
    <div class="container">
        <div class="modal-mask" v-if="loading">
            <div class="loading">
                <vue-loading
                    type="spiningDubbles"
                    color="#333"
                    :size="{ width: '50px', height: '50px' }"
                ></vue-loading>
            </div>
        </div>
        <div class="video-there-area">
            <video
                v-show="videoOn"
                ref="video-there"
                autoplay
                class="video"
            ></video>
            <div v-show="!videoOn" class="video default-video"></div>

            <div class="video-here-area">
                <video ref="video-here" autoplay class="video"></video>
            </div>
        </div>
        <br />
        <modal
            ref="modal"
            :placeData="null"
            :category="null"
            :jobTypes="null"
        ></modal>
    </div>
</template>

<script>
import modal from "../common/ModalComponent.vue";
import { VueLoading } from "vue-loading-template";

export default {
    data() {
        return {
            pusher: {
                key: process.env.MIX_APP_PUSHER_KEY,
                cluster: process.env.MIX_APP_PUSHER_CLUSTER
            },
            other: null,
            channel: null,
            stream: null,
            peers: {},
            loading: false,
            targetId: null,
            mainPusher: null,
            videoOn: false
        };
    },
    methods: {
        startVideoChat(userId) {
            this.getPeer(userId, true);
            this.targetId = userId;
        },
        pusherReconnect: function() {
            if (this.peers[this.targetId] != undefined) {
                this.peers[this.targetId].destroy();
            }
            const peer = this.peers[this.targetId];

            this.mainPusher.disconnect();
            this.channel = null;
            this.peers = {};

            this.pusherConnect();
        },
        getPeer(userId, initiator) {
            if (this.peers[userId] === undefined) {
                let peer = new Peer({
                    initiator,
                    stream: this.stream,
                    trickle: false
                });
                peer.on("signal", data => {
                    this.channel.trigger("client-signal-" + userId, {
                        userId: this.loginUser.id,
                        data: data
                    });
                })
                    .on("stream", stream => {
                        const videoThere = this.$refs["video-there"];
                        videoThere.srcObject = stream;
                        this.videoOn = true;
                    })
                    .on("close", () => {
                        const peer = this.peers[userId];

                        if (peer !== undefined) {
                            peer.destroy();
                        }

                        delete this.peers[userId];
                    });

                this.peers[userId] = peer;
            }

            return this.peers[userId];
        },
        modalShow(val) {
            this.$refs.modal.modalShow(val);
        },
        cameraSetUp() {
            var data = { token: this.token };
            axios.post("/api/findInterviewToken", data).then(res => {
                this.$store.commit("common/setLoadingFlg", false);
                if (res.data.tokenFindFlg == 1) {
                    this.targetId = res.data.targetId;
                    // カメラ、音声にアクセス
                    navigator.mediaDevices
                        .getUserMedia({ video: true, audio: true })
                        .then(stream => {
                            const videoHere = this.$refs["video-here"];
                            videoHere.srcObject = stream;
                            this.stream = stream;

                            this.pusherConnect();
                        });
                } else {
                    this.$store.dispatch("common/alertModalUp", {
                        data: 200,
                        successMessage: "無効なトークンです"
                    });
                }
            });
        },
        pusherConnect: function() {
            // エラー表示できます。
            Pusher.logToConsole = true;

            // Pusher の準備
            const pusher = new Pusher(this.pusher.key, {
                authEndpoint: "/auth/video_chat",
                cluster: this.pusher.cluster,
                auth: {
                    headers: {
                        "X-CSRF-Token": document.head.querySelector(
                            'meta[name="csrf-token"]'
                        ).content
                    }
                }
            });
            this.mainPusher = pusher;
            this.channel = pusher.subscribe("presence-video-chat");
            this.channel.bind("client-signal-" + this.loginUser.id, signal => {
                const userId = signal.userId;
                const peer = this.getPeer(userId, false);
                peer.signal(signal.data);
            });
            this.channel.bind("pusher:member_removed", member => {
                if (member.id == this.targetId) {
                    this.videoOn = false;

                    this.pusherReconnect();
                }
            });
            this.channel.bind("pusher:subscription_succeeded", members => {
                if (this.targetId in members["members"]) {
                    this.startVideoChat(this.targetId);
                }
            });
        }
    },
    mounted() {
        this.loading = false;

        if (!this.loginOrNot) {
            this.$router.push(`${this.$store.getters["auth/routePath"]}/top`);
            return;
        } else {
            this.cameraSetUp();
        }
    },
    created: function() {
        this.loading = true;
    },
    watch: {
        loginOrNot: function(val, old) {
            if (val) {
                this.loading = true;
                this.$router.go({
                    path: this.$router.currentRoute.path,
                    force: true
                });
            }
        },
        modalTarget: function() {
            if (this.modalTarget == null) {
                return;
            }
            this.modalShow(this.modalTarget);
            this.$store.commit("common/setModalTarget", null);
        }
    },
    computed: {
        loginUser: function() {
            return this.$store.state.auth.user;
        },
        loginOrNot: function() {
            return this.$store.getters["auth/check"];
        },
        token: function() {
            return this.$route.params.token;
        },
        modalTarget: function() {
            return this.$store.state.common.modalTarget;
        }
    },
    components: {
        modal,
        VueLoading
    }
};
</script>

<style scoped>
.container {
    background: #ced2e2ba;
    margin: 0;
    padding: 0;
    width: 100%;
    max-width: 100% !important;
    height: 100%;
    max-height: 100% !important;
    min-height: 1000px;
    position: relative;
}

.video-here-area {
    position: absolute;
    right: 0;
    top: 0;
    width: 20%;
}

.video-there-area {
    position: relative;
    margin: auto;
    width: 800px;
    height: 600px;
}

.video {
    width: 100%;
    height: 100%;
}

.login {
    float: right;
    margin-right: 5%;
}

.default-video {
    background: black;
}
</style>
