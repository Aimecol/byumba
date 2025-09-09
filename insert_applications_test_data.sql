-- =====================================================
-- Diocese Certificate Application System - Test Data
-- Applications Table Sample Data
-- =====================================================

-- Insert comprehensive test data for applications table
-- This includes various certificate types, statuses, and realistic scenarios

INSERT INTO `applications` (
    `user_id`, 
    `certificate_type_id`, 
    `application_number`, 
    `status`, 
    `submitted_date`, 
    `approved_date`, 
    `completed_date`, 
    `payment_code`, 
    `payment_status`, 
    `payment_date`, 
    `notes`, 
    `notification_methods`
) VALUES

-- 1. Completed Abasheshakanguhe Certificate
(1, 1, 'APP001234', 'completed', '2024-01-15 09:30:00', '2024-01-18 14:20:00', '2024-01-22 10:15:00', 
 'PAY001234', 'confirmed', '2024-01-17 16:45:00', 
 'Certificate issued and ready for pickup. All documents verified successfully.', 
 '["email", "sms"]'),

-- 2. Processing Ebenezer Certificate
(1, 2, 'APP001235', 'processing', '2024-01-20 11:15:00', NULL, NULL, 
 NULL, 'pending', NULL, 
 'Application under review by parish committee. Additional documentation requested.', 
 '["email"]'),

-- 3. Approved Father's Union Certificate (Payment Pending)
(1, 3, 'APP001236', 'approved', '2024-01-18 14:30:00', '2024-01-22 09:45:00', NULL, 
 'PAY001236', 'pending', NULL, 
 'Application approved. Awaiting payment confirmation to proceed with certificate issuance.', 
 '["email", "sms", "phone"]'),

-- 4. Completed Marriage Certificate
(1, 7, 'APP001237', 'completed', '2024-01-10 08:00:00', '2024-01-15 13:30:00', '2024-01-20 11:00:00', 
 'PAY001237', 'confirmed', '2024-01-14 10:20:00', 
 'Marriage certificate completed. Couple notified for pickup. All pre-marriage requirements fulfilled.', 
 '["email", "sms"]'),

-- 5. Pending Christian Certificate
(1, 6, 'APP001238', 'pending', '2024-01-25 16:20:00', NULL, NULL, 
 NULL, 'pending', NULL, 
 'New application received. Awaiting initial document verification.', 
 '["email"]'),

-- 6. Rejected Youth Union Certificate
(1, 9, 'APP001239', 'rejected', '2024-01-12 10:45:00', NULL, NULL, 
 NULL, 'pending', NULL, 
 'Application rejected due to incomplete documentation. Applicant advised to resubmit with required baptism certificate.', 
 '["email", "sms"]'),

-- 7. Completed Mother's Union Certificate
(1, 8, 'APP001240', 'completed', '2024-01-08 13:15:00', '2024-01-12 11:30:00', '2024-01-16 14:45:00', 
 'PAY001240', 'confirmed', '2024-01-11 09:15:00', 
 'Certificate issued successfully. Member inducted into Mother\'s Union chapter.', 
 '["email", "phone"]'),

-- 8. Processing Seminary Visit Certificate
(1, 4, 'APP001241', 'processing', '2024-01-22 09:00:00', NULL, NULL, 
 NULL, 'pending', NULL, 
 'Seminary visit request under review. Background check in progress.', 
 '["email"]'),

-- 9. Approved GFS Membership Certificate
(1, 5, 'APP001242', 'approved', '2024-01-19 15:30:00', '2024-01-24 10:15:00', NULL, 
 'PAY001242', 'paid', '2024-01-23 14:20:00', 
 'Application approved and payment received. Certificate preparation in progress.', 
 '["email", "sms"]'),

-- 10. Completed Abasheshakanguhe Certificate (Different User)
(2, 1, 'APP001243', 'completed', '2024-01-05 11:20:00', '2024-01-09 16:45:00', '2024-01-14 13:30:00', 
 'PAY001243', 'confirmed', '2024-01-08 12:10:00', 
 'Certificate completed and delivered. Member active in parish activities.', 
 '["sms"]'),

-- 11. Processing Father's Union Certificate
(2, 3, 'APP001244', 'processing', '2024-01-23 14:45:00', NULL, NULL, 
 NULL, 'pending', NULL, 
 'Marriage certificate verification in progress. Spouse documentation pending.', 
 '["email", "phone"]'),

