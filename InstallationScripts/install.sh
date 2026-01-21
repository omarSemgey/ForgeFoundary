#!/usr/bin/env bash

echo "======================================"
echo "        ForgeFoundary Installer        "
echo "======================================"

# Check if PHP is installed
if ! command -v php >/dev/null 2>&1; then
    echo "Error: PHP is not installed. Please install PHP >=8.2 and try again."
    exit 1
fi

# Check if Composer is installed
if ! command -v composer >/dev/null 2>&1; then
    echo "Error: Composer is not installed. Please install Composer and try again."
    exit 1
fi

read -p "Where should ForgeFoundary be installed? (default: ~/ForgeFoundary) " INSTALL_DIR
INSTALL_DIR=${INSTALL_DIR:-$HOME/ForgeFoundary}

INSTALL_DIR=$(eval echo $INSTALL_DIR)

echo "Cloning repository into $INSTALL_DIR..."
git clone https://github.com/omarsemgey/ForgeFoundary.git "$INSTALL_DIR"

cd "$INSTALL_DIR" || { echo "Failed to enter directory!"; exit 1; }

echo "Running composer install..."
composer install || { echo "Composer install failed! Exiting."; exit 1; }

SCRIPT_PATH="$INSTALL_DIR/ForgeFoundary"

if [ ! -f "$SCRIPT_PATH" ]; then
    echo "Error: CLI script $SCRIPT_PATH not found!"
    exit 1
fi

# Prefer /usr/local/bin if writable
if [ -w /usr/local/bin ]; then
    ln -sf "$SCRIPT_PATH" /usr/local/bin/ForgeFoundary
    echo "ForgeFoundary symlinked to /usr/local/bin/ForgeFoundary"
else
    # fallback to user bin
    mkdir -p "$HOME/.local/bin"
    ln -sf "$SCRIPT_PATH" "$HOME/.local/bin/ForgeFoundary"
    echo "ForgeFoundary symlinked to $HOME/.local/bin/ForgeFoundary"
    echo "Make sure $HOME/.local/bin is in your PATH to run it globally:"
    echo "  export PATH=\"\$HOME/.local/bin:\$PATH\""
fi

echo "Installation complete! You can now run 'ForgeFoundary' from anywhere if PATH is set."
