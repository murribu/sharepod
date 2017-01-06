<!DOCTYPE html>
<html lang="en">
<head>
  <title>Login Redirect</title>
  <script>
    function authChange() {
        window.opener.socialAuthChange();
        //simple browers won't do this so...
        location.href = '/';
    }
    authChange();
    window.close();
  </script>
</head>
<body>
</body>
</html>
