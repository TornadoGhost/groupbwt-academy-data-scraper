import {mainFetch} from "./mainFetch.js";
import {showAlert} from "./showAlert.js"

export function exportData(fetchUrl, successAlert) {
        mainFetch(fetchUrl, 'GET')
            .then((response) => {
                if (response.status === "Success") {
                    showAlert(successAlert, 'content-wrapper', true, 5000);
                }
            });
}
