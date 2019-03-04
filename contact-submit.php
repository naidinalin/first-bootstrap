<?php

//Address to deliver contact messages
$admin_email="contact@nlbat.fr";

$errors = array();

if($_SERVER['REQUEST_METHOD'] == 'POST') {

    //Clean posted inputs
    $name=cleanInput($_POST["name"]);
    $email=cleanInput($_POST["email"]);
    $phone=cleanInput($_POST["phone"]);
    $message=cleanInput($_POST["comment"]);


	if(empty($name)){
		$errors[] = "Name field cannot be left blank.";
	}
	if(empty($email)){
		$errors[] ="Email field cannot be left blank.";
	} else if(!preg_match('/^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,8}$/', $email)) {
		$errors[] = "Email Address you entered is not valid.";
	}
	
	if(empty($message)){
		$errors[] = "Comment field cannot be left blank.";
	}

	if (count($errors) == 0) {
	
		//Send email via builtin SMTP
		$headers = "From: ".$name.'<'.$email.'>'."\r\n".
		"Reply-To: ".$email."\r\n" .
		"Return-path: ".$email."\r\n" .
		"X-Mailer: PHP/" . phpversion();
		
		function clean_string($string) {
		  $bad = array("content-type","bcc:","to:","cc:","href");
		  return str_replace($bad,"",$string);
		}

		$email_message = "Form details below.\r\n\r\n";
		$email_message .= "Name: ".clean_string($name)."\r\n";
		$email_message .= "Email: ".clean_string($email)."\r\n";
		$email_message .= "Phone: ".clean_string($phone)."\r\n";
		$email_message .= "Message: ".clean_string($message)."\r\n";
		$email_message .= "IP: ".clean_string($_SERVER["REMOTE_ADDR"])."\r\n";
		$email_message .= "User-Agent: ".clean_string($_SERVER["HTTP_USER_AGENT"])."\r\n";
		
		if (mail($admin_email,"New contact form message from " . $email, $email_message, $headers, "-f ". $email)) {
			
			$name="";
			$email="";
			$message="";
		
		}
		else {
			$errors[] = "There was an error sending your message. Please try again later.";
		}

	}

} else {
    $errors[] = "Request verb unsupported.";
}


$response = array();
if (count($errors) == 0) {
    $response = array(
        'status' => true,
        'message' => 'Your message has been sent. We will review it and get back to you shortly.'
    );
} else {
    $response = array(
        'status' => false,
        'message' => implode(' ', $errors)
    );
}

//Return response in JSON format
header('Content-type: application/json');
echo json_encode($response);






//Clean user input
function cleanInput($data) {
	if (get_magic_quotes_gpc()) {
	    $data = trim($data);
	    $data = stripslashes($data);
		$data = strip_tags($data);
		$data = mysql_escape_string_replacement($data);
	} else {
	    $data = trim($data);
		$data = strip_tags($data);
		$data = mysql_escape_string_replacement($data);
	}
	return $data;
} 

//Escape SQL injection inputs
function mysql_escape_string_replacement($unescaped) {
	$replacements = array(
	   "\x00"=>'\x00',
	   "\n"=>'\n',
	   "\r"=>'\r',
	   "\\"=>'\\\\',
	   "'"=>"\'",
	   '"'=>'\"',
	   "\x1a"=>'\x1a'
	);
	return strtr($unescaped,$replacements);
  }
?>