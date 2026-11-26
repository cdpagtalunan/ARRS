#!/bin/bash

export NVM_DIR="$HOME/.nvm"
[ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"

# Check if Node 16.15.1 is installed; if not, install it
if ! nvm ls 16.15.1 > /dev/null 2>&1; then
    nvm install 16.15.1
fi

nvm use 16.15.1
npm run watch
