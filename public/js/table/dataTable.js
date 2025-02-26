import {mainFetch} from "../mainFetch.js";

// 'orderStrict' attribute can change the behavior of columns order, if 'true' value, it will order only a one column at the same time, with false multiple columns can be ordered at the same time
export function dataTable(table, tableRow, actions, orderStrict = true) {
    let path = 'products?';
    let perPage = null;
    fetchData(path, table, tableRow, actions);

    // TODO: move events to the functions
    const tableColumnsOrdersButtons = [...document.querySelectorAll('[data-order]')];

    if (orderStrict) {
        tableColumnsOrdersButtons.forEach((button) => {
            button.addEventListener('click', function () {
                if (button.classList.contains('text-secondary')) {
                    button.classList.add('text-white');
                    button.classList.remove('text-secondary');

                    tableColumnsOrdersButtons.forEach((b) => {
                        if (button !== b && b.classList.contains('text-white')) {
                            b.classList.remove('text-white');
                            b.classList.add('text-secondary');
                            path = removeQueryParameterFromPath(
                                '&sort_' + b.dataset.column,
                                b.dataset.order,
                                path
                            );
                        }
                    });

                    path = addQueryParameterToPath('&sort_' + button.dataset.column, button.dataset.order, path)
                } else if (button.classList.contains('text-white')) {
                    button.classList.remove('text-white');
                    button.classList.add('text-secondary');
                    path = removeQueryParameterFromPath(
                        '&sort_' + button.dataset.column,
                        button.dataset.order,
                        path
                    );
                }

                fetchData(path, table, tableRow, actions);
            });
        });
    } else {
        tableColumnsOrdersButtons.forEach((button) => {
            button.addEventListener('click', function (event) {
                if (button.classList.contains('text-secondary')) {
                    path = setActive(button, path);
                } else if (button.classList.contains('text-white')) {
                    path = removeActive(button, path);
                }

                fetchData(path, table, tableRow, actions);
            });
        });
    }

    document.getElementById('paginationPerPage').addEventListener('change', function (event) {
        if (perPage) {
            path = removeQueryParameterFromPath('&per_page', perPage, path)
        }

        perPage = event.target.value;
        path = addQueryParameterToPath('&per_page', perPage, path)
        fetchData(path, table, tableRow, actions);
    });

    document.getElementById('searchInTable').addEventListener('input', function (event) {
        fetchData(addQueryParameterToPath('search', event.target.value, path), table, tableRow, actions);
    });

    document.getElementById('tablePagination').addEventListener('click', function (event) {
        if (event.target.classList.contains('page-link')) {
            const pageItem = event.target.closest('.page-item');
            const getPaginationButtonAttribute = event.target.getAttribute('data-pagination-button');

            if (getPaginationButtonAttribute) {
                const pagination = document.getElementById('tablePagination');
                const activePageNumber = pagination
                    .querySelector('li.active')
                    .closest('.page-item')
                    .textContent
                    .trim()
                ;

                if (getPaginationButtonAttribute === 'previous') {
                    fetchData(path + `&page=${+activePageNumber - 1}`, table, tableRow, actions);
                }

                if (getPaginationButtonAttribute === 'next') {
                    fetchData(path + `&page=${+activePageNumber + 1}`, table, tableRow, actions);
                }
            } else if (!pageItem.classList.contains('active') && !pageItem.classList.contains('disabled')) {
                fetchData(path + `&page=${pageItem.textContent.trim()}`, table, tableRow, actions);
            }
        }
    });
}

function fetchData(path, table, tableRow, actions) {
    // TODO: add actions if data not accessed
    mainFetch(path, 'GET').then((data) => {
        const paginationPerPage = document.getElementById('paginationPerPage');
        const tableBody = table.querySelector('tbody');
        const pagination = document.getElementById('tablePagination');
        const paginationInformation = document.getElementById('paginationInformation');

        const tableElements = [tableBody, pagination, paginationInformation, paginationPerPage];
        tableElements.forEach((element) => {
            emptyInnerHtmlIfItsNot(element);
        });

        const meta = data.meta;
        fillTableData(data.data, tableBody, tableRow, actions.html);
        fillPerPageElement(paginationPerPage, meta);
        fillPaginationElement(pagination, meta);
        fillPaginationInformationElement(paginationInformation, meta);
        actions.callback();
    });
}


