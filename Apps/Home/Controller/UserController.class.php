<?php
namespace Home\Controller;

class UserController extends BaseController
{
    /**
     * 退出
     *
     * Author topshare@vip.qq.com
     *
     * @return string
     */
    public function logout()
    {
        if(is_login()){
            D('User')->logout();
            session('[destroy]');
            $this->success('退出成功！', get_redirect_url());
        } else {
            $this->redirect('login');
        }
    }

    public function profile()
    {
        $this->display();
    }

    public function login()
    {
        $this->display();
    }
}