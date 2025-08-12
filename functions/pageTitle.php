<?php
function getPageTitle($currentPage) {
    // Use a switch statement to determine the page title
    switch ($currentPage) {
        case "admin_files":
            return "Administrative Files";
        case "cild_files":
            return "Curriculum Implementation and Learning Delivery";
        case "lulr_files":
            return "Localization and Utilization of Learning Resources";                            
        case "aeld_files":
        return "Assessment/Evaluation of Learner's Development";                            

        case "if_proposals_files":
        return "Innovation Files/Proposals";                            

        case "if_completed_files":
        return "Innovation Files/Completed";       
        
        case "rp_proposal":
        return "Research Papers/Proposals";      

        case "rp_proposal_berf_files":
        return "Research Papers/Proposals/Berf";       
        
        case "rp_proposal_nonberf_files":
        return "Research Papers/Proposals/Non Berf";       

        case "rp_completed_berf_files":
        return "Research Papers/Completed/Berf";       
        
        case "rp_completed_nonberf_files":
        return "Research Papers/Completed/Non Berf";       

        case "t_lr_files":
        return "Transparency/Liquidation Report";       

        case "t_pp_files":
        return "Transparency/Project Proposal";       

        case "t_rs_files":
        return "Transparency/Realignment and Supplementals";

        case 'approved_proposal':
            return 'Approved Proposal';
                
        case "dashboard-overview":
            return "Dashboard Overview";

        case "event":
            return "Event Page";
        case "notification":
            return "Notification Page";
        case "pending-files":
            return "Pending Files";
        case "pending-users":
            return "Pending Users";
        case "teachers_profile":
        return "Teachers Profile";
        case "reports":
        return "Report Management";
        case "profile":
        return "Profile";
        case "mission_vision":
        return "Mission and Vision";
        case "settings":
        return "Settings";

        default:
            return ucfirst($currentPage) . " Page"; // Default title
    }
}
?>
