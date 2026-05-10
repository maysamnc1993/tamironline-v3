$("#OrderAjax").submit(function (e) {

    e.preventDefault();
    if(ValidateForm(this.id) == false){
        return false;
    }
    $("#OrderAjax .btn").val("در حال ارسال اطلاعات");
    var type_action = $("#OrderAjax input#type_action").val();
    var uid = $("#OrderAjax input#uid").val();
    var order_type = $("#OrderAjax select#order_type").val();
    var introduction = $("#OrderAjax select#introduction").val();
    var customer = $("#OrderAjax input#customer").val();
    var subscription = $("#OrderAjax input#subscription").val();
    var first_name = $("#OrderAjax input#first_name").val();
    var mobile = $("#OrderAjax input#mobile").val();
    var phone = $("#OrderAjax input#phone").val();
    var state = $("#OrderAjax select#state").val();
    var city = $("#OrderAjax select#city").val();
    var address = $("#OrderAjax textarea#address").val();
    var device = $("#OrderAjax select#device").val();
    var brand = $("#OrderAjax select#brand").val();
    var objection = $("#OrderAjax select#objection").val();
    var objection_description = $("#OrderAjax textarea#objection_description").val();

    var technician = $("#OrderAjax select#technician").val();


    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajaxurl,
        data: {
            'action' : 'order',
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

        },
        success: function (data) {

            if (data.status) {

                ALERT(data.message,"s");
                $("#OrderAjax .btn").val("سفارش اضافه گردید");
                $("body input , body select , body textarea").val("");
                window.location.href='https://crm.tamironline.com/add-order/';

            }else{
                ALERT(data.message,"e");
                $("#OrderAjax .btn").val("افزودن سفارش");
            }

        },
        error: function (errorThrown) {

        }
    });
});
$("#DetailAjax").submit(function (e) {

    e.preventDefault();
    if(ValidateForm(this.id) == false){
        return false;
    }
    $("#DetailAjax .btn").val("در حال ارسال اطلاعات");
    var uid = $("#DetailAjax input#uid").val();
    var status = $("#DetailAjax input#status").val();
    var price_return = $("#DetailAjax input#price_return").val();


    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajaxurl,
        data: {
            'action' : 'order_update',
            'uid' : uid,
            'status' : status,
            'price_return' : price_return,

        },
        success: function (data) {

            if (data.status) {

                ALERT(data.message,"s");
                $("#DetailAjax .btn").val("سفارش ویرایش گردید");

            }else{
                ALERT(data.message,"e");
                $("#DetailAjax .btn").val("ویرایش");
            }

        },
        error: function (errorThrown) {

        }
    });
});
$("#Tech_DetailAjax").submit(function (e) {
    e.preventDefault();
    if(ValidateForm(this.id) == false){
        return false;
    }
    $("#Tech_DetailAjax .btn").val("در حال ارسال اطلاعات");
    var uid = $("#Tech_DetailAjax input#uid").val();
    var status = $("#Tech_DetailAjax input#status:checked").val();
    var description_tech = $("#Tech_DetailAjax textarea#description_tech").val();
    var description_tech1 = $("#Tech_DetailAjax textarea#description_tech1").val();
    var description_tech2 = $("#Tech_DetailAjax textarea#description_tech2").val();
    var cancel_desc = $("#Tech_DetailAjax input#cancel_desc:checked").val();
    var cancel_desc_other = $("#Tech_DetailAjax textarea#cancel_desc_other").val();
    var invoice_descripotion = $("#Tech_DetailAjax textarea#invoice_descripotion").val();

    var device_img1 = $("#Tech_DetailAjax input#device_img1").val();
    var totalInvoice = $("#totalInvoice").val() ? $("#totalInvoice").val().replace(/,/g, '') : '';




    var customer_price = 0;
    var buy_price = 0;
    var piece_list = [];
    // var customer_price_list = [];
    var buy_price_list = [];
    // var hire = $(".InvoiceMaker input#hire").val().replace(/,/g, '');
    // var transportation = $(".InvoiceMaker input#transportation").val().replace(/,/g, '');
    // var discount = $(".InvoiceMaker input#discount").val().replace(/,/g, '');



    // if(status == 1 || status == 2 || status == 3){
    //     if(description_tech == ""){
    //         $("#Tech_DetailAjax textarea#description_tech").css("border","1px solid #ff0039");
    //         return false;
    //     }
    // }
    // $("input#customer_price").each(function(){
    //     if($(this).val() == ""){}else{
    //         customer_price = + Number(customer_price) + Number($(this).val().replace(/,/g, ''));
    //         customer_price_list.push($(this).val().replace(/,/g, ''));
    //     }
    // });

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
    var price_customer = totalInvoice;
    var cost_price = + Number(buy_price) ;
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
        if(invoice_descripotion == ""){
            ALERT("لطفا توضیحات را پر کنید","e");
            return false;
        }
    }
    if(status == 100){
        if(jQuery("#cancel_desc_other").val() ==''){
            jQuery("#cancel_desc_other").css("border","1px solid red");
            return false;
        }
    }
    var price_show = $(".TotalInvoice table td #total_Company i").html()
    var message = "سود شما از این سفارش " + price_show + " تومان می باشد. آیا مطمن هستید";
    if (confirm(message)) {
        $("#Tech_DetailAjax").hide();
    } else {
        return false;
    }



    $("#Tech_DetailAjax .btn").hide();

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajaxurl,
        data: {
            'action' : 'add_status_order',
            'uid' : uid,
            'status' : status,
            'description_tech' : description_tech,
            'description_tech1' : description_tech1,
            'description_tech2' : description_tech2,
            'invoice_descripotion' : invoice_descripotion,
            'cancel_desc' : cancel_desc,
            'cancel_desc_other' : cancel_desc_other,
            // 'customer_price' : customer_price,
            'buy_price' : buy_price,
            'piece_list' : piece_list,
            // 'customer_price_list' : customer_price_list,
            'buy_price_list' : buy_price_list,
            // 'hire' : hire,
            // 'transportation' : transportation,
            // 'discount' : discount,
            'price_customer' : price_customer,
            'cost_price' : cost_price,
            'total_invoice' : total_invoice,
            'negative_invoice' : negative_invoice,
            'device_img1' : device_img1,

        },
        success: function (data) {
            if (data.status) {
                ALERT(data.message,"s");
                $("#Tech_DetailAjax .btn").val("سفارش ویرایش گردید");
                if(status == 5){
                    // jQuery("#ّInvoiceOrderPopUp").modal("show");
                    window.location.reload();
                }else{
                    window.location.reload();
                }

            }else{
                ALERT(data.message,"e");
                $("#Tech_DetailAjax .btn").show();
                $("#Tech_DetailAjax .btn").val("ویرایش");
            }

        },
        error: function (errorThrown) {

        }
    });
});
$("#request_invoice").submit(function (e) {

    e.preventDefault();
    if(ValidateForm(this.id) == false){
        return false;
    }
    $("#request_invoice .btn").val("در حال ارسال اطلاعات");
    var uid = $("#Tech_DetailAjax input#uid").val();
    var Have_invoice = $("#request_invoice .Have_invoice li input:checked").val();
    var Type_Of_send_invoice = $("#request_invoice .next_step .Type_Of_send_invoice li input:checked").val();
    var invoice_email = $("#request_invoice #invoice_email").val();
    var invoice_paper = $("#request_invoice #invoice_paper").val();
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajaxurl,
        data: {
            'action' : 'request_invoice',
            'uid' : uid,
            'Have_invoice' : Have_invoice,
            'Type_Of_send_invoice' : Type_Of_send_invoice,
            'invoice_email' : invoice_email,
            'invoice_paper' : invoice_paper,
        },
        success: function (data) {

            if (data.status) {
                ALERT(data.message,"s");
                $("#request_invoice .btn").val("ارسال شد.");
                window.location.reload();

            }else{
                ALERT(data.message,"e");
                $("#request_invoice .btn").val("ارسال");
            }

        },
        error: function (errorThrown) {

        }
    });
});
$("#ReturnOrderAjax").submit(function (e) {

    e.preventDefault();
    if(ValidateForm(this.id) == false){
        return false;
    }
    $("#ReturnOrderAjax .btn").val("در حال ارسال اطلاعات");
    var uid = $("#ReturnOrderAjax input#uid").val();
    var return_type = $("#ReturnOrderAjax select#return_type").val();
    var return_description = $("#ReturnOrderAjax textarea#return_description").val();

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajaxurl,
        data: {
            'action' : 'return_order',
            'uid' : uid,
            'return_type' : return_type,
            'return_description' : return_description,

        },
        success: function (data) {

            if (data.status) {

                ALERT(data.message,"s");
                $("#DetailAjax .btn").val("وضعیت سفارش تغییر کرد");
                window.location.reload();

            }else{
                ALERT(data.message,"e");
                $("#DetailAjax .btn").val("بازگشت سفارش");
            }

        },
        error: function (errorThrown) {

        }
    });
});
$(".messageCRM.msg #AddMessageForOrder").submit(function (e) {

    e.preventDefault();
    if(ValidateForm(this.id) == false){
        return false;
    }
    $("#AddMessageForOrder .btn").val("در حال ارسال اطلاعات");
    var uid = $("#AddMessageForOrder input#uid").val();
    var descriptionMSG = $("#AddMessageForOrder textarea#descriptionMSG").val();

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajaxurl,
        data: {
            'action' : 'add_message_order',
            'uid' : uid,
            'descriptionMSG' : descriptionMSG,

        },
        success: function (data) {

            if (data.status) {

                ALERT(data.message,"s");
                $("#AddMessageForOrder .btn").val("پیام شما ارسال گردید");
                window.location.reload();

            }else{
                ALERT(data.message,"e");
                $("#DetailAjax .btn").val("خطا");
            }

        },
        error: function (errorThrown) {

        }
    });
});
$("#AllowNagetiveInvoice").submit(function (e) {

    e.preventDefault();
    if(ValidateForm(this.id) == false){
        return false;
    }
    $("#AllowNagetiveInvoice .btn").val("در حال ارسال اطلاعات");
    var uid = $("#AllowNagetiveInvoice input#uid").val();
    var allow_invoice = $("#AllowNagetiveInvoice input#allow_invoice:checked").val();

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajaxurl,
        data: {
            'action' : 'AllowNagetiveInvoice',
            'uid' : uid,
            'allow_invoice' : allow_invoice,

        },
        success: function (data) {

            if (data.status) {

                ALERT(data.message,"s");
                $("#AllowNagetiveInvoice .btn").val("وضعیت فاکتور منفی ارسال گردید");
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
$(".messageCRM.note #AddNoteForOrder").submit(function (e) {

    e.preventDefault();
    if(ValidateForm(this.id) == false){
        return false;
    }
    $("#AddNoteForOrder .btn").val("در حال ارسال اطلاعات");
    var uid = $("#AddNoteForOrder input#uid").val();
    var descriptionMSG = $("#AddNoteForOrder textarea#descriptionMSG").val();

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajaxurl,
        data: {
            'action' : 'add_note_order',
            'uid' : uid,
            'descriptionMSG' : descriptionMSG,

        },
        success: function (data) {

            if (data.status) {

                ALERT(data.message,"s");
                $("#AddNoteForOrder .btn").val("پیام شما ارسال گردید");
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
// function CalcInvoice(){
//
//     var customer_price = 0;
//     var buy_price = 0;
//     var percent_operator_number = $("input#percent_operator_number").val();
//     var hire = $(".InvoiceMaker input#hire").val().replace(/,/g, '');
//     var transportation = $(".InvoiceMaker input#transportation").val().replace(/,/g, '');
//     var discount = $(".InvoiceMaker input#discount").val().replace(/,/g, '');
//     $("input#customer_price").each(function(){
//         if($(this).val() == ""){}else{
//         customer_price = + Number(customer_price) + Number($(this).val().replace(/,/g, ''));
//         }
//     });
//
//     $("input#buy_price").each(function(){
//         if($(this).val() == ""){}else {
//             buy_price = + Number(buy_price) + Number($(this).val().replace(/,/g, ''));
//         }
//     });
//     var price_customer = + Number(customer_price) + Number(hire) + Number(transportation);
//     var cost_price = + Number(buy_price) + Number(discount);
//     var total_invlice = + Number(price_customer) - Number(cost_price);
//     var co_total_invoice = (Number(total_invlice) / 100) * Number(percent_operator_number);
//
//
//     $("#total_invoice i").html(price_customer.toLocaleString());
//     $("#total_cost i").html(cost_price.toLocaleString());
//     $("#total_left i").html(total_invlice.toLocaleString());
//     $("#total_Company i").html(co_total_invoice.toLocaleString());
//
//
// }



function CalcInvoice(){

    // var customer_price = 0;
    var total_price = $("input#totalInvoice").val().replace(/,/g, '');
    var buy_price = 0;
    var percent_operator_number = $("input#percent_operator_number").val();
    var type_of_calc_tech = $("input#type_of_calc_tech").val();
    var tech_per_of_all = $("input#tech_per_of_all").val();
    if(jQuery(".cancel_Btn.TransferBTN").hasClass("active")){
     percent_operator_number = 100;   
    }
    
    // var hire = $(".InvoiceMaker input#hire").val().replace(/,/g, '');
    // var transportation = $(".InvoiceMaker input#transportation").val().replace(/,/g, '');
    // var discount = $(".InvoiceMaker input#discount").val().replace(/,/g, '');


    // $("input#customer_price").each(function(){
    //     if($(this).val() == ""){}else{
    //         customer_price = + Number(customer_price) + Number($(this).val().replace(/,/g, ''));
    //     }
    // });

    $("input#buy_price").each(function(){
        if($(this).val() == ""){}else {
            buy_price = + Number(buy_price) + Number($(this).val().replace(/,/g, ''));
        }
    });

    if(type_of_calc_tech == 1){
        var tech_per = Number(1) - (Number(tech_per_of_all) / 100);
        console.log(tech_per);
        var price_customer = total_price;
        var cost_price =  Number(buy_price);
        var total_invlice = + Number(price_customer);
        var co_total_invoice = Number(total_invlice) * Number(tech_per);
    }else{
    var price_customer = total_price;
    var cost_price =  Number(buy_price);
    var total_invlice = + Number(price_customer) - Number(cost_price);
    var co_total_invoice = (Number(total_invlice) / 100) * Number(percent_operator_number);
    }

    $("#total_invoice i").html(price_customer.toLocaleString());
    $("#total_cost i").html(cost_price.toLocaleString());
    $("#total_left i").html(total_invlice.toLocaleString());
    $("#total_Company i").html(co_total_invoice.toLocaleString());


}



$(document).ready(function (){

    $(".messageCRM.msg .CrmBTnMessage").click(function (){
        $(".messageCRM.msg .CrmListMessage").toggleClass("active");
        $(".main .content").toggleClass("blur");
    });
    $(".messageCRM.msg #allMessage").click(function (e){
        e.preventDefault();
        $(".messageCRM.msg .CrmListMessage").removeClass("active");
        $(".main .content").removeClass("blur");
        window.location.href = "#messageListOrder";
    })

    $(".messageCRM.note .CrmBTnMessage").click(function (){
        $(".messageCRM.note .CrmListMessage").toggleClass("active");
        $(".main .content").toggleClass("blur");
    });
    $(".messageCRM.note #allMessage").click(function (e){
        e.preventDefault();
        $(".messageCRM.note .CrmListMessage").removeClass("active");
        $(".main .content").removeClass("blur");
        window.location.href = "#noteListOrder";
    })

    $("#request_invoice .Have_invoice li").click(function (){
        var id = this.id;
        $("#request_invoice .Have_invoice li").removeClass("active");
        $(this).addClass("active");
        if(id == 1){
            $("#request_invoice .next_step").addClass("active");
            $(".next_step_two .form-group").addClass("active");
        }
        else{
            $("#request_invoice .next_step").removeClass("active");
            $(".next_step_two .form-group").removeClass("active");
        }
    });


    $("#request_invoice .next_step .Type_Of_send_invoice li").click(function (){
        var id = this.id;
        $("#request_invoice .next_step .Type_Of_send_invoice li").removeClass("active");
        $(this).addClass("active");
        $(".next_step_two .form-group").removeClass("active");
        $(".next_step_two .form-group." + id).addClass("active");
    });
    $(".SearchBox .titleBox").click(function (){
        $(".SearchBox #SearchList").toggleClass("active");
    });


    $('#date_year').persianDatepicker({
        observer: true,
        initialValue: false,
        format: 'YYYY/MM/DD',
        altField: '#timestamp'
    });
    $('#date_year_end').persianDatepicker({
        observer: true,
        initialValue: false,
        format: 'YYYY/MM/DD',
        altField: '#timestamp_end'
    });
    $('#date_year_end_order').persianDatepicker({
        observer: true,
        initialValue: false,
        format: 'YYYY/MM/DD',
        altField: '#timestamp_end_order'
    });

})
$(document).ready(function (){

    jQuery("#AllowNagetiveInvoice .selectStatusInput li label input").click(function (){
        jQuery("#AllowNagetiveInvoice .selectStatusInput li label").removeClass("active");
        $(this).parent("label").addClass("active");
    })

    jQuery("select#technician.old").change(function (){

        var max_order = $(this).find(':selected').attr('data-max-order');
        var max_price = $(this).find(':selected').attr('data-max-price');
        var now_order = $(this).find(':selected').attr('data-now-order');
        var now_debt = $(this).find(':selected').attr('data-now-debt');
        var error = $(this).find(':selected').attr('data-error');

        $(".dataOFTechnician table td.order_limit").html(max_order);
        $(".dataOFTechnician table td.debt_limit").html(max_price + "تومان");

        $(".dataOFTechnician table td.order_exists").html(now_order);
        $(".dataOFTechnician table td.debt_exists").html(now_debt + "تومان");
        if(error == 1){
            $(".dataOFTechnician").addClass("error");
            $("input.btn").hide();
            $(".FourceChoseTec").addClass("active");
        }else{
            $(".dataOFTechnician").removeClass("error");
            $("input.btn").show();
            $(".FourceChoseTec").removeClass("active");
        }
    });

    jQuery("select#technician.new_tech").change(function (){
        ALERT("در حال دریافت اطلاعات تکنسین","W")
        var uid = jQuery(this).val();
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: ajaxurl,
            data: {
                'action' : 'ReturnTechDetail',
                'uid' : uid,
            },
            success: function (data) {

                if (data.status) {

                    $(".dataOFTechnician table td.order_limit").html(data.max_order);
                    $(".dataOFTechnician table td.debt_limit").html(data.max_price + "تومان");

                    $(".dataOFTechnician table td.order_exists").html(data.order);
                    $(".dataOFTechnician table td.debt_exists").html(data.debt + "تومان");

                    $(".dataOFTechnician").removeClass("error");
                    $("input.btn").show();
                    $(".FourceChoseTec").removeClass("active");

                }else{
                    ALERT("تکنین بیش از حد مجاز سفارش یا بدهی دارد.","e")
                    $(".dataOFTechnician table td.order_limit").html(data.max_order);
                    $(".dataOFTechnician table td.debt_limit").html(data.max_price + "تومان");

                    $(".dataOFTechnician table td.order_exists").html(data.order);
                    $(".dataOFTechnician table td.debt_exists").html(data.debt + "تومان");

                    $(".dataOFTechnician").addClass("error");
                    $("input.btn").hide();
                    $(".FourceChoseTec").addClass("active");

                }

            },
            error: function (errorThrown) {

            }
        });


    });

    $(".FourceChoseTec").click(function (){
        $("input.btn").show();
        $(".FourceChoseTec").removeClass("active");

    });
    $(".returnOrderStatus .returnBTN").click(function (){
        $(".returnOrderStatus").toggleClass("active");
    })
    $("#Tech_DetailAjax .InvoiceMaker").click(function (){
        CalcInvoice();
    });

    $("#state").select2( {
        placeholder: "استان را انتخاب کنید",
        allowClear: true
    } );
    $("#shipping").select2( {
        placeholder: "استان را انتخاب کنید",
        allowClear: true
    } );
    $("#status_internal_order").select2( {
        placeholder: "استان را انتخاب کنید",
        allowClear: true
    } );
    $("#city").select2( {
        placeholder: "منطقه را انتخاب کنید",
        allowClear: true
    } );

    $("#brand").select2( {
        placeholder: "برند را انتخاب کنید",
        allowClear: true
    } );
    $("#device").select2( {
        placeholder: "دستگاه را انتخاب کنید",
        allowClear: true
    } );
    $("#objection").select2( {
        placeholder: "ایراد را انتخاب کنید",
        allowClear: true
    } );
    $("#technician").select2( {
        placeholder: "تکنسین را انتخاب کنید",
        allowClear: true
    } );
    $("#technician_id").select2( {
        placeholder: "تکنسین را انتخاب کنید",
        allowClear: true
    } );
    $("#introduction").select2( {
        placeholder: "نحوه آشنایی را انتخاب کنید",
        allowClear: true
    } );

    $("#operatorSearch").select2( {
        placeholder: "اپراتور را انتخاب کنید",
        allowClear: true
    } );


    $("#Searchdevice").select2( {
        placeholder: "دستگاه را انتخاب کنید",
        allowClear: true
    } );
    $("#search_brand").select2( {
        placeholder: "برند را انتخاب کنید",
        allowClear: true
    } );
    $("#technicianSearch").select2( {
        placeholder: "تکنسین را انتخاب کنید",
        allowClear: true
    } );
    $("#search_state").select2( {
        placeholder: "استان را انتخاب کنید",
        allowClear: true
    } );

    $("#search_city").select2( {
        placeholder: "شهر را انتخاب کنید",
        allowClear: true
    } );

    $("#CustomerStatus").select2( {
        placeholder: "وضعیت  را انتخاب کنید",
        allowClear: true
    } );
    $("#Search_objection").select2( {
        placeholder: "ایراد  را انتخاب کنید",
        allowClear: true
    } );
    $("#Search_intro").select2( {
        placeholder: "ایراد  را انتخاب کنید",
        allowClear: true
    } );
    $("#order_type").select2( {
        placeholder: "سفارش را انتخاب کنید",
        allowClear: true
    } );

});
jQuery(document).ready(function (){

    jQuery("#subscription").on("keyup",function (){

        var val = jQuery(this).val();
        LoadCustomerBy("subscription",val);

    });
    jQuery("#mobile").on("keyup",function (){
        var val = jQuery(this).val();
        if(val.length > 10){
            LoadCustomerBy("mobile",val);
        }
    });

    $(".Show_Order.table td a#CallLog").click(function (e){
        e.preventDefault();
        var number = $(this).attr("href");
        var uid = $(this).attr("data-uid");
        numberLog = number.replace("tel:","");
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: ajaxurl,
            data: {
                'action' : 'CallLog',
                'number' : numberLog,
                'uid' : uid,
            },
            success: function (data) {
                if (data.status) {
                    window.location.href = number;

                }else{
                }
            },
            error: function (errorThrown) {

            }
        });
    })



    $("#PopUpCustomerList").on("click"," .customerItem .SelectCustomerItem",function (){

        var id = this.id;
        var subscription = $(this).attr("data-subscription");
        var first_name = $(this).attr("data-first-name");
        var mobile = $(this).attr("data-mobile");
        var phone = $(this).attr("data-phone");

        var select_date = $("#PopUpCustomerList #SelectCustumerTechnician:checked");
        var state = select_date.attr("data-state");
        var city = select_date.attr("data-city");
        var address = select_date.attr("data-address");
        var device = select_date.attr("data-device");
        var brand = select_date.attr("data-brand");
        var objection = select_date.attr("data-objection");
        var objection_description = select_date.attr("data-objection_description");

        $("#subscription").val(subscription);
        $("#first_name").val(first_name);
        $("#mobile").val(mobile);
        $("#phone").val(phone);
        $("#customer").val(id);
        $("#state").val(state);
        $("#city").select2().val(city).trigger("change");
        $("#address").val(address);
        $("#device").select2().val(device).trigger("change");
        $("#brand").select2().val(brand).trigger("change");
        $("#objection").select2().val(objection).trigger("change");
        $("#objection_description").val(objection_description);
        $("#PopUpCustomerList").modal("hide");


    });

    $("#PopUpCustomerList").on("click"," .customerItem .list_of_order_customer h3",function (){
        $(this).parent(".list_of_order_customer").toggleClass("active");
    })

});
function LoadCustomerBy(key,value){

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajaxurl,
        data: {
            'action' : 'SearchCustomer',
            'key' : key,
            'value' : value,
        },
        success: function (data) {
            if (data.status) {
                $("#PopUpCustomerList").modal("show");
                $("#PopUpCustomerList .List_of_customer").html(data.return);

            }else{
            }
        },
        error: function (errorThrown) {

        }
    });

}

$("form#SaveToShaygan").submit(function (e){
    e.preventDefault();

    var oid = $(".AddTOShaygan #SaveToShaygan #oid").val()
    var SaveToShayganStatus = $(".AddTOShaygan #SaveToShaygan #SaveToShayganStatus").val()

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajaxurl,
        data: {
            'action' : 'saveShaygan',
            'oid' : oid,
            'SaveToShayganStatus' : SaveToShayganStatus,
        },
        success: function (data) {

            if (data.status) {
                ALERT(data.message,"s");
                $("#request_invoice .btn").val("با موفقیت تغییر ایجاد گردید.");
                window.location.reload();
            }else{
                ALERT(data.message,"e");
                $("#request_invoice .btn").val("ارسال");
            }

        },
        error: function (errorThrown) {

        }
    });
});
$("#device_image_ajax").submit(function (e) {
    e.preventDefault();
    if(ValidateForm(this.id) == false){
        return false;
    }
    $("#device_image_ajax .btn").val("در حال ارسال اطلاعات");

    var uid = $("#device_image_ajax input#uid_order").val();
    var device_image_input = $("#device_image_ajax input#device_image_input").val();

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajaxurl,
        data: {
            'action' : 'upload_device_image',
            'uid' : uid,
            'device_image_input' : device_image_input,

        },
        success: function (data) {

            if (data.status) {

                ALERT(data.message,"s");
                $("#device_image_ajax .btn").val("تصویر دستگاه با موفقیت بارگذاری شد.");
                window.location.reload();

            }else{
                ALERT(data.message,"e");
                $("#device_image_ajax .btn").val("خطا");
            }

        },
        error: function (errorThrown) {

        }
    });
});

