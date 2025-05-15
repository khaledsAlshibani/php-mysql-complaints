<?php
require_once '../config.php';

$formHandler = new FormHandler();

$formHandler->handleLogin();

$formHandler->handleCreateItem();

$formHandler->handleComplaintFeedback();
$formHandler->handleSuggestionFeedback();

$formHandler->handleComplaintUpdate();
$formHandler->handleSuggestionUpdate();

$formHandler->handleComplaintDelete();
$formHandler->handleSuggestionDelete();

$formHandler->handleComplaintFeedbackDelete();
$formHandler->handleSuggestionFeedbackDelete();
