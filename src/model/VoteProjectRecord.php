<?php

namespace plugin\voting\model;


use plugin\account\model\AccountUser;
use plugin\account\model\Abs;
use think\model\relation\BelongsTo;

/**
 * 投票记录管理
 */
class VoteProjectRecord extends Abs
{

    /**
     * 关联项目
     * @return BelongsTo
     */
    public function projectName(): BelongsTo
    {
        return $this->belongsTo(VoteProject::class,'code','code')->bind(['project_name'=>'title']);
    }

    /**
     * 关联用户
     * @return BelongsTo
     */
    public function userName(): BelongsTo
    {
        return $this->belongsTo(AccountUser::class,'unid','id')->bind(['nickname']);
    }

    /**
     * 关联选手
     * @return BelongsTo
     */
    public function playerName(): BelongsTo
    {
        return $this->belongsTo(VoteProjectPlayer::class,'player_id','id')->bind(['player_name'=>'name']);
    }
}