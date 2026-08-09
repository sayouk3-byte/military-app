-- --------------------------------------------------------
-- រចនាសម្ព័ន្ធទិន្នន័យ database: military_db
-- សម្រាប់កម្មវិធីគ្រប់គ្រងបុគ្គលិកយោធា (RCAF Personnel Management)
-- --------------------------------------------------------

CREATE DATABASE IF NOT EXISTS `military_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `military_db`;

-- --------------------------------------------------------
-- រចនាសម្ព័ន្ធតារាង `military_personnel`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `military_personnel` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `id_card` VARCHAR(50) UNIQUE NOT NULL COMMENT 'អត្តលេខ',
    `name_khmer` VARCHAR(100) NOT NULL COMMENT 'ឈ្មោះខ្មែរ',
    `name_latin` VARCHAR(100) NULL COMMENT 'ឈ្មោះឡាតាំង',
    `gender` VARCHAR(10) DEFAULT 'ប្រុស' COMMENT 'ភេទ',
    `dob` DATE NULL COMMENT 'ថ្ងៃខែឆ្នាំកំណើត',
    `rank` VARCHAR(100) NULL COMMENT 'ឋានន្តរស័ក្តិ',
    `position` VARCHAR(150) NULL COMMENT 'មុខតំណែង',
    `unit` VARCHAR(150) NULL COMMENT 'អង្គភាព',
    `place_of_birth` TEXT NULL COMMENT 'ទីកន្លែងកំណើត',
    `current_address` TEXT NULL COMMENT 'ទីលំនៅបច្ចុប្បន្ន',
    `enlistment_date` DATE NULL COMMENT 'ថ្ងៃខែឆ្នាំចូលបម្រើការងារ',
    `framework_date` DATE NULL COMMENT 'ថ្ងៃខែឆ្នាំចូលក្របខ័ណ្ឌ',
    `marital_status` VARCHAR(50) NULL COMMENT 'ស្ថានភាពរស់នៅ',
    `phone` VARCHAR(30) NULL COMMENT 'លេខទូរស័ព្ទ',
    `notes` TEXT NULL COMMENT 'ផ្សេងៗ',
    `photo` VARCHAR(255) NULL COMMENT 'រូបថត',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_id_card` (`id_card`),
    INDEX `idx_rank` (`rank`),
    INDEX `idx_unit` (`unit`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- ទិន្នន័យគំរូដើម (Demo Military Personnel Data)
-- --------------------------------------------------------

INSERT INTO `military_personnel` 
(`id_card`, `name_khmer`, `name_latin`, `gender`, `dob`, `rank`, `position`, `unit`, `place_of_birth`, `current_address`, `enlistment_date`, `framework_date`, `marital_status`, `phone`, `notes`) 
VALUES
('AF-001092', 'សុខ ចាន់ថន', 'SOK CHANTHORN', 'ប្រុស', '1985-05-12', 'ឧត្តមសេនីយ៍ត្រី', 'មេបញ្ជាការរង', 'បញ្ជាការដ្ឋានកងទ័ពជើងគោក', 'ភូមិព្រែកលៀប សង្កាត់ព្រែកលៀប ខណ្ឌជ្រោយចង្វារ រាជធានីភ្នំពេញ', 'ផ្ទះលេខ ១២E0 ផ្លូវ ២៧១ សង្កាត់បឹងទំពុន ខណ្ឌមានជ័យ រាជធានីភ្នំពេញ', '2005-03-15', '2006-01-10', 'រៀបការរួច', '012 888 999', 'មន្ត្រីឆ្នើម'),
('AF-001093', 'គឹម សុផល', 'KIM SOPHAL', 'ប្រុស', '1988-09-20', 'វរៈសេនីយ៍ឯក', 'ប្រធានការិយាល័យ', 'កងពលតូចអន្តរាគមន៍លេខ១', 'ស្រុកបាទី ខេត្តតាកែវ', 'ភូមិ១ សង្កាត់បឹងកេងកង រាជធានីភ្នំពេញ', '2008-06-01', '2009-02-15', 'រៀបការរួច', '097 777 666', 'ទទួលបានបណ្ណសរសើរ'),
('AF-001094', 'ជិន ស្រីមុំ', 'CHHIN SREYMIC', 'ស្រី', '1992-11-04', 'អនុសេនីយ៍ឯក', 'អនុប្រធានផ្នែក', 'បញ្ជាការដ្ឋានយោធភូមិភាគទី១', 'ស្រុកកំពង់ត្របែក ខេត្តព្រៃវែង', 'សង្កាត់ទួលសង្កែ ខណ្ឌឫស្សីកែវ រាជធានីភ្នំពេញ', '2012-10-01', '2013-05-20', 'នៅលីវ', '088 555 4433', 'ជំនាញរដ្ឋបាល'),
('AF-001095', 'ប៉ែន វ៉ាន់នី', 'PEN VANNY', 'ប្រុស', '1990-02-28', 'វរៈសេនីយ៍ទោ', 'មេបញ្ជាការវរៈ', 'កងពលតូចលេខ ៧០', 'ស្រុកមុខកំពូល ខេត្តកណ្តាល', 'ភូមិព្រែកតាទែន ស្រុកពញាឮ ខេត្តកណ្តាល', '2010-04-12', '2011-08-01', 'រៀបការរួច', '077 123 456', 'ជំនាញយុទ្ធវិធី'),
('AF-001096', 'ម៉ៅ សម្បត្តិ', 'MAO SAMBATH', 'ប្រុស', '1994-07-15', 'អនុសេនីយ៍ទោ', 'នាយនគរបាលយោធា', 'បញ្ជាការដ្ឋានកងរាជអាវុធហត្ថ', 'ស្រុកស្វាយជ្រំ ខេត្តស្វាយរៀង', 'សង្កាត់ចោមចៅ ខណ្ឌពោធិ៍សែនជ័យ រាជធានីភ្នំពេញ', '2015-01-20', '2016-03-10', 'នៅលីវ', '096 999 1122', '');