function setActive(element, path) {
    const leftSibling = element.previousElementSibling;
    const rightSibling = element.nextElementSibling;

    if (leftSibling) {
        path = removeActive(leftSibling, path);
    } else if (rightSibling) {
        path = removeActive(rightSibling, path);
    }
    element.classList.remove('text-secondary');
    element.classList.add('text-white');
    path = addQueryParameterToPath('&sort_' + element.dataset.column, element.dataset.order, path);

    return path;
}

function removeActive(element, path) {
    element.classList.remove('text-white');
    element.classList.add('text-secondary');
    path = removeQueryParameterFromPath('&sort_' + element.dataset.column, element.dataset.order, path)

    return path;
}

function addQueryParameterToPath(parameterKey, parameterValue, path) {
    return path + `${parameterKey}=${parameterValue}`;
}

function removeQueryParameterFromPath(parameterKey, parameterValue, path) {
    if (path.includes(`${parameterKey}=${parameterValue}`)) {
        path = path.replace(`${parameterKey}=${parameterValue}`, '');
    }

    return path;
}

function emptyInnerHtmlIfItsNot(element) {
    if (element && element.hasChildNodes()) {
        element.innerHTML = '';
    }
}

function fillTableData(tableData, tableBody, tableRow, actionColumn) {
    tableData.forEach((element) => {
        const tr = document.createElement('tr');
        tr.dataset.id = element['id'];

        tableRow.forEach((column) => {
            if (element[column]) {
                const td = document.createElement('td');
                td.textContent = element[column];
                tr.appendChild(td);
            }
        });

        tr.insertAdjacentHTML('beforeend', `<td>${actionColumn}</td>`);
        tableBody.appendChild(tr);
    })
}

function fillPerPageElement(element, meta) {
    if (meta.total < 10) {
        element.addClass('disabled');
        element.insertAdjacentHTML('afterbegin', `
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="30">30</option>
            `);
    } else if (meta.total < 20) {
        element.insertAdjacentHTML('afterbegin', `
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="30" disabled>30</option>
            `);
    } else {
        element.insertAdjacentHTML('afterbegin', `
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="30">30</option>
            `);
    }

    [...element.options].forEach((option) => {
        if (Number(option.value) === Number(meta.per_page)) {
            option.setAttribute('selected', 'selected')
        }
    });
}

function fillPaginationElement(element, meta) {
    meta.links.forEach((link, idx) => {
        if (idx === 0 || idx === meta.links.length - 1) {
            return null;
        }

        if (!link.active && link.url) {
            element.insertAdjacentHTML('beforeend', `
                    <li class="page-item">
                        <button class="page-link">${link.label}</button>
                    </li>
                `);
        } else if (!link.url) {
            element.insertAdjacentHTML('beforeend', `
                    <li class="page-item disabled">
                        <button class="page-link">${link.label}</button>
                    </li>
                `);
        } else {
            element.insertAdjacentHTML('beforeend', `
                    <li class="page-item active">
                        <button class="page-link">${link.label}</button>
                    </li>
                `);
        }
    });

    if (meta.current_page === meta.from) {
        element.insertAdjacentHTML('afterbegin', `
                <li class="page-item disabled">
                    <button class="page-link" data-pagination-button="previous">Previous</button>
                </li>
            `);
    } else {
        element.insertAdjacentHTML('afterbegin', `
                <li class="page-item">
                    <button class="page-link" data-pagination-button="previous">Previous</button>
                </li>
            `);
    }

    if (meta.current_page === meta.last_page) {
        element.insertAdjacentHTML('beforeend', `
                <li class="page-item disabled">
                    <button class="page-link" data-pagination-button="next">Next</button>
                </li>
            `);
    } else {
        element.insertAdjacentHTML('beforeend', `
                <li class="page-item">
                    <button class="page-link" data-pagination-button="next">Next</button>
                </li>
            `);
    }
}

function fillPaginationInformationElement(element, meta) {
    element.insertAdjacentHTML('afterbegin', `
            Showing ${meta.from} to ${meta.to} of ${meta.total} entries
        `);
}
