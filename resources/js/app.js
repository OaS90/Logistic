/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue').default;
// import vuetify from './vuetify';

// import ExampleComponent from "./components/ExampleComponent";
import ApplicationForm from "./components/ApplicationForm";
import Popup from "./components/Popup";
import RegisterForm from './components/RegisterForm'
import DatePicker from "vue2-datepicker";
import MaskedInput from "vue-masked-input";
import ProfileForm from "./components/ProfileForm";
import QuotesTable from "./components/Admin/QuotesTable";
import QuoteEmailsTable from "./components/Admin/QuoteEmailsTable";
import LoginForm from "./components/LoginForm";
import TransportCompanySettings from "./components/Admin/TransportCompanySettings";
import TcSettings from "./components/Admin/TcSettings";
/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

// Vue.component('example-component', require('./components/ExampleComponent.vue'));
// Vue.component('popup', require('./components/Popup.vue'));
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

if (document.getElementById('app')) {
    const app = new Vue({
        el: '#app',
        components: {
            ApplicationForm,
            Popup,
            DatePicker,
            RegisterForm,
            MaskedInput,
            ProfileForm,
            LoginForm
        }
    });
}

if (document.getElementById('admin-app')) {
    const adminApp = new Vue({
        el: '#admin-app',
        components: {
            QuotesTable,
            DatePicker,
            QuoteEmailsTable,
            TransportCompanySettings,
            TcSettings
        }
    })
}
