#!/bin/bash

# Start the SSH daemon
/usr/sbin/sshd

# Start Apache server in the foreground
apache2ctl -D FOREGROUND