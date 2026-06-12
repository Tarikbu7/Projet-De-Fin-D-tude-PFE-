// Text for each language.
const translations = {
  en: {
    pageTitle: "Slahpc Computer Repair",
    metaDescription: "Professional on-site computer repair, diagnostics, upgrades, virus removal, data backup, and Wi-Fi support.",
    brandAria: "On-Site Computer Repair home",
    brandSmall: "Mobile repair service",
    navServices: "Services",
    navProcess: "Process",
    navReviews: "Reviews",
    themeDark: "Switch to dark mode",
    themeLight: "Switch to light mode",
    heroImageAria: "Computer repair desk with laptop, tools, and diagnostic equipment",
    heroEyebrow: "Professional computer repair at your location",
    heroTitle: "Computer Repair That Comes To You",
    heroCopy: "Slahpc provides reliable laptop and desktop repair, diagnostics, upgrades, virus removal, data backup, and Wi-Fi support with clear appointment-based service.",
    primaryActions: "Primary actions",
    bookAppointment: "Request Repair",
    callNow: "Call now",
    whatsappNow: "WhatsApp",
    whatsappAria: "Message Slahpc on WhatsApp",
    repairNeeds: "Common repair needs",
    stripSsd: "SSD upgrades",
    stripVirus: "Virus removal",
    stripNetwork: "Network setup",
    stripBackup: "Data backup",
    servicesEyebrow: "Services",
    servicesTitle: "What can I help you fix?",
    servicesCopy: "From a slow laptop to a broken screen or unreliable Wi-Fi, tell me what is happening and I will help you find the right fix.",
    hardwareTitle: "Hardware problems",
    hardwareCopy: "Screen replacement, batteries, storage drives, RAM, overheating, fan noise, power faults, and component replacement.",
    softwareTitle: "Windows & software help",
    softwareCopy: "Slow startup, system errors, application issues, Windows installation, driver problems, updates, and performance optimization.",
    virusTitle: "Virus & security cleanup",
    virusCopy: "Malware scans, browser cleanup, pop-up removal, account security checks, and antivirus configuration.",
    setupTitle: "Wi-Fi, printers & new setups",
    setupCopy: "Wi-Fi troubleshooting, printer setup, new computer configuration, email setup, backup planning, and file transfer.",
    processEyebrow: "How it works",
    processTitle: "Here is how it works",
    stepOneTitle: "Tell me what is happening",
    stepOneCopy: "Let me know what the computer is doing, what type of device it is, and where you need help.",
    stepTwoTitle: "We find a time that works",
    stepTwoCopy: "I will contact you to arrange a convenient time for the repair.",
    stepThreeTitle: "I check it and explain your options",
    stepThreeCopy: "I will diagnose the problem, explain the cost clearly, and only start the repair after you agree.",
    appointmentEyebrow: "Book support",
    appointmentTitle: "Appointment request",
    appointmentCopy: "Choose the closest service, add your address, and describe the problem in your own words. I will get back to you to arrange the repair.",
    appointmentSignIn: "Sign in to request an appointment",
    appointmentSignInCopy: "Once it is sent, you can follow the repair status from your dashboard.",
    accountAppointments: "My account and appointments",
    loginDashboard: "Sign in",
    labelService: "Service type",
    hardwarePriceNote: "Hardware repair depends on the problem and replacement parts. You will receive a quote before work begins.",
    optionChoose: "Choose one",
    optionSoftware: "Software repair",
    optionHardware: "Hardware repair",
    optionNetwork: "Wi-Fi / printer / setup",
    optionData: "Backup or data transfer",
    labelAddress: "Address or service area",
    placeholderAddress: "City, area, or full address",
    labelProblem: "Tell me what is wrong",
    placeholderProblem: "Example: laptop is slow, fan is loud, screen is cracked, or Wi-Fi will not connect",
    sendRequest: "Send request",
    reviewsTitle: "A few words from customers",
    reviewsCopy: "What people have said after getting help with their computers.",
    reviewStars: "5 out of 5 stars",
    reviewOne: "My laptop was overheating and very slow. The problem was explained clearly, and it works smoothly again.",
    reviewOneService: "Laptop repair",
    reviewTwo: "The SSD upgrade made my computer much faster. The price and the work were explained before the repair started.",
    reviewTwoService: "SSD upgrade",
    reviewThree: "My Wi-Fi and printer were set up quickly, and everything was tested before the job was finished.",
    reviewThreeService: "Home setup",
    footerText: "Slahpc. Professional computer repair and technical support.",
    shopLocation: "Shop: Tangier, Bendiban, Hawma Lwarda Street",
    backTop: "Back to top",
    openMenu: "Open menu",
    closeMenu: "Close menu",
    chooseLanguage: "Choose language"
  },
  fr: {
    pageTitle: "Réparation informatique à domicile",
    metaDescription: "Réparation informatique sur place pour matériel, logiciels, mises à niveau, suppression de virus et rendez-vous.",
    brandAria: "Accueil réparation informatique à domicile",
    brandSmall: "Service mobile",
    navServices: "Services",
    navProcess: "Méthode",
    navReviews: "Avis",
    themeDark: "Passer en mode sombre",
    themeLight: "Passer en mode clair",
    heroImageAria: "Bureau de réparation avec ordinateur portable, outils et équipement de diagnostic",
    heroEyebrow: "",
    heroTitle: "Réparation informatique sur place",
    heroCopy: "Je répare les ordinateurs portables, PC fixes, systèmes lents, pièces cassées, problèmes Wi-Fi, virus, mises à niveau, sauvegardes et installations. Prenez rendez-vous et je peux venir chez vous.",
    primaryActions: "Actions principales",
    bookAppointment: "Demander réparation",
    callNow: "Appeler maintenant",
    whatsappNow: "WhatsApp",
    whatsappAria: "Envoyer un message WhatsApp a Slahpc",
    repairNeeds: "Besoins de réparation courants",
    stripSsd: "Mises à niveau SSD",
    stripVirus: "Suppression de virus",
    stripNetwork: "Configuration réseau",
    stripBackup: "Sauvegarde des données",
    servicesEyebrow: "Ce que je répare",
    servicesTitle: "Comment puis-je vous aider ?",
    servicesCopy: "Ordinateur lent, écran cassé ou Wi-Fi instable : expliquez-moi ce qui se passe et je vous aiderai à trouver la bonne solution.",
    hardwareTitle: "Problèmes matériels",
    hardwareCopy: "Écrans cassés, batteries, disques, RAM, surchauffe, ventilateurs bruyants, problèmes d'alimentation et remplacement de composants.",
    softwareTitle: "Aide Windows et logiciels",
    softwareCopy: "Démarrage lent, erreurs système, problèmes d'applications, installation Windows, pilotes, mises à jour et optimisation.",
    virusTitle: "Nettoyage et sécurité",
    virusCopy: "Analyse des logiciels malveillants, nettoyage du navigateur, fenêtres indésirables, vérification de sécurité et installation d'antivirus.",
    setupTitle: "Wi-Fi, imprimantes et installation",
    setupCopy: "Dépannage Wi-Fi, installation d'imprimante, configuration d'un nouvel ordinateur, e-mail, sauvegardes et transfert de fichiers.",
    processEyebrow: "Comment ça marche",
    processTitle: "Voici comment ça se passe",
    stepOneTitle: "Expliquez-moi ce qui se passe",
    stepOneCopy: "Dites-moi ce que fait l'ordinateur, le type d'appareil et l'endroit où vous avez besoin d'aide.",
    stepTwoTitle: "Nous choisissons un bon moment",
    stepTwoCopy: "Je vous contacterai pour fixer un horaire qui vous convient.",
    stepThreeTitle: "Je vérifie et j'explique les solutions",
    stepThreeCopy: "Je diagnostique le problème, j'explique clairement le prix et je commence seulement avec votre accord.",
    appointmentEyebrow: "Réserver une aide",
    appointmentTitle: "Demande de rendez-vous",
    appointmentCopy: "Choisissez le service le plus proche, ajoutez votre adresse et décrivez le problème avec vos propres mots. Je vous contacterai pour organiser la réparation.",
    appointmentSignIn: "Connectez-vous pour envoyer votre demande",
    appointmentSignInCopy: "Après l'envoi, vous pourrez suivre l'état de la réparation depuis votre tableau de bord.",
    accountAppointments: "Mon compte et mes rendez-vous",
    loginDashboard: "Se connecter",
    labelService: "Type de service",
    hardwarePriceNote: "Le prix d'une réparation matérielle dépend du problème et des pièces. Vous recevrez un devis avant le début des travaux.",
    optionChoose: "Choisir",
    optionSoftware: "Réparation logicielle",
    optionHardware: "Réparation matérielle",
    optionNetwork: "Wi-Fi / imprimante / installation",
    optionData: "Sauvegarde ou transfert de données",
    labelAddress: "Adresse ou zone de service",
    placeholderAddress: "Ville, secteur ou adresse complète",
    labelProblem: "Expliquez le problème",
    placeholderProblem: "Exemple : ordinateur lent, ventilateur bruyant, écran cassé ou Wi-Fi impossible",
    sendRequest: "Envoyer la demande",
    reviewsTitle: "Quelques mots de mes clients",
    reviewsCopy: "Ce que les clients disent après avoir reçu de l'aide pour leur ordinateur.",
    reviewStars: "5 étoiles sur 5",
    reviewOne: "Mon ordinateur portable surchauffait et était très lent. Le problème a été clairement expliqué et il fonctionne à nouveau correctement.",
    reviewOneService: "Réparation d'ordinateur portable",
    reviewTwo: "La mise à niveau SSD a rendu mon ordinateur beaucoup plus rapide. Le prix et le travail ont été expliqués avant la réparation.",
    reviewTwoService: "Mise à niveau SSD",
    reviewThree: "Mon Wi-Fi et mon imprimante ont été configurés rapidement, puis tout a été testé avant la fin du travail.",
    reviewThreeService: "Installation à domicile",
    callService: "Appeler",
    email: "E-mail",
    footerText: "Réparation informatique. Support matériel et logiciel sur place.",
    shopLocation: "Boutique : Tanger, Bendiban, rue Hawma Lwarda",
    backTop: "Retour en haut",
    openMenu: "Ouvrir le menu",
    closeMenu: "Fermer le menu",
    chooseLanguage: "Choisir la langue"
  },
  ar: {
    pageTitle: "إصلاح الكمبيوتر في موقعك",
    metaDescription: "إصلاح الكمبيوتر في موقعك للأجهزة والبرامج والترقية وإزالة الفيروسات وحجز المواعيد.",
    brandAria: "الصفحة الرئيسية لخدمة إصلاح الكمبيوتر",
    brandSmall: "خدمة متنقلة",
    navServices: "الخدمات",
    navProcess: "الطريقة",
    navReviews: "الآراء",
    themeDark: "التبديل إلى الوضع الداكن",
    themeLight: "التبديل إلى الوضع الفاتح",
    heroImageAria: "مكتب إصلاح كمبيوتر مع حاسوب محمول وأدوات ومعدات فحص",
    heroEyebrow: "دعم الأجهزة والبرامج في مكانك",
    heroTitle: "إصلاح الكمبيوتر في موقعك",
    heroCopy: "أصلح الحواسيب المحمولة والمكتبية، الأنظمة البطيئة، القطع التالفة، مشاكل الواي فاي، الفيروسات، الترقيات، النسخ الاحتياطي والإعداد. احجز موعدا ويمكنني الحضور إليك.",
    primaryActions: "الإجراءات الرئيسية",
    bookAppointment: "طلب موعد",
    callNow: "اتصل الآن",
    whatsappNow: "WhatsApp",
    whatsappAria: "راسل Slahpc على واتساب",
    repairNeeds: "أشهر خدمات الإصلاح",
    stripSsd: "ترقية SSD",
    stripVirus: "إزالة الفيروسات",
    stripNetwork: "إعداد الشبكة",
    stripBackup: "نسخ احتياطي للبيانات",
    servicesEyebrow: "ما الذي أصلحه",
    servicesTitle: "كيف يمكنني مساعدتك؟",
    servicesCopy: "سواء كان الكمبيوتر بطيئا أو الشاشة مكسورة أو الواي فاي غير مستقر، اشرح لي ما يحدث وسأساعدك في إيجاد الحل المناسب.",
    hardwareTitle: "مشاكل قطع الجهاز",
    hardwareCopy: "الشاشات المكسورة، البطاريات، الأقراص، الذاكرة، السخونة، المراوح المزعجة، مشاكل الطاقة واستبدال القطع.",
    softwareTitle: "مساعدة ويندوز والبرامج",
    softwareCopy: "بطء التشغيل، أخطاء النظام، مشاكل التطبيقات، إعداد ويندوز، التعريفات، التحديثات وتحسين الأداء.",
    virusTitle: "تنظيف الفيروسات والحماية",
    virusCopy: "فحص البرمجيات الضارة، تنظيف المتصفح، إزالة النوافذ المزعجة، فحص أمان الحسابات وإعداد برامج الحماية.",
    setupTitle: "الواي فاي والطابعات والإعداد",
    setupCopy: "حل مشاكل الواي فاي، إعداد الطابعة، إعداد كمبيوتر جديد، ضبط البريد الإلكتروني، خطط النسخ الاحتياطي ونقل الملفات.",
    processEyebrow: "كيف تعمل الخدمة",
    processTitle: "هكذا تتم الخدمة",
    stepOneTitle: "اشرح لي ما الذي يحدث",
    stepOneCopy: "أخبرني بما يفعله الكمبيوتر، ونوع الجهاز، والمكان الذي تحتاج فيه إلى المساعدة.",
    stepTwoTitle: "نختار وقتا مناسبا",
    stepTwoCopy: "سأتواصل معك لتحديد وقت مناسب للإصلاح.",
    stepThreeTitle: "أفحص الجهاز وأشرح لك الحلول",
    stepThreeCopy: "سأحدد المشكلة وأشرح السعر بوضوح، ولن أبدأ الإصلاح إلا بعد موافقتك.",
    appointmentEyebrow: "احجز الدعم",
    appointmentTitle: "طلب موعد",
    appointmentCopy: "اختر أقرب خدمة لمشكلتك، وأدخل عنوانك، واشرح ما يحدث بكلماتك. سأتواصل معك لترتيب الإصلاح.",
    appointmentSignIn: "سجل الدخول لإرسال طلب الإصلاح",
    appointmentSignInCopy: "بعد الإرسال يمكنك متابعة حالة الإصلاح من لوحة التحكم.",
    accountAppointments: "حسابي ومواعيدي",
    loginDashboard: "تسجيل الدخول",
    labelService: "نوع الخدمة",
    hardwarePriceNote: "يعتمد سعر إصلاح الجهاز على المشكلة والقطع المطلوبة. ستحصل على عرض سعر قبل بدء العمل.",
    optionChoose: "اختر خدمة",
    optionSoftware: "إصلاح البرامج",
    optionHardware: "إصلاح الأجهزة",
    optionNetwork: "واي فاي / طابعة / إعداد",
    optionData: "نسخ احتياطي أو نقل بيانات",
    labelAddress: "العنوان أو منطقة الخدمة",
    placeholderAddress: "المدينة أو المنطقة أو العنوان الكامل",
    labelProblem: "اشرح ما المشكلة",
    placeholderProblem: "مثال: الكمبيوتر بطيء، المروحة مزعجة، الشاشة مكسورة، أو الواي فاي لا يعمل",
    sendRequest: "إرسال الطلب",
    reviewsTitle: "بعض كلمات العملاء",
    reviewsCopy: "ما قاله العملاء بعد مساعدتهم في حل مشاكل الكمبيوتر.",
    reviewStars: "5 نجوم من 5",
    reviewOne: "كان حاسوبي المحمول يسخن ويعمل ببطء شديد. تم شرح المشكلة بوضوح، والآن يعمل بشكل جيد من جديد.",
    reviewOneService: "إصلاح حاسوب محمول",
    reviewTwo: "جعلت ترقية SSD حاسوبي أسرع بكثير. تم شرح السعر والعمل قبل بدء الإصلاح.",
    reviewTwoService: "ترقية SSD",
    reviewThree: "تم إعداد الواي فاي والطابعة بسرعة، وتم اختبار كل شيء قبل إنهاء العمل.",
    reviewThreeService: "إعداد منزلي",
    callService: "اتصل بالخدمة",
    email: "البريد الإلكتروني",
    footerText: "إصلاح الكمبيوتر. دعم الأجهزة والبرامج في موقعك.",
    shopLocation: "المحل: طنجة، بنديبان، شارع حومة الوردة",
    backTop: "العودة للأعلى",
    openMenu: "فتح القائمة",
    closeMenu: "إغلاق القائمة",
    chooseLanguage: "اختر اللغة"
  }
};

