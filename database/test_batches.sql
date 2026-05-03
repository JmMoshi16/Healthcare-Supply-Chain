-- Add test batches expiring in June, July, August 2025
INSERT INTO batches (medicine_id, batch_number, manufacturing_date, expiry_date, supplier, purchase_price, selling_price, initial_quantity, current_quantity, status) VALUES
(2, 'TEST-JUN-2025-A', '2024-06-01', '2025-06-15', 'Test Supplier', 100.00, 150.00, 500, 450, 'active'),
(4, 'TEST-JUN-2025-B', '2024-06-01', '2025-06-25', 'Test Supplier', 200.00, 250.00, 300, 280, 'active'),
(23, 'TEST-JUL-2025-A', '2024-07-01', '2025-07-10', 'Test Supplier', 150.00, 200.00, 400, 380, 'active'),
(24, 'TEST-JUL-2025-B', '2024-07-01', '2025-07-20', 'Test Supplier', 180.00, 230.00, 350, 320, 'active'),
(31, 'TEST-AUG-2025-A', '2024-08-01', '2025-08-05', 'Test Supplier', 120.00, 170.00, 450, 420, 'active'),
(2, 'TEST-AUG-2025-B', '2024-08-01', '2025-08-18', 'Test Supplier', 160.00, 210.00, 380, 350, 'active');
