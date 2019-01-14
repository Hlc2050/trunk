<?php require(dirname(__FILE__)."/../common/login-header.php"); ?>
<style>
    .header {
        text-align: center;
    }
    .header h1 {
        font-size: 200%;
        color: #333;
        margin-top: 30px;
    }
    .header p {
        font-size: 14px;
    }
</style>
</head>
<body>
<div class="header">
    <div class="am-g">
        <h1>新微商系统</h1>
        <p>移动版</p>
    </div>
</div>
<div class="am-g">
    <div class="am-u-sm-12 am-text-center" >
        <img style="margin-bottom: 24px" src="/static/mobile/image/teamwork.png">
    </div>

    <div class="am-u-lg-6 am-u-md-8 am-u-sm-centered">
        <form method="post" class="am-form" action="<?php echo $this->createUrl("site/login") ?>">
            <fieldset class="myapp-login-form am-form-set">
                <div class="am-form-group am-form-icon">
                    <i class="am-icon-user"></i>
                    <input name="uname" required type="text" class="myapp-login-input-text am-form-field" placeholder="请输入您的账号">
                </div>
                <div class="am-form-group am-form-icon">
                    <i class="am-icon-lock"></i>
                    <input name="upass" required type="password" class="myapp-login-input-text am-form-field" placeholder="至少6个字符">
                </div>
            </fieldset>
            <button type="submit" class="myapp-login-form-submit am-btn am-btn-primary am-btn-block ">登 录</button>
        </form>
        <hr>
    </div>
</div>
</body>
</html>
