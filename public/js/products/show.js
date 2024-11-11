import {getRetailers} from "./getRetailers.js";
import {getProduct} from "./getProducts.js"

const productId = window.location.href.match(/\/products\/(\d+)/)[1];
const product = await getProduct(productId);

async function setRetailersData() {
    const retailers = await getRetailers();
    const retailersForm = document.getElementById('retailers');
    retailers.forEach((retailer, i) => {
        const existingRetailer = product.retailers.find(pr => pr.name === retailer.name);
        const productUrl = existingRetailer ? existingRetailer.product_url : '';
        retailersForm.insertAdjacentHTML("beforeend", `
                <div class="input-group mb-2">
                    <div class="input-group col-md-6 pl-0">
                        <input disabled class="form-control" id="retailers.${i}.product_url" name="retailers[${i}][product_url]" value="${productUrl}"
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
    });
});