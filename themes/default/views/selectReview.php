<link rel="stylesheet" href="<?php echo  $page['info']['css_cdn_url']; ?>/static/front/css/reset.css">
<link rel="stylesheet" href="<?php echo  $page['info']['css_cdn_url']; ?>/static/front/css/style.css">
<link href="https://cdn.bootcss.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">

<div class="select-message-container" >
    <div class="select-message">精选留言</div>
    <div class="fuzLine">
        <div class="left"></div>
        <div class="right"></div>
    </div>
    <div class="leave-messeges">写留言<i class="fa fa-pencil" aria-hidden="true"></i></div>
</div>
<div id="selectReview" name="selectReview">
<?php foreach ($ret as $k => $v) { ?>
    <div class="conment-container">
        <!--用户评论-->
        <div class="user-img"><img src="<?php echo $v[0]['avatar_url'] ?>"></div>
        <div class="user-conments">
            <div class="userNames"><span class="userName"><?php echo $v[0]['review_name'] ?></span><span
                    class="user-praise"><i class="fa fa-thumbs-o-up" aria-hidden="true"></i><?php echo mt_rand(60,600)?></span></div>
            <div class="user-conment">
                <?php echo $v[0]['review_content'] ?>
                <p class="user-conment-time"><?php echo $v[0]['review_date'] ?></p>
            </div>
            <?php if ($v[1]) { ?>
                <!--作者回复-->
                <div class="author-conments">
                    <div class="authorNames">
                        <span class="authorName"><i class="fa fa-window-minimize" aria-hidden="true"></i>作者回复</span><span
                            class="author-praise"><i class="fa fa-thumbs-o-up" aria-hidden="true"></i><?php echo mt_rand(60,600)?></span></div>
                    <div class="author-conment">
                        <?php echo $v[1]['review_content'] ?>
                        <p class="author-conment-time"><?php echo $v[1]['review_date'] ?></p>

                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
<?php } ?>
</div>