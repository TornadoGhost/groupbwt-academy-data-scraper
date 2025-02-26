export function modalWindow(headerHtml, bodyHtml, footerHtml, styles) {
    const modal = document.getElementById('modalWindow');
    modal.removeAttribute('aria-hidden');

    setHeader(modal, headerHtml, styles.header);
    setBody(modal, bodyHtml, styles.body);
    setFooter(modal, footerHtml, styles.footer);
}

function setHeader(modal, html, style) {
    const modalHeader = modal.querySelector('.modal-header');
    cleanElement(modalHeader);

    if (style) {
        modalHeader.classList.add(style);
    }

    modalHeader.insertAdjacentHTML('afterbegin', html);
}

function setBody(modal, html, style) {
    const modalBody = modal.querySelector('.modal-body');
    cleanElement(modalBody);

    if (style) {
        modalBody.classList.add(style);
    }

    modalBody.insertAdjacentHTML('afterbegin', html);
}

function setFooter(modal, html, style) {
    const modalFooter = modal.querySelector('.modal-footer');
    cleanElement(modalFooter);

    if (style) {
        modalFooter.classList.add(style);
    }

    if (!modalFooter) {
        modalFooter.insertAdjacentHTML('afterbegin',
            `button type="button" class="btn btn-secondary" data-bs-dismiss="">Close</button>`
        );
    } else {
        modalFooter.insertAdjacentHTML('afterbegin', html);
    }
}

function cleanElement(element) {
    element.innerHTML = '';
}
