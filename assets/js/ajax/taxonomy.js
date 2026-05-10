$("#BrandAjax").submit(function (e) {

    e.preventDefault();
    if(ValidateForm(this.id) == false){
        return false;
    }
    $("#BrandAjax .btn").val("در حال ارسال اطلاعات");
    var type_action = $("#BrandAjax input#type_action").val();
    var uid = $("#BrandAjax input#uid").val();
    var name = $("#BrandAjax input#name").val();


    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajaxurl,
        data: {
            'action' : 'brand',
            'type_action' : type_action,
            'uid' : uid,
            'name' : name,

        },
        success: function (data) {

            if (data.status) {

                ALERT(data.message,"s");
                $("#BrandAjax .btn").val("برند اضافه گردید");

            }else{
                ALERT(data.message,"e");
                $("#BrandAjax .btn").val("افزودن برند");
            }

        },
        error: function (errorThrown) {

        }
    });
});
$("#DeviceAjax").submit(function (e) {

    e.preventDefault();
    if(ValidateForm(this.id) == false){
        return false;
    }
    $("#DeviceAjax .btn").val("در حال ارسال اطلاعات");
    var type_action = $("#DeviceAjax input#type_action").val();
    var uid = $("#DeviceAjax input#uid").val();
    var name = $("#DeviceAjax input#name").val();


    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajaxurl,
        data: {
            'action' : 'device',
            'type_action' : type_action,
            'uid' : uid,
            'name' : name,

        },
        success: function (data) {

            if (data.status) {

                ALERT(data.message,"s");
                $("#DeviceAjax .btn").val("برند اضافه گردید");

            }else{
                ALERT(data.message,"e");
                $("#DeviceAjax .btn").val("افزودن برند");
            }

        },
        error: function (errorThrown) {

        }
    });
});
$("#StateAjax").submit(function (e) {

    e.preventDefault();
    if(ValidateForm(this.id) == false){
        return false;
    }
    $("#StateAjax .btn").val("در حال ارسال اطلاعات");
    var type_action = $("#StateAjax input#type_action").val();
    var uid = $("#StateAjax input#uid").val();
    var name = $("#StateAjax input#name").val();


    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajaxurl,
        data: {
            'action' : 'state',
            'type_action' : type_action,
            'uid' : uid,
            'name' : name,

        },
        success: function (data) {

            if (data.status) {

                ALERT(data.message,"s");
                $("#StateAjax .btn").val("استان اضافه گردید");

            }else{
                ALERT(data.message,"e");
                $("#StateAjax .btn").val("افزودن استان");
            }

        },
        error: function (errorThrown) {

        }
    });
});
$("#CityAjax").submit(function (e) {

    e.preventDefault();
    if(ValidateForm(this.id) == false){
        return false;
    }
    $("#CityAjax .btn").val("در حال ارسال اطلاعات");
    var type_action = $("#CityAjax input#type_action").val();
    var uid = $("#CityAjax input#uid").val();
    var name = $("#CityAjax input#name").val();


    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajaxurl,
        data: {
            'action' : 'city',
            'type_action' : type_action,
            'uid' : uid,
            'name' : name,

        },
        success: function (data) {

            if (data.status) {

                ALERT(data.message,"s");
                $("#CityAjax .btn").val("شهر اضافه گردید");

            }else{
                ALERT(data.message,"e");
                $("#CityAjax .btn").val("افزودن شهر");
            }

        },
        error: function (errorThrown) {

        }
    });
});