function LoadCountOrder(){
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajaxurl,
        data: {
            'action' : 'list_count_order',
        },
        success: function (data) {
            if (data.status) {

                $(".filter li i.all_count").html(data.all_count);
                $(".filter li i.new_count").html(data.new_count);
                $(".filter li i.decline_count").html(data.decline_count);
                $(".filter li i.sync_count").html(data.sync_count);
                $(".filter li i.open_count").html(data.open_count);
                $(".filter li i.notech_count").html(data.notech_count);
                $(".filter li i.pend_count").html(data.pend_count);
                $(".filter li i.cancel_count").html(data.cancel_count);
                $(".filter li i.down_count").html(data.down_count);
                $(".filter li i.back_count").html(data.back_count);
                $(".filter li i.archive").html(data.archive);
                $(".filter li i.unarchive").html(data.unarchive);
                $(".filter li i.back_open_count").html(data.back_open);
                $(".filter li i.back_count").html(data.back_count);

            }else{
            }
        },
        error: function (errorThrown) {

        }
    });
}

$("#check_register_user").submit(function (e) {
    e.preventDefault();
    if (ValidateForm(this.id) == false) {
        return false;
    }
    $("#check_register_user .btn").val("در حال ارسال اطلاعات");
    var verify_code = $("#check_register_user input#verify_code").val();
    var old_verify_code = $("#check_register_user input#old_verify_code").val();
    if(verify_code == old_verify_code){
        $(".User_Register_show_invoice").hide();
    }else{
        $("#check_register_user .btn").val("خطا! کد وارد شده اشتباه می باشد.");
    }
});
function LIst_OF_Order_user(){
    var user_id = $(".LIst_OF_Order_user").attr("id");
    var data_order_type = $(".LIst_OF_Order_user").attr("data-order-type");
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajaxurl,
        data: {
            'action' : 'LIst_OF_Order_user',
            'user_id' : user_id,
            'data_order_type' : data_order_type,
        },
        success: function (data) {
            if (data.status) {
                $(".LIst_OF_Order_user i").html(data.counts);

            }else{
            }
        },
        error: function (errorThrown) {
        }
    });
}
$(".LIst_OF_Order_user").click(function (){
    ALERT("در حال بارگذاری سفارشات","s");
    var user_id = $(this).attr("data-user-mobile");
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajaxurl,
        data: {
            'action' : 'SearchCustomer',
            'key' : "subscription",
            'value' : user_id,
        },
        success: function (data) {
            if (data.status) {
                $("#PopUpCustomerList").modal("show");
                $("#PopUpCustomerList .List_of_customer").html(data.return);

            }else{
            }
        },
        error: function (errorThrown) {
        }
    });
})

