$("#addFinancialAjax").submit(function (e) {

    e.preventDefault();
    if(ValidateForm(this.id) == false){
        return false;
    }

    if (confirm('آیا از درخواست خود مطمن هستید؟')) {
    } else {
        return false;
    }

    $("#addFinancialAjax .btn").val("در حال ارسال اطلاعات");

    var technician = $("#addFinancialAjax select#technician").val();
    var price = $("#addFinancialAjax input#price").val();
    var description = $("#addFinancialAjax input#description").val();
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajaxurl,
        data: {
            'action' : 'addFinancialAjax',
            'technician' : technician,
            'price' : price,
            'description' : description,
        },
        success: function (data) {

            if (data.status) {
                ALERT(data.message,"s");
                $("#addFinancialAjax .btn").val("شارژ کیف پول با موفقیت ایجاد شد.");

            }else{
                ALERT(data.message,"e");
                $("#addFinancialAjax .btn").val("ارسال");
            }

        },
        error: function (errorThrown) {

        }
    });
});

$("#addFinancialrewardAjax").submit(function (e) {

    e.preventDefault();
    if(ValidateForm(this.id) == false){

    }
    if (confirm('آیا از درخواست خود مطمن هستید؟')) {
    } else {
        return false;
    }

    $("#addFinancialrewardAjax .btn").val("در حال ارسال اطلاعات");

    var technician = $("#addFinancialrewardAjax select#technician_id").val();
    var rewardType = $("#addFinancialrewardAjax select#rewardType").val();
    var rewardprice = $("#addFinancialrewardAjax input#rewardprice").val();
    var rewardDesc = $("#addFinancialrewardAjax select#rewardDesc").val();

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajaxurl,
        data: {
            'action' : 'addFinancialRewardAjax',
            'technician' : technician,
            'rewardType' : rewardType,
            'rewardprice' : rewardprice,
            'rewardDesc' : rewardDesc,
        },
        success: function (data) {

            if (data.status) {
                ALERT(data.message,"s");
                $("#addFinancialrewardAjax .btn").val("فاکتور پاداش / جریمه با موفقیت ایجاد شد.");

            }else{
                ALERT(data.message,"e");
                $("#addFinancialrewardAjax .btn").val("ارسال");
            }

        },
        error: function (errorThrown) {

        }
    });
});

$("#addFinancialrewardInternalAjax").submit(function (e) {

    e.preventDefault();
    if(ValidateForm(this.id) == false){

    }
    if (confirm('آیا از درخواست خود مطمن هستید؟')) {
    } else {
        return false;
    }

    $("#addFinancialrewardInternalAjax .btn").val("در حال ارسال اطلاعات");

    var technician = $("#addFinancialrewardInternalAjax select#technician_id").val();
    var rewardType = $("#addFinancialrewardInternalAjax select#rewardType").val();
    var rewardprice = $("#addFinancialrewardInternalAjax input#rewardprice").val();
    var rewardDesc = $("#addFinancialrewardInternalAjax input#rewardDesc").val();

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajaxurl,
        data: {
            'action' : 'addFinancialRewardInternalAjax',
            'technician' : technician,
            'rewardType' : rewardType,
            'rewardprice' : rewardprice,
            'rewardDesc' : rewardDesc,
        },
        success: function (data) {

            if (data.status) {
                ALERT(data.message,"s");
                $("#addFinancialrewardInternalAjax .btn").val("فاکتور پاداش / جریمه با موفقیت ایجاد شد.");

            }else{
                ALERT(data.message,"e");
                $("#addFinancialrewardInternalAjax .btn").val("ارسال");
            }

        },
        error: function (errorThrown) {

        }
    });
});

$("#addFinancialInnerAjax").submit(function (e) {

    e.preventDefault();
    if(ValidateForm(this.id) == false){
        return false;
    }

    if (confirm('آیا از درخواست خود مطمن هستید؟')) {
    } else {
        return false;
    }

    $("#addFinancialInnerAjax .btn").val("در حال ارسال اطلاعات");

    var technician = $("#addFinancialInnerAjax select#technician").val();
    var price = $("#addFinancialInnerAjax input#price").val();
    var description = $("#addFinancialInnerAjax input#description").val();
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajaxurl,
        data: {
            'action' : 'addFinancialInternalAjax',
            'technician' : technician,
            'price' : price,
            'description' : description,
        },
        success: function (data) {

            if (data.status) {
                ALERT(data.message,"s");
                $("#addFinancialInnerAjax .btn").val("شارژ کیف پول با موفقیت ایجاد شد.");

            }else{
                ALERT(data.message,"e");
                $("#addFinancialInnerAjax .btn").val("ارسال");
            }

        },
        error: function (errorThrown) {

        }
    });
});

function LoadDebtTechFinExternal(){
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajaxurl,
        data: {
            'action' : 'LoadDebtTechFinExternal',
        },
        success: function (data) {

            if (data.status) {
                $(".Short_view_option #LoadDebtTechFinExternal").html(data.message);
            }else{
                ALERT(data.message,"e");
            }

        },
        error: function (errorThrown) {

        }
    });
}
function LoadDebtTechFinInternal(){
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajaxurl,
        data: {
            'action' : 'LoadDebtTechFinInternal',
        },
        success: function (data) {

            if (data.status) {
                $(".Short_view_option #LoadDebtTechFinInternal").html(data.message);
            }else{
                ALERT(data.message,"e");
            }

        },
        error: function (errorThrown) {

        }
    });
}

