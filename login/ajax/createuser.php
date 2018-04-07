<?php
require '../autoload.php';
//funciones de seguridad
function cleanthis($data){
    $iclean = filter_var($data, FILTER_SANITIZE_STRING);
    $iclean = thisword($iclean);
    $iclean = htmlentities($iclean, ENT_QUOTES);
    return $iclean;
}
function thisword($word){
    $badword = array("drop", "insert", "update", "delete", "alter", "index", "truncate", "'", '"');
    $badreplace = array("***", "***", "****", "***", "****", "***", "*****", "*", "*");
    $clean = str_replace($badword,$badreplace,$word);
    return $clean;
}

try {
    //Pull username, generate new ID and hash password
    $newid = uniqid(rand(), false);
    $newuser = str_replace(' ', '', $_POST['newuser']);

    if ($newuser == '') {
        throw new Exception('<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Must enter a username</div><div id="returnVal" style="display:none;">false</div>');
    }

    $newemail = $_POST['email'];
    $pw1 = $_POST['password1'];
    $code = cleanthis($_POST['password2']);
    $userarr = Array(Array('id'=>$newid, 'username'=>$newuser, 'email'=>$newemail, 'pw'=>$pw1));

    $config = new AppConfig;

    $conf = $config->pullMultiSettings(array("password_policy_enforce", "password_min_length", "signup_thanks", "base_url" ));

    $pwresp = PasswordPolicy::validate($pw1, $pw1, (bool) $conf["password_policy_enforce"], (int) $conf["password_min_length"]);

    //comprobamos codigo de registro
    $db2 = new DbConn;
    $stmt = $db2->conn->prepare("SELECT * FROM `regcodes` WHERE `code` = '$code'");
    $stmt->execute();
    $row = $stmt->fetch();

    $uses = $row['uses'];

    if (!filter_var($newemail, FILTER_VALIDATE_EMAIL) == true) {

        echo '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Must provide a valid email address</div><div id="returnVal" style="display:none;">false</div>';
    } elseif (!$row['uses'] || $row['uses'] <= 0 ){

        echo '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>WRONG INVITE CODE OR EXPIRED </div><div id="returnVal" style="display:none;">false</div>';
    } else {
        //Validation passed
        if (isset($_POST['newuser']) && !empty(str_replace(' ', '', $_POST['newuser'])) && $pwresp['status'] == 1) {

            $a = new NewUser;

            $response = $a->createUser($userarr);

            //Success
            if ($response == 1) {

                $db3 = new DbConn;
                $stmt = $db3->conn->prepare("UPDATE `regcodes` SET `uses` = '".--$uses."' WHERE `regcodes`.`code` = '$code';");
                $stmt->execute();

                echo '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. $conf['signup_thanks'] .'</div><div id="returnVal" style="display:none;">true</div><form action="'.$conf['base_url'].'/login/index.php"><button class="btn btn-success">Login</button></form><div id="returnVal" style="display:none;">true</div>';

                try { //Send verification email
                    $m = new MailSender;

                    $m->sendMail($userarr, 'Verify');

                } catch (Exception $e) {

                    echo $e->getMessage();
                }

            } else {
                //DB Failure
                MiscFunctions::mySqlErrors($response);

            }
        } else {
            //Password Failure
            echo $pwresp['message'];
        }
    }

} catch (Exception $x) {

    echo $x->getMessage();
}
