<?php

namespace Chenzh\WechatArticle;

use Dcat\Admin\Extend\ServiceProvider;
use Dcat\Admin\Admin;

class WechatArticleServiceProvider extends ServiceProvider
{
	protected $js = [
        'js/index.js',
    ];
	protected $css = [
		'css/index.css',
	];

    /**
     * 路由过滤
     */
    protected $exceptRoutes = [
        'auth' => 'wechat-check-sign',
        'permission' => 'wechat-check-sign',
    ];

	public function register()
	{
		//

    }

	public function init()
	{
		parent::init();

		//
		
	}

	public function settingForm()
	{
		return new Setting($this);
	}
}
