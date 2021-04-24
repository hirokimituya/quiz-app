require('./bootstrap');

// Import modules...
import Vue from 'vue';
import { App as InertiaApp, plugin as InertiaPlugin } from '@inertiajs/inertia-vue';
import vuetify from '@/vuetify';
import 'vuetify/dist/vuetify.min.css';
import { InertiaProgress } from '@inertiajs/progress';

Vue.mixin({ methods: { route } });
Vue.use(InertiaPlugin);

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
