<?php

use plugin\voting\Service;
use think\admin\extend\PhinxExtend;
use think\migration\Migrator;

class InstallVoting extends Migrator
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $this->_create_insertMenu();
        $this->_create_vote_project();
        $this->_create_vote_project_player();
        $this->_create_vote_project_record();
        $this->_create_vote_project_comment();
    }

    /**
     * 创建菜单
     * @return void
     */
    private function _create_insertMenu()
    {
        PhinxExtend::write2menu([
            [
                'name' => '投票管理',
                'subs' => Service::menu(),
            ],
        ], ['node' => 'plugin-voting/portal/index']);
    }

    /**
     * 投票-项目
     * @class VoteProject
     * @table vote_project
     * @return void
     */
    private function _create_vote_project()
    {

        // 当前数据表
        $table = 'vote_project';

        // 存在则跳过
        if ($this->hasTable($table)) return;

        // 创建数据表
        $this->table($table, [
            'engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => '投票-项目',
        ])
            ->addColumn('unid', 'biginteger', ['limit' => 20, 'default' => 0, 'null' => true, 'comment' => '账号编号'])
            ->addColumn('code', 'string', ['limit' => 16, 'default' => '', 'null' => true, 'comment' => '项目编号'])
            ->addColumn('title', 'string', ['limit' => 20, 'default' => '', 'null' => true, 'comment' => '项目标题'])
            ->addColumn('cover', 'string', ['limit' => 500, 'default' => '', 'null' => true, 'comment' => '项目封面'])
            ->addColumn('keywords', 'string', ['limit' => 20, 'default' => '', 'null' => true, 'comment' => '关键字'])
            ->addColumn('describe', 'string', ['limit' => 500, 'default' => '', 'null' => true, 'comment' => '项目描述'])
            ->addColumn('content', 'text', ['default' => null, 'null' => true, 'comment' => '项目内容'])
            ->addColumn('banner', 'string', ['limit' => 500, 'default' => '', 'null' => true, 'comment' => '展示图片'])
            ->addColumn('peritoneums', 'biginteger', ['limit' => 20, 'default' => 0, 'null' => true, 'comment' => '每天可投票的次数,0表示不限制'])
            ->addColumn('extra', 'text', ['default' => NULL, 'null' => true, 'comment' => '扩展数据'])
            ->addColumn('views', 'integer', ['limit' => 20, 'default' => 0, 'null' => true, 'comment' => '累积访问次数'])
            ->addColumn('is_apply', 'integer', ['limit' => 1, 'default' => 0, 'null' => true, 'comment' => '是否开放报名：0否 1是'])
            ->addColumn('is_comment', 'integer', ['limit' => 1, 'default' => 0, 'null' => true, 'comment' => '是否允许评论: 0否 1是'])
            ->addColumn('is_captcha', 'integer', ['limit' => 1, 'default' => 0, 'null' => true, 'comment' => '是否启用验证码: 0否 1是'])
            ->addColumn('start_time', 'datetime', ['default' => NULL, 'null' => true, 'comment' => '开始时间'])
            ->addColumn('end_time', 'datetime', ['default' => NULL, 'null' => true, 'comment' => '结束时间'])
            ->addColumn('remark', 'string', ['limit' => 500, 'default' => '', 'null' => true, 'comment' => '备注(内部使用)'])
            ->addColumn('sort', 'biginteger', ['limit' => 20, 'default' => 0, 'null' => true, 'comment' => '排序权重'])
            ->addColumn('status', 'integer', ['limit' => 1, 'default' => 1, 'null' => true, 'comment' => '项目状态(0拉黑,1正常)'])
            ->addColumn('deleted', 'integer', ['limit' => 1, 'default' => 0, 'null' => true, 'comment' => '删除状态(0未删,1已删)'])
            ->addColumn('create_time', 'datetime', ['default' => NULL, 'null' => true, 'comment' => '创建时间'])
            ->addColumn('update_time', 'datetime', ['default' => NULL, 'null' => true, 'comment' => '更新时间'])
            ->addIndex('unid', ['name' => 'idx_vote_project_unid'])
            ->addIndex('code', ['name' => 'idx_vote_project_code'])
            ->addIndex('title', ['name' => 'idx_vote_project_title'])
            ->addIndex('start_time', ['name' => 'idx_vote_project_start_time'])
            ->addIndex('end_time', ['name' => 'idx_vote_project_end_time'])
            ->addIndex('sort', ['name' => 'idx_vote_project_sort'])
            ->addIndex('status', ['name' => 'idx_vote_project_status'])
            ->addIndex('deleted', ['name' => 'idx_vote_project_deleted'])
            ->addIndex('create_time', ['name' => 'idx_vote_project_create_time'])
            ->create();

        // 修改主键长度
        $this->table($table)->changeColumn('id', 'integer', ['limit' => 11, 'identity' => true]);
    }

    /**
     * 投票-参赛者
     * @class VoteProjectPlayer
     * @table vote_project_player
     * @return void
     */
    private function _create_vote_project_player()
    {

        // 当前数据表
        $table = 'vote_project_player';

        // 存在则跳过
        if ($this->hasTable($table)) return;

        // 创建数据表
        $this->table($table, [
            'engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => '投票-参赛者',
        ])
            ->addColumn('code', 'string', ['limit' => 16, 'default' => '', 'null' => true, 'comment' => '项目编号'])
            ->addColumn('unid', 'biginteger', ['limit' => 20, 'default' => 0, 'null' => true, 'comment' => '账号编号'])
            ->addColumn('cover', 'string', ['limit' => 500, 'default' => '', 'null' => true, 'comment' => '参赛封面'])
            ->addColumn('name', 'string', ['limit' => 20, 'default' => '', 'null' => true, 'comment' => '昵称'])
            ->addColumn('describe', 'string', ['limit' => 500, 'default' => '', 'null' => true, 'comment' => '描述'])
            ->addColumn('content', 'text', ['default' => null, 'null' => true, 'comment' => '参赛内容'])
            ->addColumn('banner', 'string', ['limit' => 500, 'default' => '', 'null' => true, 'comment' => '展示图片'])
            ->addColumn('extra', 'text', ['default' => NULL, 'null' => true, 'comment' => '扩展数据'])
            ->addColumn('views', 'integer', ['limit' => 20, 'default' => 0, 'null' => true, 'comment' => '累积访问数'])
            ->addColumn('votes', 'integer', ['limit' => 20, 'default' => 0, 'null' => true, 'comment' => '实际得票数'])
            ->addColumn('virtually', 'integer', ['limit' => 20, 'default' => 0, 'null' => true, 'comment' => '虚拟得票数'])
            ->addColumn('comments', 'integer', ['limit' => 20, 'default' => 0, 'null' => true, 'comment' => '累积评论数'])
            ->addColumn('sort', 'biginteger', ['limit' => 20, 'default' => 0, 'null' => true, 'comment' => '排序权重'])
            ->addColumn('is_check', 'integer', ['limit' => 1, 'default' => 0, 'null' => true, 'comment' => '审核状态(0待审核,1正常,2拒绝)'])
            ->addColumn('status', 'integer', ['limit' => 1, 'default' => 1, 'null' => true, 'comment' => '参赛状态(0拉黑,1正常)'])
            ->addColumn('deleted', 'integer', ['limit' => 1, 'default' => 0, 'null' => true, 'comment' => '删除状态(0未删,1已删)'])
            ->addColumn('create_time', 'datetime', ['default' => NULL, 'null' => true, 'comment' => '创建时间'])
            ->addColumn('update_time', 'datetime', ['default' => NULL, 'null' => true, 'comment' => '更新时间'])
            ->addIndex('unid', ['name' => 'idx_vote_project_player_unid'])
            ->addIndex('code', ['name' => 'idx_vote_project_player_code'])
            ->addIndex('name', ['name' => 'idx_vote_project_player_name'])
            ->addIndex('views', ['name' => 'idx_vote_project_player_views'])
            ->addIndex('votes', ['name' => 'idx_vote_project_player_votes'])
            ->addIndex('comments', ['name' => 'idx_vote_project_player_comments'])
            ->addIndex('sort', ['name' => 'idx_vote_project_player_sort'])
            ->addIndex('is_check', ['name' => 'idx_vote_project_player_is_check'])
            ->addIndex('status', ['name' => 'idx_vote_project_player_status'])
            ->addIndex('deleted', ['name' => 'idx_vote_project_player_deleted'])
            ->addIndex('create_time', ['name' => 'idx_vote_project_player_create_time'])
            ->create();

        // 修改主键长度
        $this->table($table)->changeColumn('id', 'integer', ['limit' => 11, 'identity' => true]);
    }

    /**
     * 投票-记录
     * @class VoteProjectRecord
     * @table vote_project_record
     * @return void
     */
    private function _create_vote_project_record()
    {

        // 当前数据表
        $table = 'vote_project_record';

        // 存在则跳过
        if ($this->hasTable($table)) return;

        // 创建数据表
        $this->table($table, [
            'engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => '投票-记录',
        ])
            ->addColumn('code', 'string', ['limit' => 16, 'default' => '', 'null' => true, 'comment' => '项目编号'])
            ->addColumn('player_id', 'biginteger', ['limit' => 20, 'default' => 0, 'null' => true, 'comment' => '参赛ID'])
            ->addColumn('unid', 'biginteger', ['limit' => 20, 'default' => 0, 'null' => true, 'comment' => '账号编号'])
            ->addColumn('login_ip', 'string', ['limit' => 32, 'default' => '', 'null' => true, 'comment' => 'IP地址'])
            ->addColumn('address', 'string', ['limit' => 32, 'default' => '', 'null' => true, 'comment' => 'IP归属地'])
            ->addColumn('deleted', 'integer', ['limit' => 1, 'default' => 0, 'null' => true, 'comment' => '删除状态(0未删,1已删)'])
            ->addColumn('create_time', 'datetime', ['default' => NULL, 'null' => true, 'comment' => '创建时间'])
            ->addColumn('update_time', 'datetime', ['default' => NULL, 'null' => true, 'comment' => '更新时间'])
            ->addIndex('unid', ['name' => 'idx_vote_project_record_unid'])
            ->addIndex('code', ['name' => 'idx_vote_project_record_code'])
            ->addIndex('player_id', ['name' => 'idx_vote_project_record_player_id'])
            ->addIndex('deleted', ['name' => 'idx_vote_project_record_deleted'])
            ->addIndex('create_time', ['name' => 'idx_vote_project_record_create_time'])
            ->create();

        // 修改主键长度
        $this->table($table)->changeColumn('id', 'integer', ['limit' => 11, 'identity' => true]);
    }

    /**
     * 投票-评论
     * @class VoteProjectComment
     * @table vote_project_comment
     * @return void
     */
    private function _create_vote_project_comment()
    {

        // 当前数据表
        $table = 'vote_project_comment';

        // 存在则跳过
        if ($this->hasTable($table)) return;

        // 创建数据表
        $this->table($table, [
            'engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => '投票-评论',
        ])
            ->addColumn('code', 'string', ['limit' => 16, 'default' => '', 'null' => true, 'comment' => '项目编号'])
            ->addColumn('player_id', 'biginteger', ['limit' => 20, 'default' => 0, 'null' => true, 'comment' => '参赛ID'])
            ->addColumn('unid', 'biginteger', ['limit' => 20, 'default' => 0, 'null' => true, 'comment' => '账号编号'])
            ->addColumn('comment', 'string', ['limit' => 500, 'default' => '', 'null' => true, 'comment' => '评论内容'])
            ->addColumn('login_ip', 'string', ['limit' => 255, 'default' => '', 'null' => true, 'comment' => 'IP地址'])
            ->addColumn('address', 'string', ['limit' => 32, 'default' => '', 'null' => true, 'comment' => 'IP归属地'])
            ->addColumn('is_check', 'integer', ['limit' => 1, 'default' => 0, 'null' => true, 'comment' => '审核状态(0待审核,1正常,2拒绝)'])
            ->addColumn('status', 'integer', ['limit' => 1, 'default' => 1, 'null' => true, 'comment' => '评论状态(0拉黑,1正常)'])
            ->addColumn('deleted', 'integer', ['limit' => 1, 'default' => 0, 'null' => true, 'comment' => '删除状态(0未删,1已删)'])
            ->addColumn('create_time', 'datetime', ['default' => NULL, 'null' => true, 'comment' => '创建时间'])
            ->addColumn('update_time', 'datetime', ['default' => NULL, 'null' => true, 'comment' => '更新时间'])
            ->addIndex('unid', ['name' => 'idx_vote_project_comment_unid'])
            ->addIndex('code', ['name' => 'idx_vote_project_comment_code'])
            ->addIndex('player_id', ['name' => 'idx_vote_project_comment_player_id'])
            ->addIndex('is_check', ['name' => 'idx_vote_project_comment_is_check'])
            ->addIndex('status', ['name' => 'idx_vote_project_comment_status'])
            ->addIndex('deleted', ['name' => 'idx_vote_project_comment_deleted'])
            ->addIndex('create_time', ['name' => 'idx_vote_project_comment_create_time'])
            ->create();

        // 修改主键长度
        $this->table($table)->changeColumn('id', 'integer', ['limit' => 11, 'identity' => true]);
    }
}
