# 📚 Quantum Bookstore – Fawry Internship Task
(images/codeRun.png)
(images/output.png)
This project is a simple simulation of an online bookstore built in PHP, as part of the **Fawry Full Stack Internship - 2nd Challenge**.

It showcases core object-oriented programming (OOP) principles and focuses on building an easily extensible system for managing different types of books.

---

## 🚀 Features

- ✅ Add books to the inventory with:
  - ISBN
  - Title
  - Author
  - Year of publication
  - Price
- 📦 Support for multiple book types:
  - `PaperBook` – has stock, requires shipping
  - `EBook` – has file type, sent via email
  - `DemoBook` – not for sale
- 🔁 Remove outdated books (older than X years)
- 🛒 Buy books by ISBN
  - Validates availability
  - Reduces quantity (for paper books)
  - Sends to:
    - **ShippingService** (simulated)
    - **MailService** (simulated)
- 🔐 Fully OOP and extendable
- 🧪 Comes with a test class: `QuantumBookstoreFullTest`

---

## 🛠️ Technologies

- PHP 8+
- No external libraries
- Console-based application

---

## 📁 Folder Structure


