// Bishop Meeting Page Specific JavaScript

// Global variables
let currentLanguage = 'en';

document.addEventListener('DOMContentLoaded', function() {
    initializeMeetingForm();
    initializeDateValidation();
    initializeFormValidation();
    initializeLanguageSupport();

    // Initial content update after language manager loads
    setTimeout(() => {
        if (window.languageManager && window.languageManager.currentLanguage) {
            currentLanguage = window.languageManager.currentLanguage;
            updateBishopMeetingContent();
        }
    }, 100);
});

// Initialize language support
function initializeLanguageSupport() {
    // Listen for language changes
    document.addEventListener('languageChanged', function(event) {
        currentLanguage = event.detail.language;
        // Use setTimeout to ensure this runs after language manager updates
        setTimeout(() => {
            updateBishopMeetingContent();
        }, 10);
    });
}

// Update content based on language
function updateBishopMeetingContent(language) {
    // Update current language if provided
    if (language) {
        currentLanguage = language;
    }

    // Update all elements with data-translate attributes
    updateTranslatableElements();

    // Update form placeholders
    updateFormPlaceholders();

    // Update dynamic time slots if date is selected
    const preferredDateInput = document.getElementById('preferredDate');
    if (preferredDateInput && preferredDateInput.value) {
        updateAvailableTimeSlots(preferredDateInput.value);
    }
}

// Update all translatable elements
function updateTranslatableElements() {
    const translatableElements = document.querySelectorAll('[data-translate]');

    translatableElements.forEach(element => {
        const key = element.getAttribute('data-translate');
        const translation = getTranslation(key);
        if (translation && translation !== key) { // Only update if we have a real translation
            element.textContent = translation;
        }
    });

    // Also update placeholder elements
    const placeholderElements = document.querySelectorAll('[data-translate-placeholder]');
    placeholderElements.forEach(element => {
        const key = element.getAttribute('data-translate-placeholder');
        const translation = getTranslation(key);
        if (translation && translation !== key) {
            element.placeholder = translation;
        }
    });
}

// Update form placeholders based on language
function updateFormPlaceholders() {
    const placeholderElements = document.querySelectorAll('[data-translate-placeholder]');

    placeholderElements.forEach(element => {
        const key = element.getAttribute('data-translate-placeholder');
        const translation = getTranslation(key);
        if (translation) {
            element.placeholder = translation;
        }
    });
}

