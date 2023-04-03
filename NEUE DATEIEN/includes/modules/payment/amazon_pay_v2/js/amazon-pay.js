const zencartAmazonPayV2 = {
    payButtonCount: 0,
    initCheckout: function () {
        console.log('would start checkout');
    },
    ajaxPost: function (form, callback) {
        const url = form.action, xhr = new XMLHttpRequest();
        const params = [];
        const fields = form.querySelectorAll('input, select, textarea');
        for (let i = 0; i < fields.length; i++) {
            const field = fields[i];
            if (field.name && field.value) {
                params.push(encodeURIComponent(field.name) + '=' + encodeURIComponent(field.value));
            }
        }
        xhr.open("POST", url);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.onload = callback.bind(xhr);
        xhr.send(params.join('&'));
    }
}