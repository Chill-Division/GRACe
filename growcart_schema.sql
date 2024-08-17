-- Create Users table
CREATE TABLE Users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(255) NOT NULL,
    surname VARCHAR(255) NOT NULL,
    username VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    last_logged_in DATETIME(6)
);

-- Create Companies table
CREATE TABLE Companies (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    license_number VARCHAR(255) NOT NULL,
    address TEXT,
    primary_contact_name VARCHAR(255),
    primary_contact_email VARCHAR(255),
    primary_contact_phone VARCHAR(255)
);

-- Create Genetics table
CREATE TABLE Genetics (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    breeder VARCHAR(255),
    genetic_lineage TEXT
);

-- Create Plants table
CREATE TABLE Plants (
    id INT PRIMARY KEY AUTO_INCREMENT,
    genetics_id INT,
    status ENUM('Growing', 'Harvested', 'Destroyed', 'Sent'),
    date_created DATETIME,
    date_harvested DATETIME,
    FOREIGN KEY (genetics_id) REFERENCES Genetics(id)
);

-- Create Flower table to handle dried flower
CREATE TABLE Flower (
    id INT PRIMARY KEY AUTO_INCREMENT,
    genetics_id INT,
    weight DECIMAL(10, 2) NOT NULL,
    transaction_type ENUM('Add', 'Subtract') NOT NULL,
    transaction_date DATETIME NOT NULL,
    reason VARCHAR(255),
    company_id INT,
    FOREIGN KEY (company_id) REFERENCES Companies(id),
    FOREIGN KEY (genetics_id) REFERENCES Genetics(id)
);


-- Create ShippingManifests table
CREATE TABLE ShippingManifests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    sender_id INT,
    sending_company_id INT,
    recipient_id INT,
    shipment_date DATETIME,
    product_type VARCHAR(255),
    item_count INT,
    net_weight DECIMAL(10, 2),
    gross_weight DECIMAL(10, 2),
    manifest_file VARCHAR(255), 
    FOREIGN KEY (sender_id) REFERENCES Users(id),
    FOREIGN KEY (sending_company_id) REFERENCES Companies(id),
    FOREIGN KEY (recipient_id) REFERENCES Companies(id)
);

-- Create PoliceVettingRecords table
CREATE TABLE PoliceVettingRecords (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT, 
    record_date DATE,
    file_path VARCHAR(255),
    FOREIGN KEY (user_id) REFERENCES Users(id) 
);

-- Create SOPs table
CREATE TABLE SOPs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    upload_date DATE,
    file_path VARCHAR(255)
);
