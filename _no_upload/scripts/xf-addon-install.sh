#!/bin/sh

if [[ "$#" -ne 1 ]]; then
    echo "Require an add-on name"
    exit 1;
fi

if [[ ! -d "src/addons/$1" ]]; then
    echo "Expect src/addons/$1  to exist"
    exit 1;
fi

php cmd.php xf:addon-install "$@" -v || exit 1