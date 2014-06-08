<?php 

echo "Hello, PHP!";

// Create connection
$con=mysqli_connect('rsadak.com', 'rsadak', 'Whistle', 'rsadak_betting');
//$con=mysqli_connect('localhost', 'rsadak', 'Whistle', 'rsadak_betting');

// Check connection
if( mysqli_connect_errno()){
	echo "Error: ";
	echo mysqli_connect_errno();
}
else{
	echo "Success";
}

?>