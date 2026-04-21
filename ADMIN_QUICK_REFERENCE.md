# 🔑 Complete Admin Panel - Quick Reference

## 📍 Access Admin Panel

### Method 1: Direct URL
```
http://localhost/law/admin/dashboard.php
```

### Method 2: Login First
1. Go to `http://localhost/law/login.php`
2. Login with admin account (role = 3)
3. Automatically redirected to `http://localhost/law/admin/dashboard.php`

---

## 👤 Admin Credentials

Create an admin user with these requirements:
- **role** = 3 (Administrator)
- **status** = active
- **email** = any valid email
- **password** = any secure password

### Database Query to Create Admin:
```sql
INSERT INTO users (
  full_name, 
  email, 
  password, 
  phone, 
  role, 
  status, 
  created_at, 
  updated_at
) VALUES (
  'Admin Name',
  'admin@lawconnect.in',
  '$2y$10$...', -- Use password_hash('password', PASSWORD_BCRYPT)
  '9876543210',
  3,
  'active',
  NOW(),
  NOW()
);
```

---

## 🎯 Main Admin Pages

| Page | URL | Purpose |
|------|-----|---------|
| Dashboard | `/admin/dashboard.php` | Overview & statistics |
| Users | `/admin/users.php` | Manage all users |
| Experts | `/admin/experts.php` | Verify & manage experts |
| Sessions | `/admin/sessions.php` | Track consultations |
| Reports | `/admin/reports.php` | Handle user reports |
| Analytics | `/admin/analytics.php` | View analytics |
| Revenue | `/admin/revenue.php` | Revenue tracking |
| Content | `/admin/content.php` | Manage content |
| Settings | `/admin/settings.php` | System settings |
| Logs | `/admin/logs.php` | Audit trail |

---

## 🎨 Features at a Glance

✅ **Dashboard**
- Real-time statistics
- Interactive charts
- Recent activities
- System overview

✅ **User Management**
- Add/Edit/Delete users
- Filter by role and status
- Search functionality
- User detail view

✅ **Expert Management**
- Verification workflow
- Expert profiles
- Rating and specialization tracking
- Session history

✅ **Session Management**
- Track all consultations
- Update session status
- View participant details
- Payment tracking

✅ **Content Moderation**
- Manage user reports
- Review flagged content
- Resolution tracking
- Admin notes

✅ **Analytics**
- Session distribution
- Expert performance
- Specialization breakdown
- Revenue trends

✅ **Revenue Tracking**
- Daily/monthly revenue
- Expert earnings
- Commission calculation
- Top performers

✅ **Settings**
- Platform configuration
- Notification preferences
- Account management

---

## 🚀 Quick Actions

### Add New User
1. Go to Users page
2. Click "Add New User" button
3. Fill in details
4. Select role (Student/Expert/Admin)
5. Save

### Verify Expert
1. Go to Experts page
2. Find expert in pending status
3. Click verify icon
4. Expert becomes active

### Update Session Status
1. Go to Sessions page
2. Click on session row
3. Update status (Confirmed/Cancelled/Completed)
4. Changes saved automatically

### Review Report
1. Go to Reports page
2. Click view icon
3. Review details
4. Take action (approve/delete/warn)

---

## 📊 Dashboard Statistics

**Statistics Displayed:**
- Total Users (all roles)
- Total Experts
- Total Students
- Active Sessions (in progress)
- Pending Sessions
- Total Revenue
- Recent Users List
- Recent Consultations List

**Charts Shown:**
- Session Status Distribution (Doughnut)
- User Distribution by Role (Doughnut)

---

## 🔒 Security & Permissions

✅ **Role-Based Access Control**
- Only users with role = 3 can access admin panel
- Automatic redirect if unauthorized

✅ **Database Security**
- Prepared statements for all queries
- SQL injection prevention
- Input sanitization

✅ **Session Management**
- Automatic logout after inactivity
- Session token validation

---

## 📱 Responsive Design

The admin panel works perfectly on:
- ✅ Desktop (1920px+)
- ✅ Laptop (1024px - 1920px)
- ✅ Tablet (768px - 1024px)
- ✅ Mobile (320px - 768px)

