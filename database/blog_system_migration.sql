-- Blog System Migration Script
-- This script adds missing fields and inserts blog post data for the Diocese of Byumba website

-- Add missing fields to blog_posts table
ALTER TABLE `blog_posts` 
ADD COLUMN `reading_time` varchar(20) DEFAULT NULL AFTER `excerpt`,
ADD COLUMN `seo_description` text DEFAULT NULL AFTER `content`,
ADD COLUMN `seo_keywords` text DEFAULT NULL AFTER `seo_description`,
ADD COLUMN `tags` text DEFAULT NULL AFTER `seo_keywords`;

-- Create blog_tags table for better tag management
CREATE TABLE IF NOT EXISTS `blog_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag_name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `tag_name` (`tag_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create blog_post_tags junction table for many-to-many relationship
CREATE TABLE IF NOT EXISTS `blog_post_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `blog_post_id` int(11) NOT NULL,
  `blog_tag_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_post_tag` (`blog_post_id`, `blog_tag_id`),
  KEY `blog_post_id` (`blog_post_id`),
  KEY `blog_tag_id` (`blog_tag_id`),
  CONSTRAINT `blog_post_tags_ibfk_1` FOREIGN KEY (`blog_post_id`) REFERENCES `blog_posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `blog_post_tags_ibfk_2` FOREIGN KEY (`blog_tag_id`) REFERENCES `blog_tags` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add new blog categories for our posts
INSERT INTO `blog_categories` (`category_key`, `icon`, `is_active`) VALUES
('spiritual-reflections', 'fa-cross', 1),
('youth-ministry', 'fa-users', 1),
('family-life', 'fa-heart', 1)
ON DUPLICATE KEY UPDATE `icon` = VALUES(`icon`);

-- Insert blog tags
INSERT INTO `blog_tags` (`tag_name`, `slug`) VALUES
('Advent', 'advent'),
('Reflection', 'reflection'),
('Prayer', 'prayer'),
('Spiritual Growth', 'spiritual-growth'),
('Youth Ministry', 'youth-ministry'),
('Christmas', 'christmas'),
('Concert', 'concert'),
('Community', 'community'),
('Outreach', 'outreach'),
('Charity', 'charity'),
('Support', 'support'),
('Mass Schedule', 'mass-schedule'),
('Announcements', 'announcements'),
('Worship', 'worship'),
('Parish Life', 'parish-life'),
('Leadership', 'leadership'),
('Training', 'training'),
('Community Development', 'community-development'),
('Daily Practice', 'daily-practice'),
('Faith', 'faith'),
('Seminary', 'seminary'),
('Ordination', 'ordination'),
('Priests', 'priests'),
('Graduation', 'graduation'),
('Marriage', 'marriage'),
('Family Life', 'family-life'),
('Preparation', 'preparation'),
('Sacrament', 'sacrament');

-- Clear existing blog posts to avoid conflicts
DELETE FROM `blog_posts` WHERE `slug` IN (
    'advent-reflection', 'youth-concert', 'community-outreach', 
    'mass-schedule', 'youth-leadership', 'power-of-prayer', 
    'seminary-graduation', 'marriage-preparation'
);

