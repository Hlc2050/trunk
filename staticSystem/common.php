<script>
    var ewm = document.getElementsByClassName("ew_img");
    for (var i = 0; l = ewm.length, i < l; i++) {
        var img = document.createElement("img");
        img.src = "<?php echo $wechat_img ;?>";
        img.setAttribute('data-original', <?php echo '"' . $wechat_img . '"';?>);
        ewm[i].appendChild(img);
    }
    var aImgs = document.getElementsByTagName('img');
    console.log(aImgs.length);
    for (var j = 0; l = aImgs.length, j < l; j++) {
        console.log(aImgs[j].getAttribute('src').indexOf('http'));
        if (aImgs[j].getAttribute('src').indexOf('http') == -1) {
            aImgs[j].setAttribute('src', <?php echo '"' . $page->cdn_url . '"';?>+aImgs[j].getAttribute('src'));
        }
    }
//    $(".ew_img").append('<img src="<?php //echo $wechat_img ;?>//">');
</script>
<div style="display:none">
    <?php echo $page->independent_cnzz; ?>
    <?php echo $page->total_cnzz; ?>
</div>

</body>
</html>