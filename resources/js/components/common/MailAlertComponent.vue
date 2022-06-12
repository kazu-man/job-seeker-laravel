<template>
    <transition name="mailAlert">
        <div class="newMessageAlert badge badge-danger" v-if="flg">
            新規メッセージを受信しました
        </div>
    </transition>
</template>

<script>
export default {
    data() {
        return {
            flg: false,
            noticeInProcess: false
        };
    },
    computed: {
        liveMessage: function() {
            return this.$store.state.common.liveMessage;
        }
    },
    watch: {
        liveMessage: function(val, old) {
            if (
                this.loginCheck &&
                this.loginUser != undefined &&
                this.loginUser.id == val.message.toId &&
                !this.flg &&
                !this.noticeInProcess
            ) {
                this.noticeInProcess = true;
                setTimeout(() => {
                    this.flg = true;
                }, 4000);
            }
        },
        flg: function(val, old) {
            if (val) {
                setTimeout(() => {
                    this.flg = false;
                    this.noticeInProcess = false;
                }, 6000);
            }
        }
    },
    props: ["loginCheck", "loginUser"]
};
</script>

<style scoped>
.newMessageAlert {
    z-index: 999999999999999;
    position: fixed;
    top: 3%;
    right: -200px;
    background: white;
}
.disp {
    display: none;
}

.mailAlert-enter-active {
    animation: bounce-in 6s ease-out both;
}

@keyframes bounce-in {
    0% {
        transform: translateX(0px);
        opacity: 1;
    }
    5% {
        transform: translateX(-250px);
    }
    80% {
        transform: translateX(-250px);
        opacity: 1;
    }
    100% {
        transform: translateX(-650px);
        opacity: 0;
    }
}
</style>