-- Insert our 8 blog posts
INSERT INTO `blog_posts` (
    `blog_category_id`, `post_number`, `title`, `slug`, `excerpt`, `reading_time`,
    `content`, `seo_description`, `seo_keywords`, `is_featured`, `is_published`, 
    `published_at`, `views_count`, `created_at`, `updated_at`
) VALUES
-- 1. Advent Reflection
(
    (SELECT id FROM blog_categories WHERE category_key = 'spiritual-reflections'),
    'POST006', 
    'Advent: A Time of Preparation and Reflection',
    'advent-reflection',
    'During this holy season of Advent, we are called to prepare our hearts for the coming of Christ. Let us reflect on the virtues of patience, hope, and love as we journey toward Christmas.',
    '5 min read',
    '<p>During this holy season of Advent, we are called to prepare our hearts for the coming of Christ. Let us reflect on the virtues of patience, hope, and love as we journey toward Christmas.</p><h2>The Meaning of Advent</h2><p>Advent, from the Latin word "adventus" meaning "coming," is a season of preparation and anticipation. It is a time when we prepare not only for the celebration of Christ\'s birth but also for His second coming. This dual focus makes Advent a season of both joy and solemn reflection.</p><blockquote>"The people walking in darkness have seen a great light; on those living in the land of deep darkness a light has dawned." - Isaiah 9:2</blockquote><h3>Preparing Our Hearts</h3><p>As we light the candles of the Advent wreath each week, we are reminded of the light that Christ brings into our world. The purple candles represent penance and preparation, while the pink candle symbolizes joy. The white candle in the center, lit on Christmas Day, represents Christ himself - the light of the world.</p><p>This preparation involves more than external observances. We are called to examine our hearts, to repent of our sins, and to make room for Christ in our lives. Just as Mary said "yes" to God\'s plan, we too must be open to God\'s will in our lives.</p><h3>The Virtue of Waiting</h3><p>In our fast-paced world, the concept of waiting has become almost foreign to us. We want everything instantly. But Advent teaches us the virtue of patient waiting. The Israelites waited centuries for the Messiah. Mary waited nine months for Jesus to be born. We too must learn to wait with hope and trust in God\'s timing.</p><p>This waiting is not passive but active. It involves prayer, fasting, almsgiving, and acts of charity. It means being attentive to the needs of others and responding with compassion and love.</p><h2>Living Advent Today</h2><p>How can we live the spirit of Advent in our daily lives? Here are some practical suggestions:</p><p><strong>Prayer:</strong> Set aside time each day for prayer and reflection. Consider using an Advent devotional or participating in special Advent services at your parish.</p><p><strong>Simplicity:</strong> In a season often marked by commercial excess, choose simplicity. Focus on the true meaning of Christmas rather than material gifts.</p><p><strong>Service:</strong> Look for opportunities to serve others, especially the poor and marginalized. Remember that Christ comes to us in the least of our brothers and sisters.</p><p><strong>Hope:</strong> In times of darkness and difficulty, hold fast to the hope that Christ brings. His light shines in the darkness, and the darkness cannot overcome it.</p><h3>A Season of Joy</h3><p>Despite its penitential character, Advent is ultimately a season of joy. We rejoice because God has not abandoned us but has come to dwell among us. We rejoice because the promises of God are being fulfilled. We rejoice because love has conquered fear, light has overcome darkness, and life has triumphed over death.</p><p>As we continue our Advent journey, let us open our hearts to receive the Christ child. Let us prepare room for Him not only in our homes but in our hearts. May this Advent season be a time of true conversion and spiritual renewal for all of us.</p><p>May God bless you and your families during this holy season of Advent.</p>',
    'Reflect on the meaning of Advent and how to prepare our hearts for the coming of Christ during this holy season.',
    'Advent, Christmas preparation, spiritual reflection, Catholic faith, Diocese of Byumba',
    1, 1, '2024-12-15 08:00:00', 0, NOW(), NOW()
),
-- 2. Youth Concert
(
    (SELECT id FROM blog_categories WHERE category_key = 'events'),
    'POST007',
    'Youth Christmas Concert: December 23rd',
    'youth-concert',
    'Join us for a spectacular Christmas concert featuring the talented youth choirs from across our diocese. The event will take place at the Cathedral on December 23rd at 7:00 PM.',
    '3 min read',
    '<p>Join us for a spectacular Christmas concert featuring the talented youth choirs from across our diocese. The event will take place at the Cathedral on December 23rd at 7:00 PM.</p><h2>A Celebration of Faith and Music</h2><p>Our annual Youth Christmas Concert has become a beloved tradition in the Diocese of Byumba. This year\'s concert promises to be our most spectacular yet, featuring over 200 young voices from parishes throughout our diocese.</p><h3>Program Highlights</h3><p>The evening will feature a diverse program of Christmas carols, both traditional and contemporary, performed in English, Kinyarwanda, and French. Special performances will include:</p><p>• Traditional Rwandan Christmas songs arranged for choir<br>• Contemporary Christian music with instrumental accompaniment<br>• A special nativity tableau with music<br>• Solo performances by our most talented young singers</p><blockquote>"Let the message of Christ dwell among you richly as you teach and admonish one another with all wisdom through psalms, hymns, and songs from the Spirit, singing to God with gratitude in your hearts." - Colossians 3:16</blockquote><h3>Participating Choirs</h3><p>We are blessed to have choirs from the following parishes participating in this year\'s concert:</p><p>• St. Joseph Parish Youth Choir<br>• Sacred Heart Parish Children\'s Choir<br>• St. Mary\'s Parish Young Adults Choir<br>• Cathedral Youth Ensemble<br>• St. Peter\'s Parish Mixed Choir</p><h2>Event Details</h2><p><strong>Date:</strong> December 23rd, 2024<br><strong>Time:</strong> 7:00 PM<br><strong>Venue:</strong> Cathedral of the Diocese of Byumba<br><strong>Admission:</strong> Free (donations welcome)</p><p>The concert is free and open to all members of the community. However, we will be accepting donations to support our youth ministry programs throughout the diocese.</p><h3>Special Recognition</h3><p>This year\'s concert will also include a special recognition ceremony for our youth choir directors and volunteers who have dedicated countless hours to nurturing the musical talents of our young people.</p><p>We encourage all families to attend this joyful celebration as we prepare our hearts for the birth of our Savior through the gift of music.</p>',
    'Join us for a spectacular Christmas concert featuring talented youth choirs from across the Diocese of Byumba.',
    'youth concert, Christmas music, Diocese of Byumba, youth ministry, cathedral concert',
    1, 1, '2024-12-10 12:00:00', 0, NOW(), NOW()
),
-- 3. Community Outreach
(
    (SELECT id FROM blog_categories WHERE category_key = 'community'),
    'POST008',
    'Community Outreach: Supporting Local Families',
    'community-outreach',
    'Our recent community outreach program has successfully provided assistance to over 200 families in need. Thanks to the generous donations from parishioners and the dedicated work of our volunteers.',
    '4 min read',
    '<p>Our recent community outreach program has successfully provided assistance to over 200 families in need. Thanks to the generous donations from parishioners and the dedicated work of our volunteers, we have been able to make a significant impact in our local community.</p><h2>Program Overview</h2><p>The Diocese of Byumba Community Outreach Program was launched in response to the growing needs of families in our region. Through careful assessment and coordination with local leaders, we identified families facing various challenges including food insecurity, lack of basic necessities, and educational support needs.</p><blockquote>"Whoever is kind to the poor lends to the Lord, and he will reward them for what they have done." - Proverbs 19:17</blockquote><h3>What We Accomplished</h3><p>Over the past three months, our outreach program has achieved remarkable results:</p><p>• <strong>Food Distribution:</strong> Provided monthly food packages to 200 families, ensuring basic nutrition for over 800 individuals<br>• <strong>Educational Support:</strong> Supplied school materials and uniforms to 150 children<br>• <strong>Medical Assistance:</strong> Facilitated access to healthcare for 75 families<br>• <strong>Skills Training:</strong> Organized vocational training sessions for 50 adults</p><h3>Community Impact</h3><p>The impact of this program extends far beyond material assistance. We have witnessed families regaining hope, children returning to school, and community bonds strengthening. Many beneficiaries have expressed their gratitude and some have even joined as volunteers to help others.</p><p>Mrs. Mukamana, a mother of four who received assistance, shared: "This program came at the right time when we had lost hope. Now my children are back in school, and I have learned new skills that help me support my family."</p><h2>Volunteer Appreciation</h2><p>We extend our heartfelt gratitude to the over 30 volunteers who dedicated their time and energy to make this program successful. Their commitment to serving others reflects the true spirit of Christian charity.</p><h3>Looking Forward</h3><p>Building on this success, we plan to expand the program in 2025. Our goals include:</p><p>• Reaching 300 additional families<br>• Establishing a permanent community center<br>• Launching microfinance initiatives<br>• Creating youth mentorship programs</p><p>We invite all parishioners to continue supporting this vital ministry through prayers, donations, and volunteer service. Together, we can build a stronger, more caring community.</p>',
    'Learn about our successful community outreach program that has provided assistance to over 200 families in need.',
    'community outreach, charity, Diocese of Byumba, family support, volunteer work',
    0, 1, '2024-12-12 10:00:00', 0, NOW(), NOW()
),
-- 4. Mass Schedule
(
    (SELECT id FROM blog_categories WHERE category_key = 'announcements'),
    'POST009',
    'New Mass Schedule Effective January 2025',
    'mass-schedule',
    'Please note the updated Mass schedule that will take effect from January 1st, 2025. The changes are designed to better serve our growing community and accommodate the diverse needs of our parishioners.',
    '3 min read',
    '<p>Please note the updated Mass schedule that will take effect from January 1st, 2025. The changes are designed to better serve our growing community and accommodate the diverse needs of our parishioners.</p><h2>New Mass Schedule</h2><p>After careful consideration and consultation with parish councils across the diocese, we are implementing the following changes to our Mass schedule:</p><h3>Weekday Masses</h3><p><strong>Monday to Friday:</strong><br>• 6:00 AM - Early Morning Mass (Cathedral)<br>• 12:00 PM - Midday Mass (Cathedral)<br>• 6:00 PM - Evening Mass (Cathedral)</p><p><strong>Saturday:</strong><br>• 6:00 AM - Morning Mass (Cathedral)<br>• 6:00 PM - Vigil Mass (Cathedral and all parishes)</p><h3>Sunday Masses</h3><p><strong>Cathedral:</strong><br>• 6:00 AM - Early Mass<br>• 8:00 AM - Family Mass<br>• 10:00 AM - Main Mass (with choir)<br>• 12:00 PM - Youth Mass<br>• 6:00 PM - Evening Mass</p><p><strong>Parish Churches:</strong><br>• 7:00 AM - Morning Mass<br>• 9:00 AM - Main Mass<br>• 11:00 AM - Second Mass (where needed)<br>• 5:00 PM - Evening Mass</p><blockquote>"They devoted themselves to the apostles\' teaching and to fellowship, to the breaking of bread and to prayer." - Acts 2:42</blockquote><h2>Special Considerations</h2><h3>Language Options</h3><p>To better serve our multilingual community:</p><p>• Kinyarwanda: All morning Masses (6:00 AM, 7:00 AM, 8:00 AM)<br>• English: 10:00 AM Sunday Mass at Cathedral<br>• French: 12:00 PM Sunday Mass at Cathedral<br>• Mixed languages: Evening Masses</p><h3>Accessibility</h3><p>We have ensured that all Mass times are accessible to people with disabilities. Sign language interpretation will be available for the 10:00 AM Sunday Mass at the Cathedral upon request.</p><h3>Transportation</h3><p>Parish buses will continue to operate with adjusted schedules to accommodate the new Mass times. Please contact your parish office for updated transportation schedules.</p><h2>Reasons for Changes</h2><p>These schedule adjustments were made based on:</p><p>• Feedback from parishioner surveys<br>• Attendance patterns analysis<br>• Priest availability optimization<br>• Community growth accommodation<br>• Better work-life balance for families</p><h3>Transition Period</h3><p>We understand that schedule changes can be challenging. During January 2025, we will have volunteers available to help guide parishioners and answer questions about the new schedule.</p><p>For questions about the new Mass schedule, please contact your parish office or the diocesan office at +250 788 123 456.</p><p>We thank you for your patience and understanding as we implement these changes to better serve our faith community.</p>',
    'New Mass schedule for the Diocese of Byumba effective January 2025, designed to better serve our growing community.',
    'Mass schedule, Diocese of Byumba, worship times, parish life, Catholic Mass',
    0, 1, '2024-12-10 09:00:00', 0, NOW(), NOW()
),
-- 5. Youth Leadership
(
    (SELECT id FROM blog_categories WHERE category_key = 'youth-ministry'),
    'POST010',
    'Youth Leadership Training Program Launch',
    'youth-leadership',
    'We are excited to announce the launch of our Youth Leadership Training Program, designed to empower young Catholics to become leaders in their communities and active participants in the mission of the Church.',
    '5 min read',
    '<p>We are excited to announce the launch of our Youth Leadership Training Program, designed to empower young Catholics to become leaders in their communities and active participants in the mission of the Church.</p><h2>Program Vision</h2><p>The Youth Leadership Training Program aims to develop the next generation of Catholic leaders who will serve both the Church and society with integrity, compassion, and wisdom. This comprehensive program combines spiritual formation, practical skills development, and community service opportunities.</p><blockquote>"Don\'t let anyone look down on you because you are young, but set an example for the believers in speech, in conduct, in love, in faith and in purity." - 1 Timothy 4:12</blockquote><h3>Program Components</h3><p>The six-month program includes four main components:</p><p><strong>1. Spiritual Formation (Monthly Retreats)</strong><br>• Personal prayer and meditation techniques<br>• Scripture study and reflection<br>• Sacramental life deepening<br>• Discernment and vocation exploration</p><p><strong>2. Leadership Skills Development</strong><br>• Communication and public speaking<br>• Team building and collaboration<br>• Project management and organization<br>• Conflict resolution and mediation</p><p><strong>3. Community Service Projects</strong><br>• Local outreach initiatives<br>• Environmental conservation projects<br>• Educational support programs<br>• Healthcare awareness campaigns</p><p><strong>4. Mentorship and Networking</strong><br>• Pairing with experienced Catholic leaders<br>• Peer support groups<br>• Alumni network access<br>• Career guidance and counseling</p><h2>Eligibility and Application</h2><h3>Who Can Apply</h3><p>The program is open to young Catholics aged 18-30 who demonstrate:</p><p>• Active participation in parish life<br>• Commitment to personal growth and service<br>• Leadership potential and motivation<br>• Willingness to dedicate time to the program</p><h3>Application Process</h3><p>Interested candidates should submit:</p><p>• Completed application form<br>• Personal statement (500 words)<br>• Two letters of recommendation<br>• Parish priest endorsement</p><p><strong>Application Deadline:</strong> January 15, 2025<br><strong>Program Start Date:</strong> February 1, 2025<br><strong>Program Duration:</strong> 6 months</p><h2>Program Schedule</h2><p>The program meets twice monthly with the following schedule:</p><p><strong>First Saturday of each month:</strong> Full-day retreat (9:00 AM - 5:00 PM)<br><strong>Third Saturday of each month:</strong> Skills workshop (2:00 PM - 6:00 PM)<br><strong>Ongoing:</strong> Community service projects and mentorship meetings</p><h3>Graduation and Certification</h3><p>Participants who successfully complete the program will receive:</p><p>• Certificate of completion from the Diocese<br>• Leadership portfolio documentation<br>• Recommendation letters for future opportunities<br>• Invitation to join the Alumni Leadership Network</p><h2>Expected Outcomes</h2><p>Upon completion, participants will be equipped to:</p><p>• Lead parish youth groups and ministries<br>• Organize community development projects<br>• Serve as catechists and youth mentors<br>• Participate in diocesan leadership roles<br>• Contribute to social justice initiatives</p><h3>Long-term Impact</h3><p>This program is part of our broader vision to create a generation of young Catholic leaders who will:</p><p>• Strengthen parish communities<br>• Address social challenges with Gospel values<br>• Bridge generational gaps in the Church<br>• Promote interfaith dialogue and cooperation<br>• Contribute to national development</p><p>For more information and application forms, please contact the Youth Ministry Office at youth@diocesebyumba.rw or visit your parish office.</p><p>We look forward to walking with our young people on this journey of leadership development and spiritual growth.</p>',
    'Join our Youth Leadership Training Program designed to empower young Catholics to become leaders in their communities.',
    'youth leadership, Catholic youth, training program, Diocese of Byumba, community development',
    0, 1, '2024-12-08 14:00:00', 0, NOW(), NOW()
),
-- 6. Power of Prayer
(
    (SELECT id FROM blog_categories WHERE category_key = 'spiritual-reflections'),
    'POST011',
    'The Power of Prayer in Daily Life',
    'power-of-prayer',
    'Prayer is not just a ritual but a conversation with God. In our busy lives, finding time for meaningful prayer can transform our relationship with the Divine and bring peace to our daily struggles.',
    '6 min read',
    '<p>Prayer is not just a ritual but a conversation with God. In our busy lives, finding time for meaningful prayer can transform our relationship with the Divine and bring peace to our daily struggles.</p><h2>Understanding Prayer</h2><p>Prayer is fundamentally about relationship. It is our way of connecting with God, sharing our joys and sorrows, seeking guidance, and expressing gratitude. Too often, we think of prayer as asking God for things, but true prayer encompasses much more.</p><blockquote>"Rejoice always, pray continually, give thanks in all circumstances; for this is God\'s will for you in Christ Jesus." - 1 Thessalonians 5:16-18</blockquote><h3>Types of Prayer</h3><p>The Catholic tradition recognizes several forms of prayer, each serving a unique purpose in our spiritual journey:</p><p><strong>Adoration:</strong> Recognizing God\'s greatness and our dependence on Him<br><strong>Contrition:</strong> Expressing sorrow for our sins and seeking forgiveness<br><strong>Thanksgiving:</strong> Acknowledging God\'s blessings in our lives<br><strong>Supplication:</strong> Asking for God\'s help for ourselves and others</p><h2>Prayer in Daily Life</h2><p>Integrating prayer into our daily routine doesn\'t require hours of formal meditation. Here are practical ways to maintain a prayerful spirit throughout the day:</p><h3>Morning Prayer</h3><p>Begin each day by offering it to God. A simple morning prayer can set the tone for the entire day:</p><p><em>"Lord, I offer you this day. Guide my thoughts, words, and actions. Help me to serve you and others with love. Amen."</em></p><h3>Prayer Throughout the Day</h3><p>• <strong>Before meals:</strong> Thank God for the food and those who prepared it<br>• <strong>During work:</strong> Offer brief prayers for guidance and patience<br>• <strong>In traffic or waiting:</strong> Use these moments for quiet reflection<br>• <strong>Before important decisions:</strong> Seek God\'s wisdom and guidance</p><h3>Evening Prayer</h3><p>End the day with gratitude and reflection:</p><p>• Thank God for the day\'s blessings<br>• Ask forgiveness for any shortcomings<br>• Pray for loved ones and those in need<br>• Entrust your rest to God\'s care</p><h2>Overcoming Prayer Challenges</h2><h3>When Prayer Feels Dry</h3><p>Everyone experiences periods when prayer feels difficult or unrewarding. This is normal and doesn\'t mean God isn\'t listening. During these times:</p><p>• Continue praying even when you don\'t feel like it<br>• Try different forms of prayer (Scripture reading, meditation, singing)<br>• Remember that God\'s presence isn\'t dependent on our feelings<br>• Seek spiritual direction or guidance from a trusted mentor</p><h3>Dealing with Distractions</h3><p>A wandering mind during prayer is common. When distractions arise:</p><p>• Gently return your attention to God<br>• Don\'t be discouraged by distractions<br>• Use a prayer book or rosary to help focus<br>• Find a quiet space dedicated to prayer</p><h2>The Fruits of Prayer</h2><p>Regular prayer brings numerous benefits to our spiritual and emotional well-being:</p><h3>Inner Peace</h3><p>Prayer helps us find calm in the midst of life\'s storms. When we place our concerns in God\'s hands, we experience a peace that surpasses understanding.</p><h3>Clarity and Wisdom</h3><p>Through prayer, we gain perspective on our problems and receive guidance for difficult decisions. God\'s wisdom often comes through quiet reflection and prayer.</p><h3>Strength for Challenges</h3><p>Prayer provides spiritual strength to face difficulties. When we feel overwhelmed, prayer reminds us that we are not alone.</p><h3>Deeper Relationships</h3><p>As our relationship with God deepens through prayer, our relationships with others also improve. Prayer teaches us compassion, forgiveness, and love.</p><h2>Community Prayer</h2><p>While personal prayer is essential, communal prayer has its own special power. Jesus promised, "Where two or three gather in my name, there am I with them" (Matthew 18:20).</p><p>Participate in:</p><p>• Sunday Mass and weekday liturgies<br>• Parish prayer groups and Bible studies<br>• Family prayer time<br>• Rosary groups and devotional prayers</p><h3>Teaching Children to Pray</h3><p>Parents and catechists play a crucial role in teaching young people to pray:</p><p>• Model regular prayer in daily life<br>• Teach simple prayers appropriate for their age<br>• Encourage spontaneous prayer and conversation with God<br>• Create family prayer traditions and rituals</p><h2>Conclusion</h2><p>Prayer is not a burden but a gift. It is our direct line to the Creator of the universe, who loves us unconditionally. Whether in times of joy or sorrow, success or failure, prayer keeps us connected to the source of all life and love.</p><p>I encourage you to make prayer a priority in your daily life. Start small, be consistent, and trust that God hears every prayer, even when the answer isn\'t what we expect.</p><p>May your prayer life be a source of strength, peace, and joy as you journey closer to God each day.</p>',
    'Discover the transformative power of prayer in daily life and learn practical ways to deepen your relationship with God.',
    'prayer, spiritual life, Catholic prayer, daily prayer, Diocese of Byumba, spiritual growth',
    0, 1, '2024-12-05 11:00:00', 0, NOW(), NOW()
),
-- 7. Seminary Graduation
(
    (SELECT id FROM blog_categories WHERE category_key = 'events'),
    'POST012',
    'Seminary Graduation Ceremony 2024',
    'seminary-graduation',
    'We celebrate the ordination of five new priests who completed their seminary training. The ceremony was presided over by Bishop Gahamanyi at the Cathedral, marking a significant milestone for our diocese.',
    '4 min read',
    '<p>We celebrate the ordination of five new priests who completed their seminary training. The ceremony was presided over by Bishop Gahamanyi at the Cathedral, marking a significant milestone for our diocese.</p><h2>A Joyous Celebration</h2><p>On December 1st, 2024, the Diocese of Byumba witnessed a momentous occasion as five seminarians were ordained to the priesthood. The ordination ceremony, held at the Cathedral of the Diocese of Byumba, was attended by hundreds of faithful, family members, and clergy from across the region.</p><blockquote>"Before I formed you in the womb I knew you, before you were born I set you apart; I appointed you as a prophet to the nations." - Jeremiah 1:5</blockquote><h3>The New Priests</h3><p>We are blessed to welcome these five new priests to our diocesan family:</p><p><strong>Rev. Fr. Emmanuel Nkurunziza</strong><br>Age: 28 | Home Parish: St. Joseph, Gicumbi<br>Assignment: Assistant Pastor, Sacred Heart Parish</p><p><strong>Rev. Fr. Jean Baptiste Uwimana</strong><br>Age: 30 | Home Parish: St. Mary, Byumba<br>Assignment: Assistant Pastor, St. Peter Parish</p><p><strong>Rev. Fr. Claude Habimana</strong><br>Age: 29 | Home Parish: St. Paul, Rulindo<br>Assignment: Chaplain, Byumba Hospital</p><p><strong>Rev. Fr. Innocent Mukamana</strong><br>Age: 27 | Home Parish: Holy Trinity, Gicumbi<br>Assignment: Assistant Pastor, St. Joseph Parish</p><p><strong>Rev. Fr. Damascene Niyonsenga</strong><br>Age: 31 | Home Parish: St. Francis, Byumba<br>Assignment: Director of Youth Ministry</p><h2>Seminary Formation Journey</h2><p>These five priests completed their eight-year formation program at the Major Seminary of Nyakibanda. Their journey included:</p><h3>Academic Formation</h3><p>• Bachelor\'s degree in Philosophy<br>• Bachelor\'s degree in Theology<br>• Specialized courses in pastoral ministry<br>• Language studies (Latin, English, French)</p><h3>Spiritual Formation</h3><p>• Daily prayer and meditation<br>• Regular spiritual direction<br>• Annual retreats and spiritual exercises<br>• Sacramental life and liturgical training</p><h3>Pastoral Formation</h3><p>• Parish internships during summer breaks<br>• Hospital and prison chaplaincy experience<br>• Youth ministry and catechetical training<br>• Community service projects</p><h3>Human Formation</h3><p>• Personal development and maturity<br>• Communication and leadership skills<br>• Cultural sensitivity and adaptation<br>• Physical and emotional well-being</p><h2>Ordination Ceremony Highlights</h2><p>The ordination ceremony was a beautiful celebration of faith and vocation:</p><h3>Liturgical Elements</h3><p>• Presentation of candidates by the seminary rector<br>• Homily by Bishop Gahamanyi on priestly vocation<br>• Litany of Saints sung by the cathedral choir<br>• Laying on of hands by the bishop and priests<br>• Anointing of hands with sacred chrism<br>• Presentation of chalice and paten</p><h3>Special Moments</h3><p>The ceremony included several touching moments:</p><p>• Prostration of the candidates during the Litany of Saints<br>• First blessing given by each new priest to their parents<br>• Fraternal kiss of peace from fellow priests<br>• Thanksgiving hymns sung by parish choirs</p><h2>Community Response</h2><p>The faithful of the Diocese of Byumba have welcomed these new priests with great joy and enthusiasm. Many parishes have already scheduled special Masses to welcome their new assistant pastors.</p><h3>Family Testimonies</h3><p>Mrs. Mukamana, mother of Fr. Innocent, shared: "We are so proud of our son\'s dedication to serving God and the Church. We have prayed for this day for many years."</p><p>Mr. Nkurunziza, father of Fr. Emmanuel, added: "Seeing our son ordained as a priest is the greatest blessing our family could receive. We entrust him to the service of God\'s people."</p><h2>Looking Forward</h2><p>These five new priests bring the total number of diocesan priests to 45, helping to address the pastoral needs of our growing Catholic community. They will begin their ministry assignments on January 1st, 2025.</p><h3>Continued Formation</h3><p>The new priests will participate in ongoing formation programs including:</p><p>• Monthly priests\' meetings and conferences<br>• Annual retreats and spiritual renewal<br>• Continuing education opportunities<br>• Pastoral skills development workshops</p><h3>Prayer Support</h3><p>We ask all faithful to continue praying for these new priests as they begin their ministry. We also encourage young men to consider the call to priesthood and to contact our vocations director for more information.</p><p>May God bless our new priests and grant them wisdom, strength, and joy in their service to the Church.</p>',
    'Celebrate the ordination of five new priests in the Diocese of Byumba and learn about their seminary formation journey.',
    'priest ordination, seminary graduation, Diocese of Byumba, Catholic priests, vocations',
    0, 1, '2024-12-03 15:00:00', 0, NOW(), NOW()
),
-- 8. Marriage Preparation
(
    (SELECT id FROM blog_categories WHERE category_key = 'family-life'),
    'POST013',
    'Marriage Preparation Course: January 2025',
    'marriage-preparation',
    'Couples planning to marry in 2025 are invited to attend our comprehensive marriage preparation course. The program covers communication, finances, and spiritual growth in marriage.',
    '4 min read',
    '<p>Couples planning to marry in 2025 are invited to attend our comprehensive marriage preparation course. The program covers communication, finances, and spiritual growth in marriage.</p><h2>Marriage Preparation Program</h2><p>The Diocese of Byumba is pleased to announce our comprehensive Marriage Preparation Course for couples planning to celebrate the Sacrament of Matrimony in 2025. This program is designed to help couples build a strong foundation for their married life together.</p><blockquote>"Therefore what God has joined together, let no one separate." - Mark 10:9</blockquote><h3>Program Overview</h3><p>Our marriage preparation program is a requirement for all couples seeking to marry in the Catholic Church within our diocese. The course provides essential tools and knowledge for building a successful Christian marriage.</p><p><strong>Duration:</strong> 6 weeks (12 sessions)<br><strong>Schedule:</strong> Saturdays, 9:00 AM - 12:00 PM and 2:00 PM - 5:00 PM<br><strong>Start Date:</strong> January 11, 2025<br><strong>End Date:</strong> February 15, 2025<br><strong>Location:</strong> Diocese Pastoral Center, Byumba</p><h2>Course Content</h2><h3>Week 1: Foundation of Christian Marriage</h3><p>• Understanding marriage as a sacrament<br>• Biblical foundations of marriage<br>• The role of God in married life<br>• Commitment and covenant relationship</p><h3>Week 2: Communication and Conflict Resolution</h3><p>• Effective communication skills<br>• Active listening techniques<br>• Healthy conflict resolution<br>• Building emotional intimacy</p><h3>Week 3: Financial Management</h3><p>• Budgeting and financial planning<br>• Shared financial goals and values<br>• Debt management and savings<br>• Stewardship and generosity</p><h3>Week 4: Family Planning and Sexuality</h3><p>• Catholic teaching on sexuality<br>• Natural Family Planning methods<br>• Responsible parenthood<br>• Intimacy and respect in marriage</p><h3>Week 5: Roles and Responsibilities</h3><p>• Partnership in marriage<br>• Balancing work and family life<br>• Extended family relationships<br>• Cultural considerations and traditions</p><h3>Week 6: Spiritual Life and Prayer</h3><p>• Praying together as a couple<br>• Participating in parish life<br>• Raising children in the faith<br>• Marriage as a path to holiness</p><h2>Registration Requirements</h2><h3>Eligibility</h3><p>• Both parties must be baptized Catholics or one Catholic with proper dispensation<br>• Couples must be free to marry (no previous valid marriage)<br>• Minimum age: 18 years for women, 20 years for men<br>• Must have completed confirmation</p><h3>Required Documents</h3><p>• Baptismal certificates (recent copies)<br>• Confirmation certificates<br>• National identity cards<br>• Freedom to marry letters from home parishes<br>• Medical certificates (if required)</p><h3>Registration Process</h3><p>1. Contact your parish priest to express intention to marry<br>2. Complete the pre-marriage questionnaire<br>3. Submit required documents<br>4. Pay registration fee (50,000 RWF per couple)<br>5. Attend all six sessions of the course</p><h2>Course Features</h2><h3>Experienced Facilitators</h3><p>Our course is led by a team of experienced facilitators including:</p><p>• Married couples with strong Catholic marriages<br>• Priests with extensive pastoral experience<br>• Professional counselors and therapists<br>• Family life ministry coordinators</p><h3>Interactive Learning</h3><p>The course includes various learning methods:</p><p>• Group discussions and sharing<br>• Couple exercises and reflection time<br>• Video presentations and case studies<br>• Q&A sessions with experienced couples</p><h3>Resources Provided</h3><p>Each couple receives:</p><p>• Marriage preparation workbook<br>• Prayer book for couples<br>• Resource list for ongoing support<br>• Certificate of completion</p><h2>Post-Course Support</h2><h3>Ongoing Formation</h3><p>Marriage preparation doesn\'t end with the course. We offer:</p><p>• Monthly married couples\' meetings<br>• Annual marriage enrichment retreats<br>• Counseling services when needed<br>• Mentorship programs with experienced couples</p><h3>Wedding Planning Assistance</h3><p>After completing the course, couples receive help with:</p><p>• Scheduling the wedding ceremony<br>• Liturgy planning and music selection<br>• Understanding marriage rites and traditions<br>• Connecting with wedding service providers</p><h2>Registration Information</h2><p><strong>Registration Deadline:</strong> January 5, 2025<br><strong>Maximum Participants:</strong> 20 couples per session<br><strong>Contact:</strong> Family Ministry Office<br><strong>Phone:</strong> +250 788 123 456<br><strong>Email:</strong> family@diocesebyumba.rw</p><h3>Special Accommodations</h3><p>We strive to accommodate all couples. Please contact us if you need:</p><p>• Translation services<br>• Accessibility accommodations<br>• Childcare during sessions<br>• Financial assistance with fees</p><p>We look forward to walking with you on your journey toward a blessed and holy marriage. May God bless your preparation and your future life together.</p>',
    'Join our comprehensive Marriage Preparation Course for couples planning to marry in 2025. Learn essential skills for a successful Christian marriage.',
    'marriage preparation, Catholic marriage, Diocese of Byumba, wedding preparation, sacrament of matrimony',
    0, 1, '2024-12-01 13:00:00', 0, NOW(), NOW()
);

