<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        //print_r(session('user_auth'));
        $this->display();
    }
}