$("form#OrderHappyCallCustomerAjax").submit(function (e){
    e.preventDefault();
    var order_id = $("form#OrderHappyCallCustomerAjax input#order_id").val();
    var hc_value = [];
    var hc_key = [];

    $("form#OrderHappyCallCustomerAjax input#Order_hc_customer_point").each(function(){
        if($(this).val() == ""){
            ALERT("لطفا تمام امتیازات را وارد نمایید.","e");
            return false;
        }else {
            hc_value.push($(this).val());
        }
    });
    $("form#OrderHappyCallCustomerAjax input#order_hc_customer_key").each(function(){
        if($(this).val() == ""){}else {
            hc_key.push($(this).val());
        }
    });

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajaxurl,
        data: {
            'action' : 'SetCustomerHappyCall',
            'order_id' : order_id,
            'hc_key' : hc_key,
            'hc_value' : hc_value,
        },
        success: function (data) {
            if (data.status) {
                ALERT(data.message,"s");
                $("#OrderHappyCallCustomerAjax .btn").val("هپی کال با موفقیت انجام شد");
                window.location.reload();
            }else{
                ALERT(data.message,"e");
                $("#OrderHappyCallCustomerAjax .btn").val("خطا");
            }
        },
        error: function (errorThrown) {

        }
    });

});

