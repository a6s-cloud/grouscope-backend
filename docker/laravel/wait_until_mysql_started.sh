#!/bin/bash

GLOBAL_RESULT=1
COUNT=0
RETRY_MAX=60

main() {
    while ! check_connection && [[ $COUNT -le $RETRY_MAX ]]; do
        sleep 1
        (( ++COUNT ))
    done

    return $GLOBAL_RESULT
}

check_connection() {
    mysql -h mysql -u default -psecret \
            -e "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = 'a6s_cloud'" | grep -q "a6s_cloud"
    local result=$?

    [[ "$result" -ne 0 ]] && return 1

    GLOBAL_RESULT=0
    return 0
}

main "$@" || exit 1

exit 0
