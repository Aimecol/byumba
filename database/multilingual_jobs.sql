-- Multilingual Jobs Data for Diocese of Byumba
-- This script inserts comprehensive job listings with translations in English, French, and Kinyarwanda

-- Clear existing jobs first
DELETE FROM jobs;

-- Insert comprehensive job listings with proper deadlines (future dates)
INSERT INTO jobs (job_category_id, parish_id, job_number, title, description, requirements, salary_range, employment_type, location, application_deadline, contact_email, contact_phone) VALUES

-- Parish Coordinator (Administration)
(1, 1, 'JOB001', 'Parish Coordinator', 'Coordinate parish activities and manage administrative tasks. Assist the parish priest in organizing events, maintaining records, and communicating with parishioners.', 'Bachelor\'s degree in Administration or related field. Minimum 2 years experience in administrative roles. Excellent communication skills in English and Kinyarwanda. Computer literacy required.', 'RWF 150,000 - 200,000', 'full_time', 'St. Mary\'s Parish, Byumba', '2025-07-15', 'stmary@diocesebyumba.rw', '+250788123456'),

-- Religious Education Teacher (Education)
(2, 2, 'JOB002', 'Religious Education Teacher', 'Teach religious education classes for children and adults. Develop curriculum and educational materials for faith formation programs.', 'Degree in Theology, Religious Studies, or Education. Teaching experience preferred. Strong knowledge of Catholic doctrine. Fluent in Kinyarwanda and English.', 'RWF 120,000 - 160,000', 'part_time', 'St. Joseph\'s Parish, Gicumbi', '2025-07-30', 'stjoseph@diocesebyumba.rw', '+250788234567'),

-- Youth Ministry Leader (Pastoral Care)
(3, 3, 'JOB003', 'Youth Ministry Leader', 'Lead youth programs and activities. Organize retreats, camps, and spiritual formation programs for young people aged 13-25.', 'Bachelor\'s degree preferred. Experience in youth ministry or related field. Strong leadership and communication skills. Passion for working with young people.', 'RWF 100,000 - 140,000', 'full_time', 'St. Peter\'s Parish, Rulindo', '2025-08-15', 'stpeter@diocesebyumba.rw', '+250788345678'),

-- Maintenance Technician (Maintenance)
(4, 4, 'JOB004', 'Maintenance Technician', 'Maintain church buildings and facilities. Perform routine maintenance, repairs, and ensure safety standards are met.', 'Technical diploma in electrical, plumbing, or general maintenance. Minimum 3 years experience. Ability to work independently and handle emergency repairs.', 'RWF 80,000 - 120,000', 'full_time', 'Holy Family Parish, Gakenke', '2025-08-30', 'holyfamily@diocesebyumba.rw', '+250788456789'),

-- Community Outreach Coordinator (Social Services)
(6, 5, 'JOB005', 'Community Outreach Coordinator', 'Coordinate social services and community outreach programs. Work with vulnerable populations and manage charity initiatives.', 'Degree in Social Work, Community Development, or related field. Experience in community work. Compassionate and culturally sensitive approach.', 'RWF 130,000 - 170,000', 'full_time', 'St. Paul\'s Parish, Burera', '2025-09-15', 'stpaul@diocesebyumba.rw', '+250788567890'),

-- Diocese Administrative Assistant (Administration)
(1, NULL, 'JOB006', 'Diocese Administrative Assistant', 'Provide administrative support to the Diocese office. Handle correspondence, maintain records, and assist with various diocesan activities.', 'Diploma in Administration or related field. Excellent organizational skills. Proficiency in Microsoft Office. Bilingual (English/Kinyarwanda) required.', 'RWF 110,000 - 150,000', 'full_time', 'Diocese Office, Byumba', '2025-09-30', 'admin@diocesebyumba.rw', '+250788678901');

-- Now create job translations table if it doesn't exist
CREATE TABLE IF NOT EXISTS job_translations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    job_id INT NOT NULL,
    language_code VARCHAR(5) NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    requirements TEXT,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
    FOREIGN KEY (language_code) REFERENCES languages(code),
    UNIQUE KEY unique_job_translation (job_id, language_code)
);

-- Clear existing job translations
DELETE FROM job_translations;

-- Insert job translations for all languages using job_number to find correct IDs
-- Job 1: Parish Coordinator
INSERT INTO job_translations (job_id, language_code, title, description, requirements)
SELECT j.id, 'en', 'Parish Coordinator', 'Coordinate parish activities and manage administrative tasks. Assist the parish priest in organizing events, maintaining records, and communicating with parishioners.', 'Bachelor\'s degree in Administration or related field. Minimum 2 years experience in administrative roles. Excellent communication skills in English and Kinyarwanda. Computer literacy required.'
FROM jobs j WHERE j.job_number = 'JOB001';

