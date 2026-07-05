<?php



include ('database_connection.php');
if (isset($_POST['formsubmitted'])) {
    $error = array();//Declare An Array to store any error message  
    if (empty($_POST['name'])) {//if no name has been supplied 
        $error[] = 'Please Enter a name ';//add to array "error"
    } else {
        $name = $_POST['name'];//else assign it a variable
    }

    if (empty($_POST['e-mail'])) {
        $error[] = 'Please Enter your Email ';
    } else {


        if (preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $_POST['e-mail'])) {
           //regular expression for email validation
            $Email = $_POST['e-mail'];
        } else {
             $error[] = 'Your EMail Address is invalid  ';
        }


    }


    if (empty($_POST['Password'])) {
        $error[] = 'Please Enter Your phone number ';
    } else {
        $Password = $_POST['Password'];
    }
	
	if (empty($_POST['address'])) {
        $error[] = 'Please Enter Your address ';
    } else {
        $address = $_POST['address'];
    }

    if (empty($error)) //send to Database if there's no error '

    { // If everything's OK...

        // Make sure the email address is available:
        $query_verify_email = "SELECT * FROM members  WHERE Email ='$Email'";
        $result_verify_email = mysqli_query($dbc, $query_verify_email);
        if (!$result_verify_email) {//if the Query Failed ,similar to if($result_verify_email==false)
            echo ' Database Error Occured ';
        }

        if (mysqli_num_rows($result_verify_email) == 0) { // IF no previous user is using this email .


            // Create a unique  activation code:
            $activation = md5(uniqid(rand(), true));


            $query_insert_user = "INSERT INTO `members` ( `Username`, `Email`, `Password`, `Activation`) VALUES ( '$name', '$Email', '$Password', '$address')";


            $result_insert_user = mysqli_query($dbc, $query_insert_user);
            if (!$result_insert_user) {
                echo 'Query Failed ';
            }

            if (mysqli_affected_rows($dbc) == 1) { //If the Insert Query was successfull.


                // Send the email:
				
$subject =	 'PHP Mail Attachment Test';
$bound_text =	"jimmyP123";
$bound =	"--".$bound_text."\r\n";
$bound_last =	"--".$bound_text."--\r\n";
 	 
$headers =	"From: susan@susancarrier.com\r\n";
$headers .=	"MIME-Version: 1.0\r\n"
 	."Content-Type: multipart/mixed; boundary=\"$bound_text\"";
 	 
$message =	"If you can see this MIME than your client doesn't accept MIME types!\r\n"
 	.$bound;
 	 
$message =	"Content-Type: text/html; charset=\"iso-8859-1\"\r\n"
 	."Content-Transfer-Encoding: 7bit\r\n\r\n"
 	."hey my <b>good</b> friend here is a picture of regal beagle\r\n"
 	.$bound;
 	 
$file =	 file_get_contents("hhttp://susancarrier.com/images/header.png");
 	 
$message .=	"Content-Type: image/jpg; name=\"header.jpg\"\r\n"
 	."Content-Transfer-Encoding: base64\r\n"
 	."Content-disposition: attachment; file=\"header.jpg\"\r\n"
 	."\r\n"
 	.chunk_split(base64_encode($file))
 	.$bound_last;
                
                
				mail($Email, 'Registration Confirmation', $message, $headers);
                // Flush the buffered output.


                // Finish the page:
                echo '<div class="success" style="color:#fff;">Thank you for
registering! A confirmation email
has been sent to '.$Email.' </div>';


            } else { // If it did not run OK.
                echo '<div class="errormsgbox" style="color:#fff;">You could not be registered due to a system
error. We apologize for any
inconvenience.</div>';
            }

        } else { // The email address is not available.
            echo '<div class="errormsgbox" style="color:#fff;">That email
address has already been registered.
</div>';
        }

    } else {//If the "error" array contains error msg , display them
        
        

echo '<div class="errormsgbox"> <ol>';
        foreach ($error as $key => $values) {
            
            echo '	<li>'.$values.'</li>';


       
        }
        echo '</ol></div>';

    }
  
    mysqli_close($dbc);//Close the DB Connection

} // End of the main Submit conditional.



