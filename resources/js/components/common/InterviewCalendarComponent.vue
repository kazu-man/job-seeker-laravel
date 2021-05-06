<template>

    <div>
          <FullCalendar
            :options="calendarOptions"
            ref="fullCalendar"
          ></FullCalendar>
    </div>

</template>


<script>
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
                        contentHeight: 'auto',
                        selectable:true,
                        events: [],
                        buttonText: {
                            today: '今日'
                        },
                        headerToolbar:{
                          right:   'prev,next',
                          center: 'title',
                          left:  'dayGridMonth,listMonth'
                        },
                        eventClick: this.handleDateClick,
                    },                
                }
            },
            methods: {
                handleDateClick: function(info) {

                  console.log(info.event);
                  console.log(info.event.id);
                  this.$store.dispatch('common/setInterviewConfirmModal', info.event);

                },
                removeInterview:function(id){
                  var newEvent = [];
                  for(var each of this.calendarOptions.events){
                    if(each.id != id){
                      newEvent.push(each);
                    }
                  }
                  this.calendarOptions.events = newEvent;

                }
            },
            mounted:function(){
              this.$store.commit('common/setLoadingFlg', true);

              axios.get('/api/getInterview').then(res => {
                  this.calendarOptions.events = res.data;
                  this.$store.commit('common/setLoadingFlg', false);
              });

            },
            components: {
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

<style lang='scss'>

.fc-toolbar-title{
  font-size:17px !important;
}
.fc-button-group{
  margin-right:25px;
}
</style>