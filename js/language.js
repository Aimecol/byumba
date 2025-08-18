// Language Management System for Diocese of Byumba

class LanguageManager {
  constructor() {
    this.currentLanguage = "en";
    this.availableLanguages = [];
    this.translations = {};
    this.apiBase = "api/";

    this.init();
  }

  async init() {
    try {
      // Load current language and available languages
      await this.loadLanguageData();

      // Initialize language switchers
      this.initializeLanguageSwitchers();

      // Load page content
      await this.loadPageContent();
    } catch (error) {
      console.error("Failed to initialize language manager:", error);
      // Fallback to static content if API fails
      this.initializeLanguageSwitchers();
    }
  }

  async loadLanguageData() {
    try {
      const response = await fetch(`${this.apiBase}index.php?endpoint=language`);
      const data = await response.json();

      if (data.success) {
        this.currentLanguage = data.data.current;
        this.availableLanguages = data.data.available;

        // Update HTML lang attribute
        document.documentElement.lang = this.currentLanguage;
      }
    } catch (error) {
      console.error("Failed to load language data:", error);
      // Use default values
      this.availableLanguages = [
        { code: "en", name: "English", native_name: "English" },
        { code: "rw", name: "Kinyarwanda", native_name: "Ikinyarwanda" },
        { code: "fr", name: "French", native_name: "Français" },
      ];
    }
  }

  initializeLanguageSwitchers() {
    // Desktop language toggle
    const langButtons = document.querySelectorAll(".lang-btn");
    langButtons.forEach((btn) => {
      // Set active state
      if (btn.getAttribute("data-lang") === this.currentLanguage) {
        btn.classList.add("active");
      } else {
        btn.classList.remove("active");
      }

      // Add click event
      btn.addEventListener("click", (e) => {
        e.preventDefault();
        const newLang = btn.getAttribute("data-lang");
        this.changeLanguage(newLang);
      });
    });

    // Mobile language toggle
    const mobileLangButtons = document.querySelectorAll(
      ".mobile-lang-buttons .lang-btn"
    );
    mobileLangButtons.forEach((btn) => {
      if (btn.getAttribute("data-lang") === this.currentLanguage) {
        btn.classList.add("active");
      } else {
        btn.classList.remove("active");
      }

      btn.addEventListener("click", (e) => {
        e.preventDefault();
        const newLang = btn.getAttribute("data-lang");
        this.changeLanguage(newLang);
      });
    });
  }