-- Insert blog post tag associations
INSERT INTO `blog_post_tags` (`blog_post_id`, `blog_tag_id`) VALUES
-- Advent Reflection tags
((SELECT id FROM blog_posts WHERE slug = 'advent-reflection'), (SELECT id FROM blog_tags WHERE slug = 'advent')),
((SELECT id FROM blog_posts WHERE slug = 'advent-reflection'), (SELECT id FROM blog_tags WHERE slug = 'reflection')),
((SELECT id FROM blog_posts WHERE slug = 'advent-reflection'), (SELECT id FROM blog_tags WHERE slug = 'prayer')),
((SELECT id FROM blog_posts WHERE slug = 'advent-reflection'), (SELECT id FROM blog_tags WHERE slug = 'spiritual-growth')),

-- Youth Concert tags
((SELECT id FROM blog_posts WHERE slug = 'youth-concert'), (SELECT id FROM blog_tags WHERE slug = 'youth-ministry')),
((SELECT id FROM blog_posts WHERE slug = 'youth-concert'), (SELECT id FROM blog_tags WHERE slug = 'christmas')),
((SELECT id FROM blog_posts WHERE slug = 'youth-concert'), (SELECT id FROM blog_tags WHERE slug = 'concert')),
((SELECT id FROM blog_posts WHERE slug = 'youth-concert'), (SELECT id FROM blog_tags WHERE slug = 'community')),

