-- =====================================================
-- Diocese of Byumba - Bishop Meetings Test Data
-- Realistic sample data for updated meetings table
-- =====================================================

-- Insert comprehensive test data for bishop meeting requests
INSERT INTO `meetings` (
    `user_id`, 
    `meeting_type_id`, 
    `meeting_number`, 
    `first_name`, 
    `last_name`, 
    `email`, 
    `phone`, 
    `parish`, 
    `purpose`, 
    `status`, 
    `meeting_date`, 
    `meeting_time`, 
    `location`, 
    `notes`,
    `created_at`
) VALUES

-- Submitted Requests (Recent)
(1, (SELECT id FROM meeting_types WHERE type_key = 'spiritual-guidance'), 'REQ001234', 
 'Jean', 'Uwimana', 'jean.uwimana@gmail.com', '+250788123456', 'st-mary',
 'I am seeking spiritual guidance regarding my career path and how to better serve God in my profession. I have been feeling called to ministry but am unsure about the direction.',
 'submitted', NULL, NULL, NULL, 'Request submitted via online form', NOW() - INTERVAL 2 DAY),

(1, (SELECT id FROM meeting_types WHERE type_key = 'marriage-counseling'), 'REQ001235',
 'Marie', 'Mukamana', 'marie.mukamana@yahoo.com', '+250788234567', 'st-joseph',
 'My fiancé and I would like to schedule pre-marriage counseling sessions. We are planning to get married in June and want to ensure we are spiritually and emotionally prepared.',
 'submitted', NULL, NULL, NULL, 'Request submitted via online form', NOW() - INTERVAL 1 DAY),

(2, (SELECT id FROM meeting_types WHERE type_key = 'pastoral-care'), 'REQ001236',
 'Paul', 'Nzeyimana', 'paul.nzeyimana@outlook.com', '+250788345678', 'st-peter',
 'I am going through a difficult time with the loss of my father. I need pastoral support and guidance on how to cope with grief while maintaining my faith.',
 'submitted', NULL, NULL, NULL, 'Request submitted via online form', NOW() - INTERVAL 6 HOUR),

-- Reviewed Requests
(2, (SELECT id FROM meeting_types WHERE type_key = 'confession'), 'REQ001237',
 'Grace', 'Uwimana', 'grace.uwimana@gmail.com', '+250788456789', 'holy-family',
 'I would like to schedule a confession session. I have been struggling with some personal issues and need to seek forgiveness and spiritual cleansing.',
 'reviewed', NULL, NULL, NULL, 'Request reviewed by secretary. Awaiting bishop availability', NOW() - INTERVAL 3 DAY),

(3, (SELECT id FROM meeting_types WHERE type_key = 'administrative'), 'REQ001238',
 'Emmanuel', 'Habimana', 'emmanuel.habimana@diocese.rw', '+250788567890', 'st-paul',
 'I am the parish coordinator and need to discuss the upcoming youth retreat organization, budget allocation, and volunteer coordination with His Lordship.',
 'reviewed', NULL, NULL, NULL, 'Administrative matter - priority review', NOW() - INTERVAL 4 DAY),

-- Scheduled Meetings
(3, (SELECT id FROM meeting_types WHERE type_key = 'community-issue'), 'REQ001239',
 'Agnes', 'Nyirahabimana', 'agnes.nyira@gmail.com', '+250788678901', 'st-mary',
 'Our parish community is facing challenges with youth engagement. Many young people are leaving the church. I would like to discuss strategies to re-engage them.',
 'scheduled', '2024-02-15', '10:00:00', 'Bishop\'s Office', 'Meeting scheduled for February 15th at 10:00 AM', NOW() - INTERVAL 5 DAY),

(4, (SELECT id FROM meeting_types WHERE type_key = 'spiritual-guidance'), 'REQ001240',
 'David', 'Mugisha', 'david.mugisha@hotmail.com', '+250788789012', 'st-joseph',
 'I am considering joining the seminary and becoming a priest. I need guidance on discernment and understanding if this is truly God\'s calling for my life.',
 'scheduled', '2024-02-16', '14:30:00', 'Bishop\'s Office', 'Vocational discernment meeting scheduled', NOW() - INTERVAL 6 DAY),

