# Tutor ကို ရှင်းပြရန်အတွက် မြန်မာဘာသာဖြင့် ရှင်းလင်းချက်

## ၁။ Tutor ပြောထားသောအကြောင်းအရာ၏ အဓိပ္ပာယ်

Tutor ပြောထားတာက Website ကိုပဲ ဖွင့်ပြရုံနဲ့ မပြီးပါဘူး။ Website နောက်ကွယ်မှာ အလုပ်လုပ်နေတဲ့ **Login Code** ကိုလည်း ပြသပြီး ရှင်းပြရပါမယ်။ ထို့အပြင် **Admin Panel ထဲမှာ ပါဝင်တဲ့ Function များ** ကိုလည်း ပြရပါမယ်။

Tutor က “Product Category Management” ကို ဥပမာအနေနဲ့ ပြောထားတာပါ။ သင့် Website က Product ရောင်းဝယ်ရေး Website မဟုတ်ဘဲ Student Resource Exchange System ဖြစ်တဲ့အတွက် Product Category Management မရှိလည်း ရပါတယ်။ အဲဒီအစား သင့် Website မှာ တကယ်ရှိပြီး အလုပ်လုပ်နေတဲ့ Admin Functions များကို Functional Requirements အဖြစ် ရေးရပါမယ်။

အဓိကအားဖြင့် Tutor က အောက်ပါအချက်များကို သိချင်တာဖြစ်ပါတယ်။

> “ဒီ Website မှာ ဘယ် Function တွေပါသလဲ။ Function တစ်ခုချင်းစီကို ဘယ်လို Code နဲ့ရေးထားသလဲ။ User က အသုံးပြုတဲ့အခါ ဘယ်လိုအလုပ်လုပ်သလဲ။ အဲဒီ Function တွေက Functional Requirements ထဲမှာ ပါသလား။ Chapter 4 မှာ အဲဒီ Implementation ကို ရှင်းပြထားသလား။”

## ၂။ Functional Requirement ဆိုတာဘာလဲ

**Functional Requirement (FR)** ဆိုတာ System က မည်သည့်အလုပ်ကို လုပ်ပေးရမည်ဆိုတာ ဖော်ပြထားတဲ့ လိုအပ်ချက်ဖြစ်ပါတယ်။ ဥပမာအားဖြင့် “Student သည် Resource တင်နိုင်ရမည်” ဆိုတာ Functional Requirement တစ်ခု ဖြစ်ပါတယ်။

FR တစ်ခုစီဟာ Website ထဲမှာ လက်တွေ့အလုပ်လုပ်ရမယ်။ ထို့အပြင် Code ထဲမှာလည်း အဲဒီ Function ကို အကောင်အထည်ဖော်ထားရမယ်။ Report ထဲမှာလည်း Function ရဲ့ ရည်ရွယ်ချက်၊ လုပ်ဆောင်ပုံ၊ Code နှင့် စမ်းသပ်ထားပုံကို ရေးရပါမယ်။

| FR အဆင့် | လုပ်ဆောင်ရမည့်အရာ |
|---|---|
| ၁ | User သို့မဟုတ် Admin လုပ်နိုင်ရမည့်အလုပ်ကို သတ်မှတ်ခြင်း |
| ၂ | ထိုအလုပ်အတွက် PHP, HTML, CSS နှင့် Database Code ရေးခြင်း |
| ၃ | Website တွင် လက်တွေ့စမ်းသပ်ခြင်း |
| ၄ | Chapter 4 တွင် Implementation အဖြစ် ရှင်းပြခြင်း |
| ၅ | Screenshot သို့မဟုတ် Code ဖြင့် သက်သေပြခြင်း |

## ၃။ သင့် Login Page ၏ မဖြစ်မနေပါဝင်ရမည့်အချက်များ

သင့် System တွင် Student နှင့် Administrator နှစ်မျိုးလုံးအတွက် Login Page တစ်ခုတည်းကို အသုံးပြုထားပါတယ်။ Login Page တွင် အောက်ပါအချက်များ ပါဝင်ပြီး အလုပ်လုပ်ရပါမယ်။

