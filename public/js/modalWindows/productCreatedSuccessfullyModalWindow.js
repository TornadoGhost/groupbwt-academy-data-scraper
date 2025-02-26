import {modalWindow} from "./modalWindow.js";

export function productCreatedSuccessfullyModalWindow() {
    const styles = {header: 'bg-green'}

    const header = `
            <h1 class="modal-title fs-5">Success</h1>
            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
    `;

    const body = `
        <p>Product created successfully. You can keep creating new ones or return on previous page.</p>
    `;

    const footer = `
        <a class="btn btn-secondary" href="/products">Back to products</a>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
    `;

    modalWindow(
        header,
        body,
        footer,
        styles
    );
}
