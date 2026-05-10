<?php
IFLoggedAndVerify("tech_order_list");
// Template Name: Technician Order Payment
get_header();

if(isset($_GET['success'])){
$verify = VerifyNow();
}
else{
$verify = false;
}

$Debt = loadAllDataFinَUser(get_current_user_id());

if($Debt < 0 ){
$debt_value = $Debt * -1;
$wallet = 0;
}else{
$debt_value = 0 ;
$wallet = $Debt;
}

$payment = get_option("payment");

?>

<style>
* {
box-sizing: border-box;
margin: 0;
padding: 0;
}


.modern-payment-container {
padding-top: 70px;
max-width: 480px;
margin: 0 auto;
background: #f5f7fa;
min-height: 100vh;
direction: rtl;
}

.payment-header {
background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
color: white;
padding: 20px;
text-align: right;
position: relative;
}

.payment-header h1 {
font-size: 18px;
font-weight: 600;
margin: 0;
}

.status-notification {
margin: 15px;
padding: 15px;
border-radius: 12px;
text-align: right;
font-size: 14px;
}

.status-notification.success {
background: #d4edda;
color: #155724;
border: 1px solid #c3e6cb;
}

.status-notification.error {
background: #f8d7da;
color: #721c24;
border: 1px solid #f5c6cb;
}

.wallet-card {
background: white;
margin: 15px;
border-radius: 16px;
padding: 20px;
box-shadow: 0 2px 12px rgba(0,0,0,0.08);
text-align: center;
}

.wallet-balance {
font-size: 14px;
color: #666;
margin-bottom: 8px;
text-align: center;
}

.balance-amount {
font-size: 24px;
font-weight: bold;
color: #2d3748;
margin-bottom: 15px;
text-align: center;
}

.balance-amount.negative {
color: #e53e3e;
}

.balance-amount.positive {
color: #38a169;
}

