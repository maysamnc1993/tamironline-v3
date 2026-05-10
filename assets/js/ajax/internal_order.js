$("#OrderInternalAjax").submit(function (e) {

    e.preventDefault();
    if(ValidateForm(this.id) == false){
        return false;
    }
    $("#OrderInternalAjax .btn").val("در حال ارسال اطلاعات");
    var type_action = $("#OrderInternalAjax input#type_action").val();
    var uid = $("#OrderInternalAjax input#uid").val();
    var order_type = $("#OrderInternalAjax select#order_type").val();
    var introduction = $("#OrderInternalAjax select#introduction").val();
    var customer = $("#OrderInternalAjax input#customer").val();
    var subscription = $("#OrderInternalAjax input#subscription").val();
    var first_name = $("#OrderInternalAjax input#first_name").val();
    var mobile = $("#OrderInternalAjax input#mobile").val();
    var phone = $("#OrderInternalAjax input#phone").val();
    var state = $("#OrderInternalAjax select#state").val();
    var city = $("#OrderInternalAjax select#city").val();
    var address = $("#OrderInternalAjax textarea#address").val();
    var device = $("#OrderInternalAjax select#device").val();
    var brand = $("#OrderInternalAjax select#brand").val();
    var objection = $("#OrderInternalAjax select#objection").val();
    var objection_description = $("#OrderInternalAjax textarea#objection_description").val();
    var technician = $("#OrderInternalAjax select#technician").val();
    var shipping = $("#OrderInternalAjax select#shipping").val();
    var status_internal_order = $("#OrderInternalAjax select#status_internal_order").val();


    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajaxurl,
        data: {
            'action' : 'internal_order',
            'type_action' : type_action,
            'uid' : uid,
            'order_type' : order_type,
            'introduction' : introduction,
            'customer' : customer,
            'subscription' : subscription,
            'first_name' : first_name,
            'mobile' : mobile,
            'phone' : phone,
            'state' : state,
            'city' : city,
            'address' : address,
            'device' : device,
            'brand' : brand,
            'objection' : objection,
            'objection_description' : objection_description,
            'technician' : technician,
            'shipping' : shipping,
            'status_internal_order' : status_internal_order,

        },
        success: function (data) {

            if (data.status) {

                ALERT(data.message,"s");
                $("#OrderInternalAjax .btn").val("سفارش اضافه گردید");
                $("body input , body select , body textarea").val("");
                window.location.href='https://crm.tamironline.com/add_order_internal/';

            }else{
                ALERT(data.message,"e");
                $("#OrderInternalAjax .btn").val("افزودن سفارش");
            }

        },
        error: function (errorThrown) {

        }
    });
});

$("#internal_Tech_DetailAjax").submit(function (e) {
    e.preventDefault();
    if(ValidateForm(this.id) == false){
        return false;
    }
    $("#internal_Tech_DetailAjax .btn").val("در حال ارسال اطلاعات");
    var uid = $("#internal_Tech_DetailAjax input#uid").val();
    var status = $("#internal_Tech_DetailAjax input#status:checked").val();
    var description_tech = $("#internal_Tech_DetailAjax textarea#description_tech").val();
    var cancel_desc = $("#internal_Tech_DetailAjax input#cancel_desc:checked").val();
    var cancel_desc_other = $("#internal_Tech_DetailAjax input#cancel_desc_other").val();
    var invoice_descripotion = $("#internal_Tech_DetailAjax textarea#invoice_descripotion").val();
    var device_img1 = $("#internal_Tech_DetailAjax input#device_img1").val();
    var customer_price = 0;
    var buy_price = 0;
    var piece_list = [];
    var customer_price_list = [];
    var buy_price_list = [];
    var hire = $(".InvoiceMaker input#hire").val().replace(/,/g, '');
    var transportation = $(".InvoiceMaker input#transportation").val().replace(/,/g, '');
    var discount = $(".InvoiceMaker input#discount").val().replace(/,/g, '');
    if(status == undefined){
        ALERT("لطفا یک وضعیت را باید انتخاب نمایید.","e");
        return false;
    }


    if(status == 1 || status == 2 || status == 3){
        if(description_tech == ""){
            $("#internal_Tech_DetailAjax textarea#description_tech").css("border","1px solid #ff0039");
            return false;
        }
    }
    $("input#customer_price").each(function(){
        if($(this).val() == ""){}else{
            customer_price = + Number(customer_price) + Number($(this).val().replace(/,/g, ''));
            customer_price_list.push($(this).val().replace(/,/g, ''));
        }
    });

    $("input#buy_price").each(function(){
        if($(this).val() == ""){}else {
            buy_price = + Number(buy_price) + Number($(this).val().replace(/,/g, ''));
            buy_price_list.push($(this).val().replace(/,/g, ''));
        }
    });

    $("input#piece").each(function(){
        if($(this).val() == ""){}else {
            piece_list.push($(this).val().replace(/,/g, ''));
        }
    });
    var price_customer = + Number(customer_price) + Number(hire) + Number(transportation);
    var cost_price = + Number(buy_price) + Number(discount);
    var total_invoice = + Number(price_customer) - Number(cost_price);

    var negative_invoice = 0;

    if(status == 5){

        if(total_invoice < 0 ){
            ALERT("امکان ثبت فاکتور منفی در سیستم نمی باشد","e");
            return false;
        }
        if(device_img1 == ""){
            ALERT("لطفا تصویر دستگاه را بارگذاری نمایید","e");
            return false;
        }
    }
    if(status == 5 || status == 4) {
        var price_show = $(".TotalInvoice table td #total_Company i").html()
        var message = "سود شما از این سفارش " + price_show + " تومان می باشد. آیا مطمن هستید";
        if (confirm(message)) {
            $("#internal_Tech_DetailAjax").hide();
        } else {
            return false;
        }
    }

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajaxurl,
        data: {
            'action' : 'add_status_order_internal',
            'uid' : uid,
            'status' : status,
            'description_tech' : description_tech,
            'invoice_descripotion' : invoice_descripotion,
            'cancel_desc' : cancel_desc,
            'cancel_desc_other' : cancel_desc_other,
            'customer_price' : customer_price,
            'buy_price' : buy_price,
            'piece_list' : piece_list,
            'customer_price_list' : customer_price_list,
            'buy_price_list' : buy_price_list,
            'hire' : hire,
            'transportation' : transportation,
            'discount' : discount,
            'price_customer' : price_customer,
            'cost_price' : cost_price,
            'total_invoice' : total_invoice,
            'negative_invoice' : negative_invoice,
            'device_img1' : device_img1,

        },
        success: function (data) {
            if (data.status) {
                ALERT(data.message,"s");
                $("#internal_Tech_DetailAjax .btn").val("سفارش ویرایش گردید");
                // if(status == 5){
                //     jQuery("#ّInvoiceOrderPopUp").modal("show");
                // }else{
                window.location.reload();
                // }

            }else{
                ALERT(data.message,"e");
                $("#internal_Tech_DetailAjax .btn").val("ویرایش");
            }

        },
        error: function (errorThrown) {

        }
    });
});

