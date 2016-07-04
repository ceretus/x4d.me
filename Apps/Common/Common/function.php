<?php
/**
 * 树形结构
 *
 * @param array  $list  数据
 * @param string $pk    主键
 * @param string $pid   父ID
 * @param string $child child键
 * @param int    $root
 *
 * @return array
 */
function list_to_tree($list, $pk = 'id', $pid = 'pid', $child = '_child', $root = 0) {
    // 创建Tree
    $tree = array ();
    if (is_array ( $list )) {
        // 创建基于主键的数组引用
        $refer = array ();
        foreach ( $list as $key => $data ) {
            $refer [$data [$pk]] = & $list [$key];
        }
        foreach ( $list as $key => $data ) {
            // 判断是否存在parent
            $parentId = $data [$pid];
            if ($root == $parentId) {
                $tree [] = & $list [$key];
            } else {
                if (isset ( $refer [$parentId] )) {
                    $parent = & $refer [$parentId];
                    $parent [$child] [] = & $list [$key];
                }
            }
        }
    }
    return $tree;
}

/**
 * 全局安全过滤函数
 *
 * @param $text
 * @param string $type
 *
 * @return mixed|string
 */
function safe($text, $type = 'html') {
    // 无标签格式
    $text_tags = '';
    // 只保留链接
    $link_tags = '<a>';
    // 只保留图片
    $image_tags = '<img>';
    // 只存在字体样式
    $font_tags = '<i><b><u><s><em><strong><font><big><small><sup><sub><bdo><h1><h2><h3><h4><h5><h6>';
    // 标题摘要基本格式
    $base_tags = $font_tags . '<p><br><hr><a><img><map><area><pre><code><q><blockquote><acronym><cite><ins><del><center><strike>';
    // 兼容Form格式
    $form_tags = $base_tags . '<form><input><textarea><button><select><optgroup><option><label><fieldset><legend>';
    // 内容等允许HTML的格式
    $html_tags = $base_tags . '<meta><ul><ol><li><dl><dd><dt><table><caption><td><th><tr><thead><tbody><tfoot><col><colgroup><div><span><object><embed><param>';
    // 全HTML格式
    $all_tags = $form_tags . $html_tags . '<!DOCTYPE><html><head><title><body><base><basefont><script><noscript><applet><object><param><style><frame><frameset><noframes><iframe>';
    // 过滤标签
    $text = html_entity_decode ( $text, ENT_QUOTES, 'UTF-8' );
    $text = strip_tags ( $text, ${$type . '_tags'} );

    // 过滤攻击代码
    if ($type != 'all') {
        // 过滤危险的属性，如：过滤on事件lang js
        while ( preg_match ( '/(<[^><]+)(ondblclick|onclick|onload|onerror|unload|onmouseover|onmouseup|onmouseout|onmousedown|onkeydown|onkeypress|onkeyup|onblur|onchange|onfocus|background|codebase|dynsrc|lowsrc)([^><]*)/i', $text, $mat ) ) {
            $text = str_ireplace ( $mat [0], $mat [1] . $mat [3], $text );
        }
        while ( preg_match ( '/(<[^><]+)(window\.|javascript:|js:|about:|file:|document\.|vbs:|cookie)([^><]*)/i', $text, $mat ) ) {
            $text = str_ireplace ( $mat [0], $mat [1] . $mat [3], $text );
        }
    }
    return $text;
}

/**
 * 字符串内容截取
 *
 * @param string $str     被截取的字符串
 * @param int    $start   开始截取的位置
 * @param int    $length  截取的长度
 * @param string $charset 字符集
 *
 * @return string
 */
