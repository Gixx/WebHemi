#!/bin/bash

SAVE_CURSOR=$(tput sc)
RESTORE_CURSOR=$(tput rc)
CLEAR=$(tput el)
COUNTER=1

while (($COUNTER <= 100));
do
    echo "{\"total\": 100, \"current\":$COUNTER}" > ./data/progress/test.json
    ((COUNTER++))
    sleep 0.2
done


