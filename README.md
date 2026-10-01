# 🎬 Moreseen

> **Discover. Recommend. Connect.**

Moreseen is a movie and TV recommendation platform built to help users discover movies and shows, share recommendations, and support creators through direct tips.

The platform combines **TMDb movie data**, user-generated recommendations, authentication, Google Sign-In, and Paystack payments into one cinematic experience.

---

## ✨ Features

### 🎥 Movie & TV Discovery

* Browse popular and trending movies and TV shows
* Explore top-rated content
* Search for movies and TV shows
* View detailed movie/show information
* Display official TMDb posters and metadata

### 💡 Recommendations

* Create personal movie and TV recommendations
* View recommendations from other users
* Edit and delete your own recommendations
* Browse individual recommendation pages
* Track recommendation activity

### 👤 Authentication

* User registration and login
* Secure password hashing
* Session-based authentication
* Google OAuth 2.0 Sign-In
* Protection against unauthorized actions
* CSRF protection for sensitive forms

### 💰 Creator Tips

* Users can support recommendation creators through tips
* Paystack payment integration
* Server-side payment verification
* Payment status tracking
* Protection against self-tipping

### 🎨 User Interface

* Dark cinematic design
* Responsive layouts
* Modern card-based interface
* Gold accent styling
* Mobile-friendly pages

---

## 🛠️ Technologies Used

| Technology           | Purpose                    |
| -------------------- | -------------------------- |
| **PHP**              | Backend application logic  |
| **MySQL**            | Database                   |
| **HTML5**            | Page structure             |
| **CSS3**             | User interface and styling |
| **TMDb API**         | Movie & TV data            |
| **Google OAuth 2.0** | Google authentication      |
| **Paystack API**     | Payment processing         |
| **Composer**         | PHP dependency management  |
| **Git & GitHub**     | Version control            |

---

## 🏗️ Project Structure

```text
Moreseen/
│
├── api/
│   └── tmb/
│       ├── details.php
│       ├── popular.php
│       ├── search.php
│       ├── top-rated.php
│       └── trending.php
│
├── config/
│   ├── config.php
│   ├── dbconnect.php
│   └── tmdb.php
│
├── controllers/
│   ├── AuthController.php
│   ├── GoogleAuthController.php
│   ├── PaystackCallback.php
│   └── PaystackController.php
│
├── helpers/
│   ├── auth.php
│   └── csrf.php
│
├── models/
│   ├── Media.php
│   └── Recommendation.php
│
├── views/
│   ├── auth/
│   ├── media/
│   ├── profile/
│   └── recommendations/
│
├── .env.example
├── .gitignore
├── composer.json
├── composer.lock
└── README.md
```

---

## 🚀 Getting Started

Follow these steps to run Moreseen locally.

### 1. Clone the repository

```bash
git clone https://github.com/Neverseen042/moreseen.git
```

Move into the project directory:

```bash
cd moreseen
```

---

### 2. Install Composer dependencies

Make sure Composer is installed, then run:

```bash
composer install
```

This will recreate the `vendor/` directory.

---

### 3. Create the environment file

Create a `.env` file in the project root.

You can use `.env.example` as a template:

```bash
cp .env.example .env
```

On Windows, you can also simply create a copy of `.env.example` and rename it to:

```text
.env
```

---

### 4. Configure environment variables

Open `.env` and add your local configuration.

```env
DB_HOST=localhost
DB_NAME=moreseen
DB_USER=root
DB_PASSWORD=

TMDB_API_KEY=

GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=http://localhost/moreseen/controllers/GoogleAuthController.php

PAYSTACK_SECRET_KEY=
PAYSTACK_PUBLIC_KEY=
```

**Never commit your real `.env` file to GitHub.**

---

### 5. Configure MySQL

Create a MySQL database named:

```text
moreseen
```

Then configure the database credentials in your `.env` file.

---

### 6. Run the project with XAMPP

Place the project inside your XAMPP `htdocs` directory:

```text
C:\xampp\htdocs\moreseen
```

Start:

* Apache
* MySQL

Then open:

```text
http://localhost/moreseen
```

---

## 🔐 Security

Moreseen uses several security practices, including:

* Password hashing
* Session-based authentication
* CSRF protection
* Server-side payment verification
* Environment variables for sensitive credentials
* Protected API credentials
* Authorization checks for user-owned content

Sensitive credentials such as API keys and OAuth secrets should **never** be committed to the repository.

---

## 💳 Payment Integration

Moreseen uses **Paystack** for processing creator tips.

The payment flow includes:

1. User selects a recommendation creator.
2. User enters a tip amount.
3. Moreseen creates a pending transaction.
4. User is redirected to Paystack.
5. Paystack processes the payment.
6. Moreseen verifies the transaction server-side.
7. The transaction status is updated.

---

## 🎬 TMDb Integration

Moreseen uses the **TMDb API** to retrieve movie and TV information including:

* Titles
* Posters
* Ratings
* Release dates
* Overviews
* Genres
* Trending content
* Popular content
* Top-rated content
* Search results

> Moreseen uses the TMDb API but is not endorsed or certified by TMDb.

---

## 🔑 Google Authentication

Users can authenticate using Google OAuth 2.0.

The authentication flow:

```text
User
  ↓
Google Sign-In
  ↓
Google OAuth
  ↓
Moreseen
  ↓
Verify Google Identity
  ↓
Create / Find User
  ↓
Login
```

---

## 🧪 Development

Moreseen is currently developed and tested in a local XAMPP environment using:

```text
PHP
MySQL
Apache
Composer
```

The project is structured so additional features can be added without changing the entire application architecture.

---

## 🔮 Future Improvements

Potential future improvements include:

* ⭐ Recommendation ratings
* ❤️ Favorites/watchlist
* 🔔 Notifications
* 💬 Recommendation comments
* 📊 Creator analytics
* 🏆 Recommendation leaderboards
* 📱 Progressive Web App support
* ☁️ Production cloud deployment

---

## 👨‍💻 Author

**Obi Chidiebube Fabian**

Software Engineering Student & Aspiring Software Engineer

GitHub: [@Neverseen042](https://github.com/Neverseen042)

---

## 📄 License

This project is currently intended for educational, portfolio, and development purposes.
