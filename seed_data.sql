-- Clear all existing transaction data
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE BDMS.Collection;
TRUNCATE TABLE BDMS.Issue;
TRUNCATE TABLE BDMS.Requests;
TRUNCATE TABLE BDMS.Donar;
TRUNCATE TABLE BDMS.Recipient;
SET FOREIGN_KEY_CHECKS = 1;

-- ══════════════════════════════════════════════
-- INSERT 16 DONORS (min 2 per blood group)
-- ══════════════════════════════════════════════
INSERT INTO BDMS.Donar (donar_name, donar_age, donar_gender, donar_blood_group, donar_contact, donar_address) VALUES
-- A+
('Rahul Sharma',     28, 'Male',   'A+',  '+91-9876501001', 'Hubli, Karnataka'),
('Priya Nair',       25, 'Female', 'A+',  '+91-9876501002', 'Hubli, Karnataka'),
-- A-
('Suresh Patil',     34, 'Male',   'A-',  '+91-9876501003', 'Dharwad, Karnataka'),
('Anjali Desai',     29, 'Female', 'A-',  '+91-9876501004', 'Dharwad, Karnataka'),
-- B+
('Vikram Reddy',     31, 'Male',   'B+',  '+91-9876501005', 'Belgaum, Karnataka'),
('Meena Iyer',       27, 'Female', 'B+',  '+91-9876501006', 'Belgaum, Karnataka'),
-- B-
('Arun Kumar',       40, 'Male',   'B-',  '+91-9876501007', 'Mysore, Karnataka'),
('Lakshmi Rao',      33, 'Female', 'B-',  '+91-9876501008', 'Mysore, Karnataka'),
-- O+
('Manoj Hegde',      26, 'Male',   'O+',  '+91-9876501009', 'Hubli, Karnataka'),
('Deepika Singh',    22, 'Female', 'O+',  '+91-9876501010', 'Hubli, Karnataka'),
-- O-
('Ravi Kulkarni',    38, 'Male',   'O-',  '+91-9876501011', 'Bangalore, Karnataka'),
('Sunita Joshi',     30, 'Female', 'O-',  '+91-9876501012', 'Bangalore, Karnataka'),
-- AB+
('Kiran Naik',       35, 'Male',   'AB+', '+91-9876501013', 'Mangalore, Karnataka'),
('Pooja Shetty',     24, 'Female', 'AB+', '+91-9876501014', 'Mangalore, Karnataka'),
-- AB-
('Ganesh Bhat',      42, 'Male',   'AB-', '+91-9876501015', 'Udupi, Karnataka'),
('Rekha Gowda',      36, 'Female', 'AB-', '+91-9876501016', 'Udupi, Karnataka');

-- ══════════════════════════════════════════════
-- INSERT 5 DONOR COLLECTION RECORDS
-- ══════════════════════════════════════════════
INSERT INTO BDMS.Collection (donar_id, collection_date, collection_quantity) VALUES
(1,  '2026-04-10', 450),
(3,  '2026-04-15', 350),
(5,  '2026-04-20', 450),
(9,  '2026-05-01', 450),
(11, '2026-05-10', 350);

-- ══════════════════════════════════════════════
-- INSERT 5 RECIPIENTS
-- ══════════════════════════════════════════════
INSERT INTO BDMS.Recipient (recipient_name, recipient_age, recipient_gender, recipient_blood_group, recipient_contact, recipient_hospital) VALUES
('Amit Verma',    45, 'Male',   'A+',  '+91-9988001001', 'KIMS Hospital, Hubli'),
('Sonal Mehta',   32, 'Female', 'B+',  '+91-9988001002', 'Apollo Hospital, Bangalore'),
('Rajan Das',     60, 'Male',   'O+',  '+91-9988001003', 'Manipal Hospital, Mangalore'),
('Neha Gupta',    28, 'Female', 'AB+', '+91-9988001004', 'Fortis Hospital, Bangalore'),
('Sunil Yadav',   50, 'Male',   'O-',  '+91-9988001005', 'SDM Hospital, Dharwad');

-- ══════════════════════════════════════════════
-- INSERT 5 BLOOD ISSUE RECORDS (Recipient Transactions)
-- ══════════════════════════════════════════════
INSERT INTO BDMS.Issue (recipient_id, blood_group, quantity, issue_date) VALUES
(1, 'A+',  450, '2026-04-12'),
(2, 'B+',  350, '2026-04-22'),
(3, 'O+',  450, '2026-05-03'),
(4, 'AB+', 250, '2026-05-11'),
(5, 'O-',  350, '2026-05-14');

-- ══════════════════════════════════════════════
-- INSERT 5 BLOOD REQUESTS (5 different hospitals)
-- ══════════════════════════════════════════════
INSERT INTO BDMS.Requests (patient_name, blood_group, units, hospital, contact) VALUES
('Ramesh Kumar',  'A+',  2, 'KIMS Hospital, Hubli',           '+91-9900001001'),
('Fatima Sheikh', 'B-',  1, 'Apollo Hospital, Bangalore',     '+91-9900001002'),
('Dev Anand',     'O+',  3, 'Manipal Hospital, Mangalore',    '+91-9900001003'),
('Preethi Raj',   'AB-', 1, 'Narayana Health, Bangalore',     '+91-9900001004'),
('Mohan Lal',     'O-',  2, 'Fortis Hospital, Hubli',         '+91-9900001005');
