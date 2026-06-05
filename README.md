# 🩸 LifeLine: Advanced Blood Donation Management System (BDMS)

LifeLine is a premium, state-of-the-art web application engineered to bridge the critical gap between voluntary blood donors and patients or hospitals in urgent need. Built using dynamic PHP backend scripts, custom high-performance vanilla CSS, and a relational MySQL database, LifeLine offers a stunning, responsive user experience complete with advanced statistics, a multi-step medical clearance system, and on-demand certificate generation.

---

## ✨ Features & Capabilities

### 1. 🌐 Cinematic Landing Page (`index.php`)
* **Premium Aesthetics:** Curated dark and light mode color palettes (sleek red accents, glassmorphic menus, and glowing animated ambient background elements).
* **Responsive Visual Collage:** A dynamic interactive collage displaying donors and recipients with 3D perspective transforms upon hover.
* **Camps Directory:** Ticket-styled presentation detailing upcoming community blood donation campaigns and drives with clean, structured grids.

### 2. 📊 Live Statistics Dashboard (`home.php`)
* **Real-Time Data Streams:** Direct connection to the database to compute live KPIs:
  * **Total Active Donors** registered in the system.
  * **Total Blood Units Available** (aggregated in milliliters/units).
  * **Completed Requests** successfully fulfilled.
  * **Connected Medical Centers & Hospitals**.
* **Visual Stock Grid:** Color-coded layout showing units available for each blood group (`A+`, `A-`, `B+`, `B-`, `O+`, `O-`, `AB+`, `AB-`) in real-time.
* **Quick Actions Panel:** One-click shortcuts to register, search, or request emergency assistance.

### 3. 🩺 Intelligent Donor Registration Wizard (`donor.php`)
* **Multi-Step Account Setup:** Clean step-by-step progress tracking wizard dividing account credentials, medical statistics, and certification.
* **Smart Age Validator:** Automatically parses Date of Birth (DOB) and calculates exact age, restricting registration to the medically approved 18–65 age range.
* **Clearance Engine:** Evaluates critical parameters before allowing donation:
  * **Weight Check:** Minimum threshold of 45 kg.
  * **Hemoglobin Check:** Adaptive genders-based minimum limits.
  * **Temperature Check:** Medically safe range (97.0°F – 99.5°F).
  * **Blood Pressure Check:** Systolic (90–160 mmHg) & Diastolic (60–100 mmHg) safety windows.
  * **Interval Check:** Enforces a minimum 90-day gap since the last donation.
* **Eligibility Success Modal:** Confirms physical fitness in real-time, allowing the user to select donation volume (350ml standard single unit or 450ml double unit).

### 4. 🏆 Automated Certificate Generator
* **High-Definition Print Template:** Renders a gorgeous official *Certificate of Blood Donation* complete with:
  * Authentic double-border design and blood drop watermark.
  * Dynamically populated donor details, donation date, volume, and blood group.
  * Unique transaction ID and secure verification code signatures.
* **Client-Side Export:** Integrated with `html2pdf.js` for instant, pixel-perfect PDF downloads with a single click.

### 5. 🔍 Emergency Donor Directory (`search_donor.php`)
* **Granular Search Filters:** Allows quick lookups by blood group and geographic location to locate active voluntary donors in seconds.

### 6. 🏥 Urgent Blood Request System (`request_blood.php`)
* **Hospital Integration:** Standardized portal for emergency blood requests by doctors, patients, or hospital admins, logging critical parameters like required units, target hospital, and contact channels.

### 7. 🌓 Performance Theme Engine (`theme.js`)
* **Zero-Flicker Toggle:** High-speed immediate-execution toggle block built into document head to prevent white-flash layout shifts when loading the system in dark mode.
* **Persistent Preferences:** Automatically saves theme preference to browser `localStorage`.

---

## 🛠️ Technology Stack

* **Frontend:** HTML5, Custom Vanilla CSS (with CSS Custom Properties/Variables), Vanilla JavaScript, Google Web Fonts (*Outfit*, *Inter*, *Dancing Script*).
* **Backend Engine:** PHP (Structured Scripting).
* **Database Management:** MySQL (Relational Schema).
* **Libraries:** `html2pdf.js` (Client-side PDF compilation library).

