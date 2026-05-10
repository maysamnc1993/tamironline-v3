<?php

/**
 * Metabox: تاریخ + بازه‌های باز
 * UI: Input + Date-picker جلالی + Toggle switches برای بازه‌ها
 *
 * @package Tamir\Holidays
 */

namespace Tamir\Holidays;

defined('ABSPATH') || exit;

final class HolidayMetaBox
{
    public const NONCE_ACTION = 'tamir_holiday_save';
    public const NONCE_FIELD  = 'tamir_holiday_nonce';

    public static function register(): void
    {
        add_meta_box(
            'tamir_holiday_meta',
            'اطلاعات تعطیلی',
            [self::class, 'render'],
            HolidayPostType::POST_TYPE,
            'normal',
            'high'
        );
    }

    public static function render(\WP_Post $post): void
    {
        wp_nonce_field(self::NONCE_ACTION, self::NONCE_FIELD);

        $date = (string) get_post_meta($post->ID, HolidayPostType::META_DATE, true);
        $savedSlots = get_post_meta($post->ID, HolidayPostType::META_SLOTS, true);
        $savedSlots = is_array($savedSlots) ? $savedSlots : [];
        // پیش‌فرض برای سند جدید: همه بسته (هیچ چک‌نخورده)
        $slotsConfig = TimeSlotsConfig::all();
?>
        <style>
            .tamir-hf {
                margin-bottom: 18px;
            }

            .tamir-hf label.lbl {
                display: block;
                font-weight: 600;
                margin-bottom: 6px;
            }

            .tamir-hf input[type=text] {
                width: 220px;
                font-size: 15px;
                padding: 6px 10px;
                direction: ltr;
                text-align: center;
            }

            .tamir-hint {
                color: #666;
                font-size: 12px;
                margin-top: 4px;
            }

            .tamir-err {
                color: #b32d2e;
                font-size: 12px;
                margin-top: 4px;
                display: none;
            }

            .tamir-ok {
                color: #00a32a;
                font-size: 12px;
                margin-top: 4px;
                display: none;
            }

            /* Toggle switches */
            .tamir-slots-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
                gap: 10px;
                margin-top: 8px;
                max-width: 640px;
            }

            .tamir-slot-row {
                display: flex;
                align-items: center;
                justify-content: space-between;
                background: #f6f7f7;
                border: 1px solid #dcdcde;
                border-radius: 6px;
                padding: 10px 14px;
            }

            .tamir-slot-row.is-on {
                background: #f0f6fc;
                border-color: #2271b1;
            }

            .tamir-slot-label {
                font-size: 13px;
                font-weight: 600;
                color: #1d2327;
            }

            .tamir-slot-row.is-on .tamir-slot-label {
                color: #2271b1;
            }

            .tamir-slot-value {
                font-size: 11px;
                color: #787c82;
                direction: ltr;
                font-family: ui-monospace, monospace;
                margin-top: 2px;
                display: block;
            }

            /* Toggle switch */
            .tamir-toggle {
                position: relative;
                display: inline-block;
                width: 42px;
                height: 22px;
                flex-shrink: 0;
            }

            .tamir-toggle input {
                opacity: 0;
                width: 0;
                height: 0;
            }

            .tamir-toggle .slider {
                position: absolute;
                cursor: pointer;
                inset: 0;
                background: #c3c4c7;
                border-radius: 22px;
                transition: .15s;
            }

