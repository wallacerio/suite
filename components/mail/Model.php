<?php

class Model{


	public function mail($currentDir,$args){

		$phpMailerFile = $currentDir . 'libs/phpmailer/PHPMailerAutoload.php';

		$host = isset($args['host'])?$args['host']:null;
		$username = isset($args['username'])?$args['username']:null;
		$password = isset($args['password'])?$args['password']:null;
		$secure = isset($args['secure'])?$args['secure']:'ssl';
		$port = isset($args['port'])?$args['port']:465;
		$from = isset($args['from'])?$args['from']:null;
		$to = isset($args['to'])?$args['to']:null;
		$cc = isset($args['cc'])?$args['cc']:null;
		$bcc = isset($args['bcc'])?$args['bcc']:null;
		$replyto = isset($args['replyto'])?$args['replyto']:null;		
		$returnpath = isset($args['returnpath'])?$args['returnpath']:null;		
		$subject = isset($args['subject'])?$args['subject']:'';
		$msg = isset($args['msg'])?$args['msg']:'';
		$msgAlt = isset($args['msgalt'])?$args['msgalt']:'';		
		$methodmail = isset($args['methodmail'])?$args['methodmail']:null;
		$contenttype = isset($args['contenttype'])?$args['contenttype']:'text/html';
		$charset = isset($args['charset'])?$args['charset']:'utf-8';
		$parametersOptional = isset($args['parameters-optional'])?$args['parameters-optional']:null;
		$successmessage = isset($args['successmessage'])?$args['successmessage']:null;

		// headers --------------------------------------------
		$breakline = "\r\n";

		$headers  = 'MIME-Version: 1.0' . $breakline;
		$headers .= 'Content-type: '.$contenttype.'; charset='.$charset . $breakline;

		// if($to != null) $headers = 'To: '.$to . $breakline;
		if($cc != null) $headers = 'Cc: '.$cc . $breakline;
		if($bcc != null) $headers = 'Bcc: '.$bcc . $breakline;
		if($from != null) $headers = 'From: '.$from . $breakline;
		if($replyto != null) $headers .= 'Reply-To: ' . $replyto . $breakline ;
		if($returnpath != null) $headers .= 'Return-Path: ' . $returnpath . $breakline ;



		if(mail($to,$subject,$msg,$headers,$parametersOptional)){
			$status = 'success';
			$msg = '';
		}else{
			$status = 'error';
			$msg = 'error on send mail';
		}


		return json_encode(array('status'=>$status,'msg'=>$msg));

		
	}

	public function phpmailer($currentDir,$args){


		$phpMailerFile = $currentDir . 'libs/phpmailer/PHPMailerAutoload.php';
		require $phpMailerFile;


		

		$host = isset($args['host'])?$args['host']:null;
		$username = isset($args['username'])?$args['username']:null;
		$password = isset($args['password'])?$args['password']:null;
		$secure = isset($args['secure'])?$args['secure']:'ssl';
		$port = isset($args['port'])?$args['port']:465;
		$from = isset($args['from'])?$args['from']:null;
		$to = isset($args['to'])?$args['to']:null;
		$cc = isset($args['cc'])?$args['cc']:null;
		$bcc = isset($args['bcc'])?$args['bcc']:null;
		$replyto = isset($args['replyto'])?$args['replyto']:null;		
		$returnpath = isset($args['returnpath'])?$args['returnpath']:null;		
		$subject = isset($args['subject'])?$args['subject']:'';
		$msg = isset($args['msg'])?$args['msg']:'';
		$msgAlt = isset($args['msgalt'])?$args['msgalt']:'';		
		$methodmail = isset($args['methodmail'])?$args['methodmail']:null;
		$contenttype = isset($args['contenttype'])?$args['contenttype']:'text/html';
		$charset = isset($args['charset'])?$args['charset']:'utf-8';
		$parametersOptional = isset($args['parameters-optional'])?$args['parameters-optional']:null;
		$successmessage = isset($args['successmessage'])?$args['successmessage']:'message sended with success';
		$errormessage = isset($args['errormessage'])?$args['errormessage']:'message not sended';
		
		$debug = isset($args['debug'])?$args['debug']:0;
		$attach = isset($args['attach'])?$args['attach']:null;
		$authenticate = isset($args['authenticate'])?$args['authenticate']:true;
		$isHTML = isset($args['isHTML'])?$args['isHTML']:true;

		$mail = new PHPMailer(false);

		// if($debug != null)
			$mail->SMTPDebug = 0;                              

		$mail->isSMTP();
		$mail->Host = $host;
		$mail->SMTPAuth = $authenticate;
		$mail->Username = $username;
		$mail->Password = $password;
		$mail->SMTPSecure = $secure;
		$mail->Port = $port;


		// from mail -----------------------------------------------
		preg_match_all('/,? ?(.*?)\<(.*?)\>/m', $from, $outFrom);
		$mailsArray = array();
		foreach ($outFrom[1] as $key => $value) {
			$mailsArray[$outFrom[2][$key]] = $value;
		}
		if(count($mailsArray)<1){	
			$fromName = null;
			$fromMail = $from;
		}else{
			$fromName = reset($mailsArray);
			$fromMail = key($mailsArray);
		}				
		$mail->setFrom($fromMail,$fromName);	

	
		
		$mail->ContentType = $contenttype;
		$mail->CharSet = $charset;
		$mail->ReturnPath = $returnpath;
		
		$replytoArray = explode(',', $replyto);
		if( $replyto != null && count($replytoArray)>0){				
			foreach ($replytoArray as $key => $value) {
				$mail->addReplyTo($value);
			}
		}

		$toArray = explode(',', $to);
		if($to != null && count($toArray)>0){
			foreach ($toArray as $key => $value) {				
				$mail->addAddress($value);
			}
		}

		$ccArray = explode(',', $cc);
		if($cc != null && count($ccArray)>0)
		foreach ($ccArray as $key => $value) {
			$mail->addCC($value);
		}

		$bccArray = explode(',', $bcc);
		if($bcc != null && count($bccArray)>0)
		foreach ($bccArray as $key => $value) {
			$mail->addBCC($value);
		}

		$attachArray = $attach;
		if($attachArray != null && count($attachArray)>0)
		foreach ($attachArray as $key => $value) {
			if(is_numeric($key)){	
				$mail->addAttachment($value);				
			}else{			
				$mail->addAttachment($key,$value);				
			}
			
			
		}
		

		$mail->isHTML($isHTML);                                  // Set email format to HTML

		$mail->Subject = $subject;
		$mail->Body    = $msg;
		$mail->AltBody = $msgAlt;

		if(!$mail->send()) {
		    $status = 'error';
		    $msg = ($errormessage == null)?$mail->ErrorInfo:$errormessage;		    
		} else {
			$status = 'success';
			$msg = $successmessage;		    
		}

		

		return json_encode(array('status'=>$status,'msg'=>$msg));



	}



}