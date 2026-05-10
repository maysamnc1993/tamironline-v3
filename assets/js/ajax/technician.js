$("#TechnicianAjax").submit(function (e) {

    e.preventDefault();
    if(ValidateForm(this.id) == false){
        return false;
    }
    $("#TechnicianAjax .btn").val("در حال ارسال اطلاعات");
    var type_action = $("#TechnicianAjax input#type_action").val();
    var uid = $("#TechnicianAjax input#uid").val();
    var username = $("#TechnicianAjax input#username").val();
    var password = $("#TechnicianAjax input#password").val();
    var firstname = $("#TechnicianAjax input#firstname").val();
    var firstname_tech = $("#TechnicianAjax input#firstname_tech").val();
    var technician_id = $("#TechnicianAjax input#technician_id_sh").val();
    var national_code = $("#TechnicianAjax input#national_code").val();
    var mobile = $("#TechnicianAjax input#mobile").val();
    var phone = $("#TechnicianAjax input#phone").val();
    var phone_force = $("#TechnicianAjax input#phone_force").val();
    var address = $("#TechnicianAjax textarea#address").val();
    var description = $("#TechnicianAjax textarea#description").val();
    var percent = $("#TechnicianAjax input#percent").val();
    var max_order = $("#TechnicianAjax input#max_order").val();
    var max_price = $("#TechnicianAjax input#max_price").val();
    var status = $("#TechnicianAjax select#status").val();
    var type_tech = $("#TechnicianAjax select#type_tech").val();
    var cart_img = $("#TechnicianAjax input#cart_img").val();
    var province = $("#TechnicianAjax select#province").val();
    var ready_for_derliver = $("#TechnicianAjax select#ready_for_derliver").val();
    var type_of_calc_tech = $("#TechnicianAjax select#type_of_calc_tech").val();
    var tech_per_of_all = $("#TechnicianAjax input#tech_per_of_all").val();

    // دریافت مقادیر چک‌باکس‌های تخصص‌ها (specialty)
    var specialty = [];
    $('input[name="specialty[]"]:checked').each(function() {
        specialty.push($(this).val());
    });
    var specialty_string = specialty.join(',');  // تبدیل آرایه به رشته

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajaxurl,
        data: {
            'action' : 'technician',
            'type_action' : type_action,
            'uid' : uid,
            'username' : username,
            'password' : password,
            'firstname' : firstname,
            'firstname_tech' : firstname_tech,
            'technician_id' : technician_id,
            'national_code' : national_code,
            'mobile' : mobile,
            'phone' : phone,
            'phone_force' : phone_force,
            'address' : address,
            'description' : description,
            'percent' : percent,
            'max_order' : max_order,
            'max_price' : max_price,
            'status' : status,
            'type_tech' : type_tech,
            'cart_img' : cart_img,
            'province' : province,
            'ready_for_derliver' : ready_for_derliver,
            'specialty' : specialty_string,  // ارسال رشته تخصص‌ها
            'type_of_calc_tech' : type_of_calc_tech,  // ارسال رشته تخصص‌ها
            'tech_per_of_all' : tech_per_of_all,  // ارسال رشته تخصص‌ها
        },
        success: function (data) {
            if (data.status) {
                ALERT(data.message, "s");
                $("#TechnicianAjax .btn").val("تکنسین اضافه گردید");
            } else {
                ALERT(data.message, "e");
                $("#TechnicianAjax .btn").val("افزودن تکنسین");
            }
        },
        error: function (errorThrown) {
            console.log(errorThrown);
        }
    });
});


$("#UploadImagePersonal").submit(function (e) {

    e.preventDefault();

    $("#OperatorAjax .btn").val("در حال ارسال اطلاعات");
    var img_Personal = $("#UploadImagePersonal input#img_Personal").val();

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajaxurl,
        data: {
            'action' : 'tech_img_personal',
            'img_Personal' : img_Personal,


        },
        success: function (data) {

            if (data.status) {

                ALERT(data.message,"s");
                $("#UploadImagePersonal .btn").val("تصویر با موفقیت اضافه گردید");

            }else{
                ALERT(data.message,"e");
                $("#UploadImagePersonal .btn").val("ارسال");
            }

        },
        error: function (errorThrown) {

        }
    });
});

$(".ShowDetail").click(function (){

    var id = this.id
    ALERT("در حال بارگذراری...","s");

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajaxurl,
        data: {
            'action' : 'LoadPointOfTech',
            'id' : id,


        },
        success: function (data) {

            if (data.status) {

                $("#ShowPopUpPointTech").modal("show");
                $("#ShowPopUpPointTech .List_of_point").html(data.message);


            }else{
                ALERT("خطا! لطفا مجدد تلاش نمایید.","e");
            }

        },
        error: function (errorThrown) {

        }
    });

})