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
$app->post('/api/streams', function(Illuminate\Http\Request $request) use ($app) {
    $wxcpt = new WechatEnterprise\WXBizMsgCrypt(
        env('WECHATENTERPRISE_TOKEN'),
        env('WECHATENTERPRISE_AESKEY'),
        env('WECHATENTERPRISE_CORPID'));

    $sReqMsgSig = $request->input('msg_signature');
    $sReqTimeStamp = $request->input('timestamp');
    $sReqNonce = $request->input('nonce');

    // Obtain the raw data from wechat server
    $sReqData = file_get_contents('php://input');

    $sMsg = ""; // decrypted plain text
    $errCode = $wxcpt->DecryptMsg($sReqMsgSig, $sReqTimeStamp, $sReqNonce, $sReqData, $sMsg);

    if ($errCode == 0) {
        $xml = new \DOMDocument();
        $xml->loadXML($sMsg);

        $msgType = $xml->getElementsByTagName('MsgType')->item(0)->nodeValue;
        $fromUserName = $xml->getElementsByTagName('ToUserName')->item(0)->nodeValue;
        $toUserName = $xml->getElementsByTagName('FromUserName')->item(0)->nodeValue;

        if ($msgType == 'image')
        {
            $sReqData = "<xml>
   <ToUserName><![CDATA[$toUserName]]></ToUserName>
   <FromUserName><![CDATA[$fromUserName]]></FromUserName> 
   <CreateTime>$sReqTimeStamp</CreateTime>
   <MsgType><![CDATA[text]]></MsgType>
   <Content><![CDATA[%ImgId #tag1 #tag2 #tag3 Comment]]></Content>
</xml>";
        }
        elseif ($msgType == 'text')
        {
            // Todo: the message should display depends on when the user's input is valid
            $message = "You have successfully tagged your photo";

            // Parse the text %ImgId #tag1 #tag2 #tag3 Comment
            $sReqData = "<xml>
   <ToUserName><![CDATA[$toUserName]]></ToUserName>
   <FromUserName><![CDATA[$fromUserName]]></FromUserName> 
   <CreateTime>$sReqTimeStamp</CreateTime>
   <MsgType><![CDATA[text]]></MsgType>
   <Content><![CDATA[$message]]></Content>
</xml>";
        }

        // Encrypt the message and response to user
        $sEncryptMsg = "";
        $errCode = $wxcpt->EncryptMsg($sReqData, $sReqTimeStamp, $sReqNonce, $sEncryptMsg);

        if ($errCode == 0) {
            return $sEncryptMsg;
        }
        else
        {
            return "ERR: " . $errCode;
        }
    }
    else
    {
        return "ERR: " . $errCode;
    }
});

$app->get('/api/photos/{tag}', 'PhotoController@index');