| FR နံပါတ် | Functional Requirement | သင့် System တွင် အကောင်အထည်ဖော်ထားပုံ |
|---|---|---|
| FR-LOGIN-01 | User သည် Email ထည့်နိုင်ရမည်။ | `login.php` တွင် Email Input ပါရှိသည်။ |
| FR-LOGIN-02 | User သည် Password ထည့်နိုင်ရမည်။ | Password Input တွင် ရိုက်ထားသောစာသားကို ဖုံးကွယ်ထားသည်။ |
| FR-LOGIN-03 | User သည် Login Form ကို Submit လုပ်နိုင်ရမည်။ | `POST` Method နှင့် Login Button အသုံးပြုထားသည်။ |
| FR-LOGIN-04 | System သည် Email နှင့် Password မှန်၊ မမှန် စစ်ဆေးရမည်။ | Database ထဲရှိ Account နှင့် တိုက်စစ်သည်။ |
| FR-LOGIN-05 | System သည် Student နှင့် Admin Account နှစ်မျိုးလုံးကို လက်ခံရမည်။ | `users` Table ထဲရှိ `role` Field ကို အသုံးပြုသည်။ |
| FR-LOGIN-06 | Login အောင်မြင်လျှင် Role အလိုက် Page ပြောင်းပေးရမည်။ | Admin ကို `admin.php` သို့၊ Student ကို `student.php` သို့ ပို့သည်။ |
| FR-LOGIN-07 | Login မှားလျှင် Error Message ပြရမည်။ | Email သို့မဟုတ် Password မှားကြောင်း ပြသည်။ |
| FR-LOGIN-08 | ထပ်ခါတလဲလဲ Login မှားခြင်းကို ကာကွယ်ရမည်။ | ငါးကြိမ်မှားလျှင် ငါးမိနစ် ခဏပိတ်ထားသည်။ |
| FR-LOGIN-09 | Login Form ကို CSRF မှ ကာကွယ်ရမည်။ | CSRF Token နှင့် `verifyCsrf()` အသုံးပြုထားသည်။ |
| FR-LOGIN-10 | Password ကို လုံခြုံစွာ သိမ်းဆည်းရမည်။ | Password Hash နှင့် `password_verify()` အသုံးပြုထားသည်။ |
| FR-LOGIN-11 | Database Query ကို လုံခြုံစွာ လုပ်ဆောင်ရမည်။ | MySQLi Prepared Statement အသုံးပြုထားသည်။ |
| FR-LOGIN-12 | Login ဝင်ပြီးနောက် Session ဖန်တီးရမည်။ | User Information ကို `$_SESSION["user"]` တွင် သိမ်းသည်။ |
| FR-LOGIN-13 | Login ဝင်ပြီးသား User ကို Login Page ပြန်မပြရ။ | `currentUser()` ဖြင့် သက်ဆိုင်ရာ Page သို့ ပြန်ပို့သည်။ |
| FR-LOGIN-14 | User သည် Logout လုပ်နိုင်ရမည်။ | `logout.php` က Session ကို ဖျက်ပေးသည်။ |

## ၄။ Login Code ဘယ်လိုအလုပ်လုပ်သလဲ

### ၄.၁ `config.php` ကို ခေါ်ယူခြင်း

`login.php` ၏ အစတွင် အောက်ပါ Code ပါရှိပါတယ်။

```php
require "config.php";
```

ဒီ Code က Shared Configuration File ဖြစ်တဲ့ `config.php` ကို ခေါ်ယူပါတယ်။ `config.php` ထဲမှာ Database Connection, Session, CSRF Protection, Password Security နှင့် Login Helper Function များ ပါဝင်ပါတယ်။

### ၄.၂ Session စတင်ခြင်း

`config.php` က `session_start()` ကို အသုံးပြုပြီး User Session ကို စတင်ပါတယ်။ Session ထဲမှာ Login ဝင်ထားသော User ရဲ့ လိုအပ်သောအချက်အလက်များကို သိမ်းထားပါတယ်။

