<?php

    require_once('Connections/kiosk.php');

    $hasErrors = false;
    
    // Let's make sure someone submitted this page, otherwise raise an error.
    if (isset($_POST['Submit'])) {
        
        if (strlen(trim($_POST['customer_mail'])) > 0) {
            $Email_To = $_POST['customer_mail']; // separate email addresses with commas
            //$Email_To = "tony@delaris.com, lisa@delaris.com"; // separate email addresses with commas
            $Subject = "Home Energy Makeover Contest -  Entry Link Request";
            $Body = "<p>Thank you for signing up for the contest!</p><p>More content goes here!</p>";
            
            // Send the message.
            if (mail(
                $Email_To,
                $Subject,
                $Body,
                "From: HEM <do-not-reply@homeenergymakeoveroregon.org>\n" .
                "MIME-Version: 1.0\n" .
                "Content-type: text/html; charset=iso-8859-1"
            )) {
                // Insert the record into the database.
                mysql_select_db($database_kiosk, $kiosk_connection);
                $sql = "INSERT into signups (id,name,email,`created_at`) VALUES (NULL,'','" . $_POST['customer_mail'] . "',NOW())";
                if(!mysql_query($sql,$kiosk_connection)){
                    echo "Insert statement failed. Error: " . mysql_error();
                }
                mysql_close($kiosk_connection);
            }
            else
            {
                $hasErrors = true;
            }
        }
        else
        {
            $hasErrors = true;
        }
    }
    else
    {
        // Redirect back to the kiosk page.
        header("Location: kiosk.php");
    }

    if ($hasErrors){
        // Show an error page
        header("Location: whoops.php");
    }
    else
    {
        // Show the thank you page.
        header("Location: thankyou.php");
    }
?>