$("#SendImageOrder").submit(function (e) {

    e.preventDefault();
    if(ValidateForm(this.id) == false){
        return false;
    }
    $("#SendImageOrder .btn").val("در حال ارسال اطلاعات");
    var uid = $("#SendImageOrder input#uid").val();
    var image = [];
    $("input#order_images").each(function(){
        if($(this).val() == ""){}else{
            image.push($(this).val());
        }
    });


    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajaxurl,
        data: {
            'action' : 'internal_add_image',
            'uid' : uid,
            'image' : image,

        },
        success: function (data) {

            if (data.status) {

                ALERT(data.message,"s");
                $("#AddNoteForOrder .btn").val(data.message);
                window.location.reload();

            }else{
                ALERT(data.message,"e");
                $("#AddNoteForOrder .btn").val("خطا");
            }

        },
        error: function (errorThrown) {

        }
    });
});

$("#Qc_Start").submit(function (e) {

    e.preventDefault();
    if(ValidateForm(this.id) == false){
        return false;
    }

    if (confirm('آیا از تایید خود مطمن هستید؟')) {
    } else {
        return false;
    }

    $("#Qc_Start .btn").val("در حال ارسال اطلاعات");
    var uid = $("#Qc_Start input#oid").val();

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajaxurl,
        data: {
            'action' : 'qc_approve_order',
            'uid' : uid,

        },
        success: function (data) {

            if (data.status) {

                ALERT(data.message,"s");
                $("#AddNoteForOrder .btn").val(data.message);
                window.location.reload();

            }else{
                ALERT(data.message,"e");
                $("#AddNoteForOrder .btn").val("خطا");
            }

        },
        error: function (errorThrown) {

        }
    });
});

$("#send_recive_status").submit(function (e) {

    e.preventDefault();
    if(ValidateForm(this.id) == false){
        return false;
    }

    if (confirm('آیا از تایید خود مطمن هستید؟')) {
    } else {
        return false;
    }

    $("#send_recive_status .btn").val("در حال ارسال اطلاعات");
    var uid = $("#send_recive_status input#oid").val();
    var status = $("#send_recive_status input#status").val();

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajaxurl,
        data: {
            'action' : 'qc_approve_send_recive',
            'uid' : uid,
            'status' : status,

        },
        success: function (data) {

            if (data.status) {

                ALERT(data.message,"s");
                $("#send_recive_status .btn").val(data.message);
                window.location.reload();

            }else{
                ALERT(data.message,"e");
                $("#send_recive_status .btn").val("خطا");
            }

        },
        error: function (errorThrown) {

        }
    });
});


$(document).ready(function (){

    $("#internal_Tech_DetailAjax .InvoiceMaker").click(function (){
        CalcInvoice();
    });

});
function LoadCountOrderInternal(){
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajaxurl,
        data: {
            'action' : 'list_count_order_internal',
        },
        success: function (data) {
            if (data.status) {

                $(".filter li i.all_count").html(data.all_count);
                $(".filter li i.new_count").html(data.unknown_count);
                $(".filter li i.sync_count").html(data.repairing_count);
                $(".filter li i.send_count").html(data.sending_count);
                $(".filter li i.recive_count").html(data.reciving_count);
                $(".filter li i.qc_count").html(data.qc_count);
                $(".filter li i.cancel_count").html(data.cancel_count);
                $(".filter li i.down_count").html(data.down_count);
                $(".filter li i.payment_pend_count").html(data.payment_pend_count);


            }else{
            }
        },
        error: function (errorThrown) {

        }
    });
}