?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="Angle Reading Brisbane, Psychic Tuition Brisbane, Psychic Book Brisbane, Reiki courses in  Brisbane, Clairvoyant Books brisbane, Demon Protection brisbane, Psychic in Brisbane, Clairvoyant in Brisbane" />
<meta name="description" content="we are psychic, clairboyant in brisbane. we provide psychic tuition, psychic book, reiki courses, clairvoyant books, demon protection in brisbane. Here we do live psychic readings only at $1.97." />

<title>Clairvoyant in Brisbane | Susan Carrier | Psychic Book | Angle Reading Brisbane</title>
<link href="stylesheet.css" rel="stylesheet" type="text/css" />
<link href='http://fonts.googleapis.com/css?family=Signika+Negative:400,600' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Cabin:400,500' rel='stylesheet' type='text/css'>


<link rel="stylesheet" href="themes/default/default.css" type="text/css" media="screen" />

<link rel="stylesheet" href="css/validationEngine.jquery.css" type="text/css"/>
 <script src="js/jquery-1.5.1.min.js" type="text/javascript"></script>
 <script src="js/languages/jquery.validationEngine-en.js" type="text/javascript" charset="utf-8"></script>
 <script src="js/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script>
        <script>
            jQuery(document).ready(function(){
                // binds form submission and fields to the validation engine
                jQuery("#formID").validationEngine();
            });
            
            /**
             *
             * @param {jqObject} the field where the validation applies
             * @param {Array[String]} validation rules for this field
             * @param {int} rule index
             * @param {Map} form options
             * @return an error string if validation failed
             */
            function checkHELLO(field, rules, i, options){
                if (field.val() != "HELLO") {
                    // this allows to use i18 for the error msgs
                    return options.allrules.validate2fields.alertText;
                }
            }
        </script>




</head>

<body>

<!-- Google Tag Manager -->
<noscript><iframe src="http://www.googletagmanager.com/ns.html?id=GTM-W3KB"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'../www.googletagmanager.com/gtm5445.html?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-W3KB');</script>
<!-- End Google Tag Manager -->

<div class="wrapper">

<!--TOP PART START-->

<div class="top_part">
	<div class="logo"> <a href="index-2.html"><img src="images/logo.png" width="313" height="91" border="0" /></a></div>
    
    <div class="call_us">Call Us: <br /><strong>073843 6419</strong>
</div>

</div>

<!--TOP PART END-->
<div class="clear"></div>

<!--HEADER PART START-->
<div>                 
    <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0" width="989" height="390">
       <param name="movie" value="flash/FLASH1.swf" />
       <param name="quality" value="high" />
       <embed src="flash/FLASH1.swf" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="989" height="390"></embed>
    </object>    
</div>
<!--HEADER PART END-->
<div class="clear"></div>

<!--MIDDLE PART START-->
<div class="middle_part">
<h1>LIVE PSYCHIC READINGS – Only $1.97 Per Minute.</h1>

Minimum 15 Minute Reading.<br />

In Brisbane? Face to Face Readings Available – Bookings Essential.


</div>
<!--MIDDLE PART END-->
<div class="clear"></div>
<!--CONTAIN PART START-->
<div class="contain">

