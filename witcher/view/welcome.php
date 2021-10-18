<!DOCTYPE html>
<html lang="en">

<head>
    <title>Cwil it ,then let others C</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="assets/img/welcome/icons/favicon.ico" />
    <link rel="stylesheet" type="text/css" href="assets/vendor/animate/animate.css">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="assets/vendor/css-hamburgers/hamburgers.min.css">
    <link rel="stylesheet" type="text/css" href="assets/vendor/select2/select2.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/util.css">
    <link rel="stylesheet" type="text/css" href="assets/css/welcome.css">
    <style>
        .uppon-links{padding: 1px 10px;font-size: 20px;border-radius: 3px;border: 2px solid #ddd;position: relative;top:10px;left:13px;  }
        .alert {position:fixed;display:block;width:100%;direction: rtl!important;font-size:20px;padding: 10px!important;background-color: #f44336;color: white!important;}.closebtn {margin-left: 15px!important;color: white!important;font-weight: bold!important;float: right!important;font-size: 22px!important;line-height: 20px!important;cursor: pointer!important;transition: 0.3s;}.closebtn:hover {color: black;}.alert.success {background-color: #4CAF50;}
        .alert.info {background-color: #2196F3;}
        .alert.warning {background-color: #ff9800;text-align: right;}
    </style>
</head>
<body>

<a href="" class="uppon-links">About</a>
<a href="" class="uppon-links">FAQ</a>
<a href="" class="uppon-links">Privacy & Policy</a>
<?=\Model\message::msg_box_session_show()?>
    <div class="limiter">
        <div class="container-login100">
            <div class="wrap-login100">
                <div class="login100-pic js-tilt" data-tilt>
                    <form method="post" id="signup-form">
                    <a href="#" style="text-decoration:none;">
                    <span class="login100-form-title">
						<h1>SignUp</h1>
					</span>
                    </a>

                    <div class="wrap-input100 validate-input" data-validate="Valid email is required: ex@abc.xyz">
                        <input class="input100" type="text" name="email" placeholder="Email" required autocomplete="off">
                        <span class="focus-input100"></span>
                        <span class="symbol-input100">
							<i class="fa fa-envelope" aria-hidden="true"></i>
						</span>
                    </div>

                        <div class="wrap-input100 validate-input" data-validate="Valid email is required: ex@abc.xyz">
                            <input class="input100" type="text" name="username" placeholder="Username" required autocomplete="off">
                            <span class="focus-input100"></span>
                            <span class="symbol-input100">
							<i class="fa fa-user" aria-hidden="true"></i>
						</span>
                        </div>

                    <div class="wrap-input100 validate-input" data-validate="Password is required">
                        <input class="input100" type="password" name="password" placeholder="Password" required>
                        <span class="focus-input100"></span>
                        <span class="symbol-input100">
							<i class="fa fa-lock" aria-hidden="true"></i>
						</span>
                    </div>

                    <div class="wrap-input100 validate-input" data-validate="Password is required">
                        <input class="input100" type="password" name="ret_password" placeholder="re-type Password" required>
                        <span class="focus-input100"></span>
                        <span class="symbol-input100">
							<i class="fa fa-lock" aria-hidden="true"></i>
						</span>
                    </div>
                        <input type="hidden" name="action" value="signup">
                        <input type="hidden" name="token" value="<?=\Core\tokenCSRF::get_token()?>">
                    <div class="container-login100-form-btn">
                        <input class="login100-form-btn" type="submit" value="Sign Up" onclick="clickAndDisable(this)">
                    </div>
                        <div class="text-center hidden-lg hidden-md">
                            Do you have account ?
                            <a class="txt2" href="#" onclick="window.location.reload()">
                                Login now.
                                <i class="fa fa-long-arrow-right m-l-5" aria-hidden="true"></i>
                            </a>
                        </div>
                    </form>
                </div>

                <form class="" method="post" id="login-form">
                   <a href="#" style="text-decoration:none;">
                    <span class="login100-form-title">
						<h1>Login</h1>
					</span>
                    </a>

                    <div class="wrap-input100 validate-input" data-validate="Valid email is required: ex@abc.xyz">
                        <input class="input100" type="text" name="login" placeholder="Email or Username" id = "username" required autocomplete="off">
                        <span class="focus-input100"></span>
                        <span class="symbol-input100">
							<i class="fa fa-envelope" aria-hidden="true"></i>
						</span>
                    </div>

                    <div class="wrap-input100 validate-input" data-validate="Password is required">
                        <input class="input100" type="password" name="password" placeholder="Password" required>
                        <span class="focus-input100"></span>
                        <span class="symbol-input100">
							<i class="fa fa-lock" aria-hidden="true"></i>
						</span>
                    </div>
                    <input type="hidden" name="action" value="login">
                    <input type="hidden" name="token" value="<?=\Core\tokenCSRF::get_token()?>">
                    <div class="container-login100-form-btn">
                        <input class="login100-form-btn" type="submit" value="Sign in" onclick="clickAndDisable(this)">

                    </div>

                    <div class="text-center p-t-12">
                        <span class="txt1">
							Forgot
						</span>
                        <a class="txt2" href="#">
							Username / Password?
						</a>
                    </div>

                    <div class="text-center p-t-136 hidden-lg hidden-md">
                       Didn't signup yet?
                        <a class="txt2" href="#" onclick="showsignup();">
							Do it now
							<i class="fa fa-long-arrow-right m-l-5" aria-hidden="true"></i>
						</a>
                    </div>
                </form>

            </div>

        </div>
    </div>
    <!--
    CWILIO ADMIN : Theme by Colorlib > https://colorlib.com 
    -->
    <script src="assets/vendor/jquery/jquery-3.2.1.min.js"></script>
    <script src="assets/vendor/bootstrap/js/popper.js"></script>
    <script src="assets/vendor/select2/select2.min.js"></script>
    <script src="assets/vendor/tilt/tilt.jquery.min.js"></script>
	<script src="assets/vendor/bootstrap/js/popper.js"></script>
	<script src="assets/vendor/bootstrap/js/bootstrap.min.js"></script>
	<script src="assets/vendor/select2/select2.min.js"></script>
	<script src="assets/vendor/tilt/tilt.jquery.min.js"></script>
    <script>
        $('.js-tilt').tilt({
            scale: 1.1
        })
    </script>
    <script>
        function showsignup(){
            var x = document.getElementById("signup-form").innerHTML;
            var a = document.getElementById("login-form");
            a.innerHTML = x;
        }
        var close = document.getElementsByClassName("closebtn");
        var i;

        for (i = 0; i < close.length; i++) {
            close[i].onclick = function(){
                var div = this.parentElement;
                div.style.opacity = "0";
                setTimeout(function(){ div.style.display = "none"; }, 600);
            }
        }
    </script>
    <script src="assets/js/welcome.js"></script>
</body>
</html>
