# 🔔 Notification System - Quick Start Guide

## 🎯 What You Got

A **professional floating notification center** that alerts you about:
- 🕐 Medicines expiring soon (30 days warning)
- 📉 Low stock items (below 10 units)
- 🚨 Out of stock items (0 units)

---

## 🚀 How to Use (3 Simple Steps)

### Step 1: Click the Bell 🔔
Look at the **top-right corner** of your screen. You'll see a bell icon with a red badge showing the number of unread notifications.

```
┌─────────────────────────────────────┐
│  HealthChain        🔍  📅  🔔(3)  👤│
│                                     │
└─────────────────────────────────────┘
                              ↑
                         Click here!
```

### Step 2: View Notifications 👀
A beautiful panel slides down showing all your notifications:

```
┌──────────────────────────────────────┐
│ Notifications  (3)    Mark all read ✕│
├──────────────────────────────────────┤
│ 🔴 Medicine Expiring Soon            │
│    Paracetamol expires in 5 days  →  │
│                                      │
│ 🟠 Low Stock Alert                   │
│    Aspirin has only 3 units left  →  │
│                                      │
│ 🔵 Medicine Expiring Soon            │
│    Ibuprofen expires in 20 days   →  │
└──────────────────────────────────────┘
```

### Step 3: Take Action ⚡
- **Click any notification** → Goes to that medicine/batch page
- **Click "Mark all read"** → Clears all notifications
- **Click outside** → Closes the panel

---

## 🎨 Understanding Colors

### 🔴 Red (Critical)
- Expires in 7 days or less
- Out of stock (0 units)
- **Action needed NOW!**

### 🟠 Orange (High)
- Expires in 8-15 days
- Very low stock (1-5 units)
- **Action needed soon**

### 🔵 Blue (Medium)
- Expires in 16-30 days
- Low stock (6-10 units)
- **Keep an eye on it**

---

## ⚙️ Features

### Auto-Refresh 🔄
- Notifications update every **2 minutes** automatically
- No need to refresh the page!

### Smart Badge 🔢
- Shows unread count
- Pulses to grab attention
- Updates in real-time

### Mark as Read ✅
- Click notification → Auto marks as read
- Or click "Mark all read" button
- Badge updates instantly

### Toast Notifications 🍞
- Success messages appear bottom-right
- Confirms your actions
- Auto-disappears after 3 seconds

---

## 📱 Works Everywhere

### Desktop 💻
- Full-width panel (420px)
- Positioned top-right
- Smooth animations

### Mobile 📱
- Full-screen panel
- Touch-optimized
- Swipe to close

### Dark Mode 🌙
- Automatically adapts
- Easy on the eyes
- Maintains readability

---

## 🧪 Test It Out

### Option 1: Use the App
1. Go to `http://localhost:8000/dashboard`
2. Click the bell icon (🔔)
3. See your notifications!

### Option 2: Test Page
1. Go to `http://localhost:8000/test-notifications.html`
2. Click "Run All Tests"
3. Verify everything works!

---

## 🎯 Common Scenarios

### Scenario 1: Medicine Expiring Soon
```
Notification appears:
"Paracetamol (Batch: BATCH-001) expires in 5 days"

What to do:
1. Click the notification
2. Goes to batch page
3. Check stock level
4. Order new batch if needed
```

### Scenario 2: Low Stock Alert
```
Notification appears:
"Aspirin has only 3 units remaining"

What to do:
1. Click the notification
2. Goes to medicine page
3. Click "Add Batch"
4. Record new stock
```

### Scenario 3: Out of Stock
```
Notification appears:
"Ibuprofen has 0 units remaining"

What to do:
1. Click the notification
2. Goes to medicine page
3. URGENT: Order immediately
4. Add batch when received
```

---

## 💡 Pro Tips

### Tip 1: Check Daily
- Open notifications every morning
- Stay ahead of expiries
- Prevent stockouts

### Tip 2: Use "Mark All Read"
- After reviewing all notifications
- Keeps your inbox clean
- Fresh start each day