<div class="contain_left"> <img src="images/middle_contain.png" width="207" height="207" border="0" />  <img src="images/brackets.png"  border="0" /></div>
<div class="contain_right">

	<form action="register.php" method="post" class="registration_form" enctype="multipart/form-data">
  <fieldset>
    <legend>Registration Form </legend>

    <p>Create A new Account </p>
    <table width="600" border="0">
  <tr>
    <td><div class="elements"><label for="name" style="color:#fff;">Name :</label></td>
    <td><input type="text" id="name" name="name" size="25" /></div></td>
  </tr>
  <tr>
    <td><div class="elements">
      <label for="e-mail" style="color:#fff;">E-mail :</label></td>
    <td><input type="text" id="e-mail" name="e-mail" size="25" />
    </div></td>
  </tr>
  <tr>
    <td> <div class="elements">
      <label for="Password" style="color:#fff;">Phone:</label></td>
    <td><input type="text" id="Password" name="Password" size="25" />
    </div></td>
  </tr>
  <tr>
    <td> <div class="elements">
      <label for="Password" style="color:#fff;">Address:</label></td>
    <td><textarea id="address" name="address"></textarea>
    </div></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><div class="submit">
     <input type="hidden" name="formsubmitted" value="TRUE" />
      <input type="submit" value="Register" />
    </div>
  </fieldset></td>
  </tr>
</table>
</form>
 </div>

</div>
<!--CONTAIN PART END-->




</div>

<div class="clear"></div>
<!--MIDDLE PART START-->
<div class="footer_banner">

	<div class="footer_wrapper">
            	
            	<div class="ftr_left"><h1>Contant Susan</h1>
                	<form id="formID" class="formular" method="post" action="http://www.susancarrier.com/Mailer.php">
               
               <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="35">First Name:</td>
    <td><input type="text" class="validate[required] text-input" name="fname" id="fname"  style="background:#07071e;width:208px;border:1px solid #af198f;height:30px;padding:0px 0px 0px 0px;color:#FFF;" /></td>
    <td>Email:</td>
    <td><input type="text" class="validate[required,custom[email]] text-input"  name="email" id="email"  style="background:#07071e;width:208px;border:1px solid #af198f;height:30px;padding:0px 0px 0px 0px;color:#FFF;" /></td>
  </tr>
  <tr>
    <td height="72">Gander:</td>
    <td>
    	<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><input name="Gender" type="radio" value="Male" checked> Male</td>
    <td><input name="Gender" type="radio" value="Female" unchecked> Female</td>
  </tr>
  
 
</table>

    </td>
    <td>Phone:</td>
    <td><input class="validate[required,custom[phone]] text-input" type="text" name="phone" id="phone"   style="background:#07071e;width:208px;border:1px solid #af198f;height:30px;padding:0px 0px 0px 0px;color:#FFF;" /></td>
  </tr>
  <tr>
    <td>Date of Birth:</td>
    <td><input type="text" class="validate[required] text-input" name="dob" id="dob"  style="background:#07071e;width:208px;border:1px solid #af198f;height:30px;padding:0px 0px 0px 0px;color:#FFF;" /></td>
    <td>&nbsp;</td>
    <td align="left" valign="middle"><input type="submit" value="" class="send_btn"></td>
  </tr>
</table>        
               </form> 
            </div>  
                
          <div  class="ftr_right" align="center">
          <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="TYM3WB4WNX5JY">
<table>
<tr><td><input type="hidden" name="on0" value="Live Psychic Phone Readings">Live Psychic Phone Readings</td></tr><tr><td align="center"><select name="os0">
	<option value="15 Minutes">15 Minutes $29.55 AUD</option>
	<option value="30 Minutes">30 Minutes $59.10 AUD</option>
	<option value="45 Minutes">45 Minutes $88.65 AUD</option>
</select> </td></tr>
<tr><td height="6"></td></tr>
</table>
<input type="hidden" name="currency_code" value="AUD">
<input type="image" src="images/buy_now.png" border="0" name="submit" alt="PayPal — The safer, easier way to pay online.">
<img alt="" border="0" src="../www.paypalobjects.com/en_AU/i/scr/pixel.gif" width="1" height="1">
</form>

          

          </div> 
      <!--<div class="testi"></div>--> 
    
    
    
  </div>
    <div style="padding:0px 0px 0px 0px;margin:0px 0px 0px 0px;"> <img src="images/footer_brd.png" /></div>
    <div class="txt">Copyright © 2013 Susan Carrier . All Rights Reserved.</div>
</div>
<!--MIDDLE PART END-->
</body>
</html>
