# Role-Based Access Control Setup Guide

## Overview
This guide explains how to implement the numeric role system (1=Student, 2=Expert, 3=Admin) with automatic redirects to different folder index pages based on user role.

## Role System

### Numeric Role Values
- **Role 1**: Student (Default) → Redirects to `student/mainhome.php`
- **Role 2**: Expert → Redirects to `expert/newpage.php`
- **Role 3**: Administrator → Redirects to `admin/1newpage.php`

## Implementation Steps

### Step 1: Convert Existing Roles to Numeric Format

Run the conversion script from the terminal:

```bash
php lib/convert_roles.php
```

This script will:
- Convert all string roles ('user', 'expert') to numeric (1, 2, 3)
- Set default role (1) for any NULL values
- Display current role distribution
- Show which users are experts, admins, and students

### Step 2: Update Database Schema (Optional)

If you want to enforce numeric roles in the database, run this SQL:

```sql
ALTER TABLE users MODIFY COLUMN role INT DEFAULT 1;
```

This changes the role column from VARCHAR to INT with default value of 1.

### Step 3: Code Implementation

#### 3.1 Role Constants (db.php)
Already added to `lib/db.php`:
```php
define('ROLE_STUDENT', 1);  // Student/User (default)
define('ROLE_EXPERT', 2);   // Legal Expert
define('ROLE_ADMIN', 3);    // Administrator
```

#### 3.2 Role Helper Functions (db.php)
Three new functions are available:

**requireRole($requiredRole)**
- Requires user to be logged in AND have specific role
- Usage: `$user = requireRole(ROLE_EXPERT);` in expert pages
- Denies access with 403 error if user doesn't have required role

**getRoleName($role)**
- Returns human-readable role name
- Usage: `echo getRoleName(2);` → outputs "Expert"

**getRedirectUrlByRole($role)**
- Returns the redirect URL based on role
- Automatically called after login
- Usage: `$url = getRedirectUrlByRole($role);`

#### 3.3 Automatic Redirects (index.php)
Already implemented in `index.php`:
```php
if (isLoggedIn()) {
    $role = intval($_SESSION['role'] ?? 1);
    if ($role === 2)      header('Location: expert/newpage.php');
    elseif ($role === 3)  header('Location: admin/1newpage.php');
    else                  header('Location: student/mainhome.php');
    exit;
}
```

#### 3.4 Login Process (lib/login.php)
Updated to ensure numeric roles:
```php
$_SESSION['role'] = (int)($user['role'] ?? ROLE_STUDENT);
```

#### 3.5 Signup Process (lib/signup_api.php)
Already supports numeric roles:
- Accepts `type: 1, 2, or 3`
- Defaults to role 1 (Student)
- Stores as numeric value

### Step 4: Update Protected Pages

To protect expert-only pages, add this at the top:

```php
<?php
require_once '../lib/db.php';
$user = requireRole(ROLE_EXPERT); // Only experts can access
?>
```

For admin pages:
```php
<?php
require_once '../lib/db.php';
$user = requireRole(ROLE_ADMIN); // Only admins can access
?>
```

For pages accessible to authenticated students:
```php
<?php
require_once '../lib/db.php';
$user = requireAuth(); // Any logged-in user
$role = $user['role']; // Can still check role if needed
?>
```

### Step 5: Create Seed Data (Optional)

To populate with test experts, run:

```bash
php lib/seed_experts.php
```

This creates 8 test expert users with:
- Role = 2 (Expert)
- Verified profiles
- Various specializations
- Hourly rates and ratings
- Contact information

### Step 6: Manage User Roles (After Setup)

To audit and fix user roles, run:

```bash
php lib/manage_roles.php
```

This script:
- Updates users with expert profiles to role=2
- Sets students/default users to role=1
- Shows current role distribution
- Lists all experts and their specializations

## Usage Examples

### Admin Checking User Role
```php
<?php
require_once 'lib/db.php';

$user = getCurrentUser();
if ($user['role'] == ROLE_EXPERT) {
    echo "User is an expert";
} else if ($user['role'] == ROLE_STUDENT) {
    echo "User is a student";
} else if ($user['role'] == ROLE_ADMIN) {
    echo "User is an admin";
}
?>
```

