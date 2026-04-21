# Database Setup Guide

## Problem: Dummy data showing instead of real data

## Solution: Populate Database with Expert Profiles

### Step 1: Open Terminal/Command Prompt

Navigate to your project directory:
```bash
cd c:\Users\varun\OneDrive\Desktop\law
```

### Step 2: Run the Seed Script

Run this command to populate the database with expert profiles:
```bash
php lib/seed_experts.php
```

You should see output like:
```
✓ Created user: Adv. Priya Sharma (ID: 1)
✓ Created expert profile
✓ Created user: Adv. Rahul Verma (ID: 2)
✓ Created expert profile
...
✓ Database populated successfully!
Total experts added: 8
```

### Step 3: Refresh Your Browser

Go to the Connect page and refresh:
```
http://localhost/law/student/connect.php
```

Now you should see real expert data from the database!

---

## What the Script Does:

1. **Creates User Accounts** for 8 expert advocates with proper credentials
2. **Creates Expert Profiles** with:
   - Specialization (Family Law, Criminal Law, etc.)
   - Experience years
   - Hourly rates
   - Ratings and review counts
   - Availability status
   - Pro bono participation flag

3. **Marks All as Verified** so they appear in the student's view

---

## Expert Data Included:

1. **Adv. Priya Sharma** - Family Law (₹800/hr)
2. **Adv. Rahul Verma** - Criminal Law (₹1200/hr)
3. **Adv. Anjali Nair** - Property Law (₹900/hr)
4. **Adv. Suresh Patel** - Corporate Law (₹1500/hr)
5. **Adv. Meera Joshi** - Consumer Law (₹600/hr)
6. **Adv. Karthik Rajan** - Labour Law (₹700/hr)
7. **Adv. Deepa Choudhary** - Civil Law (₹1100/hr)
8. **Adv. Arun Mishra** - Criminal Law (₹650/hr)

---

## Troubleshooting:

### If you see "Database Connection Error":
- Check if your MySQL server is running
- Verify credentials in `lib/db.php`:
  - Host: localhost
  - Port: 3306
  - Database: law
  - Username: law
  - Password: law

### If you see "No Experts Available":
- The database exists but has no experts
- Run the seed script again using the command above

### If table structure is missing:
- Import the schema from `lib/law.sql`:
  ```bash
  mysql -u law -p law < lib/law.sql
  ```
  (Password: law)

---

## Expert Login Credentials:

All experts can login with:
- **Password**: expert123
- **Email**: firstname.lastname@lawconnectors.in

For example:
- Email: priya.sharma@lawconnectors.in
- Email: rahul.verma@lawconnectors.in

---

## Next Steps:

After populating the database:
1. Experts can login and view bookings
2. Students can search, filter, and book sessions
3. Sessions get saved to `consultation_sessions` table
4. Experts can approve/reject bookings

