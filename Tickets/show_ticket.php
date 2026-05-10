<?php
IFLoggedAndVerify("ticket_list");

// Template Name: Ticket Show
get_header();
if(isset($_GET['uid'])){
    $uid = $_GET['uid'];
    $ticket = get_post($uid);
    $ticket_status = get_post_meta($uid,"ticket_status",true);
    $author = $ticket->post_author;
    $sender = get_user_meta($author,"first_name","true");
    $role = get_user_meta($author,"role",true);
    if($ticket_status == 0 && $author !== get_current_user_id()){
        $update = update_post_meta($uid,"ticket_status",1);
    }
    $attachment = get_post_meta($uid,"attachment",true);
    $action = 'add';
}
$replay = get_post_meta($uid,"reply",true);

?>

    <section class="DashboardSection">

        <?=NavDashboard();?>

        <div class="main">

            <div class="content">


                <div class="ShowTicket">
                    <div class="form-group third">
                        <label>موضوع</label>
                        <span><?=$ticket->post_title?></span>
                    </div>
                    <div class="form-group third">
                        <label>تاریخ ارسال</label>
                        <span><?=get_the_date("Y/m/d",$uid)?></span>
                    </div>
                    <div class="form-group third">
                        <label>ارسال کننده</label>
                        <span><?=$sender?></span>
                    </div>

                    <div class="ticket_content">
                        <?=$ticket->post_content?>
                        <div class="DownloadBox">
                            <?php
                                if($attachment == ""){}else{
                                    echo '<a href="'.$attachment.'" target="_blank">دانلود فایل ضمیمه</a>';
                                }
                            ?>
                        </div>
                        <div class="Signature">
                            <?=Signature($author)?>
                        </div>
                    </div>
                </div>
                <?php
                    foreach($replay as $item){
                        $first_name = get_user_meta($item["sender"],"first_name",true);
                        echo '
                            <div class="ShowTicket">
                                <div class="form-group third">
                                    <label>موضوع</label>
                                    <span>'.$item["subject"].'</span>
                                </div>
                                <div class="form-group third">
                                    <label>تاریخ ارسال</label>
                                    <span>'.$item["date"].'</span>
                                </div>
                                <div class="form-group third">
                                    <label>ارسال کننده</label>
                                    <span>'.$first_name.'</span>
                                </div>
            
                                <div class="ticket_content">
                                    '.$item["content"].'
                                    <div class="DownloadBox">
                                        ';
                                        if($item["attachment"] == ""){}else{
                                            echo '<a href="'.$item["attachment"].'" target="_blank">دانلود فایل ضمیمه</a>';
                                        }
                                        echo '
                                    </div>
                                    <div class="Signature">
                                        '.Signature($item["sender"]).'
                                    </div>
                                </div>
                            </div>
                        ';
                    }
                ?>


                <div class="ReplayTicket">
                <h1 class="title"> پاسخ به تیکت <?=$uid?></h1>

                <form action="#" method="post" id="TicketReplyAjax" >

                    <input type="hidden" id="type_action" value="<?=$action?>">
                    <input type="hidden" id="uid" value="<?=$uid?>">
                    <input type="hidden" id="role" value="<?=$role?>">

                    <div class="form-group">
                        <label>موضوع</label>
                        <input type="text" class="form-control validate"  data-validate="empty" id="subject" value="<?=$subject?>">
                    </div>
                    <div class="form-group">

                        <label>پیام</label>
                        <?=wp_editor("","content",array(
                            'media_buttons' => false,
                            'textarea_rows' => 3,
                            'tabindex' => 4,
                            'tinymce' => array(
                                'theme_advanced_buttons1' => 'bold, italic, ul, min_size, max_size',
                            ),
                        ))?>

                    </div>
                    <div class="form-group ">

                        <label>پیوست</label>
                        <div class="Img_selector">
                            <div class="loading">

                            </div>
                            <div class="thumb"></div>
                            <div class="box_upload">
                                <?=file_get_contents(wp_get_attachment_url(97));?>
                                <span>برای بارگذاری تصویر (بکشید / کلیک کنید)</span>
                                <input class="imgUpload" type="file" >
                                <input class="source_image" type="hidden" id="attachment" >
                            </div>
                        </div>
                    </div>

                    <input type="submit" class="btn" value="ارسال">

                </form>
                </div>
            </div>

        </div>
    </section>

<?php
get_footer();

?>