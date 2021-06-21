<template> 

        <div>
            <modal name="login" :draggable="false" :height="'auto'" class="login sm-modal">

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

            <modal name="registerAdmin" :draggable="false" :height="'auto'" class="registerAdmin" style="height:2000px">

                <div class="modal-header">
                    <div v-if="alert" class="alert">
                        <p>{{message}}</p>
                    </div>   
                    <p>Register Admin</p>
                    <div v-on:click="modalHide('registerAdmin')" class="close">X</div>
                </div>

                <div class="modal-body">
                    <form class="form" @submit.prevent="register(true)" style="padding:30px;margin-right:30px;overflow-y:scroll">

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

                            <place-show-component @select-city='selectCity' @loading-map='loadingOrNot' :initVal="editPostForm.city" :initAddressObj="editPostForm.addressObj" :initMarkers="editPostForm.latLng" ref="placeShowComponent"></place-show-component>
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

                        <label>
                            Video: 
                        </label>
                        <video-upload-component ref="videoUploadComponent" :initVideo="editPostForm.videoUrl"></video-upload-component>                      

                        <div class="form-group submit-form">
                            <button class="btn submit-btn" @click="updatePost" v-bind:disabled="mapLoading">Submit</button>       
                        </div>
                    </div>
                </div>
            </modal>
    

            <modal name="apply" :draggable="false" :height="'auto'" class="apply">
                <div class="modal-header">
                    <p>こちらの求人に応募しますか？</p>
                    <div v-on:click="modalHide('apply')" class="close">X</div>
                </div>
                <div class="modal-body" style="width:100%;">

                    <div  style="text-align:left;margin:auto;margin-left:20%">
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

                    <div class="row border mt-0 mx-4" style="border-radius:5px">
                        <div class="col-md-6 col-sm-12 row  pr-2" style="margin-top:12px">
                            <div class="col-4 pr-0"><strong>Name: </strong></div>
                            <div class="col-8 text-left pr-0" v-if="applicantProfile.user.user_lastname != null && applicantProfile.user.user_firstname != null">
                                {{applicantProfile.user.user_lastname}} {{applicantProfile.user.user_firstname}}
                            </div>
                        </div>

                        <div class="col-md-6 col-sm-12 row  pr-2">
                            <div class="col-4 pr-0"><strong>Email: </strong></div>
                            <div class="col-8 text-left pr-0" v-if="applicantProfile.user.email != null">
                            {{applicantProfile.user.email}}
                            </div>
                        </div>

                        <div class="col-md-6 col-sm-12 row  pr-2">
                            <div class="col-4 pr-0"><strong>Birth Day: </strong></div>
                            <div class="col-8 text-left pr-0" v-if="applicantProfile.user.user_birthday != 'null'">
                                {{applicantProfile.user.user_birthday}}
                            </div>
                        </div>

                        <div class="col-md-6 col-sm-12 row  pr-2">
                            <div class="col-4 pr-0"><strong>Gender: </strong></div>
                            <div class="col-8 text-left pr-0">
                                {{applicantProfile.gender}}
                            </div>
                        </div>
                    </div>

                    <div class="profile-item border m-4 p-3 mt-5">
                        <div class="h-3 profile-item-label"><strong>Experience</strong></div>
                        <div class="text-left" v-for="ex in applicantProfile.experiences" :obj="ex" :key="ex.id">
                            <span style="width:20%">{{ex.category.category_name}}</span> - <span style="width:70%">{{ex.experience_years}} years</span>
                        </div>
                    </div>

                    <div class="profile-item border m-4 p-3 mt-5">
                        <div class="h-3 profile-item-label"><strong>Experience Details</strong></div>
                        <div class="text-left" style="white-space:pre-line;overflow-wrap: break-word;">{{applicantProfile.experience}}</div>
                    </div>


                    <div class="profile-item border m-4 p-3 mt-5">
                        <div class="h-3 profile-item-label"><strong>Skills</strong></div>
                        <div class="text-left" style="white-space:pre-line;overflow-wrap: break-word;">{{applicantProfile.skill}}</div>
                    </div>


                    <div class="profile-item border m-4 p-3 mt-5">
                        <div class="h-3 profile-item-label"><strong>Education</strong></div>
                        <div class="text-left" style="white-space:pre-line;overflow-wrap: break-word;">{{applicantProfile.education}}</div>
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

                <div class="modal-header" style="position:relative;padding:3px;">
                    <p v-if="loginUser !=  null && loginUser.user_type == 'U'">Messages <br> <span class=" message-to">[ To: {{applyRecord.companyName}} ]</span></p>
                    <p v-else-if="loginUser !=  null && loginUser.user_type == 'C'">Messages <br><span class=" message-to">[ To: {{applyRecord.applicantName}} ]</span></p>
                    <div v-on:click="modalHide('messageModal')" class="close">X</div>
                    <div class="btn btn-info interview-btn" @click="interviewPopUpFlg = !interviewPopUpFlg" v-if="loginUser !=  null && loginUser.user_type == 'C'">
                        interview
                    </div>

                </div>

                <div class="messages-area" id="messages-area" style="height:374px;overflow-y:scroll" v-if="messagesList != null" >
                    <div class="schedule-interview" v-if="interviewPopUpFlg">
                        <div class="triangle"></div>
                        <div class="schedule-interview-header">
                            Web面接を設定
                            <div data-v-47cf1a59="" class="close" @click="interviewPopUpFlg = false">X</div>
                        </div>
                        <div class="schedule-interview-body">

                            <div class="schedule-element row">
                                <div class="col-5" style="padding-top: 20px;">面接日時</div>
                                <div class="col-7"><input type="date"  v-model="interviewForm.interviewDate" name="interview_date" id="interview_date" class="form-control" value="" required=""></div>
                            </div>
                            <div class="schedule-element row">
                                <div class="col-5" style="padding-top: 20px;">面接時間</div>
                                <div class="col-7"><input type="time"  v-model="interviewForm.interviewTime" name="interview_date" id="interview_date" class="form-control" value="" required=""></div>
                            </div>

                            <div class="schedule-element row">
                                <div class="col-5" style="padding-top: 20px;font-size: 15px;">面接予定時間（分）</div>
                                <div class="col-7"><input type="number"  v-model="interviewForm.interviewDuration" name="interview_duration" id="interview_duration" class="form-control" value="" required=""></div>
                            </div>
                            <div style="text-align: center;margin-top: 20px;">
                                <div class="btn btn-info" @click="setUpInterview()">
                                    send
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="message" @click="interviewPopUpFlg = false">
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

            <modal name="scoutModal" :draggable="false" class="scoutModal" :height="520">
                <div class="modal-header">
                    <p>Scout</p>
                    <div v-on:click="modalHide('scoutModal')" class="close">X</div>
                </div>
                <div class="modal-body" style="width:100%;padding-top:15px" v-if="scoutInfo != null">

                    <div class="scout-job-area">
                        <div style="pading-left:5%">
                            Scout for :
                        </div>

                        <div v-if="scoutInfo.id != null" class="scout-job-check-area">

                            <div v-for="job in scoutInfo.sender.company.jobs" :obj="job" :key="job.id" class="scout-job-check">
                                <input type="checkbox" :id="job.id" name="registeredJobs" :value="job.id" v-model="scoutInfo.scoutJobList" disabled="true">
                                <label :for="job.id" style="display: inline;">{{job.job_title}}</label>
                            </div>

                        </div>
                        <div v-else class="scout-job-check-area">
                            <div v-for="job in scoutInfo.registeredJobs" :obj="job" :key="job.id" class="scout-job-check">
                                <input type="checkbox" :id="job.id" name="registeredJobs" :value="job.id" v-model="scoutInfo.scoutJobList">
                                <label :for="job.id" style="display: inline;">{{job.job_title}}</label>
                            </div>
                        </div>
                    </div>  


                    <div v-if="scoutInfo.id != null" >
                        <div style="margin-top: 10px;text-align:left;">Scout Message has been sent <span style="font-size: 12px;float: right;padding-top: 5px;">({{scoutInfo.created_at}})</span></div>
                        <div class="sent-scout-message">{{scoutInfo.scout_message}}</div>
                    </div>  
                    <div v-else>
                        <div style="margin-top: 10px;text-align:left;">Scout Message</div>

                        <div>
                            <textarea class="form-control" rows="10" v-model="scoutInfo.scout_message"></textarea>
                        </div>
                    </div>  

                    <div class="row button-area">
                        <div class="col-6" :class="{width30:scoutInfo.id != null}">
                            <button  @click="modalHide('scoutModal')" name="scoutModal" class="btn btn-block btn-warning btn-md p-2 text-danger">
                                <span class="icon-heart-o mr-2 text-danger">閉じる</span>
                            </button>
                        </div>
                        <div v-if="scoutInfo.id == null" class="col-6">
                            <button @click="sendScout" type="button" class="btn btn-primary p-2 btn-block">
                                送信
                            </button>
                        </div>
                    </div>
                </div>  
            </modal> 

            <modal name="interviewModal" :draggable="false" :resizable="false" :height="500" :width="700" style="text-align:center;" class="interview">
                <div v-on:click="modalHide('interviewModal')" class="close">X</div>
                <div class="interviewPopUp">
                    <InterviewCalendarComponent ref="interviewComponent"></InterviewCalendarComponent>
                </div>
            </modal> 

            <modal name="interviewConfirmModal" :draggable="false" class="interviewConfirmModal">
                <div class="modal-header">
                    <p v-if="interviewCancelTarget != null">
                        <span v-if="interviewCancelTarget.extendedProps != null && interviewCancelTarget.extendedProps.data.user_type == 'C'">受験者</span>
                        <span v-else>企業名</span>
                        ：{{interviewCancelTarget.title}}</p>
                    <div v-on:click="modalHide('interviewConfirmModal')" class="close">X</div>
                </div>
                <div class="modal-body" style="width:100%;padding-top:40px;">

                    <div>
                        <div v-if="interviewCancelTarget.start != null">日時：{{interviewCancelTarget.start.toLocaleString("ja")}}</div>
                        <div v-if="interviewCancelTarget.extendedProps != null">予定時間：{{interviewCancelTarget.extendedProps.data.video_duration}} 分</div>
                    </div>  

                    <div class="row button-area" style="margin-top:30px">
                        <div class="col-6" :class="{marginAuto:interviewCancelTarget.extendedProps != null && interviewCancelTarget.extendedProps.data.user_type == 'U'}">
                            <button  v-bind:disabled="checkInterviewTime(interviewCancelTarget.start)" @click="startVideoChat(interviewCancelTarget.extendedProps.data)" name="interviewConfirmModal" class="btn btn-block btn-warning btn-md p-2 text-danger interviewConfirmModalBtn">
                                <span class="icon-heart-o mr-2 text-danger">開始する</span>
                            </button>
                        </div>
                        <div class="col-6" v-if="interviewCancelTarget.extendedProps != null && interviewCancelTarget.extendedProps.data.user_type == 'C'">
                            <button v-bind:disabled="checkInterviewTime(interviewCancelTarget.start)" @click="cancelInterviewConfirm" type="button" class="btn btn-primary p-2 btn-block interviewConfirmModalBtn">
                                キャンセルする
                            </button>
                        </div>
                    </div>
                </div>  
            </modal> 

            <modal name="interviewCancelConfirmModal" :draggable="false" :resizable="false" :height="'auto'" :width="320" class="interviewCancelConfirmModal">
                <div class="modal-header">
                    <p style="font-size: 15px;">こちらの面接をキャンセルしますか？</p>
                </div>
                <div class="modal-body" style="width:90%;">

                    <div class="row">
                        <div class="col-6">
                            <button v-on:click="modalHide('interviewCancelConfirmModal')" name="closePost" class="btn btn-block btn-warning btn-md p-2 text-danger">
                                <span class="icon-heart-o mr-2 text-danger">閉じる</span>
                            </button>
                        </div>
                        <div class="col-6">
                            <button @click="cancelInterview" type="button" class="btn btn-primary p-2 btn-block">
                                送信する
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
import InterviewCalendarComponent from '../common/InterviewCalendarComponent.vue';

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
        interviewPopUpFlg:false,
        interviewForm:{
            interviewDate:"",
            interviewTime:"",
            interviewDuration:"",
            applyId:""
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
        mapLoading:false
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
                    this.$modal.hide('scoutModal');
                    this.$modal.hide('interviewConfirmModal');
                    this.$modal.hide('interviewCancelConfirmModal');
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
            this.$store.commit('common/setLoadingFlg', true);

            await this.$store.dispatch('auth/login', this.loginForm);

            this.$store.commit('common/setLoadingFlg', false);

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
            this.$store.commit('common/setLoadingFlg', true);

            await this.$store.dispatch('auth/register', this.registerForm)
    
            this.$store.commit('common/setLoadingFlg', false);

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
            this.editPostForm.addressObj = postInfo.addressObj;
            console.log("postInfo.addressObj");
            console.log(postInfo.addressObj);
            if(postInfo.addressObj != null && postInfo.addressObj != undefined){
                this.editPostForm.latLng =  { lat: postInfo.addressObj.lat, lng: postInfo.addressObj.lng } ;
            }
            this.editPostForm.videoUrl = postInfo.videoUrl;

        },
        updatePost:function(){
            var tc = this;
            this.editPostForm.tagList = this.$refs.selectTagComponent.selectedTagList;

            this.editPostForm.latLng = this.$refs.placeShowComponent.lastMarkerPosition,
            this.editPostForm.mapFlg = this.$refs.placeShowComponent.mapShow
            this.editPostForm.videoFile = this.$refs.videoUploadComponent.uploadVideoUrl;
            
            this.$store.commit('common/setLoadingFlg', true);

            axios.post('/api/updatePost', this.editPostForm).then(res => {
    
                this.$store.commit('common/setLoadingFlg', false);

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
            this.$store.commit('common/setLoadingFlg', true);

            axios.post('/api/apply', data).then(res => {

                this.$store.commit('common/setLoadingFlg', false);
                console.log(res);
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
            this.$store.commit('common/setLoadingFlg', true);

            axios.post('/api/sendMessage', this.messageForm).then(res => {

                this.$store.commit('common/setLoadingFlg', false);
                this.messageForm.message = "";          
                this.$store.dispatch('common/refreshMessage', this.applyRecord.id)
                this.$store.dispatch('common/alertModalUp', {data:res.status, successMessage:'送信しました'});
            });
        },
        setUpInterview:function(){
            this.interviewForm.applyId = this.applyRecord.id;

            if(this.interviewForm.interviewDate == ""){
                this.$store.dispatch('common/alertModalUp', {data:200, successMessage:'面接日を入力して下さい'});
                reutrn;
            }else if(this.interviewForm.interviewTime == ""){
                this.$store.dispatch('common/alertModalUp', {data:200, successMessage:'面接時間を入力して下さい'});
                reutrn;
            }else if(this.interviewForm.interviewDuration == ""){
                this.$store.dispatch('common/alertModalUp', {data:200, successMessage:'面接予定時間を入力して下さい'});
                reutrn;
            }else{
                this.$store.commit('common/setLoadingFlg', true);
                axios.post('/api/setUpInterview',this.interviewForm).then(res => {
                    this.$store.commit('common/setLoadingFlg', false);
    
                    this.$store.dispatch('common/alertModalUp', {data:res.status, successMessage:'面接を予約しました'});
                    this.$store.dispatch('common/refreshMessage', this.applyRecord.id)
                    this.interviewPopUpFlg = false;
                    this.interviewForm.interviewDate = "";
                    this.interviewForm.interviewTime = "";
                    this.interviewForm.interviewDuration = "";

                });
            }



        },
        closePost(){
            this.$store.commit('common/setLoadingFlg', true);

            axios.post('/api/closePost', {"targetPostId":this.singlePost.id}).then(res => {
                this.$store.commit('common/setLoadingFlg', false);

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
  
            this.$store.commit('common/setLoadingFlg', true);

            await axios.post('/api/registerCountry', formData).then(res => {
        
                this.$store.commit('common/setLoadingFlg', false);

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
            this.$store.commit('common/setLoadingFlg', true);

            await this.$store.dispatch('auth/userDelete', this.deleteTargetUser);

            this.$store.commit('common/setLoadingFlg', false);

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
            this.$store.commit('common/setLoadingFlg', true);

            this.download(data);

            this.$store.commit('common/setLoadingFlg', false);

        },
        async changePassword(){
            console.log('changePass');
            this.$store.commit('common/setLoadingFlg', true);
            await axios.post('/api/passwordUpdate',this.passwordResetForm).then(res => {
                this.$store.commit('common/setLoadingFlg', false);
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
            this.$store.commit('common/setLoadingFlg', true);

            await axios.post('/api/password/email',this.forgotPasswordForm).then(res => {

                this.$store.commit('common/setLoadingFlg', false);

                if(res.status != 200){
                    return false;
                }
                this.$store.dispatch('common/alertModalUp', {data:res.status, successMessage:'パスワード再設定メールを送信しました'});
            })
            .catch(res => {
                this.$store.commit('common/setLoadingFlg', false);

                this.$store.dispatch('common/alertModalUp', {data:res.status, successMessage:'メールアドレスが見つかりませんでした。\nメールアドレスをご確認ください。'});
            });
        },
        async googleLogin(){
            this.$store.commit('common/setLoadingFlg', true);
            console.log('google login');
            location.href = "/api/auth/google";
        },
        sendScout:function(){

            if(this.scoutInfo.scoutJobList.length < 1){
                this.$store.dispatch('common/alertModalUp', {data:OK, successMessage:'スカウトする求人を選択して下さい',close:false});
                return;
            }
            this.$store.commit('common/setLoadingFlg', true);
            axios.post('/api/sendScout',this.scoutInfo).then(res => {
                this.$store.commit('common/setLoadingFlg', false);
                if(res.status != 200){
                    return false;
                }
                this.$store.dispatch('common/alertModalUp', {data:res.status, successMessage:'スカウトを送信しました。'});

                //searchUserComponentのscoutボタンをscouted表示にするためidを渡す
                var scoutedList = this.$store.state.common.scoutedIds;
                console.log(scoutedList);
                scoutedList.push(this.scoutInfo.reciever_id);
                this.$store.commit('common/setScoutedIds', scoutedList);

            })
            .catch(res => {
                this.$store.commit('common/setLoadingFlg', false);

                this.$store.dispatch('common/alertModalUp', {data:res.status, successMessage:'スカウトメッセージ送信に失敗しました。'});
            });
        },
        loadingOrNot:function(flg){
            this.mapLoading = flg;
        },
        cancelInterviewConfirm:function(){
            this.modalShow("interviewCancelConfirmModal");
        },
        cancelInterview:function(){
            this.$store.commit('common/setLoadingFlg', true);

            axios.post('/api/cancelInterview',{id:this.interviewCancelTarget.id}).then(res => {
                this.$store.commit('common/setLoadingFlg', false);
                if(res.status != 200){
                    return false;
                }
                this.$store.dispatch('common/alertModalUp', {data:res.status, successMessage:'WEB面接をキャンセルしました。',close:true});
                this.$refs.interviewComponent.removeInterview(this.interviewCancelTarget.id);
            })
            .catch(res => {
                this.$store.commit('common/setLoadingFlg', false);

                this.$store.dispatch('common/alertModalUp', {data:res.status, successMessage:'キャンセル処理でエラーが発生しました。'});
            });

        },
        startVideoChat:function(data){

            window.open(data.video_url, "name", 'width=900, height=620, top=0, left=0, personalbar=0, toolbar=0, scrollbars=1, resizable=!'); 
        },
        checkInterviewTime:function(time){
            var interviewTime = new Date(time);
            var interviewNextDate = interviewTime.setDate( interviewTime.getDate() + 1 );
            var now = new Date();

            if(interviewNextDate < now){
                return true;
            }else{
                return false;
            }
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
        },
        scoutInfo:function(){
            return this.$store.state.common.scoutInfo;
        },
        interviewCancelTarget:function(){
            return this.$store.state.common.interviewCancelTarget;
        },
        liveMessage:function(){
            return this.$store.state.common.liveMessage;
        },




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
                this.$store.dispatch('common/alertModalUp', {data:UNPROCESSABLE_ENTITY, successMessage:message,close:true});
            }

        },
        messagesList:function(){
            if(document.getElementById("messages-area") == undefined){
                return;
            }
            setTimeout(() => {
                this.scrollBottomOfMessage();
            },50);
        },
        liveMessage:function(val,old){
            if(document.getElementById("messages-area") != undefined && this.applyRecord != undefined && this.applyRecord.id == val.message.applyId){
                this.$store.dispatch('common/refreshMessage', val.message.applyId);
            }
        }
    },
    props:["jobTypes","category"],
    mixins: [methodMixIn],
    components: {
        InterviewCalendarComponent
    }

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

.register .form-group {
    margin:5px auto;
}
.login .form-group label,
.register .form-group label{
    padding-top:5px;
}
.register .form-group label{
    padding-bottom:5px;
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
    border-radius: 5px;
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
    width: 105%;
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
    background: white;
    border-radius: 5px;
}
.send-message-area .message-send-btn{
    padding-left:0 !important;
    padding-right:0 !important;
}
.messages-area{
    text-align:none;
    padding-bottom: 10px;
}
.message-text{
    position:relative;
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
    white-space:break-spaces;

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
    white-space:break-spaces;
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
    margin-left: 3px;

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
    font-size:15px;
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

.interview-btn{
    padding: 5px 3px;
    font-size: 10px;
    position: absolute;
    right: 0;
    bottom: 0;
}
.schedule-interview{
    position: absolute;
    right: 3px;
    z-index: 100;
    opacity: 1;
    min-width: 300px;
    margin-top: 5px;
    width: 50%;
}
.schedule-interview-header{
    background:lightgray;
    padding:2%;
    text-align:center;
    border-radius:10px 10px 0 0 ;
}
.schedule-interview-body{
    background:white;
    border:1px solid lightgray;
    padding:20px;
    border-radius:0 0 5px 5px;
}

.interviewConfirmModalBtn{
    width: 60%;
    margin: auto;
}

.triangle{
    border-right: 10px solid transparent;
    border-bottom: 10px solid lightgray;
    border-left: 10px solid transparent;
    width: 13px;
    right: 20px;
    top: -10px;
    position: absolute;
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
.width30{
    width:30%;
    flex: unset;
    max-width: unset;
    margin:auto;
}
.sent-scout-message{
    text-align: left;
    border: gray 1px solid;
    padding: 15px;
    height: 250px;
    border-radius: 5px;
    overflow-y: scroll;
    white-space:pre-line;
    overflow-wrap: break-word;
}
.scout-job-area{
    text-align: left;
}

.scout-job-check-area{
    padding-left: 5%;
    height: 60px;
    border: solid 1px #80808038;
    overflow-y: scroll;
    border-radius: 5px;
}
.scout-job-check{
    width: auto;
    display: inline-block;
    margin-right: 5%;
}
.scout-job-check input, .scout-job-check label{
    cursor:pointer;
}

.interviewPopUp{
    width:100%;
    background:white;
    padding:0 5px 5px 5px;
    z-index: 99;
    border: solid lightgray 4px;
    border-radius: 10px;
    max-height:100%;
    min-height:100%;
    overflow-y: scroll;
}
.marginAuto{
    margin:auto;
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