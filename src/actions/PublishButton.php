<?php

namespace Chenzh\WechatArticle\actions;

use Dcat\Admin\Actions\Action;
use App\Admin\Controllers\SettingController;
use Dcat\Admin\Actions\Response;
use Chenzh\WechatArticle\WechatArticleServiceProvider;
use EasyWeChat\Factory;
use EasyWeChat\Kernel\Messages\Article;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PublishButton extends Action
{

    public $model;
    protected $contentField = 'content';
    protected $titleField = 'title';
    protected $coverImageField = 'cover_image';

    public function __construct($title = null)
    {
        parent::__construct($title);
        if ($title) {
            $this->title = $title;
        } else {
            $this->title = WechatArticleServiceProvider::trans('wechat-article.publish');
        }
    }
    public function getTitle()
    {
        return WechatArticleServiceProvider::trans('wechat-article.publish');
    }

    public function getPublishConfirm()
    {
        return WechatArticleServiceProvider::trans('wechat-article.publish_confirm');
    }

    public function setModel($model)
    {
        $this->model = $model;
        return $this;
    }

    public function setContentField($value)
    {
        $this->contentField = $value;
        return $this;
    }

    public function setTitleField($value)
    {
        $this->titleField = $value;
        return $this;
    }

    public function setCoverImageField($value)
    {
        $this->coverImageField = $value;
        return $this;
    }

    protected function parameters()
    {
        return [
            'model' => $this->model,
            'contentField' => $this->contentField,
            'titleField' => $this->titleField,
            'coverImageField' => $this->coverImageField,
        ];
    }

    /**
     * 处理动作逻辑.
     *
     * @return Response
     */
    public function handle()
    {
        // 处理导出
        $id = $this->getKey();
        $model = request()->input('model');
        $coverImageField = request()->input('coverImageField');
        $titleField = request()->input('titleField');
        $contentField = request()->input('contentField');

        $reflectionClass = new \ReflectionClass($model);
        $instance = $reflectionClass->newInstance();


        $info = $instance->newQuery()->where('id', $id)->firstOrFail();

        $configSet = WechatArticleServiceProvider::setting('config');
        if ($configSet) {
            $config = config($configSet);
        } else {
            $config = [
                'app_id' => WechatArticleServiceProvider::setting('wx_app_id'),
                'secret' => WechatArticleServiceProvider::setting('wx_app_secret'),
                'response_type' => 'array',
            ];
        }

        // 上传永久素材
        $app = Factory::officialAccount($config);

        $result = $app->material->uploadThumb(Storage::disk('public')->path($info->{$coverImageField}));
        // {
        //    "media_id":MEDIA_ID,
        //    "url":URL
        // }
        Log::info($info->{$contentField});
        // 上传单篇图文
        $article = [
            'title' => $info->{$titleField},
            'thumb_media_id' => $result['media_id'],
            "content" => $info->{$contentField},
            "show_cover" => 0,
        ];
        $result = $app->draft->add(['articles' => [
            $article
        ]]);

        Log::info(json_encode($result, JSON_UNESCAPED_UNICODE));
        $result = $app->free_publish->submit($result['media_id']);
        Log::info(json_encode($result, JSON_UNESCAPED_UNICODE));
        return $this->response()
            ->success('success')
            ->refresh();
    }

    /**
     * 对话框.
     *
     * @return string[]
     */
    public function confirm()
    {
        return [$this->getTitle(), $this->getPublishConfirm()];
    }

}