// Get translation for a given key
function getTranslation(key) {
    const translations = {
        'en': {
            // Page titles and headers
            'schedule_meeting_title': 'Schedule a Meeting with the Bishop',
            'schedule_meeting_description': 'Request an appointment to meet with His Lordship Bishop of Byumba',
            'bishop_name': 'His Lordship Bishop Papias Musengamana',
            'bishop_title': 'Bishop of Byumba Diocese',
            'bishop_bio': 'His Lordship welcomes members of the diocese for pastoral guidance, spiritual counseling, and administrative matters. Please schedule your appointment in advance.',
            'office_hours': 'Office Hours',
            'monday_friday': 'Monday - Friday',
            'weekday_hours': '9:00 AM - 5:00 PM',
            'saturday': 'Saturday',
            'saturday_hours': '9:00 AM - 12:00 PM',
            'sunday': 'Sunday',
            'appointment_only': 'By Appointment Only',

            // Form labels
            'request_appointment': 'Request an Appointment',
            'first_name': 'First Name *',
            'last_name': 'Last Name *',
            'email_address': 'Email Address *',
            'phone_number': 'Phone Number *',
            'parish_church': 'Parish/Church',
            'meeting_type': 'Type of Meeting *',
            'preferred_date': 'Preferred Date *',
            'preferred_time': 'Preferred Time *',
            'alternative_date': 'Alternative Date',
            'urgency_level': 'Urgency Level',
            'purpose_meeting': 'Purpose of Meeting *',
            'additional_info': 'Additional Information',

            // Form options
            'select_parish': 'Select your parish',
            'st_mary_parish': 'St. Mary\'s Parish',
            'st_joseph_parish': 'St. Joseph\'s Parish',
            'st_peter_parish': 'St. Peter\'s Parish',
            'holy_family_parish': 'Holy Family Parish',
            'st_paul_parish': 'St. Paul\'s Parish',
            'other': 'Other',
            'select_meeting_type': 'Select meeting type',
            'spiritual_guidance': 'Spiritual Guidance',
            'pastoral_care': 'Pastoral Care',
            'marriage_counseling': 'Marriage Counseling',
            'confession': 'Confession',
            'administrative_matter': 'Administrative Matter',
            'community_issue': 'Community Issue',
            'select_time': 'Select time',
            'time_9am': '9:00 AM',
            'time_10am': '10:00 AM',
            'time_11am': '11:00 AM',
            'time_2pm': '2:00 PM',
            'time_3pm': '3:00 PM',
            'time_4pm': '4:00 PM',
            'normal': 'Normal',
            'urgent': 'Urgent',
            'emergency': 'Emergency',

            // Placeholders
            'purpose_placeholder': 'Please briefly describe the purpose of your meeting...',
            'additional_info_placeholder': 'Any additional information you\'d like to share...',

            // Checkboxes and buttons
            'terms_agreement': 'I understand that this is a request and confirmation will be provided via email or phone *',
            'newsletter_signup': 'I would like to receive updates about diocese events and activities',
            'submit_meeting_request': 'Submit Meeting Request',

            // Validation messages
            'field_required': 'This field is required.',
            'invalid_email': 'Please enter a valid email address.',
            'invalid_phone': 'Please enter a valid phone number.',
            'future_date_required': 'Please select a future date.',
            'purpose_too_short': 'Please provide more details about the purpose of your meeting.',
            'accept_terms': 'Please accept the terms and conditions.',
            'correct_form_errors': 'Please correct the errors in the form before submitting.',
            'sunday_appointment_notice': 'Sunday meetings are by appointment only. Please call the office to arrange.',
            'saturday_hours_notice': 'Saturday meetings are available only from 9:00 AM to 12:00 PM.',

            // Guidelines
            'meeting_guidelines': 'Meeting Guidelines',
            'advance_notice': 'Advance Notice',
            'advance_notice_desc': 'Please submit your request at least 3-5 business days in advance.',
            'meeting_duration': 'Meeting Duration',
            'meeting_duration_desc': 'Standard meetings are scheduled for 30 minutes. Longer sessions may be arranged if needed.',
            'confirmation': 'Confirmation',
            'confirmation_desc': 'You will receive confirmation within 24-48 hours via email or phone.',
            'emergency_situations': 'Emergency Situations',
            'emergency_situations_desc': 'For urgent pastoral care, please call the diocese office directly at +250 788 123 456.',
            'cancellations': 'Cancellations',
            'cancellations_desc': 'Please notify us at least 24 hours in advance if you need to cancel or reschedule.',
            'accompaniment': 'Accompaniment',
            'accompaniment_desc': 'You may bring a family member or friend if needed. Please mention this in your request.',

            // Contact section
            'alternative_contact': 'Alternative Contact Methods',
            'phone': 'Phone',
            'phone_hours': 'Monday - Friday: 8:00 AM - 6:00 PM',
            'email': 'Email',
            'email_response': 'Response within 24 hours',
            'office_visit': 'Office Visit',
            'diocese_office': 'Diocese of Byumba Office',
            'office_location': 'Byumba, Northern Province',

            // Success modal
            'success_title': 'Meeting Request Submitted Successfully!',
            'success_greeting': 'Dear',
            'success_message': 'Your meeting request has been received and will be reviewed by the Bishop\'s office.',
            'request_summary': 'Request Summary:',
            'what_happens_next': 'What happens next?',
            'step_1': 'You will receive a confirmation email within 24-48 hours',
            'step_2': 'The Bishop\'s office will review your request',
            'step_3': 'You will be contacted to confirm the final appointment details',
            'step_4': 'If your preferred time is not available, alternative times will be suggested',
            'urgent_matters': 'For urgent matters:',
            'urgent_call': 'Please call +250 788 123 456',
            'close_button': 'Close',

            // Navigation and common elements
            'site_name': 'Diocese of Byumba',
            'site_subtitle': 'Diocese of Byumba',
            'login': 'Login',
            'certificates': 'Certificates',
            'jobs': 'Jobs',
            'bishop_meeting': 'Bishop Meeting',
            'blog': 'Blog',
            'contact_information': 'Contact Information',
            'quick_links': 'Quick Links',
            'job_opportunities': 'Job Opportunities',
            'register': 'Register',
            'follow_us': 'Follow Us',
            'copyright': '© 2024 Diocese of Byumba. All rights reserved.'
        },
        'rw': {
            // Page titles and headers
            'schedule_meeting_title': 'Gusaba Guhura na Musenyeri',
            'schedule_meeting_description': 'Saba gahunda yo guhura na Musenyeri wa Diyosezi ya Byumba',
            'bishop_name': 'Musenyeri Papias Musengamana',
            'bishop_title': 'Musenyeri wa Diyosezi ya Byumba',
            'bishop_bio': 'Musenyeri akwakira abanyamuryango ba diyosezi mu gufasha mu by\'umwuka, ubujyanama, n\'ibibazo by\'ubuyobozi. Nyamuneka usabe gahunda mbere.',
            'office_hours': 'Amasaha y\'Akazi',
            'monday_friday': 'Kuwa mbere - Kuwa gatanu',
            'weekday_hours': '9:00 AM - 5:00 PM',
            'saturday': 'Kuwa gatandatu',
            'saturday_hours': '9:00 AM - 12:00 PM',
            'sunday': 'Ku cyumweru',
            'appointment_only': 'Gusa ku Gahunda',

            // Form labels
            'request_appointment': 'Saba Gahunda',
            'first_name': 'Izina ry\'Ubanza *',
            'last_name': 'Izina ry\'Umuryango *',
            'email_address': 'Aderesi ya Email *',
            'phone_number': 'Nimero ya Telefoni *',
            'parish_church': 'Paruwasi/Itorero',
            'meeting_type': 'Ubwoko bw\'Inama *',
            'preferred_date': 'Itariki Ushaka *',
            'preferred_time': 'Igihe Ushaka *',
            'alternative_date': 'Itariki y\'Ubundi',
            'urgency_level': 'Urwego rw\'Ubwihuse',
            'purpose_meeting': 'Intego y\'Inama *',
            'additional_info': 'Amakuru y\'Inyongera',

            // Form options
            'select_parish': 'Hitamo paruwasi yawe',
            'st_mary_parish': 'Paruwasi ya Mariya Mutagatifu',
            'st_joseph_parish': 'Paruwasi ya Yosefu Mutagatifu',
            'st_peter_parish': 'Paruwasi ya Petero Mutagatifu',
            'holy_family_parish': 'Paruwasi y\'Umuryango Wera',
            'st_paul_parish': 'Paruwasi ya Pawulo Mutagatifu',
            'other': 'Ikindi',
            'select_meeting_type': 'Hitamo ubwoko bw\'inama',
            'spiritual_guidance': 'Ubuyobozi bw\'Umwuka',
            'pastoral_care': 'Ubwitabire bw\'Umushumba',
            'marriage_counseling': 'Ubujyanama bw\'Ubukwe',
            'confession': 'Kwicuza',
            'administrative_matter': 'Ikibazo cy\'Ubuyobozi',
            'community_issue': 'Ikibazo cy\'Abaturage',
            'select_time': 'Hitamo igihe',
            'time_9am': '9:00 AM',
            'time_10am': '10:00 AM',
            'time_11am': '11:00 AM',
            'time_2pm': '2:00 PM',
            'time_3pm': '3:00 PM',
            'time_4pm': '4:00 PM',
            'normal': 'Bisanzwe',
            'urgent': 'Byihutirwa',
            'emergency': 'Byihutirwa cyane',

            // Placeholders
            'purpose_placeholder': 'Nyamuneka sobanura muri make intego y\'inama yawe...',
            'additional_info_placeholder': 'Amakuru yose y\'inyongera ushaka gutanga...',

            // Checkboxes and buttons
            'terms_agreement': 'Ndumva ko iki ni icyifuzo kandi nzahabwa ubutumwa bwa email cyangwa telefoni *',
            'newsletter_signup': 'Ndashaka kwakira amakuru yerekeye ibikorwa bya diyosezi',
            'submit_meeting_request': 'Kohereza Icyifuzo cy\'Inama',

            // Validation messages
            'field_required': 'Iki gice gikenewe.',
            'invalid_email': 'Nyamuneka shyiramo aderesi ya email nyayo.',
            'invalid_phone': 'Nyamuneka shyiramo nimero ya telefoni nyayo.',
            'future_date_required': 'Nyamuneka hitamo itariki izaza.',
            'purpose_too_short': 'Nyamuneka tanga amakuru menshi yerekeye intego y\'inama yawe.',
            'accept_terms': 'Nyamuneka wemere amabwiriza n\'amategeko.',
            'correct_form_errors': 'Nyamuneka kosora amakosa mu ifishi mbere yo kohereza.',
            'sunday_appointment_notice': 'Inama zo ku cyumweru ziba gusa ku gahunda. Nyamuneka hamagara ibiro.',
            'saturday_hours_notice': 'Inama zo kuwa gatandatu ziboneka gusa kuva saa 3 kugeza saa 6.',

            // Guidelines
            'meeting_guidelines': 'Amabwiriza y\'Inama',
            'advance_notice': 'Gutangaza Mbere',
            'advance_notice_desc': 'Nyamuneka tanga icyifuzo cyawe byibuze iminsi 3-5 y\'akazi mbere.',
            'meeting_duration': 'Igihe cy\'Inama',
            'meeting_duration_desc': 'Inama zisanzwe ziteganijwe iminota 30. Ibihe birebire birashobora guteganywa niba bikenewe.',
            'confirmation': 'Kwemeza',
            'confirmation_desc': 'Uzahabwa kwemeza mu masaha 24-48 binyuze kuri email cyangwa telefoni.',
            'emergency_situations': 'Ibihe by\'Ihutirwa',
            'emergency_situations_desc': 'Ku bijyanye n\'ubwitabire bw\'ihutirwa, nyamuneka hamagara ibiro rya diyosezi ku +250 788 123 456.',
            'cancellations': 'Gusiba',
            'cancellations_desc': 'Nyamuneka tumenyeshe byibuze masaha 24 mbere niba ukeneye gusiba cyangwa guhindura.',
            'accompaniment': 'Guherekeza',
            'accompaniment_desc': 'Urashobora kuzana umunyangwanda cyangwa inshuti niba bikenewe. Nyamuneka bivuge mu cyifuzo cyawe.',

            // Contact section
            'alternative_contact': 'Ubundi Buryo bwo Kuvugana',
            'phone': 'Telefoni',
            'phone_hours': 'Kuwa mbere - Kuwa gatanu: 8:00 AM - 6:00 PM',
            'email': 'Email',
            'email_response': 'Igisubizo mu masaha 24',
            'office_visit': 'Gusura Ibiro',
            'diocese_office': 'Ibiro bya Diyosezi ya Byumba',
            'office_location': 'Byumba, Intara y\'Amajyaruguru',

            // Success modal
            'success_title': 'Icyifuzo cy\'Inama Cyakiriwe Neza!',
            'success_greeting': 'Mwaramutse',
            'success_message': 'Icyifuzo cyawe cy\'inama cyakiriwe kandi kizasuzumwa n\'ibiro bya Musenyeri.',
            'request_summary': 'Incamake y\'Icyifuzo:',
            'what_happens_next': 'Iki gikurikira ni iki?',
            'step_1': 'Uzahabwa ubutumwa bwa email mu masaha 24-48',
            'step_2': 'Ibiro bya Musenyeri bizasuzuma icyifuzo cyawe',
            'step_3': 'Uzavugishwa kugira ngo hemeze amakuru y\'inama',
            'step_4': 'Niba igihe ushaka kitaboneka, hazatangwa ubundi bwoko bw\'ibihe',
            'urgent_matters': 'Ku bibazo by\'ihutirwa:',
            'urgent_call': 'Nyamuneka hamagara +250 788 123 456',
            'close_button': 'Funga',

            // Navigation and common elements
            'site_name': 'Diyosezi ya Byumba',
            'site_subtitle': 'Diyosezi ya Byumba',
            'login': 'Kwinjira',
            'certificates': 'Ibyangombwa',
            'jobs': 'Akazi',
            'bishop_meeting': 'Guhura na Musenyeri',
            'blog': 'Blog',
            'contact_information': 'Amakuru yo Kuvugana',
            'quick_links': 'Ihuza Ryihuse',
            'job_opportunities': 'Amahirwe y\'Akazi',
            'register': 'Kwiyandikisha',
            'follow_us': 'Dukurikire',
            'copyright': '© 2024 Diyosezi ya Byumba. Uburenganzira bwose burabitswe.'
        },
        'fr': {
            // Page titles and headers
            'schedule_meeting_title': 'Planifier une Rencontre avec l\'Évêque',
            'schedule_meeting_description': 'Demander un rendez-vous pour rencontrer Monseigneur l\'Évêque de Byumba',
            'bishop_name': 'Monseigneur Papias Musengamana',
            'bishop_title': 'Évêque du Diocèse de Byumba',
            'bishop_bio': 'Monseigneur accueille les membres du diocèse pour des conseils pastoraux, spirituels et des questions administratives. Veuillez planifier votre rendez-vous à l\'avance.',
            'office_hours': 'Heures de Bureau',
            'monday_friday': 'Lundi - Vendredi',
            'weekday_hours': '9h00 - 17h00',
            'saturday': 'Samedi',
            'saturday_hours': '9h00 - 12h00',
            'sunday': 'Dimanche',
            'appointment_only': 'Sur Rendez-vous Uniquement',

            // Form labels
            'request_appointment': 'Demander un Rendez-vous',
            'first_name': 'Prénom *',
            'last_name': 'Nom de Famille *',
            'email_address': 'Adresse Email *',
            'phone_number': 'Numéro de Téléphone *',
            'parish_church': 'Paroisse/Église',
            'meeting_type': 'Type de Rencontre *',
            'preferred_date': 'Date Préférée *',
            'preferred_time': 'Heure Préférée *',
            'alternative_date': 'Date Alternative',
            'urgency_level': 'Niveau d\'Urgence',
            'purpose_meeting': 'Objet de la Rencontre *',
            'additional_info': 'Informations Supplémentaires',

            // Form options
            'select_parish': 'Sélectionnez votre paroisse',
            'st_mary_parish': 'Paroisse Sainte-Marie',
            'st_joseph_parish': 'Paroisse Saint-Joseph',
            'st_peter_parish': 'Paroisse Saint-Pierre',
            'holy_family_parish': 'Paroisse de la Sainte Famille',
            'st_paul_parish': 'Paroisse Saint-Paul',
            'other': 'Autre',
            'select_meeting_type': 'Sélectionnez le type de rencontre',
            'spiritual_guidance': 'Guidance Spirituelle',
            'pastoral_care': 'Soins Pastoraux',
            'marriage_counseling': 'Conseil Matrimonial',
            'confession': 'Confession',
            'administrative_matter': 'Question Administrative',
            'community_issue': 'Question Communautaire',
            'select_time': 'Sélectionnez l\'heure',
            'time_9am': '9h00',
            'time_10am': '10h00',
            'time_11am': '11h00',
            'time_2pm': '14h00',
            'time_3pm': '15h00',
            'time_4pm': '16h00',
            'normal': 'Normal',
            'urgent': 'Urgent',
            'emergency': 'Urgence',

            // Placeholders
            'purpose_placeholder': 'Veuillez décrire brièvement l\'objet de votre rencontre...',
            'additional_info_placeholder': 'Toute information supplémentaire que vous souhaitez partager...',

            // Checkboxes and buttons
            'terms_agreement': 'Je comprends que ceci est une demande et la confirmation sera fournie par email ou téléphone *',
            'newsletter_signup': 'Je souhaite recevoir des mises à jour sur les événements et activités du diocèse',
            'submit_meeting_request': 'Soumettre la Demande de Rencontre',

            // Validation messages
            'field_required': 'Ce champ est requis.',
            'invalid_email': 'Veuillez entrer une adresse email valide.',
            'invalid_phone': 'Veuillez entrer un numéro de téléphone valide.',
            'future_date_required': 'Veuillez sélectionner une date future.',
            'purpose_too_short': 'Veuillez fournir plus de détails sur l\'objet de votre rencontre.',
            'accept_terms': 'Veuillez accepter les termes et conditions.',
            'correct_form_errors': 'Veuillez corriger les erreurs dans le formulaire avant de soumettre.',
            'sunday_appointment_notice': 'Les rencontres du dimanche sont sur rendez-vous uniquement. Veuillez appeler le bureau.',
            'saturday_hours_notice': 'Les rencontres du samedi sont disponibles uniquement de 9h00 à 12h00.',

            // Guidelines
            'meeting_guidelines': 'Directives de Rencontre',
            'advance_notice': 'Préavis',
            'advance_notice_desc': 'Veuillez soumettre votre demande au moins 3-5 jours ouvrables à l\'avance.',
            'meeting_duration': 'Durée de la Rencontre',
            'meeting_duration_desc': 'Les rencontres standard sont programmées pour 30 minutes. Des sessions plus longues peuvent être arrangées si nécessaire.',
            'confirmation': 'Confirmation',
            'confirmation_desc': 'Vous recevrez une confirmation dans les 24-48 heures par email ou téléphone.',
            'emergency_situations': 'Situations d\'Urgence',
            'emergency_situations_desc': 'Pour les soins pastoraux urgents, veuillez appeler directement le bureau du diocèse au +250 788 123 456.',
            'cancellations': 'Annulations',
            'cancellations_desc': 'Veuillez nous notifier au moins 24 heures à l\'avance si vous devez annuler ou reprogrammer.',
            'accompaniment': 'Accompagnement',
            'accompaniment_desc': 'Vous pouvez amener un membre de la famille ou un ami si nécessaire. Veuillez le mentionner dans votre demande.',

            // Contact section
            'alternative_contact': 'Méthodes de Contact Alternatives',
            'phone': 'Téléphone',
            'phone_hours': 'Lundi - Vendredi: 8h00 - 18h00',
            'email': 'Email',
            'email_response': 'Réponse dans les 24 heures',
            'office_visit': 'Visite au Bureau',
            'diocese_office': 'Bureau du Diocèse de Byumba',
            'office_location': 'Byumba, Province du Nord',

            // Success modal
            'success_title': 'Demande de Rencontre Soumise avec Succès!',
            'success_greeting': 'Cher/Chère',
            'success_message': 'Votre demande de rencontre a été reçue et sera examinée par le bureau de l\'Évêque.',
            'request_summary': 'Résumé de la Demande:',
            'what_happens_next': 'Que se passe-t-il ensuite?',
            'step_1': 'Vous recevrez un email de confirmation dans les 24-48 heures',
            'step_2': 'Le bureau de l\'Évêque examinera votre demande',
            'step_3': 'Vous serez contacté pour confirmer les détails finaux du rendez-vous',
            'step_4': 'Si votre heure préférée n\'est pas disponible, des alternatives seront suggérées',
            'urgent_matters': 'Pour les questions urgentes:',
            'urgent_call': 'Veuillez appeler +250 788 123 456',
            'close_button': 'Fermer',

            // Navigation and common elements
            'site_name': 'Diocèse de Byumba',
            'site_subtitle': 'Diocèse de Byumba',
            'login': 'Connexion',
            'certificates': 'Certificats',
            'jobs': 'Emplois',
            'bishop_meeting': 'Rencontre avec l\'Évêque',
            'blog': 'Blog',
            'contact_information': 'Informations de Contact',
            'quick_links': 'Liens Rapides',
            'job_opportunities': 'Opportunités d\'Emploi',
            'register': 'S\'inscrire',
            'follow_us': 'Suivez-nous',
            'copyright': '© 2024 Diocèse de Byumba. Tous droits réservés.'
        }
    };

    return translations[currentLanguage]?.[key] || translations['en']?.[key] || key;
}