Session Cookie ကိုလည်း `HttpOnly`, `SameSite=Lax` နှင့် HTTPS သုံးတဲ့အချိန်မှာ `Secure` Setting များဖြင့် ကာကွယ်ထားပါတယ်။

### ၄.၃ Login ဝင်ထားပြီးသား User ကို စစ်ဆေးခြင်း

```php
if (currentUser()) {
    header("Location: " . (currentUser()["role"] === "admin" ? "admin.php" : "student.php"));
    exit;
}
```

ဒီ Code က User Login ဝင်ထားပြီးသားလားဆိုတာ စစ်ပါတယ်။ Admin ဖြစ်ရင် `admin.php` သို့ ပို့ပါတယ်။ Student ဖြစ်ရင် `student.php` သို့ ပို့ပါတယ်။ ဒါကြောင့် Login ဝင်ပြီးသား User က Login Form ကို ထပ်မြင်ရမှာ မဟုတ်ပါဘူး။

### ၄.၄ Form Submit လုပ်ထားခြင်းကို စစ်ဆေးခြင်း

```php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
```

Login Form ကို Submit လုပ်တဲ့အခါ `POST` Request ဖြစ်ပါတယ်။ ဒီ Code က Form ကို Submit လုပ်ထားမှသာ Login Process ကို ဆက်လုပ်ပါတယ်။

### ၄.၅ CSRF Token စစ်ဆေးခြင်း

```php
verifyCsrf();
```

Login Form ထဲမှာ Hidden CSRF Token ပါပါတယ်။

```php
<input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
```

`csrfToken()` က Random Token တစ်ခု ဖန်တီးပြီး Session ထဲမှာ သိမ်းပါတယ်။ Form Submit လုပ်တဲ့အချိန်မှာ ပြန်ပါလာတဲ့ Token နဲ့ Session ထဲက Token ကို `verifyCsrf()` က တိုက်စစ်ပါတယ်။ Token မတူရင် Request ကို ရပ်တန့်ပေးပါတယ်။ ဒါက မသမာသော Website တစ်ခုကနေ Login Request ပို့ခြင်းကို ကာကွယ်ပေးပါတယ်။

### ၄.၆ Failed Login Lock စစ်ဆေးခြင်း

```php
if (loginBlocked()) {
    $message = "Too many failed attempts. Please wait five minutes and try again.";
}
```

Password မှားပြီး Login ကြိုးစားမှုများပြားလာရင် System က ခဏတာ Lock လုပ်ပါတယ်။

```php
recordLoginFailure();
```

ဒီ Function က Failed Login အကြိမ်အရေအတွက်ကို Session ထဲမှာ မှတ်ထားပါတယ်။ ငါးကြိမ်ပြည့်သွားရင် ငါးမိနစ်အတွက် Login ကို ပိတ်ထားပါတယ်။ ဒီနည်းလမ်းက Password ခန့်မှန်းပြီး ထပ်ခါတလဲလဲ ဝင်ကြည့်ခြင်းကို လျှော့ချပေးပါတယ်။

### ၄.၇ User ထည့်ထားသော Email နှင့် Password ကို ရယူခြင်း

```php
$email = strtolower(trim($_POST["email"] ?? ""));
$password = $_POST["password"] ?? "";
```

`trim()` က Email ရှေ့နောက်မှာ မတော်တဆပါလာတဲ့ Space များကို ဖယ်ရှားပါတယ်။ `strtolower()` က Email ကို စာလုံးအသေးအဖြစ် ပြောင်းပေးပါတယ်။ Password ကိုတော့ မပြောင်းပါဘူး၊ ဘာဖြစ်လို့လဲဆိုတော့ Password ထဲမှာ Space ပါဝင်နိုင်လို့ ဖြစ်ပါတယ်။

### ၄.၈ Database ထဲမှ Account ကို ရှာဖွေခြင်း

```php
$statement = $conn->prepare(
    "SELECT id, display_name, email, password_hash, role,
     academic_level, profile_visibility, avatar_path
     FROM users WHERE email = ? LIMIT 1"
);
$statement->bind_param("s", $email);
$statement->execute();
$user = $statement->get_result()->fetch_assoc();
$statement->close();
```

