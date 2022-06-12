<template>
    <div>
        <div class="component-title" :key="'title'">Place List</div>
        <div class="company-register-area">
            <keep-alive>
                <transition-group>
                    <div class="component" :key="'placeRegister'">
                        <placeRegisterComponent
                            @update_data="updateData"
                        ></placeRegisterComponent>
                    </div>
                    <div class="component show" :key="'placeList'">
                        <placeShowListComponent
                            ref="placeCom"
                        ></placeShowListComponent>
                    </div>
                </transition-group>
            </keep-alive>
        </div>
    </div>
</template>

<script>
import placeRegisterComponent from "./PlaceRegisterComponent.vue";
import placeShowListComponent from "./PlaceShowList.vue";
export default {
    data() {
        return {};
    },
    methods: {
        updateData() {
            this.$refs.placeCom.updatePlaceTable();
        }
    },
    components: {
        placeRegisterComponent,
        placeShowListComponent
    }
};
</script>

<style scoped>
.container .component {
    margin: 20px auto;
    width: 100%;
    background: white;
    opacity: 1;
    border-radius: 10px;
}
.component div {
    /* padding:50px; */
    opacity: 0.98;
    max-width: 800px;
}
.component.show {
    margin: 0 auto 50px auto;
}
/* 表示・非表示アニメーション中 */
.v-enter-active {
    transition: ease-in-out 500ms;
}

/* 表示アニメーション開始時 ・ 非表示アニメーション後 */
.v-enter,
.v-leave-to {
    opacity: 0;
}
.component-title {
    color: white;
    font-size: 25px;
    padding-left: 5%;
}
.admin-add {
    position: absolute;
    top: 0;
    right: 8%;
    padding: 10px;
}
</style>