// Make function available globally for language manager
window.updateBishopMeetingContent = updateBishopMeetingContent;

// Meeting Form Initialization
function initializeMeetingForm() {
    const meetingForm = document.getElementById('meetingForm');

    if (meetingForm) {
        meetingForm.addEventListener('submit', handleMeetingSubmission);
    }
    
    // Set minimum date to today
    const preferredDateInput = document.getElementById('preferredDate');
    const alternativeDateInput = document.getElementById('alternativeDate');
    
    if (preferredDateInput) {
        const today = new Date().toISOString().split('T')[0];
        preferredDateInput.min = today;
        
        // Set minimum date to 3 days from now for better planning
        const threeDaysFromNow = new Date();
        threeDaysFromNow.setDate(threeDaysFromNow.getDate() + 3);
        preferredDateInput.min = threeDaysFromNow.toISOString().split('T')[0];
    }
    
    if (alternativeDateInput) {
        const today = new Date().toISOString().split('T')[0];
        alternativeDateInput.min = today;
    }
}

// Date Validation
function initializeDateValidation() {
    const preferredDateInput = document.getElementById('preferredDate');
    const alternativeDateInput = document.getElementById('alternativeDate');
    const preferredTimeSelect = document.getElementById('preferredTime');
    
    if (preferredDateInput) {
        preferredDateInput.addEventListener('change', function() {
            validateSelectedDate(this.value);
            updateAvailableTimeSlots(this.value);
        });
    }
    
    if (alternativeDateInput) {
        alternativeDateInput.addEventListener('change', function() {
            validateSelectedDate(this.value);
        });
    }
}

