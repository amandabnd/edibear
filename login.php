<?php
    session_start();
    require_once("./classes/class.user.php");
    require_once("./classes/class.header.php");
    require_once("./classes/class.widgets.php");
    $userHeader = new HEADER();
    $user = new USER();
    $widgets = new WIDGETS();
    $incorrectUsernamePassword = false;
    if ( isset($_POST['loginSubmit']) && $_POST['loginSubmit']=="Login" ) {
        if(isset($_POST['h-captcha-response']) && !empty($_POST['h-captcha-response'])){
            // $secret = '0x0000000000000000000000000000000000000000';
             $secret = '0x860C2779b903fB3260e7886Cf718a59990fB7460';
            $verifyResponse = file_get_contents('https://hcaptcha.com/siteverify?secret='.$secret.'&response='.$_POST['h-captcha-response'].'&remoteip='.$_SERVER['REMOTE_ADDR']);
            $responseData = json_decode($verifyResponse);
            if($responseData->success){
                $username = strip_tags(isset($_POST['username']) ? $_POST['username'] : "");
                $password = strip_tags(isset($_POST['password']) ? $_POST['password'] : "");
                if ( $user->CountRows("tourists", array("username"=>$username, "status"=>1))==1 ) {
                    $userArr = $user->fetchAll(array("id","password"),array("tourists"),array("username"=>$username, "status"=>1))[0];
                    if ( password_verify($password, substr($userArr["password"],4)) ) {
                        $_SESSION['session_tourism_user'] = $userArr['id'];
                        $_SESSION['timeout']=time();
                        $user->redirect("./account");
                    } else {
                        $incorrectUsernamePassword = true;
                    }
                } else {
                    $incorrectUsernamePassword = true;
                }
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php echo $userHeader->printUserHeader("Login") ?>
    <script src='https://www.hCaptcha.com/1/api.js' async defer></script> 
</head>

<body>
    <?php
        echo $userHeader->printUserTopBar();        //Topbar
        //echo $userHeader->printUserNav();       //Navbar
        echo $widgets->userHeaderImage();       //Header Image
    ?>

    <!-- About Start -->
    <div class="container-fluid py-5">
        <div class="container">
            <i class="fa fa-home pt-1 pr-2 text-primary"></i><a href="./">Home</a><i class="fa fa-angle-right pt-1 px-2 text-primary"></i>Login
            <h4 class="text-warning mt-2">Login</h4>
            <div class="row mt-5 justify-content-center">
                <div class="col-md-6">
                    <div class="card border border-white">
                        <div class="card-body">
                            <form action="" method="post">
                                <input type='text' class='form-control mb-2' name='username' placeholder='Username' required>
                                <input type='password' class='form-control mb-2' name='password' placeholder='Password' required>
                                <div class="pl-4 mb-3">
                                    <input type="checkbox" class="form-check-input float-right" id="showPassword"> Show Password
                                </div>
                                <div class="mb-2"> 
                                   <!-- <div class="h-captcha" data-sitekey="10000000-ffff-ffff-ffff-000000000001" data-callback="correctCaptcha"></div>-->
                                  <div class="h-captcha" data-sitekey="cfe150fa-234b-4633-9582-b974082cbc2f" data-callback="correctCaptcha"></div> 
                                </div>
                                <input type="submit" class="btn btn-primary" value="Login" name="loginSubmit">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- About End -->

    <!-- Footer Start -->
    <?php 
        echo $userHeader->printUserFooter(); 
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
    <!-- Footer End -->
    <script>
        var x = $("input[name='password']");
        $("#showPassword").click(function (){
            if ( x.attr("type") == "password" ) {
                x.attr("type", "text");
            } else {
                x.attr("type", "password");
            }
        });
        var buttonName = ':input[name="loginSubmit"]';
        $(buttonName).prop('disabled', true);
        $(buttonName).css('backgroundColor','grey');
        function correctCaptcha() {
            $("form").each(function() {
                $(this).find(buttonName).prop('disabled', false);
                $(buttonName).css('backgroundColor','var(--primary)');
            });
        }
    </script>
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
</body>

</html>