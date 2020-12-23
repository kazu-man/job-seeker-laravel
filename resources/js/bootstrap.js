window._ = require('lodash');

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {
    window.Popper = require('popper.js').default;
    window.$ = window.jQuery = require('jquery');

    require('bootstrap');
} catch (e) {}

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */
import { getCookieValue } from './util'
import { OK,ORIGINAL_ERROR } from './util'
import store from './store' 

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

window.axios.interceptors.request.use(config => {
    // クッキーからトークンを取り出してヘッダーに添付する
    config.headers['X-XSRF-TOKEN'] = getCookieValue('XSRF-TOKEN')
  
    return config
  });
  
  window.axios.interceptors.response.use(
    response => response,
    error => {
      var status = error.response.status;
      if(status != OK){
        store.commit('auth/setApiStatus', false)
        store.commit('error/setCode', status, { root: true })
        var message = "";
        var data = error.response.data;
        var allErrors = data.errors;
        for(var error in allErrors){
            message += allErrors[error] + "\n";
        }
        if(status == ORIGINAL_ERROR){
          if(data.message != null && data.message != ""){
            message += data.message + "\n";
          }
        }
        if(message == ""){
          message = "エラーが発生しました"
        }
        store.dispatch('common/alertModalUp', {data:status, successMessage:message,close:false,reload:true});
    }
  		return Promise.reject(error)
    }
  )
/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     encrypted: true
// });
