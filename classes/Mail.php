<?php
require_once('PHPMailer/PHPMailerAutoload.php');
class Mail {
	public static function sendMail($subject,$body,$address) {
		$email = new PHPMailer();
		$email->IsSMTP();
		$email->SMTPAuth = true;
		$email->SMTPSecure = 'ssl';
		$email->Host = 'mail.buble.co.za'; 
		$email->Port = '465';
		$email->IsHTML(true);
		$email->Username = 'no-reply@buble.co.za';
		$email->Password = 'Epsilion28.';
		$email->From='no-reply@buble.co.za';
		$email->FromName='Buble Inc';
		$email->Subject = $subject;
		$email->Body = $body;
		$email->AddAddress($address);


		$email->Send();
		if(!$email->Send())
        {
            $error ="Please try Later, Error Occured while Processing...";
            
            return $error; 
        }
        else 
        {
            $error = "Thank You !! Your email is sent.";  
            
            return $error;
        }
	}

}

?>