INSERT INTO job_translations (job_id, language_code, title, description, requirements)
SELECT j.id, 'fr', 'Coordinateur de Paroisse', 'Coordonner les activités paroissiales et gérer les tâches administratives. Assister le prêtre de la paroisse dans l\'organisation d\'événements, la tenue des registres et la communication avec les paroissiens.', 'Diplôme de licence en Administration ou domaine connexe. Minimum 2 ans d\'expérience dans des rôles administratifs. Excellentes compétences de communication en anglais et en kinyarwanda. Maîtrise de l\'informatique requise.'
FROM jobs j WHERE j.job_number = 'JOB001';

INSERT INTO job_translations (job_id, language_code, title, description, requirements)
SELECT j.id, 'rw', 'Umushingamateka wa Paruwasi', 'Gushinga ibikorwa bya paruwasi no gucunga imirimo y\'ubuyobozi. Gufasha umupadiri wa paruwasi mu gutegura ibirori, kubika inyandiko, no gutumanaho n\'abaturage ba paruwasi.', 'Impamyabumenyi y\'icyiciro cya kabiri mu buyobozi cyangwa mu bindi bice bifitanye isano. Ubunararibonye bw\'amakumi abiri n\'imyaka ibiri mu mirimo y\'ubuyobozi. Ubushobozi bwo gutumanaho neza mu cyongereza no mu kinyarwanda. Ubumenyi bw\'ikoranabuhanga bukenewe.'
FROM jobs j WHERE j.job_number = 'JOB001';

-- Job 2: Religious Education Teacher
INSERT INTO job_translations (job_id, language_code, title, description, requirements)
SELECT j.id, 'en', 'Religious Education Teacher', 'Teach religious education classes for children and adults. Develop curriculum and educational materials for faith formation programs.', 'Degree in Theology, Religious Studies, or Education. Teaching experience preferred. Strong knowledge of Catholic doctrine. Fluent in Kinyarwanda and English.'
FROM jobs j WHERE j.job_number = 'JOB002';

INSERT INTO job_translations (job_id, language_code, title, description, requirements)
SELECT j.id, 'fr', 'Professeur d\'Éducation Religieuse', 'Enseigner les cours d\'éducation religieuse pour enfants et adultes. Développer des programmes et du matériel éducatif pour les programmes de formation de la foi.', 'Diplôme en Théologie, Études Religieuses ou Éducation. Expérience d\'enseignement préférée. Solide connaissance de la doctrine catholique. Maîtrise du kinyarwanda et de l\'anglais.'
FROM jobs j WHERE j.job_number = 'JOB002';

INSERT INTO job_translations (job_id, language_code, title, description, requirements)
SELECT j.id, 'rw', 'Umwarimu w\'Inyigisho z\'Idini', 'Kwigisha amasomo y\'inyigisho z\'idini ku bana n\'abantu bakuru. Gutegura integanyanyigisho n\'ibikoresho by\'uburezi mu gahunda zo kubaka kwizera.', 'Impamyabumenyi mu bya tewolojiya, ubushakashatsi bw\'amadini, cyangwa uburezi. Ubunararibonye bwo kwigisha bukenewe. Ubumenyi bukomeye bw\'inyigisho za gatolika. Kuvuga neza ikinyarwanda n\'icyongereza.'
FROM jobs j WHERE j.job_number = 'JOB002';

-- Job 3: Youth Ministry Leader
INSERT INTO job_translations (job_id, language_code, title, description, requirements)
SELECT j.id, 'en', 'Youth Ministry Leader', 'Lead youth programs and activities. Organize retreats, camps, and spiritual formation programs for young people aged 13-25.', 'Bachelor\'s degree preferred. Experience in youth ministry or related field. Strong leadership and communication skills. Passion for working with young people.'
FROM jobs j WHERE j.job_number = 'JOB003';

INSERT INTO job_translations (job_id, language_code, title, description, requirements)
SELECT j.id, 'fr', 'Responsable du Ministère des Jeunes', 'Diriger les programmes et activités pour les jeunes. Organiser des retraites, des camps et des programmes de formation spirituelle pour les jeunes âgés de 13 à 25 ans.', 'Diplôme de licence préféré. Expérience dans le ministère des jeunes ou domaine connexe. Solides compétences en leadership et communication. Passion pour le travail avec les jeunes.'
FROM jobs j WHERE j.job_number = 'JOB003';

INSERT INTO job_translations (job_id, language_code, title, description, requirements)
SELECT j.id, 'rw', 'Umuyobozi w\'Urubyiruko', 'Kuyobora gahunda n\'ibikorwa by\'urubyiruko. Gutegura amahugurwa, inkambi, n\'amahugurwa yo kubaka umwuka ku rubyiruko rw\'imyaka 13-25.', 'Impamyabumenyi y\'icyiciro cya kabiri ikenewe. Ubunararibonye mu bikorwa by\'urubyiruko cyangwa mu bindi bice bifitanye isano. Ubushobozi bukomeye bwo kuyobora no gutumanaho. Urukundo rwo gukorana n\'urubyiruko.'
FROM jobs j WHERE j.job_number = 'JOB003';

