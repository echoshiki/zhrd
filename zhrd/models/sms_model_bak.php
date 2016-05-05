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
            case 'manger':  $result = $this->sendsmsForManager( $setting['formId'], $setting['managerId']);break;
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
        $re = $this->sendSMS($template,$companyMessage[0]['MOBILE']);

        if($re !== false){
            return $this->getSmsDeliveryStatus($re);
        }
        return false;

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
			case '59': $template = $this->getSMSTemplate('memberNotPass');break;
			case '60': $template = $this->getSMSTemplate('memberNeedVouch');break;
		}

		$companyMessage = $this->db->select('COMPANY')->where('USERID',$memberId)->get('MEMBER')->result_array();
        $template = preg_replace('~\[company\]~',$companyMessage[0]['COMPANY'],$template);

       $re = $this->sendSMS($template,$address);
       if($re !== false){
           return $this->getSmsDeliveryStatus($re);
       }
       return false;
	}

    /*
     * 通知客户经理短信
     *
     */
	private function sendsmsForManager($formId, $managerId)
    {
       $template = $this->getSMSTemplate('manage');
	   
		$companyMessage = $this->db->select('COMPANY')->where('USERID',$memberId)->get('MEMBER')->result_array();
        $template = preg_replace('~\[company\]~',$companyMessage[0]['COMPANY'],$template);
		
		$companyMessage = $this->db->select('USERNAME')->where('USERID',$memberId)->get('ADMIN')->result_array();
        $template = preg_replace('~\[manage\]~',$companyMessage[0]['USERNAME'],$template);
		
       $re = $this->sendSMS($template,$address);
       if($re !== false){
           return $this->getSmsDeliveryStatus($re);
       }
       return false;
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



}