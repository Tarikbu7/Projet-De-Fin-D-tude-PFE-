<?php
require __DIR__ . '/includes/config.php';

// Get the user and form security code.
$user = current_user();
$csrfToken = csrf_token();

// Save a repair request.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['action'] ?? '') !== 'appointment') {
        http_response_code(400);
        exit('Unsupported action.');
    }

    if (!$user) {
        redirect('login.php');
    }
    if ($user['role'] === 'admin') {
        redirect('admin.php');
    }

    $serviceType = trim((string)($_POST['service_type'] ?? ''));
    $address = trim((string)($_POST['address'] ?? ''));
    $problemDetails = trim((string)($_POST['problem_details'] ?? ''));
    verify_csrf();
    $pdo = db();
    $service = $pdo->prepare('SELECT name, base_price FROM services WHERE active = 1 AND name = ? LIMIT 1');
    $service->execute([$serviceType]);
    $selectedService = $service->fetch();

    if (
        !$selectedService
        || $address === ''
        || mb_strlen($address) > 255
        || $problemDetails === ''
        || mb_strlen($problemDetails) > 3000
    ) {
        flash('Please fill out all appointment fields.');
        redirect('index.php#appointment');
    }

    $stmt = $pdo->prepare('INSERT INTO appointments (user_id, service_type, address, problem_details, price) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([
        $user['id'],
        $serviceType,
        $address,
        $problemDetails,
        $serviceType === 'Hardware repair' ? null : $selectedService['base_price'],
    ]);
    flash('Repair appointment request sent.');
    redirect('index.php#appointment');
}

