#!/bin/bash
set -e

# Start SSH service
service ssh start

# Start Apache service
exec apache2-foreground
