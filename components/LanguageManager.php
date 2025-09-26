<?php

namespace app\components;

use Yii;
use yii\base\Component;

class LanguageManager extends Component
{
    public static function getAvailableLanguages()
    {
        return [
            'en' => [
                'name' => 'English',
                'flag' => '🇺🇸',
                'code' => 'en'
            ],
            'es' => [
                'name' => 'Español',
                'flag' => '🇪🇸',
                'code' => 'es'
            ],
            'fr' => [
                'name' => 'Français',
                'flag' => '🇫🇷',
                'code' => 'fr'
            ],
            'ar' => [
                'name' => 'العربية',
                'flag' => '🇸🇦',
                'code' => 'ar'
            ]
        ];
    }

    public static function setLanguage($language)
    {
        if (in_array($language, ['en', 'es', 'fr', 'ar'])) {
            Yii::$app->language = $language;
            Yii::$app->session->set('language', $language);

            // Update system setting
            Yii::$app->db->createCommand()
                ->update(
                    'system_settings',
                    ['setting_value' => $language],
                    ['setting_key' => 'default_language']
                )
                ->execute();

            return true;
        }
        return false;
    }

    public static function getCurrentLanguage()
    {
        $sessionLanguage = Yii::$app->session->get('language');
        if ($sessionLanguage) {
            return $sessionLanguage;
        }

        // Get from system settings
        $setting = Yii::$app->db->createCommand(
            'SELECT setting_value FROM system_settings WHERE setting_key = :key'
        )->bindValue(':key', 'default_language')->queryScalar();

        return $setting ?: 'en';
    }

    public static function isRTL()
    {
        $currentLang = self::getCurrentLanguage();
        return $currentLang === 'ar';
    }

    public static function getDirection()
    {
        return self::isRTL() ? 'rtl' : 'ltr';
    }

    public static function translate($text, $params = [])
    {
        $translations = self::getTranslations();
        $currentLang = self::getCurrentLanguage();

        if (isset($translations[$currentLang][$text])) {
            $translated = $translations[$currentLang][$text];
        } else {
            $translated = $text;
        }

        // Replace parameters
        foreach ($params as $key => $value) {
            $translated = str_replace("{{$key}}", $value, $translated);
        }

        return $translated;
    }

