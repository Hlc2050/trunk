<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<div class="main mhead">
    <div class="snav">
        <table class="tb3"><tr><th align="left">批量上传二维码</th></tr></table>
    </div>
    <div class="mt10"  id="container">
        &nbsp;&nbsp;&nbsp;&nbsp;
        <a id="pickfiles"
           href="javascript:;"><?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="批量选择二维码"  />', 'auth_tag' => 'weChat_uploadQRCode')); ?>
            &nbsp;&nbsp;&nbsp;&nbsp;</a>
        <a id="uploadfiles"
           href="javascript:;"><?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="点击上传二维码"  />', 'auth_tag' => 'weChat_uploadQRCode')); ?>
            &nbsp;&nbsp;&nbsp;&nbsp;</a>
    </div>
    <div>
        <table class="tb3">
            <tr align="left">
                <th>文件列表</th>
            </tr>

        </table>
        <table class="tb3" id="filelist"></table>
        <pre id="console"></pre>
    </div>

</div>
<div class="main mbody">

</div>


<script type="text/javascript">
    // Custom example logic

    var uploader = new plupload.Uploader({
        runtimes : 'html5,flash,silverlight,html4',
        browse_button : 'pickfiles', // you can pass in id...
        container: document.getElementById('container'), // ... or DOM Element itself
        url : '<?php echo $this->createUrl('/upload/batchUploadImgs/index',array('fromid'=>'qr'));?>',
        flash_swf_url : '<?php echo Yii::app()->params['basic']['cssurl'];?>admin/js/Moxie.swf',
        silverlight_xap_url : '<?php echo Yii::app()->params['basic']['cssurl'];?>admin/js/Moxie.xap',
        filters : {
            max_file_size : '10mb',
            mime_types: [
                {title : "Image files", extensions : "jpg,gif,png"}//只允許上傳圖片
            ]
        },
        init: {
            PostInit: function() {
                document.getElementById('filelist').innerHTML = '';

                document.getElementById('uploadfiles').onclick = function() {
                    var count=uploader.files.length;
                    if(count==0){
                        alert("没有要上传的文件");
                    }else{
                        uploader.start();
                    }
                    return false;
                };
            },
            FilesAdded: function(up, files) {
                plupload.each(files, function(file) {
                    document.getElementById('filelist').innerHTML += '<tr  id="' + file.id + '"><td style="width: 5%;font-weight: bold;"><a title="取消" href="#" class="pic_delete" data-val="'+file.id+'">X</a></td><td style="width: 35%">' + file.name + ' </td><td style="width: 15%">' + plupload.formatSize(file.size) + '</td><td></td></tr>';
                });
            },

            FileUploaded : function (uploader,file,responseObject) {
                console.log(responseObject.response);
                document.getElementById(file.id).getElementsByTagName('td')[0].innerHTML ='';
                if(responseObject.response != null)
                    document.getElementById(file.id).getElementsByTagName('td')[3].innerHTML = '<span style="color: red;">' + responseObject.response + "</span>";
                else
                    document.getElementById(file.id).getElementsByTagName('td')[3].innerHTML = "<span>匹配成功</span>";
            },
            Error: function(up, err) {
                document.getElementById('console').innerHTML += "\nError #" + err.code + ": " + err.message;
            }
        }
    });
    uploader.init();
    //删除已选择的图片
    $(document).on('click','a.pic_delete',function(){
        $(this).parent().parent().remove();
        //uploader.removeFile($(this).attr("data-val"));
        var toremove = '';
        var id=$(this).attr("data-val");
        for(var i in uploader.files){
            if(uploader.files[i].id === id){
                toremove = i;
            }
        }
        uploader.files.splice(toremove, 1);
        console.log("XXX"+$(this).attr("data-val"));
    });
</script>
