<?php
$tdk_title='回馈活动火热进行中';
$path = 'goods/'.$gd.'/'.$version .'/';
$time = date("H:i:s",time());
$file = $path."click.txt";
if($time >= '23:00:00' && $time<='23:59:59' )
{
$date = "1324";
}
else
{
$content = file_get_contents($file);
$array = explode("\r\n", $content);
if ($array[0] >= 2000){
$date = "1324";
}else if($array[0] == ""){
$date = "1324";
}else{
$date = $array[0]+1;
}

}
file_put_contents($file, $date);
$content = file_get_contents($file);
$array = explode("\r\n", $content);
$online_num = $array[0];
?>
<div id="z_main">
<!--    <img src="image/ablym/5.8/1.jpg" width="100%"> -->

   <img src="image/ablym/7.0/2.jpg" width="100%">
   <img src="http://wapimg.52zine.com/image/ablym/7.0/3.jpg" width="100%" usemap="#Map" >
   <map name="Map">
     <area shape="rect" coords="11,29,192,92" href="#mflq">
  </map>
   <img src="image/ablym//7.0/4.jpg" width="100%">

<!--
  <div class="z_2">
      <div class="z_t">
          <p class="z_t2"><?php echo $online_num; ?> </p>
          <p class="z_t3"><a href="#mflq"><img src="image/ablym_3.0/15.png"></a></p>
      </div>
      <img width="100%" src="image/ablym/5.8/8.jpg">
   </div>
-->
<!--
<div class="zs_24">
   <div><b>如果你还在担心自己无法给自己的女人完美的生活，</b></div>
   <div><b>如果你还在忍受妻子的冷嘲与热讽，</b></div>
   <div><b>如果你不能让她真正满足一次，</b></div>
   <div><b>如果因此你们的婚姻快要被摧毁，</b></div>
   <div><b>请强劲起来用行动告诉她，你真的行！爱她，就要让她体验完美幸福！</b></div>
   <div><b>给自己一个机会，证明你自己！</b></div>
</div>
-->

    <div class="zs_14" id="zs_14">
        <?php
        $float_dingou = "";
        $ding_btn = "";
        include_once ('lib/lib_ordergive_0.php');
        ?>
</div>
