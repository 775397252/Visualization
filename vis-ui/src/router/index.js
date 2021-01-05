import Vue from 'vue';
import Router from 'vue-router';

Vue.use(Router);

export default new Router({
    routes: [
        {
            path: '/',
            redirect: '/home'
        },
        {
            path: '/',
            component: () => import(/* webpackChunkName: "home" */ '../views/common/Home.vue'),
            meta: { title: '首页文件' },
            children: [
                {
                    path: '/home',
                    component: () => import(/* webpackChunkName: "home" */ '../views/page/home/index.vue'),
                    meta: { title: '系统首页' }
                }
            ]
        }
    ]
});