ဒီ Code က `users` Table ထဲမှာ User ထည့်ထားတဲ့ Email ရှိ၊ မရှိ ရှာပါတယ်။ `?` သည် Parameter Placeholder ဖြစ်ပြီး User ထည့်လိုက်တဲ့ Email ကို SQL Code ထဲသို့ တိုက်ရိုက်ပေါင်းထည့်တာ မဟုတ်ပါဘူး။

`bind_param("s", $email)` က Email ကို String အဖြစ် သတ်မှတ်ပြီး Query လုပ်ပါတယ်။ ဒီနည်းလမ်းက SQL Injection ဖြစ်နိုင်ခြေကို လျှော့ချပေးပါတယ်။

### ၄.၉ Password စစ်ဆေးခြင်း

```php
if ($user && password_verify($password, $user["password_hash"])) {
```

Registration လုပ်တဲ့အချိန်မှာ Password ကို Plain Text အဖြစ် မသိမ်းပါဘူး။ `password_hash()` အသုံးပြုပြီး Hash အဖြစ် ပြောင်းသိမ်းပါတယ်။

Login ဝင်တဲ့အချိန်မှာ User ထည့်လိုက်တဲ့ Password ကို Database ထဲက Password Hash နဲ့ `password_verify()` ဖြင့် တိုက်စစ်ပါတယ်။ Hash ကို ပြန်ဖော်ပြီး Password ရှာတာ မဟုတ်ပါဘူး။ ထည့်ထားတဲ့ Password က Hash နဲ့ ကိုက်ညီ၊ မကိုက်ညီကိုသာ စစ်တာဖြစ်ပါတယ်။

### ၄.၁၀ Login အောင်မြင်လျှင် Session ပြင်ဆင်ခြင်း

```php
clearLoginFailures();
session_regenerate_id(true);
unset($user["password_hash"]);
$_SESSION["user"] = $user;
```

Login အောင်မြင်တဲ့အခါ Failed Login မှတ်တမ်းကို ဖျက်ပါတယ်။ ထို့နောက် `session_regenerate_id(true)` ဖြင့် Session ID အသစ် ပြန်ဖန်တီးပါတယ်။ ဒီနည်းလမ်းက Session Fixation ဖြစ်နိုင်ခြေကို လျှော့ချပေးပါတယ်။

Password Hash ကို Session ထဲ မလိုအပ်ဘဲ မသိမ်းထားရန် အောက်ပါ Code ဖြင့် ဖယ်ရှားပါတယ်။

```php
unset($user["password_hash"]);
```

ထို့နောက် လိုအပ်သော User Information များကို `$_SESSION["user"]` ထဲမှာ သိမ်းပါတယ်။

### ၄.၁၁ Role အလိုက် Redirect လုပ်ခြင်း

```php
header("Location: " . ($user["role"] === "admin" ? "admin.php" : "student.php"));
exit;
```

Database ထဲက `role` သည် `admin` ဖြစ်ရင် `admin.php` သို့ ပို့ပါတယ်။ Admin မဟုတ်ရင် `student.php` သို့ ပို့ပါတယ်။

ဒါကြောင့် Website မှာ Admin Login Page တစ်ခုနှင့် Student Login Page တစ်ခု သီးခြားမလိုပါဘူး။ Login Page တစ်ခုတည်းက Account Role အလိုက် သက်ဆိုင်ရာ Page သို့ ပို့ပေးပါတယ်။

## ၅။ Login ပြီးနောက် Page များကို ကာကွယ်ခြင်း

Login ဝင်ပြီးသားလားဆိုတာ စစ်ရန် `requireLogin()` ကို အသုံးပြုပါတယ်။

```php
function requireLogin(): array
{
    $user = currentUser();
    if (!$user) {
        header("Location: login.php");
        exit;
    }
    return $user;
}
```