function msubstr_local($str, $start = 0, $length, $charset = "utf-8") {
    if (function_exists ( "mb_substr" ))
        $slice = mb_substr ( $str, $start, $length, $charset );
    elseif (function_exists ( 'iconv_substr' )) {
        $slice = iconv_substr ( $str, $start, $length, $charset );
        if (false === $slice) {
            $slice = '';
        }
    } else {
        $re ['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re ['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re ['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re ['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all ( $re [$charset], $str, $match );

        $slice = join ( "", array_slice ( $match [0], $start, $length ) );
    }
    return (strlen ( $str ) > strlen ( $slice )) ? $slice . '...' : $slice;
}

/**
 * 编译生成CSS文件
 *
 * @param array $urls URL
 *
 * @return string
 */
function parse_css($urls)
{
    $url = md5(implode(',', $urls));
    $css_url = './Public/static/' . $url . '.css';
    $url_path = $css_url;

    if (!file_exists($url_path)) {
        if (!file_exists('./Public/static/'))
            mkdir('./Public/static/', 0777);

        $css_content = '';
        foreach ($urls as $url) {
            $url = './Public/' . MODULE_NAME . '/css/' . $url ;

            $css_content .= file_get_contents($url);
        }
        $css_content = preg_replace("/[\r\n]/", '', $css_content);
        //$css_content = str_replace("../images/", "http://static.400388.com/images/", $css_content);
        file_put_contents($url_path, $css_content);
    }
    return "/" . $css_url;
}

/**
 * 编辑生成JS文件
 *
 * @param array $urls       URL
 * @param array $encode_url 是否加密
 *
 * @return string
 */
function parse_script($urls, $encode_url = array())
{
    $url = md5(implode(',', $urls));
    $js_url = './Public/static/' . $url . '.js';
    $url_path = $js_url;
    if (!file_exists($url_path)) {
        if (!file_exists('./Public/static/'))
            mkdir('./Public/static/', 0777);

        /*if (count($encode_url) > 0) {
            require_once APP_ROOT_PATH . "system/libs/javascriptpacker.php";
        }*/

        $js_content = '';
        foreach ($urls as $url) {
            $url = './Public/'. MODULE_NAME .'/js/' . $url ;
            $append_content = @file_get_contents($url) . "\r\n";
            /*if (in_array($url, $encode_url)) {
                $packer = new JavaScriptPacker($append_content);
                $append_content = $packer->pack();
            }*/
            $js_content .= $append_content;
        }
        file_put_contents($url_path, $js_content);
    }
    return "/" . $js_url;
}

/**
 * 登录验证函数
 *
 * @return int
 */
function is_login(){
    $user = session('user_auth');
    if (empty($user)) {
        return 0;
    } else {
        return session('user_auth_sign') == data_auth_sign($user) ? $user['uid'] : 0;
    }
}

/**
 * 数据签名认证
 *
 * @param  array  $data 被认证的数据
 *
 * @return string       签名
 */
function data_auth_sign($data) {
    //数据类型检测
    if(!is_array($data)){
        $data = (array)$data;
    }
    ksort($data); //排序
    $code = http_build_query($data); //url编码并生成query字符串
    $sign = sha1($code); //生成签名
    return $sign;
}

/**
 * CURL 方式获取数据
 *
 * @param string $url     CURL地址
 * @param string $data    发送的数据
 * @param int    $timeout 超时时间
 *
 * @return mixed
 */
function curl_get_data($url, $data, $timeout = 1)
{
    $response = '';

    $ch = curl_init();                                //初始化curl
    curl_setopt($ch, CURLOPT_URL, $url);              //设置链接
    curl_setopt($ch, CURLOPT_POST, 1);                //设置为POST方式
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);  //POST数据
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);      //设置是否返回信息
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);      //超时时间

    $response = curl_exec($ch);                       //接收返回信息
    curl_close($ch);                                 //关闭curl链接

    return json_decode($response, true);
}

/**
 * 系统加密方法
 *
 * @param string $str 加密串
 * @param string $key 安全key
 *
 * @return string
 */
function think_ucenter_md5($str, $key = '%^%!@####111')
{
    return md5(sha1($str) . $key);
}

/**
 * 校验验证码
 *
 * @param string $code 验证码
 * @param int    $id
 *
 * @return bool
 */
function check_verify($code, $id = 1)
{
    $verify = new \Think\Verify();
    return $verify->check($code);
}

/**
 * 检查当前用户是否为管理员
 *
 * @param null $uid 用户ID
 *
 * @return bool
 */
function is_administrator($uid = null){
    $uid = is_null($uid) ? is_login() : $uid;
    return $uid && (intval($uid) === C('USER_ADMINISTRATOR'));
}

/**
 * 设置跳转链接
 *
 * @param string $url URL
 *
 * @return void
 */
function set_redirect_url($url)
{
    cookie('redirect_url', $url);
}

/**
 * 获取跳转链接
 *
 * Author xuyufei@jingge.cc
 *
 * @return mixed|null|string
 */
