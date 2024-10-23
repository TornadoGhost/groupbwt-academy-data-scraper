export function mainFetch(path = '/', method = get, body = '', headers = {}) {
    const token = localStorage.getItem('accessToken');
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
};
