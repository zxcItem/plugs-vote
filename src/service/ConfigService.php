<?php

declare (strict_types=1);

namespace plugin\voting\service;

use think\admin\Exception;

/**
 * 投票配置服务
 * @class ConfigService
 * @package plugin\voting\service
 */
class ConfigService
{

    /**
     * 投票配置缓存名
     * @var string
     */
    private static $skey = 'plugin.voting.config';

    /**
     * 页面类型配置
     * @var string[]
     */
    public static $pageTypes = [
        [
            'name' => 'user_agreement',
            'title' => '用户使用协议',
            'temp'  => 'content'
        ],
        [
            'name' => 'slider_page',
            'title' => '首页轮播',
            'temp'  => 'slider'
        ]
    ];

    /**
     * 类型配置获取
     * @param string $name
     * @return mixed
     */
    public static function pageTypes(string $name)
    {
        return array_column(self::$pageTypes,'title','name')[$name];
    }

    /**
     * 读取投票配置参数
     * @param string|null $name
     * @param $default
     * @return array|mixed|null
     * @throws Exception
     */
    public static function get(?string $name = null, $default = null)
    {
        $syscfg = sysvar(self::$skey) ?: sysvar(self::$skey, sysdata(self::$skey));
        return is_null($name) ? $syscfg : ($syscfg[$name] ?? $default);
    }

    /**
     * 保存投票配置参数
     * @param array $data
     * @return mixed
     * @throws Exception
     */
    public static function set(array $data)
    {
        return sysdata(self::$skey, $data);
    }

    /**
     * 设置页面数据
     * @param string $code 页面编码
     * @param array $data 页面内容
     * @return mixed
     * @throws Exception
     */
    public static function setPage(string $code, array $data)
    {
        return sysdata("voting.page.{$code}", $data);
    }

    /**
     * 获取页面内容
     * @param string $code
     * @return array
     * @throws Exception
     */
    public static function getPage(string $code): array
    {
        return sysdata("voting.page.{$code}");
    }
}