.increase-credit-btn {
background: linear-gradient(135deg, #4fd1c7 0%, #14b8a6 100%);
color: white;
border: none;
padding: 12px 24px;
border-radius: 8px;
font-size: 14px;
font-weight: 600;
cursor: pointer;
width: 100%;
transition: all 0.3s ease;
}

.increase-credit-btn:hover {
transform: translateY(-1px);
box-shadow: 0 4px 12px rgba(79, 209, 199, 0.3);
}

.charge-options {
background: white;
margin: 15px;
border-radius: 16px;
box-shadow: 0 2px 12px rgba(0,0,0,0.08);
max-height: 0;
overflow: hidden;
transition: all 0.5s ease;
opacity: 0;
}

.charge-options.active {
max-height: 500px;
opacity: 1;
margin-top: 0;
}

.section-title {
font-size: 16px;
font-weight: 600;
color: #2d3748;
margin-bottom: 15px;
text-align: right;
}

/* استایل برای title داخل charge-options */
.charge-options .title {
font-size: 16px;
font-weight: 600;
color: #2d3748;
margin-bottom: 15px;
text-align: right;
display: none; /* چون section-title داریم */
}

/* استایل برای ul.Charge_Wallet */
.Charge_Wallet {
list-style: none;
padding: 0;
margin: 0;
display: grid;
grid-template-columns: repeat(2, 1fr);
gap: 12px;
}

/* استایل برای li های داخل Charge_Wallet */
.Charge_Wallet li {
margin: 0;
width:100% !important;
padding: 0;
}

/* استایل برای لینک های داخل li */
.Charge_Wallet li a {
display: block;
background: #f7fafc;
border: 2px solid #e2e8f0;
border-radius: 12px;
padding: 16px 12px;
text-align: center;
cursor: pointer;
transition: all 0.3s ease;
text-decoration: none;
color: #2d3748;
font-size: 14px;
font-weight: 500;
position: relative;
}

.Charge_Wallet li a:hover {
border-color: #14b8a6;
background: #f0fdfa;
transform: translateY(-2px);
box-shadow: 0 4px 12px rgba(20, 184, 166, 0.15);
}

.Charge_Wallet li a:active {
transform: translateY(0);
}

.charge-grid {
display: grid;
grid-template-columns: repeat(2, 1fr);
gap: 10px;
margin-top: 15px;
}

.charge-option {
background: #f7fafc;
border: 2px solid #e2e8f0;
border-radius: 12px;
padding: 15px 10px;
text-align: center;
cursor: pointer;
transition: all 0.3s ease;
text-decoration: none;
color: #2d3748;
font-size: 14px;
font-weight: 500;
display: block;
}

.charge-option:hover {
border-color: #14b8a6;
background: #f0fdfa;
transform: translateY(-1px);
}

.notification-card {
background: #fff3cd;
border: 1px solid #ffeaa7;
margin: 15px;
border-radius: 12px;
padding: 15px;
}

.notification-title {
font-size: 14px;
font-weight: 600;
color: #856404;
margin-bottom: 8px;
text-align: right;
}

.notification-text {
font-size: 12px;
color: #856404;
line-height: 1.5;
text-align: right;
}

.payment-history {
background: white;
margin: 15px;
border-radius: 16px;
padding: 20px;
box-shadow: 0 2px 12px rgba(0,0,0,0.08);
}

.history-table {
width: 100%;
border-collapse: collapse;
margin-top: 15px;
}

.history-table th,
.history-table td {
padding: 12px 8px;
text-align: center;
border-bottom: 1px solid #e2e8f0;
font-size: 13px;
}

.history-table th {
background: #f7fafc;
font-weight: 600;
color: #4a5568;
}

.amount-plus {
color: #38a169;
font-weight: 600;
}

/* ریسپانسیو */
@media (max-width: 480px) {
.Charge_Wallet {
    grid-template-columns: 1fr;
}

.charge-grid {
    grid-template-columns: 1fr;
}

.modern-payment-container {
    max-width: 100%;
}
}

/* انیمیشن برای hover */
@keyframes pulse {
0% {
  box-shadow: 0 0 0 0 rgba(20, 184, 166, 0.4);
}
70% {
  box-shadow: 0 0 0 10px rgba(20, 184, 166, 0);
}
100% {
  box-shadow: 0 0 0 0 rgba(20, 184, 166, 0);
}
}

.Charge_Wallet li a:focus {
outline: none;
animation: pulse 1.5s infinite;
}
</style>

<div class="modern-payment-container">

     <?=NavDashboard();?>
 

<?php
if($verify){
    if($verify['status'] == true){
        echo '<div class="status-notification success">
                <strong>'.$verify['message'].'</strong><br>
                <small>شماره پیگیری: '.$verify["refid"].'</small>
              </div>';
    }
    else{
        echo '<div class="status-notification error">
                <strong>'.$verify['message'].'</strong><br>
                <small>علت خطا: '.$verify["error_message"].'</small>
              </div>';
    }
}
?>

<div class="wallet-card">
    <div class="wallet-balance">اعتبار شما</div>
    <div class="balance-amount <?= ($wallet - $debt_value) < 0 ? 'negative' : 'positive' ?>">
        <span class="fix-neg-l"><?= ($wallet - $debt_value) < 0 ? 'بدهکار ' : 'موجود در کیف پول ' ?></span>
        <span>
         <?=  number_format(abs($wallet - $debt_value)) ?> تومان
        </span>
    </div>
    <button class="increase-credit-btn" id="toggleChargeBtn">
        ➕ افزایش اعتبار
    </button>
</div>

<div class="charge-options" id="chargeOptions">
    <div class="section-title">شارژ کیف پول</div>
    <div class="content" style="margin: 30px 0;">

              <h1 class="title">شارژ کیف پول</h1>

              <ul class="Charge_Wallet">
                  <?php
                      if($payment['wallet1'] == ""){}else{
                          echo '
                               <li>
                                  <a href="#" id="ChargeWallet" data-charge="'.$payment['wallet1'].'" data-charge-real="'.$payment['wallet1_charge'].'">
                                      '.number_format($payment['wallet1']).' تومان
                                  </a>
                              </li>
                          ';
                      }
                  if($payment['wallet2'] == ""){}else{
                      echo '
                               <li>
                                  <a href="#" id="ChargeWallet" data-charge="'.$payment['wallet2'].'" data-charge-real="'.$payment['wallet2_charge'].'">
                                      '.number_format($payment['wallet2']).' تومان
                                  </a>
                              </li>
                          ';
                  }
                  if($payment['wallet3'] == ""){}else{
                      echo '
                               <li>
                                  <a href="#" id="ChargeWallet" data-charge="'.$payment['wallet3'].'" data-charge-real="'.$payment['wallet3_charge'].'">
                                      '.number_format($payment['wallet3']).' تومان
                                  </a>
                              </li>
                          ';
                  }
                  if($payment['wallet4'] == ""){}else{
                      echo '
                               <li>
                                  <a href="#" id="ChargeWallet" data-charge="'.$payment['wallet4'].'" data-charge-real="'.$payment['wallet4_charge'].'">
                                      '.number_format($payment['wallet4']).' تومان
                                  </a>
                              </li>
                          ';
                  }
                  if($payment['wallet5'] == ""){}else{
                      echo '
                               <li>
                                  <a href="#" id="ChargeWallet" data-charge="'.$payment['wallet5'].'" data-charge-real="'.$payment['wallet5_charge'].'">
                                      '.number_format($payment['wallet5']).' تومان
                                  </a>
                              </li>
                          ';
                  }
//                    ?>


              </ul>

          </div>
</div>

<div class="notification-card">
    <div class="notification-title">اطلاعیه مهم</div>
    <div class="notification-text">
        برای جلوگیری از وقفه در دریافت سفارشات، لطفا موجودی کیف پول خود را همواره بالای 5 میلیون تومان نگه دارید
    </div>
</div>

<div class="payment-history">
    <div class="section-title">پرداختی‌های شما</div>
    <table class="history-table">
        <tr>
            <th>#</th>
            <th>مبلغ</th>
            <th>تاریخ</th>
        </tr>
        <?php
        $financial = get_posts( array(
            "post_type" => "financial",
            "posts_per_page" => -1,
            "post_status" => "any",
            "author" => get_current_user_id(),
            "meta_query" => array(
                array(
                    "key" => "payment_status",
                    "value" => "3",
                    "compare" => "!="
                )
            )
        ) );
        $i = 0;
        foreach($financial as $item){
            $wallet = get_post_meta($item->ID,"wallet",true);
            $wallet_pay = get_post_meta($item->ID,"wallet_pay",true);
            if($wallet == 1){
                $i++;
                $status_payment = '<span class="amount-plus">+ '.number_format($wallet_pay).' تومان</span>';
                echo '<tr>
                        <td>'.$i.'</td>
                        <td>'.$status_payment.'</td>
                        <td>'.get_the_date("Y/m/d" , $item->ID).'</td>
                      </tr>';
            }
        }
        ?>
    </table>
</div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
// دکمه افزایش اعتبار
const toggleBtn = document.getElementById('toggleChargeBtn');
const chargeOptions = document.getElementById('chargeOptions');

if (toggleBtn && chargeOptions) {
    toggleBtn.addEventListener('click', function() {
        chargeOptions.classList.toggle('active');
        
        if (chargeOptions.classList.contains('active')) {
            this.textContent = '✕ بستن گزینه‌های شارژ';
            
        chargeOptions.setAttribute('style', 'padding:20px;');
        } else {
            this.textContent = '➕ افزایش اعتبار';
            
        chargeOptions.setAttribute('style', 'padding:0px;');
        }
    });
}

// فرایند شارژ کیف پول - دقیقاً مانند کد اصلی
const chargeButtons = document.querySelectorAll('#ChargeWallet');

chargeButtons.forEach(function(button) {
    button.addEventListener('click', function(e) {
        e.preventDefault();

        const charge = this.getAttribute('data-charge');
        const chargeReal = this.getAttribute('data-charge-real');

        //// ایجاد فرم مخفی
        //const form = document.createElement('form');
        //form.method = 'POST';
        //form.action = '<?php //echo admin_url("admin-ajax.php"); ?>//';
        //
        //// اضافه کردن فیلدها
        //const fields = {
        //    'action': 'charge_wallet',
        //    'charge': charge,
        //    'charge_real': chargeReal,
        //    'user_id': '<?php //echo get_current_user_id(); ?>//'
        //};
        //
        //for (const key in fields) {
        //    const input = document.createElement('input');
        //    input.type = 'hidden';
        //    input.name = key;
        //    input.value = fields[key];
        //    form.appendChild(input);
        //}
        //
        //document.body.appendChild(form);
        //form.submit();
    });
});
});
</script>

<?php
get_footer();
?>