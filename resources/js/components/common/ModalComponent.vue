<template> 

        <div>
            <modal name="login" :draggable="false" :height="360" class="login sm-modal">
                <spinner v-if="loading" style="
                    position:absolute;
                    top:45%;
                    left:50%;
                    z-index: 99999999;
                " size="40"
                line-fg-color="#f00"></spinner>

                <div class="modal-header">
                    <div v-if="alert" class="alert">
                        <p>{{message}}</p>
                    </div>   
                    <p>Login</p>
                    <div v-on:click="modalHide('login')" class="close">X</div>
                </div>
                <div class="modal-body">
                    <form class="form" @submit.prevent="login">

                    <div class="form-group row">
                        <label for="email" class="col-4">Email</label>
                        <input type="text" name="email" class="form-control col-8" v-model="loginForm.email">
                    </div>
                    <div class="form-group row">
                        <label for="password" class="col-4">Password</label>
                        <input type="password" name="password" class="form-control col-8" v-model="loginForm.password">
                    </div>
                    <div style="text-align:right;"><span @click="registerFormShow" class="login-switch">登録する</span></div>
                    <div style="text-align:right;"><span @click="passworForgotModal" class="login-switch">パスワード忘れた</span></div>

                    <div class="form-group row" style="margin-top:0">
                        <div class="btn submit" @click="login">Login</div>
                    </div>
                    <div style="text-align:center;">
                        <span @click="googleLogin" style="color:blue;text-decoration:underline;">
                            <img style="
                                cursor:pointer;
                                height: 47px;
                                width: 190px;
                                margin: 5px 0 0 0;
                                padding: 0;
                                background-color: white;" src="/images/google-button.png">
                        </span>
                    </div>

                    </form>
                </div>  
            </modal> 

            <modal name="register" :draggable="false" :height="500" class="register" style="height:2000px">

                <div class="modal-header">
                    <div v-if="alert" class="alert">
                        <p>{{message}}</p>
                    </div>   
                    <p>Register</p>
                    <div v-on:click="modalHide('register')" class="close">X</div>
                </div>

                <div class="modal-body">
                    <form class="form" @submit.prevent="register(false)">
                
                        <div class="form-group row">
                            <label for="email" class="col-4">Email</label>
                            <input type="text" class="form-control col-8" id="email" v-model="registerForm.email">
                        </div>
                        <div class="form-group row">
                            <label for="password" class="col-4">Password</label>
                            <input type="password" class="form-control col-8" id="password" v-model="registerForm.password">
                        </div>
                        <div class="form-group row">
                            <label for="password-confirmation" class="col-4">Password (confirm)</label>
                            <input type="password" class="form-control col-8" id="password-confirmation" v-model="registerForm.password_confirmation">
                        </div>

                        <div class="form-group row" style="width:50%">
                            <input type="radio" id="asUser" name="userType" class="form-control col-1" v-model="asCompany" :value="false">
                            <label for="asUser" class="col-6 userType pointer">to find a job</label>
                        </div>
                        <div class="form-group row" style="width:50%">
                            <input type="radio" id="asCompany" name="userType" class="form-control col-1" v-model="asCompany" :value="true">
                            <label for="asCompany" class="col-6 userType pointer">to find workers</label>
                        </div>
                        <div v-if="asCompany">

                            <div class="form-group row">
                                <label for="email" class="col-4">Company Name</label>
                                <input type="text" name="email" class="form-control col-8" v-model="registerForm.companyName">
                            </div>
                        </div>
                            <div style="text-align:right;"><span class="login-switch" @click="loginFormShow">ログインする</span></div>

                        <div class="form-group row">
                            <button type="submit" class="btn submit">register</button>
                        </div>
                    </form>
                </div>
            </modal> 

            <modal name="registerAdmin" :draggable="false" :height="350" class="registerAdmin" style="height:2000px">

                <div class="modal-header">
                    <div v-if="alert" class="alert">
                        <p>{{message}}</p>
                    </div>   
                    <p>Register Admin</p>
                    <div v-on:click="modalHide('registerAdmin')" class="close">X</div>
                </div>

                <div class="modal-body">
                    <form class="form" @submit.prevent="register(true)" style="padding:30px;margin-right: 30px;">

                        <div v-if="registerErrors" class="errors">
                            <ul v-if="registerErrors.name">
                            <li v-for="msg in registerErrors.name" :key="msg">{{ msg }}</li>
                            </ul>
                            <ul v-if="registerErrors.email">
                            <li v-for="msg in registerErrors.email" :key="msg">{{ msg }}</li>
                            </ul>
                            <ul v-if="registerErrors.password">
                            <li v-for="msg in registerErrors.password" :key="msg">{{ msg }}</li>
                            </ul>
                        </div>                    
                        <div class="form-group row">
                            <label for="email" class="col-4">Email</label>
                            <input type="text" class="form-control col-8" id="email" v-model="registerForm.email">
                        </div>
                        <div class="form-group row">
                            <label for="password" class="col-4">Password</label>
                            <input type="password" class="form-control col-8" id="password" v-model="registerForm.password">
                        </div>
                        <div class="form-group row">
                            <label for="password-confirmation" class="col-4">Password (confirm)</label>
                            <input type="password" class="form-control col-8" id="password-confirmation" v-model="registerForm.password_confirmation">
                        </div>

                        <div class="form-group row">
                            <button type="submit" class="btn submit" style="margin:auto">register</button>
                        </div>
                    </form>
                </div>
            </modal> 

            <modal name="changePassword" :draggable="false" class="changePassword">
                <div class="modal-header">
                    <div v-if="alert" class="alert">
                        <p>{{message}}</p>
                    </div>   
                    <p>Change Password</p>
                    <div v-on:click="modalHide('changePassword')" class="close">X</div>
                </div>
                <div class="modal-body">
                    <form class="form" @submit.prevent="login">

                    <div v-if="loginErrors" class="errors">
                        <ul v-if="loginErrors.email">
                        <li v-for="msg in loginErrors.email" :key="msg">{{ msg }}</li>
                        </ul>
                        <ul v-if="loginErrors.password">
                        <li v-for="msg in loginErrors.password" :key="msg">{{ msg }}</li>
                        </ul>
                    </div>

                    <div class="form-group row">
                        <label for="email" class="col-4">Password</label>
                        <input type="password" name="password" class="form-control col-7" v-model="passwordResetForm.password">
                    </div>
                    <div class="form-group row">
                        <label for="email" class="col-4">Password (comfirm)</label>
                        <input type="password" name="password_comfirmation" class="form-control col-7" v-model="passwordResetForm.password_confirmation">
                    </div>
                    <div class="form-group row">
                        <div class="btn submit" style="margin:auto;margin-top:10px;" @click="changePassword">Send</div>
                    </div>

                    </form>
                </div>  
            </modal> 

            <modal name="forgotPassword" :draggable="false" class="forgotPassword">
                <div class="modal-header">
                    <div v-if="alert" class="alert">
                        <p>{{message}}</p>
                    </div>   
                    <p>Password Reset</p>
                    <div v-on:click="modalHide('forgotPassword')" class="close">X</div>
                </div>
                <div class="modal-body">
                    <form class="form" @submit.prevent="login">

                    <div v-if="loginErrors" class="errors">
                        <ul v-if="loginErrors.email">
                        <li v-for="msg in loginErrors.email" :key="msg">{{ msg }}</li>
                        </ul>
                        <ul v-if="loginErrors.password">
                        <li v-for="msg in loginErrors.password" :key="msg">{{ msg }}</li>
                        </ul>
                    </div>

                    <div class="form-group row">
                        <label for="email" class="col-3" style="text-align:right;margin-bottom:0;vertical-align: top;padding-top:7px;">Email</label>
                        <input type="email" name="password" class="form-control col-8" v-model="forgotPasswordForm.email">
                    </div>
                    <div class="form-group row">
                        <div class="btn submit" style="margin:auto;margin-top:10px;" @click="forgotPassword">Send</div>
                    </div>

                    </form>
                </div>  
            </modal> 
            <modal name="editPost" :draggable="false"  :scrollable="true" :height="600" :width="1000" class="editPost" style="overflow-y:scroll">
                <div v-on:click="modalHide('editPost')" class="close">X</div>
                <div class="job-content">
                    <div class="job-register-area">  
                        <div class="form-group title-form">
                            <label>
                                Job Title: 
                            </label>
                            <input type="text" name="title" v-model="editPostForm.title" class="form-control">
                        </div>
                        <div class="form-group salary-form">

                            <label>
                                Anual Salary: 
                            </label>
                            <input type="text" name="salary" v-model="editPostForm.salary" class="form-control">
                        </div>

                        <div class="form-group place-form">

                            <place-show-component @select-city='selectCity' :initVal="editPostForm.city"></place-show-component>
                        </div>


                        <div class="form-group type-form">
                            <label>
                                Job type
                            </label>
                            <select-box-component :baseData="jobTypes" @changeSelectedVal="selectType" :initVal="editPostForm.type"></select-box-component>
                        </div>

                        <div class="form-group type-form">
                            <label>
                                Tags
                            </label>
                            <select-tag-component ref="selectTagComponent" :initialList="editPostForm.tagList"></select-tag-component>
                        </div>

                        <div class="form-group category-form">
                            <label>
                                Category
                            </label>
                            <select-box-component :baseData="category" @changeSelectedVal="selectCategory" :initVal="editPostForm.category"></select-box-component>
                        </div>

                        <div class="form-group description-form">

                            <label>
                                Description: 
                            </label>
                            <textarea name="description" v-model="editPostForm.description" class="form-control" rows="8"></textarea>
                        </div>
                        <div class="form-group requirement-form">

                            <label>
                                Requirement: 
                            </label>
                            <textarea name="requirement" v-model="editPostForm.requirement" class="form-control" rows="8"></textarea>

                        </div>
                        <div class="form-group benefit-form">

                            <label>
                                Benefit: 
                            </label>
                            <textarea name="benefit" v-model="editPostForm.benefit" class="form-control" rows="8"></textarea>
                        </div>
                        <div class="form-group experience-fom">

                            <label>
                                Experience: 
                            </label>
                            <textarea name="experience" v-model="editPostForm.experience" class="form-control" rows="8"></textarea>

                        </div>

                        <div class="form-group submit-form">
                            <button class="btn submit-btn" @click="updatePost">Submit</button>       
                        </div>
                    </div>
                </div>
            </modal>
    

            <modal name="apply" :draggable="false" :height="'auto'" class="apply">
                <div class="modal-header">
                    <p>こちらの求人に応募しますか？</p>
                    <div v-on:click="modalHide('apply')" class="close">X</div>
                </div>
                <div class="modal-body" style="width:50%;">

                    <div  style="text-align:left;margin:auto">
                        <div v-if="applyTargetPost != null">求人名：{{applyTargetPost.job_title}}</div>
                        <div v-if="applyTargetPost != null">会社名：{{applyTargetPost.company.company_name}}</div>
                        <div v-if="applyTargetPost != null">求人タイプ：{{applyTargetPost.job_type.job_type}}</div>
                        <div v-if="applyTargetPost != null">勤務地：{{applyTargetPost.city.province.province_name}} - {{applyTargetPost.city.city_name}} - {{applyTargetPost.city.province.country.country_name}}</div>
                        <div v-if="applyTargetPost != null">給与：{{applyTargetPost.annual_salary}}</div>
                    </div>  

                    <div class="row" style="margin-top:25px;margin-bottom:10px;">
                        <div class="col-6">
                            <button  @click="modalHide('apply')" name="apply" class="btn btn-block btn-warning btn-md p-2 text-danger">
                                <span class="icon-heart-o mr-2 text-danger">キャンセル</span>
                            </button>
                        </div>
                        <div class="col-6">
                            <button @click="apply" type="button" class="btn btn-primary p-2 btn-block">
                                応募する
                            </button>
                        </div>
                    </div>
                </div>  
            </modal> 

            <modal name="applicantProfile" :height="600" :width="800" class="applicantProfile">

                <div class="modal-header" v-if="applicantProfile != null" style="background:rgba(0, 0, 0, 0.03)">
                    <p>Applicant Profile</p>
                    <div v-on:click="modalHide('applicantProfile')" class="close">X</div>
                
                </div>

                <div class="modal-body" style="height:520px;overflow-y:scroll" v-if="applicantProfile != null">

                    <div v-if="applicantProfile.resume != null" class="text-right mx-4">
                        <!-- <a :href="applicantProfile.resume" target="_blank">Resume -->
                            <a href="#" @click="resumeDownLoad">Resume</a>
                        <!-- </a> -->
                    </div>

                    <div class="row border mt-0 mx-4">
                        <div class="col-md-6 col-sm-12 row  p-2">
                            <div class="col-4 pr-0"><strong>Name: </strong></div>
                            <div class="col-8 text-left p-0" v-if="applicantProfile.user.user_lastname != null && applicantProfile.user.user_firstname != null">
                                {{applicantProfile.user.user_lastname}} {{applicantProfile.user.user_firstname}}
                            </div>
                        </div>

                        <div class="col-md-6 col-sm-12 row  p-2">
                            <div class="col-4 pr-0"><strong>Email: </strong></div>
                            <div class="col-8 text-left p-0" v-if="applicantProfile.user.email != null">
                            {{applicantProfile.user.email}}
                            </div>
                        </div>

                        <div class="col-md-6 col-sm-12 row  p-2">
                            <div class="col-4 pr-0"><strong>Birth Day: </strong></div>
                            <div class="col-8 text-left p-0" v-if="applicantProfile.user.user_birthday != 'null'">
                                {{applicantProfile.user.user_birthday}}
                            </div>
                        </div>

                        <div class="col-md-6 col-sm-12 row  p-2">
                            <div class="col-4 pr-0"><strong>Gender: </strong></div>
                            <div class="col-8 text-left p-0">
                                {{applicantProfile.gender}}
                            </div>
                        </div>
                    </div>

                    <div class="profile-item border m-4 p-3 mt-5">
                        <div class="h-3 profile-item-label"><strong>Experience</strong></div>
                        <div class="text-left  word-break" >
                            {{applicantProfile.experience}}
                        </div>
                    </div>


                    <div class="profile-item border m-4 p-3 mt-5">
                        <div class="h-3 profile-item-label"><strong>Skills</strong></div>
                        <div class="text-left  word-break">
                            {{applicantProfile.skill}}
                        </div>
                    </div>


                    <div class="profile-item border m-4 p-3 mt-5">
                        <div class="h-3 profile-item-label"><strong>Education</strong></div>
                        <div class="text-left  word-break">
                            {{applicantProfile.education}}
                        </div>
                    </div>

                    <div class="btn btn-info" @click="modalHide('applicantProfile')">
                        close
                    </div>
                </div>
            </modal>


            <modal name="singlePost"  :height="600" :width="800" class="singlePost">
                <div v-on:click="modalHide('singlePost')" class="close">X</div>

                <posts-component 
                v-if="singlePost != null"
                :post="singlePost"
                :likeList="[]" 
                :applyList="[]" 
                :likePageOrNot="false"
                :toggleDisable="true"
                :onSinglePost="true"
                :pageType="'singlePost'"
                style="width:100%;height:600px;overflow-y:scroll"></posts-component>
            </modal>


            
             <modal name="messageModal" :height="600" :width="800" class="messageModal">

                <div class="modal-header">
                    <p v-if="loginUser !=  null && loginUser.user_type == 'U'">Messages <br> <span class=" message-to">[ To: {{applyRecord.companyName}} ]</span></p>
                    <p v-else-if="loginUser !=  null && loginUser.user_type == 'C'">Messages <br><span class=" message-to">[ To: {{applyRecord.applicantName}} ]</span></p>
                    <div v-on:click="modalHide('messageModal')" class="close">X</div>
                </div>

                <div class="messages-area" id="messages-area" style="height:360px;overflow-y:scroll" v-if="messagesList != null">
                    <div class="message">
                        <transition-group name="message">
                            <div v-for="message in messagesList" class="message-text" :key="message.id">

                                <div v-if="loginUser !=  null && loginUser.user_type == message.sent_to">
                                    
                                    <div class="company-logo-message" v-if="message.sent_to == 'U'">
                                        <img v-if="applyRecord.companyLogo != null" :src="applyRecord.companyLogo" class="m-auto p-auto image rounded-circle company-logo-image" alt="image">
                                        <img v-else :src="defaultImage" class="m-auto p-auto image rounded-circle company-logo-image" alt="image">
                                    </div>

                                    <div class="balloon1-left">
                                        <p>{{message.message}}</p>
                                    </div>

                                    <div class="message-time-left">
                                        <small>{{message.created_at}}</small>
                                    </div>
                                </div>

                                <div v-else>
                                    <div class="message-right">
                                        <div class="balloon1-right">
                                        <p>{{message.message}}</p>
                                        </div>
                                        <div class="message-time-right">
                                            <small>{{message.created_at}}</small>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </transition-group>
                    </div>

                </div>

                <div class="send-message-area row">
                    <div class="input-area col-10">
                        <textarea v-model="messageForm.message" rows="5"></textarea>
                    </div>
                    <div class="col-1 message-send-btn">
                        <div class="btn btn-info" @click="sendMessage()">
                            send
                        </div>
                    </div>
                </div>

            </modal>

            <modal name="postClose" :draggable="false" class="postClose">
                <div v-if="singlePost != null" class="modal-header">
                    <p v-if="singlePost.job_status == 'A'">こちらの求人を非表示にしますか？</p>
                    <p v-else>こちらの求人を再公開しますか？</p>
                    <div v-on:click="modalHide('postClose')" class="close">X</div>
                </div>
                <div class="modal-body" style="width:90%;">

                    <div  style="text-align:left;margin:auto">
                        <div v-if="singlePost != null">求人名：{{singlePost.job_title}}</div>
                        <div v-if="singlePost != null">会社名：{{singlePost.company.company_name}}</div>
                        <div v-if="singlePost != null">求人タイプ：{{singlePost.job_type.job_type}}</div>
                        <div v-if="singlePost != null">勤務地：{{singlePost.city.province.province_name}} - {{singlePost.city.city_name}} - {{singlePost.city.province.country.country_name}}</div>
                        <div v-if="singlePost != null">給与：{{singlePost.annual_salary}}</div>
                    </div>  

                    <div class="row" style="margin-top:25px;">
                        <div class="col-6">
                            <button v-on:click="modalHide('postClose')" name="closePost" class="btn btn-block btn-warning btn-md p-2 text-danger">
                                <span class="icon-heart-o mr-2 text-danger">キャンセル</span>
                            </button>
                        </div>
                        <div class="col-6">
                            <button v-if="singlePost != null && singlePost.job_status == 'A'" @click="closePost" type="button" class="btn btn-primary p-2 btn-block">
                                非表示にする
                            </button>
                            <button v-else @click="closePost" type="button" class="btn btn-primary p-2 btn-block">
                                再公開する
                            </button>
                        </div>
                    </div>
                </div>  
            </modal> 


            <modal name="bgChangeModal" :draggable="false" class="bgChangeModal" :height="500" :width="500">
                <div class="modal-header">
                    <p>背景の変更</p>
                    <div v-on:click="modalHide('bgChangeModal')" class="close">X</div>
                </div>
                <div class="modal-body" style="width:100%;" v-if="countryForm != null">

                    <div class="form-group">
                        <label>
                            Country Name: {{countryForm.countryName}}
                        </label>
                        <div style="width:50%;margin:auto">
                        <label for="modalCountryBg">
                            <input type="file" name="modalCountryBg" id="modalCountryBg" class="p-3" style="display:none" @change="countryBgChange">
                            <div style="display:inline-block;margin-left:10px;width:100%;">
                                <div>Background image :</div>
                            </div>
                            <img v-if="modalCurrentBgImage != null" :src="modalCurrentBgImage" class="m-auto p-auto image country-bg-image" alt="image">
                            <img v-else-if="countryForm.countryImage != null" :src="countryForm.countryImage" class="m-auto p-auto image country-bg-image" alt="image">
                            <img v-else :src="defaultImage" class="m-auto p-auto image country-bg-image" alt="image">
                        </label>
                        </div>
                        <div>
                            <button v-on:click='updateCountry' class="btn register-btn">Submit</button>
                        </div>       
                    </div>
                </div>  
            </modal> 

            <modal name="deleteUserModal" :draggable="false" class="deleteUserModal">
                <div class="modal-header">
                    <p v-if="deleteTargetUser != null && deleteTargetUser.user_status == 'A'">こちらのユーザをログイン不可に変更しますか？</p>
                    <p v-else>こちらのユーザをログイン可能に変更しますか？</p>
                    <div v-on:click="modalHide('deleteUserModal')" class="close">X</div>
                </div>
                <div class="modal-body" style="width:100%;padding-top:40px;">

                    <div  style="text-align:left;margin-left:15%">
                        <div v-if="deleteTargetUser != null">ユーザ名　　：{{deleteTargetUser.user_firstname}} {{deleteTargetUser.user_lastname}}</div>
                        <div v-if="deleteTargetUser != null">e-mail　　　 ：{{deleteTargetUser.email}}</div>
                        <div v-if="deleteTargetUser != null">ユーザタイプ：{{deleteTargetUser.user_type}}</div>
                    </div>  

                    <div class="row button-area">
                        <div class="col-6">
                            <button  @click="modalHide('deleteUserModal')" name="deleteUserModal" class="btn btn-block btn-warning btn-md p-2 text-danger">
                                <span class="icon-heart-o mr-2 text-danger">キャンセル</span>
                            </button>
                        </div>
                        <div class="col-6">
                            <button @click="deleteUser" type="button" class="btn btn-primary p-2 btn-block">
                                送信
                            </button>
                        </div>
                    </div>
                </div>  
            </modal> 
            

            <!-- 一番下固定 -->
            <modal name="alert" :draggable="false" :resizable="false" :height="'auto'" :width="320" style="text-align:center" class="alert">
                <div class="modal-body">
                    <p style="white-space: break-spaces;">{{alertModalMessage}}</p>
                </div>  
                    <div v-on:click="modalHide('alert')" class="btn btn-success">閉じる</div>
            </modal> 
            <modal name="relordAfterAlert" :draggable="false" :resizable="false" :height="150" :width="300" style="text-align:center" class="alert">
                <div class="modal-body" style="height:50%">
                    <p>{{alertModalMessage}}</p>
                </div>  
                    <div v-on:click="modalHide('alert')" class="btn btn-success">閉じる</div>
            </modal> 
        </div>

