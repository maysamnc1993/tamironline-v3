jQuery("#PaymentWalletNow").click(function (e){
    e.preventDefault();
    var debt = jQuery(this).attr("data-debt");
    $("#PaymentWalletNow").html("در حال ارسال اطلاعات");
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajaxurl,
        data: {
            'action' : 'paymentNow',
            'debt' : debt,

        },
        success: function (data) {

            if (data.status) {

                ALERT(data.message,"s");
                $("#PaymentWalletNow").html(data.message);
                window.location.href= data.url;

            }else{
                ALERT(data.message,"e");
                $("#PaymentWalletNow").html("پرداخت آنلاین");
            }

        },
        error: function (errorThrown) {

        }
    });

});

jQuery(".Charge_Wallet li a").click(function (e){
    e.preventDefault();
    var charge = jQuery(this).attr("data-charge");
    var chargeـreal = jQuery(this).attr("data-charge-real");
    $(".Charge_Wallet li a").html("در حال ارسال اطلاعات");
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajaxurl,
        data: {
            'action' : 'paymentNow',
            'debt' : charge,

        },
        success: function (data) {

            if (data.status) {

                ALERT(data.message,"s");
                window.location.href= data.url;

            }else{
                ALERT(data.message,"e");
            }

        },
        error: function (errorThrown) {

        }
    });

});