Login မဝင်ထားသေးတဲ့ User က Student Page သို့မဟုတ် Profile Page ကို ဝင်ကြည့်ရင် `login.php` သို့ ပြန်ပို့ပါတယ်။

Admin Page အတွက်တော့ `requireAdmin()` ကို ထပ်မံအသုံးပြုပါတယ်။

```php
function requireAdmin(): array
{
    $user = requireLogin();
    if ($user["role"] !== "admin") {
        http_response_code(403);
        exit("Access denied. Administrator permission is required.");
    }
    return $user;
}
```

ဒီ Code က ပထမဆုံး Login ဝင်ထားခြင်း ရှိ၊ မရှိ စစ်ပါတယ်။ ထို့နောက် User ရဲ့ Role သည် `admin` ဟုတ်၊ မဟုတ် ထပ်စစ်ပါတယ်။ Student တစ်ယောက်က `admin.php` ကို URL ဖြင့် တိုက်ရိုက်ဝင်ကြည့်ရင် Access Denied ပြပြီး ဝင်ခွင့်မပြုပါဘူး။

## ၆။ သင့် Admin Panel ထဲက မဖြစ်မနေပြရမည့် Function များ

သင့် Website မှာ Product Category Management မရှိတဲ့အတွက် Product Category ကို မပြရပါဘူး။ အောက်ပါ Resource Exchange ဆိုင်ရာ Admin Function များကို ပြရပါမယ်။

| FR နံပါတ် | Admin Functional Requirement | လုပ်ဆောင်ချက် |
|---|---|---|
| FR-ADM-01 | Admin သည် Login ဝင်နိုင်ရမည်။ | Admin Account ဖြင့် Login ဝင်ပြီး `admin.php` သို့ ရောက်ရမည်။ |
| FR-ADM-02 | Admin သည် Pending Resources များကို ကြည့်နိုင်ရမည်။ | Student တင်ထားပြီး စစ်ဆေးရန်ကျန်သေးသော Resource များကို မြင်ရမည်။ |
| FR-ADM-03 | Admin သည် Resource ကို Approve လုပ်နိုင်ရမည်။ | Approve လုပ်ပြီးမှ Resource သည် Public Catalogue တွင် ပေါ်ရမည်။ |
| FR-ADM-04 | Admin သည် Resource ကို Reject လုပ်နိုင်ရမည်။ | Reject လုပ်ရာတွင် အကြောင်းပြချက် ထည့်နိုင်ရမည်။ |
| FR-ADM-05 | Admin သည် Report များကို ကြည့်နိုင်ရမည်။ | Student များ Report လုပ်ထားသော Resource များကို စစ်ဆေးနိုင်ရမည်။ |
| FR-ADM-06 | Admin သည် Keep သို့မဟုတ် Delete ဆုံးဖြတ်နိုင်ရမည်။ | Report ပြီးသော Resource ကို ဆက်ထားမည် သို့မဟုတ် ဖျက်မည် ဆုံးဖြတ်နိုင်ရမည်။ |
| FR-ADM-07 | Admin သည် Student ထံ Feedback ပေးနိုင်ရမည်။ | Admin ၏ ဆုံးဖြတ်ချက်ကို Student ထံ Notification ဖြင့် ပြန်ပို့ရမည်။ |
| FR-ADM-08 | Admin သည် User Accounts များကို စီမံနိုင်ရမည်။ | Member Information နှင့် Role များကို စီမံနိုင်ရမည်။ |
| FR-ADM-09 | Admin Function များကို မသက်ဆိုင်သူများမှ ဝင်မရရမည်။ | `requireAdmin()` ဖြင့် Role စစ်ဆေးရမည်။ |
| FR-ADM-10 | Admin သည် Resource Status များကို စီမံနိုင်ရမည်။ | Pending, Approved, Rejected နှင့် Deleted Status များကို စီမံနိုင်ရမည်။ |

## ၇။ Tutor ကို Website ဖြင့် ပြသရမည့် အစီအစဉ်

ပထမဆုံး Login Page ကို ဖွင့်ပြပါ။ Email Field, Password Field, **Log in** Button နှင့် **Create an account** Link ကို ပြပါ။

