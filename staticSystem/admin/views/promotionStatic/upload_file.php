<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<div class="main mhead">
    <div class="snav">
        <table class="tb3"><tr><th align="left">已上传文件</th></tr></table>
    </div>
    <div class="mt10"  id="container">
        <?php foreach ($files as $file) { ?>
             <span style="margin: 5px; display: inline-block"><img src="<?php echo $config['css_url']; ?>img/icons/file.gif"><?php echo  $file?></span>
        <?php }?>
    </div>
    <div class="snav">
        <table class="tb3"><tr><th align="left">批量上传图片</th></tr></table>
    </div>
    <div class="mt10"  id="container">
        <a id="pickfiles" href="javascript:;"><input type="button" class="but2" value="选择文件" /></a>
        <a id="uploadfiles" href="javascript:;"><input type="button" class="but2" value="点击上传" /></a>
    </div>
    <div class="mt10">
        <table class="tb3">
            <tr align="left">
                <th>已选择文件列表</th>
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
        runtimes: 'html5,flash,silverlight,html4',
        browse_button: 'pickfiles', // you can pass in id...
        container: document.getElementById('container'), // ... or DOM Element itself
        url: 'promotionStatic.php?action=upload_file&id=<?php echo $pid; ?>',
        flash_swf_url: '<?php echo $config['css_url'];?>js/Moxie.swf',
        silverlight_xap_url: '<?php echo $config['css_url'];?>js/Moxie.xap',
        filters: {
            max_file_size: '10mb',
            mime_types: [
                {title: "files", extensions: "jpg,gif,png,css,js,html,php"}//允许删除文件类型
            ]
        },
        init: {
            PostInit: function () {
                document.getElementById('filelist').innerHTML = '';
                document.getElementById('uploadfiles').onclick = function () {
                    var count = uploader.files.length;
                    var remove_file = [];
                    plupload.each(uploader.files, function (file){
                        $.ajax({
                            url: 'promotionStatic.php?action=upload_file&id=<?php echo $pid; ?>',
                            type: 'post',
                            dataType: 'json',
                            timeout: 1000,
                            async: false,
                            data:{'file_name':file.name},
                            success: function (data, status) {
                                var file_exit = data.status;
                                if (file_exit == 0) {
                                    if (!confirm(file.name+'文件已存在，确认覆盖？')) {
                                        document.getElementById(file.id).getElementsByTagName('td')[3].innerHTML = "<span>已取消上传</span>";
                                        remove_file.push(file);
                                    }
                                }
                            },
                            fail: function (err, status) {
                            }
                        });
                    });
                    for (var i in remove_file) {
                        uploader.removeFile(remove_file[i]);
                    }
                    if (count == 0) {
                        alert("没有要上传的文件");
                    } else {
                        uploader.start();
                    }
                    return false;
                };
            },
            FilesAdded: function (up, files) {
                plupload.each(files, function (file) {
                    document.getElementById('filelist').innerHTML += '<tr  id="' + file.id + '"><td style="width: 5%;font-weight: bold;"><a href="#" title="取消" class="pic_delete" data-val="' + file.id + '">X</a></td><td style="width: 35%">' + file.name + ' </td><td style="width: 15%">' + plupload.formatSize(file.size) + '</td><td></td></tr>';

                });
            },

            FileUploaded: function (uploader, file, responseObject) {
                uploader.removeFile(file);
                document.getElementById(file.id).getElementsByTagName('td')[0].innerHTML = '';
                if (responseObject.response != null)
                    document.getElementById(file.id).getElementsByTagName('td')[3].innerHTML = '<span style="color: red;">' + responseObject.response + "</span>";
                else
                    document.getElementById(file.id).getElementsByTagName('td')[3].innerHTML = "<span>上传成功</span>";
            },
            Error: function (up, err) {
                document.getElementById('console').innerHTML += "\nError #" + err.code + ": " + err.message;
            }
        }
    });
    uploader.init();

    //删除已选择的图片
    $(document).on('click', 'a.pic_delete', function () {
        $(this).parent().parent().remove();
        //uploader.removeFile($(this).attr("data-val"));
        var toremove = '';
        var id = $(this).attr("data-val");
        for (var i in uploader.files) {
            if (uploader.files[i].id === id) {
                toremove = i;
            }
        }
        uploader.files.splice(toremove, 1);
        console.log("XXX" + $(this).attr("data-val"));
    });
</script>