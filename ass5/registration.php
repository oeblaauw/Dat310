<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>Magazine Subscription</title>
        <link rel='stylesheet' type="text/css" href="style.css">
    </head>
    <?php
    include 'Magazine.class.php';
    include 'Subscription.class.php';

    // define variables and set to empty values
    $firstname = $lastname = $email = $gender = $subLength = $expDate = $mags = "";
    $firstnameErr = $lastnameErr = $emailErr = $genderErr = $subLengthErr = $magErr = "";
    $totalPrice = $numberOfMagazines = $discount = 0;
    $emailValidated = true;
    $output = "";
    //Creating a new subscription
    $sub = new Subscription();

    // Creating an array of magazines, pushing magazines to array
    $magazines = array();
    $mag1 = new Magazine(1, "Technofreak", 24.99);
    array_push($magazines, $mag1);
    $mag2 = new Magazine(2, "ComputahWiz", 19.99);
    array_push($magazines, $mag2);
    $mag3 = new Magazine(3, "Switch and Chips", 16.99);
    array_push($magazines, $mag3);
    $mag4 = new Magazine(4, "ProGamer 4K", 19.59);
    array_push($magazines, $mag4);
    $mag5 = new Magazine(5, "Santa's Screen", 25.59);
    array_push($magazines, $mag5);

    //Validating inputs from forms
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (empty($_POST["firstname"])) {
            $firstnameErr = "Firstname is required";
        } else {
            $firstname = test_input($_POST["firstname"]);
            $firstnameErr = "";
        }
        if (empty($_POST["lastname"])) {
            $lastnameErr = "Lastname is required";
        } else {
            $lastname = test_input($_POST["lastname"]);
            $lastnameErr = "";
        }
        if (empty($_POST["email"])) {
            $emailErr = "Email is required";
            $emailValidated = false;
        } else {
            $email = test_input($_POST["email"]);
            $emailErr = "";
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $emailErr = "Invalid email format";
                $emailValidated = false;
            }
        }
        if (empty($_POST["gender"])) {
            $genderErr = "Gender is required";
        } else {
            $gender = test_input($_POST["gender"]);
            $genderErr = "";
        }

        //Checking subscription period
        if (!isset($_POST['subLength'])) {
            $subLengthErr = "Subscription period must be 6, 12, 18 or 24 months!";
        } else {
            $subLength = test_input($_POST["subLength"]);
            $subLengthErr = "";
        }

        //If email is OK, then check, if any, which magazines have been checked in form
        if ($emailValidated) {
            for ($i = 1; $i <= count($magazines); $i++) {
                $currentLabel = $current = "";
                $selectedMags = 0;
                switch ($i) {
                    case 1:
                        $current = $mag1;
                        $currentLabel = 'mag1';
                        break;
                    case 2:
                        $current = $mag2;
                        $currentLabel = 'mag2';
                        break;
                    case 3:
                        $current = $mag3;
                        $currentLabel = 'mag3';
                        break;
                    case 4:
                        $current = $mag4;
                        $currentLabel = 'mag4';
                        break;
                    case 5:
                        $current = $mag5;
                        $currentLabel = 'mag5';
                        break;
                    default:
                        break;
                }
                //If a magazine is checked, subscribe to that magazine with 
                //the (validated) email given in the form
                //Add magazine name and price to output, and increase the number of selected magazines.
                if (isset($_POST[$currentLabel])) {
                    $sub->subscribe($current->id, $email);
                    $mags .= "<tr><td>" . $current->name .
                            "</td><td>$" . $current->price .
                            " per month</td></tr>";
                    $selectedMags++;
                    $totalPrice += $current->price;
                    $numberOfMagazines++;
                }
            }
        } //Magazine selection complete
        //Validating selection of magazines, and gets discount
        if ($numberOfMagazines < 1) {
            $magErr = "You must select at least one magazine!";
        } else {
            $magErr = "";
        }
        if ($numberOfMagazines > 3) {        //4 or more
            $discount = 15;
        } else if ($numberOfMagazines > 2) { //3
            $discount = 10;
        } else if ($numberOfMagazines > 1) { //2
            $discount = 5;
        } else {                            //1
            $discount = 0;
        }

        //Calculating total price with discount
        $totalPrice*=(1 - ($discount / 100));
        $totalPrice = round($totalPrice, 2);
        
        //Calculating expiration date
        $expDate = date('Y-m-d', strtotime($subLength . ' months'));

        //PRINTING CONFIRMATION
        //Checking that every input is validated, and displays the summary of the subscription orders
        if ($firstnameErr == "" && $lastnameErr == "" && $emailValidated && $genderErr == "" && $magErr == "" && $subLengthErr == "") {
            $output = "<h3>Personal Information:</h3>" .
                    "<table><tr><td>" .
                    "Your Name: </td><td>" . $firstname . " " . $lastname .
                    "</td></tr><tr><td>" .
                    "Your Email:</td><td>" . $email .
                    "</td></tr><tr><td>" .
                    "Sub. Period: </td><td>" . $subLength . " months" .
                    "</td></tr></table>" .
                    "<h3>Subscription Summary:</h3>" .
                    "<table>" . $mags .
                    "<tr><td><br/></td><td><br/></td></tr>";
            if ($discount > 0) {
                $output .= "</tr><tr><td>" .
                        "Discount: </td><td>" . $discount .
                        "%</td></tr>";
            }
            $output .= "<tr><td>" .
                    "Total Price/month: </td><td>$" . $totalPrice .
                    "</td></tr><tr><td>" .
                    "Total Price/period: </td><td>$" . $totalPrice * $subLength .
                    "</td></tr><tr><td><br/>" .
                    "Submission expires: </td><td><br/><b>" . $expDate .
                    "</b></td></tr></table>";
        }
    } //Form validation complete

    function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
    ?>

    <body>
        <h1>Magazine Subscription Form</h1>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <table>
                <tr>
                    <td>Firstname:</td>
                    <td>
                        <input type="text" name="firstname" value="<?php echo $firstname; ?>">
                    </td>
                    <td>
                        <span class="error"><?php echo $firstnameErr; ?></span>
                    </td>
                </tr>
                <tr>
                    <td>Lastname:</td>
                    <td>
                        <input type="text" name="lastname" value="<?php echo $lastname; ?>">
                    </td>
                    <td>
                        <span class="error"><?php echo $lastnameErr; ?></span>
                    </td>
                </tr>
                <tr>
                    <td>Email:</td>
                    <td>
                        <input type="text" name="email" value="<?php echo $email; ?>">
                    </td>
                    <td>
                        <span class="error"><?php echo $emailErr; ?></span>
                    </td>
                </tr>
                <tr>
                    <td>Gender:</td>
                    <td>
                        <input type="radio" name="gender" 
                        <?php if (isset($gender) && $gender == "male") echo "checked"; ?>
                               value="male"> Male
                        <input type="radio" name="gender" 
                        <?php if (isset($gender) && $gender == "female") echo "checked"; ?>
                               value="female"> Female
                    </td>
                    <td>
                        <span class="error"><?php echo $genderErr; ?></span>
                    </td>
                </tr>
                <tr>
                    <td><br/>Choose Magazines:                         
                        <br/></td>
                </tr> 
                <tr>
                    <td></td>
                    <td>
                        <?php
                        $sub->listMagazines($magazines);
                        ?>
                    </td>
                    <td><span class="error"><?php echo $magErr; ?></span></td>
                </tr>

                <tr>
                    <td>Subscription Period:</td>
                    <td>
                        <select name='subLength'>
                            <option value='6' 
                                <?php if (isset($subLength) && $subLength == 6) echo "selected"; ?>
                                    >6 Months</option>
                            <option value='12'
                                    <?php if (isset($subLength) && $subLength == 12) echo "selected"; ?>
                                    >12 Months</option>
                            <option value='18' 
                                    <?php if (isset($subLength) && $subLength == 18) echo "selected"; ?>
                                    >18 Months</option>
                            <option value='24' 
                                    <?php if (isset($subLength) && $subLength == 24) echo "selected"; ?>
                                    >24 Months</option>
                        </select>

                    </td>
                    <td>
                        <span class="error"><?php echo $subLengthErr; ?></span>
                    </td>
                </tr>

                <tr>
                    <td>
                        <input type="submit" id="submit" value="Submit">
                    </td>
                </tr>
            </table>
        </form>
        <div id="summary">
            <?php
            echo $output;
            ?>
        </div>
    </body>
</html>
