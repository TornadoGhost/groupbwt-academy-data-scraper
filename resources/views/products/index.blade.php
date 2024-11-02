@extends('layouts.app')

{{-- Customize layout sections --}}
@section('adminlte_css_pre')
@stop

@section('subtitle', 'Products list')
@section('content_header_title', 'Home')
@section('content_header_subtitle', 'Products')
@section('content_header_subtitle_subtitle', 'All')

{{-- Content body: main page content --}}

@section('content_body')
    <div class="btn-group">
        <a href="{{ route('products.create') }}">
            <x-adminlte-button class="mb-2 mr-1" label="Create product" theme="primary"/>
        </a>
        <div class="mr-1" id="import-block">
            <form id="import-form">
                <x-adminlte-input-file id="import-file" name="csv_file" accept=".csv"
                                       placeholder="Choose csv file..." igroup-size="md" legend="Choose">
                    <x-slot name="appendSlot">
                        <x-adminlte-button type="submit" id="import-btn" theme="primary" label="Import"/>
                    </x-slot>
                    <x-slot name="prependSlot">
                        <div class="input-group-text text-primary">
                            <i class="fas fa-file-upload"></i>
                        </div>
                    </x-slot>
                </x-adminlte-input-file>
                <p class="d-none text-danger m-0" id="input-file-error"></p>
            </form>
        </div>
        <div>
            <x-adminlte-button id="export-btn" label="Export products" theme="primary"/>
        </div>
    </div>
    @php
        $heads = [
            'id',
            'Title',
            'Manufacturer part number',
            'Pack size',
            'Created',
            ['label' => 'Actions', 'no-export' => true, 'width' => 5],
        ];
        $config['dom'] = '<"row" <"col-sm-7" B> <"col-sm-5 d-flex justify-content-end" i> >
                  <"row" <"col-12" tr> >
                  <"row" <"col-sm-12 d-flex justify-content-start" f> >';
        $config['paging'] = false;
        $config["lengthMenu"] = [ 10, 50, 100, 500];
    @endphp
    <x-adminlte-datatable id="table2" :heads="$heads" head-theme="dark" :config="$config"
                          striped bordered compressed beautify with-buttons hoverable/>
    <x-adminlte-modal id="modalMin" title="Warning" theme="red">
        <p>Are you sure, you want to delete?</p>
        <x-slot name="footerSlot">
            <x-adminlte-button id="close-btn" class="mr-auto" theme="danger" label="Close" data-dismiss="modal"/>
            <x-adminlte-button id="delete-btn" theme="success" label="Delete" data-dismiss="modal"/>
        </x-slot>
    </x-adminlte-modal>
    <x-adminlte-button class="d-none" id="modal-delete-btn" label="Delete Product" data-toggle="modal"
                       data-target="#modalMin"/>
    <x-adminlte-modal id="errors-modal" title="Error" theme="red">
        Error
    </x-adminlte-modal>
    <x-adminlte-button class="d-none" id="error-modal-button" label="Error" data-toggle="modal"
                       data-target="#errors-modal"/>
@stop
{{-- Push extra CSS --}}

