<?php
class MedicalRecord {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getPatientMedicalRecords($patient_id) {
        try {
            $sql = "SELECT mr.*, a.appointment_date, a.appointment_time, d.name as doctor_name, c.name as clinic_name
                    FROM medical_records mr
                    JOIN appointments a ON mr.appointment_id = a.id
                    JOIN users d ON a.doctor_id = d.id
                    JOIN clinics c ON a.clinic_id = c.id
                    WHERE a.patient_id = :patient_id
                    ORDER BY a.appointment_date DESC, a.appointment_time DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':patient_id', $patient_id);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching medical records: " . $e->getMessage());
            return [];
        }
    }

    public function getAppointmentMedicalRecord($appointment_id, $patient_id) {
        try {
            $sql = "SELECT mr.*, a.appointment_date, a.appointment_time, d.name as doctor_name, c.name as clinic_name
                    FROM medical_records mr
                    JOIN appointments a ON mr.appointment_id = a.id
                    JOIN users d ON a.doctor_id = d.id
                    JOIN clinics c ON a.clinic_id = c.id
                    WHERE a.id = :appointment_id AND a.patient_id = :patient_id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':appointment_id', $appointment_id);
            $stmt->bindParam(':patient_id', $patient_id);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching appointment medical record: " . $e->getMessage());
            return null;
        }
    }

    public function getPatientAllergies($patient_id) {
        try {
            $sql = "SELECT * FROM patient_allergies WHERE patient_id = :patient_id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':patient_id', $patient_id);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching patient allergies: " . $e->getMessage());
            return [];
        }
    }

    public function getPatientMedications($patient_id) {
        try {
            $sql = "SELECT * FROM patient_medications WHERE patient_id = :patient_id AND status = 'active'";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':patient_id', $patient_id);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching patient medications: " . $e->getMessage());
            return [];
        }
    }

    public function getPatientConditions($patient_id) {
        try {
            $sql = "SELECT * FROM patient_conditions WHERE patient_id = :patient_id AND status = 'active'";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':patient_id', $patient_id);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching patient conditions: " . $e->getMessage());
            return [];
        }
    }
} 