$(document).ready(function (){

    $("#addFinancialrewardAjax input#rewardprice").on("keyup keydown",function (){
        var val = $(this).val();
        $("#addFinancialrewardAjax .price_char").html(Num2persian(val) + " تومان");
    });

    $("#addFinancialAjax input#price").on("keyup keydown",function (){
        var val = $(this).val();
        $("#addFinancialAjax .price_char").html(Num2persian(val) + " تومان");
    });

    $("#addFinancialrewardInternalAjax input#rewardprice").on("keyup keydown",function (){
        var val = $(this).val();
        $("#addFinancialrewardInternalAjax .price_char").html(Num2persian(val) + " تومان");
    });

    $("#addFinancialInnerAjax input#price").on("keyup keydown",function (){
        var val = $(this).val();
        $("#addFinancialInnerAjax .price_char").html(Num2persian(val) + " تومان");
    });

})

$(document).ready(function (){
    $("#done input#totalInvoice").on("keyup keydown",function () {
        var val = $(this).val();
        $("#done .price_char").html(Num2persian(val) + " تومان");
    });
});
$(document).ready(function (){
    $(".default_price li").click(function (){

       $("#addFinancialAjax #price").val("");
       $("#addFinancialAjax #price").val(this.id);
       $("#addFinancialAjax #price").keydown();
    });
})


jQuery("li.actionLi > div.delete_financialBTN").click(function (){
    if (confirm('آیا از انتقال به زباله دان مطمن هستید؟')) {
    } else {
        return false;
    }
    var id = this.id;
    ALERT("در حال ارسال اطلاعات..","s");
    jQuery.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajaxurl,
        data: {
            'action' : 'MoveTOTrash',
            'id' : id,
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
});

jQuery("li.actionLi > div.edit_financialBTN").click(function (){

    var id = this.id;
    ALERT("در حال ارسال اطلاعات..","s");
    jQuery.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajaxurl,
        data: {
            'action' : 'EditFinancialAjax',
            'id' : id,
        },
        success: function (data) {

            if (data.status) {
                jQuery("#EditFinancialPopup .PlaceOForm").html(data.message);
                jQuery("#EditFinancialPopup").modal("show");
                // ALERT(data.message,"s");
                // window.location.reload();
            }else{
                ALERT(data.message,"e");
            }
        },
        error: function (errorThrown) {

        }

    });

});


$("body").on("submit" , " #FinancialInvoiceEdit " , function (e) {

    e.preventDefault();
    if(ValidateForm(this.id) == false){
        return false;
    }

    $("#addFinancialInnerAjax .btn").val("در حال ارسال اطلاعات");
    var oid = $("#FinancialInvoiceEdit input#oid").val();
    var invoice_descripotion = $("#FinancialInvoiceEdit textarea#invoice_descripotion").val();

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajaxurl,
        data: {
            'action' : 'editFinancialInvoiceAjax',
            'oid' : oid,
            'invoice_descripotion' : invoice_descripotion,
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
});

$("body").on("submit" , " #FinancialWalletEdit " , function (e) {

    e.preventDefault();
    if(ValidateForm(this.id) == false){
        return false;
    }

    $("#FinancialWalletEdit .btn").val("در حال ارسال اطلاعات");

    var oid = $("#FinancialWalletEdit input#oid").val();
    var wallet_pay = $("#FinancialWalletEdit input#wallet_pay").val();
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajaxurl,
        data: {
            'action' : 'editFinancialWalletAjax',
            'oid' : oid,
            'wallet_pay' : wallet_pay,
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
});


// jQuery(document).ready(function($) {
//     console.log("jQuery is ready!");
//
//     $('#form-tech-financial-list').on('submit', function(e) {
//         e.preventDefault(); // جلوگیری از رفرش شدن صفحه
//         console.log("Form submitted!");
//
//         // گرفتن مقادیر تاریخ
//         var startDate = $('#timestamp_start').val();
//         var endDate = $('#timestamp_end').val();
//
//         // بررسی اینکه مقادیر دریافت شده‌اند
//         console.log("Start Date: " + startDate);
//         console.log("End Date: " + endDate);
//
//         if (!startDate || !endDate) {
//             console.error("Start Date or End Date is not defined correctly.");
//             return;
//         }
//
//         $.ajax({
//             type: 'POST',
//             url: ajaxurl, // اطمینان از مقداردهی صحیح ajaxurl
//             data: {
//                 action: 'filter_financial_list',
//                 start_date: startDate,
//                 end_date: endDate,
//             },
//             success: function(response) {
//                 console.log("Response: ", response);
//                 $('.list_of_data_orders').html(response);
//             },
//             error: function(xhr, status, error) {
//                 console.error("AJAX Error: " + error);
//                 console.log("Response: ", xhr.responseText);
//             }
//         });
//     });
// });


jQuery(document).ready(function ($) {
    $('#date_filter, #start_date, #end_date').on('change', function () {
        var selectedFilter = $('#date_filter').val(); // انتخاب فیلتر
        var startDate = $('#start_date').val(); // تاریخ شروع
        var endDate = $('#end_date').val(); // تاریخ پایان

        $.ajax({
            url: ajaxurl, // URL برای پردازش AJAX
            type: 'POST',
            data: {
                action: 'filter_financial_data', // اکشن برای وردپرس
                filter: selectedFilter,
                start_date: startDate,
                end_date: endDate,
            },
            beforeSend: function () {
                $('#financial_list').html('<p>در حال بارگذاری...</p>'); // پیام لودینگ
            },
            success: function (response) {
                $('#financial_list').html(response); // نمایش نتایج
            },
            error: function () {
                alert('مشکلی پیش آمده است.');
            },
        });
    });
});