function validateSelectedDate(dateString) {
    if (!dateString) return;

    const selectedDate = new Date(dateString);
    const dayOfWeek = selectedDate.getDay(); // 0 = Sunday, 6 = Saturday

    // Check if it's Sunday (day 0)
    if (dayOfWeek === 0) {
        const message = getTranslation('sunday_appointment_notice') || 'Sunday meetings are by appointment only. Please call the office to arrange.';
        showNotification(message, 'info');
    }

    // Check if it's Saturday afternoon (limited hours)
    if (dayOfWeek === 6) {
        const message = getTranslation('saturday_hours_notice') || 'Saturday meetings are available only from 9:00 AM to 12:00 PM.';
        showNotification(message, 'info');
    }
}

function updateAvailableTimeSlots(dateString) {
    const preferredTimeSelect = document.getElementById('preferredTime');
    if (!preferredTimeSelect || !dateString) return;
    
    const selectedDate = new Date(dateString);
    const dayOfWeek = selectedDate.getDay();
    
    // Clear existing options except the first one
    while (preferredTimeSelect.children.length > 1) {
        preferredTimeSelect.removeChild(preferredTimeSelect.lastChild);
    }
    
    let timeSlots = [];
    
    if (dayOfWeek === 0) { // Sunday
        timeSlots = [
            { value: 'appointment', text: getTranslation('appointment_only') || 'By Appointment Only' }
        ];
    } else if (dayOfWeek === 6) { // Saturday
        timeSlots = [
            { value: '09:00', text: getTranslation('time_9am') || '9:00 AM' },
            { value: '10:00', text: getTranslation('time_10am') || '10:00 AM' },
            { value: '11:00', text: getTranslation('time_11am') || '11:00 AM' }
        ];
    } else { // Monday to Friday
        timeSlots = [
            { value: '09:00', text: getTranslation('time_9am') || '9:00 AM' },
            { value: '10:00', text: getTranslation('time_10am') || '10:00 AM' },
            { value: '11:00', text: getTranslation('time_11am') || '11:00 AM' },
            { value: '14:00', text: getTranslation('time_2pm') || '2:00 PM' },
            { value: '15:00', text: getTranslation('time_3pm') || '3:00 PM' },
            { value: '16:00', text: getTranslation('time_4pm') || '4:00 PM' }
        ];
    }
    
    // Add time slots to select
    timeSlots.forEach(slot => {
        const option = document.createElement('option');
        option.value = slot.value;
        option.textContent = slot.text;
        preferredTimeSelect.appendChild(option);
    });
}

