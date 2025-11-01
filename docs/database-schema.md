# Database Schema Documentation

This document provides a comprehensive overview of the Tom's Music School LMS database schema, migrated from Symfony 6.3 to Laravel 11.x.

**Document Version**: 1.0  
**Last Updated**: [Current Date]  
**Database Engine**: MySQL 8.0  
**Charset**: utf8mb4  
**Collation**: utf8mb4_unicode_ci

---

## Table Overview

The database consists of the following tables:

### Core Entities
1. **user** - Users (students, teachers, admins)
2. **course** - Music courses
3. **course_category** / **course_categories** - Course categories
4. **lesson** - Individual lessons within courses
5. **session** - Live sessions with Google Meet integration
6. **enrollment** - Student-course relationships
7. **assignment** - Coursework assignments
8. **assignment_submission** - Student assignment submissions
9. **quiz** - Interactive quizzes
10. **quiz_attempt** - Student quiz attempts
11. **order** - Payment transactions
12. **certificate** - Course completion certificates
13. **comment** - Comments on submissions
14. **note** - User notes on lessons
15. **oauth_credential** - OAuth token storage

### Supporting Tables
16. **stored_file** - File metadata tracking
17. **course_offerings** - Recurring course offerings
18. **session_package** - Session packages for students
19. **session_bookings** - Individual session bookings
20. **session_chat** - Session chat messages
21. **teacher_availability** - Teacher availability schedules
22. **subscription_payment** - Subscription payment records

### System Tables
23. **rate_limit_attempts** - Rate limiting tracking
24. **security_events** - Security event logging
25. **security_alerts** - Security alert management

---

## Table Details

### 1. user

Central user table storing all user information including students, teachers, and admins.

**Primary Key**: `id` (INT AUTO_INCREMENT)

**Columns**:
- `id` - User ID (INT, PRIMARY KEY, AUTO_INCREMENT)
- `email` - Email address (VARCHAR(180), UNIQUE, NOT NULL)
- `roles` - User roles (JSON, NOT NULL) - e.g., ["ROLE_USER", "ROLE_TEACHER"]
- `password` - Hashed password (VARCHAR(255), NULLABLE)
- `first_name` - First name (VARCHAR(100), NULLABLE)
- `last_name` - Last name (VARCHAR(100), NULLABLE)
- `phone` - Phone number (VARCHAR(20), NULLABLE)
- `date_of_birth` - Date of birth (DATE, NULLABLE)
- `instrument` - Musical instrument (VARCHAR(100), NULLABLE)
- `skill_level` - Skill level (VARCHAR(50), NULLABLE)
- `bio` - Biography (LONGTEXT, NULLABLE)
- `profile_picture` - Profile picture URL (VARCHAR(255), NULLABLE)
- `city` - City (VARCHAR(100), NULLABLE)
- `country` - Country (VARCHAR(100), NULLABLE)
- `timezone` - Timezone (VARCHAR(100), NULLABLE)
- `preferences` - User preferences (JSON, NULLABLE)
- `created_at` - Creation timestamp (DATETIME, NOT NULL)
- `last_login_at` - Last login timestamp (DATETIME, NULLABLE)
- `is_active` - Active status (TINYINT(1), DEFAULT 1)
- `email_verified` - Email verification status (TINYINT(1), DEFAULT 0)
- `google_id` - Google OAuth ID (VARCHAR(255), NULLABLE)
- `apple_id` - Apple OAuth ID (VARCHAR(255), NULLABLE)
- `facebook_id` - Facebook OAuth ID (VARCHAR(255), NULLABLE)
- `experience_points` - Experience points (INT, DEFAULT 0)
- `level` - User level (INT, DEFAULT 1)
- `achievements` - Achievements (JSON, NULLABLE)
- `badges` - Badges (JSON, NULLABLE)
- `rating` - User rating (DECIMAL(5,2), NULLABLE)
- `total_lessons` - Total lessons (INT, DEFAULT 0)
- `completed_lessons` - Completed lessons (INT, DEFAULT 0)
- `practice_hours` - Practice hours (INT, DEFAULT 0)
- `last_practice_at` - Last practice timestamp (DATETIME, NULLABLE)
- `learning_goals` - Learning goals (JSON, NULLABLE)
- `progress_data` - Progress data (JSON, NULLABLE)
- `notes` - Notes (LONGTEXT, NULLABLE)
- `is_teacher` - Is teacher flag (TINYINT(1), DEFAULT 0)
- `teacher_bio` - Teacher biography (LONGTEXT, NULLABLE)
- `teacher_specialties` - Teacher specialties (JSON, NULLABLE)
- `teacher_certifications` - Teacher certifications (JSON, NULLABLE)
- `hourly_rate` - Hourly rate (DECIMAL(5,2), NULLABLE)
- `availability` - Availability schedule (JSON, NULLABLE)
- `student_reviews` - Student reviews (JSON, NULLABLE)
- `total_students` - Total students (INT, DEFAULT 0)
- `active_students` - Active students (INT, DEFAULT 0)
- `failed_login_attempts` - Failed login attempts (INT, DEFAULT 0)
- `last_failed_login_at` - Last failed login timestamp (DATETIME, NULLABLE)
- `last_failed_login_ip` - Last failed login IP (VARCHAR(45), NULLABLE)
- `is_locked` - Account locked flag (TINYINT(1), DEFAULT 0)
- `locked_until` - Locked until timestamp (DATETIME, NULLABLE)