-- Job 4: Maintenance Technician
INSERT INTO job_translations (job_id, language_code, title, description, requirements)
SELECT j.id, 'en', 'Maintenance Technician', 'Maintain church buildings and facilities. Perform routine maintenance, repairs, and ensure safety standards are met.', 'Technical diploma in electrical, plumbing, or general maintenance. Minimum 3 years experience. Ability to work independently and handle emergency repairs.'
FROM jobs j WHERE j.job_number = 'JOB004';

INSERT INTO job_translations (job_id, language_code, title, description, requirements)
SELECT j.id, 'fr', 'Technicien de Maintenance', 'Maintenir les bâtiments et installations de l\'église. Effectuer la maintenance de routine, les réparations et s\'assurer que les normes de sécurité sont respectées.', 'Diplôme technique en électricité, plomberie ou maintenance générale. Minimum 3 ans d\'expérience. Capacité à travailler de manière indépendante et à gérer les réparations d\'urgence.'
FROM jobs j WHERE j.job_number = 'JOB004';

INSERT INTO job_translations (job_id, language_code, title, description, requirements)
SELECT j.id, 'rw', 'Umukozi w\'Ubusugire', 'Kubungabunga inyubako n\'ibikoresho by\'itorero. Gukora ubusugire busanzwe, gusana, no kwemeza ko amahame y\'umutekano yubahirizwa.', 'Impamyabumenyi y\'ikoranabuhanga mu mashanyarazi, amazi, cyangwa ubusugire rusange. Byibuze imyaka 3 y\'ubunararibonye. Ubushobozi bwo gukora wenyine no gukemura ibibazo by\'ihutirwa.'
FROM jobs j WHERE j.job_number = 'JOB004';

-- Job 5: Community Outreach Coordinator
INSERT INTO job_translations (job_id, language_code, title, description, requirements)
SELECT j.id, 'en', 'Community Outreach Coordinator', 'Coordinate social services and community outreach programs. Work with vulnerable populations and manage charity initiatives.', 'Degree in Social Work, Community Development, or related field. Experience in community work. Compassionate and culturally sensitive approach.'
FROM jobs j WHERE j.job_number = 'JOB005';

INSERT INTO job_translations (job_id, language_code, title, description, requirements)
SELECT j.id, 'fr', 'Coordinateur de Sensibilisation Communautaire', 'Coordonner les services sociaux et les programmes de sensibilisation communautaire. Travailler avec les populations vulnérables et gérer les initiatives caritatives.', 'Diplôme en Travail Social, Développement Communautaire ou domaine connexe. Expérience dans le travail communautaire. Approche compatissante et culturellement sensible.'
FROM jobs j WHERE j.job_number = 'JOB005';

INSERT INTO job_translations (job_id, language_code, title, description, requirements)
SELECT j.id, 'rw', 'Umushingamateka w\'Ibikorwa by\'Abaturage', 'Gushinga serivisi z\'imibereho myiza n\'amahugurwa y\'abaturage. Gukorana n\'abantu bafite ibibazo no gucunga amahugurwa y\'ubufasha.', 'Impamyabumenyi mu bikorwa by\'imibereho myiza, iterambere ry\'abaturage, cyangwa mu bindi bice bifitanye isano. Ubunararibonye mu bikorwa by\'abaturage. Uburyo bw\'imbabazi kandi bukubana n\'umuco.'
FROM jobs j WHERE j.job_number = 'JOB005';

-- Job 6: Diocese Administrative Assistant
INSERT INTO job_translations (job_id, language_code, title, description, requirements)
SELECT j.id, 'en', 'Diocese Administrative Assistant', 'Provide administrative support to the Diocese office. Handle correspondence, maintain records, and assist with various diocesan activities.', 'Diploma in Administration or related field. Excellent organizational skills. Proficiency in Microsoft Office. Bilingual (English/Kinyarwanda) required.'
FROM jobs j WHERE j.job_number = 'JOB006';

INSERT INTO job_translations (job_id, language_code, title, description, requirements)
SELECT j.id, 'fr', 'Assistant Administratif du Diocèse', 'Fournir un soutien administratif au bureau du Diocèse. Gérer la correspondance, maintenir les dossiers et assister dans diverses activités diocésaines.', 'Diplôme en Administration ou domaine connexe. Excellentes compétences organisationnelles. Maîtrise de Microsoft Office. Bilingue (Anglais/Kinyarwanda) requis.'
FROM jobs j WHERE j.job_number = 'JOB006';

INSERT INTO job_translations (job_id, language_code, title, description, requirements)
SELECT j.id, 'rw', 'Umufasha w\'Ubuyobozi wa Diyosezi', 'Gutanga ubufasha bw\'ubuyobozi ku biro bya diyosezi. Gucunga inyandiko, kubika inyandiko, no gufasha mu bikorwa bitandukanye bya diyosezi.', 'Impamyabumenyi mu buyobozi cyangwa mu bindi bice bifitanye isano. Ubushobozi bukomeye bwo gutegura. Ubumenyi bwa Microsoft Office. Kuvuga indimi ebyiri (Icyongereza/Ikinyarwanda) bikenewe.'
FROM jobs j WHERE j.job_number = 'JOB006';
