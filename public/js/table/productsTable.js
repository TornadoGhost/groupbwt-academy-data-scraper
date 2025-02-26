import {dataTable} from "./dataTable.js";

export function productsTable() {
    const tableRow = ['id', 'title', 'manufacturer_part_number', 'pack_size', 'created_at']
    const actions = {
        'html': `<button class="btn btn-primary btn-sm" data-action="edit">Edit</button>
                <button class="btn btn-danger btn-sm" data-action="delete">Delete</button>`,
        'callback': actionsEvents
    };
    dataTable(document.getElementById('table2'), tableRow, actions);
}

function actionsEvents() {
    const table = document.getElementById('table2');
    const editButtons = table.querySelectorAll('[data-action="edit"]');
    [...editButtons].forEach((button) => {
        button.addEventListener('click', function() {
            const elementId = button.closest('tr').getAttribute('data-id');
            window.location.href = `products/${elementId}/edit`;
        })
    });
}
