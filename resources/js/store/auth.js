import { OK, CREATED, UNPROCESSABLE_ENTITY ,TOO_MANY_ATTEMPT} from '../util'

const state = {
  user: null,
  apiStatus: null,
  loginErrorMessages: null,
  registerErrorMessages: null,
  loginCheck: false,
  likeList:[],
  applyList:[],
  newMessageExistFlg:false,
  lastDeletedUser:null,
  routePath:'/jobsList'
}

const getters = {
  check: state => !! state.user,
  loginUser: state => state.user ? state.user : '',
  routePath: state => state.routePath
}

const mutations = {
  setUser (state, user) {
    state.user = user
  },
  setApiStatus (state, status) {
    state.apiStatus = status
  },
  setLoginErrorMessages (state, messages) {
    state.loginErrorMessages = messages
  },
  setRegisterErrorMessages (state, messages) {
    state.registerErrorMessages = messages
  },
  setLoginCheck(state, messages) {
    state.loginCheck = messages
  },
  setLikeList(state, list) {
    state.likeList = list
  },
  setApplyList(state, list) {
    state.applyList = list
  },
  setNewMessageExistFlg(state, list) {
    state.newMessageExistFlg = list
  },
  setLastDeletedUser(state, user){
    state.lastDeletedUser = user
  }
}

const actions = {
  // 会員登録
  async register (context, data) {
    context.commit('setApiStatus', null)
    const response = await axios.post('/api/register', data);

    if (response.status === CREATED) {
      context.commit('setApiStatus', true)
      context.commit('setUser', response.data)
      context.commit('setLoginCheck', true)

      return false
    }

    context.commit('setApiStatus', false)
    if (response.status != OK) {
      context.commit('error/setCode', response.status, { root: true })
      context.commit('setLoginErrorMessages', response.data.errors)
    } else {
      context.commit('error/setCode', response.status, { root: true })
    }
  },

  // ログイン
  async login (context, data) {

      context.commit('setApiStatus', null)
      const response = await axios.post('/api/login', data)
      
      if (response.status === OK) {
        context.commit('setApiStatus', true)
        context.commit('setUser', response.data)
        context.commit('setLoginCheck', true)        
        return false
      }
    
      
    context.commit('setApiStatus', false)
    if (response.status != OK) {
      context.commit('error/setCode', response.status, { root: true })
      context.commit('setLoginErrorMessages', response.data.errors)
    } else {
      context.commit('error/setCode', response.status, { root: true })
    }
  },

  // ログアウト
  async logout (context) {
    context.commit('setApiStatus', null)
    const response = await axios.post('/api/logout')

    if (response.status === OK) {
      context.commit('setApiStatus', true)
      context.commit('setUser', null)
      context.commit('setLoginCheck', false)

      context.commit('setLikeList',[]);
      context.commit('setApplyList',[]);
      context.commit('setNewMessageExistFlg',false);
      return false
    }

    context.commit('setApiStatus', false)
    context.commit('error/setCode', response.status, { root: true })

  },

  async currentUser (context) {
    context.commit('setApiStatus', null)
    const response = await axios.get('/api/user')
    const user = response.data || null

    if (response.status === OK) {
      context.commit('setApiStatus', true)
      context.commit('setUser', user)
      if(user != null){
        context.commit('setLoginCheck', true)
      }
      console.log('cuurent')

      return false
    }

    context.commit('setApiStatus', false)
    context.commit('error/setCode', response.status, { root: true })
  },

  async accessAllowCheck (context) {
    context.commit('setApiStatus', null)
    const response = await axios.get('/api/user')
    const user = response.data || null

    if (response.status === OK) {
      context.commit('setApiStatus', true)
      if(user != null){
        console.log('access OK')
        console.log(user)
        return true
      }
      console.log('access NG')
      return false
    }

    context.commit('setApiStatus', false)
    context.commit('error/setCode', response.status, { root: true })
  },

  async getLikeList (context) {
    context.commit('setApiStatus', null)
    const response = await axios.get('/api/getLikeList')
    const list = response.data || null

    if (response.status === OK) {
      context.commit('setApiStatus', true)
      if(list != null){
        context.commit('setLikeList', list)
      }
      
      return false
    }

    context.commit('setApiStatus', false)
    context.commit('error/setCode', response.status, { root: true })
  },

  async getApplyList (context) {
    context.commit('setApiStatus', null)
    const response = await axios.get('/api/getApplyList')
    const list = response.data || null

    if (response.status === OK) {
      context.commit('setApiStatus', true)
      if(list != null){
        context.commit('setApplyList', list)
      }
      
      return false
    }

    context.commit('setApiStatus', false)
    context.commit('error/setCode', response.status, { root: true })
  },
  prepareList(context){

    context.dispatch('getApplyList')
    context.dispatch('getLikeList')
    context.dispatch('getNewMessageExistFlg')

  },

  async getNewMessageExistFlg (context) {
    context.commit('setApiStatus', null)
    const response = await axios.get('/api/getNewMessageExistFlg')
    const list = response.data
    if (response.status === OK) {
      context.commit('setApiStatus', true)
      if(list != null){
        
        context.commit('setNewMessageExistFlg', response.data)
      }
      return false
    }

    context.commit('setApiStatus', false)
    context.commit('error/setCode', response.status, { root: true })
  },
  async userDelete(context, user){
    var that = this;
    context.commit('setApiStatus', null)
    const response = await axios.post('/api/userDelete',{"userId":user.id});

    if (response.status === OK) {
      context.commit('setApiStatus', true)
      context.commit('setLastDeletedUser', user)
      return false
    }

    context.commit('setApiStatus', false)
    context.commit('error/setCode', response.status, { root: true })
    return false;
  }
}

export default {
  namespaced: true,
  state,
  getters,
  mutations,
  actions
}