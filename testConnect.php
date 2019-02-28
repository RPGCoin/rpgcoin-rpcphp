<?php
/*
====================
RPGcoinRPC-PHP Connection Test

A basic example of how to disply node info and confirm that connection 
has successfully been established to the RPC host on the server.
https://github.com/HazeDevelopment/rpgcoinrpc-php/
====================
*/
//Include RPGcoinRPC-PHP class
require_once('rpgcoin-rpc.php');

//Initialize RPGCoin connection/object with default host/port
//$rpgcoin = new RPGcoin('user','pass');
//Or specify a host and port.
$rpgcoin = new RPGcoin('user','pass','localhost','7214');

//Get info on the rpgd daemon
$rpgcoin->getinfo();

//Transaction information
$rpgcoin->getrawtransaction('a9a2217d055ed6dc7b0d72bdf296d8de9b3f2c1de1199c13334871e9dfe015b4',1);

//Block Information
$rpgcoin->getblock('000005f7d089eca4f7653bf48d33ba6d126b8785ccde17d4765bce8122f1b6bc');

//Check HTTP status with $rpgcoin->status
if ($rpgcoin->status == 500) {
	echo "Connection to RPC Server Established!";
} else {
	echo "HTTP Error: ".$rpgcoin->status;
}
