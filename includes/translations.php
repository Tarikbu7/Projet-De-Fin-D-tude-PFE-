<?php
declare(strict_types=1);

// Save the selected language.
$languages = ['en', 'fr', 'ar'];
if (isset($_GET['lang']) && in_array($_GET['lang'], $languages, true)) {
    $_SESSION['lang'] = $_GET['lang'];
}
$lang = $_SESSION['lang'] ?? 'en';

// Text for each language.
$i18n = [
    'en' => [
        'dashboard' => 'Dashboard', 'admin' => 'Admin', 'user' => 'User', 'logout' => 'Logout',
        'login' => 'Login', 'register' => 'Register', 'email' => 'Email', 'password' => 'Password',
        'sign_in' => 'Sign in', 'no_account' => 'Do not have an account?', 'create_account' => 'Create account',
        'name' => 'Name', 'phone' => 'Phone', 'address' => 'Address', 'city' => 'City', 'save' => 'Save',
        'appointments' => 'Appointments', 'products' => 'Products', 'orders' => 'Orders',
        'messages' => 'Messages', 'services' => 'Services', 'invoices' => 'Invoices',
        'customers' => 'Customers', 'customer' => 'Customer', 'stats' => 'Stats', 'pc_build' => 'PC build request',
        'status' => 'Status', 'date' => 'Date', 'time' => 'Time', 'details' => 'Details',
        'request_appointment' => 'Request appointment', 'place_order' => 'Place order',
        'send_message' => 'Send message', 'submit' => 'Submit', 'pending' => 'Pending',
        'accepted' => 'Accepted', 'in_progress' => 'In progress', 'completed' => 'Completed',
        'cancelled' => 'Cancelled', 'welcome' => 'Welcome', 'admin_dashboard' => 'Admin dashboard',
        'user_dashboard' => 'User dashboard', 'home' => 'Home', 'language' => 'Language',
        'no_rows' => 'No records yet.', 'setup_needed' => 'Database setup is needed.',
        'open_site' => 'Open site', 'quantity' => 'Quantity', 'price' => 'Price', 'total' => 'Total',
        'budget' => 'Budget', 'purpose' => 'Purpose', 'subject' => 'Subject', 'message' => 'Message',
        'create_invoice' => 'Create invoice', 'amount' => 'Amount',
        'awaiting_quote' => 'Awaiting quote',
        'track_repairs' => 'Track your repair appointment requests and their current status here.',
        'review' => 'Review', 'after_completion' => 'After completion', 'available' => 'Available',
        'leave_review' => 'Leave a review', 'leave_review_desc' => 'Choose a completed service, rate it, then write your comment.',
        'which_service_review' => '1. Which service do you want to review?',
        'choose_completed_service' => 'Choose a completed service',
        'how_was_service' => '2. How was the service?',
        'write_comment' => '3. Write your comment',
        'write_comment_placeholder' => 'Tell us what you liked or what could be better.',
        'send_review' => 'Send my review',
        'no_completed_service' => 'There is no completed service available to review yet.',
        'previous_reviews' => 'Your previous reviews',
        'my_reviews' => 'My reviews',
        'my_reviews_desc' => 'After a repair is completed, you can share your experience here.',
        'review_after_completed' => 'You can write a review after an appointment is marked as completed.',
        'rating' => 'Rating', 'choose_rating' => 'Choose a rating',
        'excellent' => 'Excellent', 'very_good' => 'Very good', 'good' => 'Good', 'fair' => 'Fair', 'poor' => 'Poor',
        'your_review' => 'Your review', 'review_placeholder' => 'Tell us how the repair went',
        'stars' => 'stars',
        'price_after_diagnosis' => 'Price after diagnosis',
        'choose_one' => 'Choose one',
        'service_type_label' => 'Service type',
        'address_label' => 'Address or service area',
        'problem_label' => 'Tell me what is wrong',
        'problem_placeholder' => 'Describe the computer problem',
        'send_request' => 'Send request',
        'hardware_price_note' => 'Hardware repair depends on the problem and replacement parts. You will receive a quote before work begins.',
    ],
    'fr' => [
        'dashboard' => 'Tableau de bord', 'admin' => 'Admin', 'user' => 'Utilisateur', 'logout' => 'Déconnexion',
        'login' => 'Connexion', 'register' => 'Inscription', 'email' => 'E-mail', 'password' => 'Mot de passe',
        'name' => 'Nom', 'phone' => 'Téléphone', 'address' => 'Adresse', 'city' => 'Ville', 'save' => 'Enregistrer',
        'appointments' => 'Rendez-vous', 'products' => 'Produits', 'orders' => 'Commandes',
        'messages' => 'Messages', 'services' => 'Prix des services', 'invoices' => 'Factures',
        'customers' => 'Clients', 'customer' => 'Client', 'stats' => 'Statistiques', 'pc_build' => 'Demande de PC sur mesure',
        'status' => 'Statut', 'date' => 'Date', 'time' => 'Heure', 'details' => 'Détails',
        'request_appointment' => 'Demander un rendez-vous', 'place_order' => 'Passer commande',
        'send_message' => 'Envoyer un message', 'submit' => 'Envoyer', 'pending' => 'En attente',
        'accepted' => 'Accepté', 'in_progress' => 'En cours', 'completed' => 'Terminé',
        'cancelled' => 'Annulé', 'welcome' => 'Bienvenue', 'admin_dashboard' => 'Tableau admin',
        'user_dashboard' => 'Tableau utilisateur', 'home' => 'Accueil', 'language' => 'Langue',
        'no_rows' => 'Aucun enregistrement.', 'setup_needed' => 'La base de données doit être installée.',
        'open_site' => 'Ouvrir le site', 'quantity' => 'Quantité', 'price' => 'Prix', 'total' => 'Total',
        'budget' => 'Budget', 'purpose' => 'Utilisation', 'subject' => 'Sujet', 'message' => 'Message',
        'create_invoice' => 'Créer une facture', 'amount' => 'Montant',
        'awaiting_quote' => 'Devis en attente',
        'track_repairs' => 'Suivez ici vos demandes de rendez-vous de réparation et leur statut actuel.',
        'review' => 'Avis', 'after_completion' => 'Après la fin', 'available' => 'Disponible',
        'leave_review' => 'Laisser un avis', 'leave_review_desc' => 'Choisissez un service terminé, notez-le, puis écrivez votre commentaire.',
        'which_service_review' => '1. Quel service souhaitez-vous évaluer ?',
        'choose_completed_service' => 'Choisir un service terminé',
        'how_was_service' => '2. Comment était le service ?',
        'write_comment' => '3. Écrivez votre commentaire',
        'write_comment_placeholder' => 'Dites-nous ce que vous avez aimé ou ce qui pourrait être amélioré.',
        'send_review' => 'Envoyer mon avis',
        'no_completed_service' => 'Aucun service terminé disponible pour un avis pour le moment.',
        'previous_reviews' => 'Vos avis précédents',
        'my_reviews' => 'Mes avis',
        'my_reviews_desc' => 'Après une réparation terminée, vous pouvez partager votre expérience ici.',
        'review_after_completed' => 'Vous pouvez rédiger un avis après qu\'un rendez-vous est marqué comme terminé.',
        'rating' => 'Note', 'choose_rating' => 'Choisir une note',
        'excellent' => 'Excellent', 'very_good' => 'Très bien', 'good' => 'Bien', 'fair' => 'Passable', 'poor' => 'Médiocre',
        'your_review' => 'Votre avis', 'review_placeholder' => 'Dites-nous comment s\'est passée la réparation',
        'stars' => 'étoiles',
        'price_after_diagnosis' => 'Prix après diagnostic',
        'choose_one' => 'Choisir',
        'service_type_label' => 'Type de service',
        'address_label' => 'Adresse ou zone de service',
        'problem_label' => 'Décrivez le problème',
        'problem_placeholder' => 'Décrivez le problème informatique',
        'send_request' => 'Envoyer la demande',
        'hardware_price_note' => 'La réparation matérielle dépend du problème et des pièces de rechange. Vous recevrez un devis avant le début des travaux.',
    ],
    'ar' => [
        'dashboard' => 'لوحة التحكم', 'admin' => 'المدير', 'user' => 'المستخدم', 'logout' => 'تسجيل الخروج',
        'login' => 'تسجيل الدخول', 'register' => 'إنشاء حساب', 'email' => 'البريد الإلكتروني', 'password' => 'كلمة المرور',
        'name' => 'الاسم', 'phone' => 'الهاتف', 'address' => 'العنوان', 'city' => 'المدينة', 'save' => 'حفظ',
        'appointments' => 'المواعيد', 'products' => 'المنتجات', 'orders' => 'الطلبات',
        'messages' => 'الرسائل', 'services' => 'الخدمات', 'invoices' => 'الفواتير',
        'customers' => 'العملاء', 'customer' => 'العميل', 'stats' => 'الإحصائيات', 'pc_build' => 'طلب تجميع كمبيوتر',
        'status' => 'الحالة', 'date' => 'التاريخ', 'time' => 'الوقت', 'details' => 'التفاصيل',
        'request_appointment' => 'طلب موعد', 'place_order' => 'إرسال طلب شراء',
        'send_message' => 'إرسال رسالة', 'submit' => 'إرسال', 'pending' => 'قيد الانتظار',
        'accepted' => 'مقبول', 'in_progress' => 'قيد العمل', 'completed' => 'مكتمل',
        'cancelled' => 'ملغي', 'welcome' => 'مرحبا', 'admin_dashboard' => 'لوحة تحكم المدير',
        'user_dashboard' => 'لوحة تحكم المستخدم', 'home' => 'الرئيسية', 'language' => 'اللغة',
        'no_rows' => 'لا توجد سجلات بعد.', 'setup_needed' => 'يجب إعداد قاعدة البيانات.',
        'open_site' => 'فتح الموقع', 'quantity' => 'الكمية', 'price' => 'السعر', 'total' => 'المجموع',
        'budget' => 'الميزانية', 'purpose' => 'الاستخدام', 'subject' => 'الموضوع', 'message' => 'الرسالة',
        'create_invoice' => 'إنشاء فاتورة', 'amount' => 'المبلغ',
        'awaiting_quote' => 'في انتظار عرض السعر',
        'track_repairs' => 'تابع هنا طلبات مواعيد الإصلاح وحالتها الراهنة.',
        'review' => 'تقييم', 'after_completion' => 'بعد الانتهاء', 'available' => 'متاح',
        'leave_review' => 'اترك تقييماً', 'leave_review_desc' => 'اختر خدمة مكتملة، قيّمها، ثم اكتب تعليقك.',
        'which_service_review' => '1. أي خدمة تريد تقييمها؟',
        'choose_completed_service' => 'اختر خدمة مكتملة',
        'how_was_service' => '2. كيف كانت الخدمة؟',
        'write_comment' => '3. اكتب تعليقك',
        'write_comment_placeholder' => 'أخبرنا بما أعجبك وما يمكن تحسينه.',
        'send_review' => 'إرسال تقييمي',
        'no_completed_service' => 'لا توجد خدمة مكتملة متاحة للتقييم حالياً.',
        'previous_reviews' => 'تقييماتك السابقة',
        'my_reviews' => 'تقييماتي',
        'my_reviews_desc' => 'بعد اكتمال الإصلاح، يمكنك مشاركة تجربتك هنا.',
        'review_after_completed' => 'يمكنك كتابة تقييم بعد وضع علامة "مكتمل" على الموعد.',
        'rating' => 'التقييم', 'choose_rating' => 'اختر تقييماً',
        'excellent' => 'ممتاز', 'very_good' => 'جيد جداً', 'good' => 'جيد', 'fair' => 'مقبول', 'poor' => 'ضعيف',
        'your_review' => 'تقييمك', 'review_placeholder' => 'أخبرنا كيف سارت عملية الإصلاح',
        'stars' => 'نجوم',
        'price_after_diagnosis' => 'السعر بعد التشخيص',
        'choose_one' => 'اختر',
        'service_type_label' => 'نوع الخدمة',
        'address_label' => 'العنوان أو منطقة الخدمة',
        'problem_label' => 'أخبرني بالمشكلة',
        'problem_placeholder' => 'صف مشكلة الحاسوب',
        'send_request' => 'إرسال الطلب',
        'hardware_price_note' => 'يعتمد إصلاح الأجهزة على طبيعة المشكلة وقطع الغيار. ستتلقى عرض سعر قبل بدء العمل.',
    ],
];

