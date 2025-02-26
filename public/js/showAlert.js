export function showAlert(alertToAdd, elementClass, destroy, timer) {
    const div = document.getElementsByClassName(elementClass)[0];
    div.classList.add('position-relative');
    div.insertAdjacentHTML('beforeend', alertToAdd);

    if (destroy) {
        destroyAlert(timer, div);
    }
}

function destroyAlert(timer, block) {
    setTimeout(function () {
        if (block.lastChild) {
            block.removeChild(block.lastChild);
        }
    }, timer);
}
