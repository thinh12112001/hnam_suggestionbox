
$(function () {
    if (document.getElementById("inputContent")) {
        // Summernote
        var $sumNote = $('#inputContent').summernote(
            {
                height: 400,
            }
        );
    }

});

function changeStatus(id,token,type) {
    loadingCart();
    if (document.querySelector('input[name="status'+id+'"]:checked')){
        type = 1
    }else {
        type = 0;
    }
    ajax({
        url: '/admin/news/change-status',
        type: 'POST',
        data: {id: id, token:token, type: type},
        success: function (response) {
            var data = JSON.parse(response);
            if (data.msg){
                alert(data.msg);
            }
            if (data.reloads) {
                window.location.reload();
            }
            if (data.redirect) {
                window.location.href=data.redirect;
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
    var parent_id = document.getElementById('parent_id').value;

    var url = '/admin/news/ajax-list?search='+search+'&status='+status+'&parent_id='+parent_id;
    $('#tableNews').DataTable().ajax.url(url).load(function () {
        $("input[data-bootstrap-switch]").each(function(){
            $(this).bootstrapSwitch();
        });
        loadingCart(false);
        $("#tableNews").fadeIn("slow");
    });
}

function GetDataToExport() {
    var search = document.getElementById('search').value;inlineScript
    var status = document.getElementById('status').value;
    var parent_id = document.getElementById('parent_id').value;
    var jsonResult = $.ajax({

        url: '/admin/news/ajax-list?search='+search+'&status='+status+'&parent_id='+parent_id+'&page=all',
        data: {},
        success: function (result) {},
        async: false
    });

    var exportBody = JSON.parse(jsonResult.responseText).data;
    return exportBody.map(function (el) {
        return Object.keys(el).map(function (key) { return el[key] });
    });
}
function loadDataTableGet() {
    loadingCart();
    var search = document.getElementById('search').value;
    var status = document.getElementById('status').value;


    var url = '/admin/news/list-cate?search='+search+'&status='+status;
    window.location.href = url;
}
$(function () {
    if (document.getElementById("tableCate")){
        $("#tableCate").DataTable({
            "initComplete": function(){
                $("input[data-bootstrap-switch]").each(function(){
                    $(this).bootstrapSwitch();
                    $(this).on('switchChange.bootstrapSwitch', function (event, state) {
                        var token = $(this).data("token");
                        var id = $(this).data("id");
                        loadingCart();
                        if (state){
                            var type = 1
                        }else {
                            var type = 0;
                        }
                        ajax({
                            url: '/admin/home/change-status-cate',
                            type: 'POST',
                            data: {id: id, token:token, type: type},
                            success: function (response) {
                                var data = JSON.parse(response);
                                if (data.msg){
                                    alert(data.msg);
                                }
                                if (data.reloads) {
                                    window.location.reload();
                                }
                                if (data.redirect) {
                                    window.location.href=data.redirect;
                                }
                                loadingCart(false);
                            },
                            error: function () {
                                alert('Lỗi hệ thống! Vui lòng thử lại sau.');
                                window.location.reload();
                                loadingCart(false);
                            },
                        });
                    });
                });
            },
        });

    }
    if (document.getElementById("tableNews")){
        loadingCart();
        var search = document.getElementById('search').value;
        var status = document.getElementById('status').value;
        var parent_id = document.getElementById('parent_id').value;
        $("#tableNews").DataTable({
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
                'url':'/admin/news/ajax-list?search='+search+'&status='+status+'&parent_id='+parent_id,
                "dataSrc": function ( json ) {
                    return json.aaData;
                }
            },
            'columns': [
                { data: 'stt' },
                { data: 'p_title' },
                { data: 'p_parent_id' },
                { data: 'p_created' },
                { data: 'p_enabled' },
            ],
            columnDefs: [ {
                targets: [0,1,2,3,4],
                orderable: false,
                searchable: true,
            }],
            "searching": false,
            "initComplete": function(){
                $("input[data-bootstrap-switch]").each(function(){
                    $(this).bootstrapSwitch();
                });
                loadingCart(false);
            },

        });
    }
});




function submitAddNews(obj){
    window.event.preventDefault();
    button = obj.querySelector('.btn-add');
    var formData = new FormData(obj);
    ajax({
        url: '/admin/ajax/submit-news',
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
            alert('Lỗi hệ thống! Vui lòng thử lại sau.');
            loadingCart(false);
        },
        processForm: true,
    });
}



function submitAddCateProducts(obj){
    window.event.preventDefault();
    button = obj.querySelector('.btn-add');
    var formData = new FormData(obj);
    ajax({
        url: '/admin/ajax/submit-cate',
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
            alert('Lỗi hệ thống! Vui lòng thử lại sau.');
            loadingCart(false);
        },
        processForm: true,
    });
}

$("#download").click(function (e) {
    e.preventDefault();
    //valid form
    var $this = $(this);
    var html = $this.html();
    $this.html("Đang xử lý...").addClass("disabled");
    var title = $("#inputName").val();
    var content = $('#inputContent').summernote('code');

    if (title =="" || content == "") {
        alert("Vui lòng nhập đầy đủ nội dung!!!");
        $this.html(html).removeClass("disabled");
        return false;
    }
    var postURL = "/admin/contentsupport/download";
    $.post(postURL, $("#detail-form").serialize(), function(resp){

        var content = resp;
        if (content!="") {
            $('#inputContent').summernote('code', content);
            //unlock dowenload image
            $this.html(html).removeClass("disabled");
            alert("Download ảnh thành công!!!");
        } else {
            alert("Error download!!!");
        }
    });
});