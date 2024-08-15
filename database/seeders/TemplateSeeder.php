<?php

namespace Database\Seeders;

use App\Models\Template;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TemplateSeeder extends Seeder {

  use WithoutModelEvents;

  /**
   * Run the database seeds.
   */
  public function run(): void {

    $defaultEmailVerificationEn = <<<EDO
<h1>
  Kaizen Exchange - Team support
</h1>

<h2>
  -- Email verification --
</h2>

<h3>
  User details:
</h3>

<ul>
  <li>User name: <-username-></li>
  <li>Email: <-email-></li>
</ul>

<h4>
  Your verification code is: <-code->
</h4>
EDO;

    $defaultEmailVerificationAr = <<<EDO
<h1>
  Kaizen Exchange - فريق الدعم
</h1>

<h2>
  -- التحقق من البريد الإلكتروني --
</h2>

<h3>
  معلومات المستخدم:
</h3>

<ul>
  <li>الاسم: <-username-></li>
  <li>البريد الإلكتروني: <-email-></li>
</ul>

<h4>
  رمز التحقق الخاص بك هو: <-code->
</h4>
EDO;

    $defaultEmailVerificationFr = <<<EDO
<h1>
  Kaizen Exchange - Support d'équipe
</h1>

<h2>
-- La vérification d'e-mail --
</h2>

<h3>
  Les détails de l'utilisateur:
</h3>

<ul>
  <li>Nom: <-username-></li>
  <li>E-mail: <-email-></li>
</ul>

<h4>
  Votre code de vérification est: <-code->
</h4>
EDO;

    $defaultEmailVerificationArgs = [
      "username",
      "email",
      "code",
      "url",
    ];

    $emailVerificationTemplates = [
      "register-email_verification",
      "password_forget-email_verification",
      "change_phone-email_verification",
      "change_email_old-email_verification",
      "change_email_new-email_verification",
      "change_password-email_verification",
    ];

    foreach ($emailVerificationTemplates as $name)
      Template::firstOrCreate([
        "name" => "$name-en",
        "content" => $defaultEmailVerificationEn,
        "args" => $defaultEmailVerificationArgs,
      ]);

    foreach ($emailVerificationTemplates as $name)
      Template::firstOrCreate([
        "name" => "$name-ar",
        "content" => $defaultEmailVerificationAr,
        "args" => $defaultEmailVerificationArgs,
      ]);

    foreach ($emailVerificationTemplates as $name)
      Template::firstOrCreate([
        "name" => "$name-fr",
        "content" => $defaultEmailVerificationFr,
        "args" => $defaultEmailVerificationArgs,
      ]);

    Template::firstOrCreate([
      'name' => 'privacy_policy-en',
      'content' => <<<EOF
# Kaizen Exchange App Privacy Policy

At Kaizen Exchange, we understand the importance of your privacy and are committed to protecting it. This Privacy Policy outlines how we collect, use, and disclose your personal information when you use our currency exchange platform app (Kaizen Exchange). By using the App, you consent to the collection and use of your information as described in this policy.

## Information We Collect

1- Personal Information: When you sign up for an account on the App, we may collect personal information such as your name, email address, phone number, and payment details.

2- Transaction Information: We collect information about your currency exchange transactions, including the amount, currency types, exchange rates, and transaction history.

3- Device Information: We automatically collect certain information about your device when you use the App, including your device type, operating system, IP address, and unique device identifiers.

4- Location Information: With your consent, we may collect information about your device's location to provide you with localized services, such as currency exchange rates in your area.

5- Usage Information: We may collect information about how you interact with the App, such as the features you use, the pages you visit, and your preferences.

## How We Use Your Information

1- To Provide Services: We use your personal information to facilitate currency exchange transactions, verify your identity, and provide customer support.

2- To Improve the App: We analyze usage data to understand how our users interact with the App and make improvements to enhance the user experience.

3- To Communicate with You: We may send you transactional emails, updates about the App, and marketing communications (if you opt-in) to keep you informed about our services.

4- To Ensure Security: We use your information to detect and prevent fraud, unauthorized access, and other illegal activities on our platform.

## Information Sharing and Disclosure

1- Third-Party Service Providers: We may share your information with trusted third-party service providers who assist us in operating the App, such as payment processors and cloud storage providers. These service providers are contractually obligated to only use your information for the purposes of providing services to us and are prohibited from using it for any other purpose.

2- Legal Compliance: We may disclose your information to comply with applicable laws, regulations, legal processes, or government requests.

3- Business Transfers: In the event of a merger, acquisition, or sale of all or a portion of our assets, your information may be transferred to the acquiring entity.

## Your Choices

1- Account Information: You can review and update your account information at any time by logging into your account settings on the App.

2- Communications Preferences: You can opt out of receiving marketing communications from us by following the instructions provided in the communication or by contacting us directly.

3- Dispute Resolution: If any transaction is refused, you have the right to open a dispute and contact us to find a better solution. We are committed to resolving any issues promptly and ensuring customer satisfaction.

## Data Security

We take reasonable measures to protect your information from unauthorized access, alteration, disclosure, or destruction. However, no method of transmission over the internet or electronic storage is 100% secure, and we cannot guarantee absolute security.

## Limitation of Liability

While we take every reasonable measure to ensure the security and reliability of our platform, we want to make it clear that we are not responsible for any operations conducted outside of the application and platform. Users are advised to exercise caution and diligence when engaging in transactions outside of our platform, as we cannot guarantee the same level of security and protection.

## Changes to this Privacy Policy

We reserve the right to update or modify this Privacy Policy at any time. We will notify you of any changes by posting the updated policy on the App. Your continued use of the App after the changes have been made indicates your acceptance of the updated policy.

## Contact Us

If you have any questions or concerns about this Privacy Policy or our privacy practices, please contact us at email [kaizen-exchange@gmail.com](mailto:kaizen-exchange@gmail.com), or phone [+213*********](tel:+213*********).

### Effective Date: 2024-04-23
EOF
    ]);

    Template::firstOrCreate([
      'name' => 'privacy_policy-ar',
      'content' => <<<EOF
# سياسة خصوصية تطبيق Kaizen Exchange

في Kaizen Exchange، نحن نتفهم أهمية خصوصيتك ونلتزم بحمايتها. توضح سياسة الخصوصية هذه كيفية جمع معلوماتك الشخصية واستخدامها والكشف عنها عند استخدام تطبيق منصة صرف العملات (Kaizen Exchange). باستخدام التطبيق، فإنك توافق على جمع واستخدام المعلومات الخاصة بك كما هو موضح في هذه السياسة.

## المعلومات التي نجمعها

1- المعلومات الشخصية: عند قيامك بالتسجيل للحصول على حساب على التطبيق، قد نقوم بجمع معلومات شخصية مثل اسمك وعنوان بريدك الإلكتروني ورقم هاتفك وتفاصيل الدفع.

2- معلومات المعاملات: نقوم بجمع معلومات حول معاملات صرف العملات الخاصة بك، بما في ذلك المبلغ وأنواع العملات وأسعار الصرف وتاريخ المعاملات.

3- معلومات الجهاز: نقوم تلقائيًا بجمع معلومات معينة عن جهازك عند استخدام التطبيق، بما في ذلك نوع جهازك ونظام التشغيل وعنوان IP ومعرفات الجهاز الفريدة.

4- معلومات الموقع: بموافقتك، قد نقوم بجمع معلومات حول موقع جهازك لتزويدك بخدمات محلية، مثل أسعار صرف العملات في منطقتك.

5- معلومات الاستخدام: قد نقوم بجمع معلومات حول كيفية تفاعلك مع التطبيق، مثل الميزات التي تستخدمها والصفحات التي تزورها وتفضيلاتك.

## كيف نستخدم معلوماتك

1- لتقديم الخدمات: نستخدم معلوماتك الشخصية لتسهيل معاملات صرف العملات والتحقق من هويتك وتقديم دعم العملاء.

2- لتحسين التطبيق: نقوم بتحليل بيانات الاستخدام لفهم كيفية تفاعل مستخدمينا مع التطبيق وإجراء تحسينات لتحسين تجربة المستخدم.

3- للتواصل معك: قد نرسل إليك رسائل بريد إلكتروني خاصة بالمعاملات، وتحديثات حول التطبيق، واتصالات تسويقية (إذا قمت بالاشتراك) لإبقائك على علم بخدماتنا.

4- لضمان الأمان: نستخدم معلوماتك لكشف ومنع الاحتيال والوصول غير المصرح به وغيرها من الأنشطة غير القانونية على منصتنا.

## مشاركة المعلومات والإفصاح عنها

1- مقدمو خدمات الطرف الثالث: قد نشارك معلوماتك مع مقدمي خدمات الطرف الثالث الموثوقين الذين يساعدوننا في تشغيل التطبيق، مثل معالجات الدفع ومقدمي خدمات التخزين السحابي. مقدمو الخدمات هؤلاء ملزمون تعاقديًا باستخدام معلوماتك فقط لأغراض تقديم الخدمات لنا ويحظر عليهم استخدامها لأي غرض آخر.

2-الامتثال القانوني: قد نكشف عن معلوماتك امتثالاً للقوانين أو اللوائح أو العمليات القانونية أو الطلبات الحكومية المعمول بها.

3- تحويلات الأعمال: في حالة الاندماج أو الاستحواذ أو البيع لكل أو جزء من أصولنا، فقد يتم نقل معلوماتك إلى الجهة المستحوذة.

## اختياراتك

1- معلومات الحساب: يمكنك مراجعة وتحديث معلومات حسابك في أي وقت عن طريق تسجيل الدخول إلى إعدادات حسابك على التطبيق.

2- تفضيلات الاتصالات: يمكنك إلغاء الاشتراك في تلقي الاتصالات التسويقية منا باتباع الإرشادات الواردة في المراسلة أو عن طريق الاتصال بنا مباشرة.

3- حل النزاعات: في حالة رفض أي معاملة، يحق لك فتح نزاع والاتصال بنا لإيجاد حل أفضل. نحن ملتزمون بحل أي مشكلات على الفور وضمان رضا العملاء.

## أمن البيانات

نحن نتخذ تدابير معقولة لحماية معلوماتك من الوصول غير المصرح به أو التغيير أو الكشف أو التدمير. ومع ذلك، لا توجد وسيلة نقل عبر الإنترنت أو التخزين الإلكتروني آمنة بنسبة 100%، ولا يمكننا ضمان الأمان المطلق.

## تحديد المسؤولية

على الرغم من أننا نتخذ كل الإجراءات المعقولة لضمان أمان وموثوقية منصتنا، إلا أننا نريد أن نوضح أننا لسنا مسؤولين عن أي عمليات تتم خارج التطبيق والمنصة. يُنصح المستخدمون بتوخي الحذر والاجتهاد عند المشاركة في معاملات خارج منصتنا، حيث لا يمكننا ضمان نفس المستوى من الأمان والحماية.

## التغييرات في سياسة الخصوصية هذه

نحن نحتفظ بالحق في تحديث أو تعديل سياسة الخصوصية هذه في أي وقت. وسوف نقوم بإعلامك بأي تغييرات عن طريق نشر السياسة المحدثة على التطبيق. إن استمرارك في استخدام التطبيق بعد إجراء التغييرات يشير إلى موافقتك على السياسة المحدثة.

## اتصل بنا

إذا كانت لديك أي أسئلة أو مخاوف بشأن سياسة الخصوصية هذه أو ممارسات الخصوصية لدينا، فيرجى الاتصال بنا على البريد الإلكتروني [kaizen-exchange@gmail.com](mailto:kaizen-exchange@gmail.com)، أو الهاتف [+213*** ******](الهاتف: +213*********).

### تاريخ النفاذ: 2024-04-23
EOF
    ]);

    Template::firstOrCreate([
      'name' => 'privacy_policy-fr',
      'content' => <<<EOF
# Politique de confidentialité de l'application Kaizen Exchange

Chez Kaizen Exchange, nous comprenons l'importance de votre vie privée et nous nous engageons à la protéger. Cette politique de confidentialité décrit la manière dont nous collectons, utilisons et divulguons vos informations personnelles lorsque vous utilisez notre application de plateforme de change (Kaizen-Exchange). En utilisant l'application, vous consentez à la collecte et à l'utilisation de vos informations comme décrit dans cette politique.

## Informations que nous collectons

1- Informations personnelles: lorsque vous créez un compte sur l'application, nous pouvons collecter des informations personnelles telles que votre nom, votre adresse e-mail, votre numéro de téléphone et vos informations de paiement.

2- Informations sur les transactions: nous collectons des informations sur vos transactions de change, notamment le montant, les types de devises, les taux de change et l'historique des transactions.

3- Informations sur l'appareil: nous collectons automatiquement certaines informations sur votre appareil lorsque vous utilisez l'application, notamment votre type d'appareil, votre système d'exploitation, votre adresse IP et les identifiants uniques de votre appareil.

4- Informations de localisation: avec votre consentement, nous pouvons collecter des informations sur la localisation de votre appareil pour vous fournir des services localisés, tels que les taux de change dans votre région.

5- Informations d'utilisation: Nous pouvons collecter des informations sur la façon dont vous interagissez avec l'application, telles que les fonctionnalités que vous utilisez, les pages que vous visitez et vos préférences.

## Comment nous utilisons vos informations

1- Pour fournir des services: nous utilisons vos informations personnelles pour faciliter les transactions de change, vérifier votre identité et fournir un support client.

2- Pour améliorer l'application: nous analysons les données d'utilisation pour comprendre comment nos utilisateurs interagissent avec l'application et apportons des améliorations pour améliorer l'expérience utilisateur.

3- Pour communiquer avec vous: nous pouvons vous envoyer des e-mails transactionnels, des mises à jour sur l'application et des communications marketing (si vous acceptez) pour vous tenir informé de nos services.

4- Pour assurer la sécurité : Nous utilisons vos informations pour détecter et prévenir la fraude, les accès non autorisés et autres activités illégales sur notre plateforme.

## Partage et divulgation d'informations

1- Fournisseurs de services tiers: nous pouvons partager vos informations avec des fournisseurs de services tiers de confiance qui nous aident à exploiter l'application, tels que des processeurs de paiement et des fournisseurs de stockage cloud. Ces prestataires de services sont contractuellement tenus d'utiliser vos informations uniquement dans le but de nous fournir des services et il leur est interdit de les utiliser à d'autres fins.

2- Conformité juridique: nous pouvons divulguer vos informations pour nous conformer aux lois, réglementations, procédures juridiques ou demandes gouvernementales applicables.

3- Transferts d'entreprises : En cas de fusion, d'acquisition ou de vente de tout ou partie de nos actifs, vos informations peuvent être transférées à l'entité acquéreuse.

## Vos choix

1- Informations sur le compte: vous pouvez consulter et mettre à jour les informations de votre compte à tout moment en vous connectant aux paramètres de votre compte sur l'application.

2- Préférences de communication: Vous pouvez refuser de recevoir des communications marketing de notre part en suivant les instructions fournies dans la communication ou en nous contactant directement.

3- Résolution des litiges: Si une transaction est refusée, vous avez le droit d'ouvrir un litige et de nous contacter pour trouver une meilleure solution. Nous nous engageons à résoudre tout problème rapidement et à garantir la satisfaction du client.

## Sécurité des données

Nous prenons des mesures raisonnables pour protéger vos informations contre tout accès, modification, divulgation ou destruction non autorisés. Cependant, aucune méthode de transmission sur Internet ou de stockage électronique n’est sécurisée à 100 % et nous ne pouvons garantir une sécurité absolue.

## Limitation de responsabilité

Bien que nous prenions toutes les mesures raisonnables pour garantir la sécurité et la fiabilité de notre plateforme, nous souhaitons préciser que nous ne sommes pas responsables des opérations menées en dehors de l'application et de la plateforme. Il est conseillé aux utilisateurs de faire preuve de prudence et de diligence lorsqu'ils effectuent des transactions en dehors de notre plateforme, car nous ne pouvons pas garantir le même niveau de sécurité et de protection.

## Modifications de cette politique de confidentialité

Nous nous réservons le droit de mettre à jour ou de modifier cette politique de confidentialité à tout moment. Nous vous informerons de tout changement en publiant la politique mise à jour sur l'application. Votre utilisation continue de l'application après les modifications indique votre acceptation de la politique mise à jour.

## Contactez-nous

Si vous avez des questions ou des préoccupations concernant cette politique de confidentialité ou nos pratiques de confidentialité, veuillez nous contacter par e-mail [kaizen-exchange@gmail.com](mailto:kaizen-exchange@gmail.com) ou par téléphone [+213*** ******](tél:+213*********).

### Date d'entrée en vigueur: 2024-04-23
EOF
    ]);

  }

}
