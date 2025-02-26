import {modalWindow} from "./modalWindow.js";
import {mainFetch} from "../mainFetch.js";
import {showAlert} from "../showAlert.js";
import {showModal} from "./showModal.js";

export function productImportModalWindow() {
    document.getElementById('importButton').addEventListener('click', function () {
        const styles = {header: 'bg-blue'}
        modalWindow(
            header,
            body,
            footer,
            styles
        );
        showModal('modalWindow');
        importData('import-form');
    });

    const header = `
            <h1 class="modal-title fs-5">Import products</h1>
            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
    `;

    const body = `
            <div>
                <a href="http://localhost/products/import/example-csv">
                <button type="button" class="btn btn-primary btn-sm mb-3" id="example-scv" title="Download example CSV file">
                    Download example file
                </button>
            </a>
            </div>
            <div class="mr-1" id="import-block">
                <form id="import-form">
                    <div class="input-group mb-2">
                      <input class="form-control" id="import-file" type="file" name="csv_file" accept=".csv">
                      <button type="submit" class="btn btn-primary" id="import-btn">
                        Import file
                      </button>
                    </div>
                </form>
            </div>
    `;

    const footer = `
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
    `;
}

function importData() {
    const form = document.getElementById('import-form');
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        mainFetch('import/products', 'POST', new FormData(e.target))
            .then(response => {
                removeInputError();

                if (response.status === 'Success') {
                    resetImportInputAfterSuccess(e.target);
                    showAccessAlert();
                } else if (response.status === 'Error' && response.data) {
                    showImportErrors(e.target, response.data)
                }
            })
            .catch(errors => {
                console.log(errors);
            });
    });
}

function removeInputError() {
    const inputErrorMessage = document.getElementById('import-errors');
    if (inputErrorMessage) {
        inputErrorMessage.remove();
    }
}

function showImportErrors(target, errorsData) {
    const title = createImportErrorsTitle();
    const errorsList = createImportErrorsList(errorsData);

    const errorsContainer = document.createElement('div');
    errorsContainer.id = 'import-errors';
    errorsContainer.appendChild(title);
    errorsContainer.appendChild(errorsList);

    target.insertAdjacentElement('beforeend', errorsContainer);
}

function createImportErrorsTitle() {
    const title = document.createElement('p');
    title.classList.add('m-1', 'text-danger');
    title.innerText = 'CSV file validations errors';

    return title;
}

function createImportErrorsList(errorsData) {
    const errorList = document.createElement('ul');
    errorList.classList.add('text-danger');

    errorsData.forEach(error => {
        const errorMessage = document.createElement('li');
        errorMessage.textContent = `${error}`;
        errorList.appendChild(errorMessage);
    });

    return errorList;
}

function resetImportInputAfterSuccess(element) {
    const input = element.querySelector('#import-file');
    input.value = '';
    input.nextElementSibling.innerHTML = 'Choose csv import file...';
}

function showAccessAlert() {
    const alert = `<div class="alert alert-success m-0" role="alert">
                          <i class="bi bi-check-circle mr-1"></i>
                          The import of file data has started successfully.
                        </div>`;
    showAlert(alert, 'content-header', true, 5000);
}
