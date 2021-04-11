<template>
    <div class="card mb-5 mx-auto" style="width:90%">
        <div class="card-header mb-5">
            <h2 class="text-center">Edit Your Profile</h2>
            <div class="changePassword">
                <div @click="changePassModalUp" class="btn btn-info p-2">
                    Change Password
                </div>
            </div>
        </div>

        <div class="card-body ">
            <form class="form" @submit.prevent="updateProfile">
                <div v-if="this.$store.state.auth.user != null && this.$store.state.auth.user.user_type == 'C'">
                    <div class="form-group" style="text-align:center">
                        <div style="margin-bottom: 15px;">Upload your logo</div>
                        <label for="companyLogo">
                            <input type="file" name="companyLogo" id="companyLogo" class="p-3" style="display:none" @change="companyLogoSelect">
                            <img v-if="previewLogo != ''" :src="previewLogo" class="m-auto p-auto image rounded-circle company-logo-image" alt="image">
                            <img v-else-if="currentLogo != null" :src="currentLogo" class="m-auto p-auto image rounded-circle company-logo-image" alt="image">
                            <img v-else :src="defaultImage" class="m-auto p-auto image rounded-circle company-logo-image" alt="image">
                            <div style="display:inline-block;margin-left:10px">
                            </div>
                        </label>
                    </div>
                </div>  
                                      

                <div class="form-group">
                    <label for="">Account Name</label>
                    <input type="text" v-model="profileForm.accountName" name="accountname" id="accountname" class="form-control" value="" required="">
                </div>

                <div class="form-group" v-if="this.$store.state.auth.user != null && this.$store.state.auth.user.user_type == 'C'">
                    <label for="">Comapny Name</label>
                    <input type="text" v-model="profileForm.companyName" name="companyname" id="companyname" class="form-control" value="" required="">
                </div>

                <div class="form-group">
                    <label for="">First Name</label>
                    <input type="text" v-model="profileForm.userFirstname" name="firstname" id="firstname" class="form-control" value="" required="">
                </div>

                <div class="form-group">
                    <label for="">Last Name</label>
                    <input type="text"  v-model="profileForm.userLastname" name="lastname" id="lastname" class="form-control" value="" required="">
                </div>

                <div class="form-group">
                    <label for="">Email</label>
                    <input type="email"  v-model="profileForm.email" name="email" id="email" class="form-control" value="" required="">
                </div>

                <div v-if="this.$store.state.auth.user != null && this.$store.state.auth.user.user_type == 'U'">
                    <div class="form-group">
                        <label for="">Birth Day</label>
                        <input type="date"  v-model="profileForm.userBirthday" name="birth_day" id="birth_day" class="form-control" value="" required="">
                    </div>

                    <div class="form-group">
                        <label for="">Gender</label><br>
                        <input type="radio"  v-model="profileForm.gender" :value="'Male'" name="gender" id="Male" > Male
                        <input type="radio"  v-model="profileForm.gender" :value="'Female'" name="gender" id="Female" class="ml-1" checked=""> Female
                    </div>

                    <select-experience-component 
                    　@selectExperience="changeSelectedCategory" 
                    　@addExForm="addExForm"
                    　:experiences="profileForm.experiences"></select-experience-component>

                    <div class="form-group">
                        <label for="">Experience Details</label>
                        <textarea  v-model="profileForm.experience" name="experience" id="Experience" cols="30" rows="10" class="form-control">
                            </textarea>
                    </div>

                    <div class="form-group">
                        <label for="">Skills</label>
                        <textarea  v-model="profileForm.skill" name="skill" id="skill" cols="30" rows="10" class="form-control"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="">Education</label>
                        <textarea  v-model="profileForm.education" name="education" id="education" cols="30" rows="10" class="form-control"></textarea>
                    </div>

                    <div class="form-group my-5 border rounded p-3">
                        <label for="resume" style="display:100%;display:block">
                            Upload your resume
                        <div v-if="currentResume != ''" class="uploadedResume">
                            <a href="#" @click="resumeDownLoad">{{resumeName}}</a>
                        </div>
                        </label>
                        <input type="file" @change="resumeSelect" id="resume" name="resume" class="p-3" style="height:60px">
                    </div>
                </div>

                <div class="form-group row">
                    <div class="btn submit" style="margin:auto" @click="updateProfile">submit</div>
                </div>
            </form>
        </div>
    </div>

</template>

<script>
import methodMixIn from '../common/CommonMethodsMixIn.vue';
import { OK} from '../../util';

