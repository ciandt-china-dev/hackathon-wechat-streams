<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$app->get('/', function () use ($app) {
    return $app->version();
});


/**
 * Verify wechat
 */
$app->get('/api/streams', function (Illuminate\Http\Request $request) use ($app) {
    $wxcpt = new WechatEnterprise\WXBizMsgCrypt(
        env('WECHATENTERPRISE_TOKEN'),
        env('WECHATENTERPRISE_AESKEY'),
        env('WECHATENTERPRISE_CORPID'));

    $sVerifyMsgSig = $request->input('msg_signature');
    $sVerifyTimeStamp = $request->input('timestamp');
    $sVerifyNonce = $request->input('nonce');
    $sVerifyEchoStr = $request->input('echostr');
    $errCode = $wxcpt->VerifyURL($sVerifyMsgSig, $sVerifyTimeStamp, $sVerifyNonce, $sVerifyEchoStr, $sEchoStr);

    if ($errCode == 0) {
        //
        // 验证URL成功，将sEchoStr返回
        // HttpUtils.SetResponce($sEchoStr);
        return $sEchoStr;
    } else {
        return "ERR: " . $errCode;
    }
});

/**
 * Handler wechat response and trigger proper methods
 */
$app->post('/api/streams', function() use ($app) {

});