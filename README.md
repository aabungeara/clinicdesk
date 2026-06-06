# ClinicDesk 🩺

ClinicDesk is an enterprise-grade, login-protected **Clinic Management Dashboard** built from scratch using native Object-Oriented PHP and MySQL. The system uses a zero-public route architecture where every resource is guarded behind role-based authentication walls and strict server-side authorization.

---

## 🎓 Academic Context
* **Institution:** The Islamic University of Gaza | Faculty of Information Technology
* **Course:** Web 2 (Practical) — `SDEV 2106` / `WDMM 2010` / `MOBC 2102`
* **Semester:** Semester 2, 2025/2026
* **Instructor:** Eng. Mohammed Zuqlam
* **Project Reference:** ClinicDesk_FinalProject.pdf

---

## 🚀 System Features & Architecture

### 1. Multi-Role Dashboard Matrix
* **Admin:** Manage all system users, doctor profiles, and clinical specializations. Generates aggregate administrative reports.
* **Doctor:** Manage custom schedules, process appointments (Confirm/Complete/Cancel), and attach prescriptions with secure file uploads.
* **Patient:** Book real-time appointments with automated conflict checks, track medical history, and securely download personal prescriptions.

### 2. Core Engineering Patterns Applied[cite: 1]
* **Singleton DB Class:** Single database instance reused across all operations to optimize connection handling[cite: 1].
* **OOP Architecture:** Clean separation of concerns where database tables map directly to dedicated Model classes extending a shared `BaseModel`[cite: 1].
* **Security & Defense:** Universal use of `mysqli` prepared statements (no raw string injection), strict server-side CSRF validation tokens, and robust XSS output filtering[cite: 1].
* **Secure File Infrastructure:** Prescription PDFs utilize magic-byte validation (`finfo_file()`) and are kept in a protected uploads folder blocked from direct URL sniffing via `.htaccess` rules[cite: 1].

---

## 📁 System Architecture Blueprint

The project layout follows a strict Model-View-Controller (MVC) directory structure enforced by a centralized front controller routing system[cite: 1]:

```text
clinicdesk/
├── index.php                 # Front Controller: Routes via $_GET['page'] & $_GET['action'][cite: 1]
├── .htaccess                 # Rewrites and forces all requests through index.php[cite: 1]
├── config/                   # App configurations and database credentials[cite: 1]
├── core/                     # Engines: Database (Singleton), Auth, CSRF, and Paginator[cite: 1]
├── models/                   # Table-specific Object-Oriented Database Models[cite: 1]
├── controllers/              # App flow logic and transactional route handlers[cite: 1]
├── views/                    # Multi-role presentation layouts and isolated AdminLTE partials[cite: 1]
└── public/                   # Local assets (AdminLTE 3) and secure file upload repositories[cite: 1]