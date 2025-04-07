# Git Workflow for Trees-In-Paris

## Branches
- `main`: Production-ready code
- `dev`: Integration branch for features
- `feature/feature-name`: Individual feature development

## Workflow Steps
1. Create a feature branch from develop
   ```bash
   git checkout develop
   git pull
   git checkout -b feature/new-feature
   ```

2. Develop and commit changes
   ```bash
   git add .
   git commit -m "Description of changes"
   ```

3. Push to remote repository
   ```bash
   git push -u origin feature/new-feature
   ```

4. Create pull request to develop branch

5. After review and testing, merge to develop

6. Periodically merge develop to main for releases
```

For the database structure documentation:

```markdown:doc/database/dbStructure.md
# Database Structure Documentation

This document tracks the evolution of the database structure for the Trees-In-Paris project.

## Current Structure (Version 1.0)

### Tables

#### trees
- `id` INT PRIMARY KEY AUTO_INCREMENT
- `name` VARCHAR(255) NOT NULL
- `species` VARCHAR(255)
- `latitude` DECIMAL(10,8) NOT NULL
- `longitude` DECIMAL(11,8) NOT NULL
- `height` DECIMAL(5,2)
- `diameter` DECIMAL(5,2)
- `plantingDate` DATE
- `lastInspection` DATE
- `healthStatus` ENUM('good', 'fair', 'poor')
- `notes` TEXT

#### users
- `id` INT PRIMARY KEY AUTO_INCREMENT
- `username` VARCHAR(50) NOT NULL UNIQUE
- `email` VARCHAR(100) NOT NULL UNIQUE
- `password` VARCHAR(255) NOT NULL
- `role` ENUM('admin', 'editor', 'viewer') DEFAULT 'viewer'
- `createdAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP

## Version History

### Version 1.0 (Initial Structure)
- Created `trees` and `users` tables
```

For the test data script:

```sql:doc/database/scripts/testData.sql
-- Test Data for Trees-In-Paris Database
-- This script populates the database with sample data for testing purposes

-- Clear existing data
DELETE FROM trees;
DELETE FROM users;
ALTER TABLE trees AUTO_INCREMENT = 1;
ALTER TABLE users AUTO_INCREMENT = 1;

-- Insert sample users
INSERT INTO users (username, email, password, role) VALUES
('admin', 'admin@trees-paris.org', '$2y$10$someHashedPasswordString', 'admin'),
('editor1', 'editor1@trees-paris.org', '$2y$10$someHashedPasswordString', 'editor'),
('viewer1', 'viewer1@trees-paris.org', '$2y$10$someHashedPasswordString', 'viewer');

-- Insert sample trees
INSERT INTO trees (name, species, latitude, longitude, height, diameter, plantingDate, lastInspection, healthStatus, notes) VALUES
('Oak 1', 'Quercus robur', 48.8566, 2.3522, 15.5, 0.8, '2000-03-15', '2023-01-10', 'good', 'Healthy tree in central Paris'),
('Plane 1', 'Platanus x hispanica', 48.8606, 2.3376, 18.2, 1.2, '1985-11-20', '2023-02-05', 'good', 'Large plane tree near the Seine'),
('Chestnut 1', 'Aesculus hippocastanum', 48.8737, 2.2950, 12.0, 0.6, '2010-04-30', '2023-01-25', 'fair', 'Some leaf damage noted'),
('Linden 1', 'Tilia cordata', 48.8584, 2.2945, 10.5, 0.5, '2015-10-10', '2023-02-15', 'good', 'Young tree in good condition'),
('Maple 1', 'Acer platanoides', 48.8331, 2.3264, 8.0, 0.4, '2018-03-22', '2023-01-18', 'good', 'Recently planted tree');
