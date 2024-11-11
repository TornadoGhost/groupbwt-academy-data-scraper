import {mainFetch} from "../mainFetch.js";
import {showAlert} from "../showAlert.js";
import {exportData} from "../exportData.js";

async function getTableData() {
    let data;
    await mainFetch(`products`, 'GET')
        .then((response) => {
            if (response.data) {
                data = response.data;
            } else {
                console.log(response)
            }
        });
    return data;
}

$(document).ready(function () {
    async function initTable() {
        const table = new DataTable('#table2', {
            "data": await getTableData(),
            "layout": {
                topStart: 'buttons'
            },
            "order": [[0, 'desc']],
            "columns": [
                {"data": "id"},
                {"data": "title"},
                {"data": "manufacturer_part_number"},
                {"data": "pack_size"},
                {"data": "created_at"},
                {
                    "data": null,
                    "render": function () {
                        return `
                            <button id="product-show" class="btn btn-xs btn-default text-teal mx-1 shadow" title="Details">
                               <i class="fa fa-lg fa-fw fa-eye"></i>
                            </button>
                            <button id="product-edit" class="btn btn-xs btn-default text-primary mx-1 shadow" title="Edit">
                                <i class="fa fa-lg fa-fw fa-pen"></i>
                            </button>
                            <button id="product-delete" class="btn btn-xs btn-default text-danger mx-1 shadow" title="Delete">
                                <i class="fa fa-lg fa-fw fa-trash"></i>
                            </button>
                        `;
                    },
                    "orderable": false,
                    "searchable": false
                }
            ],
        });

        const showButtons = document.querySelectorAll('button[id=product-show]');
        showButtons.forEach(elem => {
            elem.addEventListener('click', function (event) {
                const id = getRowData(event.target.closest('tr')).id;
                window.location.href = `products/${id}`;
            });
        })

        const removeButtons = document.querySelectorAll('button[id=product-delete]');
        removeButtons.forEach(elem => {
            const handler = function (event) {
                document.getElementById('modal-delete-btn').click();
                modalRemoveProductAccept(event.target.closest('tr[class=odd]'));
                elem.removeEventListener('click', handler);
            };
            elem.addEventListener('click', handler);
        });



        const editButtons = document.querySelectorAll('button[id=product-edit]');
        editButtons.forEach(elem => {
            elem.addEventListener('click', function (event) {
                const id = getRowData(event.target.closest('tr')).id;
                window.location.href = `products/${id}/edit`;
            });
        })

        function modalRemoveProductAccept(element) {
            const handler = function (event) {
                if (event.target === document.getElementById('delete-btn')) {
                    const id = getIdFromRow(element);
                    mainFetch(`products/${id}`, 'delete')
                        .then(response => {
                            if (response?.status === 'Error') {
                                setModalWindow('Error', response.message);
                            } else {
                                table.row(element).remove().draw();
                            }
                            document.removeEventListener('click', handler);
                        });
                }
            };
            document.addEventListener('click', handler);
        }

        function getIdFromRow(element) {
            return getRowData(element).id;
        }

        function getRowData(element) {
            return table.row(element).data();
        }
    }

    if ($.fn.DataTable.isDataTable('#table2')) {
        $('#table2').DataTable().clear().destroy();
    }

    initTable();

    function importData(formId) {
        const btn = document.getElementById(formId);
        btn.addEventListener('submit', function (e) {
            e.preventDefault();
            const successAlert =
                `<x-adminlte-alert id="success-alert" class="position-absolute top-0 end-0 m-3 bg-green" style="right: 0;" icon="fa fa-lg fa-thumbs-up" title="Done" dismissable>
                                        Import started! Waiting please.
                                    </x-adminlte-alert>`;
            showAlert(successAlert, 'content-wrapper', true, 5000);

            const formData = new FormData(e.target);
            mainFetch('import/products', 'POST', formData)
                .then(response => {
                    if (response.status === 'Success') {
                        removeInputError();
                        const input = e.target.querySelector('#import-file');
                        input.value = '';
                        input.nextElementSibling.innerHTML = 'Choose csv import file...';
                    } else if (response.status === 'Error' && response.data) {
                        removeInputError();

                        const block = document.createElement('div');
                        response.data.forEach(error => {
                            const errorMessage = document.createElement('p');
                            errorMessage.textContent = `${error}`;
                            block.appendChild(errorMessage);
                        });
                        const title = 'CSV validation errors';

                        setModalWindow(title, block);
                    } else if (response.errors) {
                        const inputErrorMessage = document.getElementById('input-file-error');
                        inputErrorMessage.previousElementSibling.classList.add('mb-0');
                        inputErrorMessage.classList.remove('d-none');
                        inputErrorMessage.innerHTML = response.errors['csv_file'];
                    }
                })
                .catch(errors => {
                    console.log(errors);
                })
        });
    }

    importData('import-form');
});

function removeInputError() {
    const inputErrorMessage = document.getElementById('input-file-error');
    if (!inputErrorMessage.classList.contains('d-none')) {
        inputErrorMessage.classList.add('d-none');
    }
}

function getProductId(button) {
    return button.closest('tr[class=odd]').firstElementChild.textContent;
}

function setModalWindow(title, body, theme = 'red') {
    const modal = document.getElementById('error-modal');
    const modalHeader = modal.getElementsByClassName('modal-header')[0];
    const modalTitle = modal.getElementsByClassName('modal-title')[0];
    const modalBody = modal.getElementsByClassName('modal-body')[0];
    const modalFooter = modal.getElementsByClassName('modal-footer')[0];

    modalTitle.innerHTML = title;
    modalBody.innerHTML = '';
    modalBody.appendChild(body);

    modalHeader.classList.add(`bg-${theme}`);
    modalFooter.querySelector('button.btn.btn-default').classList.add(`bg-red`);

    document.getElementById('error-modal-button').click();
}

function exportScrapedDataRetailer() {
    const exportBtn = document.getElementById('export-btn');
    exportBtn.addEventListener('click', function() {
        const successAlert = `<x-adminlte-alert id="success-alert" class="position-absolute top-0 end-0 m-3 bg-green" style="right: 0;" icon="fa fa-lg fa-thumbs-up" title="Started" dismissable>
                                        Export started! Wait for a notification.
                                    </x-adminlte-alert>`;
        exportData('export/products', successAlert);
    });
}exportScrapedDataRetailer();