// Get page buttons and fields.
const navToggle = document.querySelector(".nav-toggle");
const nav = document.querySelector("[data-nav]");
const year = document.querySelector("[data-year]");
const themeToggle = document.querySelector("[data-theme-toggle]");
const languageSelect = document.querySelector("[data-language-select]");
const metaDescription = document.querySelector('meta[name="description"]');

// Get translated text.
const getLanguage = () => document.documentElement.dataset.language || "en";
const t = (key) => translations[getLanguage()]?.[key] ?? translations.en[key] ?? key;

// Update the theme button.
const setThemeButton = () => {
  if (!themeToggle) {
    return;
  }

  const isDark = document.documentElement.dataset.theme === "dark";
  const label = isDark ? t("themeLight") : t("themeDark");
  const icon = isDark ? "sun" : "moon";

  themeToggle.setAttribute("aria-label", label);
  themeToggle.setAttribute("title", label);
  themeToggle.innerHTML = `<i data-lucide="${icon}" aria-hidden="true"></i><span class="sr-only" data-theme-label>${label}</span>`;
  if (window.lucide) {
    window.lucide.createIcons();
  }
};

// Update the menu button text.
const setMenuButtonLabel = () => {
  if (!navToggle || !nav) {
    return;
  }

  const isOpen = nav.classList.contains("open");
  const label = isOpen ? t("closeMenu") : t("openMenu");
  navToggle.setAttribute("aria-label", label);
  navToggle.querySelector(".sr-only").textContent = label;
};

