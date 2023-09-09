function initSwiper() {
    new Swiper('.slideshow', {
        navigation: {
            nextEl: ".swiper-button-next-slide",
            prevEl: ".swiper-button-prev-slide"
        },
        breakpoints: {
            0: {
                slidesPerView: "auto"
            },
            992: {
                slidesPerView: 3
            },
        },
        loop: true,
        autoplay: true,
    });
    new Swiper('.slideshow-customer', {
        navigation: {
            nextEl: ".swiper-button-next-slide",
            prevEl: ".swiper-button-prev-slide"
        },
        loop: true,
        autoplay: true,

    });
}
function initDatepicker() {
    // $('#checking-check-in').datetimepicker();
    var $dp1 = $("#checking-check-in");
    $dp1.datepicker({
        minDate: 0,
        changeMonth: true,
        changeYear: true,
        onSelect: function() {
            var displayDate = $(this).datepicker( "option", "dateFormat", "dd MM yy" ).val();
            $(this).prev().html(displayDate);
        }
    });
    var $dp2 = $("#checking-check-out");
    $dp2.datepicker({
        minDate: 0,
        changeMonth: true,
        changeYear: true,
        onSelect: function() {
            var displayDate = $(this).datepicker( "option", "dateFormat", "dd MM yy" ).val();
            $(this).prev().html(displayDate);
        }
    });


}
function showGallery(id) {

    lightGallery(document.getElementById(id));
    document.querySelector('#'+id+' li:first-child').click();
}
function loadThirdPartyPartner() {
    if (loadThirdPartyJSScroll == false) {
        loadThirdPartyJSScroll = true;


        setTimeout(function () {
            let third_party = [];
            let third_css = [];

            third_css.push({src: '/backend/lightgallery/dist/css/lightgallery.css'});
            third_css.forEach(function (item) {
                loadCSS(item);
            });
            third_party.push({src: '/backend/js/jquery.js'});
            third_party.push({src: '/backend/js/swiper-bundle.min.js', defer: true, onload: initSwiper});
            third_party.push({src: '/backend/js/moment.js', defer: true});
            third_party.push({src: '/backend/js/jquery-ui.js', defer: true, onload: initDatepicker});
            third_party.push({src: '/backend/lightgallery/dist/js/lightgallery.js'});
            third_party.push({src: '/backend/lg-zoom/dist/lg-zoom.js?v=1', defer: true});
            third_party.push({src: '/backend/lg-video/dist/lg-video.js', defer: true});
            third_party.forEach(function (item) {
                    loadJS(item);
            });
        },100);

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