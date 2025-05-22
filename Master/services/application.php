<?php
require_once '../configs/secret.php';
require_once "../models/consultation.php";
require_once "../models/users.php";
// Application Class
class Application {
    private $db;

    public function __construct($db) {
        $this->db = $db;

    }

    public function createUser($firstName, $lastName, $loginName, $oauth_id) {
        $query = "INSERT IGNORE INTO user (first_name, last_name, login_name, oauth_id) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$firstName, $lastName, $loginName, $oauth_id]);
        return $this->db->lastInsertId();
    }

    public function getUserById($userId) {
        $query = "SELECT * FROM user WHERE user_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($user) {
            switch ($user['user_type']) {
                case 0:
                    return new Student(
                        $user['user_id'],
                        $user['first_name'],
                        $user['last_name'],
                        $user['login_name']
                    );
                case 1:
                    return new Teacher(
                        $user['user_id'],
                        $user['first_name'],
                        $user['last_name'],
                        $user['login_name']
                    );
                case 2:
                    return new Admin(
                        $user['user_id'],
                        $user['first_name'],
                        $user['last_name'],
                        $user['login_name']
                    );
                default:
                    return null;
            }
        } else {
            return null;
        }
    }

    public function findUserByLoginName($loginName) {
        $query = "SELECT * FROM user WHERE login_name = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$loginName]);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        foreach ($users as $user) {
            switch ($user['user_type']) {
                case 0:
                    return new Student(
                        $user['user_id'],
                        $user['first_name'],
                        $user['last_name'],
                        $user['login_name']
                    );
                    break;
                case 1:
                    return new Teacher(
                        $user['user_id'],
                        $user['first_name'],
                        $user['last_name'],
                        $user['login_name']
                    );
                    break;
                case 2:
                    return new Admin(
                        $user['user_id'],
                        $user['first_name'],
                        $user['last_name'],
                        $user['login_name']
                    );
                    break;
                default:
                    return null;
            }
        }
        return null;
    }

    public function listUsers($userType, $limit, $offset) {
        $query = "SELECT * FROM user WHERE user_type = ? LIMIT ? OFFSET ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $userType, PDO::PARAM_INT);
        $stmt->bindParam(2, $limit, PDO::PARAM_INT);
        $stmt->bindParam(3, $offset, PDO::PARAM_INT);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        $result = [];
        foreach ($users as $user) {
        switch ($user['user_type']) {
            case 0:
                $result[] = new Student(
                    $user['user_id'],
                    $user['first_name'],
                    $user['last_name'],
                    $user['login_name']
                );
                break;
            case 1:
                $result[] = new Teacher(
                    $user['user_id'],
                    $user['first_name'],
                    $user['last_name'],
                    $user['login_name']
                );
                break;
            case 2:
                $result[] = new Admin(
                    $user['user_id'],
                    $user['first_name'],
                    $user['last_name'],
                    $user['login_name']
                );
                break;
        }
        }
        return $result;
    }
    
    public function countUsers($userType) {
        $query = "SELECT COUNT(*) as count FROM user WHERE user_type = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $userType, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }
    
    public function updateUserType($userId, $userType) {
        $query = "UPDATE user SET user_type = ? WHERE user_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $userType, PDO::PARAM_INT);
        $stmt->bindParam(2, $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function createConsultation($teacherId, $description, $dateTime, $duration, $subject) {
        // Kontrola, zda je dateTime vyplněný a není v minulosti
        if (empty($dateTime) || strtotime($dateTime) < time()) {
            throw new InvalidArgumentException("DateTime must be provided and cannot be in the past.");
        }
    
        // Kontrola, zda je duration v rozmezí 10-100
        if ($duration < 10 || $duration > 100) {
            throw new InvalidArgumentException("Duration must be between 10 and 100 minutes.");
        }
    
        // Kontrola, zda délka subjectu je menší nebo rovna 80
        if (strlen($subject) > 80) {
            throw new InvalidArgumentException("Subject length must be 80 characters or less.");
        }
    
        $isSubjectLocked = empty($subject) ? 0 : 1;
        $query = "INSERT INTO consultation (teacher_id, description_from_teacher, consultation_date, duration, subject, subject_locked) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$teacherId, $description, $dateTime, $duration, $subject, $isSubjectLocked]);
        return $this->db->lastInsertId();
    }

    public function cancelConsultation($consultationId) {
        $query = "DELETE FROM consultation WHERE consultation_id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$consultationId]);
    }

    public function listFreeConsultationsForStudent($limit, $offset) {
        $query = "SELECT c.*, Concat(u.first_name,' ', u.last_name) AS teacher_name FROM consultation c LEFT JOIN user u ON c.teacher_id = u.user_id WHERE c.student_id IS NULL AND c.consultation_date >= NOW() ORDER BY c.consultation_date ASC LIMIT ? OFFSET ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $limit, PDO::PARAM_INT);
        $stmt->bindParam(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        $consultations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $result = [];
        foreach ($consultations as $consultation) {
            $result[] = new Consultation(
                $consultation['consultation_id'],
                $consultation['consultation_date'],
                $consultation['duration'],
                $consultation['subject'],
                $consultation['subject_locked'],
                $consultation['description_from_teacher'],
                $consultation['description_from_student'],
                $consultation['teacher_id'],
                $consultation['teacher_name'],
                $consultation['student_id']
            );
        }
        return $result;
    }

    public function countFreeConsultationsForStudent() {
        $query = "SELECT COUNT(*) as count FROM consultation WHERE student_id IS NULL AND consultation_date >= NOW()";
        $stmt = $this->db->query($query);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }

    public function listFreeConsultationsForTeacher($teacherId, $limit, $offset) {
        $query = "SELECT c.*, Concat(u.first_name,' ', u.last_name) AS student_name FROM consultation c LEFT JOIN user u ON c.student_id = u.user_id WHERE c.teacher_id = ? AND c.student_id IS NULL AND c.consultation_date >= NOW() ORDER BY c.consultation_date ASC LIMIT ? OFFSET ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $teacherId, PDO::PARAM_INT);
        $stmt->bindParam(2, $limit, PDO::PARAM_INT);
        $stmt->bindParam(3, $offset, PDO::PARAM_INT);
        $stmt->execute();
        $consultations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        $result = [];
        foreach ($consultations as $consultation) {
            $result[] = new Consultation(
                $consultation['consultation_id'],
                $consultation['consultation_date'],
                $consultation['duration'],
                $consultation['subject'],
                $consultation['subject_locked'],
                $consultation['description_from_teacher'],
                $consultation['description_from_student'],
                $consultation['teacher_id'],
                null,
                $consultation['student_id'],
                $consultation['student_name']
            );
        }
        return $result;
    }
    
    public function countFreeConsultationsForTeacher($teacherId) {
        $query = "SELECT COUNT(*) as count FROM consultation WHERE teacher_id = ? AND student_id IS NULL AND consultation_date >= NOW()";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$teacherId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }
    
    public function listPastConsultationsForTeacher($teacherId, $limit, $offset) {
        $query = "SELECT c.*, Concat(u.first_name,' ', u.last_name) AS student_name FROM consultation c LEFT JOIN user u ON c.student_id = u.user_id WHERE c.teacher_id = ? AND c.consultation_date < NOW() AND c.student_id IS NOT NULL ORDER BY c.consultation_date DESC LIMIT ? OFFSET ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $teacherId, PDO::PARAM_INT);
        $stmt->bindParam(2, $limit, PDO::PARAM_INT);
        $stmt->bindParam(3, $offset, PDO::PARAM_INT);
        $stmt->execute();
        $consultations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        $result = [];
        foreach ($consultations as $consultation) {
            $result[] = new Consultation(
                $consultation['consultation_id'],
                $consultation['consultation_date'],
                $consultation['duration'],
                $consultation['subject'],
                $consultation['subject_locked'],
                $consultation['description_from_teacher'],
                $consultation['description_from_student'],
                $consultation['teacher_id'],
                null,
                $consultation['student_id'],
                $consultation['student_name']
            );
        }
        return $result;
    }
    
    public function countPastConsultationsForTeacher($teacherId) {
        $query = "SELECT COUNT(*) as count FROM consultation WHERE teacher_id = ? AND student_id IS NOT NULL AND consultation_date < NOW()";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$teacherId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }
    
    public function listReservedConsultationsForTeacher($teacherId, $limit, $offset) {
        $query = "SELECT c.*, Concat(u.first_name,' ', u.last_name) AS student_name FROM consultation c LEFT JOIN user u ON c.student_id = u.user_id WHERE c.teacher_id = ? AND c.student_id IS NOT NULL AND c.consultation_date >= NOW() ORDER BY c.consultation_date ASC LIMIT ? OFFSET ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $teacherId, PDO::PARAM_INT);
        $stmt->bindParam(2, $limit, PDO::PARAM_INT);
        $stmt->bindParam(3, $offset, PDO::PARAM_INT);
        $stmt->execute();
        $consultations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        $result = [];
        foreach ($consultations as $consultation) {
            $result[] = new Consultation(
                $consultation['consultation_id'],
                $consultation['consultation_date'],
                $consultation['duration'],
                $consultation['subject'],
                $consultation['subject_locked'],
                $consultation['description_from_teacher'],
                $consultation['description_from_student'],
                $consultation['teacher_id'],
                null,
                $consultation['student_id'],
                $consultation['student_name']
            );
        }
        return $result;
    }
    
    public function countReservedConsultationsForTeacher($teacherId) {
        $query = "SELECT COUNT(*) as count FROM consultation WHERE teacher_id = ? AND student_id IS NOT NULL AND consultation_date >= NOW()";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$teacherId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }

    public function listConsultationsForStudent($studentId, $limit, $offset) {
        $query = "SELECT c.*, Concat(u.first_name,' ', u.last_name) AS teacher_name FROM consultation c LEFT JOIN user u ON c.teacher_id = u.user_id WHERE c.student_id = ? AND c.consultation_date >= NOW() ORDER BY c.consultation_date ASC LIMIT ? OFFSET ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $studentId, PDO::PARAM_INT);
        $stmt->bindParam(2, $limit, PDO::PARAM_INT);
        $stmt->bindParam(3, $offset, PDO::PARAM_INT);
        $stmt->execute();
        $consultations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $result = [];
        foreach ($consultations as $consultation) {
            $result[] = new Consultation(
                $consultation['consultation_id'],
                $consultation['consultation_date'],
                $consultation['duration'],
                $consultation['subject'],
                $consultation['subject_locked'],
                $consultation['description_from_teacher'],
                $consultation['description_from_student'],
                $consultation['teacher_id'],
                $consultation['teacher_name'],
                $consultation['student_id']
            );
        }
        return $result;
    }

    public function countConsultationsForStudent($studentId) {
        $query = "SELECT COUNT(*) as count FROM consultation WHERE student_id = ? AND consultation_date >= NOW()";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$studentId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }

    public function listPastConsultationsForStudent($studentId, $limit, $offset) {
        $query = "SELECT c.*, Concat(u.first_name,' ', u.last_name) AS teacher_name FROM consultation c LEFT JOIN user u ON c.teacher_id = u.user_id WHERE c.student_id = ? AND c.consultation_date < NOW() ORDER BY c.consultation_date ASC LIMIT ? OFFSET ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $studentId, PDO::PARAM_INT);
        $stmt->bindParam(2, $limit, PDO::PARAM_INT);
        $stmt->bindParam(3, $offset, PDO::PARAM_INT);
        $stmt->execute();
        $consultations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $result = [];
        foreach ($consultations as $consultation) {
            $result[] = new Consultation(
                $consultation['consultation_id'],
                $consultation['consultation_date'],
                $consultation['duration'],
                $consultation['subject'],
                $consultation['subject_locked'],
                $consultation['description_from_teacher'],
                $consultation['description_from_student'],
                $consultation['teacher_id'],
                $consultation['teacher_name'],
                $consultation['student_id']
            );
        }
        return $result;
    }

    public function countPastConsultationsForStudent($studentId) {
        $query = "SELECT COUNT(*) as count FROM consultation WHERE student_id = ? AND consultation_date < NOW()";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $studentId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }

    public function createReservation($studentId, $consultationId, $description, $subject) {
        $query = "UPDATE consultation SET student_id = ?, description_from_student = ?, subject = CASE WHEN subject_locked = 1 THEN subject ELSE ? END WHERE consultation_id = ? AND student_id IS NULL";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$studentId, $description, $subject, $consultationId]);
    }

    public function cancelReservation($consultationId) {
        $query = "UPDATE consultation SET student_id = NULL, description_from_student = NULL, subject = CASE WHEN subject_locked = 1 THEN subject ELSE NULL END WHERE consultation_id = ? AND student_id IS NOT NULL";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$consultationId]);
    }

    public function getConsultationById($consultationId) {
        $query = "SELECT c.*, Concat(u.first_name,' ', u.last_name) AS teacher_name FROM consultation c LEFT JOIN user u ON c.teacher_id = u.user_id  WHERE consultation_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$consultationId]);
        $consultation = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($consultation) {
            return new Consultation(
                $consultation['consultation_id'],
                $consultation['consultation_date'],
                $consultation['duration'],
                $consultation['subject'],
                $consultation['subject_locked'],
                $consultation['description_from_teacher'],
                $consultation['description_from_student'],
                $consultation['teacher_id'],
                $consultation['teacher_name'],
                $consultation['student_id']
            );
        } else {
            return null;
        }
    }
    public function checkConsultationForOverlap($teacherId, $dateTime, $duration) {
        $query = "SELECT COUNT(*) as count FROM consultation WHERE teacher_id = ? AND  (consultation_date <= ? AND DATE_ADD(consultation_date, INTERVAL duration MINUTE) >= ? OR consultation_date >= ? AND consultation_date <= DATE_ADD(?, INTERVAL ? MINUTE))";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$teacherId, $dateTime, $dateTime, $dateTime, $dateTime, $duration]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }
}
?>