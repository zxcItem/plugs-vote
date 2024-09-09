<?php

namespace plugin\voting\model;


use plugin\account\model\Abs;
use think\model\relation\BelongsTo;
use think\model\relation\HasMany;

/**
 * 投票选手管理
 */
class PluginVoteProjectPlayer extends Abs
{

    /**
     * 关联项目
     * @return BelongsTo
     */
    public function projectName(): BelongsTo
    {
        return $this->belongsTo(PluginVoteProject::class,'code','code')->bind(['project_name'=>'title']);
    }

    /**
     * 一对多关联投票记录
     * @return HasMany
     */
    public function record()
    {
        return $this->hasMany(PluginVoteProjectRecord::class,'player_id');
    }

}