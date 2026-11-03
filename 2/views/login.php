<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Log In - Citizen Connect</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="frame">

  <section class="layout" id="login">
    <form class="card" action="../actions/submitLogin.php" method="post">
      <h1>LOG IN</h1>
      <div class="socials">
        <button class="dot fb" type="button" aria-label="Continue with Facebook"><img src="../assets/images/facebook.svg" alt=""></button><span class="bar"></span>
        <button class="dot g" type="button" aria-label="Continue with Google"><img src="../assets/images/google.svg" alt=""></button>
      </div>
      <p class="hint">Log in with Email &amp; Password</p>
      <input class="field" name="email" type="email" placeholder="Enter Email" aria-label="Email" autocomplete="username">
      <input class="field" name="password" type="password" placeholder="Enter Password" aria-label="Password" autocomplete="current-password">
      <p class="small"><a href="#">Forgot Password?</a></p>
      <p class="switch">No Account? <a href="register.php">Sign Up here.</a></p>
      <button class="btn" type="submit">Log In</button>
    </form>
    <div class="brand">
      <img class="logo" src="../assets/images/logo.png" alt="Citizen Connect - Your Voice. Our Action.">
    </div>
  </section>

</div>
</body>
</html>
