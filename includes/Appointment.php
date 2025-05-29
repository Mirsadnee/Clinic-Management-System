<?php
class Appointment {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function scheduleAppointment($patient_id, $doctor_id, $clinic_id, $appointment_date, $appointment_time, $reason) {
        try {
            $sql = "INSERT INTO appointments (patient_id, doctor_id, clinic_id, appointment_date, appointment_time, reason, status) 
                    VALUES (:patient_id, :doctor_id, :clinic_id, :appointment_date, :appointment_time, :reason, 'scheduled')";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':patient_id', $patient_id);
            $stmt->bindParam(':doctor_id', $doctor_id);
            $stmt->bindParam(':clinic_id', $clinic_id);
            $stmt->bindParam(':appointment_date', $appointment_date);
            $stmt->bindParam(':appointment_time', $appointment_time);
            $stmt->bindParam(':reason', $reason);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error scheduling appointment: " . $e->getMessage());
            return false;
        }
    }

    public function getPatientAppointments($patient_id) {
        try {
            $sql = "SELECT a.*, d.name as doctor_name, c.name as clinic_name 
                    FROM appointments a 
                    JOIN users d ON a.doctor_id = d.id 
                    JOIN clinics c ON a.clinic_id = c.id 
                    WHERE a.patient_id = :patient_id 
                    ORDER BY a.appointment_date DESC, a.appointment_time DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':patient_id', $patient_id);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching patient appointments: " . $e->getMessage());
            return [];
        }
    }

    public function getUpcomingAppointments($patient_id) {
        try {
            $sql = "SELECT a.*, d.name as doctor_name, c.name as clinic_name 
                    FROM appointments a 
                    JOIN users d ON a.doctor_id = d.id 
                    JOIN clinics c ON a.clinic_id = c.id 
                    WHERE a.patient_id = :patient_id 
                    AND (a.appointment_date > CURDATE() 
                    OR (a.appointment_date = CURDATE() AND a.appointment_time > CURTIME()))
                    ORDER BY a.appointment_date ASC, a.appointment_time ASC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':patient_id', $patient_id);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching upcoming appointments: " . $e->getMessage());
            return [];
        }
    }

    public function getPastAppointments($patient_id) {
        try {
            $sql = "SELECT a.*, d.name as doctor_name, c.name as clinic_name 
                    FROM appointments a 
                    JOIN users d ON a.doctor_id = d.id 
                    JOIN clinics c ON a.clinic_id = c.id 
                    WHERE a.patient_id = :patient_id 
                    AND (a.appointment_date < CURDATE() 
                    OR (a.appointment_date = CURDATE() AND a.appointment_time < CURTIME()))
                    ORDER BY a.appointment_date DESC, a.appointment_time DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':patient_id', $patient_id);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching past appointments: " . $e->getMessage());
            return [];
        }
    }

    public function cancelAppointment($appointment_id, $patient_id) {
        try {
            $sql = "UPDATE appointments 
                    SET status = 'cancelled' 
                    WHERE id = :appointment_id AND patient_id = :patient_id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':appointment_id', $appointment_id);
            $stmt->bindParam(':patient_id', $patient_id);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error cancelling appointment: " . $e->getMessage());
            return false;
        }
    }

    public function getDoctorsByClinic($clinic_id) {
        try {
            $sql = "SELECT id, name FROM users WHERE role = 'doctor' AND clinic_id = :clinic_id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':clinic_id', $clinic_id);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching doctors by clinic: " . $e->getMessage());
            return [];
        }
    }

    public function getClinics() {
        try {
            $sql = "SELECT id, name FROM clinics WHERE status = 'active'";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching clinics: " . $e->getMessage());
            return [];
        }
    }
} 