DROP TABLE IF EXISTS `test`.`core_students`;

CREATE TABLE `test`.`core_students` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    birthdate VARCHAR(50),
    address VARCHAR(255),
    city VARCHAR(100),
    state VARCHAR(50),
    zip VARCHAR(20),
    email VARCHAR(150),
    phone VARCHAR(50),
    grade VARCHAR(50),
    parent1_name VARCHAR(100),
    parent1_email VARCHAR(150),
    parent1_phone VARCHAR(50),
    parent2_name VARCHAR(100),
    parent2_email VARCHAR(150),
    parent2_phone VARCHAR(50),
    admission_date VARCHAR(50),
    tuition_fee INT,
    total_fees INT,
    total_paid INT,
    balance INT,
    photo VARCHAR(255)
);

-- Sample Data Insert
INSERT INTO `test`.`core_students`
(first_name, last_name, birthdate, address, city, state, zip, email, phone, grade,
parent1_name, parent1_email, parent1_phone,
parent2_name, parent2_email, parent2_phone,
admission_date, tuition_fee, total_fees, total_paid, balance, photo)
VALUES
('Freddy', 'Fredders', '2010-01-02', '12531 Main St.', 'Los Angeles', 'CA', '90034', 'fred@ferdders.com', '23115454', '4th Grade',
 'Par 1', 'par1@test.com', '1213112121',
 'Par 2', 'par2@test.com', '121151515',
 '2020-01-15', 4545, 5000, 3000, 2000, 'freddy.jpg'),

('Lucy', 'Smith', '2011-03-15', '742 Evergreen Terrace', 'Springfield', 'IL', '62704', 'lucy.smith@example.com', '9876543210', '3rd Grade',
 'John Smith', 'john.smith@example.com', '1234567890',
 'Jane Smith', 'jane.smith@example.com', '0987654321',
 '2021-08-20', 4000, 4500, 2500, 2000, 'lucy.jpg'),

('Mark', 'Johnson', '2009-11-05', '123 Oak Street', 'Austin', 'TX', '73301', 'mark.johnson@example.com', '5551234567', '5th Grade',
 'Robert Johnson', 'robert.johnson@example.com', '5557654321',
 'Mary Johnson', 'mary.johnson@example.com', '5559876543',
 '2019-05-10', 5000, 5500, 4000, 1500, 'mark.jpg');
