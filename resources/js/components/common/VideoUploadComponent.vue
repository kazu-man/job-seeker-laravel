<template>
    <v-row justify="center">
        <v-col sm="12" md="11" lg="9" xl="6" style="margin:auto">
            <v-sheet
                class="pa-3"
                style="border-radius:10px"
                :class="{ borderRadiusOpen: uploadVideoUrl }"
            >
                <v-form ref="form" style="padding:10px">
                    <v-file-input
                        v-model="inputVideo"
                        accept="video/mp4"
                        label="動画ファイルを選択してください"
                        prepend-icon="mdi-video"
                        small-chips
                        counter
                        show-size
                        @change="onVideoPicked"
                    ></v-file-input>
                </v-form>
            </v-sheet>
            <video
                v-if="uploadVideoUrl"
                controls
                autoplay
                style="width:100%; padding: 0 1%;"
            >
                <source :src="uploadVideoUrl" />
            </video>
            <div v-else-if="initVideoFlg" style="width:100%; padding: 0 1%;">
                <video-player :options="videoOptions"></video-player>
            </div>
        </v-col>
    </v-row>
</template>

<script>
import VideoPlayer from "./VideoPlayer.vue";

export default {
    data() {
        return {
            inputVideo: null,
            uploadVideoUrl: "",
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
            initVideoFlg: false
        };
    },
    methods: {
        onVideoPicked(file) {
            if (file !== undefined && file !== null) {
                if (file.name.lastIndexOf(".") <= 0) {
                    return;
                }
                this.uploadVideoUrl = null;
                const fr = new FileReader();
                fr.readAsDataURL(file);
                fr.addEventListener("load", () => {
                    this.uploadVideoUrl = fr.result;
                });
            } else {
                this.uploadVideoUrl = "";
            }
        }
    },
    components: {
        VideoPlayer
    },
    created: function() {
        if (this.initVideo != null && this.initVideo != undefined) {
            this.videoOptions.sources[0].src = this.initVideo;
            this.initVideoFlg = true;
        }
    },
    props: ["initVideo"]
};
</script>

<style scoped>
.borderRadiusOpen {
    border-radius: 10px 10px 0 0 !important;
}

.video-select {
    cursor: pointer;
}
</style>