**Indexes**:
- PRIMARY KEY (`id`)
- UNIQUE INDEX (`email`)

**Relationships**:
- Has many `courses` (as teacher)
- Has many `enrollments` (as student)
- Has many `sessions` (as tutor)
- Has many `assignments` (through relationships)
- Has many `assignment_submissions` (as student)
- Has many `quiz_attempts` (as student)
- Has many `orders`
- Has many `certificates`
- Has many `notes`
- Has many `oauth_credentials`

**Notes**:
- Laravel will use `created_at` and `updated_at` instead of `created_at` only
- Symfony schema uses `created_at` only, Laravel needs both timestamps
- `roles` is stored as JSON array
- Many columns are teacher-specific or student-specific

---

### 2. course_category / course_categories

Course category/categories tables (may exist in different forms in different schemas).

**Primary Key**: `id`

**Columns** (course_category):
- `id` - Category ID (INT, PRIMARY KEY, AUTO_INCREMENT)
- `name` - Category name (VARCHAR(100), NOT NULL)
- `slug` - URL slug (VARCHAR(120), NOT NULL)
- `description` - Description (TEXT, NULLABLE)

**Columns** (course_categories - more complete):
- `id` - Category ID (BIGINT UNSIGNED, PRIMARY KEY, AUTO_INCREMENT)
- `name` - Category name (VARCHAR(100), NOT NULL)
- `slug` - URL slug (VARCHAR(200), UNIQUE, NOT NULL)
- `description` - Description (TEXT, NULLABLE)
- `image_url` - Image URL (VARCHAR(500), NULLABLE)
- `color` - Color code (VARCHAR(7), NULLABLE)
- `sort_order` - Sort order (INT, DEFAULT 0)
- `is_active` - Active status (TINYINT(1), DEFAULT 1)
- `created_at` - Creation timestamp (DATETIME, NOT NULL)
- `updated_at` - Update timestamp (DATETIME, NOT NULL)

**Indexes**:
- PRIMARY KEY (`id`)
- UNIQUE INDEX (`slug`) - in course_categories
- INDEX (`is_active`) - in course_categories
- INDEX (`sort_order`) - in course_categories

**Relationships**:
- Has many `courses`

---

### 3. stored_file

File metadata tracking table.