-- Confirmed Meetings
(4, (SELECT id FROM meeting_types WHERE type_key = 'marriage-counseling'), 'REQ001241',
 'Sarah', 'Mukamana', 'sarah.mukamana@gmail.com', '+250788890123', 'st-peter',
 'Pre-marriage counseling for Sarah Mukamana and John Uwimana. We need guidance on building a strong Christian marriage foundation.',
 'confirmed', '2024-02-12', '09:00:00', 'Bishop\'s Office', 'Confirmed - Both parties to attend. Bring baptism certificates', NOW() - INTERVAL 8 DAY),

(5, (SELECT id FROM meeting_types WHERE type_key = 'pastoral-care'), 'REQ001242',
 'Joseph', 'Nkurunziza', 'joseph.nkuru@yahoo.com', '+250788901234', 'holy-family',
 'Family crisis - my teenage son has been involved in substance abuse. We need pastoral guidance and prayer support for our family healing.',
 'confirmed', '2024-02-13', '15:00:00', 'Bishop\'s Office', 'Family counseling session confirmed. Parents and son to attend', NOW() - INTERVAL 9 DAY),

-- Completed Meetings
(5, (SELECT id FROM meeting_types WHERE type_key = 'other'), 'REQ001243',
 'Immaculee', 'Uwimana', 'immaculee.uwimana@gmail.com', '+250789012345', 'st-paul',
 'I would like to discuss establishing a women\'s ministry group in our parish. Need guidance on structure, activities, and spiritual focus.',
 'completed', '2024-01-25', '11:00:00', 'Bishop\'s Office', 'Meeting completed successfully. Ministry approved with guidelines provided', NOW() - INTERVAL 15 DAY),

(6, (SELECT id FROM meeting_types WHERE type_key = 'confession'), 'REQ001244',
 'Peter', 'Hakizimana', 'peter.hakizi@outlook.com', '+250789123456', 'st-mary',
 'Personal confession and spiritual direction needed. I have been struggling with anger management and need spiritual healing.',
 'completed', '2024-01-28', '16:00:00', 'Bishop\'s Office', 'Confession and counseling completed. Follow-up scheduled with parish priest', NOW() - INTERVAL 12 DAY),

-- Cancelled Meetings
(6, (SELECT id FROM meeting_types WHERE type_key = 'administrative'), 'REQ001245',
 'Christine', 'Mukamazimpaka', 'christine.muka@diocese.rw', '+250789234567', 'st-joseph',
 'Discussion about parish financial management and transparency issues that have been raised by parishioners.',
 'cancelled', '2024-02-08', '13:00:00', 'Bishop\'s Office', 'Cancelled due to bishop\'s emergency travel. Rescheduling in progress', NOW() - INTERVAL 10 DAY),

-- Rejected Requests
(1, (SELECT id FROM meeting_types WHERE type_key = 'other'), 'REQ001246',
 'Robert', 'Nsengimana', 'robert.nseng@gmail.com', '+250789345678', 'other',
 'I want to discuss my disagreement with the parish priest about church policies and demand changes to be made.',
 'rejected', NULL, NULL, NULL, 'Request rejected - inappropriate tone and demands. Referred to parish council', NOW() - INTERVAL 7 DAY),

-- More Recent Requests (Various Parishes)
(2, (SELECT id FROM meeting_types WHERE type_key = 'spiritual-guidance'), 'REQ001247',
 'Beatrice', 'Nyiramana', 'beatrice.nyira@gmail.com', '+250789456789', 'st-peter',
 'I am facing a crisis of faith after losing my job and struggling financially. I need spiritual guidance to strengthen my trust in God\'s plan.',
 'submitted', NULL, NULL, NULL, 'Request submitted via online form', NOW() - INTERVAL 8 HOUR),

