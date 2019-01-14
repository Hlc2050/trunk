<div id="swiper_change"  style="display: none">
    <div class="change clf">
        <div id="case">
            <input type="hidden" id="psq_num" value="<?php echo $psqNum; ?>">
            <div id="swiper_container_hide">
                <div class="swiper-container swiper-container-horizontal">
                    <div class="swiper-wrapper notmove" style="transition-duration: 0ms;">
                        <div class="swiper-slide swiper-slide-active" style="width: 586px;">
                            <img src="<?php echo $page['info']['cdn_url']; ?>/static/front/images/test.png" style="margin: 0 ">
                            <div class="first" id="first">
                                <div class="tips"<?php if ($psqNum == 1) echo "style='height:2rem'" ?>>

                                    剩<?php $psqNum = $psqNum - 1;
                                    echo $psqNum; ?>题
                                </div>
                                <h3 style="color: rgb(136, 194, 11); margin-top: 0px; font-size: 1.1rem; font-weight: normal;">
                                    <?php echo $psqList[0]['vote_title'] ?></h3>
                                <h3>1.<?php echo $psqList[0]['quest_title'] ?>？</h3>
                                <ul class="clf">
                                    <li><?php echo $psqList[0]['tab_a'] ?></li>
                                    <li><?php echo $psqList[0]['tab_b'] ?></li>
                                    <li><?php echo $psqList[0]['tab_c'] ?></li>
                                    <li><?php echo $psqList[0]['tab_d'] ?></li>
                                </ul>
                                <?php if ($psqNum == 0) { ?>
                                    <p class="submit_btn"><a href="javascript:void(0);" class="xinjia Link"><span class="fl"
                                                                                                                  style="color:#ffffff; font-size:0.9rem;">提交</span></a>
                                    </p>
                                <?php } ?>
                            </div>
                        </div>
                        <?php if ($psqNum != 0) { ?>
                            <div class="swiper-slide swiper-slide-next" style="width: 586px;">
                                <img src="<?php echo $page['info']['cdn_url']; ?>/static/front/images/test.png" style="margin: 0 ">
                                <div class="first" id="second">
                                    <div class="tips clf"><span
                                            class="fl prev">上一题</span><?php $psqNum = $psqNum - 1;
                                        echo $psqNum == 0 ? '' : '剩' . $psqNum . '题'; ?></div>
                                    <h3 style="margin-top: 0px;">2.<?php echo $psqList[1]['quest_title'] ?>
                                        ?</h3>
                                    <ul class="clf" id="second">
                                        <li><?php echo $psqList[1]['tab_a'] ?></li>
                                        <li><?php echo $psqList[1]['tab_b'] ?></li>
                                        <li><?php echo $psqList[1]['tab_c'] ?></li>
                                        <li><?php echo $psqList[1]['tab_d'] ?></li>

                                    </ul>
                                    <?php if ($psqNum == 0) { ?>
                                        <p class="submit_btn"><a href="javascript:void(0);" class="xinjia Link"><span class="fl"
                                                                                                                      style="color:#ffffff; font-size:0.9rem;">提交</span></a>
                                        </p>
                                    <?php } ?>
                                </div>
                            </div>
                            <?php if ($psqNum != 0) { ?>
                                <div class="swiper-slide swiper-slide-next" style="width: 586px;">
                                    <img
                                        src="<?php echo $page['info']['cdn_url']; ?>/static/front/images/test.png" style="margin: 0;">
                                    <div class="first" id="third">
                                        <div class="tips clf"><span
                                                class="fl prev">上一题</span><?php $psqNum = $psqNum - 1;
                                            echo $psqNum == 0 ? '' : '剩' . $psqNum . '题'; ?></div>
                                        <h3 style="margin-top: 0px;">3.<?php echo $psqList[2]['quest_title'] ?>
                                            ?</h3>
                                        <ul class="clf" id="third">
                                            <li><?php echo $psqList[2]['tab_a'] ?></li>
                                            <li><?php echo $psqList[2]['tab_b'] ?></li>
                                            <li><?php echo $psqList[2]['tab_c'] ?></li>
                                            <li><?php echo $psqList[2]['tab_d'] ?></li>
                                        </ul>
                                        <?php if ($psqNum == 0) { ?>
                                            <p class="submit_btn"><a href="javascript:void(0);" class="xinjia Link"><span
                                                        class="fl"
                                                        style="color:#ffffff; font-size:0.9rem;">提交</span></a>
                                            </p>
                                        <?php } ?>
                                    </div>
                                </div>
                                <?php if ($psqNum != 0) { ?>
                                    <div class="swiper-slide swiper-slide-next" style="width: 586px;">
                                        <img
                                            src="<?php echo $page['info']['cdn_url']; ?>/static/front/images/test.png" style="margin: 0;">
                                        <div class="first" id="four">
                                            <div class="tips clf"><span
                                                    class="fl prev">上一题</span><?php $psqNum = $psqNum - 1;
                                                echo $psqNum == 0 ? '' : '剩' . $psqNum . '题'; ?></div>
                                            <h3 style="margin-top: 0px;">
                                                4.<?php echo $psqList[3]['quest_title'] ?>
                                                ？</h3>
                                            <ul class="clf" id="four">
                                                <li><?php echo $psqList[3]['tab_a'] ?></li>
                                                <li><?php echo $psqList[3]['tab_b'] ?></li>
                                                <li><?php echo $psqList[3]['tab_c'] ?></li>
                                                <li><?php echo $psqList[3]['tab_d'] ?></li>

                                            </ul>
                                            <?php if ($psqNum == 0) { ?>
                                                <p class="submit_btn"><a href="javascript:void(0);" class="xinjia Link"><span
                                                            class="fl"
                                                            style="color:#ffffff; font-size:0.9rem;">提交</span></a>
                                                </p>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <?php if ($psqNum != 0) { ?>
                                        <div class="swiper-slide" style="width: 586px;">
                                            <img
                                                src="<?php echo $page['info']['cdn_url']; ?>/static/front/images/test.png" style="margin: 0;">
                                            <div class="first" id="five">
                                                <div class="tips clf"><span class="fl prev">上一题</span></div>
                                                <h3 style="margin-top: 0px;">
                                                    5.<?php echo $psqList[4]['quest_title'] ?>？</h3>
                                                <ul class="clf">
                                                    <li><?php echo $psqList[4]['tab_a'] ?></li>
                                                    <li><?php echo $psqList[4]['tab_b'] ?></li>
                                                    <li><?php echo $psqList[4]['tab_c'] ?></li>
                                                    <li><?php echo $psqList[4]['tab_d'] ?></li>
                                                </ul>
                                                <p><a href="javascript:void(0);" class="xinjia Link"><span
                                                            class="fl"
                                                            style="color:#ffffff; font-size:0.9rem;">提交</span></a>
                                                </p>
                                            </div>
                                        </div>
                                    <?php }
                                }
                            }
                        } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
