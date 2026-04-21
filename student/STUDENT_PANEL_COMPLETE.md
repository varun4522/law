# LawConnect - Student Panel Documentation

## Overview

The Student Panel is a fully functional, modern web application that allows students to:
- Browse and connect with verified legal experts
- Book consultation sessions with experts
- Read comprehensive law-related articles and guides
- Track consultation history and ratings
- Manage their profile and preferences
- Participate in community discussions

---

## Features

### 1. Dashboard (`dashboard.php`)
- **Overview Statistics**: Total sessions, completed sessions, pending sessions, average rating
- **Upcoming Sessions**: List of upcoming consultations with expert details
- **Top Rated Experts**: Browse and connect with highest-rated legal professionals
- **Recent Articles**: Latest law-related content for learning
- **Quick Tips**: Best practices for using the platform

### 2. Expert Directory (`experts.php`)
- **Advanced Filtering**: By specialization, rating, experience level
- **Search Functionality**: Find experts by name or specialization
- **Sorting Options**: By rating, price, experience
- **Expert Cards**: Complete information including rate, rating, and session count
- **Pagination**: Efficient browsing through large expert lists

### 3. Expert Profile (`expert-profile.php`)
- **Complete Expert Information**: Bio, qualifications, specialization
- **Reviews & Ratings**: Real feedback from previous students
- **Session History**: Number of completed consultations
- **Availability Status**: Communication methods (video, phone, chat)
- **Direct Booking**: One-click session booking

### 4. Article System (`articles.php` & `article.php`)
- **Knowledge Base**: Comprehensive articles on legal topics
- **Categorization**: Articles sorted by type (guides, tutorials, case studies, news)
- **Search & Filter**: Find articles by keyword and category
- **Read Time Indicator**: Estimated reading duration
- **View Counter**: Track popular articles
- **Social Sharing**: Share articles on social media

### 5. Session Management (`sessions.php`)
- **Session History**: All consultation records with status tracking
- **Status Filters**: View sessions by status (pending, confirmed, in-progress, completed, cancelled)
- **Session Details**: Date, time, expert, amount, and status
- **Rating System**: Rate completed sessions and leave reviews
- **Status Summary**: Quick overview of session counts by status

### 6. Session Booking (`book-session.php`)
- **Expert Selection**: Choose from verified experts
- **Date & Time Selection**: Pick preferred consultation time
- **Duration Options**: 30 min, 45 min, 1 hr, 1.5 hrs, 2 hrs
- **Topic Description**: Explain your legal issue
- **Communication Type**: Video call, phone, or chat
- **Real-time Pricing**: Automatic calculation with commission breakdown
- **Payment Methods**: Multiple payment options (card, UPI, wallet)

### 7. Profile Management (`profile.php`)
- **Personal Information**: Edit full name, email, phone, gender
- **Address Management**: Update city, state, pincode
- **Password Security**: Change password with current password verification
- **Account Activity**: View last login time
- **Preferences**: Learning and notification settings

### 8. Community Forum (`community.php`)
- **Discussion Board**: Ask questions and share experiences
- **Post Creation**: Create topics for community discussion
- **Engagement**: Like, comment, and share posts
- **Topic Categories**: Organize discussions by legal area

---

## File Structure

```
student/
├── dashboard.php              # Main dashboard
├── experts.php               # Expert directory with filters
├── expert-profile.php        # Individual expert profile
├── articles.php              # Article list with search/filter
├── article.php               # Individual article view
├── sessions.php              # Student's consultation sessions
├── book-session.php          # Booking form and checkout
├── profile.php               # Student profile settings
├── community.php             # Community forum
└── api/
    ├── search.php           # Search experts and articles
    ├── book-session.php     # Book session endpoint
    └── rate-session.php     # Rate completed session

assets/
├── student-styles.css       # Comprehensive student styling
└── student-common.js        # Shared JavaScript functions
```

---

## Styling

### Design System
- **Primary Color**: #3b82f6 (Blue)
- **Secondary Color**: #8b5cf6 (Purple)
- **Success Color**: #10b981 (Green)
- **Warning Color**: #f59e0b (Orange)
- **Danger Color**: #ef4444 (Red)
- **Border Radius**: 8px (Consistent rounded corners)
- **Font**: Inter (system font fallback)

### Responsive Breakpoints
- **Desktop**: 1200px and above (2-column layout with sidebar)
- **Tablet**: 768px - 1199px (Flexible grid)
- **Mobile**: 480px - 767px (Single column)
- **Small Mobile**: Below 480px (Optimized for small screens)

### Key Components
- **Navbar**: Sticky navigation with user menu
- **Sidebar**: Quick access navigation
- **Cards**: Reusable content containers
- **Buttons**: Primary, secondary, small, large variants
- **Tables**: Responsive consultation history
- **Modals**: Search, rating, and post creation overlays
- **Forms**: Input validation with focus states
- **Alerts**: Success, error, and info notifications

---

## JavaScript Features

