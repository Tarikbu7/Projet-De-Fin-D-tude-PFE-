const translations = {
  en: {
    pageTitle: "On-Site Computer Repair",
    metaDescription: "On-site computer repair for hardware, software, upgrades, virus removal, and appointment-based support.",
    brandAria: "On-Site Computer Repair home",
    brandSmall: "Mobile service",
    navServices: "Services",
    navProcess: "Process",
    navAppointment: "Shop",
    navContact: "Contact",
    themeDark: "Switch to dark mode",
    themeLight: "Switch to light mode",
    heroImageAria: "Computer repair desk with laptop, tools, and diagnostic equipment",
    heroEyebrow: "Hardware and software support at your place",
    heroTitle: "On-Site Computer Repair",
    heroCopy: "I repair laptops, desktops, slow systems, broken parts, Wi-Fi issues, virus problems, upgrades, backups, and setup work. Book an appointment and I can come to you.",
    primaryActions: "Primary actions",
    bookAppointment: "Shop parts",
    callNow: "Call now",
    repairNeeds: "Common repair needs",
    stripSsd: "SSD upgrades",
    stripVirus: "Virus removal",
    stripNetwork: "Network setup",
    stripBackup: "Data backup",
    servicesEyebrow: "What I fix",
    servicesTitle: "Repair service for everyday computer problems",
    servicesCopy: "Choose the service that matches your issue. If you are not sure, start with diagnostics and I will identify the problem first.",
    hardwareTitle: "Hardware Repair",
    hardwareCopy: "Broken screens, batteries, drives, RAM, overheating, noisy fans, power issues, and component replacement.",
    softwareTitle: "Software Repair",
    softwareCopy: "Slow startup, system errors, app problems, Windows setup, driver issues, updates, and performance cleanup.",
    virusTitle: "Virus Removal",
    virusCopy: "Malware scans, browser cleanup, unwanted pop-ups, account safety checks, and security software setup.",
    setupTitle: "Home Tech Setup",
    setupCopy: "Wi-Fi troubleshooting, printer setup, new computer setup, email configuration, backup plans, and file transfer.",
    processEyebrow: "How it works",
    processTitle: "Simple appointment, clear repair path",
    stepOneTitle: "Tell me the issue",
    stepOneCopy: "Describe what is happening, the computer type, and where you need help.",
    stepTwoTitle: "Pick a time",
    stepTwoCopy: "Select the best appointment window for at-home, office, or remote support.",
    stepThreeTitle: "Repair or quote",
    stepThreeCopy: "I diagnose the problem, explain the options, and fix it if parts are not required.",
    appointmentEyebrow: "Book support",
    appointmentTitle: "Request an appointment",
    appointmentCopy: "Use this form to collect appointment details now. Later, the same fields can be sent to PHP and saved into SQL.",
    shopEyebrow: "PC parts shop",
    shopTitle: "Buy parts or request a custom PC build",
    shopCopy: "Customers can order CPUs, RAM, motherboards, SSDs, GPUs, power supplies, or ask for a complete PC build from their dashboard.",
    productCpu: "CPU",
    productCpuCopy: "Processors for gaming, school, work, and upgrade builds.",
    productRam: "RAM",
    productRamCopy: "Memory upgrades for faster multitasking and smoother systems.",
    productMobo: "Motherboard",
    productMoboCopy: "Boards matched to your CPU, case, storage, and future upgrades.",
    productSsd: "SSD",
    productSsdCopy: "Fast storage for Windows, games, and important files.",
    productGpu: "GPU",
    productGpuCopy: "Graphics cards selected by budget and performance target.",
    productPsu: "Power supply",
    productPsuCopy: "Reliable power supplies for repairs, upgrades, and new builds.",
    createAccount: "Create account",
    loginDashboard: "Login dashboard",
    estimateLabel: "Estimated starting visit",
    estimateNote: "Final price depends on parts, travel, and repair complexity.",
    labelName: "Full name",
    placeholderName: "Your name",
    labelPhone: "Phone number",
    placeholderPhone: "(555) 555-5555",
    labelService: "Service type",
    optionChoose: "Choose one",
    optionDiagnostic: "Diagnostic visit",
    optionSoftware: "Software repair",
    optionHardware: "Hardware repair",
    optionNetwork: "Wi-Fi / printer / setup",
    optionData: "Backup or data transfer",
    labelDate: "Preferred day",
    preferredTime: "Preferred time",
    timeMorning: "Morning",
    timeAfternoon: "Afternoon",
    timeEvening: "Evening",
    labelAddress: "Address or service area",
    placeholderAddress: "City, area, or full address",
    labelProblem: "Tell me what is wrong",
    placeholderProblem: "Example: laptop is slow, fan is loud, screen is cracked, or Wi-Fi will not connect",
    confirmText: "I understand this is an appointment request and the visit time will be confirmed.",
    sendRequest: "Send request",
    formRequired: "Please fill out the required appointment details.",
    formReady: ({ name, service, date, time }) => `Thanks, ${name}. Your ${service} appointment request for ${date} in the ${time} is ready to send.`,
    contactEyebrow: "Need help now?",
    contactTitle: "Call, text, or send a message for computer repair.",
    callService: "Call service",
    email: "Email",
    footerText: "Computer Repair. On-site hardware and software support.",
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
    navAppointment: "Boutique",
    navContact: "Contact",
    themeDark: "Passer en mode sombre",
    themeLight: "Passer en mode clair",
    heroImageAria: "Bureau de réparation avec ordinateur portable, outils et équipement de diagnostic",
    heroEyebrow: "Support matériel et logiciel chez vous",
    heroTitle: "Réparation informatique sur place",
    heroCopy: "Je répare les ordinateurs portables, PC fixes, systèmes lents, pièces cassées, problèmes Wi-Fi, virus, mises à niveau, sauvegardes et installations. Prenez rendez-vous et je peux venir chez vous.",
    primaryActions: "Actions principales",
    bookAppointment: "Voir la boutique",
    callNow: "Appeler maintenant",
    repairNeeds: "Besoins de réparation courants",
    stripSsd: "Mises à niveau SSD",
    stripVirus: "Suppression de virus",
    stripNetwork: "Configuration réseau",
    stripBackup: "Sauvegarde des données",
    servicesEyebrow: "Ce que je répare",
    servicesTitle: "Service de réparation pour les problèmes informatiques du quotidien",
    servicesCopy: "Choisissez le service qui correspond à votre problème. Si vous n'êtes pas sûr, commencez par un diagnostic et j'identifierai la cause.",
    hardwareTitle: "Réparation matérielle",
    hardwareCopy: "Écrans cassés, batteries, disques, RAM, surchauffe, ventilateurs bruyants, problèmes d'alimentation et remplacement de composants.",
    softwareTitle: "Réparation logicielle",
    softwareCopy: "Démarrage lent, erreurs système, problèmes d'applications, installation Windows, pilotes, mises à jour et optimisation.",
    virusTitle: "Suppression de virus",
    virusCopy: "Analyse des logiciels malveillants, nettoyage du navigateur, fenêtres indésirables, vérification de sécurité et installation d'antivirus.",
    setupTitle: "Installation informatique maison",
    setupCopy: "Dépannage Wi-Fi, installation d'imprimante, configuration d'un nouvel ordinateur, e-mail, sauvegardes et transfert de fichiers.",
    processEyebrow: "Comment ça marche",
    processTitle: "Un rendez-vous simple, une réparation claire",
    stepOneTitle: "Décrivez le problème",
    stepOneCopy: "Expliquez ce qui se passe, le type d'ordinateur et l'endroit où vous avez besoin d'aide.",
    stepTwoTitle: "Choisissez un horaire",
    stepTwoCopy: "Sélectionnez le meilleur créneau pour une aide à domicile, au bureau ou à distance.",
    stepThreeTitle: "Réparation ou devis",
    stepThreeCopy: "Je diagnostique le problème, j'explique les options et je répare si aucune pièce n'est nécessaire.",
    appointmentEyebrow: "Réserver une aide",
    appointmentTitle: "Demander un rendez-vous",
    appointmentCopy: "Ce formulaire collecte les détails du rendez-vous maintenant. Plus tard, les mêmes champs pourront être envoyés en PHP et enregistrés dans SQL.",
    shopEyebrow: "Boutique de pièces PC",
    shopTitle: "Acheter des pièces ou demander un PC sur mesure",
    shopCopy: "Les clients peuvent commander CPU, RAM, cartes mères, SSD, GPU, alimentations ou demander un PC complet depuis leur tableau de bord.",
    productCpu: "CPU",
    productCpuCopy: "Processeurs pour gaming, école, travail et mises à niveau.",
    productRam: "RAM",
    productRamCopy: "Mémoire pour accélérer le multitâche et rendre le système plus fluide.",
    productMobo: "Carte mère",
    productMoboCopy: "Cartes adaptées à votre CPU, boîtier, stockage et futures évolutions.",
    productSsd: "SSD",
    productSsdCopy: "Stockage rapide pour Windows, jeux et fichiers importants.",
    productGpu: "GPU",
    productGpuCopy: "Cartes graphiques choisies selon le budget et les performances voulues.",
    productPsu: "Alimentation",
    productPsuCopy: "Alimentations fiables pour réparations, mises à niveau et nouveaux PC.",
    createAccount: "Créer un compte",
    loginDashboard: "Connexion tableau",
    estimateLabel: "Visite à partir de",
    estimateNote: "Le prix final dépend des pièces, du déplacement et de la complexité de la réparation.",
    labelName: "Nom complet",
    placeholderName: "Votre nom",
    labelPhone: "Numéro de téléphone",
    placeholderPhone: "(555) 555-5555",
    labelService: "Type de service",
    optionChoose: "Choisir",
    optionDiagnostic: "Visite de diagnostic",
    optionSoftware: "Réparation logicielle",
    optionHardware: "Réparation matérielle",
    optionNetwork: "Wi-Fi / imprimante / installation",
    optionData: "Sauvegarde ou transfert de données",
    labelDate: "Jour préféré",
    preferredTime: "Heure préférée",
    timeMorning: "Matin",
    timeAfternoon: "Après-midi",
    timeEvening: "Soir",
    labelAddress: "Adresse ou zone de service",
    placeholderAddress: "Ville, secteur ou adresse complète",
    labelProblem: "Expliquez le problème",
    placeholderProblem: "Exemple : ordinateur lent, ventilateur bruyant, écran cassé ou Wi-Fi impossible",
    confirmText: "Je comprends qu'il s'agit d'une demande de rendez-vous et que l'heure sera confirmée.",
    sendRequest: "Envoyer la demande",
    formRequired: "Veuillez remplir les informations obligatoires du rendez-vous.",
    formReady: ({ name, service, date, time }) => `Merci, ${name}. Votre demande de rendez-vous pour ${service} le ${date} (${time}) est prête à être envoyée.`,
    contactEyebrow: "Besoin d'aide maintenant ?",
    contactTitle: "Appelez, envoyez un SMS ou un message pour une réparation informatique.",
    callService: "Appeler",
    email: "E-mail",
    footerText: "Réparation informatique. Support matériel et logiciel sur place.",
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
    navAppointment: "المتجر",
    navContact: "اتصل بنا",
    themeDark: "التبديل إلى الوضع الداكن",
    themeLight: "التبديل إلى الوضع الفاتح",
    heroImageAria: "مكتب إصلاح كمبيوتر مع حاسوب محمول وأدوات ومعدات فحص",
    heroEyebrow: "دعم الأجهزة والبرامج في مكانك",
    heroTitle: "إصلاح الكمبيوتر في موقعك",
    heroCopy: "أصلح الحواسيب المحمولة والمكتبية، الأنظمة البطيئة، القطع التالفة، مشاكل الواي فاي، الفيروسات، الترقيات، النسخ الاحتياطي والإعداد. احجز موعدا ويمكنني الحضور إليك.",
    primaryActions: "الإجراءات الرئيسية",
    bookAppointment: "تصفح القطع",
    callNow: "اتصل الآن",
    repairNeeds: "أشهر خدمات الإصلاح",
    stripSsd: "ترقية SSD",
    stripVirus: "إزالة الفيروسات",
    stripNetwork: "إعداد الشبكة",
    stripBackup: "نسخ احتياطي للبيانات",
    servicesEyebrow: "ما الذي أصلحه",
    servicesTitle: "خدمة إصلاح لمشاكل الكمبيوتر اليومية",
    servicesCopy: "اختر الخدمة المناسبة لمشكلتك. إذا لم تكن متأكدا، ابدأ بالفحص وسأحدد سبب المشكلة أولا.",
    hardwareTitle: "إصلاح الأجهزة",
    hardwareCopy: "الشاشات المكسورة، البطاريات، الأقراص، الذاكرة، السخونة، المراوح المزعجة، مشاكل الطاقة واستبدال القطع.",
    softwareTitle: "إصلاح البرامج",
    softwareCopy: "بطء التشغيل، أخطاء النظام، مشاكل التطبيقات، إعداد ويندوز، التعريفات، التحديثات وتحسين الأداء.",
    virusTitle: "إزالة الفيروسات",
    virusCopy: "فحص البرمجيات الضارة، تنظيف المتصفح، إزالة النوافذ المزعجة، فحص أمان الحسابات وإعداد برامج الحماية.",
    setupTitle: "إعداد التقنية المنزلية",
    setupCopy: "حل مشاكل الواي فاي، إعداد الطابعة، إعداد كمبيوتر جديد، ضبط البريد الإلكتروني، خطط النسخ الاحتياطي ونقل الملفات.",
    processEyebrow: "كيف تعمل الخدمة",
    processTitle: "موعد بسيط وخطة إصلاح واضحة",
    stepOneTitle: "اشرح المشكلة",
    stepOneCopy: "صف ما يحدث، نوع الكمبيوتر، والمكان الذي تحتاج فيه إلى المساعدة.",
    stepTwoTitle: "اختر الوقت",
    stepTwoCopy: "حدد أفضل وقت للمساعدة في المنزل أو المكتب أو عن بعد.",
    stepThreeTitle: "إصلاح أو عرض سعر",
    stepThreeCopy: "أفحص المشكلة، أوضح الخيارات، وأصلحها إذا لم تكن هناك حاجة لقطع إضافية.",
    appointmentEyebrow: "احجز الدعم",
    appointmentTitle: "طلب موعد",
    appointmentCopy: "استخدم هذا النموذج لجمع تفاصيل الموعد الآن. لاحقا يمكن إرسال نفس الحقول إلى PHP وحفظها في SQL.",
    shopEyebrow: "متجر قطع الكمبيوتر",
    shopTitle: "اشتر قطعا أو اطلب تجميع كمبيوتر",
    shopCopy: "يمكن للعملاء طلب CPU وRAM ولوحات أم وSSD وGPU ومزودات طاقة أو طلب تجميع كمبيوتر كامل من لوحة التحكم.",
    productCpu: "المعالج",
    productCpuCopy: "معالجات للألعاب والدراسة والعمل والترقيات.",
    productRam: "الذاكرة RAM",
    productRamCopy: "ترقية ذاكرة لتعدد مهام أسرع ونظام أكثر سلاسة.",
    productMobo: "اللوحة الأم",
    productMoboCopy: "لوحات مناسبة للمعالج والصندوق والتخزين والترقيات المستقبلية.",
    productSsd: "SSD",
    productSsdCopy: "تخزين سريع لويندوز والألعاب والملفات المهمة.",
    productGpu: "كرت الشاشة",
    productGpuCopy: "كروت شاشة حسب الميزانية والأداء المطلوب.",
    productPsu: "مزود الطاقة",
    productPsuCopy: "مزودات طاقة موثوقة للإصلاح والترقية والتجميع.",
    createAccount: "إنشاء حساب",
    loginDashboard: "دخول لوحة التحكم",
    estimateLabel: "بداية سعر الزيارة",
    estimateNote: "السعر النهائي يعتمد على القطع، المسافة، وصعوبة الإصلاح.",
    labelName: "الاسم الكامل",
    placeholderName: "اسمك",
    labelPhone: "رقم الهاتف",
    placeholderPhone: "(555) 555-5555",
    labelService: "نوع الخدمة",
    optionChoose: "اختر خدمة",
    optionDiagnostic: "زيارة فحص",
    optionSoftware: "إصلاح البرامج",
    optionHardware: "إصلاح الأجهزة",
    optionNetwork: "واي فاي / طابعة / إعداد",
    optionData: "نسخ احتياطي أو نقل بيانات",
    labelDate: "اليوم المفضل",
    preferredTime: "الوقت المفضل",
    timeMorning: "الصباح",
    timeAfternoon: "بعد الظهر",
    timeEvening: "المساء",
    labelAddress: "العنوان أو منطقة الخدمة",
    placeholderAddress: "المدينة أو المنطقة أو العنوان الكامل",
    labelProblem: "اشرح ما المشكلة",
    placeholderProblem: "مثال: الكمبيوتر بطيء، المروحة مزعجة، الشاشة مكسورة، أو الواي فاي لا يعمل",
    confirmText: "أفهم أن هذا طلب موعد وأن وقت الزيارة سيتم تأكيده.",
    sendRequest: "إرسال الطلب",
    formRequired: "يرجى تعبئة تفاصيل الموعد المطلوبة.",
    formReady: ({ name, service, date, time }) => `شكرا ${name}. طلب موعد ${service} بتاريخ ${date} في فترة ${time} جاهز للإرسال.`,
    contactEyebrow: "تحتاج مساعدة الآن؟",
    contactTitle: "اتصل أو أرسل رسالة لخدمة إصلاح الكمبيوتر.",
    callService: "اتصل بالخدمة",
    email: "البريد الإلكتروني",
    footerText: "إصلاح الكمبيوتر. دعم الأجهزة والبرامج في موقعك.",
    backTop: "العودة للأعلى",
    openMenu: "فتح القائمة",
    closeMenu: "إغلاق القائمة",
    chooseLanguage: "اختر اللغة"
  }
};