// Form Validation
function initializeFormValidation() {
    const form = document.getElementById('meetingForm');
    if (!form) return; // Exit if form doesn't exist (e.g., on test pages)

    const inputs = form.querySelectorAll('input, select, textarea');
    
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });
        
        input.addEventListener('input', function() {
            clearFieldError(this);
        });
    });
}

function validateField(field) {
    const value = field.value.trim();
    let isValid = true;
    let errorMessage = '';
    
    // Required field validation
    if (field.hasAttribute('required') && !value) {
        isValid = false;
        errorMessage = getTranslation('field_required') || 'This field is required.';
    }

    // Email validation
    if (field.type === 'email' && value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            isValid = false;
            errorMessage = getTranslation('invalid_email') || 'Please enter a valid email address.';
        }
    }

    // Phone validation
    if (field.type === 'tel' && value) {
        const phoneRegex = /^[\+]?[0-9\s\-\(\)]{10,}$/;
        if (!phoneRegex.test(value)) {
            isValid = false;
            errorMessage = getTranslation('invalid_phone') || 'Please enter a valid phone number.';
        }
    }

    // Date validation
    if (field.type === 'date' && value) {
        const selectedDate = new Date(value);
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        if (selectedDate < today) {
            isValid = false;
            errorMessage = getTranslation('future_date_required') || 'Please select a future date.';
        }
    }

    // Purpose validation (minimum length)
    if (field.id === 'purpose' && value && value.length < 10) {
        isValid = false;
        errorMessage = getTranslation('purpose_too_short') || 'Please provide more details about the purpose of your meeting.';
    }
    
    if (!isValid) {
        showFieldError(field, errorMessage);
    } else {
        clearFieldError(field);
    }
    
    return isValid;
}

