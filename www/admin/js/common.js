function isFunction(possibleFunction) {
    return typeof(possibleFunction) === typeof(Function);
}
function params(object) {
    var encodedString = '';
    for (var prop in object) {
        if (object.hasOwnProperty(prop)) {
            if (encodedString.length > 0) {
                encodedString += '&';
            }
            encodedString += encodeURI(prop + '=' + object[prop]);
        }
    }
    return encodedString;
}
function ajax(obj) {
    if (isFunction(obj.before)) {
        obj.before();
    }
    var xhr = new XMLHttpRequest();
    var type = obj.type.toLowerCase();
    var data = null;
    if (obj.data) {
        data = params(obj.data);
    }
    if (type=='get') {
        xhr.open('GET', obj.url);
    }
    if (type=='post') {
        xhr.open('POST', obj.url);
        if (obj.processForm == true) {
            var data = obj.data;
        }
        else {
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        }
    }
    xhr.onload = function() {
        if (xhr.status === 200) {
            obj.success(xhr.response);
        }
        else if (xhr.status !== 200) {
            if (undefined !== obj.error) {
                obj.error();
            }
        }
    };
    if (obj.data) {
        xhr.send(data);
    }
    else {
        xhr.send();
    }
}

function loadingCart(show) {
    var elem = document.querySelector('#checkout-loading');
    if (show != false) {
        elem.classList.remove('hide');
    }
    else {
        elem.classList.add('hide');
    }
}