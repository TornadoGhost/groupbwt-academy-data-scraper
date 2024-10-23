export function mainFetch(path = '/', method = get, body = '', headers = {}) {
    const token = getCookie('laravel_token');
    console.log(token)
    return fetch(`http://localhost/api/${path}`, {
        method: method,
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`,
            ...headers,
        },
        body: body
    }).then(response => response.json());
}

const allCookies = document.cookie;
const cookiesArray = allCookies.split('; ');
function getCookie(name) {
    const cookie = cookiesArray.find(cookie => cookie.startsWith(`${name}=`));
    return cookie ? cookie.split('=')[1] : null; // Повертає значення cookie або null, якщо не знайдено
}