-- 12. Pending Ebenezer Certificate
(3, 2, 'APP001245', 'pending', '2024-01-26 10:30:00', NULL, NULL, 
 NULL, 'pending', NULL, 
 'Application received. Scheduled for initial review next week.', 
 '["email"]'),

-- 13. Completed Marriage Certificate (Recent)
(3, 7, 'APP001246', 'completed', '2024-01-16 09:15:00', '2024-01-21 14:30:00', '2024-01-25 11:45:00', 
 'PAY001246', 'confirmed', '2024-01-20 16:20:00', 
 'Marriage ceremony completed successfully. Certificate issued to couple.', 
 '["email", "sms", "phone"]'),

-- 14. Approved Christian Certificate
(4, 6, 'APP001247', 'approved', '2024-01-17 12:00:00', '2024-01-22 09:30:00', NULL, 
 'PAY001247', 'pending', NULL, 
 'Christian faith verification completed. Awaiting final payment for certificate issuance.', 
 '["email"]'),

-- 15. Processing Youth Union Certificate
(4, 9, 'APP001248', 'processing', '2024-01-24 16:15:00', NULL, NULL, 
 NULL, 'pending', NULL, 
 'Youth leadership assessment in progress. Community service hours being verified.', 
 '["email", "sms"]'),

-- 16. Completed Seminary Visit Certificate
(5, 4, 'APP001249', 'completed', '2024-01-11 08:30:00', '2024-01-16 13:15:00', '2024-01-19 10:20:00', 
 'PAY001249', 'confirmed', '2024-01-15 11:45:00', 
 'Seminary visit approved and completed. Visitor feedback received positively.', 
 '["phone"]'),

-- 17. Pending Mother's Union Certificate
(5, 8, 'APP001250', 'pending', '2024-01-27 13:45:00', NULL, NULL, 
 NULL, 'pending', NULL, 
 'New Mother\'s Union application. Initial documentation review scheduled.', 
 '["email", "sms"]'),

-- 18. Approved Abasheshakanguhe Certificate
(6, 1, 'APP001251', 'approved', '2024-01-21 11:30:00', '2024-01-25 15:20:00', NULL, 
 'PAY001251', 'paid', '2024-01-24 14:10:00', 
 'Membership verification completed. Payment processed. Certificate being prepared.', 
 '["email"]'),

-- 19. Processing GFS Certificate
(6, 5, 'APP001252', 'processing', '2024-01-25 09:45:00', NULL, NULL, 
 NULL, 'pending', NULL, 
 'GFS membership application under review. Interview scheduled with chapter leader.', 
 '["email", "phone"]'),

-- 20. Recent Pending Application
(1, 1, 'APP001253', 'pending', '2024-01-28 14:20:00', NULL, NULL, 
 NULL, 'pending', NULL, 
 'Fresh application submitted today. Awaiting document verification process.', 
 '["email", "sms"]');

-- =====================================================
-- Additional Recent Applications (Last 7 Days)
-- =====================================================

INSERT INTO `applications` (
    `user_id`, 
    `certificate_type_id`, 
    `application_number`, 
    `status`, 
    `submitted_date`, 
    `notes`, 
    `notification_methods`
) VALUES

-- Recent applications for testing current functionality
(1, 2, 'APP001254', 'pending', NOW() - INTERVAL 2 DAY, 
 'Recent Ebenezer application for testing purposes.', '["email"]'),

(1, 3, 'APP001255', 'pending', NOW() - INTERVAL 1 DAY, 
 'Recent Father\'s Union application submitted yesterday.', '["email", "sms"]'),

(1, 7, 'APP001256', 'pending', NOW() - INTERVAL 3 HOUR, 
 'Marriage certificate application submitted today.', '["email", "sms", "phone"]'),

(2, 6, 'APP001257', 'pending', NOW() - INTERVAL 6 HOUR, 
 'Christian certificate application from this morning.', '["email"]'),

(3, 9, 'APP001258', 'pending', NOW() - INTERVAL 1 HOUR, 
 'Youth Union application submitted one hour ago.', '["sms"]');

-- =====================================================
-- Additional Test Scenarios for Edge Cases
-- =====================================================

INSERT INTO `applications` (
    `user_id`,
    `certificate_type_id`,
    `application_number`,
    `status`,
    `submitted_date`,
    `approved_date`,
    `completed_date`,
    `payment_code`,
    `payment_status`,
    `payment_date`,
    `notes`,
    `notification_methods`
) VALUES

