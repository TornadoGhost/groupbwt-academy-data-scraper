export function getCookie(name) {
    const cookie = cookiesArray.find(cookie => cookie.startsWith(`${name}=`));
    return cookie ? cookie.split('=')[1] : null;
}
const allCookies = document.cookie;
const cookiesArray = allCookies.split('; ');
