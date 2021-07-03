require('./bootstrap');

// Import modules...
import Vue from 'vue';
import { App as InertiaApp, plugin as InertiaPlugin } from '@inertiajs/inertia-vue';
import vuetify from '@/vuetify';
import 'vuetify/dist/vuetify.min.css';
import { InertiaProgress } from '@inertiajs/progress';
import PortalVue from 'portal-vue';

Vue.mixin({ methods: { route } });
Vue.use(InertiaPlugin);
Vue.use(PortalVue);

// ブラウザバックを検知してコンポーネントで「this.$browserBackFlg」で使用できるようにする。
Vue.prototype.$browserBackFlg = false;
history.replaceState(null, document.getElementsByTagName('title')[0].innerHTML, null);
window.addEventListener('popstate', function(e) {
    Vue.prototype.$browserBackFlg  = true;

    window.setTimeout(() => {
        Vue.prototype.$browserBackFlg  = false;
    }, 500);
});

const el = document.getElementById('app');

new Vue({
    vuetify,
    render: (h) =>
        h(InertiaApp, {
            props: {
                initialPage: JSON.parse(app.dataset.page),
                resolveComponent: (name) => require(`./Pages/${name}`).default,
            },
        }),
}).$mount(el);

InertiaProgress.init({ color: '#4B5563' });
