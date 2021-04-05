import Vue from 'vue'
import VueRouter from 'vue-router'

// ページコンポーネントをインポートする
import JobList from './components/jobList/jobListMain.vue'
import Setting from './components/setting/SettingMain.vue'
import SystemError from './system.vue'
import jobsRegisterComponent from './components/common/JobsRegisterComponent.vue'
import top from './components/jobList/TopComponent.vue'
import postsList from './components/jobList/PostsListComponent.vue'
import profile from './components/jobList/ProfileComponent.vue'
import applyRecordTable from './components/jobList/ApplyRecordTable.vue'
import passwordReset from './components/resetPassword/resetPassword.vue'
import userShow from './components/setting/UserShowComponent.vue'
import categoryShow from './components/setting/CategoryShowComponent.vue'
import AdminPlaceShowComponent from './components/setting/AdminPlaceShowComponent.vue'
import SearchUserComponent from './components/jobList/SearchUserComponent.vue'
import MyScoutListComponent from './components/jobList/MyScoutListComponent.vue'

import store from './store' 
import { UNAUTHORIZED } from './util'

// VueRouterプラグインを使用する
// これによって<RouterView />コンポーネントなどを使うことができる
Vue.use(VueRouter)

// パスとコンポーネントのマッピング
const routes = [
  {
    path: store.getters['auth/routePath'],
    component: JobList,
    children: [
        {
            path: '',
            props: { pageType: "top",initPage: "top" },
            component: top
          },
          {
            path: 'top',
            props: { pageType: "top",initPage: "top" },
            component: top
          },    
          {
            path: 'password/reset/:token',
            component: passwordReset
            },          
          {
            path: 'country/:country',
            props: { pageType: "country",initPage: "country" },
            component: top,
  
          },
          {
            path: 'category/:category',
            props: { pageType: "category",initPage: "category" },
            component: top,
  
          },

        {
            path: 'postJob/:id',
            component: jobsRegisterComponent,
            beforeEnter (to, from, next) {
              store.dispatch('auth/accessAllowCheck')
              .then(value => {
                const id = to.params.id
                if (store.state.auth.user != null && value && id == store.state.auth.user.company_id) {
                    next()
                  } else {
                    window.location.href = "/";
                  }
              });
            }
          },  
          {
            path: 'posts/:id',
            component: postsList,
            props: { pageType: "posts",initPage: "posts" },
            beforeEnter (to, from, next) {
              store.dispatch('auth/accessAllowCheck').then(value => {
                const id = to.params.id
                if (store.state.auth.user != null && value && id == store.state.auth.user.company_id) {
                    next()
                  } else {
                    window.location.href = "/";
                  }
              });
            }
          },  

          {
            path: 'appliesList/:id',
            component: applyRecordTable,
            beforeEnter (to, from, next) {
              store.dispatch('auth/accessAllowCheck').then(value => {
                const id = to.params.id
                if (store.state.auth.user != null && value && id == store.state.auth.user.company_id) {
                    next()
                  } else {
                    window.location.href = "/";

                  }
              });
            },
          },  
          {
            path: 'sendScout',
            component: SearchUserComponent,
            beforeEnter (to, from, next) {
              store.dispatch('auth/accessAllowCheck')
              .then(value => {
                const id = to.params.id
                if (store.state.auth.user != null && value && store.state.auth.user.user_type == "C") {
                    next()
                  } else {
                    window.location.href = "/";
                  }
              });
            }
          },  
          {
            path: 'scoutList/:id',
            component: MyScoutListComponent,
            beforeEnter (to, from, next) {
              store.dispatch('auth/accessAllowCheck')
              .then(value => {
                const id = to.params.id
                if (store.state.auth.user != null && value && store.state.auth.user.user_type == "U" && id == store.state.auth.user.id) {
                    next()
                  } else {
                    window.location.href = "/";
                  }
              });
            }
          }, 
          
          {
            path: 'profile/:id',
            component: profile,
            beforeEnter (to, from, next) {
              var value =  store.dispatch('auth/accessAllowCheck')
              .then(value => {
                const id = to.params.id
                if (store.state.auth.user != null && value && id == store.state.auth.user.id) {
                    console.log('mada')
                    next()
                  } else {
                    window.location.href = "/";
                  }
              });
            }
          },  
          {
          path: 'likes/:id',
          component: postsList,
          props: { initPage: "likes" },
          beforeEnter (to, from, next) {
            store.dispatch('auth/accessAllowCheck').then(value => {
              const id = to.params.id
              if (store.state.auth.user != null && value && id == store.state.auth.user.id) {
                  next()
                } else {
                  window.location.href = "/";

                }
            });
          }
        },  
        {
          path: 'applies/:id',
          component: postsList,
          props: { initPage: "applies" },
          beforeEnter (to, from, next) {
            store.dispatch('auth/accessAllowCheck').then(value => {
              const id = to.params.id
              if (store.state.auth.user != null && value && id == store.state.auth.user.id) {
                  next()
                } else {
                  window.location.href = "/";

                }
            });
          }
        },  
        {
          path: 'setting',
          component: Setting,
          children:[
            {
              path: 'country',
              component: AdminPlaceShowComponent,
              beforeEnter (to, from, next) {
                store.dispatch('auth/accessAllowCheck').then(value => {
                  console.log("accessAllowed")
                  console.log(value)
  
                  if (store.state.auth.user != null && value && store.getters['auth/check'] && store.state.auth.user.user_type == "A") {
                      next()
                  } else {
                    window.location.href = "/";

                  }
                });
              }

            },    
            {
              path: 'category',
              component: categoryShow,
              beforeEnter (to, from, next) {
                store.dispatch('auth/accessAllowCheck').then(value => {
                  console.log("accessAllowed")
                  console.log(value)
  
                  if (store.state.auth.user != null && value && store.getters['auth/check'] && store.state.auth.user.user_type == "A") {
                      next()
                  } else {
                    window.location.href = "/";

                  }
                });
              }
            },    
            {
              path: 'users',
              component: userShow,
              beforeEnter (to, from, next) {
                store.dispatch('auth/accessAllowCheck').then(value => {
                  console.log("accessAllowed")
                  console.log(value)
  
                  if (store.state.auth.user != null && value && store.getters['auth/check'] && store.state.auth.user.user_type == "A") {
                      next()
                  } else {
                      window.location.href = "/";

                    }
                });
              }
            },    
          ],
        },
    ],
    beforeEnter (to, from, next) {
      if(store.getters['auth/check']){
        store.dispatch('auth/prepareList')
      }
      next()
    }

  },
  // {
  //   path: '/setting',
  //   component: Setting,
  //   beforeEnter (to, from, next) {
  //       if (store.getters['auth/check'] && store.state.auth.user.user_type == "C") {
  //           next()
  //       } else {
  //           next(store.getters['auth/routePath'])
  //       }
  //   }
  // },
  {
    path: '/500',
    component: SystemError
  },
]

// VueRouterインスタンスを作成する
const router = new VueRouter({
    mode: 'history', 
    routes

})

// VueRouterインスタンスをエクスポートする
// app.jsでインポートするため
export default router