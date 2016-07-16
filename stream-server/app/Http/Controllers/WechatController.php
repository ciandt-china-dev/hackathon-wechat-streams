<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MsgHandles;
use WechatEnterprise;

class WechatController extends Controller
{

  protected $wxcpt;

  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
    $this->wxcpt = new WechatEnterprise\WXBizMsgCrypt(
      env('WECHATENTERPRISE_TOKEN'),
      env('WECHATENTERPRISE_AESKEY'),
      env('WECHATENTERPRISE_CORPID'));

  }

  /**
   * Verify wechat.
   */
  public function valid(Request $request)
  {
      $sVerifyMsgSig = $request->input('msg_signature');
      $sVerifyTimeStamp = $request->input('timestamp');
      $sVerifyNonce = $request->input('nonce');
      $sVerifyEchoStr = $request->input('echostr');

      $errCode = $this->wxcpt->VerifyURL($sVerifyMsgSig, $sVerifyTimeStamp, $sVerifyNonce, $sVerifyEchoStr, $sEchoStr);

      return $errCode == 0 ? $sEchoStr : "ERR: " . $errCode;
  }

  /**
   * Recive Message.
   */
  public function reciveMsg(Request $request)
  {
    $sReqMsgSig = $request->input('msg_signature');
    $sReqTimeStamp = $request->input('timestamp');
    $sReqNonce = $request->input('nonce');

    $sReqData = file_get_contents('php://input');

    $errCode = $this->wxcpt->DecryptMsg($sReqMsgSig, $sReqTimeStamp, $sReqNonce, $sReqData, $sMsg);

    if ($errCode == 0)
    {
      $xml = new \DOMDocument();
      $xml->loadXML($sMsg);
      $msgType = $xml->getElementsByTagName('MsgType')[0]->nodeValue;

      // todo use service provider.
      $handle = null;
      switch ($msgType) {
        case 'text':
          $handle = new MsgHandles\TextMsgHandle();
          break;
        case 'image':
          $handle = new MsgHandles\ImageMsgHandle();
          break;
      }
      
      $msg = $handle->setXml($xml)->exec();

      //error_log($msg);

      $sEncryptMsg = '';
      $errCode = $this->wxcpt->EncryptMsg($msg, $sReqTimeStamp, $sReqNonce, $sEncryptMsg);
      if ($errCode == 0) {
        return $sEncryptMsg;
      }
    }

    return 'Err: ' . $errCode;

  }
}
