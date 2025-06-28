#!/bin/bash

# Laravel Vue Auth Starter Release Script
# This script helps with versioning and releasing the package

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if version is provided
if [ -z "$1" ]; then
    print_error "Please provide a version number (e.g., 1.0.0)"
    echo "Usage: ./scripts/release.sh <version>"
    exit 1
fi

VERSION=$1

print_status "Starting release process for version $VERSION"

# Check if we're on main branch
CURRENT_BRANCH=$(git branch --show-current)
if [ "$CURRENT_BRANCH" != "main" ]; then
    print_warning "You're not on the main branch. Current branch: $CURRENT_BRANCH"
    read -p "Do you want to continue? (y/N): " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        print_error "Release cancelled"
        exit 1
    fi
fi

# Check if working directory is clean
if [ -n "$(git status --porcelain)" ]; then
    print_error "Working directory is not clean. Please commit or stash your changes."
    git status --short
    exit 1
fi

# Update version in package-composer.json
print_status "Updating version in package-composer.json"
sed -i "s/\"version\": \".*\"/\"version\": \"$VERSION\"/" package-composer.json

# Update CHANGELOG.md
print_status "Updating CHANGELOG.md"
TODAY=$(date +%Y-%m-%d)
sed -i "s/## \[Unreleased\]/## [Unreleased]\n\n## [$VERSION] - $TODAY/" CHANGELOG.md

# Commit changes
print_status "Committing version changes"
git add package-composer.json CHANGELOG.md
git commit -m "Release version $VERSION"

# Create and push tag
print_status "Creating and pushing tag v$VERSION"
git tag -a "v$VERSION" -m "Release version $VERSION"
git push origin main
git push origin "v$VERSION"

print_status "Release $VERSION completed successfully!"
print_status "Next steps:"
echo "1. Wait for GitHub Actions to complete"
echo "2. Create a release on GitHub with the tag v$VERSION"
echo "3. Submit the package to Packagist (if not already done)"
