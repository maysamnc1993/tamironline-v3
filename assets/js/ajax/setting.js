$("#SettingAjax").submit(function (e) {

    e.preventDefault();
    if(ValidateForm(this.id) == false){
        return false;
    }
    $("#SettingAjax .btn").val("در حال ارسال اطلاعات");
    var token = $("#SettingAjax input#token").val();
    var customer_order = $("#SettingAjax input#customer_order").val();
    var technician_order = $("#SettingAjax input#technician_order").val();
    var customer_cancel_order = $("#SettingAjax input#customer_cancel_order").val();
    var tech_wallet_charge = $("#SettingAjax input#tech_wallet_charge").val();
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajaxurl,
        data: {
            'action' : 'settingSMS',
            'token' : token,
            'customer_order' : customer_order,
            'technician_order' : technician_order,
            'customer_cancel_order' : customer_cancel_order,
            'tech_wallet_charge' : tech_wallet_charge,
        },
        success: function (data) {

            if (data.status) {

                ALERT(data.message,"s");
                $("#SettingAjax .btn").val("تنظیمات اعمال گردید");

            }else{
                ALERT(data.message,"e");
                $("#SettingAjax .btn").val("ارسال");
            }

        },
        error: function (errorThrown) {

        }
    });
});
$("#SettingAjaxPayment").submit(function (e) {

    e.preventDefault();
    if(ValidateForm(this.id) == false){
        return false;
    }
    $("#SettingAjaxPayment .btn").val("در حال ارسال اطلاعات");
    var type_of_payment1 = $("#SettingAjaxPayment select#type_of_payment1").val();
    var token1 = $("#SettingAjaxPayment input#token1").val();
    var max_pay1 = $("#SettingAjaxPayment input#max_pay1").val();

    var type_of_payment2 = $("#SettingAjaxPayment select#type_of_payment2").val();
    var token2 = $("#SettingAjaxPayment input#token2").val();
    var max_pay2 = $("#SettingAjaxPayment input#max_pay2").val();

    var type_of_payment3 = $("#SettingAjaxPayment select#type_of_payment3").val();
    var token3 = $("#SettingAjaxPayment input#token3").val();
    var max_pay3 = $("#SettingAjaxPayment input#max_pay3").val();

    var wallet1 = $("#SettingAjaxPayment input#wallet1").val();
    var wallet1_charge = $("#SettingAjaxPayment input#wallet1_charge").val();

    var wallet2 = $("#SettingAjaxPayment input#wallet2").val();
    var wallet2_charge = $("#SettingAjaxPayment input#wallet2_charge").val();

    var wallet3= $("#SettingAjaxPayment input#wallet3").val();
    var wallet3_charge = $("#SettingAjaxPayment input#wallet3_charge").val();

    var wallet4 = $("#SettingAjaxPayment input#wallet4").val();
    var wallet4_charge = $("#SettingAjaxPayment input#wallet4_charge").val();

    var wallet5 = $("#SettingAjaxPayment input#wallet5").val();
    var wallet5_charge = $("#SettingAjaxPayment input#wallet5_charge").val();

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajaxurl,
        data: {
            'action' : 'settingPayment',
            'type_of_payment1' : type_of_payment1,
            'token1' : token1,
            'max_pay1' : max_pay1,

            'type_of_payment2' : type_of_payment3,
            'token2' : token3,
            'max_pay2' : max_pay3,

            'type_of_payment3' : type_of_payment3,
            'token3' : token3,
            'max_pay3' : max_pay3,

            'wallet1' : wallet1,
            'wallet1_charge' : wallet1_charge,

            'wallet2' : wallet2,
            'wallet2_charge' : wallet2_charge,

            'wallet2' : wallet2,
            'wallet2_charge' : wallet2_charge,

            'wallet3' : wallet3,
            'wallet3_charge' : wallet3_charge,

            'wallet4' : wallet4,
            'wallet4_charge' : wallet4_charge,

            'wallet5' : wallet5,
            'wallet5_charge' : wallet5_charge,

        },
        success: function (data) {

            if (data.status) {

                ALERT(data.message,"s");
                $("#SettingAjaxPayment .btn").val("تنظیمات اعمال گردید");

            }else{
                ALERT(data.message,"e");
                $("#SettingAjaxPayment .btn").val("ارسال");
            }

        },
        error: function (errorThrown) {

        }
    });
});

$("#ObjectionAjax").submit(function (e) {

    e.preventDefault();
    if(ValidateForm(this.id) == false){
        return false;
    }
    $("#ObjectionAjax .btn").val("در حال ارسال اطلاعات");
    var objectionsList = [];
    $("#ObjectionAjax input#objectionsList").each(function(){
        if($(this).val() == ""){}else {
            objectionsList.push($(this).val());
        }
    });

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajaxurl,
        data: {
            'action' : 'objectionsList',
            'objectionsList' : objectionsList,

        },
        success: function (data) {

            if (data.status) {

                ALERT(data.message,"s");
                $("#ObjectionAjax .btn").val("تنظیمات اعمال گردید");
                window.location.reload();

            }else{
                ALERT(data.message,"e");
                $("#ObjectionAjax .btn").val("ارسال");
            }

        },
        error: function (errorThrown) {

        }
    });
});

