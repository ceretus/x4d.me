<?php
namespace Material\Controller;

use Org\Util\PinYin;
use Think\Controller;

class ToolController extends Controller
{
    /**
     * 更新数据
     *
     * Author xuyufei@jingge.cc
     *
     * @return string
     */
    public function index()
    {
        @ini_set('memory_limit', '4000M');

        // 文件锁避免重复执行
        $fp = fopen(__FILE__ . '.lock', 'c');
        if (!flock($fp, LOCK_EX | LOCK_NB)) {
            exit('Another Process Is Running');
        }

        $maxId = 0;
        $count = $this->countList('Material', 'id', $maxId);
        for ($i = 0; $i < $count; $i += 100) {
            $dataList = $this->getList('Material', 'id', $maxId, $i, 100);
            foreach ($dataList as $data) {
                $this->do_material_en($data);
            }
        }
    }

    private function do_material_en($data)
    {
        $result = M('Material')->save(array(
            'id' => $data['id'],
            'en' => PinYin::toPingyin($data['name'])
        ));
        if (false === $result) {
            echo "failure \n";
        } else {
            echo "success \n";
        }
    }

    private function countList($table, $pk, $maxId)
    {
        return M($table)->where("$pk > $maxId")->count();
    }

    private function getList($table, $pk, $maxId, $offset, $limit = 1000)
    {
        return M($table)->where("$pk > $maxId")->limit($offset, $limit)->select();
    }
}