@push('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@endpush

{{-- Push extra scripts --}}

@push('js')
    <script type="module">
        import {mainFetch} from "{{ asset('js/mainFetch.js') }}";

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

                table.on('draw', function () {
                    const showButtons = document.querySelectorAll('button[id=product-show]');
                    showButtons.forEach(elem => {
                        elem.addEventListener('click', function (event) {
                            const id = getRowData(event.target.closest('tr[class=odd]')).id;
                            window.location.href = `products/${id}`;
                        });
                    });
                    const removeButtons = document.querySelectorAll('button[id=product-delete]');
                    removeButtons.forEach(elem => {
                        elem.addEventListener('click', function (event) {
                            document.getElementById('modal-delete-btn').click();
                            modalRemoveProductAccept(event.target.closest('tr[class=odd]'));
                            document.removeEventListener('click', modalRemoveProductAccept);
                        });
                    });
                    const editButtons = document.querySelectorAll('button[id=product-edit]');
                    editButtons.forEach(elem => {
                        elem.addEventListener('click', function (event) {
                            const id = getIdFromRow(event.target.closest('tr[class=odd]'));
                            window.location.href = `products/${id}/edit`;
                        });
                    });
                });

                const showButtons = document.querySelectorAll('button[id=product-show]');
                showButtons.forEach(elem => {
                    elem.addEventListener('click', function (event) {
                        const id = getRowData(event.target.closest('tr[class=odd]')).id;
                        window.location.href = `products/${id}`;
                    });
                })

                const removeButtons = document.querySelectorAll('button[id=product-delete]');
                removeButtons.forEach(elem => {
                    elem.addEventListener('click', function (event) {
                        document.getElementById('modal-delete-btn').click();
                        modalRemoveProductAccept(event.target.closest('tr[class=odd]'));
                        document.removeEventListener('click', modalRemoveProductAccept);
                    });
                });

                const editButtons = document.querySelectorAll('button[id=product-edit]');
                editButtons.forEach(elem => {
                    elem.addEventListener('click', function (event) {
                        const id = getRowData(event.target.closest('tr[class=odd]')).id;
                        window.location.href = `products/${id}/edit`;
                    });
                })

                function modalRemoveProductAccept(element) {
                    document.addEventListener('click', function (event) {
                        if (event.target === document.getElementById('delete-btn')) {
                            const id = getIdFromRow(element);
                            mainFetch(`products/${id}`, 'delete')
                                .then(response => {
                                    if (response?.status === 'Error') {
                                        setErrorModalWindow(response.message);
                                    } else {
                                        table.row(element).remove().draw();
                                    }
                                })
                        }
                    })
                }

                function getMpnForRow(element) {
                    return getRowData(element).manufacturer_part_number;
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

                    const formData = new FormData(e.target);
                    mainFetch('import/products', 'POST', formData)
                        .then(response => {
                            if (response.status === 'Success') {
                                $('#table2').DataTable().clear().destroy();
                                initTable();
                                removeInputError();

                                const input = e.target.querySelector('#import-file');
                                input.value = '';
                                input.nextElementSibling.innerHTML = 'Choose csv import file...';

                                const successAlert =
                                    `<x-adminlte-alert id="success-alert" class="position-absolute top-0 end-0 m-3 bg-green" style="right: 0;" icon="fa fa-lg fa-thumbs-up" title="Done" dismissable>
                                        Your data was imported!
                                    </x-adminlte-alert>`;

                                showAlert(successAlert, 'content-wrapper');
                                destroyAlert('success-alert', 5000);
                            } else if (response.status === 'Error' && response.data) {
                                removeInputError();

                                const block = document.createElement('div');
                                response.data.forEach(elem => {
                                    elem.errors.forEach(error => {
                                        const errorMessage = document.createElement('p');
                                        errorMessage.textContent = `row-${elem.row} x ${error}`;
                                        block.appendChild(errorMessage);
                                    })
                                });

                                setErrorModalWindow(block);
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

        function showAlert(element, place) {
            const div = document.getElementsByClassName(place)[0];
            div.classList.add('position-relative');
            div.insertAdjacentHTML('afterbegin', element);
        }

        function destroyAlert(alertId, timer) {
            setInterval(function () {
                const alertToRemove = document.getElementById(alertId);
                if (alertToRemove) {
                    alertToRemove.remove();
                }
            }, timer);
        }

        function setErrorModalWindow(body) {
            const errorModal = document.getElementById('errors-modal');
            const modalBody = errorModal.getElementsByClassName('modal-body')[0];
            modalBody.innerHTML = '';
            modalBody.appendChild(body);
            document.getElementById('error-modal-button').click();
        }

        function exportButton() {
            const btn = document.getElementById('export-btn');
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                mainFetch('export/products', 'GET')
                    .then(response => {
                       console.log(response)
                    })
                    .catch(errors => {
                        console.log(errors)
                    })
            });
        }exportButton();
    </script>
@endpush



{{--
<button id="product-images" class="btn btn-xs btn-default text-danger mx-1 shadow" title="Images" data-toggle="modal" data-target="#productImages">
    <i class="fa fa-lg fa-fw fa-image"></i>
</button>--}}

{{--const productImagesButtons = document.querySelectorAll('button[id=product-images]');
productImagesButtons.forEach(elem => {
elem.addEventListener('click', function (event) {
const data = getRowData(event.target.closest('tr[class=odd]'));
const modalBody = getModalBody();
const productImages = data.images;
modalBody.innerHTML = '<div class="container d-flex justify-content-center flex-column"></div>';

productImages.forEach(img => {
const elem = addProductImage(img.id, img.path);
putProductImageInBody(modalBody, elem);
});
removeImage();
document.removeEventListener('click', removeImage);
addImage();
document.removeEventListener('click', addImage);
});
})--}}

{{--
function addProductImage(imageId, imagePath) {
return `<div class="d-flex justify-content-center flex-column mb-2 position-relative d-inline-block">
    <button id="delete-image" type="button" class="btn btn-xs btn-default text-primary position-absolute top-0 end-0 mt-2 mr-2" aria-label="Close" style="top: 0; right: 0; transform: translate(50%, -50%);"><i class="bi bi-x"></i></button>
    <input type="hidden" name="image_path" value="${imageId}"/>
    <img src="${imagePath}" class="img-fluid">
</div>`;
}--}}
{{--
const productImagesButtons = document.querySelectorAll('button[id=product-images]');
productImagesButtons.forEach(elem => {
elem.addEventListener('click', function (event) {
const data = getRowData(event.target.closest('tr[class=odd]'));
const modalBody = getModalBody();
const productImages = data.images;
modalBody.innerHTML = '<div class="container d-flex justify-content-center flex-column"></div>';

productImages.forEach(img => {
const elem = addProductImage(img.id, img.path);
putProductImageInBody(modalBody, elem);
});
removeImage();
document.removeEventListener('click', removeImage);
addImage();
document.removeEventListener('click', addImage);
});
})--}}
{{--
function removeImage() {
document.addEventListener('click', function (event) {
const deleteBtn = document.getElementById('delete-image');
if (event.target === deleteBtn || (event.target === deleteBtn.firstElementChild)) {
const parent = event.target.closest('div.d-flex');
const imageId = parent.querySelector('input[type=hidden]').value;
mainFetch(`images/${imageId}`, 'DELETE')
.then(response => {
if (response.status === 'Success') {
parent.remove();
console.log('Image deleted');
}
})
}
});
}

function addImage() {
document.addEventListener('click', function (event) {
const addBtn = document.getElementById('add-image');
if (event.target === addBtn) {
const productData = getRowData(event.target.closest('tr[class=odd]'));
const images = document.getElementById('image-file');
const data = new FormData();
data.append('product_id', productData.id);
if (images.files.length > 0) {
for (let i = 0; i < images.files.length; i++) {
data.append('images[]', images.files[i]);
}
}
mainFetch('images', 'POST', data)
.then(response => {
if (response.status === 'Success') {
response.data.forEach(image => {
const modalBody = getModalBody();
const elem = addProductImage(image.id, image.path);
putProductImageInBody(modalBody, elem);
});
}
})
}
});
}--}}
{{--
function getModalBody() {
return document.getElementById('productImages').getElementsByClassName('modal-body')[0];
}

function putProductImageInBody(modalBody, elem) {
modalBody.getElementsByClassName('container')[0].insertAdjacentHTML('beforeend', elem);
}--}}
{{--<x-adminlte-modal id="productImages" title="Product Images" size="lg" theme="teal" v-centered static-backdrop
                  scrollable>
    <p></p>
    <x-slot name="footerSlot">
        <x-adminlte-input-file id="image-file" name="image-file" multiple/>
        <x-adminlte-button id="add-image" class="mr-auto" theme="success" label="Add"/>
        <x-adminlte-button theme="danger" label="Close" data-dismiss="modal"/>
    </x-slot>
</x-adminlte-modal>--}}