### Tip 3: Click to Navigate
- Don't just read notifications
- Click them to take action
- Faster workflow

### Tip 4: Watch the Badge
- Red badge = unread notifications
- No badge = all clear!
- Pulse animation = urgent

---

## 🔧 Customization (For Admins)

### Change Alert Thresholds
Edit `app/Controllers/NotificationController.php`:

```php
// Expiry warning (default: 30 days)
$expiring = $batch->getExpiringSoon(30);

// Low stock threshold (default: 10 units)
$lowStock = $medicine->getLowStock(10);

// Critical urgency (default: 7 days)
$urgency = $daysLeft <= 7 ? 'critical' : ...
```

### Change Refresh Rate
Edit `app/Views/layouts/app.php`:

```javascript
// Auto-refresh (default: 2 minutes)
setInterval(loadNotifications, 120000);

// Change to 1 minute:
setInterval(loadNotifications, 60000);
```

---

## 🐛 Troubleshooting

### Problem: Badge not showing
**Solution**: 
- Check if you have any expiring medicines
- Verify you're logged in
- Refresh the page (Ctrl+R)

### Problem: Panel not opening
**Solution**:
- Check browser console (F12)
- Clear browser cache (Ctrl+Shift+Delete)
- Try different browser

### Problem: Notifications not loading
**Solution**:
- Check internet connection
- Verify server is running
- Check API endpoint: `/api/notifications`

### Problem: Badge shows wrong count
**Solution**:
- Click "Mark all read"
- Refresh the page
- Wait for auto-refresh (2 min)

---

## 📊 What Gets Notified

### ✅ You WILL Get Notified About:
- Medicines expiring in 30 days or less
- Medicines with stock below 10 units
- Medicines completely out of stock
- Critical urgency items (expires ≤ 7 days)

### ❌ You WON'T Get Notified About:
- Medicines expiring after 30 days
- Medicines with stock above 10 units
- Inactive medicines
- Already expired batches (handled separately)

---

## 🎓 Best Practices

### For Managers
1. Check notifications **twice daily** (morning & afternoon)
2. Prioritize **red (critical)** notifications first
3. Delegate **orange (high)** to staff
4. Monitor **blue (medium)** for planning

### For Staff
1. Check notifications at **start of shift**
2. Report critical items to manager
3. Update stock levels promptly
4. Mark notifications as read after action

### For Admins
1. Review notification patterns weekly
2. Adjust thresholds if needed
3. Monitor system performance
4. Train users on notification system

---

## 🚀 Next Steps

### Week 1: Learn
- Explore the notification panel
- Click different notifications
- Try "Mark all read"
- Test on mobile device

### Week 2: Integrate
- Make it part of daily routine
- Check notifications first thing
- Take action on alerts
- Track response times

### Week 3: Optimize
- Adjust thresholds if needed
- Set up notification schedule
- Train team members
- Monitor effectiveness

---

## 📞 Need Help?

### Quick Links
- **Full Documentation**: `NOTIFICATION_SYSTEM.md`
- **Implementation Guide**: `NOTIFICATION_IMPLEMENTATION.md`
- **Test Page**: `http://localhost:8000/test-notifications.html`

### Support Checklist
1. ✅ Read this guide
2. ✅ Check browser console (F12)
3. ✅ Test on different browser
4. ✅ Clear cache and retry
5. ✅ Check API responses

---

## ✨ Summary

**You now have a powerful notification system that:**
- ✅ Alerts you about important events
- ✅ Updates automatically every 2 minutes
- ✅ Works on desktop and mobile
- ✅ Supports dark mode
- ✅ Is easy to use
- ✅ Helps prevent stockouts
- ✅ Reduces expired medicines
- ✅ Improves workflow efficiency

**Start using it today and never miss an important alert!** 🎯

---

**Version**: 1.0.0  
**Last Updated**: <?= date('F j, Y') ?>  
**Status**: Ready to Use ✅
