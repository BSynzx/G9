<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Register - Citizen Connect</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="frame">

  <section class="layout reverse" id="register">
    <form class="card wide" action="../actions/submitRegister.php" method="post">
      <h1>REGISTER</h1>
      <div class="socials">
        <button class="dot fb" type="button" aria-label="Continue with Facebook"><img src="../assets/images/facebook.svg" alt=""></button><span class="bar"></span>
        <button class="dot g" type="button" aria-label="Continue with Google"><img src="../assets/images/google.svg" alt=""></button>
      </div>
      <p class="hint">Create your citizen account</p>

        <input class="field" name="firstName" id="" placeholder="First Name" aria-label="First name" autocomplete="given-name">
        <input class="field" name="middleName" id="" placeholder="Middle Name (optional)" aria-label="Middle name (optional)" autocomplete="additional-name">

      <input class="field" name="lastName" id="" placeholder="Last Name" aria-label="Last name" autocomplete="family-name">
      <input class="field" name="address" id="" placeholder="Address" aria-label="Address" autocomplete="street-address">
      <input class="field" name="contact" id="" type="tel" placeholder="Contact Number" aria-label="Contact number" autocomplete="tel">
      <input class="field" name="email" id="" type="email" placeholder="Enter Email" aria-label="Email" autocomplete="email">
      <div class="row">
        <input class="field" name="password" id="" type="password" placeholder="Enter Password" aria-label="Password" autocomplete="new-password">
        <input class="field" name="confirmpassword" id="" type="password" placeholder="Confirm Password" aria-label="Confirm password" autocomplete="new-password">
      </div>
      <button class="btn" type="submit">Create Account</button>
      <p class="switch">Have an account? <a href="login.php">Log in.</a></p>
    </form>
    <div class="brand">
      <img class="logo" src="../assets/images/logo.png" alt="Citizen Connect - Your Voice. Our Action.">
    </div>
  </section>

</div>
</body>
</html>
