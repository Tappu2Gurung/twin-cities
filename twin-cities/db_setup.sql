CREATE DATABASE IF NOT EXISTS twin_cities CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE twin_cities;

CREATE TABLE IF NOT EXISTS category (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL
);

CREATE TABLE IF NOT EXISTS city (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    country VARCHAR(100) NOT NULL,
    population INT,
    latitude DECIMAL(10,6) NOT NULL,
    longitude DECIMAL(10,6) NOT NULL,
    currency VARCHAR(50),
    language VARCHAR(50),
    timezone VARCHAR(50),
    description TEXT
);

CREATE TABLE IF NOT EXISTS twinning (
    id INT AUTO_INCREMENT PRIMARY KEY,
    city1_id INT NOT NULL,
    city2_id INT NOT NULL,
    year_established INT,
    FOREIGN KEY (city1_id) REFERENCES city(id),
    FOREIGN KEY (city2_id) REFERENCES city(id)
);

CREATE TABLE IF NOT EXISTS place_of_interest (
    id INT AUTO_INCREMENT PRIMARY KEY,
    city_id INT NOT NULL,
    category_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    capacity INT,
    latitude DECIMAL(10,6) NOT NULL,
    longitude DECIMAL(10,6) NOT NULL,
    description TEXT,
    photo_url VARCHAR(255),
    wikipedia_url VARCHAR(255),
    FOREIGN KEY (city_id) REFERENCES city(id),
    FOREIGN KEY (category_id) REFERENCES category(id)
);

-- Categories
INSERT INTO category (name) VALUES
('Stadium'), ('University'), ('Cathedral'), ('Airport'),
('Railway Station'), ('Museum'), ('Concert Hall'), ('Library');

-- Cities
INSERT INTO city (name, country, population, latitude, longitude, currency, language, timezone, description) VALUES
('Birmingham', 'United Kingdom', 1141400, 52.4862, -1.8904, 'GBP', 'English', 'Europe/London',
 'Birmingham is the second-largest city in the UK, known for its industrial heritage and vibrant cultural scene.'),
('Frankfurt', 'Germany', 773000, 50.1109, 8.6821, 'EUR', 'German', 'Europe/Berlin',
 'Frankfurt is Germany''s financial capital and home to the European Central Bank.'),
('Lyon', 'France', 522228, 45.7640, 4.8357, 'EUR', 'French', 'Europe/Paris',
 'Lyon is France''s third-largest city, renowned for its gastronomy and Renaissance architecture.');

-- Twinning relationships
INSERT INTO twinning (city1_id, city2_id, year_established) VALUES
(1, 2, 1966),
(1, 3, 1951);

-- Places of Interest: Birmingham
INSERT INTO place_of_interest (city_id, category_id, name, capacity, latitude, longitude, description, photo_url, wikipedia_url) VALUES
(1, 1, 'Villa Park', 42640, 52.5090, -1.8847, 'Home of Aston Villa FC, one of England''s oldest football stadiums.',
 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/9e/Villa_Park_2018.jpg/320px-Villa_Park_2018.jpg',
 'https://en.wikipedia.org/wiki/Villa_Park'),
(1, 2, 'University of Birmingham', 38000, 52.4508, -1.9303, 'A leading Russell Group university founded in 1900.',
 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/27/Aston_Webb_building%2C_University_of_Birmingham.jpg/320px-Aston_Webb_building%2C_University_of_Birmingham.jpg',
 'https://en.wikipedia.org/wiki/University_of_Birmingham'),
(1, 3, 'Birmingham Cathedral', NULL, 52.4810, -1.8990, 'Grade I listed Anglican cathedral in the heart of the city.',
 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/30/Birmingham_Cathedral_-_geograph.org.uk.jpg/320px-Birmingham_Cathedral_-_geograph.org.uk.jpg',
 'https://en.wikipedia.org/wiki/Birmingham_Cathedral'),
