import Vue from "vue";
import Vuex from "vuex";

import auth from "./auth";
import error from "./error";
import common from "./common";

Vue.use(Vuex);

const store = new Vuex.Store({
    modules: {
        auth,
        error,
        common
    }
});

export default store;