-- Emergency/Urgent Applications
(1, 7, 'APP001259', 'processing', NOW() - INTERVAL 2 HOUR, NULL, NULL,
 NULL, 'pending', NULL,
 'URGENT: Marriage certificate needed for visa application. Expedited processing requested.',
 '["email", "sms", "phone"]'),

-- Applications with Payment Issues
(2, 3, 'APP001260', 'approved', '2024-01-20 10:00:00', '2024-01-23 15:30:00', NULL,
 'PAY001260', 'pending', NULL,
 'Payment failed due to insufficient funds. Applicant notified to retry payment.',
 '["email", "sms"]'),

-- Long Processing Applications
(3, 8, 'APP001261', 'processing', '2023-12-15 09:00:00', NULL, NULL,
 NULL, 'pending', NULL,
 'Complex case requiring additional verification from multiple parishes. Extended processing time.',
 '["email"]'),

-- Applications with Special Requirements
(4, 4, 'APP001262', 'approved', '2024-01-19 14:15:00', '2024-01-24 11:20:00', NULL,
 'PAY001262', 'confirmed', '2024-01-23 16:45:00',
 'Seminary visit approved for theological research purposes. Special access permissions granted.',
 '["email", "phone"]'),

-- Bulk Family Applications
(5, 1, 'APP001263', 'completed', '2024-01-12 11:30:00', '2024-01-16 14:45:00', '2024-01-19 10:20:00',
 'PAY001263', 'confirmed', '2024-01-15 13:10:00',
 'Family membership certificate - Father. Part of family group application.',
 '["email"]'),

(5, 8, 'APP001264', 'completed', '2024-01-12 11:35:00', '2024-01-16 14:50:00', '2024-01-19 10:25:00',
 'PAY001264', 'confirmed', '2024-01-15 13:15:00',
 'Family membership certificate - Mother. Part of family group application.',
 '["email"]'),

-- Applications Requiring Document Updates
(6, 6, 'APP001265', 'processing', '2024-01-21 16:00:00', NULL, NULL,
 NULL, 'pending', NULL,
 'Baptism certificate requires verification from original parish. Awaiting response from Kigali Diocese.',
 '["email", "sms"]');

-- =====================================================
-- Test Data for Different Time Periods
-- =====================================================

-- Applications from different months for reporting tests
INSERT INTO `applications` (
    `user_id`,
    `certificate_type_id`,
    `application_number`,
    `status`,
    `submitted_date`,
    `approved_date`,
    `completed_date`,
    `payment_code`,
    `payment_status`,
    `payment_date`,
    `notes`,
    `notification_methods`
) VALUES

-- December 2023 Applications
(1, 1, 'APP001266', 'completed', '2023-12-05 10:15:00', '2023-12-08 14:30:00', '2023-12-12 11:45:00',
 'PAY001266', 'confirmed', '2023-12-07 16:20:00',
 'December batch - Abasheshakanguhe certificate completed successfully.',
 '["email"]'),

(2, 7, 'APP001267', 'completed', '2023-12-20 09:30:00', '2023-12-23 13:15:00', '2023-12-28 10:00:00',
 'PAY001267', 'confirmed', '2023-12-22 15:45:00',
 'Christmas season marriage certificate. Ceremony held on December 30th.',
 '["email", "sms", "phone"]'),

-- November 2023 Applications
(3, 2, 'APP001268', 'completed', '2023-11-15 14:20:00', '2023-11-18 11:30:00', '2023-11-22 16:15:00',
 'PAY001268', 'confirmed', '2023-11-17 12:45:00',
 'November Ebenezer certificate - member actively participating in community service.',
 '["sms"]'),

-- February 2024 Applications (Future dates for testing)
(1, 9, 'APP001269', 'pending', '2024-02-01 08:00:00', NULL, NULL,
 NULL, 'pending', NULL,
 'Future-dated application for testing date filtering functionality.',
 '["email"]');

-- =====================================================
-- Summary of Complete Test Data:
-- - 35+ total applications
-- - All certificate types (1-9) represented
-- - All status types: pending, processing, approved, completed, rejected
-- - Various time periods: Nov 2023 - Feb 2024
-- - Different users (1-6) for multi-user testing
-- - Edge cases: urgent, payment issues, long processing
-- - Family/group applications
-- - Special requirements and notes
-- - Various notification preferences
-- - Realistic payment codes and progression
-- - Applications suitable for testing:
--   * API endpoints
--   * Status filtering
--   * Date range queries
--   * Payment processing
--   * User-specific data
--   * Reporting functionality
-- =====================================================
