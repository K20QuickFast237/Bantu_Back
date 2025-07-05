<html>
  <body>
    <script src="https://accounts.google.com/gsi/client" async></script>
    <script>
      function handleCredentialResponse(response) {
        console.log("Encoded JWT ID token: " + response.credential);
        console.log("All response: ");
        console.log(response);
      }
      window.onload = function () {
        google.accounts.id.initialize({
          client_id: "<?php echo env('GOOGLE_CLIENT_ID'); ?>",
          callback: handleCredentialResponse
        });
        google.accounts.id.renderButton(
          document.getElementById("buttonDiv"),
          { theme: "outline", size: "large" }  // customization attributes
        );
        google.accounts.id.prompt(); // also display the One Tap dialog
      }
    </script>
    <div id="buttonDiv"></div>
  </body>
</html>