$("#ShippingAjax").submit(function (e) {

    e.preventDefault();
    if(ValidateForm(this.id) == false){
        return false;
    }
    $("#ShippingAjax .btn").val("در حال ارسال اطلاعات");
    var objectionsList = [];
    $("#ShippingAjax input#objectionsList").each(function(){
        if($(this).val() == ""){}else {
            objectionsList.push($(this).val());
        }
    });

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajaxurl,
        data: {
            'action' : 'ShippingSetting',
            'objectionsList' : objectionsList,

        },
        success: function (data) {

            if (data.status) {

                ALERT(data.message,"s");
                $("#ObjectionAjax .btn").val("تنظیمات اعمال گردید");
                window.location.reload();

            }else{
                ALERT(data.message,"e");
                $("#ObjectionAjax .btn").val("ارسال");
            }

        },
        error: function (errorThrown) {

        }
    });
});

$("#introductionAjax").submit(function (e) {

    e.preventDefault();
    if(ValidateForm(this.id) == false){
        return false;
    }
    $("#introductionAjax .btn").val("در حال ارسال اطلاعات");
    var objectionsList = [];
    $("#introductionAjax input#objectionsList").each(function(){
        if($(this).val() == ""){}else {
            objectionsList.push($(this).val());
        }
    });

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajaxurl,
        data: {
            'action' : 'introductionList',
            'objectionsList' : objectionsList,

        },
        success: function (data) {

            if (data.status) {

                ALERT(data.message,"s");
                $("#ObjectionAjax .btn").val("تنظیمات اعمال گردید");
                window.location.reload();

            }else{
                ALERT(data.message,"e");
                $("#ObjectionAjax .btn").val("ارسال");
            }

        },
        error: function (errorThrown) {

        }
    });
});

$("#CostSettingAjax").submit(function (e) {

    e.preventDefault();
    if(ValidateForm(this.id) == false){
        return false;
    }
    $("#CostSettingAjax .btn").val("در حال ارسال اطلاعات");
    var objectionsList = [];
    $("#CostSettingAjax input#objectionsList").each(function(){
        if($(this).val() == ""){}else {
            objectionsList.push($(this).val());
        }
    });

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajaxurl,
        data: {
            'action' : 'cost_setting',
            'objectionsList' : objectionsList,

        },
        success: function (data) {

            if (data.status) {

                ALERT(data.message,"s");
                $("#ObjectionAjax .btn").val("تنظیمات اعمال گردید");
                window.location.reload();

            }else{
                ALERT(data.message,"e");
                $("#ObjectionAjax .btn").val("ارسال");
            }

        },
        error: function (errorThrown) {

        }
    });
});

$("#SettingAjaxCancel").submit(function (e) {

    e.preventDefault();
    if(ValidateForm(this.id) == false){
        return false;
    }

    $("#SettingAjaxCancel .btn").val("در حال ارسال اطلاعات");
    var cancle_desc_1 = $("#SettingAjaxCancel #cancle_desc_1").val();
    var cancle_desc_2 = $("#SettingAjaxCancel #cancle_desc_2").val();
    var cancle_desc_3 = $("#SettingAjaxCancel #cancle_desc_3").val();
    var cancle_desc_4 = $("#SettingAjaxCancel #cancle_desc_4").val();
    var cancle_desc_5 = $("#SettingAjaxCancel #cancle_desc_5").val();
    var cancle_desc_6 = $("#SettingAjaxCancel #cancle_desc_6").val();
    var cancle_desc_7 = $("#SettingAjaxCancel #cancle_desc_7").val();
    var cancle_desc_8 = $("#SettingAjaxCancel #cancle_desc_8").val();
    var cancle_desc_9 = $("#SettingAjaxCancel #cancle_desc_9").val();
    var cancle_desc_10 = $("#SettingAjaxCancel #cancle_desc_10").val();
    var cancle_desc_11 = $("#SettingAjaxCancel #cancle_desc_11").val();
    var cancle_desc_12 = $("#SettingAjaxCancel #cancle_desc_12").val();
    var cancle_desc_13 = $("#SettingAjaxCancel #cancle_desc_13").val();
    var cancle_desc_14 = $("#SettingAjaxCancel #cancle_desc_14").val();
    var cancle_desc_15 = $("#SettingAjaxCancel #cancle_desc_15").val();

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajaxurl,
        data: {
            'action' : 'cancel_lsit',
            'cancle_desc_1' : cancle_desc_1,
            'cancle_desc_2' : cancle_desc_2,
            'cancle_desc_3' : cancle_desc_3,
            'cancle_desc_4' : cancle_desc_4,
            'cancle_desc_5' : cancle_desc_5,
            'cancle_desc_6' : cancle_desc_6,
            'cancle_desc_7' : cancle_desc_7,
            'cancle_desc_8' : cancle_desc_8,
            'cancle_desc_9' : cancle_desc_9,
            'cancle_desc_10' : cancle_desc_10,
            'cancle_desc_11' : cancle_desc_11,
            'cancle_desc_12' : cancle_desc_12,
            'cancle_desc_13' : cancle_desc_13,
            'cancle_desc_14' : cancle_desc_14,
            'cancle_desc_15' : cancle_desc_15,
        },
        success: function (data) {

            if (data.status) {

                ALERT(data.message,"s");
                $("#SettingAjaxCancel .btn").val("تنظیمات اعمال گردید");
                window.location.reload();

            }else{
                ALERT(data.message,"e");
                $("#SettingAjaxCancel .btn").val("ارسال");
            }

        },
        error: function (errorThrown) {

        }
    });
});

