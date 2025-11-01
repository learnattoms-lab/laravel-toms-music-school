# Git Workflow for Migration Project

## 📋 Workflow Rules

### Commit Strategy
- **One task = One commit**
- Each task should have a clear, descriptive commit message
- Commit messages should follow the format: `Task X.Y: Task Title`

### Feature Branch Strategy
- **One phase = One feature branch**
- Feature branches follow the pattern: `feature/phase-X-phase-name`
- When a phase is complete, merge the feature branch to `main`
- Example: `feature/phase-0-local-dev-setup`

---

## 🔄 Development Workflow

### Starting a New Task

1. **Ensure you're on the feature branch for the current phase**
   ```bash
   git checkout feature/phase-X-phase-name
   # or create it if starting a new phase:
   git checkout -b feature/phase-X-phase-name
   ```

2. **Complete the task** (make changes, test, verify)

3. **Commit with task-specific message**
   ```bash
   git add .
   git commit -m "Task X.Y: Task Title

   - Subtask 1 completed
   - Subtask 2 completed
   - Acceptance criteria met"
   ```

4. **Push to remote** (if working collaboratively)
   ```bash
   git push origin feature/phase-X-phase-name
   ```

### Completing a Phase

1. **Ensure all tasks are committed individually**
   ```bash
   git log --oneline  # Verify each task has its own commit
   ```

2. **Create a phase completion commit** (optional summary)
   ```bash
   git commit --allow-empty -m "Phase X Complete: Phase Name

   - All X tasks completed
   - Ready for Phase Y"
   ```

3. **Merge feature branch to main**
   ```bash
   git checkout main
   git merge feature/phase-X-phase-name --no-ff
   git push origin main
   ```

4. **Tag the phase completion** (optional)
   ```bash
   git tag -a phase-X-complete -m "Phase X: Phase Name - Complete"
   git push origin phase-X-complete
   ```

---

## 📝 Commit Message Format

### Standard Format
```
Task X.Y: Task Title

- Subtask or change description
- Another subtask or change description
- Acceptance criteria met/verified
```

### Examples

**Task 0.1: Docker Prerequisites Installation**
```
Task 0.1: Docker Prerequisites Installation

- Verified Docker 28.4.0 installed
- Verified Docker Compose v2.39.4 installed
- Verified Docker Desktop running
- Acceptance criteria met
```

**Task 0.3: Create New Laravel Project Structure**
```
Task 0.3: Create New Laravel Project Structure

- Installed Laravel 12.36.1
- Generated application key
- Configured initial project structure
- Acceptance criteria met
```

---

## 🌳 Branch Structure

```
main (production-ready code)
├── feature/phase-0-local-dev-setup
│   ├── Task 0.1 commit
│   ├── Task 0.2 commit
│   ├── Task 0.3 commit
│   └── ... (all Phase 0 tasks)
├── feature/phase-1-foundation-setup
│   └── ... (Phase 1 tasks)
└── feature/phase-2-backend-migration
    └── ... (Phase 2 tasks)
```

---

## ✅ Checklist for Each Task

Before committing:
- [ ] Task completed according to acceptance criteria
- [ ] Code follows Laravel/Vue.js best practices
- [ ] No linter errors
- [ ] Tests passing (if applicable)
- [ ] Documentation updated (if applicable)
- [ ] Commit message follows format: `Task X.Y: Task Title`

---

## ✅ Checklist for Phase Completion

Before merging:
- [ ] All tasks in phase have individual commits
- [ ] All acceptance criteria met for all tasks
- [ ] Phase completion commit created (optional)
- [ ] Feature branch pushed to remote
- [ ] Ready to merge to `main`

---

## 🚨 Important Notes

1. **Never commit multiple tasks in one commit** - Always one task per commit
2. **Never merge incomplete phases** - Wait until all tasks are done
3. **Keep feature branches focused** - One phase per branch
4. **Push regularly** - Don't let commits pile up locally
5. **Tag important milestones** - Helps track progress

---

## 📚 Phase Feature Branches

| Phase | Feature Branch Name | Description |
|-------|-------------------|-------------|
| Phase 0 | `feature/phase-0-local-dev-setup` | Local development environment |
| Phase 1 | `feature/phase-1-foundation-setup` | Foundation setup |
| Phase 2 | `feature/phase-2-backend-migration` | Backend migration |
| Phase 3 | `feature/phase-3-frontend-migration` | Frontend migration |
| Phase 4 | `feature/phase-4-integration-testing` | Integration testing |
| Phase 5 | `feature/phase-5-performance-optimization` | Performance optimization |
| Phase 6 | `feature/phase-6-security-hardening` | Security hardening |
| Phase 7 | `feature/phase-7-deployment` | Deployment setup |

---

**Last Updated**: November 1, 2025  
**Document Owner**: Development Team

