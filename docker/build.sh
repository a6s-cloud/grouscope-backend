#!/usr/bin/env bash

main() {
    cd "$( dirname "${BASH_SOURCE[0]}" )"

    local dir
    while read dir; do
        dir="$(basename $dir)"
        if [[ -f "${dir}/Dockerfile" ]]; then
            pushd "$dir"
            docker build -t "a6scloud/grouscope-${dir}" .
            popd
        fi
    done < <(find . -maxdepth 1 -mindepth 1 -type d)
}

main "$@"

