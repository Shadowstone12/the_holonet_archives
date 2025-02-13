<?php
    require_once '../../php/functions.php';
    
    require_once DOCUMENT_ROOT . 'php/initialise.php';
    
    display_header($title, $randstr, $logged_in_as, $logged_in, $privilege);

    echo <<<_END
        <script type='text/javascript'>
            $(document).ready(function(){
                $('#show-password').on('click', function() {
                    var passwordField = $('#password-input-field');
                    var passwordFieldType = passwordField.attr('type');
                    if (passwordFieldType == 'password') {
                        passwordField.attr('type', 'text')
                        $(this).text('Hide')
                    } else {
                        passwordField.attr('type', 'password')
                        $(this).text('Show')
                    }
                });
            });
        </script>

    _END;

    $has_signed_up = FALSE;

    $error = $user = $pass = ''; //initialise variables
    if (isset($_SESSION['user'])) destroy_session_completely(); //if user is already logged in, lot them out

    if (isset($_POST['user'])) { //if the user has already filled in the form and submitted it
        $user = sanitise_string($pdo, $_POST['user']); //sanitise the imputs they have supplied
        $pass = sanitise_string($pdo, $_POST['pass']);

        if ($user == '' || $pass == '') $error = 'Not all fields were entered<br  /><br  />'; //if the fields are blank, return an error message
        else {
            $result = $pdo->query("SELECT username, pass FROM user WHERE username='$user'"); //check for users with the username the user gave us

            $is_valid_user = validate_username($user); //check if the username given is valid
            $is_valid_pass = validate_password($pass); //check if the password given is valid

            if ($result->rowCount()) $error = 'That username already exists<br  /><br  />'; //if there exists a record for the username given, the username is taken, and inform the user for them to try again
            elseif ($is_valid_user != '') $error .= $is_valid_user; //if the validate function found a problem with either username or password, these problems are
            elseif ($is_valid_pass != '') $error .= $is_valid_pass; //appended to the error message shown to the user
            else { //otherwise, if the username is taken and it and the password are valid
                $hash = password_hash($pass, PASSWORD_DEFAULT); //hash the password for security
                $result = $pdo->query("INSERT INTO user VALUES ('$user', '$hash')"); //and insert it into the database of users for future reference
                echo "<div class='centre'><h4>Account created</h4>Please Log in.</div></div></body></html>"; //inform the user that their account has been created, and ask them to login, stopping the script
                $has_signed_up = TRUE;
            }
        }
    }
    /*
        What follows is the form sent to the user that gathers password and username and calls this script with the values they give. Also includes basic validation to save server resources.
    */

    if (!$has_signed_up) {
        echo <<<_END
                <div class='centre'><h3>Please note that this is seperate to discord.</h3>Please do not use your discord credentials to log in.</div>
                <br  />
                <form method='post' action='sign_up.php?r=$randstr' onsubmit='return validate_login(this)'>
                <div class='login-form'>
                    <div class='centre'>Please enter your details to sign up</div>
                    <div id='sign-up-fail' class='error'>$error</div>
                    <div class='top-margin'>    
                        <label for='user'><span class='form-label'>Username</span></label>
                        <div>
                            <input type='text' name='user' value='$user' id='username-field' onBlur='check_user(this)'  />
                        </div>
                        <div id='used'>&nbsp;</div>
                    </div>
                    <label for='pass'><span class='form-label'>Password</span></label>
                    <input type='password' name='pass' value='$pass' id='password-input-field' autocomplete='off'  />
                    <label for='sign-up-btn' class='ui-hidden-accessible'>Sign Up</label>
                    <input data-role='button' id='sign-up-btn' data-transition='slide' type='submit' value='Sign Up'  />
                </div>
                </form>
            </div>
        _END;
    }

    include_once DOCUMENT_ROOT . '/php/footer.php'; //footer