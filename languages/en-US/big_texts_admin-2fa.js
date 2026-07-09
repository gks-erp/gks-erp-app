/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

document.getElementById('gks_2fa_text1').innerHTML =
'<p>2FA stands for <b>Two-factor authentication</b> or <b>Two-factor authentication</b></p>'+
'<p>That is, when we are going to log in to the site, we will enter our username and password as we have been doing so far. With 2FA, we will be asked for an additional code in the next step. We will see this additional code on our mobile phone. Each time this code will be different.</p>'+
'<p>So to connect we also need our mobile phone, so if someone steals or guesses our password, they would have to have our mobile phone too.</p>'+
'<p>The application that will give us this extra code, for Android:</p>';

document.getElementById('gks_2fa_text2').innerHTML =
'<p>for iPhone:</p>';

document.getElementById('gks_2fa_text3').innerHTML =
'<p>The activation process is as follows:</p>'+
'<p>We connect to the site and go to the following page:</p>';

document.getElementById('gks_2fa_text4').innerHTML =
'<p>And the page will appear:</p>';

document.getElementById('gks_2fa_text5').innerHTML =
'<p>On your mobile phone, open the <b>Google Authenticator</b> application and in the bottom right corner there is a button with a (+) to add an account.</p>';

document.getElementById('gks_2fa_text6').innerHTML =
'<p>We scan the QR Code and add the account.</p>'+
'<p>The new account will be named <b>Wordfence (' + window.location.host +')</b></p>'+
'<p>If you are doing the process from a mobile phone and therefore cannot scan the QR Code, you can add the account as follows.<br>'+
'Select and copy the key code:</p>';

document.getElementById('gks_2fa_text7').innerHTML =
'<p>In the <b>Google Authenticator</b> application (<b>Google Authenticator</b>) and at the bottom right there is a button with the (+) to add an account and we select <b>Enter a setup key</b>.<br>'+
'In the <b>Account name</b> field, type a name, e.g. <b>' + window.location.host +'</b>.<br>'+
'In the <b>Your key</b> field we paste the above code.<br>'+
'In the <b>Key type</b> field, leave <b>Time-based</b> selected.<br>'+
'We click on the add button.</p>';

document.getElementById('gks_2fa_text8').innerHTML =
'<p>Once the account has been successfully added to the <b>Google Authenticator</b> application, it will display a 6-digit code, which changes every 10 seconds.</p>'+
'<p>On the page:</p>';

document.getElementById('gks_2fa_text9').innerHTML =
'<p>We enter the 6-digit number that we see at that moment on our mobile phone in the corresponding text box and click on the <b>ACIVATE</b> button.</p>'+
'<p>If done correctly, it will suggest downloading some recovery codes. Select <b>SKIP</b>.</p>';

document.getElementById('gks_2fa_text10').innerHTML =
'<p>The page you will see is the following:</p>';


document.getElementById('gks_2fa_text11').innerHTML =
'<p>Which means that the activation was successful.<br>'+
'To go to our familiar administrative environment, click on <b>gks System</b></p>';

document.getElementById('gks_2fa_text12').innerHTML =
'<p>When we need to reconnect to the site, after entering the login details and pressing the <b>Login</b> button:</p>';

document.getElementById('gks_2fa_text13').innerHTML =
'<p>In the next step we will be asked for the additional code:</p>';

document.getElementById('gks_2fa_text14').innerHTML =
'<p>So we go to our mobile, to the <b>Google Authenticator</b> application (<b>Google Authenticator</b>) and see the 6-digit code it has for <b>Wordfence (' + window.location.host +')</b> at that moment.<br>(If we hold it down, it copies the 6-digit number).</p>'+
'<p>So we register it on the above page and click on <b>Log in</b>.<br>It is a good idea to activate <b>Remember for 30 days</b> so that for the next 30 days you are not asked for an additional password for this device, if you connect from a personal device, e.g. mobile phone or your personal laptop.</p>'+
'<p>If we buy a new phone, we will either have to transfer our <b>Google Authenticator</b> data (<b>Google Authenticator</b>) to the new phone in some way, or disable 2FA on the site and re-enable it for the new phone.</p>';
