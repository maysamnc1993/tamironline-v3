jQuery(document).ready(function () {
    jQuery('body').on('keyup keydown'," input.after_200", function () {
        let value = parseInt(jQuery(this).val().replace(/,/g, ''), 10);
    
        if (value > 200000) {
            alert('مقدار وارد شده نباید بیشتر از ۲۰۰,۰۰۰ تومان باشد!');
            jQuery(this).val(200000);
            jQuery(this).keyup();
        }
    });
});


$(document).ready(function (){

    jQuery("body").on("change", ".Img_selector input.imgUpload", function (e) {
        var jQuerythis = jQuery(this);
        jQuery(this).parent(".box_upload").parent(".Img_selector").addClass("selected");
        var file_obj = jQuerythis.prop('files');
        var formData = new FormData();
        for(i=0; i<file_obj.length; i++) {
            formData.append('file[]', file_obj[i]);
        }
        formData.append('action', 'file_upload')
        jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            contentType: false,
            processData: false,
            data: formData,
            success: function (response) {

                var data = jQuery.parseJSON(response);
                // var data = response;
                jQuery(".Img_selector.selected .thumb").css("background-image","url("+data.url+")");
                jQuery(".Img_selector.selected input.source_image").val(data.url);
                jQuery(".Img_selector.selected").addClass("uploaded");
                jQuery(".Img_selector.selected").removeClass("selected");

            },
            error: function (errorThrown) {
                console.log(errorThrown);
            }
        });

    });

    $("#SearchBoxBTN").click(function (){
        $("#SearchModalPopUp").modal("show");
    })

    $("#OrderAjax #customer").change(function (){
        var subscription  = $(this).find(':selected').attr('data-subscription');
        var mobile  = $(this).find(':selected').attr('data-mobile');
        var phone  = $(this).find(':selected').attr('data-phone');
        var name  = $(this).find(':selected').attr('data-name');
        $("#OrderAjax #subscription").val(subscription);
        $("#OrderAjax #mobile").val(mobile);
        $("#OrderAjax #phone").val(phone);
        $("#OrderAjax #first_name").val(name);

    })
    $("#Tech_DetailAjax label input ").click(function (){
        $("#Tech_DetailAjax label").removeClass("active");
        $(this).parent("label").addClass("active");
        var action = $(this).attr("data-action");
        var transfer = $(this).attr("data-action-transfer");

        $(".box_status_return").removeClass("active");
        $(".box_status_return#" + action).addClass("active");
        var val = $(this).val();
        
        if(transfer == 'filter'){
            jQuery(".totalInvoiceSection #totalInvoice").addClass("after_200");
            jQuery(".List_Of_piece").hide();
        }else{
         jQuery(".totalInvoiceSection #totalInvoice").removeClass("after_200");
         jQuery(".List_Of_piece").show();
        }


    })
    $("#internal_Tech_DetailAjax label input ").click(function (){
        $("#internal_Tech_DetailAjax label").removeClass("active");
        $(this).parent("label").addClass("active");
        var action = $(this).attr("data-action");
        $(".box_status_return").removeClass("active");
        $(".box_status_return#" + action).addClass("active");

        var val = $(this).val();


    })

    $(".add_image_order_internal").click(function (){

        var data = '<div class="form-group third">\n' +
            '<label>تصویر</label>\n' +
            '<div class="Img_selector">\n' +
            '<div class="loading">\n' +
            '\n' +
            '</div>\n' +
            '<div class="thumb" style=""></div>\n' +
            '<div class="box_upload">\n' +
            '<svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 480.24 480.24" style="enable-background:new 0 0 480.24 480.24;" xml:space="preserve">\n' +
            '<g>\n' +
            '\t<g>\n' +
            '\t\t<g>\n' +
            '\t\t\t<path d="M391.86,144.552c0.16-2.712,0.248-5.464,0.248-8.312c0.09-59.919-39.046-112.838-96.364-130.3     c-71.85-21.89-147.842,18.61-169.732,90.46c-2.024-0.104-3.976-0.16-5.904-0.16c-66.274,0-120,53.726-120,120s53.726,120,120,120     h72.088c4.418,0,8-3.582,8-8s-3.582-8-8-8h-72.088c-57.438,0-104-46.562-104-104s46.562-104,104-104     c3.688,0.01,7.373,0.211,11.04,0.6c3.93,0.329,7.534-2.2,8.56-6.008c13.401-53.473,61.51-90.937,116.637-90.828     c66.274,0.13,119.894,53.962,119.763,120.236c0.021,4.99-0.303,9.975-0.968,14.92c-0.049,0.358-0.073,0.72-0.073,1.081     c0.001,4.418,3.583,7.999,8.001,7.999h1.04c44.183,0,80,35.817,80,80c0,44.183-35.817,80-80,80h-95.464c-4.418,0-8,3.582-8,8     s3.582,8,8,8h95.464c50.028,0.01,91.677-38.401,95.707-88.267C484.086,195.127,444.707,148.823,391.86,144.552z"></path>\n' +
            '\t\t\t<polygon points="336.276,245.088 240.108,148.928 143.94,245.088 155.26,256.408 232.108,179.552 232.108,480.24 248.108,480.24      248.108,179.552 324.956,256.408    "></polygon>\n' +
            '\t\t</g>\n' +
            '\t</g>\n' +
            '</g>\n' +
            '<g>\n' +
            '</g>\n' +
            '<g>\n' +
            '</g>\n' +
            '<g>\n' +
            '</g>\n' +
            '<g>\n' +
            '</g>\n' +
            '<g>\n' +
            '</g>\n' +
            '<g>\n' +
            '</g>\n' +
            '<g>\n' +
            '</g>\n' +
            '<g>\n' +
            '</g>\n' +
            '<g>\n' +
            '</g>\n' +
            '<g>\n' +
            '</g>\n' +
            '<g>\n' +
            '</g>\n' +
            '<g>\n' +
            '</g>\n' +
            '<g>\n' +
            '</g>\n' +
            '<g>\n' +
            '</g>\n' +
            '<g>\n' +
            '</g>\n' +
            '</svg>\n' +
            '<span>برای بارگذاری تصویر (بکشید / کلیک کنید)</span>\n' +
            '<input class="imgUpload" type="file" >\n' +
            '<input class="source_image" type="hidden" id="order_images"  value="">\n' +
            '</div>\n' +
            '</div>\n' +
            '</div>';

        $(".placeholder_image_order").append(data);
    })

    $(".add_box_temp").click(function (){
        $(".place_box_temp").append($(".invoice_box_temp").html());
    })
    jQuery("body").on("keyup"," .PriceFormat",function (event) {
        if (event.which >= 37 && event.which <= 40) return;
        // format number
        jQuery(this).val(function (index, value) {
            return value
                .replace(/\D/g, "")
                .replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        });
    })
    $("select#send_to").change(function (){

        var val = $(this).val();
        $("#operator_part,#technician_part").hide();
        $("#"+val+"_part").show();

    })
    $("ul.filter li").click(function (){
        var id = this.id;
        $("ul.filter li").removeClass("active");
        $(this).addClass("active");
        $(".itemOrderBox").removeClass("active");
        $(".itemOrderBox." + id).addClass("active");
        if(id == "all"){
            $(".itemOrderBox").removeClass("active");
            $(".itemOrderBox.s_0 , .itemOrderBox.s_1 , .itemOrderBox.s_3").addClass("active");
        }
    });
    $("nav .MobileMenu").click(function (){
        $("nav .List_Of_Menu").toggleClass("active");
    })
    $("nav .List_Of_Menu ul li a").click(function (e){
        e.preventDefault();
        var id = this.id;

        if(id == "Exit"){
            jQuery("#ExitPanel").modal("show");
        }
        else if(id == "SearchTech"){
            $(".contactListTechnician").toggleClass("active");
        }
        else{

            var href = $(this).attr("href");
            if($(this).next("ul").hasClass("sub_menu")){
                $(this).next("ul").toggleClass("active");
            }
            else{
                window.location.href = href;
            }
        }
    });
    $(".contactListTechnician .icon").click(function (e){
        e.preventDefault();
        $(this).parent().toggleClass("active");
    })
    $(".contactListTechnician .ContactList #searchTech").on("keydown keyup",function (){

        var val = $(this).val()

        if(val.length > 0){
            $(".contactListTechnician .ContactList .tech_list li").hide();
            $(".contactListTechnician .ContactList .tech_list li:contains("+ val +")").show();
        }else{
            $(".contactListTechnician .ContactList .tech_list li").show();
        }
    });

    $(" .content #searchTech").on("keydown keyup",function (){

        var val = $(this).val()

        if(val.length > 0){
            $(".content.ListTable tr").hide();
            $(".content.ListTable tr:contains("+ val +")").show();
        }else{
            $(".content.ListTable tr").show();
        }
    });
    $("select#rewardType").change(function (){

        var val = $(this).val();
        $("select#rewardDesc option").hide();
        $("select#rewardDesc option").each(function (){
            var id = this.id;
            if(id == val){
                $(this).show();
            }
        });

    })

    $(".Status_search li label input").click(function (e){
        var parent_id = $(this).parent().parent().parent(".Status_search").attr("id");
        if(parent_id == "return_search" || parent_id == "no_tech"){
            $(this).parent("label").toggleClass("active");
        }else{
            $(".Status_search#" + parent_id + " li label").removeClass("active");
            $(this).parent("label").addClass("active");
        }
    })
});

