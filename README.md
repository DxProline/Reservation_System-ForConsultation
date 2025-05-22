# Reservation_System-Consultation

Tento rezervační systém na konzultace vznikl jako můj maturitní projekt na Střední škole se zaměřením na IT. 
Cílem je usnadnit školám dohadování konzultací mezi učiteli a studenty pomocí přehledného webového rozhraní.

Systém je připraven k nasazení ve školním prostředí s podporou autentizace přes Microsoft účty a databází MariaDB/PostgreSQL. Díky rozdělení rolí mezi studenty, učitele a administrátory je možné systém snadno rozšířit i pro větší školní zařízení.

---

## 🚀 Funkce

- Přihlášení pomocí Microsoft účtu (OAuth 2.0)
- Rezervace a rušení konzultací (student)
- Vytváření a správa konzultací (učitel)
- Administrace uživatelů a impersonifikace (admin)
- Přehled rezervací a historie konzultací
- Emailové notifikace pomocí PHPMaileru

---


## 📸 Ukázky aplikace

| Přihlašovací obrazovka | Studenti - výběr konzultace |
|------------------------|------------------------------|
| ![](Navrh2_web.png) | ![](Web_Student_layout.png) |

| Učitel - tvorba konzultace | Admin - seznam studentů |
|-----------------------------|--------------------------|
| ![](Techer_Web_Layout.png) | ![](Admin_Web_Layout.png) |

---

## 📦 Instalace a spuštění (dev)



### 📀 Instalace a nasazení

##### 1. Požadavky

PHP 8.1 nebo novější

Apache / Nginx

MariaDB nebo PostgreSQL

Git

Composer (pro PHPMailer)

Microsoft Entra ID (pro OAuth 2.0)

2. **Klonuj repozitář:**
   ```bash
   git clone https://github.com/DxProline/Reservation_System-ForConsultation.git
   cd Reservation_System-ForConsultation

##### 3. Konfigurace databáze

Vytvoř databázi (např. consultation_system)

Importuj SQL strukturu a demo data, pokud je poskytne autor

V souboru Configs/secret.php uprav připojení k databázi:

`define("DB_HOST", "localhost");`
`define("DB_NAME", "consultation_system");`
`define("DB_USER", "root");`
`define("DB_PASS", "heslo");`

 ##### 4. Konfigurace Microsoft OAuth

Přihlas se do Microsoft Entra

Získej TenantID tvé domény se kterou Microsoft komunikuje.

Zaregistruj novou aplikaci pro CLIENTID


Nastav callback URL:

`http://tvujWEB/Reservation_System-ForConsultation/Actions/callback.php`

Nastav oprávnění:

`openid`

`User.Read`

`email`

Vlož klientský ID a tajný klíč do Configs/secret.php:

`define("CLIENT_ID", "xxxx");`
`define("CLIENT_SECRET", "xxxx");`
`define("REDIRECT_URI", "http://tvujWEB/Reservation_System-ForConsultation/Actions/callback.php");`


## 👣 Uživatelský manuál – Jak systém používat

### Přihlášení do systému

Otevři stránku systému ve webovém prohlížeči

Klikni na tlačítko „Přihlásit se pomocí Microsoft účtu“

Po úspěšném přihlášení budeš automaticky zařazen jako student (výchozí role)

### Funkce podle role

__👤 Student__

Po přihlášení klikni na „Volné konzultace“

Vyber konzultaci a klikni na „Rezervovat“

Vyplň předmět a popis problému (nepovinné)

Potvrď rezervaci → obdržíš e-mail

Konzultaci lze zrušit v sekci „Rezervované konzultace“

__👨‍🏫 Učitel__

Klikni na „Vytvořit konzultaci“

Vyplň datum, čas, délku, předmět a popis

Systém automaticky zabrání konfliktům v čase

Sleduj přihlášené studenty v sekci „Rezervované konzultace“

V případě nutnosti zruš konzultaci → student dostane e-mail

__👨‍💼 Administrátor__

Otevři „Seznam uživatelů“

Můžeš měnit role student/učitel/admin

Funkce „Impersonifikace“ ti umožní přihlásit se jako jiný uživatel

Sleduj historii konzultací nebo zakládej konzultace za ostatní



##### 5. PHPMailer

Otevři terminál v root adresáři projektu

Spusť:

`composer require phpmailer/phpmailer`

Uprav SMTP konfiguraci v Utils/mailer.php


🔧 Dokumentace kódu (vybrané akce)

`createConsultation.php`

Slouží k přidání nové konzultace učitelem

Validuje datum/čas, vkládá do tabulky consultations

`reserveConsultation.php`

Rezervace konzultace studentem

Odesílá notifikaci pomocí PHPMaileru studentovi i učiteli

`cancelConsultation.php`

Učitel nebo admin ruší konzultaci

Notifikace se odesílá studentům (pokud byli přihlášeni)

`cancelReservation.php`

Student ruší svou rezervaci

Učitel je o zrušení informován

`updateUserType.php`

Admin mění roli uživatele (0=student, 1=učitel, 2=admin)

`callback.php`

Ošetřuje navrácení uživatele z Microsoft OAuth loginu

Zaznamená uživatele do databáze, pokud je nový

**Verze**

__1.0__

První stabilní verze s plnou funkcionalitou

_2025_

**💼 Licence**

Projekt je tvořen DxProlinem (mnou) je tedy sice OpenSource ale při zavedení ve školních systémech si autor vyhrazuje právo na budoucí zpoplatnění podle jeho uvážení.

**🙏 Poděkování**

Velké díky patří panu učiteli na Programování na mé škole, @kubavojak  za podněty k zabezpečení a jeho přítelkyni za grafický vizuál.

