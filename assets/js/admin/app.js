window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

Vue.component('ProductPropertiesComponent', require('../components/ProductPropertiesComponent.vue').default);
Vue.component('PageFooterAddressesComponent', require('../components/PageFooterAddressesComponent.vue').default);
Vue.component('EventTagsComponent', require('../components/EventTagsComponent.vue').default);
Vue.component('ProductRestCatMultiselectComponent', require('../components/ProductRestCatMultiselectComponent.vue').default);

let root = document.getElementById('app');

if (root) {
    new Vue({
        el: root
    });
}