(3, (SELECT id FROM meeting_types WHERE type_key = 'community-issue'), 'REQ001248',
 'Francis', 'Uwimana', 'francis.uwimana@hotmail.com', '+250789567890', 'holy-family',
 'Our parish has conflicts between different ethnic groups. We need guidance on promoting unity and reconciliation within our community.',
 'submitted', NULL, NULL, NULL, 'Urgent community matter - ethnic tensions', NOW() - INTERVAL 4 HOUR),

(4, (SELECT id FROM meeting_types WHERE type_key = 'marriage-counseling'), 'REQ001249',
 'Alice', 'Mukamuganga', 'alice.mukamu@yahoo.com', '+250789678901', 'st-mary',
 'Marriage difficulties - my husband and I are considering separation. We want to try Christian counseling before making any final decisions.',
 'reviewed', NULL, NULL, NULL, 'Urgent marriage counseling needed. Priority case', NOW() - INTERVAL 1 DAY),

(5, (SELECT id FROM meeting_types WHERE type_key = 'pastoral-care'), 'REQ001250',
 'Vincent', 'Habimana', 'vincent.habi@gmail.com', '+250789789012', 'st-joseph',
 'My mother is terminally ill and we need pastoral support for our family. Also need guidance on end-of-life spiritual care.',
 'scheduled', '2024-02-14', '10:30:00', 'Bishop\'s Office', 'Pastoral care meeting scheduled. Family support needed', NOW() - INTERVAL 2 DAY),

-- International/Diaspora Requests
(6, (SELECT id FROM meeting_types WHERE type_key = 'other'), 'REQ001251',
 'Diane', 'Uwimana', 'diane.uwimana@international.com', '+250789890123', 'other',
 'I am living abroad but visiting Rwanda. I would like to discuss establishing a Rwandan Catholic community in my host country.',
 'submitted', NULL, NULL, NULL, 'International diaspora ministry request', NOW() - INTERVAL 12 HOUR),

-- Youth and Vocational Requests
(1, (SELECT id FROM meeting_types WHERE type_key = 'spiritual-guidance'), 'REQ001252',
 'Samuel', 'Nkurunziza', 'samuel.nkuru@student.edu', '+250789901234', 'st-paul',
 'I am a university student struggling with peer pressure and maintaining my faith in a secular environment. Need spiritual guidance.',
 'submitted', NULL, NULL, NULL, 'Youth spiritual guidance request', NOW() - INTERVAL 3 HOUR),

(2, (SELECT id FROM meeting_types WHERE type_key = 'confession'), 'REQ001253',
 'Esperance', 'Mukamana', 'esperance.muka@gmail.com', '+250780123456', 'st-peter',
 'I need to make a general confession and receive spiritual direction for my spiritual growth and commitment to religious life.',
 'reviewed', NULL, NULL, NULL, 'Vocational discernment - religious life', NOW() - INTERVAL 5 HOUR);

-- =====================================================
-- Job Applications Test Data
-- =====================================================

-- Insert comprehensive test data for job applications
INSERT INTO `job_applications` (
    `user_id`,
    `first_name`,
    `last_name`,
    `email`,
    `phone`,
    `address`,
    `education_level`,
    `years_experience`,
    `skills`,
    `cover_letter`,
    `job_title`,
    `job_department`,
    `job_type`,
    `status`,
    `priority`,
    `terms_accepted`,
    `data_consent`,
    `notification_methods`,
    `submitted_at`
) VALUES

-- Submitted Applications
(1, 'Jean Baptiste', 'Uwimana', 'jeanbaptiste.uwimana@gmail.com', '+250788123456',
 'Kigali, Gasabo District, Remera Sector',
 'bachelor', '2-3', 'Project Management, Microsoft Office, Financial Analysis, Budget Planning, Team Leadership',
 'I am writing to express my strong interest in the Finance Officer position at the Diocese of Byumba. With my background in accounting and passion for serving the church, I believe I can contribute significantly to the financial stewardship of the diocese.',
 'Finance Officer', 'Administration', 'full_time', 'submitted', 'medium', 1, 1,
 '["email"]', NOW() - INTERVAL 2 DAY),

