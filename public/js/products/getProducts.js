import {mainFetch} from "../mainFetch.js";

export async function getProduct(id) {
    let data;
    await mainFetch(`products/${id}`, 'GET')
        .then(response => {
            if (response?.status === 'Success') {
                data = response.data;
            }
        })
    return data;
}