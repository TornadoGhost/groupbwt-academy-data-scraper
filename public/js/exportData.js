import {mainFetch} from "./mainFetch.js";
import {showAlert} from "./showAlert.js"

export function exportData(fetchUrl, successAlert) {
    showAlert(successAlert, 'content-wrapper', true, 5000);
    mainFetch(fetchUrl, 'GET')
}