$("#ExitPanel .ExitSistem li a.no").click(function (e){
    e.preventDefault();
    jQuery("#ExitPanel").modal("hide");
})

function ALERT(msg,status){
    $(".MessageLog").addClass("active");
    $(".MessageLog").addClass(status);
    $(".MessageLog .msg").html(msg);
    setTimeout(function (){
        $(".MessageLog.active").removeClass("active");
        $(".MessageLog").removeClass(status);
    },4000)
}


$(".HappyCallCustomer").click(function(){
    $("#PopUpHappyCallCustomer").modal("show");
});
$(".HappyCalltech").click(function(){
    $("#PopUpHappyCallTech").modal("show");
});


$(".SelectTypeUser").click(function(){
    $("#SelectTypeUserPopUp").modal("show");
});
$(document).ready(function (){
    $(".SelectType #SelectTypeUserFOrmAJax .SelectTypeUserSwitch li").click(function (){
        $(".SelectType #SelectTypeUserFOrmAJax .SelectTypeUserSwitch li").removeClass("active");
        $(this).addClass("active");
    })
})
//
// function convertToWords(num) {
//     // بررسی اگر ورودی خالی باشد یا عدد نباشد
//     if (num === '' || isNaN(num)) {
//         document.getElementById('invoiceInWords').innerText = '';
//         return;
//     }
//
//     // تبدیل عدد به حروف و افزودن کلمه "تومان"
//     var words = toWords(parseInt(num));
//     document.getElementById('invoiceInWords').innerText = words + ' تومان';
// }
//
// // تابع تبدیل عدد به حروف به زبان فارسی
// function toWords(num) {
//     const units = ['', 'یک', 'دو', 'سه', 'چهار', 'پنج', 'شش', 'هفت', 'هشت', 'نه'];
//     const tens = ['', '', 'بیست', 'سی', 'چهل', 'پنجاه', 'شصت', 'هفتاد', 'هشتاد', 'نود'];
//     const hundreds = ['', 'یکصد', 'دویست', 'سیصد', 'چهارصد', 'پانصد', 'ششصد', 'هفتصد', 'هشتصد', 'نهصد'];
//     const thousands = ['', 'هزار', 'میلیون', 'میلیارد'];
//
//     if (num === 0) return 'صفر';
//
//     let result = '';
//     let partIndex = 0;
//
//     while (num > 0) {
//         let part = num % 1000;
//         if (part > 0) {
//             let partStr = convertThreeDigits(part);
//             result = partStr + (thousands[partIndex] ? ' ' + thousands[partIndex] : '') + (result ? ' و ' + result : '');
//         }
//         num = Math.floor(num / 1000);
//         partIndex++;
//     }
//
//     return result.trim();
// }
//
// // تابع کمکی برای تبدیل سه رقم به حروف
// function convertThreeDigits(num) {
//     const units = ['', 'یک', 'دو', 'سه', 'چهار', 'پنج', 'شش', 'هفت', 'هشت', 'نه'];
//     const tens = ['', '', 'بیست', 'سی', 'چهل', 'پنجاه', 'شصت', 'هفتاد', 'هشتاد', 'نود'];
//     const hundreds = ['', 'یکصد', 'دویست', 'سیصد', 'چهارصد', 'پانصد', 'ششصد', 'هفتصد', 'هشتصد', 'نهصد'];
//
//     let result = '';
//
//     if (num >= 100) {
//         result += hundreds[Math.floor(num / 100)];
//         num = num % 100;
//     }
//
//     if (num >= 20) {
//         result += (result ? ' و ' : '') + tens[Math.floor(num / 10)];
//         num = num % 10;
//     }
//
//     if (num > 0) {
//         result += (result ? ' و ' : '') + units[num];
//     }
//
//     return result;
// }


const inputs = document.querySelectorAll('.input');

inputs.forEach((input, index) => {
    input.addEventListener('input', (e) => {
        const value = e.target.value;
        if (value.length === 1 && index < inputs.length - 1) {
            inputs[index + 1].focus();
        }
    });

    input.addEventListener('keydown', (e) => {
        if (e.key === 'Backspace' && index > 0 && e.target.value === '') {
            inputs[index - 1].focus();
        }
    });
});




