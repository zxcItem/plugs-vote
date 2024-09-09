<?php

namespace plugin\voting\controller;

use plugin\voting\model\PluginVoteProject;
use plugin\voting\model\PluginVoteProjectComment;
use plugin\voting\model\PluginVoteProjectPlayer;
use plugin\voting\model\PluginVoteProjectRecord;
use think\admin\Controller;
use think\admin\extend\CodeExtend;
use think\admin\helper\QueryHelper;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * 投票项目管理
 * Class Project
 * @package plugin\voting\controller
 */
class Project extends Controller
{

    /**
     * 投票项目管理
     * @auth true
     * @menu true
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function index()
    {
        $this->type = $this->get['type'] ?? 'index';
        PluginVoteProject::mQuery()->layTable(function () {
            $this->title = '投票项目管理';
        }, function (QueryHelper $query) {
            $query->like('code,title')->equal('status')->dateBetween('create_time');
            $query->where(['deleted' => 0, 'status' => intval($this->type === 'index')]);
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
     * 表单数据处理
     * @param array $data
     */
    protected function _form_filter(array &$data)
    {
        if (empty($data['code'])) $data['code'] = CodeExtend::uniqidNumber(5, 'T');
    }

    /**
     * 添加投票项目
     * @auth true
     */
    public function add()
    {
        PluginVoteProject::mForm('form');
    }

    /**
     * 编辑投票项目
     * @auth true
     */
    public function edit()
    {
        PluginVoteProject::mForm('form');
    }

    /**
     * 修改投票项目状态
     * @auth true
     */
    public function state()
    {
        PluginVoteProject::mSave($this->_vali([
            'status.in:0,1'  => '状态值范围异常！',
            'status.require' => '状态值不能为空！',
        ]),'code');
    }

    /**
     * 状态更新回调
     * @param bool $result
     * @return void
     */
    protected function _save_result(bool $result){
        if ($result) {
            PluginVoteProjectPlayer::mk()->where('code',input('code'))->save(['status'=>input('status')]);
        }
    }

    /**
     * 删除投票项目
     * @auth true
     */
    public function remove()
    {
        PluginVoteProject::mDelete();
    }

    /**
     * 项目选择
     * @login true
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function select()
    {
        $this->get['status'] = 1;
        $this->index();
    }

    /**
     * 定时更新
     * TODO
     * @auth true
     */
    public function views()
    {
        $this->_queue(CodeExtend::uniqidDate(), 'xdata:Message', 60, ['code'=>input('code')],0,60);
    }

    /**
     * 项目统计报表
     * @auth true
     * @return void
     * @throws DbException
     */
    public function total()
    {
        $code = $this->request->param('code');
        $this->recordTotal = PluginVoteProjectRecord::mk()->where(['code'=>$code,'deleted'=>0])->cache(true, 60)->count();
        $this->userTotal = count(array_unique(PluginVoteProjectRecord::mk()->where(['code'=>$code,'deleted'=>0])->cache(true, 60)->column('unid')));
        $this->playerTotal = PluginVoteProjectPlayer::mk()->where(['code'=>$code,'is_check'=>1,'status'=>1,'deleted'=>0])->cache(true, 60)->count();
        $this->commentTotal = PluginVoteProjectComment::mk()->where(['code'=>$code,'deleted'=>0])->cache(true, 60)->count();
        $this->playerList = PluginVoteProjectPlayer::mk()->where(['code'=>$code,'is_check'=>1,'status'=>1,'deleted'=>0])->field('id,name')->withCount(['record'=>'count'])->cache(true, 60)->select()->toArray();

        for ($i = 0; $i < 24; $i++) {
            $date = date('Y-m-d H',strtotime(date('Y-m-d')) + $i * 3600);
            $this->todayHours[] = [
                '当天时间' => date('H:i', strtotime(date('Y-m-d')) + $i * 3600),
                '今日统计' => PluginVoteProjectRecord::mk()->where(['code'=>$code,'deleted'=>0])->whereLike('create_time', "{$date}%")->count()
            ];
        }

        $this->todayRecord = PluginVoteProjectRecord::mk()->where(['code'=>$code,'deleted'=>0])->whereDay('create_time')->cache(true, 60)->count();
        $this->todayUser = count(array_unique(PluginVoteProjectRecord::mk()->where(['code'=>$code,'deleted'=>0])->whereDay('create_time')->cache(true, 60)->column('unid')));
        $this->todayPlayer = PluginVoteProjectPlayer::mk()->where(['code'=>$code,'is_check'=>0,'status'=>1,'deleted'=>0])->cache(true, 60)->count();
        $this->todayComment = PluginVoteProjectComment::mk()->where(['code'=>$code,'is_check'=>1,'deleted'=>0])->cache(true, 60)->count();

        for ($i = 30; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i}days"));
            $this->proDays[] = [
                '当天日期' => date('m-d', strtotime("-{$i}days")),
                '投票统计' => PluginVoteProjectRecord::mk()->where(['code'=>$code,'deleted'=>0])->whereLike('create_time', "{$date}%")->count(),
            ];
        }
        $this->fetch();
    }
}