ထို့နောက် Student Account ဖြင့် Login ဝင်ပြီး Student Page သို့ ရောက်ကြောင်း ပြပါ။ Admin Account ဖြင့် Login ဝင်ပြီး Admin Page သို့ ရောက်ကြောင်း ပြပါ။

Admin Page ရောက်ပြီးနောက် Pending Resource တစ်ခုကို ဖွင့်ပြပါ။ Admin က Resource ကို Approve လုပ်နိုင်ကြောင်း ပြပါ။ Approve လုပ်ပြီးနောက် Student Catalogue တွင် Resource ပေါ်လာကြောင်း ပြပါ။

ထို့နောက် Resource တစ်ခုကို Reject လုပ်ပြီး Rejection Reason ထည့်နိုင်ကြောင်း ပြပါ။ Student Page သို့ ပြန်ဝင်ပြီး Student သည် အဲဒီ Feedback ကို မြင်နိုင်ကြောင်း ပြပါ။

Report Function ကိုလည်း ပြပါ။ Student တစ်ယောက်က Resource ကို Report လုပ်ပြီးနောက် Admin Page မှာ Report ကို ကြည့်ပါ။ Admin က **Keep** သို့မဟုတ် **Delete** ဆုံးဖြတ်ပြီး Feedback ရေးနိုင်ကြောင်း ပြပါ။ ထို့နောက် Reporting Student ထံ Notification ပြန်ရောက်ကြောင်း ပြပါ။

## ၈။ Tutor ကို Code ဖြင့် ပြသရမည့်အရာများ

| ဖိုင် | Tutor ကို ရှင်းပြရမည့်အကြောင်းအရာ |
|---|---|
| `login.php` | Login Form, POST Request, CSRF စစ်ဆေးခြင်း, Database Query, Password Verification နှင့် Role Redirect ။ |
| `config.php` | Database Connection, Session, CSRF Helper, Failed Login Lock, `currentUser()`, `requireLogin()` နှင့် `requireAdmin()` ။ |
| `register.php` | Account ဖန်တီးခြင်းနှင့် Password Hash သိမ်းခြင်း။ |
| `student.php` | Student Login ပြီးနောက် Student Function များကို ပြသခြင်း။ |
| `admin.php` | Admin Role ဖြင့်သာ ဝင်ရောက်နိုင်သော Moderation Function များ။ |
| `logout.php` | Session ဖျက်ပြီး Account မှ ထွက်ခြင်း။ |

## ၉။ Chapter 4 တွင် ရေးနိုင်သော မြန်မာဘာသာစာပိုဒ်

> ဤစနစ်တွင် Student နှင့် Administrator နှစ်မျိုးလုံးအတွက် Login Page တစ်ခုတည်းကို အသုံးပြုထားသည်။ User သည် Email နှင့် Password ထည့်ပြီး Login ပြုလုပ်သောအခါ System သည် CSRF Token ကို အရင်စစ်ဆေးပြီး မအောင်မြင်သော Login ကြိုးစားမှုများကြောင့် Lock ဖြစ်နေခြင်း ရှိ၊ မရှိ စစ်ဆေးသည်။ ထို့နောက် Email ကို MySQLi Prepared Statement ဖြင့် Database ထဲတွင် ရှာဖွေပြီး ထည့်သွင်းထားသော Password ကို Database ထဲရှိ Password Hash နှင့် `password_verify()` ဖြင့် တိုက်စစ်သည်။ Login အောင်မြင်လျှင် Session ID အသစ်ကို ပြန်လည်ဖန်တီးပြီး Password Hash ကို Session Data ထဲမှ ဖယ်ရှားကာ လိုအပ်သော User Information များကို Session ထဲတွင် သိမ်းဆည်းသည်။ User ၏ Role အလိုက် Administrator ကို `admin.php` သို့မဟုတ် Student ကို `student.php` သို့ Redirect လုပ်သည်။ ထို့အပြင် `requireLogin()` နှင့် `requireAdmin()` တို့ဖြင့် Protected Page များကို Server Side တွင် စစ်ဆေးထားသောကြောင့် Student သည် Administrator URL ကို တိုက်ရိုက်ရိုက်ထည့်၍ ဝင်ရောက်နိုင်ခြင်း မရှိပါ။

