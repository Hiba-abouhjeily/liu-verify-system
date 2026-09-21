# LIU Verify System

A web-based verification system for issuing, managing, and validating academic certificates. Built with PHP and MySQL, the application uses SHA-256 hashing and dynamic QR codes to verify record authenticity.

## Features

- **PDF Certificate Generation**: Creates downloadable certificates formatted with the FPDF library.
- **QR Code Verification**: Generates unique QR codes using PHPQRCode for instant document scanning and lookup.
- **SHA-256 Record Protection**: Hashes certificate records upon creation to detect tampering.
- **Role-Based Login**: Access controls separated for administrators, staff, and students.
- **Database Tracking**: Stores student details, verification hashes, and record creation dates in MySQL.

## Tech Stack

- **Backend**: PHP
- **Database**: MySQL / phpMyAdmin
- **Frontend**: HTML5, CSS3, JavaScript
- **Libraries**:
  - FPDF
  - PHPQRCode

## Folder Structure

```text
liu-verify-system/
│
├── assets/          # CSS, JavaScript, and image files
├── libs/            # FPDF and PHPQRCode libraries
├── config.php       # Database connection file
├── database.sql     # Database schema and initial data
├── dashboard.php    # User and admin dashboard
├── index.php        # Main portal page
├── login.php        # Account authentication
├── verify.php       # Public verification lookup
└── README.md        # Documentation
