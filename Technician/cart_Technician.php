<?php
// Template Name: Technician Panel

get_header();

// ابتدا بررسی می‌کنیم که کاربر وارد سیستم شده است یا خیر
if (is_user_logged_in()) {
    // دریافت شناسه کاربر وارد شده
    $current_user_id = get_current_user_id();

    // دریافت اطلاعات تکنسین از متا داده‌های کاربر
    $firstname = get_user_meta($current_user_id, "first_name", true);
    $lastname = get_user_meta($current_user_id, "last_name", true);
    $firstname_tech = get_user_meta($current_user_id, "firstname_tech", true);
    $technician_id = get_current_user_id();
    $cart_img = get_user_meta($current_user_id, "cart_img", true);
    $specialty = get_user_meta($current_user_id, "specialty", true);
    $province = get_user_meta($current_user_id, "province", true);
    // بررسی اینکه آیا داده‌ای برای تخصص‌ها وجود دارد یا خیر
    $specialty_array = !empty($specialty) ? explode(",", $specialty) : [];
    
    // نمایش اطلاعات تکنسین در قالب پنل
    ?>

    <section class="DashboardSection CartDashboard">
        <?=NavDashboard();?>
        <div class="main">
            <div class="Cart-Technician-style">
                <div class="content">
                    <h1 class="title-tech-cart">کارت شناسایی تکنسین</h1>
                    <div class="technician-info-cart">
                        <p class="profile-cart-tech"><img src="<?php echo $cart_img; ?>" alt="تصویر"></p>
                        <p><strong>نام و نام خانوادگی تکنسین: </strong></br><?php echo $firstname_tech; ?></p>
                        <p><strong>کد شناسایی: </strong><?php echo $technician_id; ?></p>
                        <p><strong>محدوده: </strong><?php echo $province ? $province : "مشخص نشده"; ?></p>
                        <p><strong>تخصص‌ها: </strong></p>
                        <ul class="style-technician-info-cart">
                            <?php
                            if (!empty($specialty_array)) {
                                foreach ($specialty_array as $spec) {
                                    echo "<li>" . esc_html($spec) . "</li>";
                                }
                            } else {
                                echo "<li>تخصصی انتخاب نشده است</li>";
                            }
                            ?>
                        </ul>
                        <img  src="https://crm.tamironline.com/wp-content/uploads/2024/09/5.png" id="DashboardLogo-cartTech">
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php
} else {
    // اگر کاربر وارد نشده است، پیام ورود را نمایش می‌دهیم
    echo '<p>لطفا وارد حساب کاربری خود شوید.</p>';
}

get_footer();
?>
