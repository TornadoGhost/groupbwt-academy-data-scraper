import {mainFetch} from "../mainFetch.js";
import {updatePrepareData} from "../updatePrepareData.js";
import {getRetailers} from "./getRetailers.js";
import {getProduct} from "./getProducts.js";

const productId = window.location.href.match(/\/products\/(\d+)/)[1];
const product = await getProduct(productId);

const submitButton = document.getElementById('save-button');
submitButton.addEventListener('click', function (event) {
    event.preventDefault();

    const form = document.getElementById('product-update');
    const data = updatePrepareData(form);

    mainFetch(`products/${productId}`, 'PATCH', data.toString())
        .then(response => {
            if (response.errors) {
                const errors = response.errors;
                const errorInputName = Object.entries(errors);
                errorInputName.forEach(value => {
                    addValidationMessage(value[0], value[1][0])
                })
            } else {
                document.getElementById('modal-open-btn').click();
            }
        });
});

function addValidationMessage(id, message) {
    const element = document.getElementById(id);
    if (element) {
        element.classList.add('is-invalid');
        element.nextElementSibling.children[0].innerHTML = message;
    }
}

removeValidationMessage();

function removeValidationMessage() {
    document.querySelectorAll('input').forEach(input => {
        input.addEventListener('keypress', function (event) {
            event.target.classList.remove('is-invalid');
        });
    })
}

function addReturnButton() {
    const modal = document.getElementById('modalMin');
    const footer = modal.querySelector('.modal-footer');
    const button = `
          <a href="/products"><button type="button" class="btn btn-default bg-green">Back to products</button></a>`;
    footer.insertAdjacentHTML("afterbegin", button)
}

addReturnButton();

async function setRetailersData() {
    const retailers = await getRetailers();
    const retailersForm = document.getElementById('retailers');
    retailers.forEach((retailer, i) => {
        const existingRetailer = product.retailers.find(pr => pr.name === retailer.name);
        const productUrl = existingRetailer ? existingRetailer.product_url : '';
        retailersForm.insertAdjacentHTML("beforeend", `
                <div class="input-group mb-2">
                    <div class="input-group col-md-6 pl-0">
                        <input class="form-control" id="retailers.${i}.product_url" name="retailers[${i}][product_url]" value="${productUrl}"
                        type="text" placeholder="Enter product url">
                        <span class="invalid-feedback" role="alert">
                            <strong></strong>
                        </span>
                    </div>
                    <input disabled class="form-control" type="text" value="${retailer.name}" >
                    <input type="hidden" name="retailers[${i}][retailer_id]" value="${retailer.id}">
                    <span class="invalid-feedback" role="alert">
                        <strong></strong>
                    </span>
                </div>
    `);
    });
}setRetailersData();

$(document).ready(async function () {
    const baseUrl = 'http://localhost/';
    const imagesPath = product.images.map(item => `${baseUrl}${item.path}`);
    const imagesId = product.images.map(item => item.id);
    $("#input-b5").fileinput({
        initialPreview: imagesPath,
        initialPreviewAsData: true,
        initialPreviewConfig: imagesId.map((id, index) => ({
            caption: `Image ${index + 1}`,
            key: id,
        })),
        showRemove: false,
        showUpload: false,
        showClose: false,
        overwriteInitial: false,
        maxFileCount: 10
    }).on('fileloaded', function (event, file, previewId, index, reader) {
        const formData = new FormData();
        formData.append('product_id', productId);
        formData.append('images[]', file);

        mainFetch('images', 'POST', formData, null, null, false);
    })


    function updateDeleteButtons() {
        const deleteImageButtons = document.querySelectorAll('button[type=button][title="Remove file"]');
        deleteImageButtons.forEach(item => {
            item.addEventListener('click', async function (event) {
                const imageId = event.target.closest('button').getAttribute('data-key');
                const elem = event.target.closest('div.krajee-default');
                elem.remove();

                try {
                    const response = await mainFetch(`images/${imageId}`, 'DELETE');
                    console.log("Image deleted:", response);
                } catch (error) {
                    console.error("Error deleting image:", error);
                }
            });
        });
    }updateDeleteButtons();
});