            .tamir-toggle .slider:before {
                position: absolute;
                content: "";
                height: 16px;
                width: 16px;
                left: 3px;
                top: 3px;
                background: #fff;
                border-radius: 50%;
                transition: .15s;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15);
            }

            .tamir-toggle input:checked+.slider {
                background: #2271b1;
            }

            .tamir-toggle input:checked+.slider:before {
                transform: translateX(20px);
            }

            /* Bulk controls */
            .tamir-bulk-bar {
                display: flex;
                gap: 8px;
                margin-top: 10px;
            }

            .tamir-bulk-bar button {
                font-size: 12px;
            }

            /* Summary pill */
            .tamir-slot-summary {
                display: inline-block;
                margin-top: 10px;
                padding: 4px 10px;
                border-radius: 12px;
                font-size: 12px;
                font-weight: 600;
                background: #fcf9e8;
                color: #8a6d00;
                border: 1px solid #f0e3a0;
            }

            .tamir-slot-summary.all-closed {
                background: #fcecea;
                color: #b32d2e;
                border-color: #f4c6c0;
            }

            .tamir-slot-summary.all-open {
                background: #e6f4ea;
                color: #00632f;
                border-color: #b5dcc0;
            }
        </style>

        <!-- ── تاریخ ───────────────────────────────────── -->
        <div class="tamir-hf">
            <label class="lbl" for="tamir_holiday_date">تاریخ شمسی تعطیلی <span style="color:#b32d2e">*</span></label>
            <input type="text"
                id="tamir_holiday_date"
                name="tamir_holiday_date"
                value="<?php echo esc_attr($date); ?>"
                placeholder="۱۴۰۵-۰۱-۱۳"
                autocomplete="off"
                required
                pattern="^\d{4}-\d{2}-\d{2}$"
                maxlength="10">
            <button type="button" class="button" id="tamir-holiday-pickbtn" style="margin-right:6px">📅 انتخاب از تقویم</button>
            <p class="tamir-hint">فرمت: <code>YYYY-MM-DD</code> — مثال: <code>1405-01-13</code></p>
            <p class="tamir-err" id="tamir-date-err"></p>
            <p class="tamir-ok" id="tamir-date-ok">✓ تاریخ معتبر است</p>
        </div>

        <div id="tamir-holiday-picker-wrap" style="display:none; margin-top:8px; padding:16px; background:#f6f7f7; border:1px solid #ddd; border-radius:4px; max-width:320px;"></div>

        <!-- ── بازه‌های باز ──────────────────────────────── -->
        <div class="tamir-hf" style="margin-top:24px">
            <label class="lbl">بازه‌های قابل رزرو در این روز</label>
            <p class="tamir-hint" style="margin-top:0; margin-bottom:8px">
                فقط بازه‌هایی که <strong>روشن</strong> باشند، برای مشتریان در این روز باز خواهند بود.
                بازه‌های خاموش غیرقابل رزرو می‌شوند.
            </p>

            <div class="tamir-bulk-bar">
                <button type="button" class="button button-small" id="tamir-slots-all-on">همه روشن</button>
                <button type="button" class="button button-small" id="tamir-slots-all-off">همه خاموش</button>
                <button type="button" class="button button-small" id="tamir-slots-middle">فقط وسط (۱۲-۱۴ و ۱۵-۱۷)</button>
            </div>

            <div class="tamir-slots-grid">
                <?php foreach ($slotsConfig as $slot):
                    $checked = in_array($slot['value'], $savedSlots, true); ?>
                    <label class="tamir-slot-row <?php echo $checked ? 'is-on' : ''; ?>" data-slot="<?php echo esc_attr($slot['value']); ?>">
                        <div>
                            <span class="tamir-slot-label"><?php echo esc_html($slot['label']); ?></span>
                            <span class="tamir-slot-value"><?php echo esc_html($slot['value']); ?></span>
                        </div>
                        <span class="tamir-toggle">
                            <input type="checkbox"
                                name="tamir_holiday_open_slots[]"
                                value="<?php echo esc_attr($slot['value']); ?>"
                                <?php checked($checked); ?>>
                            <span class="slider"></span>
                        </span>
                    </label>
                <?php endforeach; ?>
            </div>

            <span class="tamir-slot-summary" id="tamir-slot-summary"></span>
        </div>
