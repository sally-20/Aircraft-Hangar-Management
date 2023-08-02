CREATE TABLE users (
    uid INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE hangars (
    hid INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    location VARCHAR(255) NOT NULL,
    user_id INT(11) UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_user_id FOREIGN KEY (user_id) REFERENCES users (uid) ON DELETE CASCADE
);
CREATE TABLE aircraft (
    aid INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    registration_number VARCHAR(15) NOT NULL,
    model VARCHAR(50) NOT NULL,
    hangar_id INT(11) UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_hangar_id FOREIGN KEY (hangar_id) REFERENCES hangars (hid) ON DELETE SET NULL
);
CREATE TABLE reservations (
    rid INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) UNSIGNED,
    hangar_id INT(11) UNSIGNED,
    aircraft_id INT(11) UNSIGNED,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_user_id_res FOREIGN KEY (user_id) REFERENCES users (uid) ON DELETE CASCADE,
    CONSTRAINT fk_hangar_id_res FOREIGN KEY (hangar_id) REFERENCES hangars (hid) ON DELETE CASCADE,
    CONSTRAINT fk_aircraft_id FOREIGN KEY (aircraft_id) REFERENCES aircraft (aid) ON DELETE CASCADE
);
