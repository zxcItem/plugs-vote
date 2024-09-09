<?php

namespace plugin\voting\controller;

use plugin\voting\model\PluginVoteProject;
use plugin\voting\model\PluginVoteProjectComment;
use think\admin\Controller;
use think\admin\helper\QueryHelper;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * 投票评论管理
 * Class Comment
 * @package plugin\voting\controller
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
        PluginVoteProjectComment::mQuery()->layTable(function () {
            $this->title = '投票评论管理';
            $this->projects = PluginVoteProject::item();
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
        PluginVoteProjectComment::mForm('form');
    }


    /**
     * 删除投票评论
     * @auth true
     */
    public function remove()
    {
        PluginVoteProjectComment::mDelete();
    }
}