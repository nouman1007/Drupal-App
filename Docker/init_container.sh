#!/bin/bash
set -e

# Start SSH service
service ssh start

service ssh status

# Start Apache service
exec apache2-foreground