$("form#OrderHappyCallTechAjax").submit(function (e){
    e.preventDefault();
    var order_id = $("form#OrderHappyCallTechAjax input#order_id").val();
    var hc_value = [];
    var hc_key = [];

    $("form#OrderHappyCallTechAjax input#Order_hc_tech_point").each(function(){
        if($(this).val() == ""){
            ALERT("لطفا تمام امتیازات را وارد نمایید.","e");
            return false;
        }else {
            hc_value.push($(this).val());
        }
    });
    $("form#OrderHappyCallTechAjax input#order_hc_tech_key").each(function(){
        if($(this).val() == ""){}else {
            hc_key.push($(this).val());
        }
    });

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajaxurl,
        data: {
            'action' : 'SetTechHappyCall',
            'order_id' : order_id,
            'hc_key' : hc_key,
            'hc_value' : hc_value,
        },
        success: function (data) {
            if (data.status) {
                ALERT(data.message,"s");
                $("#OrderHappyCallTechAjax .btn").val("هپی کال با موفقیت انجام شد");
                window.location.reload();
            }else{
                ALERT(data.message,"e");
                $("#OrderHappyCallTechAjax .btn").val("خطا");
            }
        },
        error: function (errorThrown) {

        }
    });

});

$("form#SelectTypeUserFOrmAJax").submit(function (e){
    e.preventDefault();

    var uid = $("#SelectTypeUserFOrmAJax input#uid").val();
    var selectTypeUsers = $("#SelectTypeUserFOrmAJax input#selectTypeUsers:checked").val();

    if(selectTypeUsers === undefined){
        ALERT("لطفا نوع مشتری را انتخاب نمایید.","e");
        return false;
    }

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajaxurl,
        data: {
            'action' : 'SetTypeCustomer',
            'uid' : uid,
            'selectTypeUsers' : selectTypeUsers,
        },
        success: function (data) {
            if (data.status) {
                ALERT(data.message,"s");
                $("#SelectTypeUserFOrmAJax .btn").val(" انتخاب نوع مشتری با موفقیت انجام شد");
                window.location.reload();
            }else{
                ALERT(data.message,"e");
                $("#OrderHappyCallTechAjax .btn").val("خطا");
            }
        },
        error: function (errorThrown) {

        }
    });

});


