<?php

use Chenzh\WechatArticle\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::get('wechat-article', Controllers\WechatArticleController::class.'@index');
Route::any('wechat-check-sign', Controllers\WechatArticleController::class.'@checkSignature');
