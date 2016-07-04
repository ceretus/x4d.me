<?php
/**
 * 用户
 *
 * PHP Version 5
 *
 * @category  Model
 * @package   Home
 * @author    xuyufei <xuyufei@jingge.cc>
 * @time      15/05/19
 * @copyright 2015 精格网络科技（北京）有限公司
 * @license   http://www.haoxue.com license
 * @link      xuyufei@jingge.cc
 */
namespace Home\Model;
use Think\Model;

/**
 * User
 *
 * @category Model
 * @package  Home
 * @author   xuyufei <xuyufei@jingge.cc>
 * @license  http://www.haoxue.com license
 * @link     xuyufei@jingge.cc
 */
class UserModel extends Model
{
    /**
     * 自动完成
     *
     * @var array
     */
    protected $_auto = array (
        //array('status', 1),
        //array('password', 'think_ucenter_md5', self::MODEL_BOTH, 'function'),
    );

    /**
     * 登录
     *
     * Author xuyufei@jingge.cc
     *
     * @param int $userId 用户数据
     */
    public function login($user)
    {
        $this->autoLogin($user);
    }

    /**
     * 自动登录
     *
     * Author xuyufei@jingge.cc
     *
     * @param array $user 用户信息
     */
    private function autoLogin($user)
    {
        $this->save(array(
            'id' => $user['id'],
            'login_times' => array('exp', '`login_times`+1'),
            'last_login_ip' => get_client_ip(0,true),
            'last_login_time' => date('Y-m-d H:i:s', time())
        ));
        $auth = array(
            'user_id' => $user['id'],
            'type' => $user['type'],
            'username' => $user['username'],
            'last_login_time' => $user['last_login_time'],
        );
        session('user_auth', $auth);
        session('user_auth_sign', data_auth_sign($auth));
        $this->updateToken($user);
    }

    /**
     * 更新token
     *
     * @param $user
     */
    private function updateToken($user)
    {
        $this->save(array(
            'id' => $user['id'],
            'token' => session_id()
        ));
    }

    /**
     * 注销当前用户
     * @return void
     */
    public function logout(){
        session('user_auth', null);
        session('user_auth_sign', null);
    }
}
