<?php

class FormHandler {
    private $user;
    private $complaint;
    private $suggestion;
    
    public function __construct() {
        $this->user = new User();
        $this->complaint = new Complaint();
        $this->suggestion = new Suggestion();
    }
    
    public function handleLogin() {
        $username = Req::retrievePostValue("username");
        $password = Req::retrievePostValue("password");
        
        if ($username && $password) {
            if ($this->user->authenticate($username, $password)) {
                header("Location: " . BASE_URL . "index.php");
                exit();
            }
            header("Location: " . BASE_URL . "templates/pages/login.php");
            exit();
        }
    }
    
    public function handleCreateItem() {
        $type = Req::retrievePostValue("type");
        $title = Req::retrievePostValue("title");
        $description = Req::retrievePostValue("description");
        
        if ($type && $title && $description) {
            $data = [
                'user_id' => $this->user->getUserId(),
                'title' => $title,
                'description' => $description,
                'status' => '0'
            ];
            
            try {
                if ($type == 1) {
                    $this->complaint->createComplaint($data);
                } elseif ($type == 2) {
                    $this->suggestion->createSuggestion($data);
                }
                header("Location: " . BASE_URL . "index.php");
                exit();
            } catch (Exception $e) {
                error_log("Create Item Error: " . $e->getMessage());
                header("Location: " . BASE_URL . "templates/pages/create.php");
                exit();
            }
        }
    }
    
    public function handleComplaintFeedback() {
        $complaintId = Req::retrievePostValue("complaint_id");
        $feedback = Req::retrievePostValue("complaint_feedback");
        
        if ($feedback && $complaintId) {
            try {
                $this->complaint->addFeedback($complaintId, $feedback);
                header("Location: " . BASE_URL . "index.php");
                exit();
            } catch (Exception $e) {
                error_log("Complaint Feedback Error: " . $e->getMessage());
                header("Location: " . BASE_URL . "index.php");
                exit();
            }
        }
    }
    
    public function handleSuggestionFeedback() {
        $suggestionId = Req::retrievePostValue("suggestion_id");
        $feedback = Req::retrievePostValue("suggestion_feedback");
        
        if ($feedback && $suggestionId) {
            try {
                $this->suggestion->addFeedback($suggestionId, $feedback);
                header("Location: " . BASE_URL . "index.php");
                exit();
            } catch (Exception $e) {
                error_log("Suggestion Feedback Error: " . $e->getMessage());
                header("Location: " . BASE_URL . "index.php");
                exit();
            }
        }
    }
    
    public function handleComplaintUpdate() {
        $complaintId = Req::retrievePostValue("complaint_id");
        $title = Req::retrievePostValue("complaint_title");
        $description = Req::retrievePostValue("complaint_description");
        
        if ($description && $complaintId) {
            try {
                $data = [
                    'title' => $title,
                    'description' => $description,
                    'status' => '0'
                ];
                
                $this->complaint->updateComplaint($complaintId, $data);
                header("Location: " . BASE_URL . "index.php");
                exit();
            } catch (Exception $e) {
                error_log("Complaint Update Error: " . $e->getMessage());
                header("Location: " . BASE_URL . "index.php");
                exit();
            }
        }
    }
    
    public function handleSuggestionUpdate() {
        $suggestionId = Req::retrievePostValue("suggestion_id");
        $title = Req::retrievePostValue("suggestion_title");
        $description = Req::retrievePostValue("suggestion_description");
        
        if ($description && $suggestionId) {
            try {
                $data = [
                    'title' => $title,
                    'description' => $description,
                    'status' => '0'
                ];
                
                $this->suggestion->updateSuggestion($suggestionId, $data);
                header("Location: " . BASE_URL . "index.php");
                exit();
            } catch (Exception $e) {
                error_log("Suggestion Update Error: " . $e->getMessage());
                header("Location: " . BASE_URL . "index.php");
                exit();
            }
        }
    }
    
