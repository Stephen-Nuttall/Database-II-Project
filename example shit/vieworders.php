<?php
  //create short variable name
  $DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
?>
<html>
<head>
  <title>Bob's Auto Parts - Customer Orders</title>
</head>
<body>
<h1>Bob's Auto Parts</h1>
<h2>Customer Orders</h2>
<?php

   $fp = fopen("$DOCUMENT_ROOT/code/orders.txt", 'rb');

   if (!$fp) {
     echo "<p><strong>No orders pending.
           Please try again later.</strong></p>";
     exit;
   }

   while (!feof($fp)) {
      $order= fgets($fp, 999);
      echo $order."<br />";
   }

   fclose($fp);

?>
</body>
</html>
