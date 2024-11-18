CREATE TABLE donation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    donationType VARCHAR(20) NOT NULL,
    food VARCHAR(255),  -- Assuming food details are stored in a VARCHAR field
    foodLocation VARCHAR(255),  -- Assuming food location is stored in a VARCHAR field
    clothes VARCHAR(255),  -- Assuming clothes details are stored in a VARCHAR field
    clothesLocation VARCHAR(255),  -- Assuming clothes location is stored in a VARCHAR field
    mpesaNumber VARCHAR(20),  -- Assuming M-Pesa number is stored in a VARCHAR field
    amount DECIMAL(10, 2)  -- Assuming amount is stored as a DECIMAL
);
