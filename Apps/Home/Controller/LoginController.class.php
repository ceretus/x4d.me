<?php
/**
 * yk
 *
 * PHP Version 5
 *
 * @category  LoginController.class.php
 * @package   package
 * @author    xuyufei <xuyufei@jingge.cc>
 * @time      10
 * @copyright 2015 精格网络科技（北京）有限公司
 * @license   http://www.haoxue.com license
 * @link      xuyufei@jingge.cc
 */
namespace Home\Controller;

/**
 * Course
 *
 * @category LoginController.class.php
 * @package  Admin
 * @author   xuyufei <xuyufei@jingge.cc>
 * @license  http://www.haoxue.com license
 * @link     xuyufei@jingge.cc
 */
class LoginController extends BaseController
{
    public function index($type = 'qq')
    {
        empty($type) && $this->error('参数错误');
        vendor('Auth.ThinkSDK.Sdk.ThinkOauth');
        $sns  = \ThinkOauth::getInstance($type);
        set_redirect_url(I('return_url'));
        redirect($sns->getRequestCodeURL());
    }

    //授权回调地址
    public function callback($type = null, $code = null){
        echo get_redirect_url();
        (empty($type) || empty($code)) && $this->error('参数错误');
        vendor('Auth.ThinkSDK.Sdk.ThinkOauth');
        $sns  = \ThinkOauth::getInstance($type);

        //腾讯微博需传递的额外参数
        $extend = null;
        if($type == 'tencent'){
            $extend = array('openid' => I('openid'), 'openkey' => I('openkey'));
        }
        $token = $sns->getAccessToken($code , $extend);
        if(is_array($token)){
            $qq   = \ThinkOauth::getInstance('qq', $token);
            $data = $qq->call('user/get_user_info');

            if($data['ret'] == 0){
                $hasItem = M('ApiLogin')->where(array('open_id' => $token['openid']))->find();
                if (!$hasItem) {
                    $result = M('ApiLogin')->add(array(
                        'plat_id' => 1,
                        'open_id' => $token['openid'],
                        'access_token' => $token['access_token']
                    ));
                    if (false === $result) {
                        $this->error('登录失败');
                    } else {
                        $loginResult = M('User')->add(array(
                            'username' => $token['openid'],
                            'reg_ip' => get_client_ip(),
                            'last_login_ip' => get_client_ip(),
                            'last_login_time' => date('Y-m-d H:i:s', time()),
                            'nickname' => $data['nickname'],
                            'avatar' => $data['figureurl_2']
                        ));
                        if ($loginResult) {
                            $apiLoginResult = M('ApiLogin')->save(array(
                                'id' => $result,
                                'user_id' => $loginResult
                            ));
                            if ($apiLoginResult) {
                                $user = M('User')->where(array('user_id' => $loginResult))->find();
                                $autoLoginResult = D('User')->login($user);
                                if ($autoLoginResult) {
                                    $this->success('登录成功', get_redirect_url());
                                } else {
                                    $this->error('登录失败');
                                }
                            } else {
                                $this->error('登录失败');
                            }
                        } else {
                            $this->error('登录失败');
                        }
                    }
                } else {
                    $user = M('User')->where(array('user_id' => $hasItem['user_id']))->find();
                    $result = D('User')->login($user);
                    $this->success('登录成功', get_redirect_url());
                }

                /*$userInfo['type'] = 'QQ';
                $userInfo['name'] = $data['nickname'];
                $userInfo['nick'] = $data['nickname'];
                $userInfo['head'] = $data['figureurl_2'];
                echo("<h1>恭喜！使用 {$type} 用户登录成功</h1><br>");
                echo("授权信息为：<br>");
                dump($token);
                echo("当前登录用户信息为：<br>");
                dump($userInfo);*/
            } else {
                throw_exception("获取腾讯QQ用户信息失败：{$data['msg']}");
            }


        }
    }
}