<template>
    <div>
        <div class="tagShownArea">
            <span class="add-btn" v-if="!listShow" @click="tagListShow">+</span>
            <span class="add-btn" v-else @click="tagListShow">-</span>

            <tag-component
                v-for="tag in selectedTagList"
                :key="tag.id"
                :value="tag.id"
                :tag="tag"
                :active="false"
            >
            </tag-component>
        </div>
        <transition name="content">
            <div class="tagListArea" v-show="listShow">
                <div
                    class="tagTypeArea"
                    v-for="tagType in tagTypeList"
                    :key="'type:' + tagType.id"
                >
                    <div class="tagTypeName">{{ tagType.type_name }}</div>
                    <tag-component
                        v-for="tag in tagType.tags"
                        :key="tag.id"
                        :tag="tag"
                        :active="true"
                        :initialList="initialList"
                        @updateTag="updateTag"
                    >
                    </tag-component>
                </div>
            </div>
        </transition>
    </div>
</template>

<script>
import TagComponent from "./TagComponent.vue";

export default {
    components: { TagComponent },
    data() {
        return {
            data: {},
            listShow: false,
            selectedTagList: []
        };
    },
    props: ["initialList"],
    methods: {
        tagListShow: function() {
            this.listShow = !this.listShow;
        },
        updateTag: function(newTag) {
            for (var target of this.selectedTagList) {
                if (target.id == newTag.id) {
                    this.reduceTag(newTag);
                    return;
                }
            }
            this.selectedTagList.push(newTag);
        },
        reduceTag: function(targetTag) {
            var updatedTagList = [];
            for (var el of this.selectedTagList) {
                if (el.id != targetTag.id) {
                    updatedTagList.push(el);
                }
            }
            this.selectedTagList = updatedTagList;
        }
    },
    computed: {
        tagTypeList: function() {
            return this.$store.getters["auth/tagTypeList"];
        },

        route: function() {
            return this.$route.path;
        }
    },
    watch: {
        route: function() {
            this.selectedTagList = [];
            this.listShow = false;
        }
    },

    created: function() {
        if (this.initialList != null && this.initialList != undefined) {
            this.selectedTagList = this.initialList;
        }
    }
};
</script>

<style scoped>
select {
    width: 30%;
}
.tagShownArea {
    width: 100%;
    min-height: 30px;
    border-radius: 5px;
    padding-left: 20px;
    position: relative;
    margin-bottom: 20px;
}
.tagListArea {
    width: 100%;
    height: 500px;
    border-radius: 5px;
    background: white;
    padding: 1px 5% 15px 5%;
    overflow: scroll;
}
.tagTypeArea {
    color: black;
}

.tagTypeName {
    font-size: 18px;
    /* border-bottom: solid 2px darkgray; */
    margin: 0 auto;
    padding: 0px 20px;
    margin: 20px 0px;
    font-weight: 500;
}
.tagTypeName:after {
    background-color: darkgray;
    border-radius: 5px;
    content: "";
    display: block;
    height: 6px;
    width: 103%;
    margin-left: -3%;
}

.add-btn {
    border-radius: 100%;
    border: 1px solid white;
    padding: 0px;
    cursor: pointer;
    font-size: 15px;
    width: 23px;
    height: 23px;
    display: inline-block;
    text-align: center;
    position: absolute;
    left: 0px;
    color: white;
}

.register-btn {
    width: 100px;
    padding: 10px 5px;
    margin-left: 20px;
}
.place-show-area {
    border: solid 2px lightgray;
    border-radius: 10px;
    padding: 20px;
    width: 100%;
}

.form-title {
    font-size: 24px;
}

.form-group {
    margin: 10px auto;
    text-align: center;
}
.countryRow {
    color: red;
}

.provinceRow {
    color: blue;
}

/* 表示・非表示アニメーション中 */
.content-enter-active {
    transition: ease-in-out 500ms;
}
.content-leave-active {
    transition: ease-in-out 500ms;
}

/* 表示アニメーション開始時 ・ 非表示アニメーション後 */
.content-enter,
.content-leave-to,
.content-leave {
    height: 0;
    margin: 0;
    padding-top: 0;
    padding-bottom: 0;
}

.content-move {
    transition: ease-in-out 300ms;
}
</style>
