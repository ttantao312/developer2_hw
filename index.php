<!DOCTYPE html>
<html>
<body>

<?php

//use Monolog\Handler\StreamHandler; 
//use Monolog\Logger;

//$logger = new Logger('data_logger'); For some reason, this is not working because of undefined errors. I cannot seem to use Monolog totally?
//$log->pushHandler(new StreamHandler('data_logger.log', Logger::INFO));

$email = "";
$username = "";
$password = "";
$gender = "";

$emailMessage = "";
$usernameMessage = "";
$passwordMessage = "";
$genderMessage = "";
$numErrors = 0;

class User {
    public $email;
    public $username;
    public $password;
    public $gender;

    public function __construct($email, $username, $password, $gender) {
        $this->email = $email;
        $this->username = $username;
        $this->password = $password;
        $this->gender = $gender;
    }
}

function check($input) {
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input);
    return $input;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST["email"])) {
        $email = check($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailMessage = "Invalid email address!";
            $numErrors++;   
        }
    }
    else {
        $emailMessage = "Error: Email cannot be empty!";
        $numErrors++;
    }
    if (!empty($_POST["username"])) {
        $username = check($_POST["username"]);
        if (!preg_match("/^[a-zA-Z0-9]{6,10}$/",$username)) {
            $usernameMessage = "Only 6-10 alphanumeric characters are allowed!";
            $numErrors++;
        }
    }
    else {
        $usernameMessage = "Error: Username cannot be empty!";
        $numErrors++;
    }
    if (!empty($_POST["password"])) {
        $password = check($_POST["password"]);
        if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/", $password)) {
            $passwordMessage = 'At least one lowercase, one uppercase letter, and one number are required!';
            $numErrors++;
        }
    }
    else {
        $passwordMessage = "Error: Password cannot be empty!";
        $numErrors++;
    }
    if (!empty($_POST["gender"])) {
        $gender = check($_POST["gender"]);
        if (!in_array($gender, ['male', 'female', 'non-binary', 'other'])) {
            $genderMessage = 'Invalid gender'; // Impossible?
            $numErrors++;
        }
    }
    else {
        $genderMessage = "Error: Gender cannot be empty!";
        $numErrors++;
    }
    if ($numErrors == 0) {
        $user = new User($email, $username, $password, $gender);
        $data = ["email" => $email, "username" => $username, "password" => $password, "gender" => $gender];
        $fp = fopen("log.txt", "a");
        fwrite($fp, json_encode($data) . "\n");
        fclose($fp);
    }

}
?>


<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
E-mail: <input type="text" name="email" value="<?php echo isset($email) ? $email : ''; ?>">
<span class="error"><?php echo $emailMessage;?></span><br>
Username: <input type="text" name="username" value="<?php echo isset($username) ? $username : ''; ?>">
<span class="error"><?php echo $usernameMessage;?></span><br>
Password: <input type="text" name="password">
<span class="error"><?php echo $passwordMessage;?></span><br>
Gender:
<input type="radio" name="gender" value="male" <?php if (isset($gender) && $gender == 'male') { echo 'checked'; } ?>>male
<input type="radio" name="gender" value="female" <?php if (isset($gender) && $gender == 'female') { echo 'checked'; } ?>>female
<input type="radio" name="gender" value="non-binary" <?php if (isset($gender) && $gender == 'non-binary') { echo 'checked'; } ?>>non-binary
<input type="radio" name="gender" value="other" <?php if (isset($gender) && $gender == 'other') { echo 'checked'; } ?>>other
<span class="error"><?php echo $genderMessage;?></span><br>
<input type="submit">
</form>

</body>
</html>