**Primary Key**: `id`

**Columns**:
- `id` - File ID (INT, PRIMARY KEY, AUTO_INCREMENT)
- `original_filename` - Original filename (VARCHAR(255), NOT NULL)
- `stored_path` - Storage path (VARCHAR(500), NOT NULL)
- `mime_type` - MIME type (VARCHAR(100), NOT NULL)
- `file_size` - File size in bytes (INT, NOT NULL)
- `uploader_id` - Uploader user ID (INT, NOT NULL)
- `created_at` - Creation timestamp (DATETIME, NOT NULL)

**Indexes**:
- PRIMARY KEY (`id`)

**Relationships**:
- Belongs to `user` (uploader_id)

---

### 4. course

Music course table.

**Primary Key**: `id` (BIGINT or INT depending on schema)

**Columns**:
- `id` - Course ID (BIGINT AUTO_INCREMENT or INT, PRIMARY KEY)
- `teacher_id` - Teacher user ID (INT or BIGINT UNSIGNED, NOT NULL)
- `category_id` - Category ID (INT or BIGINT UNSIGNED, NOT NULL)
- `cover_file_id` - Cover file ID (INT or BIGINT UNSIGNED, NULLABLE)
- `title` - Course title (VARCHAR(200) or VARCHAR(255), NOT NULL)
- `slug` - URL slug (VARCHAR(220), NOT NULL, may have UNIQUE constraint)
- `description` - Description (TEXT or LONGTEXT, NOT NULL)
- `instrument` - Instrument (VARCHAR(100), DEFAULT 'other')
- `level` - Level (VARCHAR(50), DEFAULT 'beginner')
- `price_cents` - Price in cents (INT, DEFAULT 0)
- `status` - Status (VARCHAR(20), DEFAULT 'draft')
- `published_at_utc` - Published timestamp (DATETIME(6) or DATETIME, NULLABLE)
- `created_at_utc` - Creation timestamp (DATETIME(6) or DATETIME, NOT NULL)
- `updated_at_utc` - Update timestamp (DATETIME(6) or DATETIME, NOT NULL)
- `thumbnail` - Thumbnail URL (VARCHAR(255), NULLABLE) - may exist
- `tags` - Tags (JSON, NULLABLE) - may exist
- `total_lessons` - Total lessons count (INT, DEFAULT 0) - may exist
- `total_duration` - Total duration in minutes (INT, DEFAULT 0) - may exist

**Indexes**:
- PRIMARY KEY (`id`)
- INDEX (`teacher_id`)
- INDEX (`status`)
- UNIQUE INDEX (`slug`) - may exist

**Relationships**:
- Belongs to `user` (teacher_id)
- Belongs to `course_category` (category_id)
- Belongs to `stored_file` (cover_file_id) - nullable
- Has many `lessons`
- Has many `sessions`
- Has many `enrollments`
- Has many `assignments`
- Has many `orders`

**Notes**:
- Laravel will use `created_at` and `updated_at` instead of `created_at_utc` and `updated_at_utc`
- `published_at` will be used instead of `published_at_utc`

---

### 5. lesson

Individual lesson table.

**Primary Key**: `id` (BIGINT or INT depending on schema)

**Columns**:
- `id` - Lesson ID (BIGINT AUTO_INCREMENT or INT, PRIMARY KEY)
- `course_id` - Course ID (BIGINT or INT, NOT NULL)
- `title` - Lesson title (VARCHAR(200) or VARCHAR(255), NOT NULL)
- `description` - Description (TEXT, NULLABLE) - may not exist
- `order_index` - Order within course (INT, NOT NULL)
- `video_url` - Video URL (VARCHAR(500), NULLABLE) - may not exist
- `materials_json` - Materials (JSON, NULLABLE) - may not exist
- `content_html` - HTML content (LONGTEXT, NOT NULL) - may exist
- `duration_min` - Duration in minutes (INT, NOT NULL or DEFAULT NULL)
- `resources` / `resources_json` - Resources (JSON, NULLABLE)
- `created_at_utc` / `created_at` - Creation timestamp (DATETIME or VARCHAR(255), NOT NULL)
- `updated_at_utc` - Update timestamp (DATETIME, NOT NULL) - may not exist
- `summary` - Summary (LONGTEXT, NULLABLE) - may exist
- `learning_objectives` - Learning objectives (JSON, NULLABLE) - may exist
- `is_required` - Required flag (TINYINT(1), DEFAULT 0) - may exist

