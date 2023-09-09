
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
function changeRoomsSelect(elm) {
    var number_room = parseInt(elm.value);
    var num_list = document.querySelectorAll(".room-user-select").length;
    if (num_list > number_room){// remove element
        for (i = (number_room); i < num_list; i++){
            $("#room-user-select-"+i).hide('slow', function(){ $(this).remove(); });
        }
    } else // create element
    {
        var _option_selct = "";
        for (i =0; i< 10; i++){
            _option_selct +='<option value="'+i+'">'+i+'</option>'
        }
        for (i = num_list; i < number_room; i++){
            var total_room = i+1;
            var dom_element = '<div class="clear"></div><div style="display: none" class="room-user-select" id="room-user-select-'+i+'">\n' +
                '                                    <div class="title-room-select">ROOMS '+total_room+'</div>\n' +
                '                                    <div class="checking-field checking-combobox check-room" >\n' +
                '                                        <label for="checking-adult-number-'+i+'" class="checking-field-title">Adults</label>\n' +
                '                                        <div class="checking-combobox-wrap">\n' +
                '                                            <select name="checking-adult-number" id="checking-adult-number-'+i+'" class="checking-select-box">'+_option_selct+'</select>\n' +
                '                                        </div>\n' +
                '                                    </div>\n' +
                '                                    <div class="checking-field checking-combobox check-room">\n' +
                '                                        <label for="checking-children-number-'+i+'" class="checking-field-title">Children</label>\n' +
                '                                        <div class="checking-combobox-wrap">\n' +
                '                                            <select name="checking-children-number" id="checking-children-number-'+i+'" class="checking-select-box">\n' +_option_selct+'</select>\n' +
                '                                        </div>\n' +
                '                                    </div>\n' +
                '                                </div>\n';
            $("#room-user-checking").append(dom_element);
            $("#room-user-select-"+i).show("slow");

        }
    }
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
            third_party.push({src: '/backend/js/jquery.js', async: true});
            third_party.push({src: '/backend/js/moment.js', defer: true});
            third_party.push({src: '/backend/js/jquery-ui.js', defer: true, onload: initDatepicker});
            third_party.push({src: '/backend/lightgallery/dist/js/lightgallery.js'});
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