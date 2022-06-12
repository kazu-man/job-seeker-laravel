<template>
    <div>
        <main>
            <transition name="root">
                <div class="route-container">
                    <RouterView />
                </div>
            </transition>
        </main>
    </div>
</template>

<script>
import { INTERNAL_SERVER_ERROR } from "./util";

export default {
    computed: {
        errorCode() {
            return this.$store.state.error.code;
        }
    },
    watch: {
        errorCode: {
            handler(val) {
                if (val === INTERNAL_SERVER_ERROR) {
                    window.location.href = store.getters["auth/routePath"];
                }
            },
            immediate: true
        },
        $route() {
            this.$store.commit("error/setCode", null);
        }
    }
};
</script>

<style scoped>
/* 表示・非表示アニメーション中 */
.root-enter-active {
    transition: ease-in-out 500ms;
}
.root-leave-active {
    transition: ease-in-out 1000ms;
}

.root-enter,
.root-leave-to,
.root-leave {
    opacity: 0;
}

.root-move {
    transition: ease-in-out 500ms;
}
.root-leave-active {
    position: absolute;
}
</style>
