<?php
$dbhost = 'localhost';
$dbuser = 'susancn5_susan2';
$dbpass = 'susan@123';
$conn = mysql_connect($dbhost, $dbuser, $dbpass) or die ('Error connecting to mysql');

$dbname = 'susancn5_mail';
mysql_select_db($dbname);

$query = "SELECT Username, Email, Password, Activation FROM members";
$result = mysql_query($query) 
or die(mysql_error()); 
print " 
<center>
<table border=\"5\" cellpadding=\"5\" cellspacing=\"0\" style=\"border-collapse: collapse\" bordercolor=\"#808080\" width=\"20%\" id=\"AutoNumber2\" bgcolor=\"#C0C0C0\"><tr> 
<td align=center bgcolor=\"#ffffcc\"><b>Name</b></td>
<td align=center bgcolor=\"#ffffcc\"><b>Email</b></td>
<td align=center bgcolor=\"#ffffcc\"><b>Phone</b></td>
<td align=center bgcolor=\"#ffffcc\"><b>Address</b></td>
</tr>"; 

while($row = mysql_fetch_array($result, MYSQL_ASSOC))
{ 
print "<tr>"; 
print "<td align=center>" . $row['Username'] . "</td>"; 
print "<td align=center>" . $row['Email'] . "</td>"; 
print "<td align=center>" . $row['Password'] . "</td>"; 
print "<td align=center>" . $row['Activation'] . "</td>"; 
print "</tr>"; 
} 
print "</table>"; 
print "</center>";
?>