</template>

<script>
import { UNAUTHORIZED ,OK, UNPROCESSABLE_ENTITY} from '../../util';
import methodMixIn from './CommonMethodsMixIn.vue';

export default {
    data() {
        return{
        message:'',
        alert:false,
        modalType:"",
        loginForm: {
            email: '',
            password: '',
        },
        registerForm: {
            email: '',
            password: '',
            password_confirmation: '',
            companyName: '',
            companyImage: null,
            userType:'U'
        },

        editPostForm:{
            jobId:'',
            title:'',
            salary:'',
            type:'',
            category:'',
            city:'',
            description:'',
            requirement:'',
            benefit:'',
            experience:'',
            tagList:[]
        },
        messageForm:{
            applyRecordId:"",
            message:""
        },
        asCompany:false,            
            //あとで消す
            currentLogo:null,
            defaultImage: "/images/search.jpg",
        newMessage:'',
        applyRecord:'',

        countryForm:{
            countryName:"",
            countryImage:null,  
            modalUploadedFile:null,
        },
        passwordResetForm:{
            "password":"",
            "password_confirmation":""
        },
        forgotPasswordForm:{
            "email":""
        },
        modalCurrentBgImage:null,
        defaultImage: "/images/search.jpg",
        loading:false
    }},
    methods: {

        modalShow : function(val) {
            if(val == "editPost"){
                this.initEditPostModal();
            }else if(val == "messageModal"){
                this.initMessageModal();
            }

            this.modalType = val;
            console.log("modal Componentから")
            console.log(val);
            this.$modal.show(this.modalType);
        },
        modalHide : async function (target) {
            console.log(target);
            var status = this.$store.state.error.code;
            var reloadFlg = this.$store.state.common.alertModalMessage != null ? this.$store.state.common.alertModalMessage.reload : true;

            if(target == 'alert' && status == UNAUTHORIZED){
                this.$router.go({path: this.routePath, force: true})
                await this.$store.dispatch('auth/logout')       
            }else if(target == 'alert'){
                console.log('クローズするか')
                console.log(this.$store.state.common.alertModalMessage.close)
                this.$modal.hide(target);
                if(this.$store.state.common.alertModalMessage.close){
                    this.$modal.hide('changePassword');
                    this.$modal.hide('editPost');
                    this.$modal.hide('apply');
                    this.$modal.hide('postClose');
                    this.$modal.hide('deleteUserModal');
                    this.$modal.hide('register');
                    this.$modal.hide('registerAdmin');
                    this.$modal.hide('login');
                    this.$modal.hide('bgChangeModal');
                }else{
                    reloadFlg = false;
                }
                //エラーの場合などはリロードする
                if(reloadFlg){
                    window.location.href = "/";
                }
                this.$store.commit('common/setApplyTargetPost', null)
                this.$store.commit('common/setEditPost', null)
                this.$store.commit('common/setAlertModalMessage', null)
                this.passwordResetForm.password = "";
                this.passwordResetForm.password_confirmation = "";
            }else{
                this.$modal.hide(target);
            }
            

            this.clearError();
        },
        alertHide : function () {
            this.$modal.hide("alert");
        },
        registerFormShow : function () {
            this.$modal.hide(this.modalType);
            this.$modal.show('register');
        },
        loginFormShow : function () {
            this.$modal.hide('register');
            this.$modal.show('login');
        },      
        async login () {
          await this.$store.dispatch('auth/login', this.loginForm);

            if (this.apiStatus) {
                // トップページに移動する
                this.modalHide(this.modalType);
                this.$store.dispatch('common/alertModalUp', {data:OK, successMessage:'ログインしました。'});
                this.$store.dispatch('auth/prepareList')
            }
        },
        async register (adminFlg) {

            if(adminFlg){
                this.registerForm.userType = "A";
            }
            await this.$store.dispatch('auth/register', this.registerForm)

            if (this.apiStatus) {
                this.modalHide('register');
                this.$store.dispatch('common/alertModalUp', {data:OK, successMessage:'登録しました。'});
                this.$store.dispatch('auth/refreshAdminData', true);
            }
        },
        clearError () {
            this.$store.commit('auth/setLoginErrorMessages', null);
            this.$store.commit('auth/setRegisterErrorMessages', null);
            this.modalCurrentBgImage = null;
            this.countryForm.modalUploadedFile = null;
        },
        selectType: function(val){
            console.log("type selected");
            this.editPostForm.type = val;
        },
        selectCategory: function(val) {
            console.log("category selected");
            this.editPostForm.category = val;
        },
        selectCity: function(val) {
            console.log("city selected");
            this.editPostForm.city = val;
        },
        initEditPostModal:function(){
            var postInfo = this.$store.state.common.editPost;
            this.editPostForm.jobId = postInfo.jobId;
            this.editPostForm.title = postInfo.title;
            this.editPostForm.salary = postInfo.salary;
            this.editPostForm.description = postInfo.description;
            this.editPostForm.requirement = postInfo.requirement;
            this.editPostForm.benefit = postInfo.benefit;
            this.editPostForm.experience = postInfo.experience;
            this.editPostForm.type = postInfo.type;
            this.editPostForm.category = postInfo.category;
            this.editPostForm.city = postInfo.city;
            this.editPostForm.tagList = postInfo.tagList;

        },
        updatePost:function(){
            var tc = this;
            this.editPostForm.tagList = this.$refs.selectTagComponent.selectedTagList;

            axios.post('/api/updatePost', this.editPostForm).then(res => {

                this.$store.dispatch('common/alertModalUp', {data:res.status, successMessage:'postを更新しました。'});
                this.$store.commit('common/setUpdatedPost', res.data.updatedPost)
            });

        },
        initMessageModal(){
            this.applyRecord = this.$store.state.common.applyRecord;
            this.$store.dispatch('auth/getNewMessageExistFlg');
            this.scrollBottomOfMessage();
        },
        apply(){
            var data = {
                "postId":this.applyTargetPost.id,
                "companyId":this.applyTargetPost.company.id
                };
            axios.post('/api/apply', data).then(res => {
                if(res.data.status != null){
                    this.$store.dispatch('common/alertModalUp', {data:res.status, successMessage:res.data.status});
                    return;
                }
                this.$store.dispatch('common/alertModalUp', {data:res.status, successMessage:'applyしました'});
                this.$store.dispatch('auth/getApplyList');
            });
        },
        sendMessage(){
            this.messageForm.applyRecordId = this.applyRecord.id;
            if(this.messageForm.message == null || this.messageForm.message == ""){
                this.$store.dispatch('common/alertModalUp', {data:OK, successMessage:'メッセージを入力してください'});
                return false;
            }
            axios.post('/api/sendMessage', this.messageForm).then(res => {
                this.$store.dispatch('common/refreshMessage', this.applyRecord.id)
                this.$store.dispatch('common/alertModalUp', {data:res.status, successMessage:'送信しました'});
            });
        },
        closePost(){
            axios.post('/api/closePost', {"targetPostId":this.singlePost.id}).then(res => {
                if(this.singlePost.job_status == "D"){
                    this.$store.dispatch('common/alertModalUp', {data:res.status, successMessage:'再公開しました'});
                    this.singlePost.job_status = "A"

                }else{
                    this.$store.dispatch('common/alertModalUp', {data:res.status, successMessage:'非表示にしました'});
                    this.singlePost.job_status = "D"
                }
                this.$store.commit('common/setUpdatedPost', this.singlePost);
            });
        },
        scrollBottomOfMessage:function(){
              setTimeout(() => {
                var scrollPosition = document.getElementById("messages-area").scrollTop;
                var scrollHeight = document.getElementById("messages-area").scrollHeight;
                document.getElementById("messages-area").scrollTop = scrollHeight;
            }, 10);
        },
        countryBgChange: function(file){

            var target = file.target.files[0]
            this.countryForm.modalUploadedFile = target;
            const self = this; 
            
            this.readFileAsDataURL(target).then((value) => {
                self.modalCurrentBgImage = value;
            });
        },
        updateCountry: async function(){
            if(this.countryForm.countryName == ""){
                this.$store.dispatch('common/alertModalUp', {data:OK, successMessage:'国名を入力してください'});
                return;
            }else if(this.countryForm.modalUploadedFile == null){
                this.$store.dispatch('common/alertModalUp', {data:OK, successMessage:'背景を選択してください。',close:false});
                return;
            }

            const formData = new FormData()

            formData.append("countryName", this.countryForm.countryName);
            formData.append("countryImage", this.countryForm.modalUploadedFile);
            formData.append("countryId", this.commonDataForModal.country_id);

            await axios.post('/api/registerCountry', formData).then(res => {
                if(res.status != 200){
                    this.$store.dispatch('common/alertModalUp', {data:OK, successMessage:"背景を選択してください"});
                    return false;
                }
                
                this.$store.dispatch('common/alertModalUp', {data:res.status, successMessage:'背景を更新しました。'});
                console.log(res.data);
                this.$store.commit('common/setSettingCountryReloadFlg',!this.settingCountryReloadFlg);
                this.countryForm.countryImage = this.modalCurrentBgImage;
            });
        },
        deleteUser:async function(){
            await this.$store.dispatch('auth/userDelete', this.deleteTargetUser);
            var message = "ユーザをログイン可能に変更しました。"
            if(this.deleteTargetUser.user_status == 'A'){
                message = "ユーザをログイン不可に変更しました。"
            }
            this.$store.dispatch('common/alertModalUp', {data:status, successMessage:message});
            this.$store.dispatch('auth/getUsers')
        },
        resumeDownLoad(){
            var data = {
                "resumeFilePath":this.applicantProfile.resume,
                "resumeFile":this.resumeName
            }
            this.download(data);
        },
        async changePassword(){
            console.log('changePass');
            await axios.post('/api/passwordUpdate',this.passwordResetForm).then(res => {
                if(res.status != 200){
                    return false;
                }
                
                this.$store.dispatch('common/alertModalUp', {data:res.status, successMessage:'パスワードを変更しました'});
            });
        },
        passworForgotModal(){
            this.$store.commit('common/setModalTarget', 'forgotPassword');

        },
        async forgotPassword(){
            console.log('forgot pass');
            await axios.post('/api/password/email',this.forgotPasswordForm).then(res => {
                if(res.status != 200){
                    return false;
                }
                this.$store.dispatch('common/alertModalUp', {data:res.status, successMessage:'パスワード再設定メールを送信しました'});
            })
            .catch(res => {
                this.$store.dispatch('common/alertModalUp', {data:res.status, successMessage:'メールアドレスが見つかりませんでした。\nメールアドレスをご確認ください。'});
            });
        },
        async googleLogin(){
            this.loading = true;

            console.log('google login');
            location.href = "/api/auth/google";
        }

    },
    computed: {
        apiStatus () {
            return this.$store.state.auth.apiStatus
        },
        loginErrors () {
            return this.$store.state.auth.loginErrorMessages
        },
        registerErrors () {
            return this.$store.state.auth.registerErrorMessages
        },
        alertModalMessage(){
            if(this.$store.state.common.alertModalMessage == null){
                return "";
            }
            var message = this.$store.state.common.alertModalMessage.successMessage;

            if(this.alertModalStatus != null && this.alertModalStatus == UNAUTHORIZED){

                message = "ログアウトされています。ログインし直してください。";

            }else if(message == null && message == ""){

                message = "エラーが発生しました。"
            }
            return message;
        },
        alertModalStatus(){
            return this.$store.state.error.code;
        },
        applyTargetPost(){
            return this.$store.state.common.applyTargetPost;
        },
        applicantProfile(){
            return this.$store.state.common.applicantProfile;
        },
        singlePost(){
            return this.$store.state.common.singlePost;
        },
        messagesList(){
            return this.$store.state.common.messagesList;
        },
        loginUser:function() {
            return this.$store.state.auth.user;
        },
        commonDataForModal:function(){
            return this.$store.state.common.commonDataForModal;
        },
        deleteTargetUser:function(){
            return this.$store.state.common.deleteTargetUser;
        },
        settingCountryReloadFlg:function(){
            return this.$store.state.common.settingCountryReloadFlg;
        },
        routePath:function(){
            return this.$store.getters['auth/routePath'];
        },
        resumeName:function(){
            if(this.applicantProfile.resume != null){
                return this.applicantProfile.resume.substr(this.applicantProfile.resume.lastIndexOf('/') + 1);
            }
        }


    },
    watch:{
        asCompany:function(val, old){
            if(val){
                this.registerForm.userType = 'C';
            }else{
                this.registerForm.userType = 'U';
            }
        },
        commonDataForModal:function(val,old){
            if(val.country_name != null){
                this.countryForm.countryName = val.country_name;
            }
            if(val.country_name != null){
                this.countryForm.countryImage = val.country_image;
            }
        },
        loginErrors:function(){
            var message = "";
            if(!this.apiStatus && this.loginErrors != null && this.loginErrors.email != null){
                for(var error of this.loginErrors.email){
                    message += error + '\n';
                }
            }
            if(!this.apiStatus && this.loginErrors != null && this.loginErrors.password != null){
                for(var error of this.loginErrors.password){
                    message += error;
                }
            }

            if(!this.apiStatus && this.registerErrors != null && this.registerErrors.name != null){
                for(var error of this.registerErrors.name){
                    message += error + '\n';
                }
            }
            if(!this.apiStatus && this.registerErrors != null && this.registerErrors.email != null){
                for(var error of this.registerErrors.email){
                    message += error + '\n';
                }
            }
            if(!this.apiStatus && this.registerErrors != null && this.registerErrors.password != null){
                for(var error of this.registerErrors.password){
                    message += error;
                }
            }

            if(message != null){
                this.$store.dispatch('common/alertModalUp', {data:UNPROCESSABLE_ENTITY, successMessage:message,close:false});
            }

        },
        registerErrors:function(){
            var message = "";

            if(!this.apiStatus && this.registerErrors != null && this.registerErrors.name != null){
                for(var error of this.registerErrors.name){
                    message += error + '\n';
                }
            }
            if(!this.apiStatus && this.registerErrors != null && this.registerErrors.email != null){
                for(var error of this.registerErrors.email){
                    message += error + '\n';
                }
            }
            if(!this.apiStatus && this.registerErrors != null && this.registerErrors.password != null){
                for(var error of this.registerErrors.password){
                    message += error + '\n';
                }
            }

            if(message != null){
                this.$store.dispatch('common/alertModalUp', {data:UNPROCESSABLE_ENTITY, successMessage:message,close:false});
            }

        },
        messagesList:function(){
            setTimeout(() => {
                this.scrollBottomOfMessage();
            },50);
        }
    },
    props:["jobTypes","category"],
    mixins: [methodMixIn],

}
</script>