    private static function getTranslations()
    {
        return [
            'en' => [
                'System Settings Management' => 'System Settings Management',
                'General' => 'General',
                'Security' => 'Security',
                'Email' => 'Email',
                'Backup' => 'Backup',
                'UI' => 'UI',
                'Integration' => 'Integration',
                'Application Name' => 'Application Name',
                'System Version' => 'System Version',
                'Default Language' => 'Default Language',
                'Time Zone' => 'Time Zone',
                'Date Format' => 'Date Format',
                'System Status' => 'System Status',
                'Active' => 'Active',
                'Maintenance Mode' => 'Maintenance Mode',
                'Offline' => 'Offline',
                'Users' => 'Users',
                'Modules' => 'Modules',
                'Roles' => 'Roles',
                'Add New' => 'Add New',
                'Edit' => 'Edit',
                'Delete' => 'Delete',
                'Save' => 'Save',
                'Cancel' => 'Cancel',
                'Success' => 'Success',
                'Error' => 'Error',
                'Warning' => 'Warning',
                'Info' => 'Info',
                'Loading...' => 'Loading...',
                'No data available' => 'No data available',
                'Are you sure?' => 'Are you sure?',
                'Operation completed successfully' => 'Operation completed successfully',
                'An error occurred while processing your request' => 'An error occurred while processing your request',
            ],
            'es' => [
                'System Settings Management' => 'Gestión de Configuración del Sistema',
                'General' => 'General',
                'Security' => 'Seguridad',
                'Email' => 'Correo',
                'Backup' => 'Respaldo',
                'UI' => 'Interfaz',
                'Integration' => 'Integración',
                'Application Name' => 'Nombre de la Aplicación',
                'System Version' => 'Versión del Sistema',
                'Default Language' => 'Idioma Predeterminado',
                'Time Zone' => 'Zona Horaria',
                'Date Format' => 'Formato de Fecha',
                'System Status' => 'Estado del Sistema',
                'Active' => 'Activo',
                'Maintenance Mode' => 'Modo Mantenimiento',
                'Offline' => 'Desconectado',
                'Users' => 'Usuarios',
                'Modules' => 'Módulos',
                'Roles' => 'Roles',
                'Add New' => 'Agregar Nuevo',
                'Edit' => 'Editar',
                'Delete' => 'Eliminar',
                'Save' => 'Guardar',
                'Cancel' => 'Cancelar',
                'Success' => 'Éxito',
                'Error' => 'Error',
                'Warning' => 'Advertencia',
                'Info' => 'Información',
                'Loading...' => 'Cargando...',
                'No data available' => 'No hay datos disponibles',
                'Are you sure?' => '¿Estás seguro?',
                'Operation completed successfully' => 'Operación completada exitosamente',
                'An error occurred while processing your request' => 'Ocurrió un error al procesar tu solicitud',
            ],
            'fr' => [
                'System Settings Management' => 'Gestion des Paramètres Système',
                'General' => 'Général',
                'Security' => 'Sécurité',
                'Email' => 'Email',
                'Backup' => 'Sauvegarde',
                'UI' => 'Interface',
                'Integration' => 'Intégration',
                'Application Name' => 'Nom de l\'Application',
                'System Version' => 'Version du Système',
                'Default Language' => 'Langue par Défaut',
                'Time Zone' => 'Fuseau Horaire',
                'Date Format' => 'Format de Date',
                'System Status' => 'Statut du Système',
                'Active' => 'Actif',
                'Maintenance Mode' => 'Mode Maintenance',
                'Offline' => 'Hors Ligne',
                'Users' => 'Utilisateurs',
                'Modules' => 'Modules',
                'Roles' => 'Rôles',
                'Add New' => 'Ajouter Nouveau',
                'Edit' => 'Modifier',
                'Delete' => 'Supprimer',
                'Save' => 'Enregistrer',
                'Cancel' => 'Annuler',
                'Success' => 'Succès',
                'Error' => 'Erreur',
                'Warning' => 'Avertissement',
                'Info' => 'Information',
                'Loading...' => 'Chargement...',
                'No data available' => 'Aucune donnée disponible',
                'Are you sure?' => 'Êtes-vous sûr?',
                'Operation completed successfully' => 'Opération terminée avec succès',
                'An error occurred while processing your request' => 'Une erreur s\'est produite lors du traitement de votre demande',
            ],
            'ar' => [
                'System Settings Management' => 'إدارة إعدادات النظام',
                'General' => 'عام',
                'Security' => 'الأمان',
                'Email' => 'البريد الإلكتروني',
                'Backup' => 'النسخ الاحتياطي',
                'UI' => 'واجهة المستخدم',
                'Integration' => 'التكامل',
                'Application Name' => 'اسم التطبيق',
                'System Version' => 'إصدار النظام',
                'Default Language' => 'اللغة الافتراضية',
                'Time Zone' => 'المنطقة الزمنية',
                'Date Format' => 'تنسيق التاريخ',
                'System Status' => 'حالة النظام',
                'Active' => 'نشط',
                'Maintenance Mode' => 'وضع الصيانة',
                'Offline' => 'غير متصل',
                'Users' => 'المستخدمون',
                'Modules' => 'الوحدات',
                'Roles' => 'الأدوار',
                'Add New' => 'إضافة جديد',
                'Edit' => 'تعديل',
                'Delete' => 'حذف',
                'Save' => 'حفظ',
                'Cancel' => 'إلغاء',
                'Success' => 'نجح',
                'Error' => 'خطأ',
                'Warning' => 'تحذير',
                'Info' => 'معلومات',
                'Loading...' => 'جاري التحميل...',
                'No data available' => 'لا توجد بيانات متاحة',
                'Are you sure?' => 'هل أنت متأكد؟',
                'Operation completed successfully' => 'تم إكمال العملية بنجاح',
                'An error occurred while processing your request' => 'حدث خطأ أثناء معالجة طلبك',
                'System Settings' => 'إعدادات النظام',
                'Configure all system settings including application preferences, security, and integrations' => 'تكوين جميع إعدادات النظام بما في ذلك تفضيلات التطبيق والأمان والتكامل',
                'Settings are organized into tabs for easy navigation and management' => 'يتم تنظيم الإعدادات في علامات تبويب للتنقل والإدارة السهلة',
                'Changes are saved automatically and applied immediately to the system' => 'يتم حفظ التغييرات تلقائياً وتطبيقها فوراً على النظام',
                'Some settings may require system restart to take full effect' => 'قد تتطلب بعض الإعدادات إعادة تشغيل النظام لتفعيلها بالكامل',
                'All setting changes are logged for audit and security purposes' => 'يتم تسجيل جميع تغييرات الإعدادات لأغراض التدقيق والأمان',
                'Session Timeout (minutes)' => 'مهلة الجلسة (دقائق)',
                'Max Login Attempts' => 'الحد الأقصى لمحاولات تسجيل الدخول',
                'Password Policy' => 'سياسة كلمة المرور',
                'Two-Factor Authentication' => 'المصادقة الثنائية',
                'IP Whitelist' => 'القائمة البيضاء للعنوان IP',
                'Enable Audit Log' => 'تفعيل سجل التدقيق',
                'Low (6+ characters)' => 'منخفض (6+ أحرف)',
                'Medium (8+ chars, mixed)' => 'متوسط (8+ أحرف، مختلط)',
                'High (10+ chars, special chars)' => 'عالي (10+ أحرف، أحرف خاصة)',
                'Disabled' => 'معطل',
                'Optional' => 'اختياري',
                'Required' => 'مطلوب',
                'Enter IP addresses (one per line)' => 'أدخل عناوين IP (واحد في كل سطر)',
                'Enabled' => 'مفعل',
                'SMTP Host' => 'خادم SMTP',
                'SMTP Port' => 'منفذ SMTP',
                'SMTP Username' => 'اسم مستخدم SMTP',
                'SMTP Password' => 'كلمة مرور SMTP',
                'From Email' => 'من البريد الإلكتروني',
                'From Name' => 'من الاسم',
                'Auto Backup' => 'النسخ الاحتياطي التلقائي',
                'Backup Retention (days)' => 'الاحتفاظ بالنسخ الاحتياطي (أيام)',
                'Backup Location' => 'موقع النسخ الاحتياطي',
                'Daily' => 'يومي',
                'Weekly' => 'أسبوعي',
                'Monthly' => 'شهري',
                'Theme' => 'المظهر',
                'Primary Color' => 'اللون الأساسي',
                'Items Per Page' => 'العناصر في الصفحة',
                'Show Notifications' => 'إظهار الإشعارات',
                'Light' => 'فاتح',
                'Dark' => 'داكن',
                'Auto (System)' => 'تلقائي (النظام)',
                'API Rate Limit' => 'حد معدل API',
                'Enable API' => 'تفعيل API',
                'Webhook URL' => 'رابط Webhook',
                'System Users Management' => 'إدارة مستخدمي النظام',
                'This page lists all system users and their key details. Users are the main building blocks of your application' => 'تعرض هذه الصفحة جميع مستخدمي النظام وتفاصيلهم الرئيسية. المستخدمون هم اللبنات الأساسية لتطبيقك',
                'You can edit user details by clicking the Edit button. Changes are saved instantly and securely' => 'يمكنك تعديل تفاصيل المستخدم بالنقر على زر التعديل. يتم حفظ التغييرات فوراً وبأمان',
                'The Active/Inactive toggle lets you enable or disable a user account. Toggle the switch to update the user status' => 'يتيح لك مفتاح التشغيل/الإيقاف تفعيل أو تعطيل حساب المستخدم. حرك المفتاح لتحديث حالة المستخدم',
                'Use the search bar to quickly filter users by name, email, or username' => 'استخدم شريط البحث لتصفية المستخدمين بسرعة حسب الاسم أو البريد الإلكتروني أو اسم المستخدم',
                'All changes are logged for audit and security purposes' => 'يتم تسجيل جميع التغييرات لأغراض التدقيق والأمان',
                'Search user by name' => 'البحث عن المستخدم بالاسم',
                'All Roles' => 'جميع الأدوار',
                'Username' => 'اسم المستخدم',
                'Phone' => 'الهاتف',
                'Gender' => 'الجنس',
                'City' => 'المدينة',
                'Country' => 'البلد',
                'Created' => 'تم الإنشاء',
                'Last Login' => 'آخر تسجيل دخول',
                'Inactive' => 'غير نشط',
                'System Modules Management' => 'إدارة وحدات النظام',
                'This page lists all system modules and their key details. Modules are the building blocks of your application' => 'تعرض هذه الصفحة جميع وحدات النظام وتفاصيلها الرئيسية. الوحدات هي اللبنات الأساسية لتطبيقك',
                'You can edit module details by double-clicking on any field. Changes are saved instantly and securely' => 'يمكنك تعديل تفاصيل الوحدة بالنقر المزدوج على أي حقل. يتم حفظ التغييرات فوراً وبأمان',
                'The Active/Inactive toggle lets you enable or disable a module. Toggle the switch to update the module status' => 'يتيح لك مفتاح التشغيل/الإيقاف تفعيل أو تعطيل الوحدة. حرك المفتاح لتحديث حالة الوحدة',
                'Use the search bar to quickly filter modules by name or description' => 'استخدم شريط البحث لتصفية الوحدات بسرعة حسب الاسم أو الوصف',
                'Search modules by name, description...' => 'البحث عن الوحدات بالاسم أو الوصف...',
                'Name' => 'الاسم',
                'Link' => 'الرابط',
                'Description' => 'الوصف',
                'Sort Order' => 'ترتيب الفرز',
                'Status' => 'الحالة',
                'Parent Module' => 'الوحدة الأم',
                'Child Modules' => 'الوحدات الفرعية',
                'Add Module' => 'إضافة وحدة',
                'Type' => 'النوع',
                'Parent' => 'أم',
                'Child' => 'فرعي',
                'Select Parent Module' => 'اختر الوحدة الأم',
                'Update' => 'تحديث',
                'Create' => 'إنشاء',
                'Remove' => 'إزالة',
                'Close' => 'إغلاق',
                'Submit' => 'إرسال',
                'Reset' => 'إعادة تعيين',
                'Back' => 'رجوع',
                'Next' => 'التالي',
                'Previous' => 'السابق',
                'Finish' => 'إنهاء',
                'Continue' => 'متابعة',
                'Skip' => 'تخطي',
                'No results found' => 'لم يتم العثور على نتائج',
                'This action cannot be undone' => 'لا يمكن التراجع عن هذا الإجراء',
                'Please check your input and try again' => 'يرجى التحقق من إدخالك والمحاولة مرة أخرى',
                'Access denied' => 'تم رفض الوصول',
                'You do not have permission to perform this action' => 'ليس لديك إذن لتنفيذ هذا الإجراء',
                'First Name' => 'الاسم الأول',
                'Last Name' => 'اسم العائلة',
                'Password' => 'كلمة المرور',
                'Confirm Password' => 'تأكيد كلمة المرور',
                'Role' => 'الدور',
                'Select Role' => 'اختر الدور',
                'Date of Birth' => 'تاريخ الميلاد',
                'Alternate Phone' => 'هاتف بديل',
                'Address' => 'العنوان',
                'Profile Image' => 'صورة الملف الشخصي',
                'Male' => 'ذكر',
                'Female' => 'أنثى',
                'Other' => 'آخر',
                'Select Gender' => 'اختر الجنس',
                'System Roles Management' => 'إدارة أدوار النظام',
                'This page lists all system roles and their key details. Roles define user permissions and access levels' => 'تعرض هذه الصفحة جميع أدوار النظام وتفاصيلها الرئيسية. تحدد الأدوار صلاحيات المستخدم ومستويات الوصول',
                'You can edit role details by clicking the Edit button. Changes are saved instantly and securely' => 'يمكنك تعديل تفاصيل الدور بالنقر على زر التعديل. يتم حفظ التغييرات فوراً وبأمان',
                'The Active/Inactive toggle lets you enable or disable a role. Toggle the switch to update the role status' => 'يتيح لك مفتاح التشغيل/الإيقاف تفعيل أو تعطيل الدور. حرك المفتاح لتحديث حالة الدور',
                'Use the search bar to quickly filter roles by name or description' => 'استخدم شريط البحث لتصفية الأدوار بسرعة حسب الاسم أو الوصف',
                'Search roles by name, description...' => 'البحث عن الأدوار بالاسم أو الوصف...',
                'Module' => 'الوحدة',
                'Report' => 'التقرير',
                'Changing language...' => 'جاري تغيير اللغة...',
                'Language changed successfully!' => 'تم تغيير اللغة بنجاح!',
                'Failed to change language.' => 'فشل في تغيير اللغة.',
                'Setting updated successfully!' => 'تم تحديث الإعداد بنجاح!',
                'Failed to update setting.' => 'فشل في تحديث الإعداد.',
                'Setting key is required.' => 'مفتاح الإعداد مطلوب.',
                'Invalid language.' => 'لغة غير صالحة.',
                'No permission.' => 'لا توجد صلاحية.',
                'Invalid request.' => 'طلب غير صالح.',
                'Module updated!' => 'تم تحديث الوحدة!',
                'Failed to update module.' => 'فشل في تحديث الوحدة.',
                'Module status updated!' => 'تم تحديث حالة الوحدة!',
                'Failed to update module status.' => 'فشل في تحديث حالة الوحدة.',
                'Name is required.' => 'الاسم مطلوب.',
                'Failed to add module.' => 'فشل في إضافة الوحدة.',
                'No permission to add module.' => 'لا توجد صلاحية لإضافة وحدة.',
                'UTC' => 'توقيت عالمي منسق',
                'Eastern Time' => 'التوقيت الشرقي',
                'Central Time' => 'التوقيت المركزي',
                'Mountain Time' => 'التوقيت الجبلي',
                'Pacific Time' => 'التوقيت الباسيفيكي',
                'YYYY-MM-DD' => 'سنة-شهر-يوم',
                'MM/DD/YYYY' => 'شهر/يوم/سنة',
                'DD/MM/YYYY' => 'يوم/شهر/سنة',
                'English' => 'الإنجليزية',
                'Spanish' => 'الإسبانية',
                'French' => 'الفرنسية',
                'Arabic' => 'العربية'
            ]
        ];
    }
}
