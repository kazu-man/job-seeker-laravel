<template>

    <div>
        <div @click="openPopUp()">kokodeOPEN</div>
                <modal ref="modal" :placeData="null" :category="null" :jobTypes="null"></modal>
          <FullCalendar
            @dateClick="handleDateClick"
            :options="calendarOptions"
            ref="fullCalendar"
          ></FullCalendar>
    </div>

</template>


<script>
import modal from '../common/ModalComponent.vue';
import FullCalendar from '@fullcalendar/vue'
import dayGridPlugin from '@fullcalendar/daygrid'
import FullCalendarInteraction from '@fullcalendar/interaction';
import timeGridPlugin from '@fullcalendar/timegrid'
import listPlugin from '@fullcalendar/list'

 
export default {
    
            data() {
                return {
                    calendarOptions: {
                        plugins: [ dayGridPlugin, FullCalendarInteraction, timeGridPlugin, listPlugin ],
                        initialView: 'dayGridMonth',
                        events: [
                            { title: 'event 1', date: '2021-05-08 14:00' },
                            { title: 'event 2', date: '2021-05-09 14:00' }
                        ],
                        buttonText: {
                            today: '今日'
                        },
                        customButtons: {
                            addPostButton: {
                                text: '追加!',
                                click: function() {
                                    app.$children[0].events.push({
                                        title: 'event 3', 
                                    date: day3
                                    });
                                }
                            }
                        },
                                        header:{
                                            right:   'title',
                                            center: 'addPostButton',
                                            left:  'today prev,next'
                                        },
                    },






                
                }
            },
            methods: {
              openPopUp:function(){
                if(!this.loginOrNot){
                    
                    this.modalShow('login');
                    return

                }else{
                    
                    window.open("http://localhost/video_chat/FeHxUPUpAGITJPyq", "name", 'width=900, height=620, top=0, left=0, personalbar=0, toolbar=0, scrollbars=1, resizable=!');
                }


              },
                              modalShow(val){
                    this.$refs.modal.modalShow(val);
                },
                handleDateClick: function(info) {
        console.log(info);
        alert('click day :' + info.dateStr);
      }


            },
            watch:{
                loginOrNot:function(val,old){
                    if(val){
                        window.open("http://localhost/video_chat/FeHxUPUpAGITJPyq", "name", 'width=800, height=600, top=0, left=0, personalbar=0, toolbar=0, scrollbars=1, resizable=!');
                    }
                }
            },
            computed:{
                loginOrNot:function(){
                    return this.$store.getters['auth/check'];
                },
            },
            components: {
                modal,
                FullCalendar
            },


}
</script>

<style scoped>
.container {
    background:#ced2e2ba;
    margin:0;
    padding:0;
    width:100%;
    max-width:100% !important;
    height:100%;
    max-height:100% !important;
    min-height:1000px;
    position:relative;
}

.video-here-area{
    position:absolute;
    right:5%;
    top:5%;
    width:20%;
}

.video-there-area{
    position:absolute;
    left:5%;
    top:5%;
    width:60%;
}

.video{
    width:100%;
    height:100%;
}

.login{
    float:right ;
    margin-right:5%;
}
</style>