-- Community Outreach tags
((SELECT id FROM blog_posts WHERE slug = 'community-outreach'), (SELECT id FROM blog_tags WHERE slug = 'community')),
((SELECT id FROM blog_posts WHERE slug = 'community-outreach'), (SELECT id FROM blog_tags WHERE slug = 'outreach')),
((SELECT id FROM blog_posts WHERE slug = 'community-outreach'), (SELECT id FROM blog_tags WHERE slug = 'charity')),
((SELECT id FROM blog_posts WHERE slug = 'community-outreach'), (SELECT id FROM blog_tags WHERE slug = 'support')),

-- Mass Schedule tags
((SELECT id FROM blog_posts WHERE slug = 'mass-schedule'), (SELECT id FROM blog_tags WHERE slug = 'mass-schedule')),
((SELECT id FROM blog_posts WHERE slug = 'mass-schedule'), (SELECT id FROM blog_tags WHERE slug = 'announcements')),
((SELECT id FROM blog_posts WHERE slug = 'mass-schedule'), (SELECT id FROM blog_tags WHERE slug = 'worship')),
((SELECT id FROM blog_posts WHERE slug = 'mass-schedule'), (SELECT id FROM blog_tags WHERE slug = 'parish-life')),

-- Youth Leadership tags
((SELECT id FROM blog_posts WHERE slug = 'youth-leadership'), (SELECT id FROM blog_tags WHERE slug = 'youth-ministry')),
((SELECT id FROM blog_posts WHERE slug = 'youth-leadership'), (SELECT id FROM blog_tags WHERE slug = 'leadership')),
((SELECT id FROM blog_posts WHERE slug = 'youth-leadership'), (SELECT id FROM blog_tags WHERE slug = 'training')),
((SELECT id FROM blog_posts WHERE slug = 'youth-leadership'), (SELECT id FROM blog_tags WHERE slug = 'community-development')),

