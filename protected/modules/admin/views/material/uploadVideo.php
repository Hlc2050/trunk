<?php
require(dirname(__FILE__) . "/../common/head.php");
//页面默认值
if (!isset($page['info'])) {
    $page['info']['id'] = '';
    $page['info']['group_name'] = '';
}
?>
<div class="main mhead">
    <div class="snav"><?php echo $page['info']['id'] ? '编辑视频' : '添加视频' ?>  </div>
</div>

<div class="main mbody">
    <form method="post"
          action="<?php echo $this->createUrl($page['info']['id'] ? 'material/videoEdit' : 'material/videoAdd'); ?>">
        <input type="hidden" id="id" name="id" value="<?php echo $page['info']['id']; ?>"/>
        <input id="video_id" name="video_id" value="" type="hidden"/>
        <input id="rname" name="rname" value="" type="hidden"/>
        <input id="video_size" name="video_size" value="" type="hidden"/>
        <table class="tb3">
            <?php if ($page['info']['id']): ?>
                <tr>
                    <td width="150">视频ID：</td>
                    <td><?php echo $page['info']['id'] ?></td>
                </tr>
            <?php endif ?>
            <tr>
                <td width="150">视频名称：</td>
                <td>
                    <input type="text" class="ipt" id="video_name" name="video_name"
                           value="<?php echo $page['info']['video_name']; ?>"/>
                </td>
            </tr>
            <tr>
                <td width="150">视频文件：</td>
                <td>
                    <?php if (!$page['info']['id']) { ?>
                        <div id="container">
                            <input id="video_upload_btn" type="button" class="but2" value="选择文件"/>
                            <b id="video_file_name"></b>
                            <input id="upload_video" type="button" class="but2" disabled value="点击上传"/>

                        </div>

                        <div id="video_loading">
                            <b id="size"></b>
                            <div class="Bar">
                                <div id="percent" style="width: 0%">
                                    <span id="percentnum">0%</span>
                                </div>
                            </div>
                            <b id="video_msg" style="color: red"></b>
                            <b id="video_error" style="color: red"></b>
                        </div>
                        <pre id="console"></pre>
                        <div>
                            <span style="color: grey;">
                                视频不能超过20M，视频时长不少于1秒，不多于10小时，支持大部分主流视频格式。
                            </span>
                        </div>
                    <?php } else echo $page['info']['o_name'] ?>
                </td>
            </tr>
            <tr>
                <td>支持人员：</td>
                <td class="alignleft">
                    <?php
                    $supportStafflist = SupportStaff::model()->getSupportStaffList();
                    echo CHtml::dropDownList('support_staff_id', $page['info']['support_staff_id'], CHtml::listData($supportStafflist, 'user_id', 'name'),
                        array(
                            'empty' => '选择支持人员',
                        )
                    );
                    ?>
                </td>
            </tr>
            <tr>
                <td></td>
                <td class="alignleft"><input type="submit" class="but" id="subtn" value="确定"/>
                </td>
            </tr>
        </table>
    </form>
</div>

<style type="text/css">
    #n a {
        padding: 0 4px;
        color: #333
    }

    .Bar {
        position: relative;
        width: 200px;
        border: 1px solid #2FA4E7;
        padding: 1px;
    }

    .Bar div {
        display: block;
        position: relative;
        background: #2FA4E7;
        color: #333333;
        height: 12px;
        line-height: 12px;
    }

    .Bar div span {
        position: absolute;
        width: 200px;
        text-align: center;
        font-weight: bold;
    }

    #video_loading {
        margin: 7px 0;
        width: 210px;
        overflow: hidden
    }
</style>
<script type="text/javascript">
    $("#video_loading").hide();
    var uploader = new plupload.Uploader({
        runtimes: 'html5,flash,silverlight,html4',
        browse_button: 'video_upload_btn', // you can pass in id...
        container: document.getElementById('container'), // ... or DOM Element itself
        url: '<?php echo $this->createUrl('/upload/uploadVideo/index');?>?fromid=video',
        flash_swf_url: '<?php echo Yii::app()->params['basic']['cssurl'];?>admin/js/Moxie.swf',
        silverlight_xap_url: '<?php echo Yii::app()->params['basic']['cssurl'];?>admin/js/Moxie.xap',
        filters: {
            max_file_size: '20mb',
            mime_types: [
                {title: "shipin files", extensions: "mpg,m4v,mp4,flv,3gp,mov,avi,rmvb,mkv,wmv"}
            ]
        },
        multi_selection: false, //true:ctrl多文件上传, false 单文件上传
        init: {
            PostInit: function () {
                $("#upload_video").click(function () {
                    console.log(uploader.files);
                    if ($("#video_id").val() == "") {
                        uploader.start();
                    }
                    return false;
                });
            },
            //添加文件触发
            FilesAdded: function (up, files) {
                $("#video_upload_btn").hide();
                $("#video_file_name").text(files[0].name);
                $("#rname").val(files[0].name);
                $("#upload_video").removeAttr("disabled");
                //$("#video_iput").attr("file_id", files[0]['id']);
                $("#video_loading").show();
                $("#size").text("Size:" + plupload.formatSize(files[0]['size']));
                $("#video_size").val(files[0]['size']);
            },
            //上传中，显示进度条
            UploadProgress: function (up, file) {
                var percent = file.percent;
                $("#percent").width(percent + "%");
                $("#percentnum").text(percent + "%");
            },
            //文件上传成功的时候触发
            FileUploaded: function (up, file, responseObject) {
                var obj = JSON.parse(responseObject.response);
                $("#video_msg").text(obj.msg);
                if (obj.status == 1) {
                    $("#video_id").val(obj.result);
                }
            },
            Error: function (up, err) { //上传出错的时候触发
                document.getElementById('console').innerHTML += "\nError #" + err.code + ": " + err.message;
            }
        }
    });
    uploader.init();

    //重新选择视频
    $("#video_file_name").dblclick(function () {
        $("#video_upload_btn").show();
        $("#video_file_name").text('');
        $("#video_msg").text('');
        $("#video_loading").hide();
    })

</script>