(2, 'Marie Claire', 'Mukamana', 'marieclaire.mukamana@yahoo.com', '+250788234567',
 'Byumba, Northern Province',
 'diploma', '4-5', 'Teaching, Curriculum Development, Child Psychology, French and English Fluency, Computer Skills',
 'As a dedicated educator with 5 years of experience in primary education, I am excited to apply for the Primary School Teacher position. My commitment to Christian values aligns perfectly with the diocese\'s educational mission.',
 'Primary School Teacher', 'Education', 'full_time', 'submitted', 'high', 1, 1,
 '["email", "sms"]', NOW() - INTERVAL 1 DAY),

(3, 'Paul', 'Nzeyimana', 'paul.nzeyimana@outlook.com', '+250788345678',
 'Musanze, Northern Province',
 'master', '6-10', 'Social Work, Community Development, Counseling, Crisis Intervention, Program Management, Grant Writing',
 'I am passionate about community development and social justice. The Community Outreach Coordinator position represents an opportunity to serve vulnerable populations while advancing the church\'s social mission.',
 'Community Outreach Coordinator', 'Social Services', 'full_time', 'under_review', 'high', 1, 1,
 '["email"]', NOW() - INTERVAL 5 DAY),

-- Shortlisted Applications
(4, 'Grace', 'Uwimana', 'grace.uwimana@gmail.com', '+250788456789',
 'Ruhengeri, Northern Province',
 'bachelor', '2-3', 'Graphic Design, Adobe Creative Suite, Web Design, Social Media Management, Photography, Video Editing',
 'With my creative background and understanding of church communications, I am excited to apply for the Communications Specialist role. I believe effective communication is essential for spreading God\'s word in the digital age.',
 'Communications Specialist', 'Communications', 'full_time', 'shortlisted', 'medium', 1, 1,
 '["email", "phone"]', NOW() - INTERVAL 8 DAY),

(5, 'Emmanuel', 'Habimana', 'emmanuel.habimana@diocese.rw', '+250788567890',
 'Byumba, Northern Province',
 'secondary', '10+', 'Maintenance, Electrical Work, Plumbing, Carpentry, Facility Management, Equipment Repair',
 'I have been serving various parishes as a maintenance worker for over 10 years. I understand the unique needs of church facilities and am committed to maintaining God\'s house with excellence.',
 'Maintenance Supervisor', 'Facilities', 'full_time', 'shortlisted', 'medium', 1, 1,
 '["phone"]', NOW() - INTERVAL 10 DAY),

-- Interview Scheduled
(6, 'Agnes', 'Nyirahabimana', 'agnes.nyira@gmail.com', '+250788678901',
 'Gicumbi, Northern Province',
 'master', '4-5', 'Theology, Pastoral Care, Youth Ministry, Event Planning, Public Speaking, Counseling',
 'As a theology graduate with experience in youth ministry, I am called to serve in the Youth Pastor role. I believe in empowering young people to develop strong relationships with Christ.',
 'Youth Pastor', 'Pastoral Care', 'full_time', 'interview_scheduled', 'high', 1, 1,
 '["email", "sms"]', NOW() - INTERVAL 12 DAY),

-- Interviewed Applications
(1, 'David', 'Mugisha', 'david.mugisha@hotmail.com', '+250788789012',
 'Kigali, Nyarugenge District',
 'bachelor', '2-3', 'Accounting, QuickBooks, Excel, Financial Reporting, Audit Support, Tax Preparation',
 'I am seeking to use my accounting skills in service to the church. The Accountant position would allow me to ensure proper stewardship of church resources while supporting the diocese\'s mission.',
 'Accountant', 'Finance', 'full_time', 'interviewed', 'medium', 1, 1,
 '["email"]', NOW() - INTERVAL 15 DAY),

