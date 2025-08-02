<?php

// functions/file_operations.php
function setFileTableVariables($table1, $file) {
    $uploadDir = '';
    $uploadDir2 = '';
    $file_path = '';
    $download_path = '';
    $table2 = '';
    
    switch ($table1) {
        case 'admin_files':
            $file_path = '/west2es/uploads/files/admin_files/' . basename($file['name']);
            $download_path = '../uploads/files/admin_files/' . basename($file['name']);
            $uploadDir = '../../uploads/files/admin_files/';
            $uploadDir2 = 'uploads/files/admin_files/';
            $table2 = 'admin_files_versions';
            break;
        case 'aeld_files':
            $file_path = '/west2es/uploads/files/aeld_files/' . basename($file['name']);
            $download_path = '../uploads/files/aeld_files/' . basename($file['name']);
            $uploadDir = '../../uploads/files/aeld_files/';
            $table2 = 'aeld_files_versions';
            break;
        case 'cild_files':
            $file_path = '/west2es/uploads/files/cild_files/' . basename($file['name']);
            $download_path = '../uploads/files/cild_files/' . basename($file['name']);
            $uploadDir = '../../uploads/files/cild_files/';
            $table2 = 'cild_files_versions';
            break;
        case 'if_completed_files':
            $file_path = '/west2es/uploads/files/if_completed_files/' . basename($file['name']);
            $download_path = '../uploads/files/if_completed_files/' . basename($file['name']);
            $uploadDir = '../../uploads/files/if_completed_files/';
            $table2 = 'if_completed_files_versions';
            break;
        case 'if_proposals_files':
            $file_path = '/west2es/uploads/files/if_proposals_files/' . basename($file['name']);
            $download_path = '../uploads/files/if_proposals_files/' . basename($file['name']);
            $uploadDir = '../../uploads/files/if_proposals_files/';
            $table2 = 'if_proposals_files_versions';
            break;
        case 'lulr_files':
            $file_path = '/west2es/uploads/files/lulr_files/' . basename($file['name']);
            $download_path = '../uploads/files/lulr_files/' . basename($file['name']);
            $uploadDir = '../../uploads/files/lulr_files/';
            $table2 = 'lulr_files_versions';
            break;
        case 'rp_completed_berf_files':
            $file_path = '/west2es/uploads/files/rp_completed_berf_files/' . basename($file['name']);
            $download_path = '../uploads/files/rp_completed_berf_files/' . basename($file['name']);
            $uploadDir = '../../uploads/files/rp_completed_berf_files/';
            $table2 = 'rp_completed_berf_files_versions';
            break;
        case 'rp_completed_nonberf_files':
            $file_path = '/west2es/uploads/files/rp_completed_nonberf_files/' . basename($file['name']);
            $download_path = '../uploads/files/rp_completed_nonberf_files/' . basename($file['name']);
            $uploadDir = '../../uploads/files/rp_completed_nonberf_files/';
            $table2 = 'rp_completed_nonberf_files_versions';
            break;
        case 'rp_proposal_berf_files':
            $file_path = '/west2es/uploads/files/rp_proposal_berf_files/' . basename($file['name']);
            $download_path = '../uploads/files/rp_proposal_berf_files/' . basename($file['name']);
            $uploadDir = '../../uploads/files/rp_proposal_berf_files/';
            $table2 = 'rp_proposal_berf_files_versions';
            break;
        case 'rp_proposal_nonberf_files':
            $file_path = '/west2es/uploads/files/rp_proposal_nonberf_files/' . basename($file['name']);
            $download_path = '../uploads/files/rp_proposal_nonberf_files/' . basename($file['name']);
            $uploadDir = '../../uploads/files/rp_proposal_nonberf_files/';
            $table2 = 'rp_proposal_nonberf_files_versions';
            break;
        case 't_lr_files':
            $file_path = '/west2es/uploads/files/t_lr_files/' . basename($file['name']);
            $download_path = '../uploads/files/t_lr_files/' . basename($file['name']);
            $uploadDir = '../../uploads/files/t_lr_files/';
            $table2 = 't_lr_files_versions';
            break;
        case 't_pp_files':
            $file_path = '/west2es/uploads/files/t_pp_files/' . basename($file['name']);
            $download_path = '../uploads/files/t_pp_files/' . basename($file['name']);
            $uploadDir = '../../uploads/files/t_pp_files/';
            $table2 = 't_pp_files_versions';
            break;
        case 't_rs_files':
            $file_path = '/west2es/uploads/files/t_rs_files/' . basename($file['name']);
            $download_path = '../uploads/files/t_rs_files/' . basename($file['name']);
            $uploadDir = '../../uploads/files/t_rs_files/';
            $table2 = 't_rs_files_versions';
            break;
        default:
            throw new Exception('Invalid table1 value.');
    }

    // Return the variables for further use
    return [
        'uploadDir' => $uploadDir,
        'uploadDir2' => $uploadDir2,
        'file_path' => $file_path,
        'download_path' => $download_path,
        'table2' => $table2
    ];
}




// File: functions/file_functions/getFileCategory.php

function getFileCategory($pageName) {
    // Initialize the fileCategory variable
    $fileCategory = "";

    // Use a switch statement to set the fileCategory
    switch ($pageName) {
        case 'admin_files':
            $fileCategory = "Files/Administrative Files";
            break;
        case 'cild_files':
            $fileCategory = "Files/Curriculum Implementation and Learning Delivery";
            break;
        case 'lulr_files':
            $fileCategory = "Localization and Utilization of Learning Resources";
            break;    
        case 'aeld_files':
            $fileCategory = "Assessment/Evaluation of Learner's Development";
            break;        
        case 'if_proposals_files':
            $fileCategory = "Innovation Files/Proposals";
            break;            
        case 'if_completed_files':
            $fileCategory = "Innovation Files/Completed";
            break;   
        case 'rp_proposal_berf_files':
            $fileCategory = "Research Papers/Proposals/Berf";
            break;       
        case 'rp_proposal_nonberf_files':
            $fileCategory = "Research Papers/Proposals/Non Berf";
            break;           
        case 'rp_completed_berf_files':
            $fileCategory = "Research Papers/Completed/Berf";
            break;
        case 'rp_completed_nonberf_files':
            $fileCategory = "Research Papers/Completed/Non Berf";
            break;    
        case 't_lr_files':
            $fileCategory = "Transparency/Liquidation Report";
            break;           
        case 't_pp_files':
            $fileCategory = "Transparency/Project Proposal";
            break;
        case 't_rs_files':
            $fileCategory = "Transparency/Realignment and Supplementals";
            break;
        default:
            $fileCategory = "Unknown Category"; // Fallback if no match
            break;
    }

    return $fileCategory;
}
?>