  async changeLanguage(languageCode) {
    try {
      // Show loading state
      this.showLoadingState();

      // Update language on server
      const response = await fetch(`${this.apiBase}index.php?endpoint=language`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ language: languageCode }),
      });

      const data = await response.json();

      if (data.success) {
        this.currentLanguage = languageCode;

        // Update HTML lang attribute
        document.documentElement.lang = languageCode;

        // Update active states
        this.updateLanguageButtons();

        // Reload page content
        await this.loadPageContent();

        // Dispatch language changed event for other components
        const languageChangedEvent = new CustomEvent('languageChanged', {
          detail: { language: languageCode }
        });
        document.dispatchEvent(languageChangedEvent);

        // Show success message
        this.showNotification(
          this.getLanguageText("language_changed"),
          "success"
        );
      } else {
        throw new Error(data.message || "Failed to change language");
      }
    } catch (error) {
      console.error("Failed to change language:", error);
      this.showNotification(
        this.getLanguageText("language_change_failed"),
        "error"
      );
    } finally {
      this.hideLoadingState();
    }
  }

  updateLanguageButtons() {
    const allLangButtons = document.querySelectorAll(".lang-btn");
    allLangButtons.forEach((btn) => {
      if (btn.getAttribute("data-lang") === this.currentLanguage) {
        btn.classList.add("active");
      } else {
        btn.classList.remove("active");
      }
    });
  }

  async loadPageContent() {
    const currentPage = this.getCurrentPage();

    try {
      switch (currentPage) {
        case "dashboard":
          await this.loadDashboardContent();
          break;
        case "my-applications":
          await this.loadApplicationsContent();
          break;
        case "my-meetings":
          await this.loadMeetingsContent();
          break;
        case "notifications":
          await this.loadNotificationsContent();
          break;
        case "profile":
          await this.loadProfileContent();
          break;
        case "certificates":
          await this.loadCertificatesContent();
          break;
        case "jobs":
          await this.loadJobsContent();
          break;
        case "bishop-meeting":
          await this.loadBishopMeetingContent();
          break;
        default:
          // For other pages, just update static text
          this.updateStaticText();
      }
    } catch (error) {
      console.error("Failed to load page content:", error);
      this.updateStaticText();
    }
  }

  getCurrentPage() {
    const path = window.location.pathname;
    if (path.includes("dashboard.html")) return "dashboard";
    if (path.includes("my-applications.html")) return "my-applications";
    if (path.includes("my-meetings.html")) return "my-meetings";
    if (path.includes("notifications.html")) return "notifications";
    if (path.includes("profile.html")) return "profile";
    if (
      path.includes("index.html") ||
      path.endsWith("/") ||
      path.endsWith("/byumba")
    )
      return "certificates";
    if (path.includes("jobs.html")) return "jobs";
    if (path.includes("bishop-meeting.html")) return "bishop-meeting";
    return "other";
  }

  async loadDashboardContent() {
    try {
      const response = await fetch(`${this.apiBase}index.php?endpoint=dashboard`);
      const data = await response.json();

      if (data.success && window.updateDashboardContent) {
        window.updateDashboardContent(data.data);
      }
    } catch (error) {
      console.error("Failed to load dashboard content:", error);
    }
  }

  async loadApplicationsContent() {
    try {
      const response = await fetch(`${this.apiBase}index.php?endpoint=applications`);
      const data = await response.json();

      if (data.success && window.updateApplicationsContent) {
        window.updateApplicationsContent(data.data);
      }
    } catch (error) {
      console.error("Failed to load applications content:", error);
    }
  }

  async loadMeetingsContent() {
    try {
      const response = await fetch(`${this.apiBase}index.php?endpoint=meetings`);
      const data = await response.json();

      if (data.success && window.updateMeetingsContent) {
        window.updateMeetingsContent(data.data);
      }
    } catch (error) {
      console.error("Failed to load meetings content:", error);
    }
  }

  async loadNotificationsContent() {
    try {
      const response = await fetch(`${this.apiBase}index.php?endpoint=notifications`);
      const data = await response.json();

      if (data.success && window.updateNotificationsContent) {
        window.updateNotificationsContent(data.data);
      }
    } catch (error) {
      console.error("Failed to load notifications content:", error);
    }
  }

  async loadProfileContent() {
    // Profile content is mostly static forms, just update labels
    this.updateStaticText();
  }

  async loadCertificatesContent() {
    try {
      // Update static text first
      this.updateStaticText();

      // Update certificates-specific content if function is available
      if (window.updateCertificatesContent) {
        window.updateCertificatesContent(this.currentLanguage);
      }

      // Reload certificates data to get translations in current language
      if (window.loadCertificates) {
        await window.loadCertificates();
      }
    } catch (error) {
      console.error("Failed to load certificates content:", error);
      this.updateStaticText();
    }
  }

  async loadJobsContent() {
    try {
      // Update static text first
      this.updateStaticText();

      // Update jobs-specific content if function is available
      if (window.updateJobsContent) {
        window.updateJobsContent(this.currentLanguage);
      }

      // Reload jobs data to get translations in current language
      if (window.loadJobs) {
        await window.loadJobs();
      }
    } catch (error) {
      console.error("Failed to load jobs content:", error);
      this.updateStaticText();
    }
  }

  async loadBishopMeetingContent() {
    try {
      // Update site name and description first
      const siteNameElements = document.querySelectorAll(".diocese-name");
      const siteSubtitleElements = document.querySelectorAll(".diocese-subtitle");

      siteNameElements.forEach((el) => {
        el.textContent = this.getSiteName();
      });

      siteSubtitleElements.forEach((el) => {
        el.textContent = this.getSiteSubtitle();
      });

      // Update navigation labels
      this.updateNavigationLabels();

      // Update page titles and descriptions
      this.updatePageTitles();

      // Update bishop meeting-specific content if function is available
      // This should handle all data-translate elements with bishop meeting translations
      if (window.updateBishopMeetingContent) {
        window.updateBishopMeetingContent(this.currentLanguage);
      } else {
        // Fallback to generic static text update
        this.updateStaticText();
      }
    } catch (error) {
      console.error("Failed to load bishop meeting content:", error);
      this.updateStaticText();
    }
  }

  updateStaticText() {
    // Update site name and description
    const siteNameElements = document.querySelectorAll(".diocese-name");
    const siteSubtitleElements = document.querySelectorAll(".diocese-subtitle");

    siteNameElements.forEach((el) => {
      el.textContent = this.getSiteName();
    });

    siteSubtitleElements.forEach((el) => {
      el.textContent = this.getSiteSubtitle();
    });

    // Update all elements with data-translate attributes
    this.updateTranslatableElements();

    // Update navigation labels
    this.updateNavigationLabels();

    // Update page titles and descriptions
    this.updatePageTitles();
  }

  updateTranslatableElements() {
    const translations = this.getStaticTranslations();

    document.querySelectorAll("[data-translate]").forEach((element) => {
      const key = element.getAttribute("data-translate");
      if (translations[key]) {
        element.textContent = translations[key];
      }
    });

    // Handle placeholder translations
    document
      .querySelectorAll("[data-translate-placeholder]")
      .forEach((element) => {
        const key = element.getAttribute("data-translate-placeholder");
        if (translations[key]) {
          element.placeholder = translations[key];
        }
      });
  }

  getStaticTranslations() {
    const translations = {
      en: {
        site_name: "Diocese of Byumba",
        site_subtitle: "Diocese of Byumba",
        login: "Login",
        certificates: "Certificates",
        jobs: "Jobs",
        bishop_meeting: "Bishop Meeting",
        blog: "Blog",
        certificate_services: "Certificate Services",
        certificate_services_description:
          "Apply for various certificates issued by the Diocese of Byumba",
        search_certificates: "Search certificates...",
        all_categories: "All Categories",
        apply_now: "Apply Now",
        loading_certificates: "Loading certificates...",
        error_loading_certificates:
          "Failed to load certificates. Please try again.",
        retry: "Retry",
        contact_information: "Contact Information",
        quick_links: "Quick Links",
        job_opportunities: "Job Opportunities",
        job_opportunities_description:
          "Explore career opportunities within the Diocese of Byumba",
        search_jobs: "Search jobs...",
        all_job_types: "All Job Types",
        all_departments: "All Departments",
        full_time: "Full Time",
        part_time: "Part Time",
        contract: "Contract",
        volunteer: "Volunteer",
        loading_jobs: "Loading jobs...",
        error_loading_jobs: "Failed to load jobs. Please try again.",
        posted: "Posted",
        deadline: "Deadline",
        expired: "Expired",
        key_requirements: "Key Requirements",
        apply_now: "Apply Now",
        salary_negotiable: "Salary Negotiable",
        ongoing_opportunity: "Ongoing Opportunity",
        no_jobs_found: "No Jobs Found",
        no_jobs_description:
          "No jobs match your current search criteria. Try adjusting your filters.",
        register: "Register",
        follow_us: "Follow Us",
        copyright: "© 2024 Diocese of Byumba. All rights reserved.",
        // Blog translations
        featured: "Featured",
        read_more: "Read More",
        read_full_article: "Read Full Article",
        load_more_posts: "Load More Posts",
        search_blog_posts: "Search blog posts...",
        diocese_blog: "Diocese Blog",
        stay_updated: "Stay updated with the latest news, events, and spiritual reflections from the Diocese of Byumba",
        subscribe: "Subscribe",
        enter_email: "Enter your email address",
        no_posts_found: "No blog posts found",
        successfully_subscribed: "Successfully subscribed to our newsletter!",
      },
      rw: {
        site_name: "Diyosezi ya Byumba",
        site_subtitle: "Diyosezi ya Byumba",
        login: "Kwinjira",
        certificates: "Ibyemezo",
        jobs: "Akazi",
        bishop_meeting: "hura na Musenyeri",
        blog: "Amakuru",
        certificate_services: "Serivisi z'Ibyemezo",
        certificate_services_description:
          "Saba ibyemezo bitandukanye bitangwa na Diyosezi ya Byumba",
        search_certificates: "Shakisha ibyemezo...",
        all_categories: "Ibyiciro Byose",
        apply_now: "Saba Ubu",
        loading_certificates: "Gupakurura ibyemezo...",
        error_loading_certificates:
          "Gupakurura ibyemezo byanze. Nyamuneka gerageza ukundi.",
        retry: "Ongera Ugerageze",
        contact_information: "Amakuru y'Itumanaho",
        quick_links: "Ihuza Ryihuse",
        job_opportunities: "Amahirwe y'Akazi",
        job_opportunities_description:
          "Shakisha amahirwe y'akazi muri Diyosezi ya Byumba",
        search_jobs: "Shakisha akazi...",
        all_job_types: "Ubwoko Bwose bw'Akazi",
        all_departments: "Amashami Yose",
        full_time: "Igihe Cyose",
        part_time: "Igihe Gito",
        contract: "Amasezerano",
        volunteer: "Ubushake",
        loading_jobs: "Gupakurura akazi...",
        error_loading_jobs:
          "Gupakurura akazi byanze. Nyamuneka gerageza ukundi.",
        posted: "Byashyizwe",
        deadline: "Itariki Nyuma",
        expired: "Byarangiye",
        key_requirements: "Ibisabwa by'Ingenzi",
        apply_now: "Saba Ubu",
        salary_negotiable: "Umushahara Ushobora Kuganirwaho",
        ongoing_opportunity: "Amahirwe Akomeje",
        no_jobs_found: "Nta kazi Kaboneka",
        no_jobs_description:
          "Nta kazi gahuje n'ibyo ushakisha. Gerageza guhindura amashyirondoro yawe.",
        register: "Kwiyandikisha",
        follow_us: "Dukurikire",
        copyright: "© 2024 Diyosezi ya Byumba. Uburenganzira bwose burarinzwe.",
        // Blog translations
        featured: "Byagaranzwe",
        read_more: "Soma Byinshi",
        read_full_article: "Soma Inyandiko Yose",
        load_more_posts: "Shyira Andi Makuru",
        search_blog_posts: "Shakisha amakuru...",
        diocese_blog: "Amakuru ya Diyosezi",
        stay_updated: "Komeza uhabwa amakuru mashya, ibirori, n'amateka y'umwuka kuva muri Diyosezi ya Byumba",
        subscribe: "Iyandikishe",
        enter_email: "Shyira aderesi yawe ya imeyili",
        no_posts_found: "Nta makuru yabonetse",
        successfully_subscribed: "Wiyandikishije neza kuri newsletter yacu!",
      },
      fr: {
        site_name: "Diocèse de Byumba",
        site_subtitle: "Diocèse de Byumba",
        login: "Connexion",
        certificates: "Certificats",
        jobs: "Emplois",
        bishop_meeting: "Rencontre avec l'Évêque",
        blog: "Blog",
        certificate_services: "Services de Certificats",
        certificate_services_description:
          "Demandez divers certificats délivrés par le Diocèse de Byumba",
        search_certificates: "Rechercher des certificats...",
        all_categories: "Toutes les Catégories",
        apply_now: "Postuler Maintenant",
        loading_certificates: "Chargement des certificats...",
        error_loading_certificates:
          "Échec du chargement des certificats. Veuillez réessayer.",
        retry: "Réessayer",
        contact_information: "Informations de Contact",
        quick_links: "Liens Rapides",
        job_opportunities: "Opportunités d'Emploi",
        job_opportunities_description:
          "Explorez les opportunités de carrière au sein du Diocèse de Byumba",
        search_jobs: "Rechercher des emplois...",
        all_job_types: "Tous les Types d'Emploi",
        all_departments: "Tous les Départements",
        full_time: "Temps Plein",
        part_time: "Temps Partiel",
        contract: "Contrat",
        volunteer: "Bénévole",
        loading_jobs: "Chargement des emplois...",
        error_loading_jobs:
          "Échec du chargement des emplois. Veuillez réessayer.",
        posted: "Publié",
        deadline: "Date Limite",
        expired: "Expiré",
        key_requirements: "Exigences Clés",
        apply_now: "Postuler Maintenant",
        salary_negotiable: "Salaire Négociable",
        ongoing_opportunity: "Opportunité Continue",
        no_jobs_found: "Aucun Emploi Trouvé",
        no_jobs_description:
          "Aucun emploi ne correspond à vos critères de recherche actuels. Essayez d'ajuster vos filtres.",
        register: "S'inscrire",
        follow_us: "Suivez-nous",
        copyright: "© 2024 Diocèse de Byumba. Tous droits réservés.",
        // Blog translations
        featured: "En Vedette",
        read_more: "Lire Plus",
        read_full_article: "Lire l'Article Complet",
        load_more_posts: "Charger Plus d'Articles",
        search_blog_posts: "Rechercher des articles...",
        diocese_blog: "Blog du Diocèse",
        stay_updated: "Restez informé des dernières nouvelles, événements et réflexions spirituelles du Diocèse de Byumba",
        subscribe: "S'abonner",
        enter_email: "Entrez votre adresse e-mail",
        no_posts_found: "Aucun article trouvé",
        successfully_subscribed: "Abonnement réussi à notre newsletter!",
      },
    };

    return translations[this.currentLanguage] || translations["en"];
  }

  updateNavigationLabels() {
    const navLabels = {
      en: {
        certificates: "Certificates",
        jobs: "Jobs",
        "bishop-meeting": "Bishop Meeting",
        blog: "Blog",
        dashboard: "Dashboard",
        profile: "Profile",
        "my-applications": "My Applications",
        "my-meetings": "My Meetings",
        notifications: "Notifications",
      },
      rw: {
        certificates: "Ibyemezo",
        jobs: "Akazi",
        "bishop-meeting": "Hura na Musenyeri",
        blog: "Amakuru",
        dashboard: "Ikibaho",
        profile: "Umwirondoro",
        "my-applications": "Ubusabe Bwanjye",
        "my-meetings": "Inama Zanjye",
        notifications: "Ubutumwa",
      },
      fr: {
        certificates: "Certificats",
        jobs: "Emplois",
        "bishop-meeting": "Rencontre avec l'Évêque",
        blog: "Blog",
        dashboard: "Tableau de Bord",
        profile: "Profil",
        "my-applications": "Mes Demandes",
        "my-meetings": "Mes Rendez-vous",
        notifications: "Notifications",
      },
    };

    const labels = navLabels[this.currentLanguage] || navLabels["en"];

    // Update main navigation
    document.querySelectorAll(".nav-link").forEach((link) => {
      const href = link.getAttribute("href");
      const span = link.querySelector("span");
      if (span && href) {
        for (const [key, value] of Object.entries(labels)) {
          if (href.includes(key)) {
            span.textContent = value;
            break;
          }
        }
      }
    });

    // Update dashboard navigation
    document.querySelectorAll(".dashboard-nav-item").forEach((item) => {
      const href = item.getAttribute("href");
      const span = item.querySelector("span");
      if (span && href) {
        for (const [key, value] of Object.entries(labels)) {
          if (href.includes(key)) {
            span.textContent = value;
            break;
          }
        }
      }
    });
  }

  updatePageTitles() {
    const pageTitles = {
      en: {
        dashboard: "User Dashboard",
        profile: "My Profile",
        "my-applications": "My Applications",
        "my-meetings": "My Meetings",
        notifications: "Notifications",
        certificates: "Certificate Services",
        jobs: "Job Opportunities",
      },
      rw: {
        dashboard: "Ikibaho cy'Ukoresha",
        profile: "Umwirondoro Wanjye",
        "my-applications": "Ubusabe Bwanjye",
        "my-meetings": "Inama Zanjye",
        notifications: "Ubutumwa",
        certificates: "Serivisi z'Ibyemezo",
        jobs: "Amahirwe y'Akazi",
      },
      fr: {
        dashboard: "Tableau de Bord Utilisateur",
        profile: "Mon Profil",
        "my-applications": "Mes Demandes",
        "my-meetings": "Mes Rendez-vous",
        notifications: "Notifications",
        certificates: "Services de Certificats",
        jobs: "Opportunités d'Emploi",
      },
    };

    const currentPage = this.getCurrentPage();
    const titles = pageTitles[this.currentLanguage] || pageTitles["en"];

    if (titles[currentPage]) {
      document.title = `${titles[currentPage]} - ${this.getSiteName()}`;

      const pageTitle = document.querySelector(".page-title");
      if (pageTitle) {
        pageTitle.textContent = titles[currentPage];
      }
    }
  }

  getSiteName() {
    const siteNames = {
      en: "Diocese of Byumba",
      rw: "Diyosezi ya Byumba",
      fr: "Diocèse de Byumba",
    };
    return siteNames[this.currentLanguage] || siteNames["en"];
  }

  getSiteSubtitle() {
    const subtitles = {
      en: "Diocese of Byumba",
      rw: "Diyosezi ya Byumba",
      fr: "Diocèse de Byumba",
    };
    return subtitles[this.currentLanguage] || subtitles["en"];
  }

  getLanguageText(key) {
    const texts = {
      en: {
        language_changed: "Language changed successfully",
        language_change_failed: "Failed to change language",
      },
      rw: {
        language_changed: "Ururimi rwahinduwe neza",
        language_change_failed: "Guhindura ururimi byanze",
      },
      fr: {
        language_changed: "Langue changée avec succès",
        language_change_failed: "Échec du changement de langue",
      },
    };

    return texts[this.currentLanguage]?.[key] || texts["en"][key] || key;
  }

  showLoadingState() {
    // Add loading class to body
    document.body.classList.add("language-loading");

    // Disable language buttons
    document.querySelectorAll(".lang-btn").forEach((btn) => {
      btn.disabled = true;
    });
  }

  hideLoadingState() {
    // Remove loading class
    document.body.classList.remove("language-loading");

    // Enable language buttons
    document.querySelectorAll(".lang-btn").forEach((btn) => {
      btn.disabled = false;
    });
  }

  showNotification(message, type = "info") {
    // Use existing notification system if available
    if (window.showNotification) {
      window.showNotification(message, type);
    } else {
      // Fallback to console
      console.log(`${type.toUpperCase()}: ${message}`);
    }
  }
}

// Initialize language manager when DOM is loaded
document.addEventListener("DOMContentLoaded", function () {
  window.languageManager = new LanguageManager();
});

// Export for use in other scripts
window.LanguageManager = LanguageManager;