    public function handleComplaintDelete() {
        error_log("Handling complaint delete request");
        
        $complaintId = Req::retrievePostValue("complaint_id");
        $deleteComplaint = Req::retrievePostValue("delete_complaint");
        
        if ($complaintId && $deleteComplaint) {
            error_log("POST data received - delete_complaint: " . $deleteComplaint . ", complaint_id: " . $complaintId);
            try {
                error_log("User type: " . $this->user->getUserType());
                
                if ($deleteComplaint === "1") {
                    error_log("Attempting to delete complaint with ID: " . $complaintId);
                    $this->complaint->deleteComplaint($complaintId);
                    error_log("Successfully deleted complaint");
                } else {
                    error_log("Delete flag not set to 1, skipping delete. Value: " . $deleteComplaint);
                }
                header("Location: " . BASE_URL . "index.php");
                exit();
            } catch (Exception $e) {
                error_log("Complaint Delete Error: " . $e->getMessage());
                error_log("Stack trace: " . $e->getTraceAsString());
                header("Location: " . BASE_URL . "index.php");
                exit();
            }
        } else {
            error_log("Required POST parameters missing");
            error_log("Expected parameters: delete_complaint, complaint_id");
            error_log("Received parameters: " . implode(", ", array_keys($_POST)));
        }
    }
    
    public function handleSuggestionDelete() {
        error_log("Handling suggestion delete request");
        
        $suggestionId = Req::retrievePostValue("suggestion_id");
        $deleteSuggestion = Req::retrievePostValue("delete_suggestion");
        
        if ($suggestionId && $deleteSuggestion) {
            error_log("POST data received - delete_suggestion: " . $deleteSuggestion . ", suggestion_id: " . $suggestionId);
            try {
                if ($deleteSuggestion === "1") {
                    error_log("Attempting to delete suggestion with ID: " . $suggestionId);
                    $this->suggestion->deleteSuggestion($suggestionId);
                    error_log("Successfully deleted suggestion");
                } else {
                    error_log("Delete flag not set to 1, skipping delete");
                }
                header("Location: " . BASE_URL . "index.php");
                exit();
            } catch (Exception $e) {
                error_log("Suggestion Delete Error: " . $e->getMessage());
                error_log("Stack trace: " . $e->getTraceAsString());
                header("Location: " . BASE_URL . "index.php");
                exit();
            }
        } else {
            error_log("Required POST parameters missing");
            error_log("POST data: " . print_r($_POST, true));
        }
    }

    public function handleComplaintFeedbackDelete() {
        error_log("Handling complaint feedback delete request");
        
        $complaintId = Req::retrievePostValue("complaint_id");
        $removeFeedback = Req::retrievePostValue("remove_comp_feedback");
        
        if ($complaintId && $removeFeedback) {
            error_log("POST data received - remove_comp_feedback: " . $removeFeedback . ", complaint_id: " . $complaintId);
            try {
                error_log("User type: " . $this->user->getUserType());
                
                $this->complaint->removeFeedback($complaintId);
                error_log("Successfully removed complaint feedback");
                header("Location: " . BASE_URL . "index.php");
                exit();
            } catch (Exception $e) {
                error_log("Remove Complaint Feedback Error: " . $e->getMessage());
                error_log("Stack trace: " . $e->getTraceAsString());
                header("Location: " . BASE_URL . "index.php");
                exit();
            }
        } else {
            error_log("Required POST parameters missing for feedback deletion");
            error_log("Expected parameters: remove_comp_feedback, complaint_id");
            error_log("Received parameters: " . implode(", ", array_keys($_POST)));
        }
    }

    public function handleSuggestionFeedbackDelete() {
        $suggestionId = Req::retrievePostValue("suggestion_id");
        $removeFeedback = Req::retrievePostValue("remove_sug_feedback");
        
        if ($suggestionId && $removeFeedback) {
            try {
                $this->suggestion->removeFeedback($suggestionId);
                header("Location: " . BASE_URL . "index.php");
                exit();
            } catch (Exception $e) {
                error_log("Remove Suggestion Feedback Error: " . $e->getMessage());
                header("Location: " . BASE_URL . "index.php");
                exit();
            }
        }
    }
}
