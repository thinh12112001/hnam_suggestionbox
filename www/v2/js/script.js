let navbar__burger = document.querySelector('.navbar__burger');
navbar__burger.addEventListener('click', function () {
    this.classList.toggle('is-active');
    document.getElementById("wrapper-menu").classList.toggle('is-active');
});

function isEmptyObject(obj) {
    return JSON.stringify(obj) == '{}';
}
function loadJS(obj) {
    if (obj.src !== undefined && obj.src != '') {
        var newJS;
        newJS = document.createElement('script');
        if (obj.async === true) {
            newJS.async = true;
        }
        if (obj.defer === true) {
            newJS.defer = true;
        }
        if (obj.crossorigin != undefined) {
            newJS.crossorigin = obj.crossorigin;
        }
        if (isFunction(obj.onload)) {
            newJS.onload = obj.onload;
        }
        newJS.src = obj.src;
        document.head.appendChild(newJS);
    }
}
function loadCSS(obj) {
    if (obj.src !== undefined && obj.src != '') {

        var link  = document.createElement('link');
        link.rel  = 'stylesheet';
        link.type = 'text/css';
        link.href = obj.src;
        link.media = 'all';
        document.head.appendChild(link);
    }
}
function isFunction(possibleFunction) {
    return typeof(possibleFunction) === typeof(Function);
}
