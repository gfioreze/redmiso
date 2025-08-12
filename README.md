# Redmiso - Article Publishing Platform

<p align="center">
  <img src="https://raw.githubusercontent.com/gfioreze/redmiso/main/public/assets/redmiso_logo.svg" alt="Redmiso Logo" width="250">
</p>

<p align="center">
  <img src="https://symfony.com/logos/symfony_black_02.svg" alt="Powered by Symfony" width="250">
</p>

## Overview

Redmiso is a modern lifestyle-focused article platform built with Symfony 7.2. It offers a robust environment for discovering and reading high-quality, curated articles.
Registered users can also comment on articles after logging in.

With its clean design and intuitive interface, Redmiso delivers an outstanding user experience, making content discovery and interaction seamless.

## ✨ Features

- **Article Reading Experience**
  - Rich content articles with images and categorization
  - SEO-friendly URL slugs
  - Intuitive article browsing and discovery

- **User System**
  - Secure authentication and registration
  - User profiles for commenting
  - Simple account management

- **Interactive Community**
  - Article commenting system
  - User engagement tracking

- **Content Discovery**
  - Search functionality
  - Category-based navigation side bar for article browsing

- **Modern UI/UX**
  - Responsive design for all devices
  - Clean, minimalist interface
  - Fast loading times

## 🛠️ Technology Stack

- **Backend**
  - PHP 8.2+
  - Symfony 7.2 Framework
  - Doctrine ORM

- **Frontend**
  - Twig Templates
  - CSS3 with modern design principles
  - Vanilla JavaScript

- **Database**
  - MySQL (through Doctrine ORM)

- **Testing**
  - PHPUnit for unit and functional tests
  - Symfony testing utilities

## 🚀 Installation

### Prerequisites

- PHP 8.2 or higher
- Composer
- MySQL or PostgreSQL
- Symfony CLI (optional but recommended)

### Setup Steps

1. **Clone the repository**

```bash
git clone https://github.com/yourusername/redmiso.git
cd redmiso
```

2. **Install dependencies**

```bash
composer install
```

3. **Configure environment variables**

Copy the `.env` file to `.env.local` and configure your database connection:

```bash
cp .env .env.local
# Edit .env.local with your database credentials
```

4. **Create database and run migrations**

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

5. **Load fixtures (optional, for development)**

```bash
php bin/console doctrine:fixtures:load
```

6. **Start the Symfony development server**

```bash
symfony server:start
# or
php -S localhost:8000 -t public/
```

7. **Visit the application in your browser**

Open `http://localhost:8000` in your web browser.

## 🧪 Running Tests

Redmiso includes comprehensive tests to ensure functionality remains consistent:

```bash
php bin/phpunit
```

## 📝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the [MIT License](LICENSE) - see the LICENSE file for details.

## 🙏 Acknowledgements

- [PHP](https://www.php.net/)
- [Symfony](https://symfony.com/)
- [PHPUnit](https://phpunit.de/)
- [Bootstrap Icons](https://icons.getbootstrap.com/)

---

<p align="center">
  Made with ❤️ by me :)
</p>