$("#accounting_setting_Ajax").submit(function (e) {
    e.preventDefault();
    if(ValidateForm(this.id) == false){
        return false;
    }
    $("#accounting_setting_Ajax .btn").val("در حال ارسال اطلاعات");
    var type_of_desc_acc = [];
    $("#accounting_setting_Ajax select#type_of_desc_acc").each(function(){
        if($(this).val() == ""){}else {
            type_of_desc_acc.push($(this).val());
        }
    });

    var title_of_desc_acc = [];
    $("#accounting_setting_Ajax input#title_of_desc_acc").each(function(){
            title_of_desc_acc.push($(this).val());
    });

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajaxurl,
        data: {
            'action' : 'accounting_setting',
            'type_of_desc_acc' : type_of_desc_acc,
            'title_of_desc_acc' : title_of_desc_acc,

        },
        success: function (data) {

            if (data.status) {

                ALERT(data.message,"s");
                $("#accounting_setting_Ajax .btn").val("تنظیمات اعمال گردید");
                window.location.reload();

            }else{
                ALERT(data.message,"e");
                $("#accounting_setting_Ajax .btn").val("ارسال");
            }

        },
        error: function (errorThrown) {

        }
    });
});

$(document).ready(function (){

    $(".addPlaceObjectionslist").click(function (){
        $(".PlaceObjectionslist").append($(".thempObjectionslist").html());
    });

});

$("#SettingAjaxDescription").submit(function (e) {

    e.preventDefault();
    if(ValidateForm(this.id) == false){
        return false;
    }

    $("#SettingAjaxCancel .btn").val("در حال ارسال اطلاعات");
    var invoice_descritpion_show = tinymce.get('invoice_descritpion_show').getContent();
    var print_invoice_descritpion_show = tinymce.get('print_invoice_descritpion_show').getContent();

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajaxurl,
        data: {
            'action' : 'invoice_setting',
            'invoice_descritpion_show' : invoice_descritpion_show,
            'print_invoice_descritpion_show' : print_invoice_descritpion_show,
        },
        success: function (data) {

            if (data.status) {

                ALERT(data.message,"s");
                $("#SettingAjaxDescription .btn").val("تنظیمات اعمال گردید");
                window.location.reload();

            }else{
                ALERT(data.message,"e");
                $("#SettingAjaxDescription .btn").val("ارسال");
            }

        },
        error: function (errorThrown) {

        }
    });
});

$("#addHappyCallTechAjax").submit(function (e) {

    e.preventDefault();
    if(ValidateForm(this.id) == false){
        return false;
    }
    $("#addHappyCallTechAjax .btn").val("در حال ارسال اطلاعات");

    var title = $("#addHappyCallTechAjax input#title_of_happycall").val();
    var key = $("#addHappyCallTechAjax input#key_of_happycall").val();
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajaxurl,
        data: {
            'action' : 'addHappyCallTech',
            'key' : key,
            'title' : title,

        },
        success: function (data) {

            if (data.status) {

                ALERT(data.message,"s");
                $("#addHappyCallTechAjax .btn").val("تنظیمات اعمال گردید");
                window.location.href = "https://crm.tamironline.com/happycalltech/";

            }else{
                ALERT(data.message,"e");
                $("#addHappyCallTechAjax .btn").val("ارسال");
            }

        },
        error: function (errorThrown) {

        }
    });
});
$("#addHappyCallCustomerAjax").submit(function (e) {

    e.preventDefault();
    if(ValidateForm(this.id) == false){
        return false;
    }
    $("#addHappyCallCustomerAjax .btn").val("در حال ارسال اطلاعات");

    var title = $("#addHappyCallCustomerAjax input#title_of_happycall").val();
    var key = $("#addHappyCallCustomerAjax input#key_of_happycall").val();
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajaxurl,
        data: {
            'action' : 'addHappyCallCustomer',
            'key' : key,
            'title' : title,

        },
        success: function (data) {

            if (data.status) {

                ALERT(data.message,"s");
                $("#addHappyCallCustomerAjax .btn").val("تنظیمات اعمال گردید");
                window.location.href = "https://crm.tamironline.com/happycallcustomer/";

            }else{
                ALERT(data.message,"e");
                $("#addHappyCallCustomerAjax .btn").val("ارسال");
            }

        },
        error: function (errorThrown) {

        }
    });
});
