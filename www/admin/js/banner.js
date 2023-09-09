function changeStatus(id, token, type) {
    loadingCart();
    if (document.querySelector('input[name="status' + id + '"]:checked')) {
        type = 1
    } else {
        type = 0;
    }
    ajax({
        url: '/admin/banner/change-status-banner',
        type: 'POST',
        data: { id: id, token: token, type: type },
        success: function (response) {
            var data = JSON.parse(response);
            if (data.msg) {
                alert(data.msg);
            }
            if (data.reloads) {
                window.location.reload();
            }
            if (data.redirect) {
                window.location.href = data.redirect;
            }
            loadingCart(false);
        },
        error: function () {
            alert('Lỗi hệ thống! Vui lòng thử lại sau.');
            window.location.reload();
            loadingCart(false);
        },
    });
}
function loadDataTable() {
    loadingCart();
    var search = document.getElementById('search').value;
    var status = document.getElementById('status').value;
    var group_type = document.getElementById('groupType').value;

    var url = '/admin/banner/ajax-list-banner?search=' + search + '&status=' + status + '&group_type=' + group_type;
    $('#tableBanner').DataTable().ajax.url(url).load(function () {
        $("input[data-bootstrap-switch]").each(function () {
            $(this).bootstrapSwitch();
        });
        loadingCart(false);
        $("#tableBanner").fadeIn("slow");
    });
}

function GetDataToExport() {
    var search = document.getElementById('search').value;
    var status = document.getElementById('status').value;
    var group_type = document.getElementById('groupType').value;
    var jsonResult = $.ajax({

        url: '/admin/banner/ajax-list-banner?search=' + search + '&status=' + status + '&group_type=' + group_type + '&page=all',
        data: {},
        success: function (result) { },
        async: false
    });

    var exportBody = JSON.parse(jsonResult.responseText).data;
    return exportBody.map(function (el) {
        return Object.keys(el).map(function (key) { return el[key] });
    });
}

$(function () {
    if (document.getElementById("tableBanner")) {
        loadingCart();
        var search = document.getElementById('search').value;
        var status = document.getElementById('status').value;
        var group_type = document.getElementById('groupType').value;
        $("#tableBanner").DataTable({
            "responsive": true, "lengthChange": false, "autoWidth": false,
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
                // {
                //     extend: 'excel',
                //     header: false,
                //     exportOptions: {
                //         columns: [0, 1, 2, 3, 4],
                //         customizeData: function (d) {
                //             var exportBody = GetDataToExport2();
                //             d.body.length = 0;
                //             d.body.push.apply(d.body, exportBody);
                //         }
                //     },
                //     text: 'Excel tồn kho theo thương hiệu',
                //     title: 'tồn kho theo thương hiệu'
                // }
            ],
            'processing': true,
            'serverSide': true,
            'serverMethod': 'post',
            'ajax': {
                'url': '/admin/banner/ajax-list-banner?search=' + search + '&status=' + status + '&group_type=' + group_type,
                "dataSrc": function (json) {
                    return json.aaData;
                }
            },
            'columns': [
                { data: 'stt' },
                { data: 'p_images' },
                { data: 'p_title' },
                { data: 'p_group_type' },
                { data: 'p_date_start' },
                { data: 'p_date_end' },
                { data: 'p_actived' },
            ],
            columnDefs: [{
                targets: [0, 1, 2, 3, 4, 5, 6],
                orderable: false,
                searchable: true,
            }],
            "searching": false,
            "initComplete": function () {
                $("input[data-bootstrap-switch]").each(function () {
                    $(this).bootstrapSwitch();
                });
                loadingCart(false);
            },

        });
    }
});
function submitAddBanner(obj) {
    window.event.preventDefault();
    button = obj.querySelector('.btn-add');
    var formData = new FormData(obj);
    ajax({
        url: '/admin/ajax/submit-banner',
        type: 'POST',
        data: formData,
        before: function () {
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
                var field = obj.querySelector("#" + data.field);
                if (field) {
                    field.focus();
                }
            }
            else {
                if (data.reloads) {
                    window.location.reload();
                }
                if (data.url) {
                    window.location.href = data.url;
                }
            }
            loadingCart(false);
        },
        error: function () {
            alert('Lỗi hệ thống! Vui lòng thử lại sau.');
            loadingCart(false);
        },
        processForm: true,
    });
}