# Expert Users Setup Guide

## What Changed:

✅ **Role Column**: Already exists in `users` table (defaults to 'user')  
✅ **Expert Filtering**: Connect page now shows ONLY users with `role = 'expert'`  
✅ **Role Management**: Scripts to manage and verify roles

---

## Setup Steps:

### Step 1: Manage Existing Roles
Run this to ensure existing users have correct roles:
```bash
php lib/manage_roles.php
```

Output will show:
- How many users were updated to 'expert'
- Current role distribution
- List of all expert users

### Step 2: Populate Database with Experts (if needed)
Run this to add expert profiles:
```bash
php lib/seed_experts.php
```

This will:
- Create 8 expert users (all with `role='expert'`)
- Create their profiles
- Mark them as verified and active

### Step 3: Refresh Connect Page
Visit: `http://localhost/law/student/connect.php`

Now you'll see all experts with `role = 'expert'` in the database!

---

## Database Query:

The Connect page now runs this query:
```sql
SELECT * FROM expert_profiles ep
INNER JOIN users u ON ep.user_id = u.id
WHERE ep.verification_status = 'verified' 
  AND u.role = 'expert'
  AND u.status = 'active'
ORDER BY ep.rating DESC
```

This ensures:
- ✅ Only verified experts show
- ✅ Only users with role='expert' show
- ✅ Only active users show
- ✅ Sorted by rating (best first)

---

## User Roles in Database:

| Role | Purpose | Show in Connect? |
|------|---------|-----------------|
| `expert` | Legal experts/advocates | ✅ YES |
| `user` | Regular students/clients | ❌ NO |
| `admin` | Platform admins | ❌ NO |
| `lawyer` | (alternative, not used) | ❌ NO |

---

## Checking Current Setup:

### See all expert users:
```sql
SELECT id, email, full_name, role 
FROM users 
WHERE role = 'expert'
ORDER BY id;
```

### See role distribution:
```sql
SELECT role, COUNT(*) as count 
FROM users 
GROUP BY role;
```

### Verify expert profiles:
```sql
SELECT u.full_name, u.role, ep.specialization, ep.verification_status
FROM expert_profiles ep
JOIN users u ON ep.user_id = u.id
WHERE u.role = 'expert';
```

---

## Next: Adding More Experts Manually

To manually add an expert:

### 1. Create User Account:
```sql
INSERT INTO users 
(email, password, full_name, name, role, phone, bio, status)
VALUES 
('adv.name@example.com', '[hashed_password]', 'Adv. Name', 'Adv. Name', 'expert', '9876543210', 'Bio here', 'active');
```

### 2. Create Expert Profile:
```sql
INSERT INTO expert_profiles 
(user_id, specialization, experience_years, hourly_rate, verification_status)
VALUES 
([user_id], 'Family Law', 10, 800, 'verified');
```

---

## Troubleshooting:

### "No experts showing in Connect page"
1. Run: `php lib/manage_roles.php` to check roles
2. Run: `php lib/seed_experts.php` to add experts
3. Verify: `SELECT * FROM users WHERE role = 'expert'`

### "Experts showing but marked as busy"
Check availability_status in expert_profiles:
```sql
SELECT specialization, availability_status 
FROM expert_profiles 
WHERE user_id = [id];
```

### "Wrong role showing"
Update manually:
```sql
UPDATE users SET role = 'expert' 
WHERE email = 'expert@example.com';
```

