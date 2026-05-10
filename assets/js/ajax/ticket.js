$("#TicketAjax").submit(function (e) {

    e.preventDefault();
    if(ValidateForm(this.id) == false){
        return false;
    }
    $("#TicketAjax .btn").val("در حال ارسال اطلاعات");
    var type_action = $("#TicketAjax input#type_action").val();
    var uid = $("#TicketAjax input#uid").val();
    var send_to = $("#TicketAjax select#send_to").val();
    var operator = $("#TicketAjax select#operator").val();
    var order_ticket = $("#TicketAjax select#order_ticket").val();
    var technician = $("#TicketAjax select#technician").val();
    var subject = $("#TicketAjax input#subject").val();
    var content = tinymce.get('content').getContent()
    var attachment = $("#TicketAjax input#attachment").val();



    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajaxurl,
        data: {
            'action' : 'ticket',
            'type_action' : type_action,
            'uid' : uid,
            'send_to' : send_to,
            'operator' : operator,
            'technician' : technician,
            'order_ticket' : order_ticket,
            'subject' : subject,
            'content' : content,
            'attachment' : attachment,

        },
        success: function (data) {

            if (data.status) {

                ALERT(data.message,"s");
                $("#TicketAjax .btn").val("تیکت اضافه گردید");

            }else{
                ALERT(data.message,"e");
                $("#TicketAjax .btn").val("افزودن تیکت");
            }

        },
        error: function (errorThrown) {

        }
    });
});
$("#TicketReplyAjax").submit(function (e) {

    e.preventDefault();
    if(ValidateForm(this.id) == false){
        return false;
    }
    $("#TicketReplyAjax .btn").val("در حال ارسال اطلاعات");
    var type_action = $("#TicketReplyAjax input#type_action").val();
    var uid = $("#TicketReplyAjax input#uid").val();
    var role = $("#TicketReplyAjax input#role").val();
    var subject = $("#TicketReplyAjax input#subject").val();
    var content = tinymce.get('content').getContent()
    var attachment = $("#TicketReplyAjax input#attachment").val();


    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajaxurl,
        data: {
            'action' : 'reply_ticket',
            'type_action' : type_action,
            'uid' : uid,
            'role' : role,
            'subject' : subject,
            'content' : content,
            'attachment' : attachment,

        },
        success: function (data) {

            if (data.status) {

                ALERT(data.message,"s");
                $("#TicketAjax .btn").val("تیکت اضافه گردید");

            }else{
                ALERT(data.message,"e");
                $("#TicketAjax .btn").val("افزودن تیکت");
            }

        },
        error: function (errorThrown) {

        }
    });
});
$(document).ready(function (){
    $("select#order_ticket").select2( {
        placeholder: "سفارش را انتخاب کنید",
        allowClear: true
    } );
    $("select#technician").select2( {
        placeholder: "تکنسین را انتخاب کنید",
        allowClear: true
    } );
    $("select#operator").select2( {
        placeholder: "اپراتور را انتخاب کنید",
        allowClear: true
    } );
});