const navToggle = document.querySelector(".nav-toggle");
const nav = document.querySelector("[data-nav]");
const year = document.querySelector("[data-year]");
const form = document.querySelector("[data-booking-form]");
const statusText = document.querySelector("[data-form-status]");
const serviceSelect = document.querySelector("[data-service-select]");
const estimate = document.querySelector("[data-estimate]");
const themeToggle = document.querySelector("[data-theme-toggle]");
const languageSelect = document.querySelector("[data-language-select]");
const metaDescription = document.querySelector('meta[name="description"]');

const getLanguage = () => document.documentElement.dataset.language || "en";
const t = (key) => translations[getLanguage()]?.[key] ?? translations.en[key] ?? key;

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

const setMenuButtonLabel = () => {
  if (!navToggle || !nav) {
    return;
  }

  const isOpen = nav.classList.contains("is-open");
  const label = isOpen ? t("closeMenu") : t("openMenu");
  navToggle.setAttribute("aria-label", label);
  navToggle.querySelector(".sr-only").textContent = label;
};

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

if (window.lucide) {
  window.lucide.createIcons();
} else {
  window.addEventListener("load", () => {
    if (window.lucide) {
      window.lucide.createIcons();
    }
  });
}

if (year) {
  year.textContent = new Date().getFullYear();
}