-- Power of Prayer tags
((SELECT id FROM blog_posts WHERE slug = 'power-of-prayer'), (SELECT id FROM blog_tags WHERE slug = 'prayer')),
((SELECT id FROM blog_posts WHERE slug = 'power-of-prayer'), (SELECT id FROM blog_tags WHERE slug = 'spiritual-growth')),
((SELECT id FROM blog_posts WHERE slug = 'power-of-prayer'), (SELECT id FROM blog_tags WHERE slug = 'daily-practice')),
((SELECT id FROM blog_posts WHERE slug = 'power-of-prayer'), (SELECT id FROM blog_tags WHERE slug = 'faith')),

-- Seminary Graduation tags
((SELECT id FROM blog_posts WHERE slug = 'seminary-graduation'), (SELECT id FROM blog_tags WHERE slug = 'seminary')),
((SELECT id FROM blog_posts WHERE slug = 'seminary-graduation'), (SELECT id FROM blog_tags WHERE slug = 'ordination')),
((SELECT id FROM blog_posts WHERE slug = 'seminary-graduation'), (SELECT id FROM blog_tags WHERE slug = 'priests')),
((SELECT id FROM blog_posts WHERE slug = 'seminary-graduation'), (SELECT id FROM blog_tags WHERE slug = 'graduation')),

