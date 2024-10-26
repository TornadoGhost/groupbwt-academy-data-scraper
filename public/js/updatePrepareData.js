export function updatePrepareData(form) {
    let formData = new FormData(form);
    const urlEncodedData = new URLSearchParams();
    formData.forEach((value, key) => {
        if (key === 'password' && value === '') {
            return;
        }
        urlEncodedData.append(key, value);
    });

    return urlEncodedData;
}