### Core Utilities
- **User Menu Toggle**: Dropdown menu management
- **Search Modal**: Advanced search functionality
- **Form Validation**: Client-side input validation
- **API Calls**: Centralized API communication
- **Notifications**: Toast-style alerts
- **Date/Currency Formatting**: Locale-aware formatting
- **Lazy Loading**: Image optimization for performance
- **Debounce/Throttle**: Performance optimization
- **Online Status Monitoring**: Detect connectivity

### Interactive Features
- **Tab Switching**: Content navigation
- **Rating System**: Interactive star rating
- **Modal Management**: Overlay dialogs
- **Smooth Scrolling**: Enhanced navigation experience
- **Copy to Clipboard**: Quick reference copying

---

## API Endpoints

### Search (`/student/api/search.php`)
**Method**: GET
**Parameters**: 
- `q` - Search query string

**Response**:
```json
{
  "success": true,
  "results": [
    {
      "title": "Expert Name - Specialization",
      "type": "Expert",
      "icon": "user-tie",
      "url": "expert-profile.php?id=1"
    }
  ]
}
```

### Book Session (`/student/api/book-session.php`)
**Method**: POST
**Body**:
```json
{
  "expert_id": 1,
  "session_date": "2026-03-15",
  "session_time": "10:00",
  "duration": 60,
  "topic": "Legal question description",
  "preferences": "Optional notes",
  "communication_type": "video",
  "payment_method": "card"
}
```

### Rate Session (`/student/api/rate-session.php`)
**Method**: POST
**Body**:
```json
{
  "session_id": 1,
  "rating": 5,
  "review": "Excellent consultation"
}
```

---

## Database Integration

### Key Tables Used
- **users**: Student account information
- **expert_profiles**: Expert qualifications and ratings
- **consultation_sessions**: Booking and session records
- **data_records**: Articles and learning content

### Relationships
```
students → consultation_sessions ← experts
students → data_records
experts → expert_profiles
```

---

## Security Features

✅ **Password Security**: bcrypt hashing for passwords
✅ **SQL Injection Prevention**: Prepared statements in all queries
✅ **Input Validation**: Sanitization and trim on user inputs
✅ **Role-Based Access**: Student role verification on all pages
✅ **Session Management**: Server-side session authentication
✅ **CSRF Protection**: Ready for token implementation
✅ **XSS Prevention**: htmlspecialchars() for output encoding

---

## Performance Optimizations

- **CSS Minification**: Optimized stylesheet (1200+ lines)
- **Lazy Loading**: Images load on scroll
- **Debounced Search**: Reduces API calls during search
- **Pagination**: Limits records per page (10-15 items)
- **Caching**: Static asset caching ready
- **Responsive Images**: Mobile-optimized delivery
- **Font Loading**: System fonts with web font fallback

---

## User Experience Features

### Navigation
- Sticky top navbar for quick access
- Persistent sidebar for quick navigation
- Breadcrumb "Back" links on detail pages
- Quick action buttons throughout

### Feedback
- Toast notifications for actions
- Loading states for async operations
- Empty states with helpful messaging
- Progress indicators
- Success/error messages

### Accessibility
- Semantic HTML structure
- ARIA labels for screen readers
- Keyboard navigation support
- High contrast colors (WCAG AA compliant)
- Focus states on interactive elements

---

## Future Enhancements

🔄 **Phase 2 - Community Features**
- Full post creation and commenting
- User profiles and following system
- Notifications for community activity

🔄 **Phase 3 - AI Integration**
- AI-powered legal information system
- Smart legal document analysis
- Chatbot for common questions

🔄 **Phase 4 - Advanced Features**
- Video conferencing integration
- Payment gateway integration
- Document storage and sharing
- Advanced analytics dashboard
- Mobile app

---

## Getting Started

### Prerequisites
- PHP 7.4+
- MySQL 5.7+
- Modern web browser

### Setup
1. Ensure database tables are created (see DATABASE_SETUP.md)
2. Verify users table has role-based access control
3. Check expert_profiles table has verification_status
4. Confirm consultation_sessions table exists

### Access
Students access the panel via:
- Dashboard: `/student/dashboard.php`
- Expert Directory: `/student/experts.php`
- Articles: `/student/articles.php`
- Community: `/student/community.php`

### Testing
- Test expert filtering and search
- Create sample article records
- Book test sessions
- Rate completed sessions
- Verify all payment methods

---

## Support & Maintenance

### Common Issues
- **Sessions not appearing**: Check consultation_sessions table
- **Expert ratings incorrect**: Recalculate averages in expert_profiles
- **Styling not loading**: Verify CSS file path in assets/

### Admin Panel
For admin operations, see ADMIN_PANEL_COMPLETE.md

### Database Queries
- Scheduled maintenance queries for old sessions
- Archive completed sessions monthly
- Recalculate expert ratings weekly

---

## Version Info

- **Version**: 1.0
- **Last Updated**: April 2026
- **Status**: Production Ready
- **Platform**: Web (Responsive Design)

---

**Created for Law Connectors - Connecting People with Legal Expertise**