// Change the page language.
const applyLanguage = (language) => {
  const nextLanguage = translations[language] ? language : "en";

  document.documentElement.dataset.language = nextLanguage;
  document.documentElement.lang = nextLanguage;
  document.documentElement.dir = nextLanguage === "ar" ? "rtl" : "ltr";

  document.querySelectorAll("[data-i18n]").forEach((element) => {
    element.textContent = t(element.dataset.i18n);
  });

  document.querySelectorAll("[data-i18n-placeholder]").forEach((element) => {
    element.setAttribute("placeholder", t(element.dataset.i18nPlaceholder));
  });

  document.querySelectorAll("[data-i18n-aria]").forEach((element) => {
    element.setAttribute("aria-label", t(element.dataset.i18nAria));
  });

  document.title = t("pageTitle");
  if (metaDescription) {
    metaDescription.setAttribute("content", t("metaDescription"));
  }

  if (languageSelect) {
    languageSelect.value = nextLanguage;
    languageSelect.closest(".language-control")?.setAttribute("aria-label", t("chooseLanguage"));
  }

  setThemeButton();
  setMenuButtonLabel();
};

// Show the page icons.
if (window.lucide) {
  window.lucide.createIcons();
} else {
  window.addEventListener("load", () => {
    if (window.lucide) {
      window.lucide.createIcons();
    }
  });
}