**Indexes**:
- PRIMARY KEY (`id`)
- INDEX (`course_id`)
- UNIQUE INDEX (`course_id`, `order_index`) - may exist

**Relationships**:
- Belongs to `course` (course_id)
- Has many `sessions` - nullable
- Has many `assignments` - nullable
- Has many `quizzes`
- Has many `notes`

**Notes**:
- Schema variations exist - need to consolidate
- Laravel will use `created_at` and `updated_at`

---

### 6. session

Live session table with Google Meet integration.

**Primary Key**: `id` (BIGINT or INT)

**Columns**:
- `id` - Session ID (BIGINT AUTO_INCREMENT or INT, PRIMARY KEY)
- `course_id` - Course ID (BIGINT or INT, NOT NULL)
- `lesson_id` - Lesson ID (BIGINT or INT, NULLABLE)
- `tutor_id` - Tutor user ID (INT or BIGINT UNSIGNED, NOT NULL)
- `offering_id` - Course offering ID (INT, NULLABLE)
- `start_at_utc` / `start_at` - Start timestamp (DATETIME(6) or VARCHAR(255), NOT NULL)
- `end_at_utc` / `end_at` - End timestamp (DATETIME(6) or VARCHAR(255), NOT NULL)
- `join_url` - Join URL (VARCHAR(500), NULLABLE)
- `google_meet_link` - Google Meet link (VARCHAR(500), NULLABLE) - may exist
- `google_event_id` - Google Calendar event ID (VARCHAR(128) or VARCHAR(255), NULLABLE)
- `materials_json` / `materials` - Materials (JSON, NULLABLE)
- `recording_url` - Recording URL (VARCHAR(500), NULLABLE)
- `created_at_utc` / `created_at` - Creation timestamp (DATETIME or VARCHAR(255), NOT NULL)
- `updated_at_utc` / `updated_at` - Update timestamp (DATETIME or VARCHAR(255), NOT NULL)
- `notes` - Notes (LONGTEXT, NULLABLE) - may exist
- `status` - Status (VARCHAR(50), DEFAULT 'scheduled') - may exist
- `max_students` - Max students (INT, DEFAULT 0) - may exist

**Indexes**:
- PRIMARY KEY (`id`)
- INDEX (`course_id`, `start_at_utc`)
- INDEX (`tutor_id`, `start_at_utc`)
- INDEX (`offering_id`)
- INDEX (`lesson_id`) - may exist
- INDEX (`tutor_id`) - may exist

**Relationships**:
- Belongs to `course` (course_id)
- Belongs to `lesson` (lesson_id) - nullable
- Belongs to `user` (tutor_id)
- Belongs to `course_offerings` (offering_id) - nullable
- Has many `session_chat` messages
- Has many students (pivot table) - may exist

**Notes**:
- Schema variations exist
- Laravel will use `created_at` and `updated_at`
- Google Meet integration columns important

---

### 7. enrollment

Student-course enrollment table.

**Primary Key**: `id` (BIGINT or INT)

