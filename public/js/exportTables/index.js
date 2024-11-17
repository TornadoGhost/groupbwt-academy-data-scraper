import {mainFetch} from "../mainFetch.js";

async function getTableData() {
    let data;
    await mainFetch(`export-tables`, 'GET')
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
            "order": [[1, 'desc']],
            "columns": [
                {"data": "file_name"},
                {"data": "created_at"},
                {
                    "data": null,
                    "render": function () {
                        return `
                            <button id="export-download" class="btn btn-xs btn-default text-danger mx-1 shadow" title="Download">
                                <i class="fa fa-lg fa-fw fa-download text-success"></i>
                            </button>
                            <button id="export-delete" class="btn btn-xs btn-default text-danger mx-1 shadow" title="Delete">
                                <i class="fa fa-lg fa-fw fa-trash"></i>
                            </button>
                        `;
                    },
                    "orderable": false,
                    "searchable": false
                }
            ],
        });

        const downloadButtons = document.querySelectorAll('button[id=export-download]');
        downloadButtons.forEach(elem => {
            elem.addEventListener('click', function (event) {
                const filePath = getRowData(event).path;
                const fileName = getRowData(event).file_name;
                window.location.href = `export-tables/download?file_path=${filePath}&file_name=${fileName}`
            });
        });

        document.querySelector('#table2').addEventListener('click', function(event) {
            if ((event.target && event.target.id === 'export-delete') || event.target.closest('button[id=export-delete]')) {
                document.getElementById('modal-delete-btn').click();
                modalRemoveExportAccept(event.target.closest('tr'));
            }
        });

        function modalRemoveExportAccept(element) {
            const handle = function (event) {
                if (event.target === document.getElementById('delete-btn')) {
                    const id = table.row(element).data().id;
                    mainFetch(`export-tables/${id}`, 'delete')
                        .then(response => {
                            if (response?.status === 'Error') {
                                setErrorModalWindow(response.message);
                            } else {
                                table.row(element).remove().draw();
                            }
                        })
                    document.removeEventListener('click', handle)
                }
            };
            document.addEventListener('click', handle)
        }

        function getRowData(event) {
            return table.row(event.target.closest('tr')).data();
        }
    }

    if ($.fn.DataTable.isDataTable('#table2')) {
        $('#table2').DataTable().clear().destroy();
    }

    initTable();
});

function removeInputError() {
    const inputErrorMessage = document.getElementById('input-file-error');
    if (!inputErrorMessage.classList.contains('d-none')) {
        inputErrorMessage.classList.add('d-none');
    }
}

function getExportId(button) {
    return button.closest('tr[class=odd]').firstElementChild.textContent;
}


function setErrorModalWindow(body) {
    const errorModal = document.getElementById('errors-modal');
    const modalBody = errorModal.getElementsByClassName('modal-body')[0];
    modalBody.innerHTML = '';
    modalBody.appendChild(body);
    document.getElementById('error-modal-button').click();
}
