<?php

namespace Chenzh\WechatArticle;

use Dcat\Admin\Extend\Setting as Form;

class Setting extends Form
{
    public function form()
    {
        $this->text('wx_app_id')->required();
        $this->text('wx_app_secret')->required();
        $this->text('config')->placeholder("like:wechat.official");
        $this->text('check_sign_token')->placeholder("配置公众号服务器");
    }
}