> Administrator Panel သည် Student Resource Exchange System ၏ အဓိက Functional Requirements များကို အကောင်အထည်ဖော်ပေးသည်။ Administrator သည် Pending Resource များကို ကြည့်ရှုနိုင်ခြင်း၊ Resource များကို Approve သို့မဟုတ် Reject လုပ်နိုင်ခြင်း၊ Student များ၏ Report များကို စစ်ဆေးနိုင်ခြင်း၊ Report ပြီးသော Resource များအတွက် Keep သို့မဟုတ် Delete ဆုံးဖြတ်နိုင်ခြင်းနှင့် Reporting Student ထံ Feedback ပေးနိုင်ခြင်းတို့ကို လုပ်ဆောင်နိုင်သည်။ Administrator Action တစ်ခုချင်းစီကို `requireAdmin()` ဖြင့် Server Side Role Authorisation စစ်ဆေးထားသည်။ ဤစနစ်သည် Product Category Management ကို အသုံးမပြုဘဲ Academic Resource Moderation နှင့် Subject-Based Catalogue Organisation ကို အဓိကစီမံထားသည်။

## ၁၀။ Tutor ပြောသောအခါ အတိုချုံးပြန်ဖြေရန်

> “ကျွန်မ Website မှာ Product Category Management မရှိပါဘူး၊ ဘာဖြစ်လို့လဲဆိုတော့ ဒီ Project က Product Website မဟုတ်ဘဲ Student Resource Exchange System ဖြစ်လို့ပါ။ ဒါကြောင့် ကျွန်မ System နဲ့ သက်ဆိုင်တဲ့ Functional Requirements တွေကို Resource Upload, Resource Moderation, Approval, Rejection Feedback, Report Management, Keep/Delete Decision, Notification, User Management နဲ့ Role-Based Access Control အဖြစ် သတ်မှတ်ထားပါတယ်။ ဒီ Function တစ်ခုချင်းစီကို Admin Panel နဲ့ PHP Code ထဲမှာ အကောင်အထည်ဖော်ထားပြီး Chapter 4 မှာလည်း Implementation အဖြစ် ရှင်းပြထားပါတယ်။ Login အတွက်တော့ Student နဲ့ Admin နှစ်မျိုးလုံးက Login Page တစ်ခုတည်းကို အသုံးပြုကြပြီး Database ထဲက Role အလိုက် သက်ဆိုင်ရာ Page သို့ Redirect လုပ်ပါတယ်။”

## ၁၁။ ပြသရန် Screenshot များ

Tutor ကို သက်သေပြရန် အောက်ပါ Screenshot များကို ပြင်ဆင်ထားပါ။

| Screenshot | ပြသရမည့်အကြောင်းအရာ |
|---|---|
| Screenshot 1 | ပုံမှန် Login Page ။ |
| Screenshot 2 | Email သို့မဟုတ် Password မှားသည့်အခါ Error Message ။ |
| Screenshot 3 | Student Login အောင်မြင်ပြီး Student Page ရောက်ခြင်း။ |
| Screenshot 4 | Admin Login အောင်မြင်ပြီး Admin Page ရောက်ခြင်း။ |
| Screenshot 5 | Admin Panel မှ Pending Resource ကို Approve လုပ်ခြင်း။ |
| Screenshot 6 | Resource ကို Reject လုပ်ပြီး Reason ပေးခြင်း။ |
| Screenshot 7 | Student ထံသို့ Rejection Feedback ပြန်ရောက်ခြင်း။ |
| Screenshot 8 | Student က Resource Report တင်ခြင်း။ |
| Screenshot 9 | Admin က Report ကို Keep သို့မဟုတ် Delete ဆုံးဖြတ်ခြင်း။ |
| Screenshot 10 | Reporting Student ထံ Admin Feedback Notification ပြန်ရောက်ခြင်း။ |

