<template>
    <div  class="category-register-area">
        <div clas="form-group">
            <input type="text" v-model="category" class="form-control">
            <button @click="register" class="btn">登録</button>
        </div>
    </div>
</template>


<script>
import { OK} from '../../util';

export default {
    data() {return{
        category:"",
    }},
    methods:{
        register:function(){
            this.category = this.category.trim();
            if(this.category == ""){
                this.$store.dispatch('common/alertModalUp', {data:OK, successMessage:'カテゴリーを入力してください'});
                return false;
            }
            var data = {"category": this.category};
            axios.post('/api/category', data).then(res => {
                // テストのため返り値をコンソールに表示
                console.log(res.data);
                this.$emit('refresh-list');
            });
        }
    }
}
</script>


<style scoped>
input {
    margin-left:5%;
    width:70%;
    display:inline-block;
}
button {
    width:60px;
    padding:10px 5px;
}
.category-register-area {
    display:inline-block;
    width:100%;
    text-align: center;
}

</style>