<?php
/**
 * @param $url
 * @param int $is_https 是否为https请求
 * @return mixed|string
 */
function curl_get($url,$is_https=0)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    if ($is_https == 1) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    }
    $res = curl_exec($ch);
    if (curl_errno($ch)) {
        return 'curl_error';
    }
    curl_close($ch);
    return $res;
}

/**提示界面
 * @params $params['state'] 0=失败，并且浏览器后退一步,1=成功，并且跳转到上一页,-1=不进行页面跳转，只显示 msgwords,-2=错误，停止
 * @params $params['url']  强制跳转的url
 * @params $params['msgwords'] 显示的文字
 * @params $params['jscode']  自定义js行为
 * @params $params['type']   类型，默认页面,可选 json,xml,jsonp';
 * @atention 如果制定了url话跳转到 url
 * 如果 msgwords 提示文字有的话，则显示提示文字
 * 如果
 */
function msg($params=array()){

    $params['state']=isset($params['state'])?$params['state']:1;
    $params['url']=isset($params['url'])?$params['url']:(isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'');
    $params['msgwords']=isset($params['msgwords'])?$params['msgwords']:'';
    $params['type']=isset($params['type'])?$params['type']:'';
    $msgwords_bak=$params['msgwords'];
    $jscode='';
    if($params['state']==0){
        $params['msgwords']=$params['msgwords']?$params['msgwords']:'操作失败';
        $params['icon']='error';
        $jscode='<script>setTimeout(function (){window.location="'.$params['url'].'";},2000)</script>';
        //$jscode='<script>setTimeout(function (){history.go(-1);},1000)</script>';
    }else if($params['state']==1){
        $params['msgwords']=$params['msgwords']?$params['msgwords']:'操作成功';
        $params['icon']='succeed';
        $jscode='<script>setTimeout(function (){window.location="'.$params['url'].'";},2000)</script>';
    }else if($params['state']==-1){
        $jscode='';
        $params['msgwords']=$msgwords_bak?$msgwords_bak:'操作停止';
        $params['icon']='question';
    }else if($params['state']==-2){
        $jscode='';
        $params['msgwords']=$msgwords_bak?$msgwords_bak:'操作停止';
        $params['icon']='error';
        $jscode='<script>setTimeout(function (){window.location="'.$params['url'].'";},2000)</script>';

    }
    if(!$params['jscode']){
        $params['jscode']=$jscode;
    }
    if($params['type']=='json'){
        die(json_encode($params));
    }
    if($params['type']=='jsonp'){
        $jsoncallback=isset($_GET['jsoncallback'])?$_GET['jsoncallback']:'';
        die($jsoncallback.'('.json_encode($params).')');
    }
    $msg = $params;
    $config=include('config.php');
    $new_path = str_replace('\\','/',dirname(__FILE__));
    $root = str_replace('/include','',$new_path);
    require ($root.'/admin/views/common/msg.php');
    exit();
}

/**
 * api请求token
 * @param $account string 账号
 * @return string
 */
function getAccountToken($account)
{
    $config=include('config.php');
    //当前时间：小时
    $time = intval(time()/60/60)*60*60;
    $check_token = md5(str_pad(strrev($account),30,$config['token_str'],STR_PAD_BOTH).$time);
    return $check_token;
}

/**
 * 推广文件夹
 * @param $pid int 推广id
 * @return string
 */
function getPromotionFolder($pid)
{
    //用来填充的字符
    $add_letter = 'qkn';
    $letter = array('h','p','w','a','e','s','b','x','z','l');
    //反转推广id,21->12
    $new_pid = strrev($pid);
    $pid_arr = str_split($new_pid);
    $str = '';
    //将数字转化为英文
    for($i=0;$i<count($pid_arr);$i++) {
        $str .= $letter[$pid_arr[$i]];
    }
    //不足6位填充为6位
    $new_str = str_pad($str,6,$add_letter,STR_PAD_BOTH);
    return $new_str;
}

/**
 * 根据文件夹获取推广id
 * @param $folder string
 * @return string
 */
function getFolderPid($folder)
{
    $add_letter = 'qkn';
    $letter = array('h'=>0,'p'=>1,'w'=>2,'a'=>3,'e'=>4,'s'=>5,'b'=>6,'x'=>7,'z'=>8,'l'=>9);
    $pid_str = str_replace('q','',$folder);
    $pid_str = str_replace('k','',$pid_str);
    $pid_str = str_replace('n','',$pid_str);
    $pid_array = str_split($pid_str);
    $pid = '';
    foreach ($pid_array as $value) {
        $pid .= $letter[$value];
    }
    $pid = strrev($pid);
    return $pid;
}

