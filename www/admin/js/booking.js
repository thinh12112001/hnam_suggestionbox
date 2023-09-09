$(function () {
    if (document.getElementById("tableBooking")){
        loadingCart();
        var search = " ";
        var status = " ";
        var group_type = " ";
       
        
        $("#tableBooking").DataTable({            
            "dom": 'Blfrtip',
            "buttons": [
                {
                    extend: 'excel',
                    exportOptions: {
                        customizeData: function (d) {
                            var exportBody = GetDataToExport();
                            d.body.length = 0;
                            d.body.push.apply(d.body, exportBody);
                            
                        }
                    }
                },
            ],
            
            'processing': true,
            'serverSide': true,
            'serverMethod': 'post',
            'ajax': {
                
                'url':'/admin/booking/ajax-list-booking?search='+search+'&status='+status+'&group_type='+group_type,
                "dataSrc": function ( json ) {
                    return json.aaData;
                }
            },
            'columns': [
                { data: 'stt' },
                { data: 'p_customername' },
                { data: 'p_phone' },
            ],
            "initComplete": function(){
                $("input[data-bootstrap-switch]").each(function(){
                    $(this).bootstrapSwitch();
                });
                loadingCart(false);
            },

        });
    }
});

function loadDataTable() {
    loadingCart();
    // var search = document.getElementById('search').value;
    // var status = document.getElementById('status').value;
    var search = " ";
    var status = " ";
    var group_type = " ";
    // var group_type = document.getElementById('groupType').value;

    var url = '/admin/booking/ajax-list-booking?search='+search+'&status='+status+'&group_type='+group_type;
    $('#tableBooking').DataTable().ajax.url(url).load(function () {
        $("input[data-bootstrap-switch]").each(function(){
            $(this).bootstrapSwitch();
        });
        loadingCart(false);
        $("#tableBooking").fadeIn("slow");
    });
}

function GetDataToExport() {
    var search = " ";
    var status = " ";
    var group_type = " ";
    var jsonResult = $.ajax({

        url: '/admin/booking/ajax-list-booking?search='+search+'&status='+status+'&group_type='+group_type+'&page=all',
        data: {},
        success: function (result) {},
        async: false
    });

    var exportBody = JSON.parse(jsonResult.responseText).data;
    return exportBody.map(function (el) {
        return Object.keys(el).map(function (key) { return el[key] });
    });
}

function submitAddBooking(obj){
    window.event.preventDefault();
    button = obj.querySelector('.btn-add');
    var formData = new FormData(obj);
    ajax({
        url: '/admin/ajax/submit-booking',
        type: 'POST',
        data: formData,
        before: function() {
            button.setAttribute('disabled', true);
            loadingCart();
        },
        success: function (response) {
            button.removeAttribute('disabled');
            var data = JSON.parse(response);
            console.log(data);
            if (data.msg != '') {
                alert(data.msg);
            }
            if (data.field) {
                var field = obj.querySelector("#"+data.field);
                if (field) {
                    field.focus();
                }
            }
            else {
                if (data.reloads) {
                    window.location.reload();
                }
                if(data.url){
                    window.location.href = data.url;
                }
            }
            loadingCart(false);
        },
        error: function () {
            alert('Lỗi hệ thống! Vui lòng thử lại sau. hehe');
            loadingCart(false);
        },
        processForm: true,
    });
}