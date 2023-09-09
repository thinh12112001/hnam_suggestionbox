

function loadThirdPartyPartner() {
    if (loadThirdPartyJSScroll == false) {
        loadThirdPartyJSScroll = true;


        setTimeout(function () {
            let third_party = [];

            third_party.push({src: '/backend/js/dayjs.js'});
            third_party.push({src: '/backend/js/search-init-calendar.js', defer: true});

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