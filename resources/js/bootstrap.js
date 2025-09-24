/**
 * resources/js/bootstrap.js
 *
 * Lightweight app bootstrap for Inertia + Vue3 (Breeze).
 * Sets up axios, CSRF header and a tiny helpful console hint.
 */

import _ from 'lodash';
window._ = _;

import axios from 'axios';
window.axios = axios;

// XHR header
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Attach CSRF token (meta tag inserted by Laravel in your blade layout)
const tokenMeta = document.querySelector('meta[name="csrf-token"]');
if (tokenMeta) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = tokenMeta.getAttribute('content');
} else {
    // helpful hint if token is missing
    // this will not break the app — just logs to console
    // you usually get this token via: <meta name="csrf-token" content="{{ csrf_token() }}">
    // see: https://laravel.com/docs/csrf
    // eslint-disable-next-line no-console
    console.warn('CSRF token not found (meta[name="csrf-token"]). If you see 419 errors, add the token to your layout.');
}

/*
 * Optional: you can enable Inertia Progress here if you prefer,
 * but createInertiaApp({ progress: { ... } }) in app.js already configures it.
 *
 * import { InertiaProgress } from '@inertiajs/progress';
 * InertiaProgress.init({ color: '#4B5563' });
 */

/*
 * Any global helpers you want can be attached to window here.
 * Keep bootstrap.js minimal — heavy logic belongs in modules or controllers.
 */
