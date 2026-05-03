# 🚀 Healthcare Supply Chain - Git Workflow Guide

## 📋 Team Members & Branches

| Member | Email | Branch Name |
|--------|-------|-------------|
| **Member 1** | 423001971@ntc.edu.ph | `feature/member1-core` |
| **Member 2** | 423004636@ntc.edu.ph | `feature/member2-auth` |
| **Member 3** | magistradoshairamae@gmail.com | `feature/member3-medicine` |
| **Member 4** | 423003266@ntc.edu.ph | `feature/member4-ui` |

---

## 🎯 Repository

**GitHub Repository:** https://github.com/JmMoshi16/Healthcare-Supply-Chain.git

---

## 📝 Step-by-Step Guide

### **STEP 1: First Time Setup (Do This Once)**

Open your terminal/command prompt and run:

```bash
# Navigate to your Desktop (or wherever you want)
cd Desktop

# Clone the repository
git clone https://github.com/JmMoshi16/Healthcare-Supply-Chain.git

# Enter the project folder
cd Healthcare-Supply-Chain

# Set your identity (IMPORTANT!)
git config user.name "Your Full Name"
git config user.email "your@ntc.edu.ph"

# Verify your identity
git config user.name
git config user.email
```

---

### **STEP 2: Create Your Branch**

Each member creates their own branch:

#### **Member 1 (423001971@ntc.edu.ph):**
```bash
git checkout -b feature/member1-core
```

#### **Member 2 (423004636@ntc.edu.ph):**
```bash
git checkout -b feature/member2-auth
```

#### **Member 3 (magistradoshairamae@gmail.com):**
```bash
git checkout -b feature/member3-medicine
```

#### **Member 4 (423003266@ntc.edu.ph):**
```bash
git checkout -b feature/member4-ui
```

---

### **STEP 3: Daily Workflow (Work & Commit)**

Every time you work on the project:

```bash
# 1. Make sure you're on your branch
git checkout feature/your-branch-name

# 2. Work on your files (edit, create, delete)
# ... make your changes ...

# 3. Check what changed
git status

# 4. Add your changes
git add .

# 5. Commit with a message
git commit -m "feat: describe what you did"

# 6. Push to GitHub (FIRST TIME)
git push -u origin feature/your-branch-name

# 7. Push to GitHub (AFTER FIRST TIME)
git push
```

---

## 💡 Commit Message Examples

Use clear, descriptive commit messages:

```bash
# Good examples ✅
git commit -m "feat: implement user authentication"
git commit -m "feat: create Medicine model"
git commit -m "ui: create dashboard layout"
git commit -m "fix: correct stock calculation"
git commit -m "style: add CSS animations"
git commit -m "security: implement CSRF protection"

# Bad examples ❌
git commit -m "update"
git commit -m "changes"
git commit -m "fix"
```

---

## 🔄 Daily Routine

### **Morning:**
```bash
# Get latest code from main
git checkout main
git pull origin main

# Switch back to your branch
git checkout feature/your-branch-name
```

### **During Work:**
```bash
# After making changes
git add .
git commit -m "feat: what you did"
git push
```

### **End of Day:**
```bash
# Make sure everything is pushed
git status
git push
```

---

## 📊 Check Your Progress

### **See your commits:**
```bash
git log --oneline
```

### **Count your commits:**
```bash
git log --oneline | wc -l
```

### **See what branch you're on:**
```bash
git branch
```

---

## 🎯 Target Goals

- ✅ **20-30 commits** per member
- ✅ **Commit daily** (not all at once!)
- ✅ **Clear commit messages**
- ✅ **Push regularly** so team can see progress

---

## 🚨 Common Issues & Solutions

### **Problem: "Permission denied"**
**Solution:** Make sure you're added as a collaborator. Check your email for GitHub invitation.

### **Problem: "Can't push"**
```bash
# Solution: Pull first, then push
git pull origin feature/your-branch-name
git push
```

### **Problem: "Forgot which branch I'm on"**
```bash
# Check current branch
git branch
```

### **Problem: "Made changes on wrong branch"**
```bash
# Save changes temporarily
git stash

# Switch to correct branch
git checkout feature/your-branch-name

# Apply saved changes
git stash pop
```

---

## ✅ Quick Reference

### **First Time:**
```bash
git clone https://github.com/JmMoshi16/Healthcare-Supply-Chain.git
cd Healthcare-Supply-Chain
git config user.name "Your Name"
git config user.email "your@ntc.edu.ph"
git checkout -b feature/your-branch-name
```

### **Every Day:**
```bash
git add .
git commit -m "feat: what you did"
git push
```

---

## 📱 Need Help?

- **GitHub Repository:** https://github.com/JmMoshi16/Healthcare-Supply-Chain
- **Contact:** JmMoshi16 (Project Lead)
- **Group Chat:** Ask questions anytime!

---

## 🎓 Important Notes

1. ✅ **Never commit directly to `main`** - Always use your branch
2. ✅ **Commit regularly** - Don't wait until the end
3. ✅ **Use meaningful messages** - Describe what you did
4. ✅ **Push often** - So team can see your progress
5. ✅ **Ask if stuck** - Better to ask than to mess up!

---

## 🏆 Success Checklist

- [ ] Cloned the repository
- [ ] Set my name and email
- [ ] Created my branch
- [ ] Made my first commit
- [ ] Pushed to GitHub
- [ ] Can see my branch on GitHub
- [ ] Making regular commits (20-30 total)

---

**Last Updated:** May 2, 2026  
**Version:** 1.0  
**Project:** Healthcare Supply Chain Management System
