<?php
require(dirname(__FILE__) . "/../common/head.php");
//页面默认值
if (!isset($page['info'])) {
    $page['info']['id'] = '';
    $page['info']['group_name'] = '';
}
?>
<div class="main mhead">
    <div class="snav"><?php echo $page['info']['id'] ? '编辑语音' : '添加语音' ?>  </div>
</div>

<div class="main mbody">
    <form method="post"
          action="<?php echo $this->createUrl($page['info']['id'] ? 'material/audioEdit' : 'material/audioAdd'); ?>">
        <input type="hidden" id="id" name="id" value="<?php echo $page['info']['id']; ?>"/>
        <input id="audio_id" name="audio_id" value="" type="hidden"/>
        <input id="rname" name="rname" value="" type="hidden"/>
        <input id="playtime" name="playtime" value="" type="hidden"/>
        <input id="audio_size" name="audio_size" value="" type="hidden"/>
        <table class="tb3">
            <?php if ($page['info']['id']): ?>
                <tr>
                    <td width="150">语音ID：</td>
                    <td><?php echo $page['info']['id'] ?></td>
                </tr>
            <?php endif ?>
            <tr>
                <td width="150">语音名称：</td>
                <td>
                    <input type="text" class="ipt" id="audio_name" name="audio_name"
                           value="<?php echo $page['info']['audio_name']; ?>"/>
                </td>
            </tr>
            <tr>
                <td width="150">语音文件：</td>
                <td>
                    <?php if (!$page['info']['id']) { ?>
                        <div id="container">
                            <input id="audio_upload_btn" type="button" class="but2" value="选择文件"/>
                            <b id="audio_file_name"></b>
                            <input id="upload_audio" type="button" class="but2" disabled value="点击上传"/>

                        </div>

                        <div id="audio_loading">
                            <b id="size"></b>
                            <div class="Bar">
                                <div id="percent" style="width: 0%">
                                    <span id="percentnum">0%</span>
                                </div>
                            </div>
                            <b id="audio_msg" style="color: red"></b>
                            <b id="audio_error" style="color: red"></b>
                        </div>
                        <pre id="console"></pre>
                        <div>
                            <span style="color: grey;">
                               语音不能超过20M，语音时长不多于30分钟，支持mp3,ogg格式。
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

    #audio_loading {
        margin: 7px 0;
        width: 210px;
        overflow: hidden
    }
</style>
<script type="text/javascript">
    $("#audio_loading").hide();
    var uploader = new plupload.Uploader({
        runtimes: 'html5,flash,silverlight,html4',
        browse_button: 'audio_upload_btn', // you can pass in id...
        container: document.getElementById('container'), // ... or DOM Element itself
        url: '<?php echo $this->createUrl('/upload/uploadVideo/index');?>?fromid=audio',
        flash_swf_url: '<?php echo Yii::app()->params['basic']['cssurl'];?>admin/js/Moxie.swf',
        silverlight_xap_url: '<?php echo Yii::app()->params['basic']['cssurl'];?>admin/js/Moxie.xap',
        filters: {
            max_file_size: '20mb',
            mime_types: [
                {title: "shipin files", extensions: "mp3,ogg"}
            ]
        },
        multi_selection: false, //true:ctrl多文件上传, false 单文件上传
        init: {
            PostInit: function () {
                $("#upload_audio").click(function () {
                    console.log(uploader.files);
                    if ($("#audio_id").val() == "") {
                        uploader.start();
                    }
                    return false;
                });
            },
            //添加文件触发
            FilesAdded: function (up, files) {
                $("#audio_upload_btn").hide();
                $("#audio_file_name").text(files[0].name);
                $("#rname").val(files[0].name);
                $("#upload_audio").removeAttr("disabled");
                //$("#audio_iput").attr("file_id", files[0]['id']);
                $("#audio_loading").show();
                $("#size").text("Size:" + plupload.formatSize(files[0]['size']));
                $("#audio_size").val(files[0]['size']);
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

                $("#audio_msg").text(obj.msg);
                if (obj.status == 1) {
                    $("#audio_id").val(obj.result);
                    $("#playtime").val(obj.time);
                }
            },
            Error: function (up, err) { //上传出错的时候触发
                document.getElementById('console').innerHTML += "\nError #" + err.code + ": " + err.message;
            }
        }
    });
    uploader.init();

    //重新选择语音
    $("#audio_file_name").dblclick(function () {
        $("#audio_upload_btn").show();
        $("#audio_file_name").text('');
        $("#audio_msg").text('');
        $("#audio_loading").hide();
    })

</script>