// Language and safe text tools.
function lang(): string {
    return $_SESSION['lang'] ?? 'en';
}

function is_rtl(): bool {
    return lang() === 'ar';
}

function t(string $key): string {
    global $i18n;
    return $i18n[lang()][$key] ?? $i18n['en'][$key] ?? $key;
}

// Translate service names.
function translate_service(string $name): string {
    $map = [
        'en' => [
            'Hardware repair'          => 'Hardware repair',
            'Software repair'          => 'Software repair',
            'Wi-Fi / printer / setup'  => 'Wi-Fi / printer / setup',
            'Backup or data transfer'  => 'Backup or data transfer',
            'Diagnostic visit'         => 'Diagnostic visit',
        ],
        'fr' => [
            'Hardware repair'          => 'Réparation matérielle',
            'Software repair'          => 'Réparation logicielle',
            'Wi-Fi / printer / setup'  => 'Wi-Fi / imprimante / configuration',
            'Backup or data transfer'  => 'Sauvegarde ou transfert de données',
            'Diagnostic visit'         => 'Visite de diagnostic',
        ],
        'ar' => [
            'Hardware repair'          => 'إصلاح الأجهزة',
            'Software repair'          => 'إصلاح البرامج',
            'Wi-Fi / printer / setup'  => 'واي فاي / طابعة / إعداد',
            'Backup or data transfer'  => 'نسخ احتياطي أو نقل البيانات',
            'Diagnostic visit'         => 'زيارة التشخيص',
        ],
    ];
    $lang = lang();
    return $map[$lang][$name] ?? $map['en'][$name] ?? $name;
}

// Translate status names.
function status_label(string $status): string {
    return match ($status) {
        'Pending' => t('pending'),
        'Confirmed', 'Accepted' => t('accepted'),
        'In progress' => t('in_progress'),
        'Completed' => t('completed'),
        'Cancelled' => t('cancelled'),
        default => $status,
    };
}
