<?php
namespace Home\Controller;

class RecipeController extends BaseController
{
    public function index()
    {
        $this->display();
    }

    public function lists()
    {
        $this->display();
    }

    public function info()
    {
        $this->display();
    }

    public function create()
    {
        if (IS_POST) {

        } else {
            //$this->check_login();
            $this->display();
        }
    }
}