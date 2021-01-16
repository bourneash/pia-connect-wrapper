#!/usr/bin/php

<?php
//  */5 * * * *     root    /home/jesse/scripts/pia.php > /dev/null 2>&1

function piactl_background() {
        shell_exec("/usr/local/bin/piactl background enable");
}

function piactl_connect() {
        shell_exec("/usr/local/bin/piactl connect");
}

function piactl_disconnect() {
        shell_exec("/usr/local/bin/piactl disconnect");
}

function piactl_login() {
        shell_exec("/usr/local/bin/piactl login /home/jesse/scripts/p4500711.cfg");
}

function piactl_logout() {
        shell_exec("/usr/local/bin/piactl logout");
}


function check_if_vpn_active() {
        $status = trim(shell_exec("/usr/local/bin/piactl get connectionstate"));
        $cur_ip = trim(shell_exec("/usr/bin/curl -s -m 2 ifconfig.me"));
        print "Current Status: $status\n";
        print "Current IP is: $cur_ip\n";

        if($status == "Connected") {
                return(True); //return true if VPN is connected
        } else {
                return(False); //return false if PIA is not connected
        }
}

function ufw_check_enabled() {
        $status = trim(shell_exec("/usr/sbin/ufw status | grep 'Status' | awk '{print $2}'"));
        print "UFW Status: $status\n";
        return($status);
}

function ufw_enable() {
        shell_exec("/usr/sbin/ufw --force enable");
}

function ufw_disable() {
        shell_exec("/usr/sbin/ufw disable");
}

function connect_pia() {
        ufw_disable();
        sleep(1);
        piactl_disconnect();
        piactl_logout();
        sleep(2);
        piactl_login();
        piactl_connect();
        check_if_vpn_active();
//      shell_exec("piactl set requestportforward");
//      shell_exec("piactl get portforward");
        ufw_enable();
        ufw_check_enabled();
}


//MAIN
print "\n\n";

if(isset($argv[1])) {

        if($argv[1] == "disconnect") {
                piactl_disconnect();
                exit();
        }
        if($argv[1] == "reconnect") {
                connect_pia();
                exit();
        }
        if($argv[1] == "checkonly") {
                print "Current PIA connected: " . check_if_vpn_active() . "\n";
                exit();
        }
        if($argv[1] == "firewallcheck") {
                ufw_check_enabled();
                exit();
        }
}



if(check_if_vpn_active() == False) {
        print "---- PIA is not connected - starting----\n";
        connect_pia();
} else {
        print "PIA is connected, doing nothing\n";
}

print "\n\n";




?>
