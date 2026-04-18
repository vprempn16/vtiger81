# Branch Users + Project Access (Simple Explanation)

## What this means in daily work

This system keeps projects private to the correct people in a reporting line.

- If a project is created in a line like **A -> B -> C**, only people in that same line should see it.
- People from other lines should not see it.
- If someone opens a project link directly without permission, they will get **Permission Denied**.

## Easy way to understand it

Think of each project as having a private path:

- **Creator side** (who started it)
- **Assigned side** (who it is given to)

Only users in that allowed path can access the project.

## Where users can see the effect

### 1) Project List

Users should only see projects that belong to their allowed line.

### 2) Project Detail/Edit

Even if a user gets the URL, access is blocked if they are not in the allowed line.

### 3) Project Save

When saving, the system checks that the selected "Assigned To" user is valid for that line.

## Expected business result

- Better privacy for projects
- No cross-branch visibility
- Clear permission behavior for list, open, and edit

## Quick example

- Project created in path: **A -> B -> C**
- Allowed: **A, B, C**
- Not allowed: users outside that exact path (even if in another nearby branch)

