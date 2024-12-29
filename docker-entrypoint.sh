#!/bin/bash
echo "Starting cron service..." >>/var/log/cron.log
service cron start
echo "Starting Flask..." >>/var/log/cron.log
flask run --host=0.0.0.0