(1, 4, 'Birmingham Airport', NULL, 52.4539, -1.7480, 'The busiest airport in the UK outside London.',
 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/4b/BHX_Terminal.jpg/320px-BHX_Terminal.jpg',
 'https://en.wikipedia.org/wiki/Birmingham_Airport'),
(1, 5, 'Birmingham New Street Station', NULL, 52.4778, -1.9000, 'The busiest railway station outside London.',
 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/60/New_Street_Station_Birmingham_2015.jpg/320px-New_Street_Station_Birmingham_2015.jpg',
 'https://en.wikipedia.org/wiki/Birmingham_New_Street_station'),
(1, 6, 'Birmingham Museum & Art Gallery', NULL, 52.4800, -1.9030, 'Houses an outstanding collection of Pre-Raphaelite art.',
 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e8/Birmingham_Museum_and_Art_Gallery.jpg/320px-Birmingham_Museum_and_Art_Gallery.jpg',
 'https://en.wikipedia.org/wiki/Birmingham_Museum_and_Art_Gallery'),
(1, 7, 'Symphony Hall', 2262, 52.4773, -1.9072, 'One of the world''s finest concert halls, home of the CBSO.',
 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/63/Birmingham_Symphony_Hall.jpg/320px-Birmingham_Symphony_Hall.jpg',
 'https://en.wikipedia.org/wiki/Symphony_Hall,_Birmingham'),
(1, 8, 'Birmingham Central Library', NULL, 52.4791, -1.9083, 'The largest public library in Europe.',
 'https://upload.wikimedia.org/wikipedia/commons/thumb/b/b4/Library_of_Birmingham_2014.jpg/320px-Library_of_Birmingham_2014.jpg',
 'https://en.wikipedia.org/wiki/Library_of_Birmingham');

-- Places of Interest: Frankfurt
INSERT INTO place_of_interest (city_id, category_id, name, capacity, latitude, longitude, description, photo_url, wikipedia_url) VALUES
(2, 1, 'Deutsche Bank Park', 51500, 50.0687, 8.6450, 'Home of Eintracht Frankfurt football club.',
 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/5e/Frankfurt_Commerzbank-Arena_-_Luftaufnahme.jpg/320px-Frankfurt_Commerzbank-Arena_-_Luftaufnahme.jpg',
 'https://en.wikipedia.org/wiki/Deutsche_Bank_Park'),
(2, 2, 'Goethe University Frankfurt', 45000, 50.1281, 8.6694, 'Named after Frankfurt''s most famous son, Johann Wolfgang von Goethe.',
 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/13/GoetheUni_Frankfurt_Westend-Campus_Hauptgebaeude.jpg/320px-GoetheUni_Frankfurt_Westend-Campus_Hauptgebaeude.jpg',
 'https://en.wikipedia.org/wiki/Goethe_University_Frankfurt'),
(2, 3, 'Frankfurt Cathedral', NULL, 50.1103, 8.6830, 'Historic Gothic cathedral where Holy Roman Emperors were crowned.',
 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/5e/Frankfurt_Dom_2013.jpg/320px-Frankfurt_Dom_2013.jpg',
 'https://en.wikipedia.org/wiki/Frankfurt_Cathedral'),
(2, 4, 'Frankfurt Airport', NULL, 50.0379, 8.5622, 'Germany''s busiest and Europe''s third busiest airport.',
 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8f/FRA_Airport_Terminal_1.jpg/320px-FRA_Airport_Terminal_1.jpg',
 'https://en.wikipedia.org/wiki/Frankfurt_Airport'),
(2, 5, 'Frankfurt Central Station', NULL, 50.1070, 8.6634, 'One of the busiest railway stations in Europe.',
 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/4b/Hauptbahnhof_Frankfurt_2009.jpg/320px-Hauptbahnhof_Frankfurt_2009.jpg',
 'https://en.wikipedia.org/wiki/Frankfurt_Central_Station'),
(2, 6, 'Städel Museum', NULL, 50.1036, 8.6825, 'One of Germany''s most important art museums, founded in 1815.',
 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/5e/St%C3%A4del_Museum_Frankfurt.jpg/320px-St%C3%A4del_Museum_Frankfurt.jpg',
 'https://en.wikipedia.org/wiki/Städel'),