// Show the current year.
if (year) {
  year.textContent = new Date().getFullYear();
}

// Load the selected language.
applyLanguage(getLanguage());

// Save a new language.
if (languageSelect) {
  languageSelect.addEventListener("change", () => {
    localStorage.setItem("repair-language", languageSelect.value);
    applyLanguage(languageSelect.value);
  });
}

// Save the light or dark theme.
if (themeToggle) {
  themeToggle.addEventListener("click", () => {
    const nextTheme = document.documentElement.dataset.theme === "dark" ? "light" : "dark";
    document.documentElement.dataset.theme = nextTheme;
    localStorage.setItem("repair-theme", nextTheme);
    setThemeButton();
  });
}

// Open and close the mobile menu.
if (navToggle && nav) {
  navToggle.addEventListener("click", () => {
    const isOpen = nav.classList.toggle("open");
    navToggle.setAttribute("aria-expanded", String(isOpen));
    setMenuButtonLabel();
  });

  nav.addEventListener("click", (event) => {
    if (event.target.closest("a")) {
      nav.classList.remove("open");
      navToggle.setAttribute("aria-expanded", "false");
      setMenuButtonLabel();
    }
  });
}

// Add a menu shadow after scrolling.
const siteHeader = document.querySelector("[data-header]");
if (siteHeader) {
  const onScroll = () => {
    siteHeader.classList.toggle("scrolled", window.scrollY > 20);
  };
  window.addEventListener("scroll", onScroll, { passive: true });
  onScroll();
}

// Update the old price box.
if (serviceSelect && estimate) {
  serviceSelect.addEventListener("change", () => {
    const option = serviceSelect.selectedOptions[0];
    const price = option?.dataset.price || "55";
    estimate.textContent = `$${price}`;
  });
}

// Check the old repair form.
if (form && statusText) {
  form.addEventListener("submit", (event) => {
    event.preventDefault();
    statusText.classList.remove("error");

    if (!form.checkValidity()) {
      form.reportValidity();
      statusText.textContent = t("formRequired");
      statusText.classList.add("error");
      return;
    }

    const data = new FormData(form);
    const selectedService = serviceSelect?.selectedOptions[0]?.textContent.trim() || data.get("service_type");
    const checkedTime = form.querySelector('input[name="preferred_time"]:checked');
    const selectedTime = checkedTime?.closest("label")?.querySelector("span")?.textContent.trim() || data.get("preferred_time");

    statusText.textContent = t("formReady")({
      name: data.get("full_name"),
      service: selectedService,
      date: data.get("preferred_date"),
      time: selectedTime
    });

    // Clear the old form.
    form.reset();
    estimate.textContent = "$55";
  });
}