**Columns**:
- `id` - Enrollment ID (BIGINT AUTO_INCREMENT or INT, PRIMARY KEY)
- `student_id` - Student user ID (INT or BIGINT UNSIGNED, NOT NULL)
- `course_id` - Course ID (BIGINT or INT, NOT NULL)
- `offering_id` - Course offering ID (INT, NULLABLE) - may exist
- `enrolled_at` / `started_at` / `created_at` - Enrollment timestamp (DATETIME or VARCHAR(255), NOT NULL)
- `status` - Status (VARCHAR(50) or VARCHAR(20), NOT NULL)
- `completed_at` - Completion timestamp (DATETIME, NULLABLE)
- `progress_pct` - Progress percentage (DECIMAL(5,2), DEFAULT 0.00) - may exist
- `last_accessed_at` - Last access timestamp (DATETIME, NULLABLE) - may exist
- `lessons_completed` - Lessons completed count (INT, DEFAULT 0)
- `total_lessons` - Total lessons count (INT, DEFAULT 0)
- `completed_lessons` - Completed lessons IDs (JSON, NULLABLE)
- `quiz_scores` - Quiz scores (JSON, NULLABLE)
- `assignment_scores` - Assignment scores (JSON, NULLABLE)
- `notes` - Notes (LONGTEXT, NULLABLE)

**Indexes**:
- PRIMARY KEY (`id`)
- INDEX (`student_id`)
- INDEX (`course_id`)
- INDEX (`offering_id`) - may exist
- UNIQUE INDEX (`student_id`, `course_id`)
- UNIQUE INDEX (`student_id`, `offering_id`) - may exist

**Relationships**:
- Belongs to `user` (student_id)
- Belongs to `course` (course_id)
- Belongs to `course_offerings` (offering_id) - nullable

**Notes**:
- Unique constraint prevents duplicate enrollments
- Progress tracking via JSON columns

---

### 8. assignment

Assignment table for coursework.

**Primary Key**: `id` (INT)

**Columns**:
- `id` - Assignment ID (INT, PRIMARY KEY, AUTO_INCREMENT)
- `course_id` - Course ID (BIGINT or INT, NOT NULL) - may exist
- `lesson_id` - Lesson ID (BIGINT or INT, NULLABLE)
- `session_id` - Session ID (INT, NULLABLE) - may exist
- `title` - Assignment title (VARCHAR(200) or VARCHAR(255), NOT NULL)
- `description` - Description (TEXT, NULLABLE) - may exist
- `instructions_html` - HTML instructions (LONGTEXT, NOT NULL) - may exist
- `due_date` / `due_at` - Due date (DATETIME or VARCHAR(255), NULLABLE)
- `max_points` - Max points (INT, NOT NULL) - may exist
- `created_at` / `created_at_utc` - Creation timestamp (DATETIME or VARCHAR(255), NOT NULL)
- `updated_at` / `updated_at_utc` - Update timestamp (DATETIME or VARCHAR(255), NOT NULL)
- `rubric` - Rubric (LONGTEXT, NULLABLE) - may exist
- `attachments` - Attachments (JSON, NULLABLE) - may exist
- `is_required` - Required flag (TINYINT(1), DEFAULT 1) - may exist
- `allow_late_submission` - Allow late submission (TINYINT(1), DEFAULT 0) - may exist
- `late_penalty` - Late penalty percentage (INT, DEFAULT 0) - may exist

**Indexes**:
- PRIMARY KEY (`id`)
- INDEX (`course_id`) - may exist
- INDEX (`lesson_id`)
- INDEX (`session_id`) - may exist

**Relationships**:
- Belongs to `course` (course_id) - may exist
- Belongs to `lesson` (lesson_id) - nullable
- Belongs to `session` (session_id) - nullable, may exist
- Has many `assignment_submissions`
- Has many `comments` - may exist

**Notes**:
- Schema variations exist
- Can belong to lesson or session

---

### 9. assignment_submission

Student assignment submission table.

**Primary Key**: `id` (INT)

