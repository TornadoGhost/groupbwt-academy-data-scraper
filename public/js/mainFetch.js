export function mainFetch(path = '/', method = 'get', body = '', headers = {}) {
    const token = getCookie('laravel_token');
    const options = {
        method: method,
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`,
            ...headers,
        },
    };

    if (body && (method === 'POST' || method === 'PUT' || method === 'PATCH')) {
        options.body = JSON.stringify(body);
    }
    return fetch(`http://localhost/api/${path}`, options)
        .then(response => response.json());
}

const allCookies = document.cookie;
const cookiesArray = allCookies.split('; ');
function getCookie(name) {
    const cookie = cookiesArray.find(cookie => cookie.startsWith(`${name}=`));
    return cookie ? cookie.split('=')[1] : null; // Повертає значення cookie або null, якщо не знайдено
}