export default {
    data() {
        return {
            profileForm:{
                accountName:"",
                userLastname:"",
                userFirstname:"",
                userBirthday:"",
                experience:"",
                skill:"",
                gender:"",
                education:"",
                resume:"",
                email:"",
                companyName:"",
                companyLogo:"",
                categoryId:"",
                experiences: [
                    { 
                        id :"", category_id : "", experience_years : "" 
                    },
                ]
            },
            currentLogo:"",
            previewLogo:"",
            defaultImage: "/images/search.jpg",
            currentResume:"",
            
        }
    },

    methods: {
        updateProfile() {

            const formData = new FormData()
            formData.append("accountName", this.profileForm.accountName);
            formData.append("userLastname", this.profileForm.userLastname);
            formData.append("userFirstname", this.profileForm.userFirstname);
            formData.append("userBirthday", this.profileForm.userBirthday);
            formData.append("experience", this.profileForm.experience);
            formData.append("skill", this.profileForm.skill);
            formData.append("gender", this.profileForm.gender);
            formData.append("education", this.profileForm.education);
            formData.append("resume", this.profileForm.resume);
            formData.append("email", this.profileForm.email);
            formData.append("companyName", this.profileForm.companyName);
            formData.append("companyLogo", this.profileForm.companyLogo);
            formData.append("experiences", JSON.stringify(this.profileForm.experiences));

            axios.post('/api/updateProfile', formData).then(res => {
                this.$store.dispatch('auth/currentUser');
                this.$store.dispatch('common/alertModalUp', {data:res.status, successMessage:'プロフィールを更新しました。'});
                console.log(res.data);
            })
        },
        getProfile() {
            axios.get('/api/getProfile').then(res => {
                var data = res.data;
                console.log(data.experiences);
                if(data.profileType == 'U'){
                    this.profileForm.experience = data.experience != undefined ? data.experience : "" ;
                    this.profileForm.skill = data.skill != undefined ? data.skill : "" ;
                    this.profileForm.gender = data.gender != undefined ? data.gender : "" ;
                    this.profileForm.education = data.education != undefined ? data.education : "" ;
                    this.currentResume = data.resume != undefined ? data.resume : "" ;
                    if(data.experiences != undefined){
                        this.profileForm.experiences = data.experiences ;
                    }
                    console.log('user profile');
                }else if(data.profileType == 'C'){
                    this.profileForm.companyName = data.company_name;
                    this.currentLogo = data.company_image;
                    console.log('company profile');
                }
            });
        },
        companyLogoSelect: function(file){
            var target = file.target.files[0]

            if(target == undefined){
                return;
            }
            this.profileForm.companyLogo = target;
            var vc = this;
            // FileReaderを生成
            var fileReader = new FileReader();
            var newFile = "";
            // 読み込み完了時の処理を追加
            fileReader.onload = function() {
                newFile = this.result;
                console.log("onload");
                console.log(this.result);
                vc.previewLogo = newFile;
            };
    
            // ファイルの読み込み(Data URI Schemeの取得)
            fileReader.readAsDataURL( target );
        },
        resumeSelect: function(file){

            var target = file.target.files[0]
            if(target == undefined){
                return;
            }
            this.profileForm.resume = target;
            console.log(file);
            console.log(file);
        },
        changePassModalUp:function(){
            console.log('osita?');
            this.$store.commit('common/setModalTarget', 'changePassword');
        },
        resumeDownLoad(){
            var data = {
                "resumeFilePath":this.currentResume,
                "resumeFile":this.resumeName
            }
            this.download(data);
        },
        changeSelectedCategory:function(val,experience,index){

          this.profileForm.experiences[index].category_id = val;

        },
        addExForm:function(){
            var length = this.profileForm.experiences.length;
            this.profileForm.experiences.push(
                { 
                        id : "", category_id : "", experience_years : "" 
                }
            );
        }

    },
    created: function(){
        this.profileForm.email = this.$store.state.auth.user.email != null ? this.$store.state.auth.user.email : "" ;
        this.profileForm.accountName = this.$store.state.auth.user.name != null ? this.$store.state.auth.user.name : "" ;
        this.profileForm.userLastname = this.$store.state.auth.user.user_lastname != null ? this.$store.state.auth.user.user_lastname : "" ;
        this.profileForm.userFirstname = this.$store.state.auth.user.user_firstname != null ? this.$store.state.auth.user.user_firstname : "" ;
        this.profileForm.userBirthday = this.$store.state.auth.user.user_birthday != null ? this.$store.state.auth.user.user_birthday : "" ;
        this.getProfile();
    },
    mixins:[methodMixIn],
    computed:{
        resumeName:function(){
            if(this.currentResume != null){
                return this.currentResume.substr(this.currentResume.lastIndexOf('/') + 1);
            }
        },
    }
}
</script>


<style scoped>

.company-logo-image {
    width:150px;
    height:150px;
    margin-top:-15px !important;
    cursor:pointer;
}
.uploadedResume {
    display:inline-block;
    padding-left:10px;
    text-decoration:underline;
}
.changePassword {
    position:absolute;
    top:10px;
    right:0px;
}
.card-header{
    margin-bottom:0 !important;
}
.add-btn{
    border-radius: 100%;
    border: 1px solid gray;
    padding: 0px;
    cursor: pointer;
    font-size: 15px;
    width: 23px;
    height: 23px;
    display: inline-block;
    text-align: center;
    position: absolute;
    left: 0px;
    color:gray;
}
</style>