**Columns**:
- `id` - Submission ID (INT, PRIMARY KEY, AUTO_INCREMENT)
- `assignment_id` - Assignment ID (INT, NOT NULL)
- `student_id` - Student user ID (INT or BIGINT UNSIGNED, NOT NULL)
- `graded_by_id` - Grader user ID (INT or BIGINT UNSIGNED, NULLABLE) - may exist
- `submission_text` - Submission text (TEXT, NULLABLE) - may exist
- `submitted_file_id` - Submitted file ID (INT, NULLABLE) - may exist
- `files` - Files (JSON, NOT NULL) - may exist
- `status` - Status (VARCHAR(50), NOT NULL) - may exist
- `submitted_at` - Submission timestamp (DATETIME or VARCHAR(255), NULLABLE or NOT NULL)
- `graded_at` - Grading timestamp (DATETIME, NULLABLE)
- `grade` / `grade_points` - Grade (DECIMAL(5,2) or INT, NULLABLE)
- `feedback` / `feedback_html` - Feedback (TEXT or LONGTEXT, NULLABLE)
- `notes` - Notes (LONGTEXT, NULLABLE) - may exist
- `is_late` - Late flag (TINYINT(1), DEFAULT 0) - may exist
- `late_penalty_applied` - Late penalty applied (INT, DEFAULT 0) - may exist

**Indexes**:
- PRIMARY KEY (`id`)
- INDEX (`assignment_id`)
- INDEX (`student_id`)
- INDEX (`graded_by_id`) - may exist

**Relationships**:
- Belongs to `assignment` (assignment_id)
- Belongs to `user` (student_id)
- Belongs to `user` (graded_by_id) - nullable, may exist
- Has many `comments`

---

### 10. quiz

Quiz table for interactive assessments.

**Primary Key**: `id` (INT)

**Columns**:
- `id` - Quiz ID (INT, PRIMARY KEY, AUTO_INCREMENT)
- `lesson_id` - Lesson ID (INT, NOT NULL)
- `questions` - Questions (JSON, NOT NULL)
- `pass_mark` - Pass mark percentage (INT, NOT NULL)
- `created_at` - Creation timestamp (VARCHAR(255) or DATETIME, NOT NULL)
- `instructions` - Instructions (LONGTEXT, NULLABLE)
- `time_limit` - Time limit in minutes (INT, DEFAULT 0)
- `allow_retakes` - Allow retakes (TINYINT(1), DEFAULT 1)
- `max_attempts` - Max attempts (INT, DEFAULT 3)
- `shuffle_questions` - Shuffle questions (TINYINT(1), DEFAULT 0)
- `show_correct_answers` - Show correct answers (TINYINT(1), DEFAULT 0)

**Indexes**:
- PRIMARY KEY (`id`)
- INDEX (`lesson_id`)

**Relationships**:
- Belongs to `lesson` (lesson_id)
- Has many `quiz_attempts`

**Notes**:
- `questions` stored as JSON
- `created_at` may be VARCHAR in some schemas, should be DATETIME in Laravel

---

### 11. quiz_attempt

Student quiz attempt table.

**Primary Key**: `id` (INT)

**Columns**:
- `id` - Attempt ID (INT, PRIMARY KEY, AUTO_INCREMENT)
- `quiz_id` - Quiz ID (INT, NOT NULL)
- `student_id` - Student user ID (INT or BIGINT UNSIGNED, NOT NULL)
- `score` - Score (INT, NOT NULL)
- `passed` - Passed flag (TINYINT(1), NOT NULL)
- `submitted_at` - Submission timestamp (VARCHAR(255) or DATETIME, NOT NULL)
- `responses` - Student responses (JSON, NOT NULL)
- `started_at` - Start timestamp (DATETIME, NULLABLE)
- `completed_at` - Completion timestamp (DATETIME, NULLABLE)
- `time_spent` - Time spent in seconds (INT, NULLABLE)
- `question_order` - Question order (JSON, NULLABLE)
- `notes` - Notes (LONGTEXT, NULLABLE)

