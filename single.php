<?php
get_header();
while ( have_posts() ) : the_post();
$id = get_the_ID();
$author = AuthorDetail(get_the_author_meta('ID'));

?>
<div class="main InnerMain Single">

    <div class="row">

        <div class="col-md-9">

            <div class="articleSingle">
                <div class="article_header">
                    <ul class="optionArticle">
                        <li class="shareArticle" id="<?=$id?>">
                            <i class="fi fi-share-a"></i>
                            <span>اشتراک گذاری</span>
                            <div class="ArticleShareBOx">
                                <ul>
                                    <li><a href="#"><i class="fi fi-whatsapp"></i><span>واتس اپ</span></a> </li>
                                    <li><a href="#"><i class="fi fi-telegram"></i><span>تلگرام</span></a> </li>
                                    <li><a href="#"><i class="fi fi-telegram"></i><span>لینک کوتاه</span></a> </li>
                                </ul>
                            </div>
                        </li>
                        <?=SavedArticle($id)?>

                    </ul>
                    <button class="BTNBack" onclick="history.back();">بازگشت</button>

                </div>
                <img src="<?=get_the_post_thumbnail_url($id)?>" class="articleImage">
                <div class="articleDescripton">

                    <h1><?=the_title();?></h1>
                    <div class="descrioption">
                        <?=the_content();?>
                    </div>
                </div>
            </div>

        </div>
        <div class="col-md-3">
            <?=HeaderBoxInner("innerHeader");?>

            <div class="CommentSingle">
                <div class="commentList"><?=listOfComment($id);?></div>
                <div class="commentAdd">
                    <form action="#" id="addComment">
                        <input type="hidden" id="c_id" value="<?=$id?>">
                        <div class="form-group half" style="display: none">
                            <label>نام شما</label>
                            <input type="text" data-validate="empty" class="form-control validate" name="c_name" id="c_name" value="<?=Get_User_Date()["name"]?>">
                        </div>
                        <div class="form-group">
                            <label>دیدگاه شما</label>
                            <textarea class="form-control validate" data-validate="empty" name="c_comment" id="c_comment"></textarea>
                        </div>
                        <input type="submit" class="btn" value="ارسال دیدگاه">
                    </form>
                </div>
            </div>

        </div>

    </div>

</div>
<?php
endwhile;
get_footer();
?>
