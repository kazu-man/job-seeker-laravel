/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
import './bootstrap'
// require('./bootstrap');
import Vue from 'vue'


Vue.use(VModal);
Vue.use(VueGoogleMaps, {
    load: {
        key: process.env.MIX_APP_GOOGLE_MAP_KEY,
        libraries: 'places',
        region: 'JP',
        language: 'ja'
    }
})
window.Vue = require('vue');

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i);
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

Vue.component('place-show-component', require('./components/setting/PlaceShowComponent.vue').default);
Vue.component('place-show-component2', require('./components/common/PlaceShowComponent2.vue').default);
Vue.component('side-header-component', require('./components/common/SideHeaderComponent.vue').default);
Vue.component('select-box-component', require('./components/common/SelectBox.vue').default);
Vue.component('posts-component', require('./components/jobList/PostComponent.vue').default);
Vue.component('select-tag-component', require('./components/common/SelectTagComponent.vue').default);
Vue.component('tag-component', require('./components/common/TagComponent.vue').default);

Vue.component('spinner', require('vue-simple-spinner'));

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

// ルーティングの定義をインポートする
import router from './router'
// // ルートコンポーネントをインポートする
import App from './App.vue'

import VModal from 'vue-js-modal'
import store from './store'
import * as VueGoogleMaps from 'vue2-google-maps'   

const createApp = async () => {
    await store.dispatch('auth/currentUser')
    
    const app = new Vue({
        el: '#app',
        router, // ルーティングの定義を読み込む
        components: { App }, // ルートコンポーネントの使用を宣言する
        template: '<App />', // ルートコンポーネントを描画する
        store
    });

}

createApp()