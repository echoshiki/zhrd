<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class sms_model extends CI_Model{
    /*
     * webservice 插件Id
     */
    private static  $applicationID = 'P000000000000014';

    /*
     * webservice地址
     */
    private static $webserviceUrl = 'http://218.4.197.56:8081/services/cmcc_mas_wbs';

	public function __consturct(){
        parent::__construct();
    }
    /*
     * 对外接口
     *  action: captcha || member || manger
     * $setting = array( 'captchaCode'=>'', 'memberId' =>'','formId' =>'','managerId' =>'',)
     */
	public function send($action, $setting)
    {
	    $result = false;
		if(!is_array($setting) ){
		    return false;
		}

        switch( $action){
            case 'captcha': $result = $this->sendsmsForCaptcha( $setting['captchaCode'],$setting['memberId']);break;
            case 'member':  $result = $this->sendsmsForMember(  $setting['formId'], $setting['memberId']);break;
            case 'manager':  $result = $this->sendsmsForManager( $setting['formId'], $setting['managerId']);break;
        }

        return $result;

	}

    /*
     *验证码短信
     *
     */
	private function sendsmsForCaptcha( $captchaCode, $memberId)
    {
        $template = $this->getSMSTemplate('captcha');
        $companyMessage = $this->db->select('COMPANY,MOBILE')->where('USERID',$memberId)->get('MEMBER')->result_array();

        $template = preg_replace('~\[captcha\]~',$captchaCode,$template);
        $template = preg_replace('~\[company\]~',$companyMessage[0]['COMPANY'],$template);
// return true;
		return $this->_sendSMS($template,$companyMessage[0]['MOBILE']);
		//------------------------
        // $re = $this->sendSMS($template,$companyMessage[0]['MOBILE']);
        // if($re !== false){
            // return $this->getSmsDeliveryStatus($re);
        // }
        // return false;

	}

    /*
     *通知用户短信
     *
     */
	private function sendsmsForMember($formId, $memberId)
    {
		$queryForm = $this->db->select('STATUS')->where('ID',$formId)->get('FORM')->result_array();
		switch( $queryForm[0]['STATUS']){
			case '58': $template = $this->getSMSTemplate('memberPass');break;
			case '210': 
			case '59': $template = $this->getSMSTemplate('memberNotPass');break;
			case '60': $template = $this->getSMSTemplate('memberNeedVouch');break;
		}

		$companyMessage = $this->db->select('COMPANY,MOBILE')->where('USERID',$memberId)->get('MEMBER')->result_array();
        $template = preg_replace('~\[company\]~',$companyMessage[0]['COMPANY'],$template);

		return $this->_sendSMS($template,$companyMessage[0]['MOBILE']);
		//------------------------
		
       // $re = $this->sendSMS($template,$companyMessage[0]['MOBILE']);
       // if($re !== false){
           // return $this->getSmsDeliveryStatus($re);
       // }
       // return false;
	}

    /*
     * 通知客户经理短信
     *
     */
	private function sendsmsForManager($formId, $managerId)
    {
       $template = $this->getSMSTemplate('manage');
	   
		$queryForm = $this->db->select('COMPANY')->where('ID',$formId)->get('FORM')->result_array();
        $template = preg_replace('~\[company\]~',$queryForm[0]['COMPANY'],$template);
		
		$managemessage = $this->db->select('USERNAME,TEL')->where('USERID',$managerId)->get('ADMIN')->result_array();
        $template = preg_replace('~\[manage\]~',$managemessage[0]['USERNAME'],$template);
		
		return $this->_sendSMS($template,$managemessage[0]['TEL']);
		//----------------------
       // $re = $this->sendSMS($template,$address);
       // if($re !== false){
           // return $this->getSmsDeliveryStatus($re);
       // }
       // return false;
	}

    /*
     * 短信模板选择
     */
    private  function getSMSTemplate($type){
        switch($type){
            case 'captcha':         $type = 'smsCaptcha';break;
            case 'memberPass':      $type = 'smsMemberPass';break;
            case 'memberNotPass':   $type = 'smsMemberNotPass';break;
            case 'memberNeedVouch': $type = 'smsMemberNeedVouch';break;
            case 'manage':          $type = 'smsManage';break;
            default: $type = false;
        }
        if($type === false)
            return false;
        $query = $this->db->where('KEY',$type)->get('SETTING')->result_array();
		// print_r( $query);
        return $query[0]['VALUE'];
    }

    /*
     * 发送短信
     */
    private function sendSMS($message,$address)
    {
        $sendSmsXML = '<?xml version="1.0" encoding="GB2312"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
<soap:Body>
<sendSmsRequest xmlns="http://www.csapi.org/schema/sms">
<ApplicationID xmlns="">'.self::$applicationID.'</ApplicationID>
<DestinationAddresses xmlns="">tel:'.$address.'</DestinationAddresses>
<ExtendCode xsi:nil="true" xmlns="" />
<Message xmlns="">'.$message.'</Message>
<MessageFormat xmlns="">GB2312</MessageFormat>
<SendMethod xmlns="">Long</SendMethod>
<DeliveryResultRequest xmlns="">true</DeliveryResultRequest>
</sendSmsRequest>
</soap:Body>
</soap:Envelope>';
		// return 'test';
        //curl
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::$webserviceUrl);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER,array('SOAPAction: "http://www.csapi.org/service/sendSms"'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $sendSmsXML);
        $data = curl_exec($ch);
        curl_close($ch);

        if( preg_match('~<RequestIdentifier.*?>([-a-zA-Z0-9]*?)</RequestIdentifier>~',$data,$d) ){
            return $d[1];
        }

        return false;
    }
    /*
     * 接受短信信息状态
     */
    private  function  getSmsDeliveryStatus($smsID)
    {
        $getSmsStatusXML = '<?xml version="1.0" encoding="GB2312"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
<soap:Body>
<GetSmsDeliveryStatusRequest xmlns="http://www.csapi.org/schema/sms">
<ApplicationID xmlns="">'.self::$applicationID.'</ApplicationID>
<RequestIdentifier xmlns="">'.$smsID.'</RequestIdentifier>
</GetSmsDeliveryStatusRequest>
</soap:Body>
</soap:Envelope>';
		// return TRUE;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::$webserviceUrl);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER,array('SOAPAction: "http://www.csapi.org/service/GetSmsDeliveryStatus"'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $getSmsStatusXML);
        $data = curl_exec($ch);
        curl_close($ch);
        if( preg_match('~<DeliveryStatus>([A-Za-z0-9]*?)</DeliveryStatus>~',$data,$d)){
            return $d[1];
        }
        return false;
    }


	private function _sendSMS($message,$address)
	{
		$sendSmsXML = '<?xml version="1.0" encoding="utf-8"?>'
.'<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:soapenc="http://schemas.xmlsoap.org/soap/encoding/" xmlns:tns="http://218.205.11.146:81/services/RemoteSendSMS" xmlns:types="http://218.205.11.146:81/services/RemoteSendSMS/encodedTypes" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">'
.'<soap:Body soap:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">'
.'<q1:sendSMS xmlns:q1="http://send.axis.webservice.service.mcc.roya.com">'
.'<exNumber xsi:type="xsd:string">123</exNumber>'
.'<phone xsi:type="xsd:string">' .$address. '</phone>'
.'<msg xsi:type="xsd:string">' .$message. '</msg>'
.'<timeFlag xsi:type="xsd:int">0</timeFlag>'
.'<smsTime xsi:type="xsd:long">' . time()*1000 . '</smsTime>'
.'<msgFlag xsi:type="xsd:string">backup</msgFlag>'
.'</q1:sendSMS>'
.'</soap:Body>'
.'</soap:Envelope>';

		$url = 'http://218.205.11.146:81/services/RemoteSendSMS';
		
		//curl
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_HTTPHEADER,array('SOAPAction: ""'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $sendSmsXML);
curl_setopt($ch, CURLOPT_TIMEOUT, 8);
		$data = curl_exec($ch);
		curl_close($ch);
        if( preg_match('~<multiRef.*?>([0-9]*?)</multiRef>~',$data,$d) ){
			if($d[1]  == '0')
				return TRUE;
        }
		return FALSE;
	
	}

}