applyLanguage(getLanguage());

if (languageSelect) {
  languageSelect.addEventListener("change", () => {
    localStorage.setItem("repair-language", languageSelect.value);
    applyLanguage(languageSelect.value);
  });
}

if (themeToggle) {
  themeToggle.addEventListener("click", () => {
    const nextTheme = document.documentElement.dataset.theme === "dark" ? "light" : "dark";
    document.documentElement.dataset.theme = nextTheme;
    localStorage.setItem("repair-theme", nextTheme);
    setThemeButton();
  });
}

if (navToggle && nav) {
  navToggle.addEventListener("click", () => {
    const isOpen = nav.classList.toggle("is-open");
    navToggle.setAttribute("aria-expanded", String(isOpen));
    setMenuButtonLabel();
  });

  nav.addEventListener("click", (event) => {
    if (event.target.closest("a")) {
      nav.classList.remove("is-open");
      navToggle.setAttribute("aria-expanded", "false");
      setMenuButtonLabel();
    }
  });
}

if (serviceSelect && estimate) {
  serviceSelect.addEventListener("change", () => {
    const option = serviceSelect.selectedOptions[0];
    const price = option?.dataset.price || "55";
    estimate.textContent = `$${price}`;
  });
}

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

    // Later: replace this front-end confirmation with fetch("appointment.php", { method: "POST", body: data }).
    form.reset();
    estimate.textContent = "$55";
  });
}
