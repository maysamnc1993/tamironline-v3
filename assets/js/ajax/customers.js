$("#CustomersAjax").submit(function (e) {

    e.preventDefault();
    if(ValidateForm(this.id) == false){
        return false;
    }
    $("#CustomersAjax .btn").val("در حال ارسال اطلاعات");
    var type_action = $("#CustomersAjax input#type_action").val();
    var uid = $("#CustomersAjax input#uid").val();
    var username = $("#CustomersAjax input#username").val();
    var password = $("#CustomersAjax input#password").val();
    var firstname = $("#CustomersAjax input#firstname").val();
    var mobile = $("#CustomersAjax input#mobile").val();
    var phone = $("#CustomersAjax input#phone").val();

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajaxurl,
        data: {
            'action' : 'customers',
            'type_action' : type_action,
            'uid' : uid,
            'username' : username,
            'password' : password,
            'firstname' : firstname,
            'mobile' : mobile,
            'phone' : phone,

        },
        success: function (data) {

            if (data.status) {

                ALERT(data.message,"s");
                $("#CustomersAjax .btn").val("مشتری اضافه گردید");

            }else{
                ALERT(data.message,"e");
                $("#CustomersAjax .btn").val("افزودن مشتری");
            }

        },
        error: function (errorThrown) {

        }
    });
});