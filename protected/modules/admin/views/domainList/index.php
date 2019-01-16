<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<style>
    .file {
        position: relative;
        display: inline-block;
        background: #2fa4e7;
        border: 1px solid #2C91CB;
        padding: 4px 12px;
        overflow: hidden;
        color: white;
        text-decoration: none;
        text-indent: 0;
        line-height: 20px;
        margin-left: 4px;
    }

    .file input {
        position: absolute;
        font-size: 100px;
        right: 0;
        top: 0;
        opacity: 0;
    }

    .file:hover {
        color: white;
    }
</style>
<script>
    function show_frame_infos() {
        var d = dialog({
            title: 'Result',
            content: $(window.frames["frame02"].document).find(".msgbox0009").html(),
            okValue: '确定',
            ok: function () {
                window.location.reload(1);
            }
        });
        d.showModal();
    }

    function recheck_domain(id) {
        jQuery.ajax({
            'type': 'POST',
            'url': '/admin/domainList/recheckDomain',
            'data': {'id': id},
            'cache': false,
            'success': function (result) {
                var obj = JSON.parse(result);
                var d = dialog({
                    title: '检测结果',
                    content: obj.msg,
                    okValue: '确定',
                    ok: function () {
                        window.location.reload(1);
                    }
                });
                d.showModal();
            }
        });
        return false;

    }

    function exportList() {
        var data = $("#searchForm").serialize();
        var url = "<?php echo $this->createUrl('domainList/export');?>";
        window.location.href=url+'?'+data;
    }

</script>

<div class="main mhead">
    <div class="snav">域名管理 » 域名列表</div>

    <div class="mt10">
        <form action="<?php echo $this->createUrl('domainList/index'); ?>" id="searchForm">
            <?php
            $domain_status = vars::$fields['domain_status'];
            echo CHtml::dropDownList('search_type2', $this->get('search_type2'), CHtml::listData($domain_status, 'value', 'txt'), array('empty' => '全部')
            );
            ?>
            &nbsp;
            <select name="search_domain_types" style="width: 85px">
                <option value="">类型</option>
                    <?php foreach (vars::$fields['domain_types'] as $value){?>
                        <option value="<?php echo $value['value'];?>" <?php if ($this->get('search_domain_types') == $value['value'] && $this->get('search_domain_types') !='') echo 'selected';?>><?php echo $value['txt'];?></option>
                    <?php } ?>
            </select>&nbsp;
            <select id="search_type" name="search_type" style="width: 85px">
                <option value="domain" <?php echo $this->get('search_type') == 'domain' ? 'selected' : ''; ?>>域名
                </option>
                <option value="uid" <?php echo $this->get('search_type') == 'uid' ? 'selected' : ''; ?>>推广人员
                </option>
            </select>&nbsp;
            <select id="search_public_domain" name="search_public_domain" style="width: 85px">
                <option value="">公众号</option>
                <option value="1" <?php echo $this->get('search_public_domain') == '1' ? 'selected' : ''; ?>>是</option>
                <option value="0" <?php echo $this->get('search_public_domain') == '0' ? 'selected' : ''; ?>>否</option>
            </select>&nbsp;
            <input type="text" id="search_txt" name="search_txt" class="ipt"
                   value="<?php echo $this->get('search_txt'); ?>">
            <input type="submit" class="but" value="查询">
        </form>
        <div class="mt10 clearfix">
            <div class="l">
                <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="添加域名" onclick="location=\'' . $this->createUrl('domainList/add?url='.$page['listdata']['url']) . '\'" />', 'auth_tag' => 'domainList_add')); ?>
                <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="删除" onclick="set_some(\'' . $this->createUrl('domainList/delete') . '?ids=[@]\',\'确定删除吗？\');" />', 'auth_tag' => 'domainList_delete')); ?>
                <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="模板下载" onclick="location=\'' . $this->createUrl('domainList/template') . '\'" />', 'auth_tag' => 'domainList_template')); ?>
                <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="列表导出" onclick="exportList()" />', 'auth_tag' => 'domainList_export')); ?>
                <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="批量编辑" onclick="set_some(\'' . $this->createUrl('domainList/edit') . '?id=[@]\',\'none\');"  />', 'auth_tag' => 'domainList_edit')); ?>
            <form action="<?php echo $this->createUrl('domainList/load'); ?>" method="post" target="frame02"
                  enctype="multipart/form-data" style="display: inline-block">
                <a href="javascript:;" class="file">选择文件
                    <?php $this->check_u_menu(array('code' => '<input type="file"  name="filename"  />', 'auth_tag' => 'domainList_load')); ?>
                </a>
                <?php $this->check_u_menu(array('code' => '<input type="submit" name="submit" class="but2" value="导入" />', 'auth_tag' => 'domainList_load')); ?>
            </form>
            <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="导出无推广人员域名" onclick="location=\'' . $this->createUrl('domainList/noUserDomain') . '\'" />', 'auth_tag' => 'UnUserDomainList_export')); ?>

            <form action="<?php echo $this->createUrl('domainList/importDomainUser'); ?>" method="post" target="frame02"
                  enctype="multipart/form-data" style="display: inline-block">
                <a href="javascript:;" class="file">选择文件
                    <?php $this->check_u_menu(array('code' => '<input type="file"  name="filename"  />', 'auth_tag' => 'domainList_importDomainUser')); ?>
                </a>
                <?php $this->check_u_menu(array('code' => '<input type="submit" name="submit" class="but2" value="导入推广人员" />', 'auth_tag' => 'domainList_importDomainUser')); ?>
            </form>
            </div>
            <div class="r">

            </div>
        </div>
    </div>
