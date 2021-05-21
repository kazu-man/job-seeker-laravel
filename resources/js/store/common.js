
const state = {
    modalTarget:"",
    alertModalMessage:null,
    editPost:null,
    updatedPost:null,
    applyTargetPost:null,
    applicantProfile:null,
    singlePost:null,
    messagesList:null,
    applyRecord:null,
    commonDataForModal:null,
    deleteTargetUser:null,
    settingCountryReloadFlg:false,
    scoutInfo:null,
    scoutedIds:[],
    loadingFlg:false,
    interviewCancelTarget:[],
    liveMessage:null
  }
    
const mutations = {
    setModalTarget(state, target) {
      state.modalTarget = target
    },
    setEditPost(state, target) {
        state.editPost = target
      },
    setUpdatedPost(state, target) {
        state.updatedPost = target
    },
    setAlertModalMessage(state, target) {
        state.alertModalMessage = target
    },
    setApplyTargetPost(state, target) {
        state.applyTargetPost = target
    },
    setApplicantProfile(state, target) {
        state.applicantProfile = target
    },
    setSinglepost(state, target) {
        state.singlePost = target
    },
    setMessagesList(state, target){
        state.messagesList = target
    },
    setApplyRecord(state, target){
        state.applyRecord = target
    },
    setCommonDataForModal(state, target){
        state.commonDataForModal = target
    },
    setDeleteTargetUser(state, target){
        state.deleteTargetUser = target
    },
    setSettingCountryReloadFlg(state, target){
        state.settingCountryReloadFlg = target
    },
    setScoutInfo(state, target){
        state.scoutInfo = target
    },
    setScoutedIds(state, target){
        state.scoutedIds = target
    },
    setLoadingFlg(state, target){
        state.loadingFlg = target
    },
    setInterviewCancelTarget(state, target){
        state.interviewCancelTarget = target

    },
    setLiveMessage(state, target){
        state.liveMessage = target

    }


}

const getters = {
    scoutedIds: state => state.scoutedIds,
    loadingFlg: state => state.loadingFlg,
  }
 
const actions = {
    setApplyModal(context, post){

        context.commit('setApplyTargetPost', post);
        context.commit('setModalTarget', 'apply');

    },
    setEditForm(context, editForm){

        context.commit('setEditPost', editForm);
        context.commit('setModalTarget', 'editPost');
    
    },
    setApplicantProfileModal(context,profileId){
        axios.get('/api/getApplicantProfile/' + profileId).then(res => {
            console.log(res.data);

            context.commit('setApplicantProfile', res.data);
            context.commit('setModalTarget', 'applicantProfile');
                
        });
    },
    setSinglePostModal(context,postId){
        axios.get('/api/getSinglePost/' + postId).then(res => {
            console.log(res.data);

            context.commit('setSinglepost', res.data[0]);
            context.commit('setModalTarget', 'singlePost');
                
        });
    },
    refreshMessage(context,recordId){
        axios.get('/api/getMessages/' + recordId).then(res => {
            context.commit('setMessagesList', res.data);                
        });
    },
    setMessageModal(context,record){
        axios.get('/api/getMessages/' + record.id).then(res => {
            context.commit('setLoadingFlg', false);
            context.commit('setMessagesList', res.data);
            context.commit('setApplyRecord', record);
            context.commit('setModalTarget', 'messageModal');
        });
    },
    setPostCloseModal(context,post){
        context.commit('setModalTarget', 'postClose');
        context.commit('setSinglepost', post);
    },
    alertModalUp (context,　{data, successMessage, close, reload}) {
        console.log('modal up');
        if(close == null){
            close = true;
        }
        if(reload == null){
            reload = false;
        }

        context.commit('setAlertModalMessage', {"successMessage":successMessage, "status":data, "close":close,"reload":reload})
        context.commit('setModalTarget', 'alert')
    },
    setBgChangeModal(context,countryData){
    
        context.commit('setCommonDataForModal', countryData);
        context.commit('setModalTarget', 'bgChangeModal');
    },
    setUserDeleteModal(context,user){
        context.commit('setModalTarget', 'deleteUserModal');
        context.commit('setDeleteTargetUser', user);
    },
    setScoutModal(context,record){

        axios.post('/api/getScout' ,record).then(res => {
            console.log(res.data);
            var response = res.data;

            context.commit('setScoutInfo', response);
            context.commit('setModalTarget', 'scoutModal');
        });
    },
    setInterviewModal(context){
    
        context.commit('setModalTarget', 'interviewModal');
    },
    setInterviewConfirmModal(context,event){

        context.commit('setInterviewCancelTarget', event);
        context.commit('setModalTarget', 'interviewConfirmModal');

    }

  
}




export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions
  }