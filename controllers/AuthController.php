<?php

require_once __DIR__ . "/../core/Auth.php";
require_once __DIR__ . "/../core/CSRF.php";
require_once __DIR__ . "/../core/helpers.php";
require_once __DIR__ . "/../models/UserModel.php";

class AuthController
{
    public function login()
    {


        if (Auth::check()) {
            redirect("index.php?page=dashboard");
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            $this->handleLogin();
            return;
        }

        require_once __DIR__
            . "/../views/auth/login.php";
    }

    private function handleLogin(): void
    {

        if (
            !CSRF::validateToken(
                $_POST["csrf_token"] ?? ""
            )
        ) {
            $_SESSION["flash"] =
                "Invalid CSRF token";

            redirect(
                "index.php?page=auth&action=login"
            );
        }

        $email =
            trim($_POST["email"] ?? "");

        $password =
            $_POST["password"] ?? "";

        $model =
            new UserModel();

        $user =
            $model->findByEmail(
                $email
            );
        if (
            !$user ||
            !password_verify(
                $password,
                $user["password"]
            )
        ) {

            $_SESSION["flash"] =
                "Invalid email or password";

            redirect(
                "index.php?page=auth&action=login"
            );
        }

        if (
            (int)$user["is_active"] !== 1
        ) {

            $_SESSION["flash"] =
                "Account disabled";

            redirect(
                "index.php?page=auth&action=login"
            );
        }

        Auth::login($user);

        if (isset($user["first_login"]) && (int)$user["first_login"] === 1) {
            // نرفع علم في الجلسة لعزل المستخدم حتى يغير كلمة المرور
            $_SESSION['must_change_password'] = true;

            // نوجهه فوراً إلى صفحة التغيير وننهي السكريبت هنا لمنع التوجيه للـ Dashboard
            redirect("index.php?page=auth&action=changePassword");
            exit();
        }

        switch ($user["role"]) {

            case "admin":
                redirect(
                    "index.php?page=dashboard"
                );
                break;

            case "doctor":
                redirect(
                    "index.php?page=dashboard"
                );
                break;

            case "patient":
                redirect(
                    "index.php?page=dashboard"
                );
                break;

            default:
                Auth::logout();
        }
    }

    public function logout(): void
    {

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {

            redirect(
                "index.php?page=dashboard"
            );
        }

        if (
            !CSRF::validateToken(
                $_POST["csrf_token"] ?? ""
            )
        ) {

            $_SESSION["flash"] =
                "Invalid CSRF token";

            redirect(
                "index.php?page=dashboard"
            );
        }

        Auth::logout();
    }

    public function changePassword(): void
    {
        // التحقق من أن المستخدم مسجل دخول ومجبر على التغيير فعلياً
        if (!Auth::check() || !isset($_SESSION['must_change_password'])) {
            redirect("index.php?page=dashboard");
            exit();
        }

        $errors = [];
        require_once __DIR__ . "/../views/auth/change_password.php";
    }

    /**
     * 2. استقبال ومعالجة طلب التحديث والتحقق من الشروط القوية
     */
    /**
     * 2. استقبال ومعالجة طلب التحديث والتحقق من الشروط القوية عبر البريد الإلكتروني
     */
    public function updatePassword(): void
    {
        if (!Auth::check() || !isset($_SESSION['must_change_password'])) {
            redirect("index.php?page=dashboard");
            exit();
        }

        $errors          = [];
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword     = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // 🛡️ الحل الذكي: جلب البريد الإلكتروني للمستخدم الحالي من الجلسة لضمان الوصول للسجل الصحيح
        // تأكد من مطابقة اسم حقل الإيميل في الجلسة لديك (غالباً $_SESSION['user']['email'] أو $_SESSION['email'])
        $userEmail = $_SESSION['user']['email'] ?? $_SESSION['email'] ?? '';

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            
            if (!CSRF::validateToken($_POST["csrf_token"] ?? "")) {
                $errors[] = "Secure session expired. Please try again.";
            }

            $model = new UserModel();
            
            // جلب البيانات عن طريق الإيميل الموثوق في الجلسة بدلاً من الـ ID الملتبس
            $user = $model->findById($_SESSION['user']['id']);
            $userEmail= $user['email'] ?? 'N/A';
     
            // التحقق من وجود المستخدم وصحة كلمة المرور الحالية المكتوبة في الفورم
            if (!$user || password_verify($currentPassword, $user['password']) === false) {
                $errors[] = "The current password you entered is incorrect.";
            }

            // تحقق من قوة كلمة المرور الجديدة
            if (strlen($newPassword) < 8) {
                $errors[] = "New password must be at least 8 characters long.";
            }
            if (!preg_match('/[A-Z]/', $newPassword)) {
                $errors[] = "Password must contain at least one uppercase letter (A-Z).";
            }
            if (!preg_match('/[a-z]/', $newPassword)) {
                $errors[] = "Password must contain at least one lowercase letter (a-z).";
            }
            if (!preg_match('/[0-9]/', $newPassword)) {
                $errors[] = "Password must contain at least one digit (0-9).";
            }

            // تطابق كلمتي المرور
            if ($newPassword !== $confirmPassword) {
                $errors[] = "New password confirmation does not match.";
            }

            // إذا كانت البيانات سليمة تماماً، نقوم بالتحديث والعبور
            if (empty($errors)) {
                $newHash = password_hash($newPassword, PASSWORD_BCRYPT);
                
                // استخدام الـ ID الحقيقي المستخرج مباشرة من قاعدة البيانات ($user['id'])
                $success = $model->updateFirstLoginPassword($user['id'], $newHash);

                if ($success) {
                    // إزالة قيود الحماية من الجلسة بنجاح وعمل تحديث لبيانات الجلسة الحالية إن لزم الأمر
                    unset($_SESSION['must_change_password']);
                    
                    // تحيين حالة الـ first_login في الـ session النشط حتى لا يطالب بها مجدداً في نفس الجلسة
                    if (isset($_SESSION['user'])) {
                        $_SESSION['user']['first_login'] = 0;
                    }
                    
                    redirect("index.php?page=dashboard");
                    exit();
                } else {
                    $errors[] = "An unexpected database error occurred.";
                }
            }
        }

        // في حال وجود أخطاء، يتم إعادة عرض الصفحة مع مصفوفة الأخطاء
        require_once __DIR__ . "/../views/auth/change_password.php";
    }
}