/**
 * 删除文件夹
 * @param $dirName string
 * @return bool
 */
function removeDir($dirName)
{
    if(!is_dir($dirName))
    {
        return true;
    }
    $handle = @opendir($dirName);
    while(($file = @readdir($handle)) !== false)
    {
        if($file != '.' && $file != '..')
        {
            $dir = $dirName . '/' . $file;
            is_dir($dir) ? removeDir($dir) : @unlink($dir);
        }
    }
    closedir($handle);

    return rmdir($dirName) ;
}

/**
 * 判断文件夹是否存在或为空
 * @param $dirName string
 * @return bool
 */
function emptyDir($dirName)
{
    if (!is_dir($dirName)) {
        return true;
    }
    if($handle = opendir($dirName)){
        while($item = readdir($handle)){
            if ($item !='.' && $item != '..'){
                return false;
            }
        }
    } else {
        return true;
    }
}


/**
 * 获取文件夹下所有文件
 * @param $dirName
 * @return array|bool
 */
function getFiles($dirName)
{
    $files = array();
    if (!is_dir($dirName)) {
        return false;
    }
    if($handle = opendir($dirName)){
        while($item = readdir($handle)){
            if ($item !='.' && $item != '..'){
                $files[] = $item;
            }
        }
    } else {
        return false;
    }
    return $files;
}

/**
 * goto地址
 * @param $domain
 * @param $pid
 * @return string
 */
function buildGotoLink($domain,$pid)
{
    $goto_link = '';
    $goto_domain = $domain->domain;
    if (!$goto_domain) {
        return $goto_link;
    }
    $rand_str = randGotoStr($pid);
    $is_https = $domain->is_https;
    $goto_link = $goto_domain.'/index.php/'.$rand_str.'.html';
    if ($is_https) {
        $goto_link = 'https://'.$goto_link;
    }else {
        $goto_link = 'http://'.$goto_link;
    }
    return $goto_link;
}

/**
 * 生成GOTO随机串
 * @param $str
 * @return string
 */
function randGotoStr($pid)
{
    $letter = '0123456789abcdefghijklmnopqxyz';
    $new_str = '';
    $dlen = strlen($pid);
    $le_len = strlen($letter)-1;
    for ($i = 0; $i < $dlen; $i++) {
        $new_str .= substr($letter,rand(0, $le_len),1);
    }
    $new_str .= $dlen . $pid;
    for ($i = 0; $i < $dlen; $i++) {
        $new_str .= substr($letter,rand(0, $le_len),1);
    }
    return $new_str;
}

/**
 * goto随机串获取pid
 * @param $randstr
 * @return bool|string
 */
function getGotoPid($randstr)
{
    $str_len = (strlen($randstr)-1)/3;
    $count_len = strlen($str_len);
    $pid = substr($randstr,$str_len+$count_len,$str_len);
    return $pid;
}

function buildTgLink($data,$pid,$promotion_type)
{
    $tg_link = [];
    $domains = $data->domain_list;
    if (!$domains) {
        return $tg_link;
    }
    $extends = '';
    if ($promotion_type != 3) {
        $rand_leter = 'abcdefghijklmnopqrstuvwxyz';
        $le_len = strlen($rand_leter)-1;
        $str ='';
        for ($i=0;$i<=6;$i++) {
            $str.=substr($rand_leter,rand(0, $le_len),1);
        }
        $extends .= '/index.php/'.$str.'/'.digital_encrypt($pid).'.html';
    }

    foreach ($domains as $value) {
        $is_https = $value->is_https==1?'https://':'http://';
        $tg_link[] = array(
            'domain'=>$is_https.$value->domain.$extends,
            'domain_status'=>$value->domain_status,
        );
    }

    return $tg_link;

}

function digital_encrypt($digital, $len = 16, $operation = 'E')
{
//        $position=5
    if ($operation == 'E') {
        $new_digital = rand(1, 9) . rand(1000, 9999);
        $dlen = strlen($digital);
        $new_digital .= $dlen . $digital;
        for ($i = $dlen + 6; $i < $len; $i++) {
            $new_digital .= rand(0, 9);
        }
    } else {
        $len = substr($digital, 5, 1);
        $new_digital = substr($digital, 6, $len);
    }
    return $new_digital;
}