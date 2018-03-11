<?php
$lv = !$langBully ?  "lang" : "bully";
${$lv}['email'] = array(
'reset_password_body' => "Dear {RECIP_NAME},

Your password has now been reset. Please find your new access details below:

~~~~~~~~~~~~~~~~~~~~~~~~~~
Email Address: {EMAIL}
Password: {PASSWORD}
~~~~~~~~~~~~~~~~~~~~~~~~~~
To login, please follow the link below:
{SITE_URL}
~~~~~~~~~~~~~~~~~~~~~~~~~~

Requester's IP Address: {SENDER_IP}",
'reset_password_subject' => "New Password",
'profile_mofified_body' => "Dear {CUSTOMER_NAME},

This email has been sent to confirm that your personal information has been updated successfully. If you feel that your account has been updated by someone other than yourself please contact a member of staff immediately.\n\n

This email was sent from {SITE_URL}\n

Visitor's IP Address: {SENDER_IP}",
'profile_mofified_subject' => "Personal Info Updated",

'admin_new_reg_subject' => "Your Membership at DigitalCollectionPlate!",
'admin_new_reg_body' => "Dear Admin,

For your records the following account has been setup so that you can login to our site. Once logged in you can view the status of your account, and amend your profile.
~~~~~~~~~~~~~~~~~~~~~~~~~~
Account  details are given below:
~~~~~~~~~~~~~~~~~~~~~~~~~~
Name: {RECIP_NAME}
Email: {EMAIL}
Telephone:{PHONE}
Mobile: {MOBILE}
Company Name:{COMPANYNAME}
Address:{ADDRESS1}
Address:{ADDRESS2}
Town:{TOWN}
Country:{COUNTRY}
County/State:{COUNTY}
Postcode:{POSTCODE}

Kindest Regards,
{RECIP_NAME}
~~~~~~~~~~~~~~~~~~~~~~~~~~
This email was sent from {SITE_URL}
Registration IP Address: {SENDER_IP}",
'new_reg_subject' => "Your Membership at DigitalCollectionPlate!",
'new_reg_body' => "Dear {CUSTOMER_NAME},

Thank you for registering at our website, http://www.digitalcollectionplate.com!

Your Exclusive Login Area may be found here:
http://www.digitalcollectionplate.com/signin.php.

Your access details are:

~~~~~~~~~~~~~~~~~~~~~~~~~~
Email:	{EMAIL}
Password:	{PASSWORD}
~~~~~~~~~~~~~~~~~~~~~~~~~~
Kindest Regards,

Digital Collection Plate Coordinator

This email was sent from {SITE_URL}

Registration IP Address: {SENDER_IP}",

'admin_reset_pass_body' => "Dear {RECIP_NAME},

You, or someone with access to your account has requested to have your password be reset.

Your new access details are:

~~~~~~~~~~~~~~~~~~~~~~~~~~
Username: {USERNAME}
Password: {PASSWORD}
~~~~~~~~~~~~~~~~~~~~~~~~~~

This email was sent from {SITE_URL}\n

Requester's IP Address: {SENDER_IP}",
'admin_reset_pass_subject' => "New Admin Access Details",

'admin_contact_us_subject' => "digitalcollectionplate.com Online Enquiry",
'admin_contact_us_body' => "Dear Admin,

You have recieved a new online query from the site!

Enquiry  details are given below:

~~~~~~~~~~~~~~~~~~~~~~~~~~
Name: {RECIP_NAME}
Phone: {TELEPHONE}
Email: {EMAIL}
Comments: {COMMENTS}
~~~~~~~~~~~~~~~~~~~~~~~~~~

This email was sent from {SITE_URL}\n
Requester's IP Address: {SENDER_IP}",

'users_contact_us_subject' => "Thank you for sending  online inquiry form at www.digitalcollectionplate.com",

'users_contact_us_body' => "Dear {RECIP_NAME},

Thank you for sending  online inquiry form at our site.
Your email has been forwarded to a Customer Services Advisor who will be in contact with you shortly to answer your queries.

Your details are given below:
~~~~~~~~~~~~~~~~~~~~~~~~~~
Name: {RECIP_NAME}
Phone: {TELEPHONE}
Email: {EMAIL}
Comments: {COMMENTS}
~~~~~~~~~~~~~~~~~~~~~~~~~~
Kindest Regards,

Digital Collection Plate Coordinator

{ADDRESS}

Phone: {TELEPHONE}
Web. {SITE_URL}
Requester's IP Address: {SENDER_IP}",

);
?>