---

## 📁 Repository Structure

```
BloodDonation/
├── db.php                     # Database Connection Engine (XAMPP Port 3307)
├── seed_data.sql              # Database initialization schema and sample records
├── index.php                  # Public Cinematic Front Page
├── home.php                   # Live System Stats Dashboard
├── sidebar.php                # Reusable Glassmorphism Navigation Menu
├── donor.php                  # Donor wizard & medical clearance simulator
├── request_blood.php          # Emergency Blood Request Form
├── search_donor.php           # Active Donor Lookup Engine
├── transactions.php           # Unified Logs of Donations and Issuance
├── theme.js                   # Immediate Dark/Light mode theme manager
├── style.css                  # Core design system stylesheet
├── style_v2.css               # Supporting layouts stylesheet
├── about.php                  # Institutional overview
├── contact.php                # Emergency contact desk
└── images/                    # Asset assets & graphics
```

---

## ⚙️ Setup & Installation Instructions

Follow these simple steps to host the LifeLine system locally using **XAMPP**:

### Prerequisites
* Install [XAMPP](https://www.apachefriends.org/index.html) (packaged with Apache server and MySQL database).

### Step 1: Clone/Copy the Codebase
Move the project directory into the root web directory of XAMPP:
```bash
C:\xampp\htdocs\BloodDonation\
```

### Step 2: Set Up the Relational Database
1. Launch the **XAMPP Control Panel** and start both **Apache** and **MySQL**.
2. Open your web browser and navigate to `http://localhost/phpmyadmin/`.
3. Create a new database named **`BDMS`** (or **`bdms`**).
4. Select the newly created database and click the **Import** tab.
5. Choose the `seed_data.sql` file from the project directory and click **Import** to populate the schema and sample records.

### Step 3: Configure Database Connection
If your local MySQL service runs on a port other than `3306` (e.g., `3307`), verify the port configuration inside the `db.php` file:
```php
$conn = new mysqli("localhost", "username", "password", "BDMS", 3307);
```

### Step 4: Run the Application
Open your browser and visit:
```
http://localhost/BloodDonation/index.php
```

---

## 🗄️ Database Schema & Entities

The application communicates with a structured schema consisting of the following key tables:

* **`Donar`:** Stores donor accounts, hashed passwords, date of birth, contact details, blood group, address, and medical variables.
* **`Collection`:** Logs every successful donation transaction (associating `donar_id`, `collection_date`, and `collection_quantity`).
* **`Recipient`:** Holds records of patients who received transfusions.
* **`Issue`:** Tracks issuance of units to specific patients including quantity and target hospital.
* **`Requests`:** Holds active, unresolved blood requests waiting to be fulfilled by voluntary donors.
* **`stock`:** Tracks current physical inventory of blood units, dynamically modified by donation inputs and issuance requests.

---

## 🛡️ Medical Validation Standards Enforced

| Parameter | Minimum Requirement | Maximum Limit | Purpose |
| :--- | :--- | :--- | :--- |
| **Age** | 18 Years | 65 Years | Protects young development and elderly physical recovery. |
| **Weight** | 45 kg | — | Ensures donor has sufficient blood volume to spare. |
| **Hemoglobin** | 12.5 g/dL (Female) / 13.0 g/dL (Male) | — | Prevents post-donation anemia. |
| **Temperature** | 97.0°F | 99.5°F | Screen for ongoing infections or fever. |
| **Systolic BP** | 90 mmHg | 160 mmHg | Ensures stable vascular condition during collection. |
| **Diastolic BP** | 60 mmHg | 100 mmHg | Ensures stable vascular condition during collection. |
| **Interval Gap** | 90 Days | — | Enforces safe red blood cell regeneration windows. |

---

## 🤝 Contribution Guidelines

We welcome contributions to make LifeLine even more powerful:
1. Fork the repository.
2. Create a feature branch (`git checkout -b feature/CoolNewFeature`).
3. Commit your changes (`git commit -m 'Add some CoolNewFeature'`).
4. Push to the branch (`git push origin feature/CoolNewFeature`).
5. Open a Pull Request.

---

*Designed for humanity. Every drop counts.* 🩸