</div>
    <div class="main mbody">
        <form action="<?php echo $this->createUrl('domainList/saveOrder'); ?>" name="form_order" method="post">
            <table class="tb fixTh">
                <thead>
                <tr>
                    <th width="50" class="cklist"><a href="javascript:void(0);" class="cklist"
                                                     onclick="check_all('.cklist');">全选/反选</a></th>
                    <th width="40">ID</th>
                    <th width="100">域名</th>
                    <th width="40">支持Https</th>
                    <th width="100">公众号</th>
                    <th width="80">域名费用</th>
                    <th width="80">推广人员</th>
                    <th width="80">状态</th>
                    <th width="80">类型</th>
                    <th width="80">总统计组别</th>
                    <th width="80">应用类型</th>
                    <th width="80">添加时间</th>
                    <th width="100">备注</th>
                    <th width=100>操作</th>
                </tr>
                </thead>

                <?php
                $recheck_auth = $this->check_u_menu(array('auth_tag' => 'domainList_reCheck'));
                $edit_auth = $this->check_u_menu(array('auth_tag' => 'domainList_edit'));
                $delete_auth = $this->check_u_menu(array('auth_tag' => 'domainList_delete'));
                foreach ($page['listdata']['list'] as $r) {
                    ?>
                    <tr>
                        <td><input type="checkbox" class="cklist" value="<?php echo $r['id']; ?>"/></td>
                        <td><?php echo $r['id'] ?></td>
                        <td><?php echo $r['domain'] ?></td>
                        <td><?php echo $r['is_https']==1?'<span style="color: red">✔</span>':'<span style="color: dodgerblue;">✖</span>'; ?></td>
                        <td><?php echo $r['is_public_domain']==1?'<span style="color: red">✔</span>':'<span style="color: dodgerblue;">✖</span>'; ?></td>
                        <td><?php echo $r['money'] ?></td>
                        <td><?php echo $r['csname_true'];?></td>
                        <td><?php echo vars::get_field_str('domain_status', $r['status']); ?></td>
                        <td><?php echo vars::get_field_str('domain_types', $r['domain_type']); ?></td>
                        <td><?php echo $r['cnzz_code_id']?$r['name']:'无' ?></td>
                        <td><?php echo $r['application_type'] == 1?'静态应用':'普通应用' ?></td>
                        <td><?php echo date("Y-m-d H:i", $r['create_time']); ?></td>
                        <td><?php echo $r['mark'] ?></td>
                        <td>
                            <?php if ($r['status'] == 2)
                                if ($recheck_auth) echo '<a style="color:red" href="#" onclick="recheck_domain('.$r['id'].')">重新检测 &nbsp;</a>';
                                if ($edit_auth) echo '<a href="' . $this->createUrl('domainList/edit') .'?id='.$r['id'].'&url='.$page['listdata']['url'].'">修改&nbsp;</a>';
                                if ($delete_auth) echo '<a href="' . $this->createUrl('domainList/delete') . '?ids=' . $r['id'] . '" onclick="return confirm(\'确定删除吗\')">删除</a>';
                            ?>
                        </td>
                    </tr>
                    <?php
                } ?>

            </table>
            <div class="pagebar"><?php echo $page['listdata']['pagearr']['pagecode']; ?></div>
            <div class="clear"></div>
        </form>
    </div>
    <div class="float-simage-box" style="position: absolute;">
    </div>

    <script src="/static/lib/jquery.jcrop/jquery.jcrop.min.js"></script>
    <link rel="stylesheet" href="/static/lib/jquery.jcrop/jquery.Jcrop.css">
    <div id="framebox" style="display: none;">
        <iframe name="frame02" src="" id="frame02"></iframe>
    </div>


