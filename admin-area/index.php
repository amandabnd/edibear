<?php
session_start();
require_once("../classes/class.user.php");
$login = new USER();

if($login->is_loggedin()){
    $login->redirect('dashboard');
}

$incorrectUsernamePassword = false;


//if(isset($_POST['loginSubmit'])){
   // if(isset($_POST['h-captcha-response']) && !empty($_POST['h-captcha-response'])){
        // $secret = 'ES_eaaedab7ac1040b593545138aef2e3bc';
        // $verifyResponse = file_get_contents('https://api.hcaptcha.com/siteverify?secret='.$secret.'&response='.$_POST['h-captcha-response'].'&remoteip='.$_SERVER['REMOTE_ADDR']);
        // echo "verify Responce: " . $verifyResponse;
        // $responseData = json_decode($verifyResponse);    
        
        // $SECRET_KEY = "ES_eaaedab7ac1040b593545138aef2e3bc";
        // $VERIFY_URL = "https://api.hcaptcha.com/siteverify";
        
        // // Retrieve token from POST data with key 'h-captcha-response'
        // $token = $_POST['h-captcha-response'];
        
        // // Build payload with secret key and token
        // $data = array(
        // 'secret' => $SECRET_KEY,
        // 'response' => $token
        // );
        
        // // Make POST request with data payload to hCaptcha API endpoint
        // $options = array(
        // 'http' => array(
        //     'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
        //     'method' => 'POST',
        //     'content' => http_build_query($data)
        // )
        // );
        // $context = stream_context_create($options);
        // $response = file_get_contents($VERIFY_URL, false, $context);
        
        // //echo $response;
         
        // // Parse JSON from response. Check for success or error codes
        // $response_json = json_decode($response, true);
        // $success = $response_json['success'];
        

        //echo "Captcha Responce: " . $success;
        //if($success){
        
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_submitted']) && $_POST['form_submitted'] === 'true') {
    
          $umail = strip_tags(isset($_POST['inputEmail']) ? $_POST['inputEmail'] : "");
          $upass = strip_tags(isset($_POST['inputPassword']) ? $_POST['inputPassword'] : "");
          
          // Verify reCAPTCHA token
          $recaptcha_token = isset($_POST['g-recaptcha-response']) ? $_POST['g-recaptcha-response'] : '';
          $secret_key = '6LccC4wqAAAAAEpDGD7q1dVvHZzJ8rxdmVYFLz7B'; // Replace with your actual secret key
          $verify_url = 'https://www.google.com/recaptcha/api/siteverify';
          
          $response = file_get_contents($verify_url, false, stream_context_create([
              'http' => [
                  'method' => 'POST',
                  'header' => 'Content-type: application/x-www-form-urlencoded',
                  'content' => http_build_query(['secret' => $secret_key, 'response' => $recaptcha_token])
              ]
          ]));
          
          $result = json_decode($response);
          
          
              if($login->doLogin($umail,$upass)){
                  if (isset($_GET['page'])) {
                    $redirect = $_GET['page'];
                  } else {
                    $redirect = "dashboard";
                  }
                  $login->redirect($redirect);
              }else{
                $incorrectUsernamePassword = true;
              }
          
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="./assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../img/Favicon.png">
  <title>Admin Login - Edibear</title>
  <meta name="Title" content="Traveylo | Sri Lanka Tour Packages | Travel Agent in Sri Lanka" />
  <meta property='og:title' content='Traveylo | Sri Lanka Tour Packages | Travel Agent in Sri Lanka'/>
  <meta name='description' content='“Ayubowan!” Traveylo.com provides tour packages covering the most beautiful places 
in Sri Lanka, and you can travel in luxury with your own vehicle around 
our beautiful country. So reserve your tour with us.' />
  <meta name='keywords' content='Travel Agents In Sri Lanka / Sri Lanka Tourism / Sri Lanka Tourist Destinations / Places To Visit In Sri Lanka With Family / How To Travel In Sri Lanka / Sri Lanka Tours & Travels / Tour Packages In Sri Lanka / Sri Lanka Itinerary / Sri Lanka Travel Guide /Sri Lanka HotelsSri Lanka Tour Operators /Sri Lanka Budgets Tours /Small Group Tour In Sri Lanka / Sri Lanka Holiday Packages /Sri Lanka Tour Packages For Couple / Sri Lanka Tour Packages For Family /Sri Lanka Tour Packages Price / What To Do In Sri Lanka /Popular Destinations In Sri Lanka' />
		
    <!-- for Facebook -->
    <meta property="og:title" content="Traveylo | Sri Lanka Tour Packages | Travel Agent in Sri Lanka"/>
    <meta property="og:site_name" content="Traveylo Website"/>
    <meta property="og:image" content="https://traveylo.com/img/Logo-Footer.png" />
    <meta property="og:url" content="https://traveylo.com" />
    <meta property="og:description" content='"Ayubowan!” Traveylo.com provides tour packages covering the most beautiful places 