function get_redirect_url(){
    $url = cookie('redirect_url');
    return empty($url) ? __APP__ : $url;
}

/**
 * file_get_contents 的防超时版本
 *
 * @param string $url 链接
 *
 * @return bool|string
 */
function wp_file_get_contents($url) {
    $context = stream_context_create ( array (
        'http' => array (
            'timeout' => 30
        )
    ) );

    return file_get_contents ( $url, 0, $context );
}

/**
 * 获取微信token
 *
 * @param null $token
 *
 * @return int|mixed|null
 */
function get_token($token = NULL) {
    if ($token !== NULL) {
        session ( 'token', $token );
    } elseif (! empty ( $_REQUEST ['token'] )) {
        session ( 'token', $_REQUEST ['token'] );
    }
    $token = session ( 'token' );

    if (empty ( $token )) {
        return - 1;
    }

    return $token;
}

function clear_cache($key)
{
    $redis = new \Think\Cache\Driver\Redis();
    $redis->rm($key);
}

/**
 * 系统邮件发送函数
 *
 * @param string $to    接收邮件者邮箱
 * @param string $name  接收邮件者名称
 * @param string $subject 邮件主题
 * @param string $body    邮件内容
 * @param string $attachment 附件列表
 *
 * @return boolean
 */
function send_mail($to, $subject = '', $body = '', $attachment = null)
{
    vendor('PhpMailer.Mail');
    $mail = new \Mail();
    return $mail->send_email($to, $subject, $body);
}

function app_redirect($url,$time=0,$msg='')
{
    //多行URL地址支持
    $url = str_replace(array("\n", "\r"), '', $url);
    if(empty($msg))
        $msg    =   "系统将在{$time}秒之后自动跳转到{$url}！";
    if (!headers_sent()) {
        // redirect
        if(0===$time) {
            if(substr($url,0,1)=="/")
            {
                header("Location:".get_domain().$url);
            }
            else
            {
                header("Location:".$url);
            }

        }else {
            header("refresh:{$time};url={$url}");
            echo($msg);
        }
        exit();
    }else {
        $str    = "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
        if($time!=0)
            $str   .=   $msg;
        exit($str);
    }
}

function get_user_info($id, $field)
{
    if (empty($id) || empty($field)) {
        return false;
    }
    $userInfo = M('UserDetail')->where(array('user_id' => $id))->find();
    return $userInfo[$field];
}

function get_extension($file)
{
    return substr($file, strrpos($file, '.')+1);
}

function api($name,$vars=array()){
    $array     = explode('/',$name);
    $method    = array_pop($array);
    $classname = array_pop($array);
    $module    = $array? array_pop($array) : 'Common';
    $callback  = $module.'\\Api\\'.$classname.'Api::'.$method;
    if(is_string($vars)) {
        parse_str($vars,$vars);
    }
    return call_user_func_array($callback,$vars);
}

/**
 * 短信发送（阿里）
 *
 * @param string $mobile 手机号
 * @param string $tpl    邮件模板
 * @param string $sign   邮件签名
 *
 * @return mixed
 */
function do_send_sms($mobile, $tpl = 'SMS_4740784', $sign = '注册验证')
{
    $ch = curl_init();
    $headers = array('X-Ca-Key' => '23301634', 'X-Ca-Secret' => '406f1908eca1efaa5904625c7de9b3e3');
    $_headers = array();
    if (!is_null($headers) && is_array($headers)) {
        foreach ($headers as $k => $v) {
            array_push($_headers, "{$k}: {$v}");
        }
    }
    $verify = new \Think\Verify();
    $code = $verify->smsEntry();
    $smsParam = json_encode(array("code" => "$code", "product" => "[好学网]"));
    $postData = array(
        'rec_num' => $mobile,
        'sms_template_code' => $tpl,
        'sms_free_sign_name' => $sign,
        'sms_type' => 'normal',
        'sms_param' => $smsParam
    );
    curl_setopt($ch, CURLOPT_URL, 'https://ca.aliyuncs.com/gw/alidayu/sendSms');
    curl_setopt($ch, CURLOPT_HTTPHEADER, $_headers);
    curl_setopt($ch, CURLOPT_TIMEOUT, 120);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
    curl_setopt($ch ,CURLOPT_POSTFIELDS , $postData);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}
