<template>
    <div>
        <video ref="videoPlayer" class="video-js"></video>
    </div>
</template>

<script>
import videojs from 'video.js';

export default {
    name: "VideoPlayer",
    props: {
        options: {
            type: Object,
            default() {
                return {};
            }
        }
    },
    data() {
        return {
            player: null
        }
    },
    mounted() {
        this.player = videojs(this.$refs.videoPlayer, this.options, function onPlayerReady() {
            console.log('onPlayerReady', this);
        })
    },
    beforeDestroy() {
        if (this.player) {
            this.player.dispose()
        }
    }
}
</script>

<style scoped>
    .video-js{
        margin:auto;
    }
</style>

<style>

    .vjs-big-play-button{
        top: 50% !important;
        left: 43% !important;
    }

    @media (max-width:667px){
        .vjs-big-play-button{
            left: 40% !important;
        }
    }

    @media (max-width:520px){
        .vjs-big-play-button{
            left: 37% !important;
        }
    }
</style>