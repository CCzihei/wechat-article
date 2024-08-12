<?php

namespace Chenzh\WechatArticle\Http\Controllers;

use Chenzh\WechatArticle\WechatArticleServiceProvider;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;
use EasyWeChat\Factory;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class WechatArticleController extends Controller
{
    public function index(Content $content)
    {


        return $content
            ->title('Title')
            ->description('Description')
            ->body(Admin::view('chenzh.wechat-article::index'));
    }


    public function checkSignature()
    {
        try {

            $signature = $_GET["signature"];
            $timestamp = $_GET["timestamp"];
            $nonce = $_GET["nonce"];

            $token = WechatArticleServiceProvider::setting('check_sign_token');
            $tmpArr = array($token, $timestamp, $nonce);
            sort($tmpArr, SORT_STRING);
            $tmpStr = implode( $tmpArr );
            $tmpStr = sha1( $tmpStr );
            if( $tmpStr == $signature ){
                return $_GET['echostr'];
            }else{
                return false;
            }
        } catch (\Throwable $e) {
            Log::info("checkSignature:" . $e->getMessage());
            exit($e->getMessage());
        }

    }
}
