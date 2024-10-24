import {getCookie} from "./getCookie.js";

export function mainFetch(path, method, body, headers) {
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
    return fetch(`http://localhost/api/${path}`, options)
        .then(response => response.json());
}
