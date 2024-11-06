export function showAlert(alertToAdd, elementClass, destroy, timer) {
    const div = document.getElementsByClassName(elementClass)[0];
    div.classList.add('position-relative');
    div.insertAdjacentHTML('afterbegin', alertToAdd);

    if (destroy) {
        destroyAlert(timer, div);
    }
}

function destroyAlert(timer, block) {
    setTimeout(function () {
        if (block.firstChild) {
            block.removeChild(block.firstChild);
        }
    }, timer);
}