jQuery(".saveAsDraft").click(function (){
    if (confirm('آیا مطمن هستید فقط میخواهید این سفارش ذخیره شود و وضعیت آن تغییر نکند؟')) {
    } else {
        return false;
    }


    var uid = $("#Tech_DetailAjax input#uid").val();
    var status = $("#Tech_DetailAjax input#status:checked").val();
    var description_tech = $("#Tech_DetailAjax textarea#description_tech").val();
    var description_tech1 = $("#Tech_DetailAjax textarea#description_tech1").val();
    var description_tech2 = $("#Tech_DetailAjax textarea#description_tech2").val();
    var cancel_desc = $("#Tech_DetailAjax input#cancel_desc:checked").val();
    var cancel_desc_other = $("#Tech_DetailAjax textarea#cancel_desc_other").val();
    var invoice_descripotion = $("#Tech_DetailAjax textarea#invoice_descripotion").val();

    var device_img1 = $("#Tech_DetailAjax input#device_img1").val();
    var totalInvoice = $("#totalInvoice").val() ? $("#totalInvoice").val().replace(/,/g, '') : '';
    var customer_price = 0;
    var buy_price = 0;
    var piece_list = [];
    var buy_price_list = [];

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
    var price_customer = totalInvoice;
    var cost_price = + Number(buy_price) ;
    var total_invoice = + Number(price_customer) - Number(cost_price);
    var negative_invoice = 0;
    var price_show = $(".TotalInvoice table td #total_Company i").html()

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajaxurl,
        data: {
            'action' : 'Save_as_Draft_order',
            'uid' : uid,
            'invoice_descripotion' : invoice_descripotion,
            'buy_price' : buy_price,
            'piece_list' : piece_list,
            'buy_price_list' : buy_price_list,
            'price_customer' : price_customer,
            'cost_price' : cost_price,
            'total_invoice' : total_invoice,
            'negative_invoice' : negative_invoice,
            'device_img1' : device_img1,

        },
        success: function (data) {
            if (data.status) {
                ALERT(data.message,"s");
                window.location.reload();

            }else{
                ALERT(data.message,"e");
            }

        },
        error: function (errorThrown) {

        }
    });


})


jQuery(".SendSMSForDeliverCustomer").click(function (){
    if (confirm('آیا از ارسال پیامک مطمئن هستید؟')) {
    } else {
        return false;
    }
    var id = this.id;
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajaxurl,
        data: {
            'action' : 'send_sms_delivery_to_customer',
            'id' : id,


        },
        success: function (data) {
            if (data.status) {
                ALERT(data.message,"s");
                // window.location.reload();
            }else{
                ALERT(data.message,"e");
            }

        },
        error: function (errorThrown) {

        }
    });
})
jQuery("window").on("load",function(){
    jQuery("#totalInvoice").keyup();
})

jQuery(".show_report_dec").click(function (){
    jQuery("#show_decline_report").modal("show");
});