"use strict";var delimiter=" و ",zero="صفر",negative="منفی ",letters=[["","یک","دو","سه","چهار","پنج","شش","هفت","هشت","نه"],["ده","یازده","دوازده","سیزده","چهارده","پانزده","شانزده","هفده","هجده","نوزده","بیست"],["","","بیست","سی","چهل","پنجاه","شصت","هفتاد","هشتاد","نود"],["","یکصد","دویست","سیصد","چهارصد","پانصد","ششصد","هفتصد","هشتصد","نهصد"],[""," هزار"," میلیون"," میلیارد"," بیلیون"," بیلیارد"," تریلیون"," تریلیارد"," کوآدریلیون"," کادریلیارد"," کوینتیلیون"," کوانتینیارد"," سکستیلیون"," سکستیلیارد"," سپتیلیون"," سپتیلیارد"," اکتیلیون"," اکتیلیارد"," نانیلیون"," نانیلیارد"," دسیلیون"," دسیلیارد"]],decimalSuffixes=["","دهم","صدم","هزارم","ده‌هزارم","صد‌هزارم","میلیونوم","ده‌میلیونوم","صدمیلیونوم","میلیاردم","ده‌میلیاردم","صد‌‌میلیاردم"],prepareNumber=function(e){var t=e;return"number"==typeof t&&(t=t.toString()),t.length%3==1?t="00".concat(t):t.length%3==2&&(t="0".concat(t)),t.replace(/\d{3}(?=\d)/g,"$&*").split("*")},tinyNumToWord=function(e){if(0===parseInt(e,0))return"";var t=parseInt(e,0);if(t<10)return letters[0][t];if(t<=20)return letters[1][t-10];if(t<100){var r=t%10,n=(t-r)/10;return r>0?letters[2][n]+delimiter+letters[0][r]:letters[2][n]}var i=t%10,u=(t-t%100)/100,s=(t-(100*u+i))/10,a=[letters[3][u]],o=10*s+i;return 0===o?a.join(delimiter):(o<10?a.push(letters[0][o]):o<=20?a.push(letters[1][o-10]):(a.push(letters[2][s]),i>0&&a.push(letters[0][i])),a.join(delimiter))},convertDecimalPart=function(e){return""===(e=e.replace(/0*$/,""))?"":(e.length>11&&(e=e.substr(0,11))," ممیز "+Num2persian(e)+" "+decimalSuffixes[e.length])},Num2persian=function(e){e=e.toString().replace(/[^0-9.-]/g,"");var t=!1,r=parseFloat(e);if(isNaN(r))return zero;if(0===r)return zero;r<0&&(t=!0,e=e.replace(/-/g,""));var n="",i=e,u=e.indexOf(".");if(u>-1&&(i=e.substring(0,u),n=e.substring(u+1,e.length)),i.length>66)return"خارج از محدوده";for(var s=prepareNumber(i),a=[],o=0;o<s.length;o+=1){var l=tinyNumToWord(s[o]);""!==l&&a.push(l+letters[4][s.length-(o+1)])}return n.length>0&&(n=convertDecimalPart(n)),(t?negative:"")+a.join(delimiter)+n};String.prototype.toPersianLetter=function(){return Num2persian(this)},Number.prototype.toPersianLetter=function(){return Num2persian(parseFloat(this).toString())},String.prototype.num2persian=function(){return Num2persian(this)},Number.prototype.num2persian=function(){return Num2persian(parseFloat(this).toString())};

$(document).ready(function (){

    $("#AddCostAjax input#cost_price").on("keyup keydown",function (){
        var val = $(this).val();
        $("#AddCostAjax .price_char").html(Num2persian(val) + " تومان");
    });

    $("#AddCostAjax").submit(function (e) {

        e.preventDefault();
        if(ValidateForm(this.id) == false){
            return false;
        }

        if (confirm('آیا از درخواست خود مطمن هستید؟')) {
        } else {
            return false;
        }

        $("#AddCostAjax .btn").val("در حال ارسال اطلاعات");

        var cost_type = $("#AddCostAjax select#cost_type").val();
        var cost_desc = $("#AddCostAjax textarea#cost_desc").val();
        var cost_price = $("#AddCostAjax input#cost_price").val();
        var document = $("#AddCostAjax input#document").val();
        var document_2 = $("#AddCostAjax input#document_2").val();
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: ajaxurl,
            data: {
                'action' : 'addCostAjax',
                'cost_type' : cost_type,
                'cost_desc' : cost_desc,
                'cost_price' : cost_price,
                'document' : document,
                'document_2' : document_2,
            },
            success: function (data) {

                if (data.status) {
                    ALERT(data.message,"s");
                    $("#addFinancialAjax .btn").val("هزینه با موفقیت ایجاد شد.");

                }else{
                    ALERT(data.message,"e");
                    $("#addFinancialAjax .btn").val("ارسال");
                }

            },
            error: function (errorThrown) {

            }
        });
    });

})


function LoadcostTotal(start = '' , end = ''){
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajaxurl,
        data: {
            'action' : 'LoadcostTotal',
            'start' : start,
            'end' : end,
        },
        success: function (data) {
            if (data.status) {

                $(".list_of_invoice.total span.tt").html(data.price);

            }else{
            }
        },
        error: function (errorThrown) {

        }
    });
}