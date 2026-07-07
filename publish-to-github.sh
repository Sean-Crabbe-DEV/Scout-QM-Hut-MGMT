#!/usr/bin/env bash
set -Eeuo pipefail

REPO_URL="https://github.com/Sean-Crabbe-DEV/Scout-QM-Hut-MGMT.git"
BRANCH="main"

if [[ ! -d .git ]]; then
  git init
  git branch -M "$BRANCH"
  git remote add origin "$REPO_URL"
fi

git add .
git commit -m "feat: Scout Hut Management System v1.2" || true
git push -u origin "$BRANCH"