<style scoped>

.modal-header,
.modal-body {
    margin:0 auto;
}
.modal-header{
    background:rgba(0, 0, 0, 0.03);
}
.modal-header p {
    text-align:center;
    font-size:25px;
    margin:0 auto;
}
.modal-body {
    text-align:center;
}
.modal-body button {
    margin-top:10px;
}
.login .form-group,
.register .form-group {
    width:70%;
    margin:10px auto;
}
.login .form-group label,
.register .form-group label{
    padding-top:5px;
}
.close {
    position: absolute;
    right: 15px;
    top: 10px;
    font-size: 15px;
    z-index:1;
    cursor:pointer;
}
.login .submit,
.register .submit {
    margin:0px auto 0px auto;
}

.login-switch{
    margin-right:90px;
    font-size:15px;
}
.login-switch:hover {
    cursor: pointer;
}

input[type=radio] {
    font-size:4px;
}
label.userType{
    text-align: left;
    padding-top:0 !important;
}

.editPost .form-group {
    margin:10px auto;
    text-align:left;
}
.editPost .title-form,
.editPost .salary-form {
    display: inline-block;
    width: 49%;
    padding-left: 2%;
    padding-right: 2%;
}
.editPost .container {
    z-index:100;
    height: 100%;
}

.editPost .job-content {
    width:100%;
    margin:auto;
    overflow-y:scroll;
    height:100%;
}
.editPost .form-control {
    width:15%;
    display:inline-block;
    width: 100%;
}
.editPost label {
    text-align:left;
    padding-top:15px;
}
.editPost .submit-form {
    width:100px;
    text-align:center;
}
.editPost .submit-btn {
    padding:20px 20px;
    border-radius:10px;
}
.editPost .job-register-area {  
    border:solid 2px lightgray;
    border-radius:10px;
    padding:20px;
    width:100%;
    color:white;
    height:100%;

    background:gray;
    opacity:0.9;
    overflow-y:scroll;
}