-- Selected Applications
(2, 'Sarah', 'Mukamana', 'sarah.mukamana@gmail.com', '+250788890123',
 'Byumba, Northern Province',
 'diploma', '0-1', 'Customer Service, Data Entry, Phone Etiquette, Microsoft Office, Organization, Multi-tasking',
 'I am excited to begin my career in church administration. As a recent graduate, I am eager to learn and contribute to the diocese\'s administrative efficiency while serving God\'s people.',
 'Administrative Assistant', 'Administration', 'full_time', 'selected', 'low', 1, 1,
 '["email", "sms", "phone"]', NOW() - INTERVAL 20 DAY),

-- Part-time and Contract Applications
(3, 'Joseph', 'Nkurunziza', 'joseph.nkuru@yahoo.com', '+250788901234',
 'Musanze, Northern Province',
 'bachelor', '6-10', 'Music Theory, Piano, Choir Direction, Worship Leading, Audio Equipment, Music Arrangement',
 'Music has been my calling since childhood. I would be honored to serve as Music Director, leading worship and training choirs to glorify God through music.',
 'Music Director', 'Worship', 'part_time', 'submitted', 'medium', 1, 1,
 '["email"]', NOW() - INTERVAL 3 DAY),

(4, 'Immaculee', 'Uwimana', 'immaculee.uwimana@gmail.com', '+250789012345',
 'Gicumbi, Northern Province',
 'secondary', '4-5', 'Cleaning, Laundry, Kitchen Management, Food Preparation, Hospitality, Time Management',
 'I have experience managing household and facility cleaning. I understand the importance of maintaining clean and welcoming church spaces for worship and community gatherings.',
 'Custodial Staff', 'Facilities', 'part_time', 'under_review', 'low', 1, 1,
 '["phone"]', NOW() - INTERVAL 6 DAY),

-- Volunteer Applications
(5, 'Peter', 'Hakizimana', 'peter.hakizi@outlook.com', '+250789123456',
 'Byumba, Northern Province',
 'bachelor', '2-3', 'Teaching, Catechesis, Bible Study Leadership, Public Speaking, Youth Mentoring',
 'I feel called to volunteer as a Catechist to help prepare children and adults for sacraments. Teaching the faith is a privilege I would cherish.',
 'Volunteer Catechist', 'Religious Education', 'volunteer', 'submitted', 'medium', 1, 1,
 '["email"]', NOW() - INTERVAL 1 DAY),

-- Rejected Applications
(6, 'Christine', 'Mukamazimpaka', 'christine.muka@diocese.rw', '+250789234567',
 'Kigali, Kicukiro District',
 'primary', '0-1', 'Basic Computer Skills, Cleaning, Cooking',
 'I need a job to support my family. I am willing to do any work available at the diocese.',
 'Secretary', 'Administration', 'full_time', 'rejected', 'low', 1, 1,
 '["phone"]', NOW() - INTERVAL 25 DAY);

-- =====================================================
-- Summary of Complete Test Data:
--
-- BISHOP MEETINGS:
-- - 20 bishop meeting requests
-- - All meeting types: spiritual-guidance, pastoral-care, marriage-counseling, confession, administrative, community-issue, other
-- - All status types: submitted, reviewed, scheduled, confirmed, completed, cancelled, rejected
-- - Various parishes: st-mary, st-joseph, st-peter, holy-family, st-paul, other
-- - Realistic pastoral scenarios and purposes
-- - Different users (1-6) for multi-user testing
-- - Time distribution from recent to weeks ago
--
-- JOB APPLICATIONS:
-- - 12 job applications
-- - Various positions: Finance Officer, Teacher, Coordinator, Specialist, Pastor, etc.
-- - All departments: Administration, Education, Social Services, Communications, Facilities, etc.
-- - All job types: full_time, part_time, volunteer
-- - All status types: submitted, under_review, shortlisted, interview_scheduled, interviewed, selected, rejected
-- - All education levels: primary, secondary, diploma, bachelor, master
-- - Various experience levels: 0-1, 2-3, 4-5, 6-10, 10+
-- - Realistic cover letters and skill sets
-- - Different users (1-6) for multi-user testing
-- - Geographic diversity across Northern Province
-- =====================================================