in Sri Lanka, and you can travel in luxury with your own vehicle around 
our beautiful country. So reserve your tour with us.'>
  <!--     Fonts and icons     -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
  <!-- Nucleo Icons -->
  <link href="./assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="./assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <link href="./assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- CSS Files -->
  <link id="pagestyle" href="./assets/css/argon-dashboard.css?v=2.0.4" rel="stylesheet" />
  <!--hCaptcha
  <script src='https://www.hCaptcha.com/1/api.js' async defer></script> -->
   <!--reCaptcha-->
  
  <script src="./assets/js/plugins/jquery.min.js"></script>
</head>

<body class="">
  <main class="main-content  mt-0">
    <section>
      <div class="page-header min-vh-100">
        <div class="container">
          <div class="row">
            <div class="col-xl-4 col-lg-5 col-md-7 d-flex flex-column mx-lg-0 mx-auto">
              <div class="card card-plain">
                <div class="card-header pb-0 text-start">
                  <div class="row justify-content-center py-5">
                    <img src="../img/Logo.png" style="max-width: 250px;" alt="Logo">
                  </div>
                  <h4 class="font-weight-bolder">Sign In</h4>
                  <p class="mb-0">Enter your email and password to sign in</p>
                </div>
                <div class="card-body">
                   <form id="loginForm" action="" method="POST">
                    <div class="mb-3">
                      <input type="email" class="form-control form-control-lg" name="inputEmail" placeholder="Email" aria-label="Email" required>
                    </div>
                    <div class="mb-3">
                      <input type="password" class="form-control form-control-lg" name="inputPassword" placeholder="Password" aria-label="Password" required>
                    </div>
                    <div class="mb-3">
                      <!-- <div class="h-captcha" data-sitekey="10000000-ffff-ffff-ffff-000000000001" data-callback="correctCaptcha"></div>
                      <div class="h-captcha" data-sitekey="27f5a1ea-c309-4a54-99e0-f8efd7c1c05a" data-callback="correctCaptcha"></div> -->
                    </div>
                    <div class="text-center">
                      <!--<input type="submit" id="loginSubmit" name="loginSubmit" value="Sign in" class="btn btn-lg btn-primary btn-lg w-100 mt-4 mb-0">-->
                      <input type="hidden" name="form_submitted" value="true">
                     <button  id="loginSubmit" name="loginSubmit"
                        class="g-recaptcha btn btn-lg btn-primary btn-lg w-100 mt-4 mb-0" 
                        type="submit"
                        >Submit</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
            <div class="col-6 d-lg-flex d-none h-100 my-auto pe-0 position-absolute top-0 end-0 text-center justify-content-center flex-column">
              <div class="position-relative bg-gradient-primary h-100 m-3 px-7 border-radius-lg d-flex flex-column justify-content-center overflow-hidden" style="background-image: url('./assets/img/polar1.png');
          background-size: cover;">
                <!-- <span class="mask bg-gradient-primary opacity-6"></span> -->
                
                <p class="text-white position-relative"></p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>
  <?php
    if ($incorrectUsernamePassword) {
      echo "
        <script>
            $(function(){
                $('#IncorrectUsernamePasswordModal').modal('show');
            });
        </script>
      ";
    }
  ?>
  <!--   Core JS Files   -->
  <script src="./assets/js/core/popper.min.js"></script>
  <script src="./assets/js/core/bootstrap.min.js"></script>
  <script src="./assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="./assets/js/plugins/smooth-scrollbar.min.js"></script>
   <script>
   
 </script>
  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
    
  </script>
  <!-- Github buttons -->
  <script async defer src="./assets/js/plugins/buttons.js"></script>

    <!--IncorrectUsernamePassword Modal-->
    <div class="modal fade" id="IncorrectUsernamePasswordModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true" style="margin-top:200px">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Incorrect Username or Password.<br>Please Try again</h5>
                </div>
                <div class="modal-body">
                  <button class="btn btn-sm btn-primary" type="button" onclick="location.reload()">Okay</button>
                </div>
            </div>
        </div>
    </div>
    <!--Modal End-->

    <script>
document.getElementById("loginForm").addEventListener("submit", function(e) {

    console.log("FORM SUBMITTED");

    const email = document.querySelector("input[name='inputEmail']").value;
    const password = document.querySelector("input[name='inputPassword']").value;

    console.log("Email entered:", email);
    console.log("Password length:", password.length);

});
</script>

</body>

</html>