**Indexes**:
- PRIMARY KEY (`id`)
- INDEX (`quiz_id`)
- INDEX (`student_id`)

**Relationships**:
- Belongs to `quiz` (quiz_id)
- Belongs to `user` (student_id)

**Notes**:
- `submitted_at` may be VARCHAR in some schemas, should be DATETIME in Laravel
- `responses` and `question_order` stored as JSON

---

### 12. order

Payment order table.

**Primary Key**: `id` (INT)

**Columns**:
- `id` - Order ID (INT, PRIMARY KEY, AUTO_INCREMENT)
- `user_id` - User ID (INT or BIGINT UNSIGNED, NOT NULL)
- `course_id` - Course ID (INT or BIGINT, NOT NULL)
- `amount_cents` - Amount in cents (INT, NOT NULL)
- `currency` - Currency code (VARCHAR(3), NOT NULL, DEFAULT 'usd')
- `status` - Status (VARCHAR(20), NOT NULL)
- `stripe_session_id` - Stripe session ID (VARCHAR(255), NULLABLE)
- `stripe_payment_intent_id` - Stripe payment intent ID (VARCHAR(255), NULLABLE)
- `created_at` / `created_at_utc` - Creation timestamp (VARCHAR(255) or DATETIME, NOT NULL)
- `updated_at` / `updated_at_utc` - Update timestamp (VARCHAR(255) or DATETIME, NOT NULL)
- `notes` - Notes (LONGTEXT, NULLABLE)
- `failure_reason` - Failure reason (VARCHAR(255), NULLABLE)

**Indexes**:
- PRIMARY KEY (`id`)
- INDEX (`user_id`)
- INDEX (`course_id`)

**Relationships**:
- Belongs to `user` (user_id)
- Belongs to `course` (course_id)

**Notes**:
- Stripe integration columns important
- Timestamps may be VARCHAR in some schemas

---

### 13. certificate

Course completion certificate table.

**Primary Key**: `id` (INT)

**Columns**:
- `id` - Certificate ID (INT, PRIMARY KEY, AUTO_INCREMENT)
- `user_id` - User ID (INT or BIGINT UNSIGNED, NOT NULL)
- `course_id` - Course ID (INT or BIGINT, NOT NULL)
- `issued_at` - Issue timestamp (VARCHAR(255) or DATETIME, NOT NULL)
- `certificate_url` - Certificate URL (VARCHAR(500), NOT NULL)
- `serial` - Serial number (VARCHAR(100), NOT NULL)
- `final_score` - Final score (DECIMAL(5,2), NOT NULL)
- `grade` - Grade (VARCHAR(20), NOT NULL)
- `notes` - Notes (LONGTEXT, NULLABLE)
- `metadata` - Metadata (JSON, NULLABLE)
- `is_valid` - Valid flag (TINYINT(1), DEFAULT 1)
- `revoked_at` - Revocation timestamp (DATETIME, NULLABLE)
- `revocation_reason` - Revocation reason (LONGTEXT, NULLABLE)

**Indexes**:
- PRIMARY KEY (`id`)
- INDEX (`user_id`)
- INDEX (`course_id`)

**Relationships**:
- Belongs to `user` (user_id)
- Belongs to `course` (course_id)

---

### 14. comment

Comment table for submission comments.

**Primary Key**: `id` (INT)

**Columns**:
- `id` - Comment ID (INT, PRIMARY KEY, AUTO_INCREMENT)
- `submission_id` - Assignment submission ID (INT, NOT NULL)
- `author_id` - Author user ID (INT or BIGINT UNSIGNED, NOT NULL)
- `body` - Comment body (LONGTEXT, NOT NULL)
- `created_at` - Creation timestamp (VARCHAR(255) or DATETIME, NOT NULL)
- `is_internal` - Internal flag (TINYINT(1), DEFAULT 0)
- `attachments` - Attachments (JSON, NULLABLE)