function showFieldError(field, message) {
    clearFieldError(field);
    
    field.style.borderColor = '#d14438';
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'field-error';
    errorDiv.textContent = message;
    errorDiv.style.cssText = `
        color: #d14438;
        font-size: 12px;
        margin-top: 5px;
        display: block;
    `;
    
    field.parentNode.appendChild(errorDiv);
}

function clearFieldError(field) {
    field.style.borderColor = '#dcdcdc';
    
    const existingError = field.parentNode.querySelector('.field-error');
    if (existingError) {
        existingError.remove();
    }
}

// Handle Meeting Form Submission
function handleMeetingSubmission(e) {
    e.preventDefault();
    
    const form = e.target;
    const formData = new FormData(form);
    
    // Validate all fields
    const inputs = form.querySelectorAll('input, select, textarea');
    let isFormValid = true;
    
    inputs.forEach(input => {
        if (!validateField(input)) {
            isFormValid = false;
        }
    });
    
    // Check required checkboxes
    const termsCheckbox = document.getElementById('terms');
    if (!termsCheckbox.checked) {
        isFormValid = false;
        const message = getTranslation('accept_terms') || 'Please accept the terms and conditions.';
        showNotification(message, 'error');
    }

    if (!isFormValid) {
        const message = getTranslation('correct_form_errors') || 'Please correct the errors in the form before submitting.';
        showNotification(message, 'error');
        return;
    }
    
    // Show loading state
    const submitButton = form.querySelector('.submit-meeting-btn');
    const originalText = submitButton.innerHTML;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
    submitButton.disabled = true;
    
    // Simulate form submission (replace with actual API call)
    setTimeout(() => {
        // Reset button
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
        
        // Show success message
        showSuccessModal(formData);
        
        // Reset form
        form.reset();
        
        // Clear any remaining errors
        const errors = form.querySelectorAll('.field-error');
        errors.forEach(error => error.remove());
        
        // Reset field styles
        inputs.forEach(input => {
            input.style.borderColor = '#dcdcdc';
        });
        
    }, 2000);
}

