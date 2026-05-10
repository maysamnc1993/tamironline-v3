$("#OperatorAjax").submit(function (e) {

    e.preventDefault();
    if(ValidateForm(this.id) == false){
        return false;
    }
    $("#OperatorAjax .btn").val("در حال ارسال اطلاعات");
    var type_action = $("#OperatorAjax input#type_action").val();
    var uid = $("#OperatorAjax input#uid").val();
    var username = $("#OperatorAjax input#username").val();
    var password = $("#OperatorAjax input#password").val();
    var firstname = $("#OperatorAjax input#firstname").val();
    var operator_id = $("#OperatorAjax input#operator_id").val();
    var national_code = $("#OperatorAjax input#national_code").val();
    var mobile = $("#OperatorAjax input#mobile").val();
    var phone = $("#OperatorAjax input#phone").val();
    var address = $("#OperatorAjax textarea#address").val();
    var description = $("#OperatorAjax textarea#description").val();
    var operator = $("#OperatorAjax input#operator").val();
    var call = $("#OperatorAjax input#call").val();
    var price = $("#OperatorAjax input#price").val();
    var StartTimeWork = $("#OperatorAjax select#StartTimeWork").val();
    var endTimeWork = $("#OperatorAjax select#endTimeWork").val();
    var document = $("#OperatorAjax input#document").val();
    var img_profile = $("#OperatorAjax input#img_profile").val();
    var status = $("#OperatorAjax select#status").val();
    var access =[];
    $("#OperatorAjax input[name^=access]:checked").each(function(){
        access.push($(this).val());
    });

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajaxurl,
        data: {
            'action' : 'operator',
            'type_action' : type_action,
            'uid' : uid,
            'username' : username,
            'password' : password,
            'firstname' : firstname,
            'operator_id' : operator_id,
            'national_code' : national_code,
            'mobile' : mobile,
            'phone' : phone,
            'address' : address,
            'description' : description,
            'operator' : operator,
            'call' : call,
            'price' : price,
            'StartTimeWork' : StartTimeWork,
            'endTimeWork' : endTimeWork,
            'document' : document,
            'img_profile' : img_profile,
            'status' : status,
            'access' : access,

        },
        success: function (data) {

            if (data.status) {

                ALERT(data.message,"s");
                $("#OperatorAjax .btn").val("اپراتور اضافه گردید");

            }else{
                ALERT(data.message,"e");
                $("#OperatorAjax .btn").val("افزودن اپراتور");
            }

        },
        error: function (errorThrown) {

        }
    });
});
