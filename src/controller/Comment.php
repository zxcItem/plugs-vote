<?php

namespace app\vote\controller;

use plugin\vote\model\VoteProject;
use plugin\vote\model\VoteProjectComment;
use think\admin\Controller;
use think\admin\helper\QueryHelper;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * 投票评论管理
 * Class Comment
 * @package plugin\vote\controller
 */
class Comment extends Controller
{

    /**
     * 投票评论管理
     * @auth true
     * @menu true
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function index()
    {
        VoteProjectComment::mQuery()->layTable(function () {
            $this->title = '投票评论管理';
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
     * 编辑投票评论
     * @auth true
     */
    public function edit()
    {
        VoteProjectComment::mForm('form');
    }


    /**
     * 删除投票评论
     * @auth true
     */
    public function remove()
    {
        VoteProjectComment::mDelete();
    }
}