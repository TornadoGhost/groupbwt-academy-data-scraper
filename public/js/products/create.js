import {mainFetch} from "../mainFetch.js";

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
                document.getElementById('modal-open-btn').click();
                form.reset();
            }
        });
});

function addReturnButton() {
    const modal = document.getElementById('modalMin');
    const footer = modal.querySelector('.modal-footer');
    const button = `
                    <a href="/products"><button type="button" class="btn btn-default bg-green">Back to products</button></a>
            `;
    footer.insertAdjacentHTML("afterbegin", button)
}

addReturnButton();

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
    for (let i = 0; i < retailers.length; i++) {
        retailersForm.insertAdjacentHTML("beforeend", `
                    <div class="input-group mb-2">
                        <div class="input-group col-md-6 pl-0">
                        <input class="form-control" id="retailers.${i}.product_url" name="retailers[${i}][product_url]"
                        type="text" placeholder="Enter product url">
                                <span class="invalid-feedback" role="alert">
                                    <strong></strong>
                                </span>
                        </div>
                    <input disabled class="form-control" type="text" value="${retailers[i]['name']}" >
                    <input type="hidden" name="retailers[${i}][retailer_id]" value="${retailers[i]['id']}">
                                <span class="invalid-feedback" role="alert">
                                    <strong></strong>
                                </span>
                    </div>`);
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