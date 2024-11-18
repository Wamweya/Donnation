CREATE TABLE mpesa_responses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    MerchantRequestID VARCHAR(100) NOT NULL,
    CheckoutRequestID VARCHAR(100) NOT NULL,
    ResponseCode VARCHAR(10) NOT NULL,
    ResponseDescription TEXT NOT NULL,
    CustomerMessage TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
