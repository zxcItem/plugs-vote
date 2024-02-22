<?php

namespace plugin\voting\controller;

use plugin\voting\model\VoteProject;
use plugin\voting\model\VoteProjectRecord;
use think\admin\Controller;
use think\admin\helper\QueryHelper;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * 投票记录管理
 * Class Record
 * @package plugin\voting\controller
 */
class Record extends Controller
{

    /**
     * 投票记录管理
     * @auth true
     * @menu true
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function index()
    {
        VoteProjectRecord::mQuery()->layTable(function () {
            $this->title = '投票记录管理';
            $this->projects = VoteProject::item();
        }, function (QueryHelper $query) {
            $query->with(['projectName','userName','playerName'])->equal('code')->like('login_ip')->dateBetween('create_time');
        });
    }


    /**
     * 数据列表处理
     * @param array $data
     */
    protected function _index_page_filter(array &$data)
    {

    }


    /**
     * 删除投票记录
     * @auth true
     */
    public function remove()
    {
        VoteProjectRecord::mDelete();
    }
}