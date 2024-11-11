import {mainFetch} from "../mainFetch.js";

export async function getRetailers() {
    let data;
    await mainFetch('retailers', 'GET')
        .then(response => {
            if (response?.status === 'Success') {
                data = response.data;
            }
        })
    return data;
}