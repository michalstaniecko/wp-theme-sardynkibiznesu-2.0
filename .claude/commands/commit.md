---
allowed-tools: Bash(git status:*), Bash(git diff:*), Bash(git log:*), Bash(git add:*), Bash(git commit:*)
description: Analyze changes and create git commit
---

## Changes

!`git status --short`

!`git diff --staged`

!`git diff`

## Recent commits style

!`git log --oneline -5`

## Task

Create a concise commit message based on the changes above. Follow the commit style from recent commits. Stage all relevant files and commit.

End with: `Co-Authored-By: Claude <noreply@anthropic.com>`
