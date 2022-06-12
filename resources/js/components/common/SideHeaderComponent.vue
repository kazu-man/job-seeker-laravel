<template>
    <div class="side-header">
        <transition-group name="left">
            <slot
                :drawerFlg="drawerFlg"
                :openDrawerMenu="openDrawerMenu"
            ></slot>
        </transition-group>
    </div>
</template>

<script>
export default {
    data() {
        return {
            drawerFlg: false
        };
    },
    methods: {
        openDrawerMenu(val) {
            this.drawerFlg = !this.drawerFlg;
        }
    }
};
</script>

<style>
.side-header {
    height: 100%;
    position: fixed;
    padding-top: 50px;
    z-index: 50;
    opacity: 0.8;
}
.left-enter-active,
.left-leave-active {
    transition: transform 500ms;
}
.left-enter,
.left-leave-to {
    transform: translateX(-200px);
}
.right-enter-active,
.right-leave-active {
    transition: transform 500ms;
}
.right-enter,
.right-leave-to {
    transform: translateX(-200px);
}

li {
    list-style: none;
    padding-left: 15px;
}

.drawer-menu ul {
    padding: 0;
}
</style>
