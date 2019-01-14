<!DOCTYPE html>
<html lang="en">

<head>
    <?php flush();?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0">
    <title>问答测试</title>
    <link rel="stylesheet" href="<?php echo  $page['info']['css_cdn_url']; ?>/static/front/css/reset.css">
    <link rel="stylesheet" href="<?php echo  $page['info']['css_cdn_url']; ?>/static/front/css/style.css">
    <script src="<?php echo  $page['info']['css_cdn_url']; ?>/static/front/js/voteFlexble.js"></script>
    <script type="text/javascript" src="<?php echo  $page['info']['css_cdn_url']; ?>/static/front/js/jquery.min-1.11.3.js"></script>
    <script>
        $(function () {

            $('.one,.two,.three,.four').on('click', function () {
                var v= parseInt($('#qsp-id').html());
                var c= parseInt($('.question-number').html());
                var array= <?php echo json_encode($page['psq']);?>;
                $('.one,.two,.three,.four').removeClass('active');
                $(this).addClass('active');
                setTimeout(function(){
                    if(c>0){
                        var d=v+1;
                        $('.question-tests').html(array[d]['quest_title']);
                        $('.one').html(array[d]['tab_a']);
                        $('.two').html(array[d]['tab_b']);
                        $('.three').html(array[d]['tab_c']);
                        $('.four').html(array[d]['tab_d']);
                        $('#qsp-id').html(d);
                        $('.question-number').html(c-1);
                        $('.one,.two,.three,.four').removeClass('active');
                    }
                },300);
            })
        });
        function lastQsp() {
            var v= parseInt($('#qsp-id').html());
            var c= parseInt($('.question-number').html());
            //  alert(window.location.href);
            if(v>0){
                var d=v-1;
                var array= <?php echo json_encode($page['psq']);?>;
                $('.one,.two,.three,.four').removeClass('active');
                $('.one').html(array[d]['tab_a']);
                $('.two').html(array[d]['tab_b']);
                $('.three').html(array[d]['tab_c']);
                $('.four').html(array[d]['tab_d']);
                $('#qsp-id').html(d);
                $('.question-number').html(c+1);

            }
        }

        function jump() {
            var c= parseInt($('.question-number').html());
            if(c>0){
                alert("还有题未选!")
            }else {
                var url = window.location.href;
                str=url.match("html$");
                if(str==null) {
                    var new_url = url + '/1';
                }else {
                    new_url=url.substring(0,url.length-5)
                    new_url=new_url+"/1.html";
                    console.log(new_url);
                }
                window.location = new_url
            }
        }
    </script>
</head>

<body style="background-color:#2ab32e;">
<?php if ($page['top_img'] != '') { ?>
    <div class="qa-banner"><img src="<?php echo $page['top_img']; ?>"></div>
<?php }
if ($page['vote_page'] != '') { ?>
    <div class="header-careTip"><?php echo $page['vote_page'] ?></div>
<?php }
if ($page['vote_page'] != '') { ?>
    <div class="header-tip">
        <img src="<?php echo Yii::app()->params['basic']['cssurl']; ?>front/images/header-tip.png">
        <p><?php echo $page['tip'] ?></p>
    </div>
<?php } ?>
<div class="test"><?php echo $page['vote_title'] ?></div>
<div class="question-test">
    <div class="question-test-header"><img onclick="lastQsp()"
                                           src="<?php echo  $page['info']['cdn_url']; ?>/static/front/images/lastquestion.png">
        <p>剩<span class="question-number"><?php echo $page['psq_count'] - 1 ?></span>题</p>
    </div>
    <div class="question-tests"><?php echo $page['psq'][0]['quest_title'] ?></div>
    <div id="qsp-id" hidden>0</div>
    <div class="question-test-content">
        <div class="question-test-middle">
            <div class="one answer"><?php echo $page['psq'][0]['tab_a'] ?></div>
            <div class="two answer"><?php echo $page['psq'][0]['tab_b'] ?></div>
        </div>
        <div class="question-test-bottom">
            <div class="three answer"><?php echo $page['psq'][0]['tab_c'] ?></div>
            <div class="four answew"><?php echo $page['psq'][0]['tab_d'] ?></div>
        </div>
    </div>
</div>
<div class="submit-button"><img onclick="jump();" src="<?php echo  $page['info']['cdn_url']; ?>/static/front/images/yellow-button.png"></div>
<!--<button type="submit">提交</button>-->
<div class="footer-tip"><?php echo $page['bottom_tip'] ?></div>
</body>

</html>