-- Marriage Preparation tags
((SELECT id FROM blog_posts WHERE slug = 'marriage-preparation'), (SELECT id FROM blog_tags WHERE slug = 'marriage')),
((SELECT id FROM blog_posts WHERE slug = 'marriage-preparation'), (SELECT id FROM blog_tags WHERE slug = 'family-life')),
((SELECT id FROM blog_posts WHERE slug = 'marriage-preparation'), (SELECT id FROM blog_tags WHERE slug = 'preparation')),
((SELECT id FROM blog_posts WHERE slug = 'marriage-preparation'), (SELECT id FROM blog_tags WHERE slug = 'sacrament'));

-- Update category translations for new categories
INSERT INTO `blog_category_translations` (`blog_category_id`, `language_code`, `name`, `description`) VALUES
((SELECT id FROM blog_categories WHERE category_key = 'spiritual-reflections'), 'en', 'Spiritual Reflections', 'Spiritual insights and reflections'),
((SELECT id FROM blog_categories WHERE category_key = 'spiritual-reflections'), 'rw', 'Gutekereza ku Mwuka', 'Ubushishozi n\'ubwiyunge bw\'umwuka'),
((SELECT id FROM blog_categories WHERE category_key = 'spiritual-reflections'), 'fr', 'Réflexions Spirituelles', 'Réflexions et méditations spirituelles'),

((SELECT id FROM blog_categories WHERE category_key = 'youth-ministry'), 'en', 'Youth Ministry', 'Programs and activities for young people'),
((SELECT id FROM blog_categories WHERE category_key = 'youth-ministry'), 'rw', 'Ubutumwa bw\'Urubyiruko', 'Gahunda n\'ibikorwa by\'urubyiruko'),
((SELECT id FROM blog_categories WHERE category_key = 'youth-ministry'), 'fr', 'Ministère des Jeunes', 'Programmes et activités pour les jeunes'),

((SELECT id FROM blog_categories WHERE category_key = 'family-life'), 'en', 'Family Life', 'Marriage, family, and relationship guidance'),
((SELECT id FROM blog_categories WHERE category_key = 'family-life'), 'rw', 'Ubuzima bw\'Umuryango', 'Ubuyobozi bw\'ubukwe, umuryango n\'ubusabane'),
((SELECT id FROM blog_categories WHERE category_key = 'family-life'), 'fr', 'Vie Familiale', 'Guidance pour le mariage, la famille et les relations');

-- Success message
SELECT 'Blog system migration completed successfully!' as message;
