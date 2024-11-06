import {mainFetch} from "./mainFetch.js";
import {showAlert} from "./showAlert.js"
export function exportData(exportBtn, fetchUrl, successAlert) {
    const handler = function () {
        exportBtn.setAttribute('disabled', '');
        mainFetch(fetchUrl, 'GET')
            .then((response) => {
                if (response.status === "Success") {
                    exportBtn.removeAttribute('disabled');
                    showAlert(successAlert, 'content-wrapper', true, 5000);
                }
            });
        exportBtn.removeEventListener('click', handler);
    };
    exportBtn.addEventListener('click', handler);
}