function showSuccessModal(formData) {
    const firstName = formData.get('firstName');
    const lastName = formData.get('lastName');
    const preferredDate = formData.get('preferredDate');
    const preferredTime = formData.get('preferredTime');
    const meetingType = formData.get('meetingType');

    // Get translated meeting type
    const meetingTypeTranslated = getTranslation(meetingType.replace(/\s+/g, '_').toLowerCase()) || meetingType;

    const modalOverlay = document.createElement('div');
    modalOverlay.className = 'success-modal-overlay';
    modalOverlay.innerHTML = `
        <div class="success-modal-content">
            <div class="success-modal-header">
                <div class="success-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3>${getTranslation('success_title')}</h3>
            </div>
            <div class="success-modal-body">
                <p>${getTranslation('success_greeting')} ${firstName} ${lastName},</p>
                <p>${getTranslation('success_message')}</p>

                <div class="request-summary">
                    <h4>${getTranslation('request_summary')}</h4>
                    <ul>
                        <li><strong>${getTranslation('meeting_type')}:</strong> ${meetingTypeTranslated}</li>
                        <li><strong>${getTranslation('preferred_date')}:</strong> ${new Date(preferredDate).toLocaleDateString()}</li>
                        <li><strong>${getTranslation('preferred_time')}:</strong> ${preferredTime}</li>
                    </ul>
                </div>

                <div class="next-steps">
                    <h4>${getTranslation('what_happens_next')}</h4>
                    <ol>
                        <li>${getTranslation('step_1')}</li>
                        <li>${getTranslation('step_2')}</li>
                        <li>${getTranslation('step_3')}</li>
                        <li>${getTranslation('step_4')}</li>
                    </ol>
                </div>

                <div class="contact-reminder">
                    <p><strong>${getTranslation('urgent_matters')}</strong> ${getTranslation('urgent_call')}</p>
                </div>
            </div>
            <div class="success-modal-footer">
                <button class="close-success-btn">${getTranslation('close_button')}</button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modalOverlay);
    
    // Add modal styles
    const modalStyles = `
        .success-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            padding: 20px;
        }
        .success-modal-content {
            background: white;
            border-radius: 15px;
            width: 100%;
            max-width: 600px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            animation: modalSlideIn 0.3s ease;
        }
        .success-modal-header {
            text-align: center;
            padding: 30px;
            background: linear-gradient(135deg, #1e753f, #2a8f4f);
            color: white;
            border-radius: 15px 15px 0 0;
        }
        .success-icon {
            font-size: 48px;
            margin-bottom: 15px;
            color: #f2c97e;
        }
        .success-modal-header h3 {
            margin: 0;
            font-size: 24px;
        }
        .success-modal-body {
            padding: 30px;
        }
        .success-modal-body p {
            margin-bottom: 15px;
            color: #0f0f0f;
            line-height: 1.6;
        }
        .request-summary,
        .next-steps,
        .contact-reminder {
            margin: 25px 0;
            padding: 20px;
            background: #f0f0f0;
            border-radius: 10px;
        }
        .request-summary h4,
        .next-steps h4 {
            color: #1e753f;
            margin-bottom: 15px;
        }
        .request-summary ul,
        .next-steps ol {
            margin: 0;
            padding-left: 20px;
        }
        .request-summary li,
        .next-steps li {
            margin-bottom: 8px;
            color: #666;
        }
        .contact-reminder {
            background: #fff3cd;
            border-left: 4px solid #f2c97e;
        }
        .contact-reminder p {
            margin: 0;
            color: #856404;
        }
        .success-modal-footer {
            padding: 20px 30px;
            text-align: center;
            border-top: 1px solid #f0f0f0;
        }
        .close-success-btn {
            background: #1e753f;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .close-success-btn:hover {
            background: #2a8f4f;
        }
        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    `;
    
    const styleSheet = document.createElement('style');
    styleSheet.textContent = modalStyles;
    document.head.appendChild(styleSheet);
    
    // Close modal functionality
    const closeBtn = modalOverlay.querySelector('.close-success-btn');
    closeBtn.addEventListener('click', () => {
        document.body.removeChild(modalOverlay);
        document.head.removeChild(styleSheet);
    });
    
    // Close on overlay click
    modalOverlay.addEventListener('click', (e) => {
        if (e.target === modalOverlay) {
            document.body.removeChild(modalOverlay);
            document.head.removeChild(styleSheet);
        }
    });
}

// Utility function for notifications (if not already defined)
if (typeof showNotification === 'undefined') {
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.textContent = message;
        
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 8px;
            color: white;
            font-weight: 500;
            z-index: 1001;
            animation: slideIn 0.3s ease;
            background: ${type === 'success' ? '#1e753f' : type === 'error' ? '#d14438' : '#f2c97e'};
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => {
                if (notification.parentNode) {
                    document.body.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }
}