<?php
    }

    /**
     * ذخیره
     */
    public static function save(int $post_id, \WP_Post $post): void
    {
        if (
            ! isset($_POST[self::NONCE_FIELD]) ||
            ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST[self::NONCE_FIELD])), self::NONCE_ACTION)
        ) {
            return;
        }
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if ($post->post_type !== HolidayPostType::POST_TYPE) return;
        if (! current_user_can('edit_post', $post_id)) return;

        // تاریخ
        $raw = isset($_POST['tamir_holiday_date'])
            ? sanitize_text_field(wp_unslash($_POST['tamir_holiday_date']))
            : '';
        $raw = self::toEnglishDigits($raw);

        if (! HolidayRepository::isValidJalaliDate($raw)) {
            delete_post_meta($post_id, HolidayPostType::META_DATE);
            add_filter('redirect_post_location', function ($loc) {
                return add_query_arg('tamir_holiday_error', '1', $loc);
            });
            return;
        }
        update_post_meta($post_id, HolidayPostType::META_DATE, $raw);

        // بازه‌های باز
        $rawSlots = $_POST['tamir_holiday_open_slots'] ?? [];
        if (! is_array($rawSlots)) $rawSlots = [];
        $rawSlots = array_map(function ($s) {
            return sanitize_text_field((string) $s);
        }, $rawSlots);
        $clean = TimeSlotsConfig::sanitizeList($rawSlots);
        update_post_meta($post_id, HolidayPostType::META_SLOTS, $clean);
    }

    private static function toEnglishDigits(string $s): string
    {
        $fa = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $ar = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        $en = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        return str_replace(array_merge($fa, $ar), array_merge($en, $en), $s);
    }

    public static function enqueueAssets(string $hook): void
    {
        if (! in_array($hook, ['post.php', 'post-new.php'], true)) return;
        $screen = get_current_screen();
        if (! $screen || $screen->post_type !== HolidayPostType::POST_TYPE) return;

        wp_register_script('tamir-holiday-admin', '', [], '1.1.0', true);
        wp_enqueue_script('tamir-holiday-admin');
        wp_add_inline_script('tamir-holiday-admin', self::inlineJs());

        if (isset($_GET['tamir_holiday_error'])) {
            add_action('admin_notices', function () {
                echo '<div class="notice notice-error is-dismissible"><p><strong>خطا:</strong> تاریخ شمسی نامعتبر است.</p></div>';
            });
        }
    }

    /**
     * JS: validation + date-picker + toggle interactions + summary
     */
    private static function inlineJs(): string
    {
        return <<<'JS'
(function () {
    // ── Jalali math (روزبه پورنادر) ──────────────────────────
    function div(a,b){return Math.floor(a/b);}
    function g2j(gy,gm,gd){
        var g_d_m=[0,31,59,90,120,151,181,212,243,273,304,334];
        var gy2=(gm>2)?gy+1:gy;
        var days=355666+365*gy+div((gy2+3),4)-div((gy2+99),100)+div((gy2+399),400)+gd+g_d_m[gm-1];
        var jy=-1595+33*div(days,12053); days%=12053;
        jy+=4*div(days,1461); days%=1461;
        if(days>365){jy+=div((days-1),365); days=(days-1)%365;}
        var jm,jd;
        if(days<186){jm=1+div(days,31); jd=1+(days%31);}
        else{jm=7+div((days-186),30); jd=1+((days-186)%30);}
        return [jy,jm,jd];
    }
    function j2g(jy,jm,jd){
        jy+=1595;
        var days=-355668+365*jy+div(jy,33)*8+div(((jy%33)+3),4)+jd
                 +((jm<7)?(jm-1)*31:(jm-7)*30+186);
        var gy=400*div(days,146097); days%=146097;
        if(days>36524){gy+=100*div(--days,36524); days%=36524; if(days>=365)days++;}
        gy+=4*div(days,1461); days%=1461;
        if(days>365){gy+=div((days-1),365); days=(days-1)%365;}
        var gd=days+1;
        var isLeap=((gy%4===0 && gy%100!==0)||gy%400===0);
        var sal_a=[0,31,isLeap?29:28,31,30,31,30,31,31,30,31,30,31];
        var gm=0;
        for(;gm<13 && gd>sal_a[gm];gm++){ gd-=sal_a[gm]; }
        return [gy,gm,gd];
    }
    function isValidJalali(y,m,d){
        if(y<1300||y>1500) return false;
        if(m<1||m>12) return false;
        if(d<1||d>31) return false;
        if(m>=7 && m<=11 && d>30) return false;
        if(m===12 && d>30) return false;
        return true;
    }
    function daysInJMonth(y,m){
        if(m<=6) return 31;
        if(m<=11) return 30;
        // اسفند: بررسی کبیسه از طریق تبدیل
        var g1=j2g(y,12,30); var g2=j2g(y+1,1,1);
        var d1=new Date(g1[0],g1[1]-1,g1[2]).getTime();
        var d2=new Date(g2[0],g2[1]-1,g2[2]).getTime();
        return Math.round((d2-d1)/86400000)>=1?30:29;
    }
    function pad(n){return n<10?'0'+n:''+n;}
    function toEn(s){
        return s.replace(/[۰-۹]/g,function(c){return '0123456789'[c.charCodeAt(0)-0x06F0];})
                .replace(/[٠-٩]/g,function(c){return '0123456789'[c.charCodeAt(0)-0x0660];});
    }
    function todayJ(){var d=new Date(); return g2j(d.getFullYear(),d.getMonth()+1,d.getDate());}

    // ── Date validation ─────────────────────────────────────
    var input=document.getElementById('tamir_holiday_date');
    var errEl=document.getElementById('tamir-date-err');
    var okEl =document.getElementById('tamir-date-ok');
    function validate(){
        if(!input) return;
        var v=toEn((input.value||'').trim()); input.value=v;
        errEl.style.display='none'; okEl.style.display='none';
        if(!v) return;
        var m=v.match(/^(\d{4})-(\d{2})-(\d{2})$/);
        if(!m){ errEl.textContent='فرمت نامعتبر. باید YYYY-MM-DD باشد.'; errEl.style.display='block'; return; }
        if(!isValidJalali(+m[1],+m[2],+m[3])){ errEl.textContent='این تاریخ شمسی معتبر نیست.'; errEl.style.display='block'; return; }
        okEl.style.display='block';
    }
    if(input){ input.addEventListener('input',validate); input.addEventListener('blur',validate); validate(); }

    // ── Toggle interactions ─────────────────────────────────
    var rows=document.querySelectorAll('.tamir-slot-row');
    var summary=document.getElementById('tamir-slot-summary');

    function refreshSummary(){
        if(!summary) return;
        var total=rows.length;
        var on=document.querySelectorAll('.tamir-slot-row input[type=checkbox]:checked').length;
        summary.classList.remove('all-closed','all-open');
        if(on===0){ summary.classList.add('all-closed'); summary.textContent='⚠ همه بازه‌ها بسته — این روز کاملاً غیرقابل رزرو است.'; }
        else if(on===total){ summary.classList.add('all-open'); summary.textContent='✓ همه بازه‌ها باز — مثل روز عادی (شاید بخواهید یکی را ببندید).'; }
        else { summary.textContent=on+' از '+total+' بازه باز است.'; }
    }

    rows.forEach(function(row){
        var cb=row.querySelector('input[type=checkbox]');
        if(!cb) return;
        function sync(){
            if(cb.checked) row.classList.add('is-on'); else row.classList.remove('is-on');
            refreshSummary();
        }
        cb.addEventListener('change', sync);
        // کلیک روی row (نه checkbox) = toggle
        row.addEventListener('click', function(e){
            if(e.target.tagName==='INPUT' || e.target.closest('.tamir-toggle')) return;
            cb.checked=!cb.checked; sync();
        });
    });
    refreshSummary();

    // ── Bulk buttons ────────────────────────────────────────
    function setAll(state){
        rows.forEach(function(row){
            var cb=row.querySelector('input[type=checkbox]');
            if(cb){ cb.checked=state; cb.dispatchEvent(new Event('change')); }
        });
    }
    var allOn=document.getElementById('tamir-slots-all-on');
    var allOff=document.getElementById('tamir-slots-all-off');
    var mid=document.getElementById('tamir-slots-middle');
    if(allOn)  allOn.addEventListener('click', function(){ setAll(true); });
    if(allOff) allOff.addEventListener('click',function(){ setAll(false); });
    if(mid) mid.addEventListener('click',function(){
        rows.forEach(function(row){
            var cb=row.querySelector('input[type=checkbox]');
            if(!cb) return;
            var v=row.getAttribute('data-slot');
            cb.checked=(v==='12-14'||v==='15-17');
            cb.dispatchEvent(new Event('change'));
        });
    });

    // ── Date-picker (same as before) ─────────────────────────
    var btn=document.getElementById('tamir-holiday-pickbtn');
    var wrap=document.getElementById('tamir-holiday-picker-wrap');
    if(!btn||!wrap) return;
    var MONTHS=['فروردین','اردیبهشت','خرداد','تیر','مرداد','شهریور','مهر','آبان','آذر','دی','بهمن','اسفند'];
    var WEEKDAYS=['ش','ی','د','س','چ','پ','ج'];
    var today=todayJ();
    var viewY=today[0], viewM=today[1];
    var cur=(input.value||'').match(/^(\d{4})-(\d{2})-(\d{2})$/);
    if(cur){ viewY=+cur[1]; viewM=+cur[2]; }

    function firstWeekday(y,m){
        var g=j2g(y,m,1);
        var js=new Date(g[0],g[1]-1,g[2]).getDay();
        return (js+1)%7;
    }

    function render(){
        var days=daysInJMonth(viewY,viewM);
        var start=firstWeekday(viewY,viewM);
        var html='';
        html+='<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px">';
        html+='<button type="button" class="button button-small" data-nav="-1">‹</button>';
        html+='<strong>'+MONTHS[viewM-1]+' '+viewY+'</strong>';
        html+='<button type="button" class="button button-small" data-nav="1">›</button>';
        html+='</div>';
        html+='<div style="display:grid;grid-template-columns:repeat(7,1fr);gap:2px;text-align:center;font-size:11px;direction:rtl">';
        for(var i=0;i<7;i++) html+='<div style="padding:4px;font-weight:600;color:#666">'+WEEKDAYS[i]+'</div>';
        for(var j=0;j<start;j++) html+='<div></div>';
        for(var d=1;d<=days;d++){
            var isToday=(viewY===today[0]&&viewM===today[1]&&d===today[2]);
            var isSel=cur && +cur[1]===viewY && +cur[2]===viewM && +cur[3]===d;
            var st='cursor:pointer;padding:6px 0;border-radius:4px;background:'+
                (isSel?'#2271b1':(isToday?'#f0f6fc':'#fff'))+
                ';color:'+(isSel?'#fff':'#1d2327')+
                ';border:1px solid '+(isSel?'#2271b1':'#ddd')+';';
            var col=(j+d-1+start)%7;
            if(col===6 && !isSel) st+='color:#b32d2e;';
            html+='<div data-day="'+d+'" style="'+st+'">'+d+'</div>';
        }
        html+='</div>';
        wrap.innerHTML=html;
        wrap.querySelectorAll('[data-nav]').forEach(function(b){
            b.addEventListener('click',function(){
                var dir=+b.getAttribute('data-nav');
                viewM+=dir;
                if(viewM<1){viewM=12; viewY--;}
                if(viewM>12){viewM=1; viewY++;}
                render();
            });
        });
        wrap.querySelectorAll('[data-day]').forEach(function(el){
            el.addEventListener('click',function(){
                var d=+el.getAttribute('data-day');
                var val=viewY+'-'+pad(viewM)+'-'+pad(d);
                input.value=val;
                cur=val.match(/^(\d{4})-(\d{2})-(\d{2})$/);
                validate();
                render();
            });
        });
    }
    btn.addEventListener('click',function(){
        wrap.style.display=(wrap.style.display==='none'?'block':'none');
        if(wrap.style.display==='block') render();
    });
})();
JS;
    }
}