.editPost .form-title {
    font-size:24px;
    color:white;
    font-weight:bold;
}

.profile-item{
    position:relative;
}
.profile-item-label{
    position:absolute;
    top:-22px;
}
.word-break{
    word-break: break-all;
}

.send-message-area{
    position: absolute;
    bottom: 0;
    left: 16px;
    width: 100%;
    background:#dee2e6;
    border-top:1px solid #dee2e6;
    padding-left:60px
}
.send-message-area .input-area{
    text-align:center;
}
.send-message-area .btn{
    padding:6px 12px !important;
    margin-left:0 !important;
    margin-right:0 !important;
    
}
.send-message-area .input-area textarea{
    width: 100%;
    margin-left:5px;
    margin-top:5px;
}
.send-message-area .message-send-btn{
    padding-left:0 !important;
    padding-right:0 !important;
}
.messages-area{
    text-align:none;
    padding-bottom: 10px;
}
.messages-area .balloon1-right {
 position: relative;
    margin: 1.5em 0 1.5em 15px;
    padding: 7px 10px;
    max-width: 45%;
    min-width: 30%;
    color: #555;
    font-size: 16px;
    background: #e0edff;
    margin-left: auto;
    right: 20px;
    border-radius: 15px;
    margin-bottom:0;
    display: inline-block;   

}

