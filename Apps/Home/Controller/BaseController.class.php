<?php
namespace Home\Controller;

use Think\Controller;

class BaseController extends Controller
{
    public function _initialize()
    {

    }

    /**
     * 404
     *
     * @return void
     */
    public function _empty()
    {
        @header("http/1.1 404 not found");
        @header("status: 404 not found");
        $this->display('Public/error');
        exit();
    }

    public function check_login()
    {
        null !== session('user_auth.user_id') || $this->redirect('/user/login');
    }
}