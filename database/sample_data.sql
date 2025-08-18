-- Sample Data for Diocese of Byumba System
USE diocese_byumba;

-- Insert sample users
INSERT INTO users (first_name, last_name, email, phone, national_id, date_of_birth, place_of_birth, gender, address, preferred_language, email_verified, phone_verified, password_hash) VALUES
('John', 'Doe', 'john.doe@email.com', '+250788123456', '1234567890123456', '1990-05-15', 'Byumba, Rwanda', 'male', 'Northern Province, Gicumbi District, Byumba Sector, Gitoki Cell, Nyarutovu Village', 'en', TRUE, TRUE, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Marie', 'Uwimana', 'marie.uwimana@email.com', '+250788234567', '1234567890123457', '1992-08-22', 'Gicumbi, Rwanda', 'female', 'Northern Province, Gicumbi District, Rukomo Sector, Nyamiyaga Cell', 'rw', TRUE, TRUE, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Pierre', 'Nzeyimana', 'pierre.nzeyimana@email.com', '+250788345678', '1234567890123458', '1988-12-10', 'Rulindo, Rwanda', 'male', 'Northern Province, Rulindo District, Buyoga Sector, Cyungo Cell', 'fr', TRUE, FALSE, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Grace', 'Mukamana', 'grace.mukamana@email.com', '+250788456789', '1234567890123459', '1995-03-18', 'Gakenke, Rwanda', 'female', 'Northern Province, Gakenke District, Gakenke Sector, Nemba Cell', 'en', FALSE, TRUE, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Emmanuel', 'Habimana', 'emmanuel.habimana@email.com', '+250788567890', '1234567890123460', '1985-11-25', 'Burera, Rwanda', 'male', 'Northern Province, Burera District, Cyanika Sector, Kidaho Cell', 'rw', TRUE, TRUE, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Insert user parish memberships
INSERT INTO user_parish_membership (user_id, parish_id, membership_date, baptism_date, confirmation_date, role) VALUES
(1, 1, '2020-01-15', '1990-06-10', '2005-05-20', 'member'),
(2, 2, '2018-03-20', '1992-09-15', '2007-08-12', 'choir'),
(3, 3, '2019-07-10', '1988-12-25', '2003-11-30', 'catechist'),
(4, 4, '2021-05-05', '1995-04-08', '2010-06-15', 'youth_leader'),
(5, 5, '2017-09-12', '1985-12-20', '2000-10-25', 'committee');

-- Insert sample applications
INSERT INTO applications (user_id, certificate_type_id, application_number, status, submitted_date, approved_date, completed_date, payment_code, payment_status, payment_date, notes) VALUES
(1, 1, 'APP001', 'completed', '2024-01-10 09:00:00', '2024-01-12 14:30:00', '2024-01-15 10:00:00', 'BC2024001', 'confirmed', '2024-01-13 16:20:00', 'Certificate ready for pickup'),
(1, 3, 'APP002', 'approved', '2024-01-08 11:30:00', '2024-01-12 15:45:00', NULL, 'MC2024002', 'pending', NULL, 'Payment required to proceed'),
(1, 2, 'APP003', 'processing', '2024-01-05 14:15:00', NULL, NULL, NULL, 'pending', NULL, 'Under review by parish priest'),
(1, 5, 'APP004', 'completed', '2024-01-01 10:20:00', '2024-01-03 09:15:00', '2024-01-05 11:30:00', 'MC2024004', 'confirmed', '2024-01-04 14:45:00', 'Certificate issued'),
(2, 1, 'APP005', 'pending', '2024-01-18 16:30:00', NULL, NULL, NULL, 'pending', NULL, 'Waiting for document verification'),
(3, 6, 'APP006', 'completed', '2023-12-15 08:45:00', '2023-12-18 13:20:00', '2023-12-20 09:10:00', 'GS2023006', 'confirmed', '2023-12-19 10:30:00', 'Certificate delivered'),
(4, 4, 'APP007', 'pending', '2024-01-03 12:00:00', NULL, NULL, NULL, 'pending', NULL, 'Seminary verification in progress');

-- Insert sample application documents
INSERT INTO application_documents (application_id, document_name, file_path, file_size, mime_type) VALUES
(1, 'National ID Copy', '/uploads/documents/app001_national_id.pdf', 245760, 'application/pdf'),
(1, 'Birth Certificate', '/uploads/documents/app001_birth_cert.pdf', 189440, 'application/pdf'),
(1, 'Passport Photo', '/uploads/documents/app001_photo.jpg', 156672, 'image/jpeg'),
(2, 'National ID Copy (Groom)', '/uploads/documents/app002_groom_id.pdf', 251904, 'application/pdf'),
(2, 'National ID Copy (Bride)', '/uploads/documents/app002_bride_id.pdf', 248832, 'application/pdf'),
(2, 'Birth Certificate (Groom)', '/uploads/documents/app002_groom_birth.pdf', 195584, 'application/pdf'),
(2, 'Birth Certificate (Bride)', '/uploads/documents/app002_bride_birth.pdf', 198656, 'application/pdf');

-- Insert sample meetings
INSERT INTO meetings (user_id, meeting_type_id, meeting_number, title, description, meeting_date, meeting_time, duration_minutes, location, status, notes) VALUES
(1, 1, 'MTG001', 'Marriage Counseling Session', 'Pre-marriage counseling session for John Doe and Jane Smith', '2024-01-20', '10:00:00', 60, 'Bishop\'s Office', 'confirmed', 'Please bring required documents and arrive 15 minutes early'),
(1, 2, 'MTG002', 'Baptism Preparation', 'Baptism preparation session for infant baptism', '2024-01-22', '14:00:00', 45, 'Parish Hall', 'pending', 'Bring baptism application form and godparents information'),
(1, 3, 'MTG003', 'General Consultation', 'General spiritual consultation and guidance', '2024-01-25', '11:00:00', 30, 'Bishop\'s Office', 'confirmed', 'Personal consultation regarding spiritual matters'),
(2, 4, 'MTG004', 'Confession Session', 'Private confession session', '2024-01-10', '16:00:00', 30, 'Confessional', 'completed', 'Completed successfully'),
(3, 5, 'MTG005', 'Spiritual Guidance', 'Spiritual guidance and prayer session', '2024-01-05', '09:00:00', 45, 'Bishop\'s Office', 'completed', 'Discussed spiritual growth and prayer life'),
(2, 1, 'MTG006', 'Marriage Counseling Follow-up', 'Follow-up marriage counseling session', '2024-01-12', '15:00:00', 60, 'Bishop\'s Office', 'cancelled', 'Cancelled due to scheduling conflict');

-- Insert sample notifications
INSERT INTO notifications (user_id, notification_type_id, title, message, action_required, action_text, action_url, is_read) VALUES
(1, 1, 'Application Approved', 'Your Baptism Certificate application has been approved. Payment code: BC2024001. Please proceed with payment to complete the process.', TRUE, 'Make Payment', 'my-applications.html', FALSE),
(1, 2, 'Meeting Reminder', 'Your marriage counseling session is scheduled for tomorrow (January 20, 2024) at 10:00 AM in the Bishop\'s Office. Please arrive 15 minutes early.', TRUE, 'View Meeting', 'my-meetings.html', FALSE),
(1, 1, 'Document Required', 'Additional documentation is needed for your Marriage Certificate application (APP002). Please upload the missing birth certificate within 7 days.', TRUE, 'Upload Document', 'my-applications.html', FALSE),
(1, 3, 'Payment Received', 'Payment confirmed for Membership Certificate (APP004). Your certificate will be ready for pickup in 2-3 business days. You will receive another notification when ready.', FALSE, NULL, NULL, TRUE),
(1, 4, 'New Job Posting', 'A new position matching your profile has been posted: Parish Coordinator at St. Mary\'s Parish. Application deadline: January 30, 2024.', TRUE, 'View Job', 'jobs.html', TRUE),
(1, 5, 'System Maintenance', 'Scheduled system maintenance will occur on January 21, 2024, from 2:00 AM to 4:00 AM. Some services may be temporarily unavailable.', FALSE, NULL, NULL, TRUE),
(2, 1, 'Certificate Ready', 'Your Good Standing Certificate is ready for pickup. Please visit the Diocese office during business hours (8:00 AM - 5:00 PM) with your ID.', TRUE, 'Download Certificate', 'my-applications.html', TRUE),
(2, 2, 'Meeting Cancelled', 'Your scheduled meeting on January 12, 2024, has been cancelled due to an emergency. Please reschedule at your convenience.', TRUE, 'Reschedule', 'bishop-meeting.html', TRUE),
(3, 4, 'Welcome to Diocese Portal', 'Welcome to the Diocese of Byumba online portal! You can now apply for certificates, schedule meetings, and stay updated with announcements.', FALSE, NULL, NULL, TRUE),
(4, 5, 'Profile Update Required', 'Please update your profile information to ensure you receive important notifications. Some fields are missing or outdated.', TRUE, 'Update Profile', 'profile.html', TRUE);

-- Insert sample jobs
INSERT INTO jobs (job_category_id, parish_id, job_number, title, description, requirements, salary_range, employment_type, location, application_deadline, contact_email, contact_phone) VALUES
(1, 1, 'JOB001', 'Parish Coordinator', 'Coordinate parish activities and manage administrative tasks. Assist the parish priest in organizing events, maintaining records, and communicating with parishioners.', 'Bachelor\'s degree in Administration or related field. Minimum 2 years experience in administrative roles. Excellent communication skills in English and Kinyarwanda. Computer literacy required.', 'RWF 150,000 - 200,000', 'full_time', 'St. Mary\'s Parish, Byumba', '2024-01-30', 'stmary@diocesebyumba.rw', '+250788123456'),
(2, 2, 'JOB002', 'Religious Education Teacher', 'Teach religious education classes for children and adults. Develop curriculum and educational materials for faith formation programs.', 'Degree in Theology, Religious Studies, or Education. Teaching experience preferred. Strong knowledge of Catholic doctrine. Fluent in Kinyarwanda and English.', 'RWF 120,000 - 160,000', 'part_time', 'St. Joseph\'s Parish, Gicumbi', '2024-02-15', 'stjoseph@diocesebyumba.rw', '+250788234567'),
(3, 3, 'JOB003', 'Youth Ministry Leader', 'Lead youth programs and activities. Organize retreats, camps, and spiritual formation programs for young people aged 13-25.', 'Bachelor\'s degree preferred. Experience in youth ministry or related field. Strong leadership and communication skills. Passion for working with young people.', 'RWF 100,000 - 140,000', 'full_time', 'St. Peter\'s Parish, Rulindo', '2024-02-28', 'stpeter@diocesebyumba.rw', '+250788345678'),
(4, 4, 'JOB004', 'Maintenance Technician', 'Maintain church buildings and facilities. Perform routine maintenance, repairs, and ensure safety standards are met.', 'Technical diploma in electrical, plumbing, or general maintenance. Minimum 3 years experience. Ability to work independently and handle emergency repairs.', 'RWF 80,000 - 120,000', 'full_time', 'Holy Family Parish, Gakenke', '2024-03-15', 'holyfamily@diocesebyumba.rw', '+250788456789'),
(6, 5, 'JOB005', 'Community Outreach Coordinator', 'Coordinate social services and community outreach programs. Work with vulnerable populations and manage charity initiatives.', 'Degree in Social Work, Community Development, or related field. Experience in community work. Compassionate and culturally sensitive approach.', 'RWF 130,000 - 170,000', 'full_time', 'St. Paul\'s Parish, Burera', '2024-04-01', 'stpaul@diocesebyumba.rw', '+250788567890'),
(1, NULL, 'JOB006', 'Diocese Administrative Assistant', 'Provide administrative support to the Diocese office. Handle correspondence, maintain records, and assist with various diocesan activities.', 'Diploma in Administration or related field. Excellent organizational skills. Proficiency in Microsoft Office. Bilingual (English/Kinyarwanda) required.', 'RWF 110,000 - 150,000', 'full_time', 'Diocese Office, Byumba', '2024-03-31', 'admin@diocesebyumba.rw', '+250788123456');

-- Insert sample blog posts
INSERT INTO blog_posts (blog_category_id, author_id, post_number, title, slug, excerpt, content, featured_image, is_featured, is_published, published_at, views_count) VALUES
(1, NULL, 'POST001', 'New Online Certificate Application System Launched', 'new-online-certificate-system', 'The Diocese of Byumba is pleased to announce the launch of our new online certificate application system, making it easier for parishioners to request important documents.', 'The Diocese of Byumba is pleased to announce the launch of our new online certificate application system. This digital platform will streamline the process of requesting baptism, confirmation, marriage, and other important certificates.\n\nKey features include:\n- Online application submission\n- Document upload capability\n- Real-time status tracking\n- Secure payment processing\n- Multi-language support\n\nParishioners can now apply for certificates from the comfort of their homes and track the progress of their applications online. This initiative is part of our ongoing efforts to modernize our services and better serve our community.\n\nTo access the system, visit our website and create an account. For assistance, please contact our office during business hours.', '/uploads/blog/certificate-system-launch.jpg', TRUE, TRUE, '2024-01-15 10:00:00', 245),
(2, NULL, 'POST002', 'Annual Diocese Youth Retreat 2024', 'annual-youth-retreat-2024', 'Join us for the Annual Diocese Youth Retreat from February 15-17, 2024, at Lake Ruhondo. A weekend of spiritual growth, fellowship, and fun activities for young Catholics.', 'The Diocese of Byumba invites all young Catholics aged 16-30 to participate in our Annual Youth Retreat from February 15-17, 2024, at the beautiful Lake Ruhondo retreat center.\n\nThis year\'s theme is "Called to Serve" and will feature:\n- Inspiring talks by guest speakers\n- Small group discussions\n- Adoration and Mass\n- Recreational activities\n- Cultural performances\n- Networking opportunities\n\nThe retreat aims to strengthen faith, build community, and inspire young people to take active roles in their parishes and communities.\n\nRegistration fee: RWF 25,000 (includes accommodation, meals, and materials)\nRegistration deadline: February 5, 2024\n\nTo register, contact your parish youth coordinator or visit our office. Limited spaces available - register early!', '/uploads/blog/youth-retreat-2024.jpg', TRUE, TRUE, '2024-01-12 14:30:00', 189),
(3, NULL, 'POST003', 'Lenten Season Preparation and Activities', 'lenten-season-preparation-2024', 'As we approach the holy season of Lent, the Diocese of Byumba announces special programs and activities to help the faithful prepare for Easter.', 'The season of Lent is a time of prayer, fasting, and almsgiving as we prepare our hearts for the celebration of Easter. The Diocese of Byumba has prepared special programs to accompany the faithful during this holy season.\n\nLenten Activities:\n- Weekly Stations of the Cross (Fridays at 6:00 PM)\n- Lenten retreat for adults (March 2-3)\n- Children\'s Lenten program\n- Special confession schedules\n- Charity drives for the needy\n\nEach parish will also organize additional activities according to local needs. We encourage all parishioners to participate actively in these spiritual exercises.\n\nLet us use this Lenten season to grow closer to God through prayer, sacrifice, and service to others. May this be a time of spiritual renewal and preparation for the joy of Easter.', '/uploads/blog/lenten-season-2024.jpg', FALSE, TRUE, '2024-01-10 09:15:00', 156),
(4, NULL, 'POST004', 'Community Health Initiative Launch', 'community-health-initiative-launch', 'The Diocese of Byumba launches a new community health initiative in partnership with local health centers to improve healthcare access in rural areas.', 'The Diocese of Byumba is proud to announce the launch of our Community Health Initiative, a comprehensive program designed to improve healthcare access and health education in rural communities within our diocese.\n\nProgram Components:\n- Mobile health clinics visiting remote areas\n- Health education workshops\n- Maternal and child health programs\n- Nutrition education\n- Disease prevention campaigns\n- Mental health awareness\n\nThis initiative is implemented in partnership with local health centers, government agencies, and international health organizations. Our goal is to ensure that all members of our community have access to quality healthcare services.\n\nVolunteers are needed for various aspects of the program. If you have medical training or simply want to help your community, please contact our social services coordinator.\n\nTogether, we can build healthier communities and demonstrate God\'s love through caring for the sick and vulnerable.', '/uploads/blog/health-initiative.jpg', FALSE, TRUE, '2024-01-08 11:45:00', 134),
(5, NULL, 'POST005', 'Adult Faith Formation Program Registration Open', 'adult-faith-formation-program-2024', 'Registration is now open for the 2024 Adult Faith Formation Program. Deepen your understanding of Catholic teaching and grow in your relationship with God.', 'The Diocese of Byumba invites all adults to participate in our comprehensive Faith Formation Program starting February 1, 2024. This program is designed for Catholics who want to deepen their understanding of the faith and grow in their spiritual journey.\n\nProgram Features:\n- Scripture study sessions\n- Catholic doctrine classes\n- Liturgy and sacraments education\n- Prayer and spirituality workshops\n- Social justice teachings\n- Small group discussions\n\nClasses will be held every Thursday evening from 7:00-8:30 PM at the Diocese center. The program runs for 12 weeks and includes take-home materials for further study.\n\nWhether you\'re a lifelong Catholic or someone returning to the faith, this program offers something for everyone. Our experienced catechists will guide you through engaging discussions and practical applications of Catholic teaching.\n\nRegistration fee: RWF 15,000 (includes all materials)\nTo register, contact your parish office or call the Diocese at +250 788 123 456.', '/uploads/blog/faith-formation-2024.jpg', FALSE, TRUE, '2024-01-05 16:20:00', 98);

-- Insert user notification preferences
INSERT INTO user_notification_preferences (user_id, notification_type_id, email_enabled, sms_enabled, push_enabled) VALUES
(1, 1, TRUE, TRUE, TRUE),
(1, 2, TRUE, FALSE, TRUE),
(1, 3, TRUE, TRUE, TRUE),
(1, 4, TRUE, FALSE, TRUE),
(1, 5, FALSE, FALSE, TRUE),
(2, 1, TRUE, FALSE, TRUE),
(2, 2, TRUE, TRUE, TRUE),
(2, 3, TRUE, TRUE, TRUE),
(2, 4, FALSE, FALSE, TRUE),
(2, 5, FALSE, FALSE, FALSE),
(3, 1, TRUE, FALSE, TRUE),
(3, 2, TRUE, FALSE, TRUE),
(3, 3, TRUE, FALSE, TRUE),
(3, 4, TRUE, FALSE, TRUE),
(3, 5, TRUE, FALSE, FALSE);