(2, 7, 'Alte Oper', 2500, 50.1151, 8.6726, 'Historic opera house and concert hall rebuilt after WWII.',
 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/da/Alte_Oper_Frankfurt_2013.jpg/320px-Alte_Oper_Frankfurt_2013.jpg',
 'https://en.wikipedia.org/wiki/Alte_Oper'),
(2, 8, 'Frankfurt City Library', NULL, 50.1109, 8.6797, 'The main public library of Frankfurt am Main.',
 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8b/Stadtbibliothek_Frankfurt.jpg/320px-Stadtbibliothek_Frankfurt.jpg',
 'https://en.wikipedia.org/wiki/Frankfurt_City_Library');

-- Places of Interest: Lyon
INSERT INTO place_of_interest (city_id, category_id, name, capacity, latitude, longitude, description, photo_url, wikipedia_url) VALUES
(3, 1, 'Groupama Stadium', 59186, 45.7657, 4.9822, 'Home of Olympique Lyonnais, the largest stadium in France.',
 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/50/Parc_OL_-_vue_a%C3%A9rienne_%282017%29.jpg/320px-Parc_OL_-_vue_a%C3%A9rienne_%282017%29.jpg',
 'https://en.wikipedia.org/wiki/Groupama_Stadium'),
(3, 2, 'University of Lyon', 120000, 45.7782, 4.8655, 'A large public university system with multiple campuses across the city.',
 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/30/Lyon_universite.jpg/320px-Lyon_universite.jpg',
 'https://en.wikipedia.org/wiki/University_of_Lyon'),
(3, 3, 'Lyon Cathedral', NULL, 45.7596, 4.8273, 'A stunning example of French Gothic architecture on the banks of the Saône.',
 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/56/Lyon_Cathedrale_Saint_Jean.jpg/320px-Lyon_Cathedrale_Saint_Jean.jpg',
 'https://en.wikipedia.org/wiki/Lyon_Cathedral'),
(3, 4, 'Lyon–Saint-Exupéry Airport', NULL, 45.7256, 5.0811, 'The main international airport serving Lyon, named after the author of The Little Prince.',
 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/6f/Aeroport_Lyon_Saint_Exupery.jpg/320px-Aeroport_Lyon_Saint_Exupery.jpg',
 'https://en.wikipedia.org/wiki/Lyon%E2%80%93Saint-Exup%C3%A9ry_Airport'),
(3, 5, 'Lyon Part-Dieu Station', NULL, 45.7606, 4.8597, 'France''s second busiest railway station after Paris Gare du Nord.',
 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/40/Gare_de_Lyon_Part_Dieu.jpg/320px-Gare_de_Lyon_Part_Dieu.jpg',
 'https://en.wikipedia.org/wiki/Lyon-Part-Dieu_station'),
(3, 6, 'Musée des Beaux-Arts de Lyon', NULL, 45.7671, 4.8336, 'One of the largest fine arts museums in France.',
 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/9d/Mus%C3%A9e_des_Beaux-Arts_de_Lyon.jpg/320px-Mus%C3%A9e_des_Beaux-Arts_de_Lyon.jpg',
 'https://en.wikipedia.org/wiki/Mus%C3%A9e_des_Beaux-Arts_de_Lyon'),
(3, 7, 'Auditorium de Lyon', 2100, 45.7479, 4.8488, 'Home of the Orchestre National de Lyon.',
 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/7c/Auditorium_de_Lyon.jpg/320px-Auditorium_de_Lyon.jpg',
 'https://en.wikipedia.org/wiki/Auditorium_de_Lyon'),
(3, 8, 'Bibliothèque municipale de Lyon', NULL, 45.7486, 4.8330, 'One of the largest municipal libraries in France.',
 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/dc/BML_Part_Dieu.jpg/320px-BML_Part_Dieu.jpg',
 'https://en.wikipedia.org/wiki/Biblioth%C3%A8que_municipale_de_Lyon');