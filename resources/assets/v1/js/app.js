
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
Vue.config.productionTip = true;
Vue.config.debug = true;
Vue.config.silent = !true; // disabled warning
Vue.config.devtools = true;

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

// Vue.component('example-component', require('./components/ExampleComponent.vue'));
// import NavigateMenu from './components/Navigate.vue';
import Casier from './../../../js/components/CasierComponent.vue';
import Product from './../../../js/components/ProductComponent.vue';
import Toggler from './../../../js/components/TogglerComponent.vue';
import Timer from './../../../js/components/TimerComponent.vue';
import Transactions from './../../../js/components/TransactionComponent.vue';
import Vuex from 'vuex';

// import VueSweetalert2 from 'vue-sweetalert2';
// import swal from 'sweetalert';

// Vue.use({
//     install (Vue){
//         Vue.swal = swal
//         Vue.prototype.$swal = swal
//     }
// })
// 
Vue.prototype.$Event = new Vue();

Vue.filter('capitalize', function (value) {
    if (!value) return ''
    value = value.toString()
    return value.charAt(0).toUpperCase() + value.slice(1)
});

Vue.filter('toRupiah', function (value) {
    let data, reserve;
    if (!value) return 'Rp0';
    value = value.toString();
    data = parseInt(value, 10).toString().split('').reverse().join('');
    reserve = '';
    for(let i = 0; i < data.length; i++){
        reserve += data[i];
        if((i + 1) % 3 === 0 && i !== (data.length - 1)){
            reserve += '.';
        }
    }
    return `Rp${reserve.split('').reverse().join('')}`;
});

const app = new Vue({
    el: '#app',
    // render: h => h(Example),
    components:{
        Product, Toggler, Timer, Transactions
    },
    store: new Vuex.Store({
        state:{
            navigate: [],
        },
        getters:{
            navigate: state => state.navigate,
        },
        mutations:{
            UPDATE_NAVIGATE(state, payload){
                state.navigate = payload;
            },
        },
        actions:{
            GET_NAVIGATE({ commit }){
                commit('UPDATE_NAVIGATE');
            }
        }
    }),
    data(){
        return{
            overlay: false,
        };
    },
});
