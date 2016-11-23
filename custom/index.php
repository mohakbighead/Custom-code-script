<?php include("includes/common.php"); 
	  $loginObj = new LoginClass(); 
?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width">
<meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
<title>Custom - Client Login</title>
<link href="<?php echo SITEURL; ?>css/style.css" rel="stylesheet" type="text/css">
<link href='https://fonts.googleapis.com/css?family=Muli' rel='stylesheet' type='text/css'>
<script src="<?php echo SITEURL; ?>js/jquery.min.js"></script>
</head>

<body>
<main class="fixed-bg1">
  <div class="wrapper">
    <div class="login-p-logo"> <!--<a href="<?php echo SITEURL; ?>"><img src="<?php echo SITEURL; ?>image/logo.png" alt="logo" /></a>--> </div>
    <section class="login">
      <div class="login-form">
        <div class="whole-form">
          <div class="form-title">
            <h2>log in</h2>
            <div id="contact_results"></div>
          </div>
          <form id="loginform" action="">
            <ul>
              <li>
                <input name="username" id="username" type="text" value="" placeholder="Username" required="true" />
              </li>
              <li>
                <input name="password" id="password" type="password" value="" placeholder="Password" required="true" />
              </li>
              <li>
                <input type="button" id="submit_btn" value="log in" />
              </li>
              <!--<li><a href="#">forgot password? </a></li>-->
            </ul>
          </form>
        </div>
      </div>
    </section>
  </div>
</main>
<script language="javascript">
$(document).ready(function(){
	$("#submit_btn").click(function() {
		var proceed = true;
		//simple validation at client's end
        //loop through each field and we simply change border color to red for invalid fields       
        $("#loginform input[required=true]").each(function(){
            $(this).css('border-color',''); 
            if(!$.trim($(this).val())){ //if this field is empty 
                $(this).css('border-color','red'); //change border color to red   
                proceed = false; //set do not proceed flag
            }            
        });
		if(proceed) //everything looks good! proceed...
        {
            //get input field values data to be sent to server
            post_data = {
                'username'   : $('input[name=username]').val(), 
                'password'   : $('input[name=password]').val(), 
            };
            
            //Ajax post data to server
            $.post('<?php echo SITEURL;?>logchk.php', post_data, function(response){  
                if(response.type == 'error'){ //load json data from server and output message     
                    output = '<div class="error">'+response.text+'</div>';
                }else{					
                    output = '<div class="success">'+response.text+'</div>';                    
					window.open(response.urlpass,'_self');
                }
                $("#contact_results").hide().html(output).slideDown();
            }, 'json');
        }
	});
	//reset previously set border colors and hide all message on .keyup()
    $("#loginform input[required=true]").keyup(function() { 
        $(this).css('border-color',''); 
        $("#contact_results").slideUp();
    });
});
</script>
</body>
</html>