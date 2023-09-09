// import PhotoSwipeLightbox from 'https://unpkg.com/photoswipe@5.3.4/dist/photoswipe-lightbox.esm.js';
// import PhotoSwipe from 'https://unpkg.com/photoswipe@5.3.4/dist/photoswipe.esm.js';
function showGallery(id) {

    lightGallery(document.getElementById(id));
    document.querySelector('#'+id+' li:first-child').click();
}

function loadThirdPartyPartner() {
    if (loadThirdPartyJSScroll == false) {
        loadThirdPartyJSScroll = true;
        // import PhotoSwipeLightbox from '/backend/photoswipe/photoswipe.esm.js';
        // import PhotoSwipeLightbox from '/backend/photoswipe/photoswipe-lightbox.esm.js';
        //
        setTimeout(function () {
            let third_party = [];
            let third_css = [];

            third_css.push({src: '/backend/lightgallery/dist/css/lightgallery.css'});
            third_css.forEach(function (item) {
                loadCSS(item);
            });
            third_party.push({src: '/backend/lightgallery/dist/js/lightgallery.js'});
            third_party.push({src: '/backend/lg-zoom/dist/lg-zoom.js?v=1', defer: true});
            third_party.push({src: '/backend/lg-video/dist/lg-video.js', defer: true});
            third_party.forEach(function (item) {

                loadJS(item);

            });
        },50);

    }
}

document.addEventListener("scroll", loadThirdPartyPartner);
document.addEventListener('mousemove', loadThirdPartyPartner);
document.addEventListener('mousedown', loadThirdPartyPartner);
document.addEventListener('keydown', loadThirdPartyPartner);
document.addEventListener("touchstart", loadThirdPartyPartner);
document.addEventListener("DOMContentLoaded", function () {
    // if (document.querySelector('.load-third-party')) {
    //     loadThirdPartyPartner();
    // }
});