**Indexes**:
- PRIMARY KEY (`id`)
- INDEX (`submission_id`)
- INDEX (`author_id`)

**Relationships**:
- Belongs to `assignment_submission` (submission_id)
- Belongs to `user` (author_id)

---

### 15. note

User note table for lesson notes.

**Primary Key**: `id` (INT)

**Columns**:
- `id` - Note ID (INT, PRIMARY KEY, AUTO_INCREMENT)
- `user_id` - User ID (INT or BIGINT UNSIGNED, NOT NULL)
- `lesson_id` - Lesson ID (INT, NOT NULL)
- `body` - Note body (LONGTEXT, NOT NULL)
- `updated_at` - Update timestamp (VARCHAR(255) or DATETIME, NOT NULL)
- `tags` - Tags (JSON, NULLABLE)
- `is_public` - Public flag (TINYINT(1), DEFAULT 0)
- `word_count` - Word count (INT, DEFAULT 0)
- `character_count` - Character count (INT, DEFAULT 0)

**Indexes**:
- PRIMARY KEY (`id`)
- INDEX (`user_id`)
- INDEX (`lesson_id`)
- UNIQUE INDEX (`user_id`, `lesson_id`)

**Relationships**:
- Belongs to `user` (user_id)
- Belongs to `lesson` (lesson_id)

**Notes**:
- One note per user per lesson (unique constraint)

---

### 16. oauth_credential

OAuth credential storage table.

**Primary Key**: `id` (INT)

**Columns**:
- `id` - Credential ID (INT, PRIMARY KEY, AUTO_INCREMENT)
- `user_id` - User ID (INT or BIGINT UNSIGNED, NOT NULL)
- `provider` - Provider name (VARCHAR(50), NOT NULL) - e.g., 'google'
- `access_token` - Access token (LONGTEXT, NOT NULL)
- `refresh_token` - Refresh token (LONGTEXT, NULLABLE)
- `expires_at` - Expiration timestamp (VARCHAR(255) or DATETIME, NOT NULL)
- `created_at` - Creation timestamp (VARCHAR(255) or DATETIME, NOT NULL)
- `updated_at` - Update timestamp (VARCHAR(255) or DATETIME, NOT NULL)

**Indexes**:
- PRIMARY KEY (`id`)
- INDEX (`user_id`)

**Relationships**:
- Belongs to `user` (user_id)

---

### Additional Supporting Tables

See source schema files for:
- `course_offerings` - Recurring course offerings
- `session_package` - Session packages
- `session_bookings` - Session bookings
- `session_chat` - Session chat messages
- `teacher_availability` - Teacher availability
- `subscription_payment` - Subscription payments
- `rate_limit_attempts` - Rate limiting
- `security_events` - Security events
- `security_alerts` - Security alerts

---

## Migration Notes

### Timestamp Handling
- Symfony schemas use various timestamp formats (`created_at_utc`, `created_at` as VARCHAR, etc.)
- Laravel will standardize to `created_at` and `updated_at` (DATETIME/TIMESTAMP)
- `published_at_utc` will become `published_at`

### JSON Columns
- Many columns store JSON data: `roles`, `preferences`, `achievements`, `badges`, `questions`, `responses`, etc.
- Laravel Eloquent will handle JSON casting automatically

### Foreign Key Conventions
- All foreign keys will use Laravel conventions
- ON DELETE CASCADE/RESTRICT/SET NULL will be preserved where appropriate

### Index Naming
- Laravel will use standard index naming conventions
- Unique constraints will be preserved

### Table Naming
- Symfony uses lowercase table names (e.g., `user`, `course`)
- Laravel typically uses plural (e.g., `users`, `courses`)
- Decision: Keep singular table names to match existing schema OR migrate to plural
- **Recommendation**: Keep singular to match existing database if data migration is planned

---

**Document End**

