/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import './bootstrap';
import { createApp } from 'vue'
import ApplicationForm from "./components/ApplicationForm.vue";
import Popup from "./components/Popup.vue";
import RegisterForm from './components/RegisterForm.vue'
import { MaskInput } from "vue-3-mask";
import ProfileForm from "./components/ProfileForm.vue";
import QuotesTable from "./components/Admin/QuotesTable.vue";
import QuoteEmailsTable from "./components/Admin/QuoteEmailsTable.vue";
import DatePicker from "vue-datepicker-next"
import 'vue-datepicker-next/index.css';
import LoginForm from "./components/LoginForm.vue";
import TransportCompanySettings from "./components/Admin/TransportCompanySettings.vue";
import TcSettings from "./components/Admin/TcSettings.vue";
import SupportApps from "./components/Admin/SupportApps.vue";
import SupportAppImport from "./components/Admin/SupportAppImport.vue";
import { DotLoader } from 'vue3-spinner';
import TariffRegionSettings from "./components/Admin/TariffRegionEdit.vue";
import TariffRegions from "./components/Admin/TariffRegions.vue";
import Multiselect from 'vue-multiselect';
import TariffCreate from "./components/Admin/TariffCreate.vue";
import TariffsGetFromServiceButton from "./components/Admin/TariffsGetFromServiceButton.vue";
import TariffPermissionRequestButton from "./components/Admin/TariffPermissionRequestButton.vue";
import TariffList from "./components/Admin/TariffList.vue";
import UserTariffPermissions from "./components/Admin/UserTariffPermissions.vue";
import TariffNameAliasForm from "./components/Admin/TariffNameAliasForm.vue";
import YandexZones from "./components/Admin/YandexZones.vue";
import Checkbox from "./components/Admin/Checkbox.vue";
import ShipmentWarehouseSettings from "./components/Admin/ShipmentWarehouseSettings.vue";
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
    const app = createApp({
        components: {
            'application-form': ApplicationForm,
            'popup': Popup,
            'date-picker': DatePicker,
            'register-form': RegisterForm,
            'mask-input': MaskInput,
            'profile-form': ProfileForm,
            'login-form': LoginForm
        }
    });

    app.mount('#app')
}

if (document.getElementById('admin-app')) {
    const adminApp = createApp({
        components: {
            'quotes-table': QuotesTable,
            'quote-emails-table': QuoteEmailsTable,
            'date-picker': DatePicker,
            'transport-company-settings': TransportCompanySettings,
            'tc-settings': TcSettings,
            'support-apps':SupportApps,
            'support-app-import': SupportAppImport,
            'spinner': DotLoader,
            'tariff-region-settings': TariffRegionSettings,
            'tariff-regions': TariffRegions,
            'multiselect': Multiselect,
            'tariff-create': TariffCreate,
            'tariff-get-from-service-button': TariffsGetFromServiceButton,
            'tariff-permission-request-button': TariffPermissionRequestButton,
            'tariff-list': TariffList,
            'user-tariff-permissions': UserTariffPermissions,
            'tariff-name-alias-form': TariffNameAliasForm,
            'yandex-zones': YandexZones,
            'checkbox': Checkbox,
            'shipment-warehouse-settings': ShipmentWarehouseSettings
        }
    })

    adminApp.mount('#admin-app')
}

function initVueCheckboxes() {
    document.querySelectorAll('.vue-checkbox:not([data-vue-mounted])').forEach(el => {
        const entry = JSON.parse(el.dataset.entry);
        const uri = JSON.parse(el.dataset.uri);
        const type = JSON.parse(el.dataset.type);
        createApp(Checkbox, { entry, uri, type }).mount(el);
        el.setAttribute('data-vue-mounted', 'true'); // метка, чтобы не инициализировать повторно
    });
}

// Инициализация при первой загрузке
initVueCheckboxes();

const table = document.querySelector('table.dataTable');
if (table) {
    $(table).on('draw.dt', () => {
        initVueCheckboxes();
    });
}
