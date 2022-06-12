<template>
    <div>
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
            initPage: "scout"
        };
    },
    methods: {},
    created: function() {},
    components: {
        postsListComponent
    }
};
</script>

<style scoped>
.search-user-main {
    width: 100%;
    background: white;
    margin: auto;
    padding: 5px;
    border-radius: 10px;
}

.search-user-from {
    width: 90%;
    margin: 30px auto;
}
.key-word-area label {
    width: 100%;
    display: block;
    margin: 50px 0 5px 0;
}
.key-word-area input {
    width: 48%;
    display: inline-block;
    margin: 5px auto;
}

.key-word-area input:nth-child(even) {
    margin-right: 1%;
}

.search-btn-area {
    text-align: center;
    margin-top: 20px;
}

.search-btn-area button {
    margin: auto;
}

.user-table {
    position: relative;
    overflow: scroll;
}

.scouted {
    background: #33b5e56b !important;
}
</style>
