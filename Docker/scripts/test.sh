#!/bin/bash
set -e

# Run PHPUnit tests.
# @todo

# Run Cypress tests.
# @todo

# Run Behat tests
if [ $# -gt 0 ]; then
  ddev exec "cd tests/behat && ../../vendor/bin/behat --tags=$1" || true
else
  ddev exec "cd tests/behat && ../../vendor/bin/behat" || true
fi
dt=$(date +'%Y_%m_%d_%H_%M_%S')
mv tests/behat/reports/current "tests/behat/reports/${dt}"
open "tests/behat/reports/${dt}/index.html"
