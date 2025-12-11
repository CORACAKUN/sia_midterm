# 📘 Student API (PHP + MySQL)

A lightweight, schema-validated REST API for managing student records.  
Supports **JSON** and **XML** responses, includes **JSON Schema validation**, and is fully testable via **Postman**.

---

## 🚀 Features
- Create, read, update, and delete (CRUD) student records  
- Input validation using **custom JSON Schema validator**  
- Prevention of duplicate **student_id** and **rfid_uid**  
- Standardized JSON & XML responses  
- Clean modular structure (schemas, utils, student endpoints)  
- Fully compatible with Postman for testing  
- Local and server-ready version  

---

## 📂 Folder Structure
```
student_api/
│
├── schemas/
│   ├── student_create_request.json
│   ├── student_update_request.json
│   ├── student_response.json
│   ├── student_list_response.json
│
├── students/
│   ├── create.php
│   ├── get_all.php
│   ├── get_one.php
│   ├── update.php
│   ├── delete.php
│
└── utils/
    ├── json_validator.php
    ├── response.php
    └── xml.php
```

---

## 🛠 Requirements
- PHP 8+  
- MySQL / MariaDB  
- Apache / Nginx  
- Postman (optional)  
- `config/db_connection.php` file  

---

## 🧱 Database Structure
```sql
CREATE TABLE student (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_id VARCHAR(50) NOT NULL,
  full_name VARCHAR(255) NOT NULL,
  course VARCHAR(100) NOT NULL,
  year_level VARCHAR(50) NOT NULL,
  rfid_uid VARCHAR(100) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

## 🌐 API Endpoints

### **1. Create Student**  
**POST** `/students/create.php`

**Body**
```json
{
  "student_id": "2025-001",
  "full_name": "John Doe",
  "course": "BSIT",
  "year_level": "3",
  "rfid_uid": "RFID12345"
}
```

**Response**
```json
{
  "status": "success",
  "data": {
    "id": 1,
    "student_id": "2025-001",
    "full_name": "John Doe",
    "course": "BSIT",
    "year_level": "3",
    "rfid_uid": "RFID12345",
    "created_at": "2025-01-01 10:00:00"
  }
}
```

---

### **2. Get All Students**  
**GET** `/students/get_all.php`

---

### **3. Get One Student**  
**GET** `/students/get_one.php?id=1`

---

### **4. Update Student**  
**PUT** `/students/update.php?id=1`

**Body**
```json
{
  "student_id": "2025-001",
  "full_name": "John Updated",
  "course": "BSIT",
  "year_level": "4",
  "rfid_uid": "RFID12345"
}
```

---

### **5. Delete Student**  
**DELETE** `/students/delete.php?id=1`

---

## 📄 JSON Schema Validation  
The API validates requests and responses using schemas located in `/schemas/`.

---

## 📨 JSON & XML Support
The API checks the **Accept** header:

| Accept | Response |
|--------|----------|
| `application/json` | JSON |
| `application/xml` | XML |

---

## 🧪 Testing With Postman
1. Choose your HTTP method  
2. Set Body → Raw → JSON (for POST/PUT)  
3. Add headers:
```
Content-Type: application/json
Accept: application/json
```

---

## 🌍 Deployment Notes  
- Works locally and online  
- Ensure DB credentials match the hosting environment  
- PHP 8+ recommended  

---

## 📄 License  
MIT License (optional)
