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
            component: top
          },
          {
            path: 'top',
            component: top
          },    
          {
            path: 'password/reset/:token',
            component: passwordReset
            },          
          {
            path: 'country/:country',
            component: top,
            props: { initPage: "country" },
  
          },
          {
            path: 'category/:category',
            component: top,
            props: { initPage: "category" },
  
          },

        {
            path: 'postJob/:id',
            component: jobsRegisterComponent,
            async beforeEnter (to, from, next) {
              await store.dispatch('auth/accessAllowCheck').then(value => {
                const id = to.params.id
                if (value && id == store.state.auth.user.company_id) {
                    next()
                  } else {
                    window.location.href = store.getters['auth/routePath'];
                  }
              });
            }
          },  
          {
            path: 'posts/:id',
            component: postsList,
            props: { pageType: "posts",initPage: "posts" },
            async beforeEnter (to, from, next) {
              await store.dispatch('auth/accessAllowCheck').then(value => {
                const id = to.params.id
                if (value && id == store.state.auth.user.company_id) {
                    next()
                  } else {
                    window.location.href = store.getters['auth/routePath'];
                  }
              });
            }
          },  

          {
            path: 'appliesList/:id',
            component: applyRecordTable,
            async beforeEnter (to, from, next) {
              await store.dispatch('auth/accessAllowCheck').then(value => {
                const id = to.params.id
                if (value && id == store.state.auth.user.company_id) {
                    next()
                  } else {
                    window.location.href = store.getters['auth/routePath'];
                  }
              });
            }
          },  
          
          {
            path: 'profile/:id',
            component: profile,
            async beforeEnter (to, from, next) {
              await store.dispatch('auth/accessAllowCheck').then(value => {
                const id = to.params.id
                if (value && id == store.state.auth.user.id) {
                    next()
                  } else {
                    window.location.href = store.getters['auth/routePath'];
                  }
              });
            }
          },  
          {
          path: 'likes/:id',
          component: postsList,
          props: { initPage: "likes" },
          async beforeEnter (to, from, next) {
            await store.dispatch('auth/accessAllowCheck').then(value => {
              const id = to.params.id
              if (value && id == store.state.auth.user.id) {
                  next()
                } else {
                  window.location.href = store.getters['auth/routePath'];
                }
            });
          }
        },  
        {
          path: 'applies/:id',
          component: postsList,
          props: { initPage: "applies" },
          async beforeEnter (to, from, next) {
            await store.dispatch('auth/accessAllowCheck').then(value => {
              const id = to.params.id
              if (value && id == store.state.auth.user.id) {
                  next()
                } else {
                  window.location.href = store.getters['auth/routePath'];
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
              component: top,
              async beforeEnter (to, from, next) {
                var that = this;
                await store.dispatch('auth/accessAllowCheck').then(value => {
                  console.log("accessAllowed")
                  console.log(value)
  
                  if (value && store.getters['auth/check'] && store.state.auth.user.user_type == "A") {
                      next()
                  } else {
                    window.location.href = store.getters['auth/routePath'];
                  }
                });
              }

            },    
            {
              path: 'category',
              component: top,
              async beforeEnter (to, from, next) {
                await store.dispatch('auth/accessAllowCheck').then(value => {
                  console.log("accessAllowed")
                  console.log(value)
  
                  if (value && store.getters['auth/check'] && store.state.auth.user.user_type == "A") {
                      next()
                  } else {
                    window.location.href = store.getters['auth/routePath'];
                  }
                });
              }
            },    
            {
              path: 'users',
              component: top,
              async beforeEnter (to, from, next) {
                await store.dispatch('auth/accessAllowCheck').then(value => {
                  console.log("accessAllowed")
                  console.log(value)
  
                  if (value && store.getters['auth/check'] && store.state.auth.user.user_type == "A") {
                      next()
                  } else {
                      window.location.href = store.getters['auth/routePath'];
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