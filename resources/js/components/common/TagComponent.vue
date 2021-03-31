<template> 
        <p class="tag" v-bind:class="{unSelectedTag:!active ? false : selected}" @click="selectTag()" >{{tag.tag_name}}</p>
</template>

<script>

export default {
    data() {
        return{
        listShow:false,
        selected:true
    }},
    props:['tag','active','initialList'],
    methods: {
        selectTag:function(){

            if(!this.active){
                return;
            }

            this.selected = !this.selected;

            this.$emit('updateTag', this.tag);
        }
    },
    computed:{
        route:function(){
          return this.$route.params;
        }
    },
    mounted:function(){

        if(this.initialList != null && this.initialList != undefined){
            for(var list of this.initialList){
                if(list.id == this.tag.id){
                    this.selected = false;
                    return;
                }
            }
        }
        
    }
}
</script>


<style scoped>
.tag {
    position: relative;
    display: inline-block;
    height: auto;
    margin-left: 29px;
    margin-bottom:5px;
    padding: 4px;
    color: #fff;
    background: #fa4141;
    font-size:10px;
    cursor:pointer;
}

.tag:before {
    position: absolute;
    top: 0px;
    left: -15px;
    content: '';
    border-width: 13px 12px 10px 3px;
    border-style: solid;
    border-color: transparent #fa4141 transparent transparent;
    cursor:pointer;
}

.tag:after {
    position: absolute;
    top: 10px;
    left: -8px;
    width: 5px;
    height: 4px;
    content: '';
    border-radius: 50%;
    background: #fff;
    cursor:pointer;
}
.unSelectedTag{
    background:#fa414170;
}
.unSelectedTag:before{
    border-color: transparent #fa414170 transparent transparent;
}
</style>