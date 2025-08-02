<?php
// Example: Set the page name or file identifier (you can replace it dynamically)
$pageName = basename($_SERVER['PHP_SELF'], ".php");

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
    case 'if_completed_completed':
        $fileCategory = "Innovation Files/Completed";
        break;   
    case 'rp_proposal_berf_files':
        $fileCategory = "Research Papers/Proposals/Berf";
        break;       
    case 'rp_proposal_nonberf_files':
        $fileCategory = "Research Papers/Proposals/Non Berf";
        break;           
    case 'rp_completed_berf_files':
        $fileCategory = "Research Papers/Proposals/Non Berf";
        break;
    case 'rp_completed_nonberf_files':
        $fileCategory = "Research Papers/Proposals/Non Berf";
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
?>

<h3 class="mb-3">
    <?php echo $fileCategory; ?>
</h3>
