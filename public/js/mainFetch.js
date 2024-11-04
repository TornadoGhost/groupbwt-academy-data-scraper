import {getCookie} from "./getCookie.js";

export function mainFetch(path, method, body, headers, signal, jsonResponse = true) {
    const token = getCookie('laravel_token');
    const options = {
        method: method,
        headers: {
            'Accept': 'application/json',
            'Authorization': `Bearer ${token}`,
            ...headers,
        },
    };

    if (body && (method === 'POST' || method === 'PUT' || method === 'PATCH')) {
        options.body = body;
    }

    if (method === 'PATCH') {
        options.headers['Content-Type'] = 'application/x-www-form-urlencoded';
    }

    if (signal) {
        options.signal = signal;
    }

    if (jsonResponse) {
        return fetch(`http://localhost/api/${path}`, options).then(response => response.json());
    } else {
        return fetch(`http://localhost/api/${path}`, options);
    }
}