.messages-area .balloon1-right:before {
    content: "";
    position: absolute;
    top: 20px;
    left: 100%;
    margin-top: -8px;
    border: 7px solid transparent;
    border-left: 10px solid #e0edff;
    
}

.messages-area .balloon1-right p {
  margin: 0;
  padding: 0;
  text-align: left;
  word-break:break-all;

}

.messages-area .balloon1-left {
    position: relative;
    margin: 1.5em 0 1.5em 15px;
    padding: 7px 10px;
    max-width: 45%;
    min-width: 30%;
    color: #555;
    font-size: 16px;
    background: #E4E1E1;
    border-radius: 15px;
    margin-bottom:0;
    margin-left: 10px;
    display: inline-block;   
}

.messages-area .balloon1-left:before {
    content: "";
    position: absolute;
    top: 20px;
    left: -16px;
    margin-top: -8px;
    border: 7px solid transparent;
    border-right: 10px solid #E4E1E1;
}
.messages-area .balloon1-left p {
  margin: 0;
  padding: 0;
  word-break:break-all;
}
.messages-area .message-time-left {    
    margin-left: 4%;
    color:gray;
}
.messages-area .message-time-right {    
    margin-right: 5%;
    text-align:right;
    color:gray; 
}
.message-user-name {
    margin-left: 3%;
    color:gray; 
    width: 40%;
}
.company-logo-message {
    width: 50px;
    height: 50px;
    display: inline-block;
    vertical-align: top;
    margin-top:1.5em;
}
.company-logo-message img{
    width:100%;
    height: 100%;
}
.message-right{
    text-align:right;
}
.message-to{
    margin-left:25px;
}

.message-enter,
.message-leave-to,
.message-leave {
  opacity: 0
}
.message-enter-active,
.message-leave-active {
    width:100%;
    position:absolute; 
}
.message-move {
  transition: transform 500ms;
}
.country-bg-image {
    width:200px;
    height:200px;
}
.form-group label{
    padding-left:0;
}
.alert .modal-body {
    height: 55%;
    padding: 15px 3px 0 3px;
}
.deleteUserModal .button-area {
    margin-top: 25px;
    width: 75%;
    text-align: center;
    margin-right: auto;
    margin-left: auto;
}
</style>
<style>
.pointer{
    cursor:pointer;
}
.vm--modal {
    border-radius:10px !important;
}
@media(max-width:767px){
    .vm--modal{
        width: 90% !important;
        margin-right: auto !important;
        margin-left: auto !important;
        left:auto !important;
        right:auto !important;
    }
    .postClose .vm--modal{
        height:auto !important;
    }
    .editPost .vm--modal{
        height:500px !important;
    }
    .modal-header p{
        font-size:17px !important;
    }
    .message-to{
        margin-left:3px;
    }
    .send-message-area{
        padding-left:15px !important;
    }
}
</style>