// Prepare the home page data.
$notice = flash();
$services = [];
if ($user && $user['role'] !== 'admin') {
    $services = db()->query('SELECT name, base_price FROM services WHERE active = 1 ORDER BY name')->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description"
    content="Professional on-site computer repair, diagnostics, upgrades, virus removal, data backup, and Wi-Fi support.">
  <title>Slahpc Computer Repair</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="preconnect" href="https://images.unsplash.com">
  <link rel="preconnect" href="https://unpkg.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <script>
    (() => {
      const savedTheme = localStorage.getItem("repair-theme");
      const systemTheme = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
      const savedLanguage = localStorage.getItem("repair-language") || "en";
      document.documentElement.dataset.theme = savedTheme || systemTheme;
      document.documentElement.dataset.language = savedLanguage;
      document.documentElement.lang = savedLanguage;
      document.documentElement.dir = savedLanguage === "ar" ? "rtl" : "ltr";
    })();
  </script>
  <link rel="stylesheet" href="assets/styles.css">
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js" defer></script>
  <script src="assets/app.js" defer></script>
</head>

<body>
  <!-- Top menu -->
  <header class="site-header" data-header>
    <a class="brand" href="#home" aria-label="On-Site Computer Repair home" data-i18n-aria="brandAria">
      <span class="brand-mark">CR</span>
      <span>
        <strong>Slahpc</strong>
        <small data-i18n="brandSmall">Mobile repair service</small>
      </span>
    </a>

    <div class="header-actions">
      <nav class="site-nav" id="site-nav" data-nav>
        <a href="#services" data-i18n="navServices">Services</a>
        <a href="#process" data-i18n="navProcess">Process</a>
        <a href="#reviews" data-i18n="navReviews">Reviews</a>
        <span class="nav-auth" data-auth-actions>
          <?php if ($user): ?>
            <?php if ($user['role'] === 'admin'): ?>
              <a class="nav-button primary" href="admin.php">
                <i data-lucide="layout-dashboard" aria-hidden="true"></i>
                <span><?= e(t('dashboard')) ?></span>
              </a>
            <?php else: ?>
              <a class="nav-button primary account-button" href="user-dashboard.php"
                aria-label="My account and appointments" title="My account and appointments"
                data-i18n-aria="accountAppointments">
                <i data-lucide="circle-user-round" aria-hidden="true"></i>
              </a>
            <?php endif; ?>
            <?= logout_form('nav-logout-form') ?>
          <?php else: ?>
            <a class="nav-button primary" href="login.php">
              <i data-lucide="log-in" aria-hidden="true"></i>
              <span data-i18n="loginDashboard">Sign in</span>
            </a>
          <?php endif; ?>
        </span>
      </nav>

      <label class="language-control" aria-label="Choose language">
        <i data-lucide="languages" aria-hidden="true"></i>
        <select data-language-select>
          <option value="en">EN</option>
          <option value="fr">FR</option>
          <option value="ar">AR</option>
        </select>
      </label>

      <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="site-nav">
        <i data-lucide="menu" aria-hidden="true"></i>
        <span class="sr-only">Open menu</span>
      </button>
    </div>
  </header>

  <!-- Main page -->
  <main>
    <!-- Main welcome area -->
    <section class="hero" id="home" aria-labelledby="hero-title">
      <div class="hero-bg" role="img" aria-label="Computer repair desk with laptop, tools, and diagnostic equipment"
        data-i18n-aria="heroImageAria"></div>
      <div class="hero-overlay"></div>

      <div class="hero-content">
        <p class="eyebrow" data-i18n="heroEyebrow">Professional computer repair at your location</p>
        <h1 id="hero-title" data-i18n="heroTitle">Computer Repair That Comes To You</h1>
        <p class="hero-copy" data-i18n="heroCopy">
          Slahpc provides reliable laptop and desktop repair, diagnostics, upgrades, virus removal, data backup, Wi-Fi
          support, and custom PC setup with clear pricing and appointment-based service.
        </p>

        <div class="hero-actions" aria-label="Primary actions" data-i18n-aria="primaryActions">
          <a class="button primary" href="#appointment">
            <i data-lucide="calendar-check" aria-hidden="true"></i>
            <span data-i18n="bookAppointment">Request Repair</span>
          </a>
          <a class="button whatsapp"
            href="https://wa.me/212617248216?text=Hello%20Slahpc%2C%20I%20need%20help%20with%20my%20computer."
            target="_blank" rel="noopener" aria-label="Message Slahpc on WhatsApp" data-i18n-aria="whatsappAria">
            <i data-lucide="message-circle" aria-hidden="true"></i>
            <span data-i18n="whatsappNow">WhatsApp</span>
          </a>
        </div>
      </div>
    </section>

    <!-- Common repairs -->
    <section class="trust-strip" aria-label="Common repair needs" data-i18n-aria="repairNeeds">
      <div class="strip-item">
        <i data-lucide="hard-drive" aria-hidden="true"></i>
        <span data-i18n="stripSsd">SSD upgrades</span>
      </div>
      <div class="strip-item">
        <i data-lucide="shield-check" aria-hidden="true"></i>
        <span data-i18n="stripVirus">Virus removal</span>
      </div>
      <div class="strip-item">
        <i data-lucide="wifi" aria-hidden="true"></i>
        <span data-i18n="stripNetwork">Network setup</span>
      </div>
      <div class="strip-item">
        <i data-lucide="database-backup" aria-hidden="true"></i>
        <span data-i18n="stripBackup">Data backup</span>
      </div>
    </section>

    <!-- Services -->
    <section class="section services" id="services" aria-labelledby="services-title">
      <div class="section-heading">
        <h2 id="services-title" data-i18n="servicesTitle">What can I help you fix?</h2>
        <p data-i18n="servicesCopy">From a slow laptop to a broken screen or unreliable Wi-Fi, tell me what is happening
          and I will help you find the right fix.</p>
      </div>

      <div class="service-grid">
        <article class="service-card">
          <i data-lucide="cpu" aria-hidden="true"></i>
          <h3 data-i18n="hardwareTitle">Hardware problems</h3>
          <p data-i18n="hardwareCopy">Screen replacement, batteries, storage drives, RAM, overheating, fan noise, power
            faults, and component replacement.</p>
        </article>
        <article class="service-card">
          <i data-lucide="monitor-cog" aria-hidden="true"></i>
          <h3 data-i18n="softwareTitle">Windows &amp; software help</h3>
          <p data-i18n="softwareCopy">Slow startup, system errors, application issues, Windows installation, driver
            problems, updates, and performance optimization.</p>
        </article>
        <article class="service-card">
          <i data-lucide="bug-off" aria-hidden="true"></i>
          <h3 data-i18n="virusTitle">Virus &amp; security cleanup</h3>
          <p data-i18n="virusCopy">Malware scans, browser cleanup, pop-up removal, account security checks, and
            antivirus configuration.</p>
        </article>
        <article class="service-card">
          <i data-lucide="router" aria-hidden="true"></i>
          <h3 data-i18n="setupTitle">Wi-Fi, printers &amp; new setups</h3>
          <p data-i18n="setupCopy">Wi-Fi troubleshooting, printer setup, new computer configuration, email setup, backup
            planning, and file transfer.</p>
        </article>
      </div>
    </section>

    <!-- How it works -->
    <section class="section process-band" id="process" aria-labelledby="process-title">
      <div class="section-heading compact">
        <h2 id="process-title" data-i18n="processTitle">Here is how it works</h2>
      </div>

      <ol class="process-list">
        <li>
          <span>1</span>
          <div>
            <h3 data-i18n="stepOneTitle">Tell me what is happening</h3>
            <p data-i18n="stepOneCopy">Let me know what the computer is doing, what type of device it is, and where you
              need help.</p>
          </div>
        </li>
        <li>
          <span>2</span>
          <div>
            <h3 data-i18n="stepTwoTitle">We find a time that works</h3>
            <p data-i18n="stepTwoCopy">I will contact you to arrange a convenient time for the repair.</p>
          </div>
        </li>
        <li>
          <span>3</span>
          <div>
            <h3 data-i18n="stepThreeTitle">I check it and explain your options</h3>
            <p data-i18n="stepThreeCopy">I will diagnose the problem, explain the cost clearly, and only start the
              repair after you agree.</p>
          </div>
        </li>
      </ol>
    </section>

    <!-- Repair request -->
    <section class="section appointment" id="appointment" aria-labelledby="appointment-title">
      <div class="appointment-copy">
        <h2 id="appointment-title" data-i18n="appointmentTitle">Appointment request</h2>
        <p data-i18n="appointmentCopy">Choose the closest service, add your address, and describe the problem in your
          own words. I will get back to you to arrange the repair.</p>
      </div>
      <div>
        <?php if ($notice): ?>
          <p class="notice <?= $notice === 'Repair appointment request sent.' ? 'success' : 'error' ?>">
            <?= e($notice) ?>
          </p>
        <?php endif; ?>


<!-- method post for poointement for -->

        <?php if ($user && $user['role'] !== 'admin'): ?>
          <form action="index.php#appointment" method="post" class="booking-form home-appointment-form">
            <input type="hidden" name="action" value="appointment">
            <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
            <label>
              <span><?= e(t('service_type_label')) ?></span>
              <select name="service_type" required>
                <option value=""><?= e(t('choose_one')) ?></option>
                <?php foreach ($services as $service): ?>
                  <?php
                  $priceLabel = $service['name'] === 'Hardware repair'
                      ? t('price_after_diagnosis')
                      : number_format((float)$service['base_price'], 0) . ' MAD';
                  ?>
                  <option value="<?= e($service['name']) ?>">
                    <?= e(translate_service($service['name']) . ' - ' . $priceLabel) ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <small class="service-price-note"><?= e(t('hardware_price_note')) ?></small>
            </label>
            <label>
              <span><?= e(t('address_label')) ?></span>
              <input type="text" name="address" value="<?= e($user['address'] ?? '') ?>"
                autocomplete="street-address" required>
            </label>
            <label>
              <span><?= e(t('problem_label')) ?></span>
              <textarea name="problem_details" required
                placeholder="<?= e(t('problem_placeholder')) ?>"></textarea>
            </label>
            // Submit button
            <button class="button primary full" type="submit">
              <i data-lucide="send" aria-hidden="true"></i>
              <span><?= e(t('send_request')) ?></span>
            </button>
          </form>
        <?php elseif (!$user): ?>
          <div class="booking-form appointment-signin">
            <i data-lucide="log-in" aria-hidden="true"></i>
            <h3 data-i18n="appointmentSignIn">Sign in to request an appointment</h3>
            <p data-i18n="appointmentSignInCopy">Your account lets you request an appointment and track its status.</p>
            <a class="button primary full" href="login.php">
              <span data-i18n="loginDashboard">Sign in</span>
            </a>
          </div>
        <?php endif; ?>
      </div>
    </section>

    <!-- Customer reviews -->
    <section class="section reviews-section" id="reviews" aria-labelledby="reviews-title">
      <div class="reviews-heading">
        <h2 id="reviews-title" data-i18n="reviewsTitle">A few words from customers</h2>
        <p data-i18n="reviewsCopy">What people have said after getting help with their computers.</p>
      </div>
      <div class="review-grid">
        <article class="review-card">
          <div class="review-stars" aria-label="5 out of 5 stars" data-i18n-aria="reviewStars">
            <i data-lucide="star" aria-hidden="true"></i><i data-lucide="star" aria-hidden="true"></i><i
              data-lucide="star" aria-hidden="true"></i><i data-lucide="star" aria-hidden="true"></i><i
              data-lucide="star" aria-hidden="true"></i>
          </div>
          <blockquote data-i18n="reviewOne">My laptop was overheating and very slow. The problem was explained clearly,
            and it works smoothly again.</blockquote>
          <footer><strong>Youssef A.</strong><span data-i18n="reviewOneService">Laptop repair</span></footer>
        </article>
        <article class="review-card">
          <div class="review-stars" aria-label="5 out of 5 stars" data-i18n-aria="reviewStars">
            <i data-lucide="star" aria-hidden="true"></i><i data-lucide="star" aria-hidden="true"></i><i
              data-lucide="star" aria-hidden="true"></i><i data-lucide="star" aria-hidden="true"></i><i
              data-lucide="star" aria-hidden="true"></i>
          </div>
          <blockquote data-i18n="reviewTwo">The SSD upgrade made my computer much faster. The price and the work were
            explained before the repair started.</blockquote>
          <footer><strong>Salma B.</strong><span data-i18n="reviewTwoService">SSD upgrade</span></footer>
        </article>
        <article class="review-card">
          <div class="review-stars" aria-label="5 out of 5 stars" data-i18n-aria="reviewStars">
            <i data-lucide="star" aria-hidden="true"></i><i data-lucide="star" aria-hidden="true"></i><i
              data-lucide="star" aria-hidden="true"></i><i data-lucide="star" aria-hidden="true"></i><i
              data-lucide="star" aria-hidden="true"></i>
          </div>
          <blockquote data-i18n="reviewThree">My Wi-Fi and printer were set up quickly, and everything was tested before
            the job was finished.</blockquote>
          <footer><strong>Omar K.</strong><span data-i18n="reviewThreeService">Home setup</span></footer>
        </article>
      </div>
    </section>
  </main>

  <!-- WhatsApp button -->
  <a class="whatsapp-float"
    href="https://wa.me/212617248216?text=Hello%20Slahpc%2C%20I%20need%20help%20with%20my%20computer." target="_blank"
    rel="noopener" aria-label="Message Slahpc on WhatsApp" data-i18n-aria="whatsappAria">
    <i data-lucide="message-circle" aria-hidden="true"></i>
  </a>

  <!-- Bottom of the page -->
  <footer class="site-footer">
    <div class="footer-details">
      <p>&copy; <span data-year></span> <span data-i18n="footerText">Slahpc. Professional computer repair and technical
          support.</span></p>
      <p class="footer-location">
        <i data-lucide="map-pin" aria-hidden="true"></i>
        <span data-i18n="shopLocation">Shop: Tangier, Bendiban, Hawma Lwarda Street</span>
      </p>
    </div>
    <a href="#home" data-i18n="backTop">Back to top</a>
  </footer>
</body>

</html>
