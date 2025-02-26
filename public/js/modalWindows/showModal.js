export function showModal(modalWindowId) {
    let bootstrapModal = new bootstrap.Modal(document.getElementById(modalWindowId));
    bootstrapModal.show();
}