### Protecting Expert Page
```php
<?php
require_once '../lib/db.php';

// This will redirect non-experts back to login
$user = requireRole(ROLE_EXPERT);

// Now safely display expert-only content
echo "Welcome, Expert {$user['full_name']}!";
?>
```

### Database Queries Filtering by Role
```php
// Get all experts
$stmt = $pdo->prepare("
    SELECT * FROM users 
    WHERE role = " . ROLE_EXPERT . "
    ORDER BY full_name
");

// Get students
$stmt = $pdo->prepare("
    SELECT * FROM users 
    WHERE role = " . ROLE_STUDENT . "
    ORDER BY full_name
");
```

### Checking Role in Frontend (After Login)
```javascript
// After login API call returns user object
const user = response.data.user;

if (user.role === 1) {
    console.log("Redirect to student area");
} else if (user.role === 2) {
    console.log("Redirect to expert area");
} else if (user.role === 3) {
    console.log("Redirect to admin area");
}
```

## Folder Structure with Role-Based Access

```
/law/
├── index.php                 ← Redirects based on role after login
├── login.php                 ← Stores numeric role in session
├── /lib/
│   ├── db.php               ← Role constants and helper functions
│   ├── login.php            ← API endpoint for authentication
│   ├── signup_api.php       ← New user registration (defaults to role 1)
│   ├── convert_roles.php    ← Migrate to numeric roles
│   ├── manage_roles.php     ← Audit and fix roles
│   ├── seed_experts.php     ← Create test experts (role 2)
│   └── ... (other files)
├── /student/                ← Role 1 (ROLE_STUDENT)
│   ├── mainhome.php         ← Default landing page for students
│   ├── connect.php          ← Updated to filter experts by role=2
│   └── ... (student pages)
├── /expert/                 ← Role 2 (ROLE_EXPERT)
│   ├── newpage.php          ← Landing page for experts
│   └── ... (expert pages)
└── /admin/                  ← Role 3 (ROLE_ADMIN)
    ├── 1newpage.php         ← Landing page for admins
    └── ... (admin pages)
```

## Verification Checklist

- [ ] Run `php lib/convert_roles.php` to convert all roles
- [ ] Verify role distribution output shows numeric values
- [ ] Test login with expert account → Should redirect to expert/newpage.php
- [ ] Test login with student account → Should redirect to student/mainhome.php
- [ ] Test login with admin account → Should redirect to admin/1newpage.php
- [ ] Test new signup → New users should default to role 1
- [ ] Check `lib/seed_experts.php` creates users with role 2
- [ ] Verify `student/connect.php` displays experts with `role = 2` filter

## Troubleshooting

**Issue**: Users still see old string roles in database
- **Solution**: Run `php lib/convert_roles.php`

**Issue**: Login doesn't redirect to correct page
- **Solution**: Check `$_SESSION['role']` is numeric in browser console

**Issue**: Expert page shows "Access Denied"
- **Solution**: Ensure user has role=2 in database, run `php lib/manage_roles.php`

**Issue**: New signups create users with wrong role
- **Solution**: Check `lib/signup_api.php` is using `type` parameter correctly

## Key Files Modified

1. **lib/db.php**
   - Added role constants (ROLE_STUDENT, ROLE_EXPERT, ROLE_ADMIN)
   - Added requireRole() function
   - Added getRoleName() function
   - Added getRedirectUrlByRole() function

2. **lib/login.php**
   - Ensures $_SESSION['role'] is stored as numeric

3. **lib/signup_api.php**
   - Ensures numeric role defaults to 1 for new users

4. **student/connect.php**
   - Updated expert query to filter by `role = 2` instead of `role = 'expert'`

5. **lib/seed_experts.php**
   - Creates experts with `role = 2`

6. **lib/manage_roles.php**
   - Updated to use numeric roles (2, 1, 3)

7. **index.php**
   - Already has redirect logic based on role

## Next Steps

1. Convert all existing roles using `convert_roles.php`
2. Protect expert/admin pages with `requireRole()` function
3. Test complete login flow
4. Monitor for any string role references in code that need updating

