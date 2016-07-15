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

$app->post('/api/test', function(Illuminate\Http\Request $request) {
    $wxcpt = new WechatEnterprise\WXBizMsgCrypt(
        env('WECHATENTERPRISE_TOKEN'),
        env('WECHATENTERPRISE_AESKEY'),
        env('WECHATENTERPRISE_CORPID'));

    $sReqMsgSig = $request->input('msg_signature');
    $sReqTimeStamp = $request->input('timestamp');
    $sReqNonce = $request->input('nonce');

    $sReqData = file_get_contents('php://input');


    $sMsg = ""; // decrypted plain text
    $errCode = $wxcpt->DecryptMsg($sReqMsgSig, $sReqTimeStamp, $sReqNonce, $sReqData, $sMsg);

    // return var_export($sMsg,1);

    if ($errCode == 0) {
        // 解密成功，sMsg即为xml格式的明文
        // TODO: 对明文的处理
        // For example:
        $xml = new \DOMDocument();

        $xml->loadXML($sMsg);

        $msgType = $xml->getElementsByTagName('MsgType')[0]->nodeValue;
        $agentId = $xml->getElementsByTagName('AgentID')[0]->nodeValue;
        $msgId =  $xml->getElementsByTagName('MsgId')[0]->nodeValue;
        $createTime = datetime();
        $fromUserName = $xml->getElementsByTagName('ToUserName')[0]->nodeValue;
        $toUserName = $xml->getElementsByTagName('FromUserName')[0]->nodeValue;

        if ($msgType == 'image')
        {
            // Save the image and response the image Id and the format "%ImgId #tag1 #tag2 #tag3 Comment"
            $sRespData = "<xml><ToUserName><![CDATA[$toUserName]]></ToUserName> <FromUserName><![CDATA[$fromUserName]]></FromUserName><CreateTime>$createTime</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA[%ImgId #tag1 #tag2 #tag3 Comment]]></Content><MsgId>$msgId</MsgId><AgentID>$agentId</AgentID></xml>";
        }
        elseif ($msgType == 'text')
        {
            // Parse the text %ImgId #tag1 #tag2 #tag3 Comment
            $sRespData = "<xml><ToUserName><![CDATA[$toUserName]]></ToUserName> <FromUserName><![CDATA[$fromUserName]]></FromUserName><CreateTime>$createTime</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA[You have tagged the image]]></Content><MsgId>$msgId</MsgId><AgentID>$agentId</AgentID></xml>";
        }

        // Encrypt the message and response to user
        $sEncryptMsg = "";
        $errCode = $wxcpt->EncryptMsg($sRespData, $sReqTimeStamp, $sReqNonce, $sEncryptMsg);
        if ($errCode == 0) {
            // TODO:
            // 加密成功，企业需要将加密之后的sEncryptMsg返回
            // HttpUtils.SetResponce($sEncryptMsg);  //回复加密之后的密文
            return $sEncryptMsg;
        }
        else
        {
            return "ERR: " . $errCode;
            // exit(-1);
        }
    }
    else
    {
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

    $sReqData = file_get_contents('php://input');


    $sMsg = ""; // decrypted plain text
    $errCode = $wxcpt->DecryptMsg($sReqMsgSig, $sReqTimeStamp, $sReqNonce, $sReqData, $sMsg);

    // return var_export($sMsg,1);

    if ($errCode == 0) {
        // 解密成功，sMsg即为xml格式的明文
        // TODO: 对明文的处理
        // For example:
        $xml = new \DOMDocument();

        $xml->loadXML($sMsg);

        $msgType = $xml->getElementsByTagName('MsgType')[0]->nodeValue;
        $agentId = $xml->getElementsByTagName('AgentID')[0]->nodeValue;
        $msgId =  $xml->getElementsByTagName('MsgId')[0]->nodeValue;
        $createTime = datetime();
        $fromUserName = $xml->getElementsByTagName('ToUserName')[0]->nodeValue;
        $toUserName = $xml->getElementsByTagName('FromUserName')[0]->nodeValue;

        if ($msgType == 'image')
        {
            // Save the image and response the image Id and the format "%ImgId #tag1 #tag2 #tag3 Comment"
            $sRespData = "<xml><ToUserName><![CDATA[$toUserName]]></ToUserName><FromUserName><![CDATA[$fromUserName]]></FromUserName><CreateTime>$createTime</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA[%ImgId #tag1 #tag2 #tag3 Comment]]></Content><MsgId>$msgId</MsgId><AgentID>$agentId</AgentID></xml>";
        }
        elseif ($msgType == 'text')
        {
            // Parse the text %ImgId #tag1 #tag2 #tag3 Comment
            $sRespData = "<xml><ToUserName><![CDATA[$toUserName]]></ToUserName><FromUserName><![CDATA[$fromUserName]]></FromUserName><CreateTime>$createTime</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA[You have tagged the image]]></Content><MsgId>$msgId</MsgId><AgentID>$agentId</AgentID></xml>";
        }

        // Encrypt the message and response to user
        $sEncryptMsg = "";
        $errCode = $wxcpt->EncryptMsg($sRespData, $sReqTimeStamp, $sReqNonce, $sEncryptMsg);
        if ($errCode == 0) {
            // TODO:
            // 加密成功，企业需要将加密之后的sEncryptMsg返回
            // HttpUtils.SetResponce($sEncryptMsg);  //回复加密之后的密文
            return $sEncryptMsg;
        }
        else
        {
            return "ERR: " . $errCode;
            // exit(-1);
        }
    }
    else
    {
        return "ERR: " . $errCode;
    }
});