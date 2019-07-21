#!/bin/bash

main() {
    local url
    cd /opt || return 1

    for url in "${@}"; do
        local dir="$(basename "${url%%.git}")"
        if [[ ! -d "${dir}" ]]; then
            git clone "$url" "${dir}"
        else
            git -C "$dir" reset --hard
            git -C "$dir" pull origin HEAD
        fi
    done
}
main "$@" || exit 1