---

## 🛠️ API Endpoints

### User Endpoints
```
GET  /admin/api/get_user.php?id=<user_id>
POST /admin/api/create_user.php
PUT  /admin/api/update_user.php?id=<user_id>
DELETE /admin/api/delete_user.php?id=<user_id>
```

### Expert Endpoints
```
POST /admin/api/verify_expert.php?id=<expert_id>
POST /admin/api/reject_expert.php
```

### Session Endpoints
```
POST /admin/api/update_session.php
```

### Report Endpoints
```
POST /admin/api/update_report.php
```

### Notifications
```
GET /admin/api/get_notifications.php
```

---

## 💾 Database Tables Used

- `users` - All user accounts
- `expert_profiles` - Expert details
- `consultation_sessions` - Bookings
- `content_reports` - User reports
- `data_records` - Content items
- `admin_logs` - Audit trail (optional)

---

## 🎯 Workflow Examples

### Onboarding a New Expert

1. **Expert Signup** → Account created with role=2
2. **Admin Review** → Go to Experts page
3. **Verify Identity** → Click verify icon
4. **Expert Goes Live** → Can accept consultations
5. **Track Performance** → Monitor revenue and ratings

### Handling a Report

1. **User Reports Content** → Report created
2. **Admin Notified** → Notification appears
3. **Review Report** → Click view icon
4. **Investigate Content** → See details
5. **Take Action** → Approve/Delete/Warn
6. **Resolve** → Status updated

---

## ⚙️ Configuration

### Commission Percentage
- Navigate to Settings
- Set commission % for platform
- Default: 20%

### Notification Preferences
- Session alerts
- Expert verification notifications
- Content report alerts

### Timeout Settings
- Session timeout: 30 minutes (configurable)
- Auto-logout on inactivity

---

## 📈 Using Analytics

**Dashboard Charts:**
- Track session types distribution
- Monitor user growth
- Expert specialization breakdown
- Revenue trends

**Revenue Reports:**
- Daily revenue analysis
- Expert performance ranking
- Commission calculations
- Monthly revenue trends

---

## 🔄 Regular Admin Tasks

### Daily
- Check pending sessions
- Review reports
- Monitor revenue

### Weekly
- Verify new experts
- Review analytics
- Check user activity

### Monthly
- Revenue analysis
- Performance reports
- System optimization

---

## ❓ FAQs

**Q: How do I access the admin panel?**
A: Login with admin account (role=3) at login.php, you'll be auto-redirected to dashboard.

**Q: Can I create multiple admins?**
A: Yes! Go to Users page, add new user with role = Admin (3).

**Q: How do I verify experts?**
A: Go to Experts page, find pending expert, click verify icon.

**Q: Where is revenue tracked?**
A: Revenue page shows all payment data, expert earnings, and trends.

**Q: How do I manage reports?**
A: Go to Reports page, click view, review details, take action.

**Q: Can I undo a deletion?**
A: No, deletions are permanent. Be careful with user deletions.

**Q: How do I track admin actions?**
A: Go to Logs page to see audit trail (if admin_logs table exists).

---

## 📞 Support

- **Database Issues:** Check `lib/law.sql`
- **Function Reference:** See `lib/db.php`
- **Styling:** Edit `assets/admin-styles.css`
- **API Issues:** Check individual API files in `admin/api/`

---

## ✨ Admin Panel Highlights

🎨 **Modern Design**
- Clean, professional interface
- Intuitive navigation
- Responsive layout

📊 **Comprehensive Analytics**
- Real-time statistics
- Interactive charts
- Detailed reports

🔐 **Secure**
- Role-based access control
- Session management
- Input validation

⚡ **Fast & Efficient**
- Optimized queries
- Smooth UI interactions
- Quick page loads

🎯 **User-Friendly**
- Clear CTAs
- Intuitive workflows
- Helpful feedback

---

**Status:** ✅ Ready to Use  
**Version:** 1.0.0  
**Last Updated:** April 21, 2026
