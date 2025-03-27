<?php

 require_once '../includes/database.php';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $service = filter_input(INPUT_POST, 'service', FILTER_SANITIZE_STRING);
    $booking_date = filter_input(INPUT_POST, 'booking_date', FILTER_SANITIZE_STRING);
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

    // Validate inputs
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "Name is required";
    }
    
    if (empty($phone)) {
        $errors[] = "Phone number is required";
    }
    
    if (empty($service)) {
        $errors[] = "Service selection is required";
    }
    
    if (empty($booking_date)) {
        $errors[] = "Booking date is required";
    }

    // If no errors, proceed with database insertion
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO appointments 
                (name, phone, service, booking_date, additional_notes, created_at) 
                VALUES (:name, :phone, :service, :booking_date, :message, NOW())");
            
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':service', $service);
            $stmt->bindParam(':booking_date', $booking_date);
            $stmt->bindParam(':message', $message);
            
            $stmt->execute();

            // Prepare success response
            $response = [
                'status' => 'success',
                'message' => 'Appointment booked successfully!'
            ];
            echo json_encode($response);
            exit();

        } catch(PDOException $e) {
            // Prepare error response
            $response = [
                'status' => 'error',
                'message' => 'Booking failed: ' . $e->getMessage()
            ];
            echo json_encode($response);
            exit();
        }
    } else {
        // If validation errors exist
        $response = [
            'status' => 'error',
            'errors' => $errors
        ];
        echo json_encode($response);
        exit();
    }
}
?>