import {mainFetch} from "../mainFetch.js";
import {productCreatedSuccessfullyModalWindow} from "../modalWindows/productCreatedSuccessfullyModalWindow.js";
import {showModal} from "../modalWindows/showModal.js";

const submitButton = document.getElementById('save-button');
submitButton.addEventListener('click', function (event) {
    event.preventDefault();

    const form = document.getElementById('product-create');
    let formData = new FormData(form);

    mainFetch('products', 'POST', formData)
        .then(response => {
            if (response.errors) {
                const errors = response.errors;
                console.log(errors);

                const errorInputName = Object.entries(errors);
                errorInputName.forEach(value => {
                    addValidationMessage(value[0], value[1][0])
                })
            } else {
                productCreatedSuccessfullyModalWindow();
                form.reset();
                showModal('modalWindow');
            }
        });
});

/*function addReturnButton() {
    const modal = document.getElementById('modalMin');
    const footer = modal.querySelector('.modal-footer');
    const button = `
                    <a href="/products"><button type="button" class="btn btn-default bg-green">Back to products</button></a>
            `;
    footer.insertAdjacentHTML("afterbegin", button)
}

addReturnButton();*/

function addValidationMessage(id, message) {
    const element = document.getElementById(id);
    if (element) {
        element.classList.add('is-invalid');
        element.nextElementSibling.children[0].innerHTML = message;
    }
}


function removeValidationMessage() {
    document.querySelectorAll('input').forEach(input => {
        input.addEventListener('keypress', function (event) {
            event.target.classList.remove('is-invalid');
        });
    })
}

removeValidationMessage();

async function getRetailers() {
    let data;
    await mainFetch('retailers', 'GET')
        .then(response => {
            if (response?.status === 'Success') {
                data = response.data;
            }
        })
    return data;
}

async function setRetailersData() {
    const retailers = await getRetailers();
    const retailersForm = document.getElementById('retailers');

    for (let i = -1; i < retailers.length;) {
        const row = document.createElement('div');
        row.classList.add('row', 'mb-2');

        for (let j = 0; j < 3; j++) {
            i += 1;

            if (i >= retailers.length) {
                break;
            }

            row.insertAdjacentHTML('beforeend', `
                    <div class="form-group col-md-4">
                        <input type="hidden" name="retailers[${i}][retailer_id]" value="${retailers[i]['id']}">
                        <input disabled class="form-control rounded-bottom-0" type="text" value="${retailers[i]['name']}" >
                        <div>
                            <input class="form-control rounded-top-0" id="retailers.${i}.product_url" name="retailers[${i}][product_url]"
                            type="text" placeholder="Enter product url">
                            <span class="invalid-feedback" role="alert">
                                <strong></strong>
                            </span>
                        </div>
                    </div>
                `);
        }

        retailersForm.append(row);
    }
}

setRetailersData();

$(document).ready(function () {
    $("#input-b5").fileinput({
        overwriteInitial: false,
        showRemove: false,
        showUpload: false,
        showClose: false,
    });
});
