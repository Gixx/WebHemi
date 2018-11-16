#!/bin/bash

SAVE_CURSOR=$(tput sc)
RESTORE_CURSOR=$(tput rc)
CLEAR=$(tput el)
COUNTER=1

echo "TESTING PROGRESS DIALOG"
echo ""
echo "Use the following SessionID in your dialog attribute: 'test'"
echo ""
echo "Total: 100";
echo -n "Current:";
while (($COUNTER <= 100));
do
    echo -n "$SAVE_CURSOR $COUNTER$RESTORE_CURSOR"
    echo "{\"total\": 100, \"current\":$COUNTER}" > ./data/progress/test.json
    ((COUNTER++))
    sleep 0.2
done
echo ""
